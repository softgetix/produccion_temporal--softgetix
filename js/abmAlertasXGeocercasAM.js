var camposOK=false;

function filtrarGruposMoviles(val){
    if (val !== '0'){
        if (typeof filtroBoxes.Moviles === 'undefined'){
            filtroBoxes.Moviles=[];
        }
        $.merge(filtroBoxes.Moviles,$('#lstMoviles option').not('.grp_'+val).detach());
    }
}

function filtrarPerfilesUsuarios(val){
    if (val !== '0'){
        if (typeof filtroBoxes.Usuarios === 'undefined'){
            filtroBoxes.Usuarios=[];
        }
        $.merge(filtroBoxes.Usuarios,$('#lstUsuarios option').not('.pe_id_'+val).detach());
    }
}

function filtrarGruposGeocercas(val){
    //alert(val);
    if (val !== '0'){
        if (typeof filtroBoxes.Geocercas === 'undefined'){
            filtroBoxes.Geocercas=[];
        }
        $.merge(filtroBoxes.Geocercas,$('#lstGeocercas option').not('.rg_'+val).detach());
    }
}

function enviarMailPrueba(){
    var usuarios=[],otros=$('#txtOtrosEmail').val(),obj={},flag=false;
    $('#lstUsuariosElegidos option').each(function(){
        usuarios.push(this.value);
    });
    if (usuarios.length>0){
        obj.usuarios=usuarios.join(',');
        flag=true;
    }
    if (otros){
        obj.mails=otros;
        flag=true;
    }
    if (flag){
        $('#imgCargando').show();
        $.getJSON(
            'ajaxEnviarMailPruebaGeocercas.php',
            obj,
            function(d){
                if (d.ok){
                    alert(arrLang['prueba_ok']);
                }else{
                    alert(arrLang['prueba_error']);
                }
                $('#imgCargando').hide();
            }
		);
    }
}

