$(document).ready(function(e) {
    $('a.a-click-example').click(function(c){
		$(this).parent().parent().parent('fieldset.box-container-example').children('textarea').val($(this).text());
	});
});

function message_example($value){
	
	var resp = true;
	if($('textarea.ws-message').val()){
		resp = confirm('Desea remplazar la estructura de datos actual por un nuevo ejemplo?');
	}
	
	if(resp == true){
		if($value == 'SOAP'){
			$('textarea.ws-message').val('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://tempuri.org/"><soapenv:Header/><soapenv:Body><ws:Method_Define><ws:userLogin>xxxx</ws:userLogin><ws:userPassword>xxxx</ws:userPassword></ws:Method_Define></soapenv:Body></soapenv:Envelope>');
		}
		else if($value == 'JSON'){
			$('textarea.ws-message').val('variable1:nombre_variable1, variable2:nombre_variable2');	
		}
	}
}

function onOffOctopus($this, id){
	$.ajax({
		type:"POST"
		,url:"ajax.php"
		,dataType:"json"
		,data:({accion:"set-cambiar-estado-octopus",id:id})
		,success:function(result){
			if(result == 1){
				if($('a#'+$this+' img').attr('src').indexOf('ok')> 0){
					$('a#'+$this+' img').attr('src',$('a#'+$this+' img').attr('src').replace('ok','cerrar'));
				}
				else{
					$('a#'+$this+' img').attr('src',$('a#'+$this+' img').attr('src').replace('cerrar','ok'));	
				}
			}
		}
	});
}