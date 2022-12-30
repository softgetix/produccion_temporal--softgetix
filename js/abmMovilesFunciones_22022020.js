$(document).ready(function() {
    $("#txtActivacion").datepicker({
		showOn: "button",
		buttonImage: "imagenes/calendario/bul_cal.gif",
		buttonImageOnly: true,
		dateFormat: 'yy/mm/dd'
	});
	
	if($("#hidOperacion").val() == 'alta' || $("#hidOperacion").val() == 'modificar' || $("#HidPopUp").val() == 'popup'){
		var optionSelect = $('select#cmbClientes option:eq(0)').html();
		ajax_sincronico('ajaxObtenerClienteDistribuidor.php?idDistribuidor='+$('#cmbDistribuidor').val()+'&p=1');
		$('select#cmbClientes option:eq(0)').val(0).text(optionSelect);
		$('select#cmbClientes option[value='+$('#hidden_cliente_factuar').val()+']').attr('selected','selected');
		
		var optionSelect = $('select#cmbConductor option:eq(0)').html();
		ajax_sincronico('ajaxObtenerConductoresDeCliente.php?cliente='+$('#cmbClientes').val()+'&conductor='+$('#cmbConductor').val());
		$('select#cmbConductor option:eq(0)').val(0).text(optionSelect);
		$('select#cmbConductor option[value='+$('#hidden_conductor').val()+']').attr('selected','selected');
	}
	
	if($("#hidOperacion").val() == 'modificar'){
		$('#equipo_viejo').val($('#equipo_instalado').val());
	}
	
	$("#cmbClientes").change(function(){
		var optionSelect = $('select#cmbConductor option:eq(0)').html();
		$('select#cmbConductor option').not(':eq(0)').remove();
		ajax_sincronico('ajaxObtenerConductoresDeCliente.php?cliente='+$(this).val());
	});
});

function borrarImagen() {
	$('#imagen').hide();
	$('#borrar_link').hide();
	//$('#input_imagen').show().attr('disabled', '');
	$('#borrar_foto').val('true');
}
	
function retornoPopup(d){
  $("#equipo_instalado").append('<option value="'+d.id_equipo+'" selected="selected">'+d.equipo+'</option>');
}