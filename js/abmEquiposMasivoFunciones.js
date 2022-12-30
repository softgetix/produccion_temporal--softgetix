$(document).ready(function(){
	$('#cmbMarca').change(function(){
		var v=$(this).val();
		$.getJSON(
			'ajaxObtenerModelos-json.php',
			{marca:v},
			function(data){
				var cmbModelo=$('#cmbModelo');
				$('option[value!="0"]',cmbModelo).remove();
				$.each(data,function(i,m){
					$('<option>').val(m[0]).text(m[1]).appendTo(cmbModelo);
				});
			}
		);
	});
});
//R->	procesarTips(['txtHasta','txtDesde']);