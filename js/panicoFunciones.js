var urlAjax = 'ajaxProbadorDePanico.php';

if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, ''); 
  }
}

function panicoElegirCliente(elTexto) {
	var texto = elTexto;
	var partes = [];
	if (texto.length > 0) {
		var posicion = texto.lastIndexOf("(");
		
		if (posicion > 0) {
			partes["mail"] = texto.substr(posicion + 1).replace(")", "").trim();
			texto = texto.substr(0, posicion).trim();
			posicion = texto.indexOf(")");
			if (posicion > 0) {
				partes["codigo"] = texto.substr(0, posicion).replace("(", "").trim();
				partes["nombre"] = texto.substr(posicion).replace(")", "").trim();
			}
		}
	}
	
	return partes;
}

function panicoElegirClienteCompletar(elTexto) {
	var resultado = panicoElegirCliente(elTexto);
	if (resultado["codigo"] != undefined) {
		idCliente = resultado["codigo"];
		if (resultado["nombre"] != undefined) document.getElementById("num_cliente").innerHTML = resultado["nombre"];
		if (resultado["mail"] != undefined) document.getElementById("mail_cuenta").innerHTML = resultado["mail"];
		document.getElementById("div_panico_global_cliente_mail").style.visibility = "visible";

		//-- Obtiene los móviles y las referencias:--//
		$.ajax({
			url: urlAjax,
			async: true,
			cache: false,
			data: {
				action: 'moviles_referencias',
				cod_cliente: idCliente
			},
			dataType: "json",
			success: function (datos){
				var moviles = datos.moviles;
				var referencias = datos.referencias;
				
				var cantMoviles = moviles.length;
				for(i=0; i<cantMoviles; i++){
					nombresMoviles[moviles[i].mo_id] = moviles[i].mo_matricula+' ['+moviles[i].mo_identificador+' '+moviles[i].mo_otros+' '+moviles[i].mo_marca+']';
				}
				
				var cantReferencias = referencias.length;
				for(i=0; i<cantReferencias; i++){
					nombresZonas[referencias[i].re_id] = referencias[i].re_ubicacion;
				}
			},
			type: "POST"
		});
		//-- --//
		
		$.ajax({
			url: urlAjax,
			async: true,
			cache: false,
			data: {
				action: 'definir_contenido',
				cod_cliente: idCliente
			},
			success: function (datos, estado, respuesta) {
				var salida = datos;
				document.getElementById("tabla_moviles").innerHTML = salida;
				cronoUltimasPruebasPanico = setInterval("revisarUltimasPruebasPanico();", 2000);
			},
			type: "POST"
		});
	}
	/**/
}

var status_disponibilidad = [];
function changeReferencia(ide, movil, referencia){
	$('#combo_'+ide).attr('class','float_l combo_'+movil+'_'+referencia);
	
	$('.status_prueba_'+ide).attr('id','zona_'+movil+'_'+referencia);
	$('.status_prueba_'+ide).html(arrLang ['sin_probar']);
	
	$('.result_prueba_'+ide).attr('id','resultado-'+movil+'_'+referencia);
	$('.result_prueba_'+ide).removeClass('prueba-exitosa');
	
	$('.boton_prueba_'+ide).html('');
	
	if(status_disponibilidad[movil]['resp']){
		$('.boton_prueba_'+ide).attr('id','prueba_'+movil+'_'+referencia);
		agregarBotonPrueba(movil, referencia);	
	}
	else{
		$('.status_prueba_'+ide).html('<span style="color:RED">'+status_disponibilidad[movil]['msg']+'</span>');
	}
}

function resetPrueba(ide, movil){
	var idReferencia = $('#combo_'+ide).val();
	var resp = confirm(arrLang['msg_desvincular']);
	if(resp){
		$('#combo_'+ide).removeAttr('disabled');
		$('#combo_'+ide+' option').eq(0).attr('selected','selected');
		$('#href_'+ide).remove();
	
		$.ajax({
			url: urlAjax,
			async: false,
			cache: false,
			data: {
				action: 'reset_estado_prueba',
				idMovil: movil,
				idReferencia: idReferencia
			},
			dataType: "json",
			success: function (datos) {
				if(!datos){
					$('.status_prueba_'+ide).html('<span style="color:RED">## Error ##</span>');
				}
				else{
					changeReferencia(ide, movil, '');
				}
				
			},
			type: "POST"
		});
	}
	/**/
}