(function(){
    var _enviar=window.enviar;
    window.enviar=function(tipo){
        var mensaje='',temp;
        //alert(tipo);
        if ( tipo === 'guardarA' || tipo === 'guardarM' ){
            var str = "";
            $("#lstGeocercasElegidas option").each(function(i){
                str = str + $(this).val() + ",";
            });
            
            $("#hid_lstGeocercasElegidas").val(str);
                    
            var str = "";
            $("#lstAlertasElegidas option").each(function(i){
                str = str + $(this).val() + ",";
            });
            $("#hid_lstAlertasElegidas").val(str);
                    
            var str = "";
            $("#lstMovilesElegidos option").each(function(i){
                str = str + $(this).val() + ",";
            });
            $("#hid_lstMovilesElegidos").val(str);
                    
            var str = "";
            $("#lstUsuariosElegidos option").each(function(i){
                str = str + $(this).val() + ",";
            });
            $("#hid_lstUsuariosElegidos").val(str);
                    
            $("#hid_txtOtrosEmail").val($("#txtOtrosEmail").val());
                    
            if ($("#radDentroFuera1").prop('checked') == true) {
                // 1 dentro, 2 Fuera
                $("#hid_radDentroFuera").val(1);
            } else {
                $("#hid_radDentroFuera").val(0);
            }
            
            $("#hid_chkNoNotificar").val($("#chkNoNotificar").val());
                    
            if ($('#txtNombre').val()===''){
                mensaje += arrLang['campo_nombre']+"\n";
            }
                        
            /*if ($('.radHid[value="1"]:checked').length===0){
				mensaje += "Debe seleccionar al menos uno de \"Alerta por geocerca\" o \"Alerta por eventos\"\n";
			}else */
              
            useVelocMin = false;
            useVelocMax = false;

            if ($('#chkVelMax').prop('checked')){
                useVelocMax = true;
                temp=parseInt($('#txtVelMax').val(),10);
                if (isNaN(temp) || temp < 1 || temp.toString()!==$.trim($('#txtVelMax').val())){
                    mensaje += arrLang['alertas_txt_msg9']+"\n";
                } else {
                    seleccionoVelEv = true
                }
            }

            if ($('#chkVelMin').prop('checked')){
                useVelocMin = true;
                temp=parseInt($('#txtVelMin').val(),10);
                if (isNaN(temp) || temp < 1 || temp.toString()!==$.trim($('#txtVelMin').val())){
                    mensaje += arrLang['alertas_txt_msg10']+"\n";
                } else {
                    seleccionoVelEv = true
                }
            }

            velocMin = 1;
            if ( useVelocMin )
            {
                velocMin = parseInt( $("#txtVelMin").val() );
                velocMin = Math.min(velocMin, 999); // limito el maximo de velocidad
                velocMin = Math.max(velocMin, 1); // limito el minimo de velocidad

                $("#hid_txtVelMin").val( velocMin );
            }

            velocMax = 999;
            if ( useVelocMax )
            {
                velocMax = parseInt( $("#txtVelMax").val() );
                velocMax = Math.min(velocMax, 999); // limito el maximo de velocidad
                if ( useVelocMin )
                {
                    velocMax = Math.max(velocMax, velocMin); // limito el minimo de velocidad
                }
                else
                {
                    velocMax = Math.max(velocMax, 1); // limito el minimo de velocidad
                }
                
                $("#hid_txtVelMax").val( velocMax );
            }

            /*
            if ($('#radGeocerca1').prop('checked')){
                //if ($('#hid_lstGeocercasElegidas').val()===''){
                if ($('#lstGeocercasElegidas').val()===''){
                    mensaje += "Debe seleccionar al menos una geocerca para la alerta\n";
                }
                var seleccionoVelEv = false;
                if ($('.radDentroFuera:checked').length===0){
                    $('#radDentroFuera1').attr("checked","checked");
                //mensaje += "Debe elegir si la alerta se registrar\u00E1 dentro o fuera de las geocercas\n";
                }

                if ($('#chkVelMax').prop('checked')){
                    useVelocMax = true;
                    temp=parseInt($('#txtVelMax').val(),10);
                    if (isNaN(temp) || temp < 1 || temp.toString()!==$.trim($('#txtVelMax').val())){
                        mensaje += arrLang['alertas_txt_msg9']+"\n";
                    } else {
                        seleccionoVelEv = true
                    }
                }

                if ($('#chkVelMin').prop('checked')){
                    useVelocMin = true;
                    temp=parseInt($('#txtVelMin').val(),10);
                    if (isNaN(temp) || temp < 1 || temp.toString()!==$.trim($('#txtVelMin').val())){
                        mensaje += arrLang['alertas_txt_msg10']+"\n";
                    } else {
                        seleccionoVelEv = true
                    }
                }

                if ($('#radAlEventos1').prop('checked')){
                    //if ($('#hid_lstAlertasElegidas').val()===''){
                    if ($('#lstAlertasElegidas').val()===''){
                    //mensaje += arrLang['alertas_txt35']+"\n";
                    } else {
                        seleccionoVelEv = true
                    }
                }

                if (seleccionoVelEv === false) {
                    mensaje += "Debe ingresar velocidades y/o seleccionar al menos un evento para la alerta\n";
                }
            }
            */
            if ($('#radAlEventos1').prop('checked')){
                //if ($('#hid_lstAlertasElegidas').val()===''){
                if ($('#lstAlertasElegidas').val()===''){
                    mensaje += arrLang['alertas_txt35']+"\n";
                }
            }
			
            //if ($('#hid_lstMovilesElegidos').val()===''){
            if ($('#lstMovilesElegidos').val()===''){
                mensaje += arrLang['alertas_txt37']+"\n";
            }
            //if ($('#hid_lstUsuariosElegidos').val()==='' && $('#txtOtrosEmail').val()===''){
            /*
			if ($('#lstUsuariosElegidos').val()==='' && $('#txtOtrosEmail').val()===''){
            	mensaje += "Debe seleccionar al menos un destinatario para la alerta\n";
            }*/
        }
        if (mensaje){
            alert(mensaje);
        }else{
            _enviar(tipo);
        }
    };
}());

