/*global $*/
var $divHorarios;
var $option=$('<option>');
function actualizarUbicaciones(){
	var cmbUbicacion=$('#cmbUbicacion');
	$.ajax(
		'ajaxObtenerDatosCombo.php',
		{
			async:false,
			data:'t=ReferenciasPorEmpresa2%20'+$("#idEmpresa").val(),
			dataType:'json',
			success:function(data){
				cmbUbicacion.children('option[value!="0"]').remove();
				//alert(cmbUbicacion.attr('id'))
				var $option=$('<option>');
				$.each(data, function(i,x){
					$option.clone().val(x.re_id).text(x.re_nombre + '/' + x.re_descripcion).appendTo(cmbUbicacion);
				});
			}
		}
	);
}

function retornoPopup(data){
	var cmbUbicacion=$('#cmbUbicacion');
	switch (data.tipo){
		case 'referencia':
			actualizarUbicaciones();
			cmbUbicacion.val(data.id);
			$('#auto_cmbUbicacion').val(data.nombre +"/  ");
			cmbUbicacion.change();
		break;
	}
}

function getDateToday() {
	var currentTime = new Date()
	var month = currentTime.getMonth() + 1
	if (month < 10) {
		month = "0" + month;
	}
	var day = currentTime.getDate()
	if (day < 10) {
		day = "0" + day;
	}
	var year = currentTime.getFullYear()
	return day + "/" + month + "/" + year; 
}

$(document).ready(function(){
	
/*	$('#btncambiar').click(function(){
		$('#cmbUbicacion').val(16);
		$('#auto_cmbUbicacion').val("Localizar-T Circular /  ");
		$('#cmbUbicacion').change();
	})*/


	$('#txtDiaEmpieza').datepicker();
	$('#txtDiaTermina').datepicker();
	$('#txtRepEmpieza').datepicker();
	$('#txtRepTermina').datepicker();
	
	
	$divHorarios=$('#divHorarios');
	
	$('#chkHorario').change(function(){
		$divHorarios.toggle(this.checked);
		var hoy=new Date();
		
		var dateHoraEmpieza = hoy.getHours();
		var dateHoraTermina = addTimeToDate(1,'h',hoy).getHours();
		
		if(dateHoraEmpieza <= 9) {
			dateHoraEmpieza = ceroIzquierda(dateHoraEmpieza);
		}
		
		$('#txtDiaEmpieza').val(getDateToday());
		$('#txtDiaTermina').val(getDateToday());
		
		$('#cmbHoraEmpieza').val(dateHoraEmpieza);
		$('#cmbHoraTermina').val(dateHoraTermina);
		
	});
	$('#btnNewRef').click(function(){
		mostrarPopup('boot.php?c=abmReferencias&action=popup&ref=abmViajes');
	})
	$('#btnAgregar').click(function(){
		var horaEmpieza = $('#cmbHoraEmpieza').val()+$('#cmbMinutEmpieza').val();
		var horaTermina =$('#cmbHoraTermina').val()+$('#cmbMinutTermina').val();
		var fechaEmpieza = $('#txtDiaEmpieza').val();
		var fechaTermina = $('#txtDiaTermina').val();
		//alert( "Empieza Fecha: "+fechaEmpieza+"Hora:"+horaEmpieza+"\r\n Termina: Fecha: "+fechaTermina+" Hora:"+horaTermina);
		if(horaEmpieza <= horaTermina && !compareFechasJquery(fechaEmpieza,fechaTermina)){
			
			var $ref=$('#cmbUbicacion'), texto=$.trim($ref.children(':selected').text()),
			empieza=' --- ',termina=' --- ',
			$td=$('<td/>');
			if ($ref.val()!=='0'){
				if ($('#chkHorario').prop('checked')){
					empieza=$('#txtDiaEmpieza').val()+' '+$('#cmbHoraEmpieza').val()+':'+$('#cmbMinutEmpieza').val();
					termina=$('#txtDiaTermina').val()+' '+$('#cmbHoraTermina').val()+':'+$('#cmbMinutTermina').val();
				}
				
			if( $('#vi_id').val() == 0){
					 $('<tr/>').append(
								  $td.clone().text(texto).append($('<input type="hidden" name="ref_id[]" value="'+$ref.val()+'"/>')),
								  $td.clone().text(empieza).append($('<input type="hidden" name="ini[]" value="'+empieza+'"/>')),
								  $td.clone().text(termina).append($('<input type="hidden" name="fin[]" value="'+termina+'"/>')),
								  $td.clone().html('<img src="imagenes/cruz.png" class="btnDel" alt=""/>')
								  
								 ).appendTo('#tblUbicaciones');
								 // <img src="imagenes/mover_u.png" class="btnUp" alt=""/> <img src="imagenes/mover_d.png" class="btnDown" alt=""/>
			 } else {
					$('<tr/>').append(		  
								  $td.clone().text(texto).append($('<input type="hidden" name="ref_id[]" value="'+$ref.val()+'"/>')),
								  $td.clone().text(empieza).append($('<input type="hidden" name="ini[]" value="'+empieza+'"/>')),
								  $td.clone().text(termina).append($('<input type="hidden" name="fin[]" value="'+termina+'"/>')),
								  $td.clone().append($('<input type="hidden" value="0" name="es_antiguo[]"/>')),
								  $td.clone().html('<img src="imagenes/cruz.png" class="btnDel" alt=""/>')
								   ).appendTo('#tblUbicaciones');
			}
				
								 
							 
								
				$('#chkHorario').prop('checked',false);
				$divHorarios.hide();
				$('#txtDiaEmpieza,#txtDiaTermina,#auto_cmbUbicacion').val('')
				$('#cmbHoraEmpieza,#cmbHoraTermina,#cmbMinutEmpieza,#cmbMinutTermina').val('0')
				$ref.val('0');
			}
		}
	});

	$('.btnDel').live('click',function(){
		$(this).parent().parent().remove();
	});
	$('.btnUp').live('click',function(){
		var tr=$(this).parent().parent();
		if (tr.prev('tr').length){
			tr.insertBefore(tr.prev());
		}
	});
	$('.btnDown').live('click',function(){
		var tr=$(this).parent().parent();
		if (tr.next('tr').length){
			tr.insertAfter(tr.next());
		}
	});

	$('.btn_sig').click(function(){
		var parent=$(this).parentsUntil('fieldset','.fld_wrapper').animate({height:0},500,'swing'),
		next = parent.parentsUntil('#mainBoxAM','fieldset').nextAll('fieldset:not(.hidder)').eq(0).children('.fld_wrapper').animate({height:'100%'},500,'swing',function(){$(this).scrollTop(0);});
		if (next.attr('id')==='fieldB'){
			$('#fieldB').show();
		}
		//modificarMensaje();
	});
	
	
	$('.btn_ant').click(function(){
		var parent=$(this).parentsUntil('fieldset','.fld_wrapper').animate({height:0},500,'swing');
		parent.parentsUntil('#mainBoxAM','fieldset').prevAll('fieldset:not(.hidder)').eq(0).children('.fld_wrapper').animate({height:'100%'},500,'swing',function(){$(this).scrollTop(0);});
		if (parent.attr('id')==='fieldA'){
			$('#fieldA').hide();
		}
	});

	$('.btn_fin').click(function(){
		var env = 'guardar'+$('#hidOperacion').val().charAt(0).toUpperCase();
		//alert(env);
		enviar(env);
	});

	//'txtObservaciones'
	//R-> procesarTips(['txtCodigo','auto_cmbConductor','auto_cmbMovil','auto_cmbUsuario','auto_cmbUbicacion']);
});