function agregarBotonPrueba(movil, referencia){
	var contElse = false;
	if(typeof(status_disponibilidad[movil]) != 'undefined'){
		if(referencia > 0 && status_disponibilidad[movil]['resp']){
			$('#prueba_'+movil+'_'+referencia).html('<input class="panico_global_tabla_detalle" type="button" value="P" onclick="probarPanico('+ movil+','+referencia+');" />');
		}
		else{contElse = true;}
	}
	else{contElse = true;}
		
	if(contElse == true){
		$('#prueba_'+movil+'_'+referencia).html('<input class="panico_global_tabla_detalle sin_disponibilidad" type="button" value="P" onclick="javascript:;" title="'+arrLang['msg_sin_disponibilidad'] +'" />');	
	}
}

function revisarUltimasPruebasPanico() { 
	$.ajax({
		url: urlAjax,
		async: true,
		cache: false,
		data: {
			action: 'ultimas_pruebas',
			cod_cliente: idCliente
		},
		dataType: "json",
		success: function (datos) {
			var respuestas = datos.ultimas_pruebas;
			var cantPruebas = respuestas.length;
			
			var disponibilidad = datos.disponibilidad_pruebas;
			var cant_moviles = disponibilidad.length;
			for(i=0; i<cantPruebas; i++){
				var movil = respuestas[i].hp_mo_id;
				var referencia = respuestas[i].hp_re_id;
				$('#zona_'+ movil+'_'+referencia).html(respuestas[i].fecha_hora+' hs');
				$('#resultado-'+movil+'_'+referencia).addClass('prueba-exitosa');
				
				var aux1 = $('#zona_'+movil+'_'+referencia).attr('class').split(' ');
				var aux2 = aux1[0].split('_');
				var fila = aux2[2];
				
				$('#combo_'+fila).attr('disabled','disabled');
				if(!$('#col2_'+fila).find('#href_'+fila).length){
					$('#col2_'+fila).append('&nbsp;&nbsp;<a href="javascript:resetPrueba('+fila+','+movil+')" id="href_'+fila+'" class="href_'+movil+'_'+referencia+'" >'+arrLang['desvincular']+'</a>');
				}
			
				/*** **/
				for(x=0; x<cant_moviles; x++){
					if(disponibilidad[x].mo_id == movil && disponibilidad[x].resp == true){
						agregarBotonPrueba(movil, referencia);
					}	
				}
				/*** ***/
			}
			
			for(x=0; x< cant_moviles; x++){
				status_disponibilidad[disponibilidad[x].mo_id] = Array();
				status_disponibilidad[disponibilidad[x].mo_id]['resp'] = disponibilidad[x].resp;
				status_disponibilidad[disponibilidad[x].mo_id]['msg'] = disponibilidad[x].msg;
			}	
		},
		type: "POST"
	});
}

function probarPanico(movil, zona) {
	document.getElementById("inicio_prueba_nombre_movil").innerHTML = nombresMoviles[movil];
	document.getElementById("inicio_prueba_nombre_zona").innerHTML = nombresZonas[zona];
	movilEnPrueba = movil;
	zonaEnPrueba = zona;
	document.getElementById("consulta_inicio_prueba").click();
}

