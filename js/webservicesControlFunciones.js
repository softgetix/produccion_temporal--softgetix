$(document).ready(function(e) {
	$('#txtIp').keypress(function(e){
		var key = window.Event ? e.which : e.keyCode
		if((key >= 48 && key <= 57) || key == 46 || key == 0 || key == 8){
			return true;
		}
		return false;
	});
});

function iniciarCheckin($ip){
	var auxValid = false;	
	if($ip != '' && $ip.length >= 6){
		var aux = $ip.split('.');
		if(aux.length == 4){
			auxValid = true;
			$('#messageDefaultLocalizart').remove();
			
			changeRefresh($ip)
			
			$.ajax({
				async:false,
				cache:false,
				type: "POST",
				url: "controladores/webservicesControlControlador.php",
				data:({
					accion:'saveIp',
					ip:$ip
				}),
				success: function(msg){},
				beforeSend:function(){},
				error:function(objXMLHttpRequest){}	
			});
		}
	}
	
	if(auxValid == false){
		var auxMessage = '<div id="messageDefaultLocalizart">'
		auxMessage+= '<a href="javascript:cerrarMensaje();"><img id="imgCerrarMensaje" src="imagenes/cerrar.png" /></a>';
		auxMessage+= '<span style="color:#000000;"><br/>Ingrese una dirección de IP válida<br/></span><br/>';
		auxMessage+= '</div>';
		$('#content').after(auxMessage);
	}
}
	
var salto_linea = '<p>--------------------------</p>';		
function leerLog($ip){
	$('#message_log').prepend('<p id="img-rueda"><img src="imagenes/ajax-loader.gif" ></p>');
	
	$.ajax({
		async:true,
		cache:false,
		type: "POST",
		url: "controladores/webservicesControlControlador.php",
		data:({
			accion:'getInfoLog',
			ip:$ip
		}),
		success: function(msg){
			$('#img-rueda').each(function(){
				$(this).remove();
			});

			var arr = jQuery.parseJSON(msg);
			if(typeof(arr.IP) != 'undefined'){
				if(arr.IP == true){
					$('.steps').children('div.step:nth-child(1)').addClass('active_step');
						
					if(arr.authentication == true){
						$('.steps').children('div.step:nth-child(2)').addClass('active_step');	
					}
					else{
						$('.steps').children('div.step:nth-child(2)').removeClass('active_step');	
						$('.steps').children('div.step:nth-child(3)').removeClass('active_step');		
					}
					
					if(typeof(arr.msg) == 'object'){
						if(arr.authentication == true){
							$('.steps').children('div.step:nth-child(3)').addClass('active_step');
						}
						
						for (var data in arr.msg){
							var auxTxt = '';
							for (var msg in arr.msg[data]){	
								auxTxt+= '<p>'+arr.msg[data][msg]+'</p>';
							}
							$('#message_log').prepend(auxTxt+salto_linea);
						}
					}
					else if(typeof(arr.msg) != 'undefined'){
						$('#message_log').prepend('<p>'+arr.msg+'</p>'+salto_linea);
					}
				}
				else{
					$('.steps').children('div.step').removeClass('active_step');
				}
			}
			else if(typeof(arr.msg) != 'undefined'){
				$('#message_log').prepend('<p>'+arr.msg+'</p>'+salto_linea);
			}
		},	
		beforeSend:function(){},
		error:function(objXMLHttpRequest){}	
	});
}

var timer;
var auxIp = null;
var auxSeparador = '';
function changeRefresh(ip){
	if(ip){
		if(auxIp == null){
			$('#buttonAction').attr('onclick','javascript:changeRefresh(null)');
			$('#buttonAction').html('DETENER');
			$('#buttonAction').removeClass('colorin').addClass('colorRed');
			$('#message_log').prepend('<p>Iniciando Búsqueda.</p>'+auxSeparador);
			//leerLog(auxIp);
		}
		auxIp = ip;
		//timer = setTimeout("leerLog(auxIp)",5000);
		timer = setInterval("leerLog(auxIp)",5000);
	}
	else{
		clearTimeout(timer);
		auxIp = null;	
		$('#buttonAction').attr('onclick',"javascript:iniciarCheckin($('#txtIp').val())");
		$('#buttonAction').html('Verificar Conexion');
		$('#buttonAction').removeClass('colorRed').addClass('colorin');
		$('#message_log').prepend('<p>Búsqueda Detenida.</p>'+salto_linea);
		auxSeparador = salto_linea;
	}	
}