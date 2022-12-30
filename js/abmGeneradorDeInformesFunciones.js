$(document).ready(function(){
	if($('#hidOperacion').val() == 'alta' || $('#hidOperacion').val() == 'modificar'){
		CKEDITOR.replace( 'txtMensaje',
		{
			toolbar : 'Colbasic',
			height: 200, width: 530
		});
		
		if($('#cmbAgente option:selected').val()){
			$('#cmbAgente').trigger('change');
		
			if($('#cmbCliente option:selected').val()){
				$('#cmbCliente').trigger('change');
			}
		}
	}
});


function getClientes(idAgente, idCliente){
	var ide = 'cmbCliente';
	$('#'+ide).find('option:not(:first)').remove();
	
	$.ajax({
		type:"POST"
		,url:"ajax.php"
		,dataType:"json"
		,data:({accion:"get-clientes-tipo-2",id_agente:idAgente})
		,success:function(c){
			if(c != null){
				for(i=0; i<c.length; i++){
					var selected = '';
					if(idCliente == c[i].cl_id){
						selected = 'selected = "selected"';	
					}
					$('#'+ide).append('<option value="'+c[i].cl_id+'" '+selected+'>'+c[i].cl_razonSocial+'</option>');
				}
			}
		}
	});
}

function getUsuarios(idCliente, noLimpiar, arrUsuarios){
	var ide = 'IDEnviarA'; 
	if(noLimpiar == true){}
	else{
		$('#'+ide).empty();
	}
	
	if(arrUsuarios){
		arrUsuarios = arrUsuarios.split(',');
		$.each(arrUsuarios, function(i, el) {
		  arrUsuarios[$.trim(el)] = $.trim(i);
		});
	}
	
	$.ajax({
		type:"POST"
		,url:"ajax.php"
		,dataType:"json"
		,data:({accion:"get-usuarios-por-cliente",idCliente:idCliente})
		,success:function(c){
			if(c != null){
				for(i=0; i<c.length; i++){
					var checked = '';
					if(typeof(arrUsuarios[$.trim(c[i].us_id)]) != 'undefined'){
						checked = 'checked="checked"';
					}
					
					var txt = (typeof(c[i].us_nombre) != 'undefined')?c[i].us_nombre:'';
					txt+= (typeof(c[i].us_apellido) != 'undefined')?(' '+c[i].us_apellido):'';
					txt+= ' ['+c[i].us_nombreUsuario+']';
					$('#'+ide).append('<input type="checkbox" name="checkEnviarA[]"  value="'+c[i].us_id+'" class="float_l" '+checked+'/><span class="float_l" style="line-height:20px;">'+txt+'</span><span class="clear"></span>');
				}
			}
		}
	});
}

function onOffInforme($this, idInforme){
	$.ajax({
		type:"POST"
		,url:"ajax.php"
		,dataType:"json"
		,data:({accion:"set-cambiar-estado-informe",idInforme:idInforme})
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

function duplicarInforme(idInforme){
	var resp = confirm('Esta seguro de duplicar el informe?');
	if(resp){
		enviar('duplicarInforme',idInforme);
	}
}

function probarEnvio(idEnvio){
	$.ajax({
		type:"POST"
		//,dataType:"json"
		
		,url:"ajax.php"
		,data:({accion:'test-informes',id_test_envio:idEnvio})
		
		//,url :"http://www.localizar-t.com:444/informes/generar_informes.php"
		//,data:({id_test_envio:idEnvio})
		,success:function(c){
			if(c == 'ok'){
				alert('La prueba de envío, se proceso con éxito!');
			}
			else{
				alert('La prueba fallo, el envío no pudo ser generado.');
			}
		}
	});	
}

function limpiarConsulta(){
	$('#txtConsulta').empty();
	$('#txtConsulta').focus();	
}