$(document).ready(function(){
	
    if ($('#radGeocerca2').attr("checked") != "undefined" && $('#radGeocerca2').attr("checked") == "checked"){
        $('#trDentroFuera').hide();
    } 
	
    $('.radHid').click(function(){
        $('#'+this.name.replace('rad','fld')).toggleClass('hidder',!Boolean(parseInt(this.value,10)));
    });
    $('.chkOptional').change(function(){
        $('#'+this.id.replace('chk','txt')).prop('disabled',!this.checked);
    });
    $('#cmbGrupoMoviles').change(function(){
        $('#txtFiltroMoviles').keyup();
    });
    $('#cmbFiltroGeocercas').change(function(){
        $('#txtFiltroGeocercas').keyup();
    });

    $('#txtFiltroMoviles').bind('boxes.filterEnd',function(){
        var val=$('#cmbGrupoMoviles').val();
        if (val!=='0'){
            filtrarGruposMoviles(val);
        }
    });

    $('#txtFiltroGeocercas').bind('boxes.filterEnd',function(){
        var val=$('#cmbFiltroGeocercas').val();
        if (val!=='0'){
            filtrarGruposGeocercas(val);
        }
    });
	
    $('#cmbCCosto').change(function(){
        $('#txtFiltroUsuarios').keyup();
    });
	
    $('#txtFiltroUsuarios').bind('boxes.filterEnd',function(){
        var val=$('#cmbCCosto').val();
        if (val!=='0'){
            filtrarPerfilesUsuarios(val);
        }
    });
	
    $('#probar').click(enviarMailPrueba);

    $('#cmbPlantilla').change(function(){
        var val=$(this).val();

        $('#radGeocerca2,#radAlEventos2').prop('checked',true).click();
        $('#cmbGrupoMoviles').val('0');
        $('.txtFiltroTransfer,#txtOtrosEmail').val('').keyup();
        $('.ref_btnIzqT').click();
        $('.ref_lstIzq').val([]);
        $('.chkOptional').prop('checked',false).change();

        if (val > 0){
            $.getJSON(
                'ajaxObtenerAlertaXGeocerca.php',
                'id=' + val,
                function(d){
                    if (d.ref){
                        $('#lstGeocercas').val(d.referencias);
                        $('#radDentroFuera'+ (2-d.al_dentro_fuera).toString()).prop('checked',true);
                        if (d.vel_max){
                            $('#chkVelMax').prop('checked',true);
                            $('#txtVelMax').prop('disabled',false).val(d.vel_max);
                        }
                        if (d.vel_min){
                            $('#chkVelMin').prop('checked',true);
                            $('#txtVelMin').prop('disabled',false).val(d.vel_min);
                        }
                        $('#radGeocerca1').prop('checked',true).click();
                    }
                    if (d.eve){
                        $('#lstAlertas').val(d.eventos);
                        $('#radAlEventos1').prop('checked',true).click();
                    }
                    $('#lstMoviles').val(d.moviles);
                    $('#lstUsuarios').val(d.usuarios);
                    $('#txtOtrosEmail').val(d.otros);
                    $('.ref_btnDer').click();
                }
                );
        }
    });
	
    $('#radGeocerca2').click(function(){
        if ($(this).attr("checked") != "undefined" && $(this).attr("checked") == "checked"){
            $('#trDentroFuera').hide();
        } 
    });
	
    $('#radGeocerca1').click(function(){
        if ($(this).attr("checked") != "undefined" && $(this).attr("checked") == "checked"){
            $('#trDentroFuera').show();
        } 
    });
	
    $('.btn_sig').click(function(){
        var parent=$(this).parentsUntil('fieldset','.fld_wrapper').animate({
            height:0
        },500,'swing'),
        next = parent.parentsUntil('#mainBoxAM','fieldset').nextAll('fieldset:not(.hidder)').eq(0).children('.fld_wrapper').animate({
            height:'100%'
        },500,'swing',function(){
            $(this).scrollTop(0);
        });
        $('#' + next.attr('id')).show();
        //alert(next.attr('id'));
        /*if (next.attr('id')==='pruebas'){
			$('#nomenclado').show();
		}
		if (next.attr('id')==='geocercas'){
			$('#geocercas').show();
		}
		if (next.attr('id')==='eventos'){
			$('#eventos').show();
		}
		if (next.attr('id')==='moviles'){
			$('#moviles').show();
		}
		if (next.attr('id')==='usuarios'){
			$('#usuarios').show();
		}*/
        modificarMensaje();
    });
	
	
	
    $('.btn_ant').click(function(){
        var parent=$(this).parentsUntil('fieldset','.fld_wrapper').animate({
            height:0
        },500,'swing');
        parent.parentsUntil('#mainBoxAM','fieldset').prevAll('fieldset:not(.hidder)').eq(0).children('.fld_wrapper').animate({
            height:'100%'
        },500,'swing',function(){
            $(this).scrollTop(0);
        });
        //alert(parent.attr('id'));
        if (parent.attr('id')==='pruebas'){
            $('#nomenclado').hide();
        }
    });

    $('.btn_fin').click(function(){
        enviar('guardar'+$('#hidOperacion').val().charAt(0).toUpperCase());
    });

    $("#radDurationAllDays").bind("click", function(ev){
        $("#divCustomDuration").slideUp("1000", function(){
            $("#fldCustomDuration").prop("disabled", true);
            $("#trCustomDuration").hide();
        });
    });

    $("#radDurationCustom").bind("click", function(ev){
        $("#trCustomDuration").show();
        $("#fldCustomDuration").prop("disabled", false);
        $("#divCustomDuration").slideDown("1000", null);
    });
    
    $("#chkLuVi").bind("click", function(ev){
        //$("#fsetDurationLuVi").prop( "disabled", !$(this).prop("checked") );
		$("#cboDurationLuVi_desde").prop("disabled", !$(this).prop("checked") );//rds
		$("#cboDurationLuVi_hasta").prop("disabled", !$(this).prop("checked") );		
    });
    
    $("#chkSabDo").bind("click", function(ev){
        //$("#fsetDurationSabDo").prop( "disabled", !$(this).prop("checked") );		
		$("#cboDurationSabDo_desde").prop("disabled",!$(this).prop("checked") );//rds
		$("#cboDurationSabDo_hasta").prop("disabled",!$(this).prop("checked") );
    });
	
});


