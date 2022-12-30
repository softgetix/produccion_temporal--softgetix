/*global $*/
$(document).ready(function(){
	$('#cmbMovil').change(function(){
		var $this=$(this);
		$.getJSON(
			'ajaxObtenerDatosCombo.php',
			't=InstalacionMovil%20'+$this.val(),
			function(d){
				if (d){
					d=d[0];
					$('#txtMovMax1').val(d.un_velexc_1);
					$('#txtMovMax2').val(d.un_velexc_2);
					$('#txtMovMaxA').val(d.um_velocidadMaxima);
					$('#txtEquipoActual').val(d.un_mostrarComo);
					$('#txtTelefonoAnt').val(d.ug_telefono);
					$('#cmbEntradas1,#cmbEntradas2,#cmbEntradas3,#cmbEntradas4').val(0);
					$.getJSON(
						'ajaxObtenerDatosCombo.php',
						't=EntradasEquipos%20'+d.un_id,
						function(d2){
							$.each(d2,function(i,v){
								$('#cmbEntradas'+v.ee_numeroEntrada).val(v.ee_id_entrada);
							});
						}
					);
				}
			}
		);
	});

	$('#chkDesinst').change(function(){
		if (this.checked){
			$('#txtEquipo').removeClass('ob').next('.ui-autocomplete-input').prop('disabled',true);
			pruebasOK=true;
			$('#dummyOB').change();
		}else{
			estadoGuardar();
			$('#txtEquipo').addClass('ob').next('.ui-autocomplete-input').prop('disabled',false);
		}
		habilitarFin();
	});

});