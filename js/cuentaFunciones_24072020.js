$(document).ready(function(){
	if($('#solapa').val() == 'cambiar-password'){
		$("#frm_cuenta").validate();
		$("#txtPassNuevo").valid();
	}
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