$(document).ready(function(){
	$('#cmbMarca').change(function(){
		var v=parseInt($(this).val(),10);
		var cmbModelo=$('#cmbModelo');
		$('option[value!="0"]',cmbModelo).remove();
			$.getJSON(
				'ajaxObtenerModelos-json.php',
				{marca:v},
				function(data){
					var opt=$('<option>');
					$.each(data,function(i,m){
						opt.clone().val(m[0]).text(m[1]).appendTo(cmbModelo);
					});
					cmbModelo.trigger('loaded');
				}
			);
	}).change();
});