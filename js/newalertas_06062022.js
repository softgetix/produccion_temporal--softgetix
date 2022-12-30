
newAlertas = {};

$( document ).bind( "ready", function()
{
    g_dlgConfirmarAlerta = $("#dlgConfirmarAlerta");
    g_dlgConfirmarAlerta.dialog( {
        "autoOpen": false,
        "modal": true,
        "minWidth": 500,
        "close": function(){
            //console.log("Alerta confirmada");
        },
		"buttons":[
			{text: arrLang['confirmar'],
			click: function (){
				var id = $("#alerta_id").val(), ids = $("#alerta_ids").val(), motivo_conf = $("#cmbMotivoConfirmacion").val();
				confirmarAlerta( id, ids, motivo_conf );
				$(this).dialog("close");
			}},
			{text: arrLang['cancelar'],
        	click: function () {
            	$(this).dialog("close");
        	}}
		]
    });
});


function confirmarAlerta_onClick(id, ids)
{
    $("#alerta_id").val( id );
    $("#alerta_ids").val( ids.join(",") );
    g_dlgConfirmarAlerta.dialog("open");
}


newAlertas.agregarEnGrilla = function(global_index, alerta, ref)
{
    g_arrAlertas[global_index] = {
        "matricula":    alerta.matricula,
        "id_reporte":   alerta.id_reporte,
        "id":           alerta.id,
        "ids":          alerta.arr_ids,
        "ocurrencias":  alerta.ocurrencias
    };

    $tr_alerta = $("<tr>")
        .attr({ "id": "alerta_" + alerta.id, "class": "alerta" });

    $link_matricula = $("<a>")
        .attr({ "id": "alerta_link_matricula_" + alerta.id, "href": "#" })
        .html(alerta.matricula)
        .bind( "click", function(ev)
        {
            zoomMoviles();
            newTracer.seguirMovil(alerta.movilid);
        });

    nombreEmpresa = ( alerta.nombreEmpresa.length > NOMBREEMPRESA_MAX_LENGTH
        ? alerta.nombreEmpresa.substr(0, NOMBREEMPRESA_MAX_LENGTH - 3) + "..."
        : alerta.nombreEmpresa
    );
        
    $span_nombreEmpresa =   $("<span>")
        .attr({ "class": "nombreEmpresa", "title": alerta.nombreEmpresa })
        .html( nombreEmpresa );
        
    $td_matricula       =   $("<td>").css({ "width": "10%" })
        .append( $link_matricula )
        .append("<br/>")
        .append( $span_nombreEmpresa )
        .appendTo( $tr_alerta );
        
    $td_rumbo           =   $("<td>")
        .attr({ "id": "alerta_rumbo_" + alerta.id })
        .css({ "width": "5%"  })
        .html( calcularRumbo( alerta.sentido, 1 ) )
        .appendTo( $tr_alerta );
        
    $td_velocidad       =   $("<td>")
        .attr({ "id": "alerta_velocidad_" + alerta.id })
        .css({ "width": "5%"  })
        .html( alerta.velocidad )
        .appendTo( $tr_alerta );
        
    $td_recibido        =   $("<td>")
        .attr({ "id": "alerta_recibido_" + alerta.id })
        .css({ "width": "10%" })
        .html( alerta.recibido )
        .appendTo( $tr_alerta );
        
    $td_generado        =   $("<td>")
        .attr({ "id": "alerta_generado_" + alerta.id })
        .css({ "width": "10%" })
        .html( alerta.generado )
        .appendTo( $tr_alerta );
        
    $td_evento          =   $("<td>")
        .attr({ "id": "alerta_evento_" + alerta.id })
        .css({ "width": "25%" })
        .html( ((alerta.alerta!=null)?alerta.alerta:'') + " (" + alerta.descripcion + ref + ")" )
        .appendTo( $tr_alerta );
        
    $td_ocurrencias     =   $("<td>")
        .attr({ "id": "alerta_ocurrencias_" + alerta.id })
        .css({ "width": "5%"  })
        .html( alerta.ocurrencias )
        .appendTo( $tr_alerta );
        
    $link_nomenclado = $("<a>");
    $link_nomenclado
        .attr(
        { 
            "id": "link_nomenclado_" + alerta.id,
            "href": "javascript: void(0);",
            "data-lat": alerta.latitud,
            "data-lng": alerta.longitud
        })
        .bind("click", newAlertas.centrarAlertaEnMapa)
        .html( alerta.nomenclado );
    $td_nomenclado      =   $("<td>")
        .attr({ "id": "alerta_nomenclado_" + alerta.id })
        .css({ "width": "25%" })
        .append( $link_nomenclado )
        .appendTo( $tr_alerta );
        
    $link_confirmar     =   $("<a>")
        .attr({ "href" : "#", "class": "link", "data-rowid": global_index })
        .html(arrLang['confirmar'])
        .bind( "click", function(ev)
        {
            var indice = $(this).attr("data-rowid");
            confirmarAlerta_onClick(g_arrAlertas[indice].id, g_arrAlertas[indice].ids);
        });
        
    $td_confirmar       = $("<td>")
        .attr({ "id": "alerta_confirmar_" + alerta.id })
        .css({ "width":  "5%" })
        .append( $link_confirmar )
        .appendTo( $tr_alerta );

    // Insertar en la cima de la GRILLA
    $tr_alerta.prependTo( "#alertas_body" );
    
    g_iCantFilasAlertas++;
};


newAlertas.centrarAlertaEnMapa = function(ev){
    var $this = $(this);
   	mapSetCenter($(this).attr("data-lat"), $(this).attr("data-lng"));
	mapSetZoom(15);
};


newAlertas.actualizarEnGrilla = function(global_index, alerta)
{
    // sumo los ids que llegan a los existentes
    for (var a = 0; a < alerta.ocurrencias; a++ )
    {
        g_arrAlertas[global_index].ids.push( alerta.arr_ids[a] );
    }
    g_arrAlertas[global_index].ocurrencias = g_arrAlertas[global_index].ids.length;

    var $alerta_rumbo       = $("#alerta_rumbo_" + g_arrAlertas[global_index].id);
    var $alerta_velocidad   = $("#alerta_velocidad_" + g_arrAlertas[global_index].id);
    var $alerta_recibido    = $("#alerta_recibido_" + g_arrAlertas[global_index].id);
    var $alerta_generado    = $("#alerta_generado_" + g_arrAlertas[global_index].id);
    var $alerta_ocurrencias = $("#alerta_ocurrencias_" + g_arrAlertas[global_index].id);
    var $alerta_nomenclado  = $("#link_nomenclado_" + g_arrAlertas[global_index].id);

    $alerta_rumbo.html( calcularRumbo( alerta.sentido, 1 ) );
    $alerta_velocidad.html( alerta.velocidad );
    $alerta_recibido.html( alerta.recibido );
    $alerta_generado.html( alerta.generado );
    $alerta_ocurrencias.html( g_arrAlertas[global_index].ocurrencias );
    $alerta_nomenclado.html( alerta.nomenclado );
    $alerta_nomenclado.attr(
    {
        "data-lat": alerta.latitud,
        "data-lng": alerta.longitud
    });

    usar_alertas_ranking = true;
    
    if ( usar_alertas_ranking )
    {
        var $tabla_alertas = $( "#alertas_body" );
        var $tr_alerta = $( "#alerta_" + g_arrAlertas[global_index].id );

        $tr_alerta = $tr_alerta.detach();
        $tabla_alertas.prepend( $tr_alerta );
    }
}