function imprimir(){
	var filtro = document.getElementById('txtFiltro').value;
	var url = 'boot.php?c=abmModeloEquipos&method=export_prt&filtro='+filtro;
	window.open(url, '', 'width=800, height=500');
}
	//R-> procesarTips(['txtFiltro','txtDescripcion','txtNombre']);

function actualizarComboClientesPorDefecto(){		
	var valor=$('#cboAgente').val();
	//alert("valor:"+valor);
	$.ajax(
		'ajaxObtenerDatosCombo.php',
		{
			async:false,
			data:'t=ClientesModelosEquiposCombo%20' + valor,
			dataType:'json',
			success:function(data){
				$('#cboClientePorDefecto').children('option[value!="0"]').remove();					
				$.each(data, function(i,x){
					$('<option>').clone().val(x.id).text(x.dato).appendTo('#cboClientePorDefecto');
				});
			}
		}
	);
}

	