function modificarMensaje() {

    var dentro = false;
    if ($('#radDentroFuera1').attr("checked") != "undefined" && $('#radDentroFuera1').attr("checked") == "checked"){
        dentro = true;
    }
	
    var max = false;
    if ($('#chkVelMax').attr("checked") != "undefined" && $('#chkVelMax').attr("checked") == "checked"){
        max = true;
    }
	
    var min = false;
    if ($('#chkVelMin').attr("checked") != "undefined" && $('#chkVelMin').attr("checked") == "checked"){
        min = true;
    }
	
    var evento = false;
    if ($('#radAlEventos1').attr("checked") != "undefined" && $('#radAlEventos1').attr("checked") == "checked"){
        evento = true;
    }
	
    var geocerca = false;
    if ($('#radGeocerca1').attr("checked") != "undefined" && $('#radGeocerca1').attr("checked") == "checked"){
        geocerca = true;
    } 
    var mensaje = "";
    if (geocerca && evento) {
       	mensaje += (dentro)?arrLang['alertas_txt_msg1']:arrLang['alertas_txt_msg2'];
        if (max && min){
           	mensaje += " "+arrLang['alertas_txt_msg4'];
        } else if (max) {
            mensaje += " "+arrLang['alertas_txt_msg5'];
        } else if (min) {
            mensaje += " "+arrLang['alertas_txt_msg6'];
        }
        mensaje += " "+arrLang['alertas_txt_msg3'];
    } else if (geocerca) {
        mensaje += " "+arrLang['alertas_txt_msg7'];
        if (max && min) {
            mensaje += " "+arrLang['alertas_txt_msg4'];
        } else if (max) {
            mensaje += " "+arrLang['alertas_txt_msg5'];
        } else if (min) {
            mensaje += " "+arrLang['alertas_txt_msg6'];
        }
        mensaje += " "+arrLang['alertas_txt_msg3'];
    } else if (evento) {
        mensaje += " "+arrLang['alertas_txt_msg8'];
        mensaje += " "+arrLang['alertas_txt_msg3'];
    }
    $('#msjDescripcion').text(mensaje)
}


