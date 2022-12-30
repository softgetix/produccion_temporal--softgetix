function imprimir(){
	var filtro = document.getElementById('txtFiltro').value;
	var url = 'boot.php?c=abmClientes&method=export_prt&filtro='+filtro;
	window.open(url, '', 'width=800, height=500');
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

function SendMailResetPass(mail, cliente, ide){
	$('#'+ide).hide();
	$('<img src="imagenes/ajax-loader.gif" id="enviando-'+ide+'" />').insertAfter('a#'+ide);
	$.ajax({
		url: dominio+'ajaxOlvidoContrasena.php',
		dataType: "json",
		type: "POST",
		async: false,
		cache: false,
		data: {
			"action": 'enviar_mail',
			"mail": mail,
			'config':cliente,
			'ignorar_reset_previo':true
		},
		success: function(msg) {
			if(msg.ok){
				alert('Se ha enviado un correo a: '+mail+' con la informaci\u00f3n para el cambio de clave.');
			}
			else{
				alert(msg.error);
			}
			
			$('#enviando-'+ide).remove();
			$('#'+ide).show();
		}
	});
}
