$(document).ready(function(){
	var ov=$('<div id="popupOverlay" name="popupOverlay" style="position:fixed" />').prependTo('body');
	var ph=$('<div id="popupHolder" name="popupHolder" />').insertAfter(ov);
	ph.append('<iframe id="popupFrm" name="popupFrm" frameborder="0"></iframe>');
});

function mostrarPopup(url,width, height){
	if(typeof(width) == 'undefined'){
		 var width = $(window).width() - 250;
	}	
	if(typeof(height) == 'undefined'){
		 var height = $(window).height() - 150;
	}	

	var posicion_x; 
	var posicion_y; 
	//posicion_x=(screen.width/2)-(width/2); 
	//posicion_y=(screen.height/2)-(height/2);
	posicion_x=($(window).width()/2)-(width/2); 
	posicion_y=($(window).height()/2)-(height/2) + 250;
	
	$('#popupFrm,#popupHolder').css({'width':String(width)+'px','left':posicion_x+"px",'top':posicion_y+"px",'height':String(height)+'px','overflow':'hidden'});
	document.getElementById('popupFrm').src = dominio+url;
	$('#popupOverlay,#popupHolder').show();
}

function mostrarPopupRastreo(url,width, height){
	
	if(typeof(width) == 'undefined'){width = 1000;}	
	if(typeof(height) == 'undefined'){height = 500;}	
	
	var posicion_x; 
	var posicion_y; 
	posicion_x=(screen.width/2)-(width/2); 
	posicion_y=(screen.height/2)-(height/2);
	$('#popupFrm,#popupHolder').css({'width':String(width)+'px','left':posicion_x+"px",'height':String(height)+'px','overflow':'hidden'});
	$('#popupFrm').css({'overflow-y':'auto'});
	document.getElementById('popupFrm').src = dominio+url;
	$('#popupOverlay,#popupHolder').show();
	$('#popupOverlay').click(function() {
        cerrarPopup();
	})
}

function mostrarPopupTelemetria(url){
	var posicion_x; 
	var posicion_y; 
	posicion_x=(screen.width/2)-(1330/2); 
	posicion_y=(screen.height/2)-(500/2);
	$('#popupFrm,#popupHolder').css({'width':'1330px','left':posicion_x+"px",'height':'300px','overflow':'hidden'});
	//$('#popupFrm').attr('src',url);
	document.getElementById('popupFrm').src = dominio+url;
	$('#popupOverlay,#popupHolder').show();
	$('#popupOverlay').click(function() {
		cerrarPopup();
	})
}

function cerrarPopup(){
	$('#popupOverlay, #popupHolder').hide();
	$('#popupFrm').attr('src','');
}

var $aux_bandera = false;
function mostrarPopupUrlExterna(url,width, height){
	if(typeof(width) == 'undefined'){
		 var width = $(window).width() - 250;
	}	
	if(typeof(height) == 'undefined'){
		 var height = $(window).height() - 150;
	}	

	var posicion_x; 
	var posicion_y; 
	posicion_x=($(window).width()/2)-(width/2); 
	posicion_y=($(window).height()/2)-(height/2) + 250;
	
	$('#popupFrm,#popupHolder').css({'width':String(width)+'px','left':posicion_x+"px",'top':posicion_y+"px",'height':String(height)+'px','overflow':'hidden'});
	
	if(!$aux_bandera){
		$aux_bandera = true;
		$('<div id="botonesABM" style="padding-bottom:10px"><a id="botonVolver" href="javascript:cerrarPopup();" onclick=""><strong>Cerrar</strong></div>').insertBefore('#popupFrm');
	}

	document.getElementById('popupFrm').src = url;
	$('#popupOverlay,#popupHolder').show();
}

//function retornoPopup(data){
	//dummy, override
//}
