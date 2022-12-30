$(document).ready(function(){
	if($('#solapa').val() == 'cambiar-password'){
		$("#frm_cuenta").validate();
		$("#txtPassNuevo").valid();
	}

	//--Envio de sms en moviles			
	$('.pv_send_message').live("click", function(){
		var idmovil = $(this).attr('attrIdMovil');
		mostrarPopup('boot.php?c=cuenta&solapa=moviles&action=sendmessage&idmovil='+idmovil,740,350);
	});

	$('.pv_view_history').live("click", function(){
		var idmovil = $(this).attr('attrIdMovil');
		mostrarPopup('boot.php?c=cuenta&solapa=moviles&action=viewhistory&idmovil='+idmovil,740,350);
	});

	$('.pv_send_sms').live("click", function(){
        $("#hidMessage").val($(this).attr('attrmessage'));
        $("#hidNumber").val($(this).attr('attrnumber'));
        $("#hidTitle").val($(this).attr('attrtitle'));
		$("#hidPath").val($(this).attr('attrurl'));
		$("#hidMessagetype").val($(this).attr('attrtype'));
        $("#hidIdViaje").val($(this).attr('attrIdMovil'));
        $("#hidAdicional").val($("#txtadicional_"+$(this).attr('attrkey')).val());
    });
	//--
});

function verificarDatos(){						
	/*var passActual = document.getElementById("txtPassActual");
	var passNuevo = document.getElementById("txtPassNuevo");
	var passNuevo2 = document.getElementById("txtPassNuevo2");
	var oCheck;
	var sErrors = "";
	oCheck = new check('frm_cuenta');
	oCheck.checkString('txtPassActual', arrLang['password_actual'], 7, 16, false);
	oCheck.checkString('txtPassNuevo', arrLang['contrasena_nueva'],9, 16, false);
	oCheck.checkString('txtPassNuevo2', arrLang['contrasena_nueva_repetir'],10, 16, false);
	sErrors = oCheck.toString();
	if (sErrors != "") {
		alert(sErrors);
	}	
	else{*/
		if($('div.password-meter-message').hasClass('password-meter-message-strong')){
			enviar('cambiarPassword');
		}
		else{
			alert(arrLang['msg_cambio_password']);
		}
	//}
} 

function auto_ingresar(usr,pass){
	 var form,input,
    form = document.createElement('form');
    form.method = 'post';
    var arrpath = (document.location.pathname).split('/');
	form.action = '/'+arrpath[1]+'/ingresar_como.php';

    input = document.createElement('input');
    input.setAttribute('name', 'ingresar_como');
    input.setAttribute('value', usr);
    input.setAttribute('type', 'hidden');
    form.appendChild(input);

    input = document.createElement('input');
    input.setAttribute('name', 'hidPassDirect');
    input.setAttribute('value', pass);
    input.setAttribute('type', 'hidden');
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

function changeDistance($this, $idmovil){

	$id = 'a#'+$this;
	if($($id).hasClass('dist1m')){
		var $addClass = 'dist25m';
		var $removeClass = 'dist1m';
		var $value = 80;
	}
	else{
		var $addClass = 'dist1m';
		var $removeClass = 'dist25m';
		var $value = 60;
	}
	
	$.ajax({
		type: "POST",
		url: 'ajax.php',
		data:({
			accion:'change-distance',
			id_movil:$idmovil,
			value:$value
		}),
		success: function(msg){
			if(msg == true || msg == 'true'){
				$($id).addClass($addClass);
				$($id).removeClass($removeClass);
				$('#'+ide).val($value);
			}
		}	
	});		
}
