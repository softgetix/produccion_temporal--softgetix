function validaLogin(){
	var oCheck;
	var sErrors = "";
	oCheck = new check('frmLogin');
	oCheck.checkString('txtUsuario', 'nombre de usuario', 3, 50, false);
	oCheck.checkString('txtPassword', 'password',3, 15, false);
	sErrors = oCheck.toString();
	if (sErrors != "") {
		alert(sErrors);
	}
	else {
		document.frmLogin.submit();
	}
}

function despintarCampos(){
	$('#txtUsuario').removeClass('campoFaltante');
	$('#txtPassword').removeClass('campoFaltante');
}

function capLock(e){
  kc=e.keyCode?e.keyCode:e.which;
  sk=e.shiftKey?e.shiftKey:((kc==16)?true:false);
  
  if(((kc>=65&&kc<=90)&&!sk)||((kc>=97&&kc<=122)&&sk)){
    //$('#'+e.target.id).addClass('required_invalid');
	$('#caplock').css('visibility','visible');
  }
  else{
	//$('#'+e.target.id).removeClass('required_invalid');
	$('#caplock').css('visibility','hidden');
  }
}

function subm(){
	var res = screen.width;	
	$('#hidResolucion').val(res);	
	if($('#txtUsuario').val() && $('#txtPassword').val()){
	document.getElementById("login_form").submit();
	}
	else{				
		if(!$('#txtUsuario').val() ){$('#txtUsuario').addClass('campoFaltante');}
		if(!$('#txtPassword').val()){$('#txtPassword').addClass('campoFaltante');}		
	}	
}

function inicializar(){
	document.getElementById("txtUsuario").focus();
}

function setResetPassword(cliente) {
	$('#error-mail').empty();
	$('#error-mail').hide();
	
	var errores = [];
	var mail = $("#txtMail").val();
	
	if (mail == "") {
		errores.push(arrLang['ingrese_mail']);
	}
	
	if (errores.length > 0) {
		$('#error-mail').show();
		$('#error-mail').html(errores.join("\r\n"));
	}
	else {
		$.ajax({
			url: dominio+'ajaxOlvidoContrasena.php',
			dataType: "json",
			type: "POST",
			async: false,
			cache: false,
			data: {
				"action": 'enviar_mail',
				"mail": mail,
				'config':cliente
			},
			success: function(msg){
				if(msg.ok){
					$('#datos-mail').hide();
					$('#respuesta-ok').show();
					//$.colorbox.close();
				}
				else{
					$('#error-mail').show();
					$('#error-mail').html(msg.error);
				}
				
			}
		});
	}
}