function getLogAltaMobile(){
	var email = $('#email').val();
	
	if(email == ''){
		alert('Ingrese el e-mail');
	}
	else{
		$('#ResultadoLog').html('<img src="imagenes/ajax-loader.gif" >');
		
						
		$.ajax({
			async:false,
			cache:false,
			type: "POST",
			url: "controladores/abmLogAltaMobileControlador.php",
			data:({
				accion:'getInfoAltaMobile',
				email:email
			}),
			success: function(msg){
				var arr = jQuery.parseJSON(msg);
				$('#ResultadoLog').html(arr.resp);
			},	
			beforeSend:function(){},
			error:function(objXMLHttpRequest){}	
		});
	}
}

function buscarLog(e){
	tecla = (document.all) ? e.keyCode :e.which;
  	if(tecla == 13){
		getLogAltaMobile();
	}
	return (tecla!=13); 
}