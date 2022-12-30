;

// Pila de comandos enviados
var g_arrTicketsPending = [];
var g_unMostrarComo = 0;
var g_attemptsAllowed = 5;
var g_timeBetween = 2 * 1000;

var $lblStatus = null;
var $btnEnviar = null;
var $btnVerEstado = null;

function enviar_comando()
{
    var url = "ajaxComandoFavorito.php";
    var cmd = $("#cboFavCommand").val();
    var idEquipo = g_unMostrarComo;
    
    $lblStatus.removeClass("status-ok");
    $lblStatus.removeClass("status-error");
    $lblStatus.addClass("status-pending");
    
    $lblStatus.html("Esperando respuesta...");
    $lblStatus.show();

    $btnEnviar.prop("disabled", true);
    g_bCommandInProcess = true;

    $.ajax({
        "url": url,
        "type": "post",
        "dataType": "json",

        "data":
        {
            "op": "send",
            "cmd": cmd,
            "id_equipo": idEquipo
        },

        "success": function(oPackedData, status, jqxhr)
        {
            switch ( oPackedData.errcode )
            {
                case 0:
                {
                    console.log("Comando enviado satisfactoriamente (ticket: " + oPackedData.data.ticket + ")");
                    $lblStatus.html("Esperando respuesta (ticket #" + oPackedData.data.ticket + ")...");
                    g_arrTicketsPending[ oPackedData.data.ticket ] = {
                        "intentos" : g_attemptsAllowed,
                        "ticket"   : oPackedData.data.ticket,
                        "handler"  : window.setInterval( function()
                        {
                            chequear_estado_comando( oPackedData.data.ticket )
                        }, g_timeBetween )
                    };
                    break;
                }
                default:
                {
                    $lblStatus.removeClass("status-pending");
                    $lblStatus.addClass("status-error");
                    
                    $lblStatus.html("ERROR #" + oPackedData.errcode + " (" + oPackedData.errmesg + ").");
                    
                    console.warn("ERROR #" + oPackedData.errcode + " (" + oPackedData.errmesg + ").");

                    $btnEnviar.prop("disabled", false);
                    g_bCommandInProcess = false;
                    break;
                }
            }

            $lblStatus.show();
        },

        "error": function(jqxhr, status, error)
        {
            $lblStatus.removeClass("status-pending");
            $lblStatus.addClass("status-error");
            
            $lblStatus.html("Error al intentar enviar el paquete");

            $lblStatus.show();
			//console.warn("Error al llamar al AJAX: '" + url + "'.");
            $btnEnviar.prop("disabled", false);
            g_bCommandInProcess = false;
        }
    });
}


function chequear_estado_comando(iTicket)
{
    var url = "ajaxComandoFavorito.php";

    $.ajax({
        "url": url,
        "type": "post",
        "dataType": "json",
        "data":
        {
            "op": "check",
            "ticket": iTicket
        },

        "success": function(oPackedData, status, jqxhr)
        {
            $lblStatus.removeClass("status-pending");

            switch( oPackedData.errcode )
            {
                case 0:
                {
                    if ( oPackedData.data.response == false )
                    {
                        $lblStatus.removeClass("status-ok");
                        $lblStatus.addClass("status-error");

                        if ( g_arrTicketsPending[iTicket].intentos > 0 )
                        {
                            console.warn("No response for ticket #" + iTicket + " (attempt #" + g_arrTicketsPending[iTicket].intentos + ")");
                            $lblStatus.html("Intentando (" + g_arrTicketsPending[iTicket].intentos + ")... (ticket #" + iTicket + ").");
                            
                            g_arrTicketsPending[iTicket].intentos--;
                        }
                        else
                        {
                            console.warn("No response for ticket #" + iTicket + " (LAST attempt). Aborting.");

                            $lblCaption = $("<span>").addClass("status-caption").html("Sin respuesta ");
                            $lblStatus.html("");
                            $lblStatus.append( $lblCaption );
                            $lblStatus.append("(ticket #" + iTicket + "). Abortado.");
                            
                            window.clearInterval(g_arrTicketsPending[iTicket].handler);

                            delete g_arrTicketsPending[iTicket];
                            $btnEnviar.prop("disabled", false);
                            g_bCommandInProcess = false;
                        }
                    }
                    else
                    {
                        $lblStatus.removeClass("status-error");
                        $lblStatus.addClass("status-ok");

                        console.log(
                            "RESPONSE: " + oPackedData.data.response.ce_respuesta + 
                            " (" + oPackedData.data.response.ce_fechaRespuesta + ") !!!");

                        $lblCaption = $("<span>").addClass("status-caption").html("Respuesta exitosa: ");
                        $lblStatus.html("");
                        $lblStatus.append( $lblCaption );
                        $lblStatus.append( oPackedData.data.response.ce_respuesta );

                        //$lblStatus.html( $lblCaption.html() + " " + oPackedData.data.response.ce_respuesta );

                        window.clearInterval(g_arrTicketsPending[iTicket].handler);
                        delete g_arrTicketsPending[iTicket];
                        $btnEnviar.prop("disabled", false);
                        g_bCommandInProcess = false;
                    }
                    break;
                }
                default:
                {
                    $lblStatus.removeClass("status-ok");
                    $lblStatus.addClass("status-error");

                    $lblStatus.html("ERROR #" + oPackedData.errcode + " (" + oPackedData.errmesg + ").");
                    
                    console.warn("ERROR #" + oPackedData.errcode + " (" + oPackedData.errmesg + ").");
                    $btnEnviar.prop("disabled", false);
                    g_bCommandInProcess = false;
                    break;
                }
            }

            $lblStatus.show();
        },

        "error": function(jqxhr, status, error)
        {
            $lblStatus.removeClass("status-ok");
            $lblStatus.addClass("status-error");
            
           // $lblStatus.html("Error en AJAX: '" + url + "'.");
		   $lblStatus.html("Error al intentar enviar el paquete");
            $lblStatus.show();
            
            console.warn("Error al llamar al AJAX: '" + url + "'.");
            $btnEnviar.prop("disabled", false);
            g_bCommandInProcess = false;
        }
    });
}


function btn_EnviarCmdFav_onClick(ev)
{
    enviar_comando();
}


function btn_VerEstadoCmd_onClick(ev)
{
    g_dlgEstadoComandos.dialog("open");
}


function bindEvents()
{
    $lblStatus = $("#lbl_StatusCmdFav");
    
    $btnEnviar = $("#btn_EnviarCmdFav");
    $btnEnviar.bind("click", btn_EnviarCmdFav_onClick);

    //$btnVerEstado = $("#btn_VerEstadoCmd");
    //$btnVerEstado.bind("click", btn_VerEstadoCmd_onClick);

    if ( g_bCommandInProcess )
    {
        $btnEnviar.prop("disabled", true);
    }

    console.log("EVENTS have been BOUND.");
}
