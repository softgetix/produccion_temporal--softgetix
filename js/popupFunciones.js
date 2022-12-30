$(document).ready(function() {
	
	$('#botonVolver').attr('onclick','').click(function(){
		if(typeof(recargar) != 'undefined'){
			if (recargar == true) {
				window.parent.location.reload();																		
			}
		}					
		window.parent.cerrarPopup();
	});
	
	$('#botonGuardar').attr('onclick','').click(function(){
		$('#hidOperacion').val('guardarA');
		var frm=$('#frm_'+ $('#hidSeccion').val()), 
		data=frm.find('select,input,textarea,hidden').not('input.showTip').serialize();
		
		$('input.showTip').each(function(){
			data+='&'+this.name+'=';
		});
		
		if ($('#hidSeccion').val() == 'abmGrupoMoviles') {
			if ($('#hidId').val() > 0) {
				$('#hidOperacion').val('guardarAsignacion');
			} else {
				$('#hidOperacion').val('guardarAltaAsignacion');
			}
			
			var movilesAsignados = document.getElementById("cmbMovilesAsignados");
			var hidMovilesSerializados = document.getElementById("hidMovilesSerializados");
			var moviles=""; var max=movilesAsignados.options.length;
			for(i=0;i < max;i++){
				if(moviles===""){
					moviles = movilesAsignados.options[i].value;
				}else{
					moviles += "," + movilesAsignados.options[i].value;
				}
			}
			
			hidMovilesSerializados.value = moviles;
			data=frm.find('select,input,textarea,hidden').serialize();
			$('input.showTip').each(function(){
				data+='&'+this.name+'=';
			});
			
			data += '&txtGrupo=' + $('#txtGrupo').val()
		}  
		
		$('*').css('cursor','wait');

		$.post(
			frm.attr('action')+'&method=ajax',
			data,
			function(d){
				$('*').css('cursor','default');
				if (d.ok){
						if(d.cerrar == 'ok'){
							window.parent.cerrarPopup();	
						}
						else{
							$.blockUI({
								timeout: 2000, 
								message: arrLang['msg_datos_guadados'],
								onUnblock: function(){ 
									window.parent.cerrarPopup();
									if ( typeof window.parent.retornoPopup != "undefined" )
									{
										window.parent.retornoPopup(d);
									}
									
									if(typeof(recargar) != 'undefined'){
										if (recargar == true) {
											window.parent.location.reload();																		
										}
									}
									
									if(typeof(actualizaPanicoADT) != 'undefined'){
										if (actualizaPanicoADT == true) {
											window.parent.location.href = 'boot.php?c=abmClientes&hidFiltro='+parent.$('#txtFiltro').val();
										}
									}
									
									
								} 
							});
						}
				}else{
					$('#mensajeError').remove();
					if(typeof(arrLang['info_error']) == 'undefined'){
						$('#mainBoxAM').prepend('<div id="mensajeError"><br/>'+d.mensaje+'<br/></div>');
					}
					else{
						$('#mainBoxAM').prepend('<div id="mensajeError"><span><b>'+arrLang['info_error']+'</b></span><br/><br/>'+d.mensaje+'<br/></div>');
					}
				}
			},
			'json'
		);
	});	
});