//## inicio. DEFINIR CANT DE ELEMENTOS PARA LAS REFERENCIAS##//
var ref_alertas = {
	'a' : {moviles: 50, zonas: 200, eventos:null},
	'b' : {moviles: null, zonas: null, eventos:20},
	'c' : {moviles: 50, zonas: 20, eventos:20}
};
	
$('.ref_btnDer').live("click",function(){
	var fbox=$('.ref_lstIzq',$(this).parent().parent()).attr('id');
	var	tbox=$('.ref_lstDer',$(this).parent().parent()).attr('id');
	if(validateCountElement(tbox)){
		if(validarTecnologias(fbox, tbox) == true){
			move(fbox,tbox);
			actualizarHidden(tbox);
			viewCountElement(tbox);	
		}
	}
	else{msgAlertas();}
});

$('.ref_btnDerT').live("click",function(){
	var fbox=$('.ref_lstIzq',$(this).parent().parent()).attr('id');
	var	tbox=$('.ref_lstDer',$(this).parent().parent()).attr('id');
	$('#'+fbox+' option').attr('selected','selected');
	if(validateCountElement(tbox)){
		if(validarTecnologias(fbox, tbox) == true){
			move(fbox,tbox);
			actualizarHidden(tbox);
			viewCountElement(tbox);
		}
	}
	else{msgAlertas();}
});

$('.ref_btnIzq').live("click",function(){
	var tbox=$('.ref_lstIzq',$(this).parent().parent()).attr('id');
	var fbox=$('.ref_lstDer',$(this).parent().parent()).attr('id');
	if(validateCountElement(tbox) == true){
		move(fbox,tbox);
		actualizarHidden(fbox);
		viewCountElement(tbox);
	}
	else{msgAlertas();}
});

$('.ref_btnIzqT').live("click",function(){
	var tbox=$('.ref_lstIzq',$(this).parent().parent()).attr('id');
	var fbox=$('.ref_lstDer',$(this).parent().parent()).attr('id');
	$('#'+fbox+' option').attr('selected','selected');
	if(validateCountElement(tbox)){
		move(fbox,tbox);
		actualizarHidden(fbox);
		$('#hid_'+fbox).val('');
		viewCountElement(tbox);
	}
	else{msgAlertas();}
});

$('.ref_lstDer').live("dblclick",function(){
	var tbox=$('.ref_lstIzq',$(this).parent().parent()).attr('id');
	var	fbox=this.id;
	if(validateCountElement(tbox)){
		move(fbox,tbox);
		actualizarHidden(fbox);
		viewCountElement(tbox);
	}
	else{msgAlertas();}
});

$('.ref_lstIzq').live("dblclick",function(){
	var tbox=$('.ref_lstDer',$(this).parent().parent()).attr('id');
	var	fbox=this.id;
	if(validateCountElement(tbox)){
		if(validarTecnologias(fbox, tbox)){
			move(this.id,tbox);
			actualizarHidden(tbox);
			viewCountElement(tbox);
		}
	}
	else{msgAlertas();}
});
	