function iniciarPruebaPanico() {
	$('.combo_'+movilEnPrueba+'_'+zonaEnPrueba).removeAttr('disabled');
	$('.combo_'+movilEnPrueba+'_'+zonaEnPrueba).eq(0).attr('selected','selected');
	$('.href_'+movilEnPrueba+'_'+zonaEnPrueba).remove();
	$('#resultado-'+movilEnPrueba+'_'+zonaEnPrueba).removeClass('prueba-exitosa');
	$('#zona_'+movilEnPrueba+'_'+zonaEnPrueba).html(arrLang ['sin_probar']);	
		
	$.ajax({
		url: urlAjax+'?hey',
		async: false,
		cache: false,
		data: {
			action: 'insertar_prueba',
			cod_movil: movilEnPrueba,
			cod_zona: zonaEnPrueba
		},
		//dataType: "xml",
		success: function (datos, estado, respuesta) {
			codigoPruebaEnCurso = 0;
			try {
				//codigoPruebaEnCurso = datos.getElementsByTagName("resultado")[0].childNodes[0].nodeValue;
				codigoPruebaEnCurso = datos;
			}
			catch(ex){}
			
			if (codigoPruebaEnCurso > 0) {
				document.getElementById("probando_movil").innerHTML = nombresMoviles[movilEnPrueba];
				document.getElementById("probando_zona").innerHTML = nombresZonas[zonaEnPrueba];
				probando = true;
				document.getElementById("consulta_conteo_regresivo").click();
				cronoRegresivo = setInterval("revisarPruebaPanico();", 1000);//1000
				cronoRevisionPrueba = setInterval("revisarResultadoPanico();", 3000);//3000
			}
		},
		type: "POST"
	});
}

function revisarPruebaPanico() {
	segundosCuentaRegresiva--;
	document.getElementById('contador_segundos').innerHTML = segundosCuentaRegresiva;
	if (segundosCuentaRegresiva == 0) {
		probando = false;
		clearInterval(cronoRegresivo);
		clearInterval(cronoRevisionPrueba);
		document.getElementById("panico_no_recibido_movil").innerHTML = nombresMoviles[movilEnPrueba];
		document.getElementById("panico_no_recibido_zona").innerHTML = nombresZonas[zonaEnPrueba];
		document.getElementById("consulta_panico_no_recibido").click();
		
		$.ajax({
			url: urlAjax,
			async: true,
			cache: false,
			data: {
				action: 'anular_prueba',
				cod_prueba: codigoPruebaEnCurso
			},
			//dataType: "xml",
			type: "POST",
			success: function (datos, estado, respuesta){}
		});
	}
}

function revisarResultadoPanico() {
	$.ajax({
		url: urlAjax,
		async: true,
		cache: false,
		data: {
			action: 'revisar_prueba',
			cod_prueba: codigoPruebaEnCurso,
			cod_movil: movilEnPrueba,
			cod_zona: zonaEnPrueba
		},
		//dataType: "xml",
		success: function (datos, estado, respuesta) {
			//var resultado = datos.getElementsByTagName("resultado")[0].childNodes[0].nodeValue * 1;
			var resultado = datos * 1;
			switch (resultado) {
				case -1: //PANICO_ESTADO_SIN_RESULTADO
					//Sólo esperar...
					break;
				case 1: //PANICO_ESTADO_EXITOSO
					probando = false;
					clearInterval(cronoRegresivo);
					clearInterval(cronoRevisionPrueba);
					document.getElementById("exito_movil").innerHTML = nombresMoviles[movilEnPrueba];
					document.getElementById("exito_zona").innerHTML = nombresZonas[zonaEnPrueba];
					document.getElementById("consulta_prueba_exitosa").click();
					break;
				case 2: //PANICO_ESTADO_FUERA_DE_ZONA
					probando = false;
					clearInterval(cronoRegresivo);
					clearInterval(cronoRevisionPrueba);
					document.getElementById("fuera_zona_movil").innerHTML = nombresMoviles[movilEnPrueba];
					document.getElementById("fuera_zona_zona").innerHTML = nombresZonas[zonaEnPrueba];
					document.getElementById("consulta_fuera_de_zona").click();
					break;
				case 3: //PANICO_SIN_DISPONIBILIDAD_TECNICA
					probando = false;
					clearInterval(cronoRegresivo);
					clearInterval(cronoRevisionPrueba);
					document.getElementById("sin_disponibilidad_movil").innerHTML = nombresMoviles[movilEnPrueba];
					document.getElementById("sin_disponibilidad_zona").innerHTML = nombresZonas[zonaEnPrueba];
					document.getElementById("consulta_sin_disponibilidad").click();
					break;
			}
		},
		type: "POST"
	});
}
