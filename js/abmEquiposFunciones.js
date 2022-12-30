function obtenerModelos(marca,modelo){
	if (!modelo) modelo = 0;
	//simple_ajax("ajaxObtenerModelos.php?marca=" +  marca +"&modelo=" +  modelo +"&p=0");
	$('select#cmbModelo option').not(':eq(0)').remove();
	var optionSelect = $('select#cmbModelo option:eq(0)').html();
	ajax_sincronico('ajaxObtenerModelos.php?marca='+marca+'&modelo='+modelo+'&p=0');
	$('select#cmbModelo option:eq(0)').val(0).text(optionSelect);
	//$('select#cmbModelo option[value='+$('#hidden_cliente_factuar').val()+']').attr('selected','selected');
		
	
}

function imprimir(){
	var filtro = document.getElementById('txtFiltro').value;
	var url = 'boot.php?c=abmEquipos&method=export_prt&filtro='+filtro;
	window.open(url, '', 'width=800, height=500');
}