function viewCountElement(tipoAlerta){ 
	$("#cantMoviles, #cantZonas, #cantEventos").html("");
	$("#cantMovilesElegidos, #cantMovilesElegidos, #cantEventosElegidos").html("");
	
	if(tipoAlerta == 'moviles' || tipoAlerta == 'lstMovilesElegidos' || tipoAlerta == 'lstMoviles'){
		if(parseInt(ref_alertas[plantilla]['moviles']) > 0){
			$("#cantMoviles").html(arrLang['max']+' '+ref_alertas[plantilla]['moviles']+' '+arrLang['moviles']);
		}
		$("#cantMovilesElegidos").html(arrLang['cant']+': '+$("#lstMovilesElegidos option").size());
	}
	if(tipoAlerta == 'geocercas' || tipoAlerta == 'lstGeocercasElegidas' || tipoAlerta == 'lstGeocercas'){
		if(parseInt(ref_alertas[plantilla]['zonas']) > 0){
			$("#cantZonas").html(arrLang['max']+' '+ref_alertas[plantilla]['zonas']+' '+arrLang['geocercas']);
		}
		$("#cantZonasElegidos").html(arrLang['cant']+': '+$("#lstGeocercasElegidas option").size());
	}
	if(tipoAlerta == 'eventos' || tipoAlerta == 'lstAlertasElegidas' || tipoAlerta == 'lstAlertas'){
		if(parseInt(ref_alertas[plantilla]['eventos']) > 0){
			$("#cantEventos").html(arrLang['max']+' '+ref_alertas[plantilla]['eventos']+' '+arrLang['eventos']);
		}
		$("#cantEventosElegidos").html(arrLang['cant']+': '+$("#lstAlertasElegidas option").size());
	}
}

function validateCountElement(tipoAlerta){
	var returned = true;
	
	if(tipoAlerta == 'lstMovilesElegidos'){
		if(parseInt(ref_alertas[plantilla]['moviles']) > 0){
			var total = parseInt($("#lstMoviles option:selected").size()) + parseInt($("#lstMovilesElegidos option").size());
			if(total > parseInt(ref_alertas[plantilla]['moviles'])){
				returned = false;		
			}
		}
	}
	else if(tipoAlerta == 'lstGeocercasElegidas'){ 
		if(parseInt(ref_alertas[plantilla]['zonas']) > 0){
			var total = parseInt($("#lstGeocercas option:selected").size()) + parseInt($("#lstGeocercasElegidas option").size());
			if(total > parseInt(ref_alertas[plantilla]['zonas'])){
				returned = false;		
			}
		}
	}
	else if(tipoAlerta == 'lstAlertasElegidas'){ 
		if(parseInt(ref_alertas[plantilla]['eventos']) > 0){
			var total = parseInt($("#lstAlertas option:selected").size()) + parseInt($("#lstAlertasElegidas option").size());
			if(total > parseInt(ref_alertas[plantilla]['eventos'])){
				returned = false;		
			}
		}
	}
	
	if($('#idTipoEmpresa').val() == 3 || $('#idTipoEmpresa').val() == 1){
		returned = true;
	}
	
	return returned;
}

function msgAlertas(){
	alert(arrLang['alertas_txt_msg11']);	
}
//## fin. DEFINIR CANT DE ELEMENTOS PARA LAS REFERENCIAS##//

function validarTecnologias(fbox, tbox){
	resp = true;
	if(fbox == 'lstMoviles'){
		var comboDer = document.getElementById(fbox);
		var comboIzq = document.getElementById(tbox);
		var ids_movil = '';
		var coma = '';
		
		for(var i=0; i < comboDer.options.length; i++) {
			if(comboDer.options[i].selected && comboDer.options[i].value != "") {
				ids_movil = ids_movil.concat(coma).concat(comboDer.options[i].value);
				coma = ',';
			}
		}
		
		for(var i=0; i < comboIzq.options.length; i++) {
			if(comboIzq.options[i].value != "") {
				ids_movil = ids_movil.concat(coma).concat(comboIzq.options[i].value);
				coma = ',';
			}
		}
		
		var resp = "";
		$.ajax({
			async:false,
			cache:false,
			type: "POST",
			url: "ajaxAlertasXGeocercas.php",
			data:({
				accion:'get-validarTecnologia',
				moviles:ids_movil
			}),
			success: function(msg){
				if(msg == true){
					alert(arrLang['alertas_txt37']);
					resp = true;//false;	
				}
				else{
					resp = true;
				}
			},	
			beforeSend:function(){},
			error:function(objXMLHttpRequest){}	
		});
	}
	
	return resp;
}