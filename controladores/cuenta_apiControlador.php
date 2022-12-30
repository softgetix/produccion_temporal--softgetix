<?php
function listado($objSQLServer, $seccion, $mensaje = ""){
	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
	
	global $arrEntidades;
	$arrEntidades = $objCliente->obtenerCredenciales($_SESSION['idAgente']);
	/**/
	$objSQLServer_Testing = new SqlServer();
	$objSQLServer_Testing->dirConfig = 'webservices_testing';
	$objSQLServer_Testing->dbConnect();
	$objCliente_Testing = new Cliente($objSQLServer_Testing);
	global $arrEntidades_Testing;
	$arrEntidades_Testing = $objCliente_Testing->obtenerCredenciales($_SESSION['idAgente']);
	$objSQLServer_Testing->dbDisconnect();
	/**/
	//require("includes/template.php");
	
	global $doc;
	$doc = array();
	if(tieneperfil(array(27,28))){
		array_push($doc, array(
			'titulo'=>'Webservice de entregas'
			, 'url'=> null
			,'info'=>'<p>Pallet Swap puede ser integrado al ERP de su empresa a través de nuestros webservices.</p>
					<p>Genere las credenciales de acceso y obtenga el detalle del protocolo haciendo <a href="templates/api_modulo_logistico.pdf" target="_blank">click agu&iacute;</a></p>' 
		));	
	}
	else{	
		array_push($doc, array(
			'titulo'=>'Protocolo de Recepci&oacute;n Vehicular'
			, 'url'=>'protocolo_mercosur_localizart_2016.pdf'
			,'info'=>'<p>El protocolo de recepci&oacute;n vehicular tiene por finalidad recibir informaci&oacute;n de posicionamiento de los m&oacute;viles afectados a una operaci&oacute;n espec&iacute;fica monitoreados a trav&eacute;s de equipos GPS ya instalados.</p>
					<p>La comunicaci&oacute;n es del tipo PUSH desde el proveedor de equipos GPS hacia Localizar-T.</p>
					<p>La recepci&oacute;n es en forma autom&aacute;tica con periodicidad de 3 minutos.</p>'
			));
		array_push($doc, array(
			'titulo'=>'Protocolo de Solicitud de Posicionamiento Vehicular'
			, 'url'=>'api_solicitud_de_posicionamiento.pdf'
			,'info'=>'<p>El protocolo de solicitud de posicionamiento vehicular tiene por finalidad integrar la informaci&oacute;n GPS recibida y procesada en Localizar-T con su sistema de gesti&oacute;n actual.</p>
					<p>La comunicaci&oacute;n es del tipo PULL desde el proveedor de sistema de gesti&oacute;n que desea integrarse con Localizar-T.</p>
					<p>La toma de datos es en forma autom&aacute;tica con periodicidad de 3 minutos.</p>'
			));
		array_push($doc, array(
			'titulo'=>'Modulo Log&iacute;stico'
			, 'url'=>'api_modulo_logistico.pdf'
			,'info'=>'<p>El modulo log&iacute;stico permite la creaci&oacute;n y actualizaci&oacute;n de tareas en la Agenda GPS del sistema Localizar-T en forma autom&aacute;tica.</p>
					<p>Este m&oacute;dulo permite la comunicaci&oacute;n entre el ERP de la compañ&iacute;a y Localizar-T.</p>
					<p>La comunicaci&oacute;n es del tipo PUSH desde el ERP hacia Localizar-T.</p>'
		));
	}
	
}

function apiGenerarApi($objSQLServer, $seccion, $server){
	$idCliente = $_SESSION['idAgente'];
	require_once 'clases/clsApi.php';
	
	if($server == 'testing'){
		$objApi = new Api($objSQLServer,$idCliente,$server);
		$arrCliente = $objApi->getDatosCliente();
		
		$objSQLServer_Testing = new SqlServer();
		$objSQLServer_Testing->dirConfig = 'webservices_testing';
		$objSQLServer_Testing->dbConnect();
		$objConexion = $objSQLServer_Testing;
		$server = 'testing';
	}
	else{
		$objConexion = $objSQLServer;
		$server = 'produccion';
	}
	
	$objApi = new Api($objConexion,$idCliente,$server);
	
	if($server == 'testing'){//-- Si es testing y no tiene creado el cliente
		require_once 'clases/clsClientes.php';
		$objCliente = new Cliente($objConexion);	
		$credenciales = $objCliente->obtenerCredenciales($idCliente);
		if(!$credenciales['cl_id']){ //-- Existe el cliente en testing
			$aux['clientID'] = NULL;
			$aux['clientSecret'] = NULL;
			if(!$objApi->createClientTesting($arrCliente, $aux)){
				$mensaje = 'No se pudo generar las credenciales.';	
			}
		}
	}
	
	$aux = $objApi->generarCredenciales();	
	$credenciales['cl_clientID'] = $aux['clientID'];
	$credenciales['cl_clientSecret'] = $aux['clientSecret'];
	if($objApi->setCredenciales($credenciales)){
		$mensaje = 'Las credenciales se han generdo con &eacute;xito!';	
	}
	else{
		$mensaje = 'Las credenciales no han podido ser generadas, vuelva a intentarlo.';
	}
	
	if($server == 'testing'){
		$objSQLServer_Testing->dbDisconnect();	
		$objSQLServer->dbConnect();
	}
	index($objSQLServer, $seccion, $mensaje);
}

function solapaGuardarM($objSQLServer, $seccion){
	global $lang;
	$mensaje = NULL;
	$email = $_POST['email_send'];
	
	if(empty($email)){
		$mensaje = str_replace('[NOMBRE_CAMPO]',$lang->system->email,$lang->message->interfaz_generica->msj_completar);	
	}
	elseif(!validarEmail($email)){
		$mensaje = $lang->message->email_incorrecto;
	}
	
	if(!isset($_POST['adjunto'])){
		$mensaje.= "<br>".str_replace('[NOMBRE_CAMPO]','Documentaci&oacute;n a Enviar',$lang->message->interfaz_generica->msj_select_option);	
	}
	
	if(empty($mensaje)){
		$idCliente = $_SESSION['idAgente'];
		
		require_once 'clases/clsClientes.php';
		require_once 'clases/clsApi.php';
		
		$objApi = new Api($objSQLServer,$idCliente,'produccion');
		$arrCliente = $objApi->getDatosCliente();
		
		//-- Conexion Testing
		$objSQLServer_Testing = new SqlServer();
		$objSQLServer_Testing->dirConfig = 'webservices_testing';
		$objSQLServer_Testing->dbConnect();
		//-- 
		
		$objCliente_Testing = new Cliente($objSQLServer_Testing);
		$objApi_Testing = new Api($objSQLServer_Testing,$idCliente,'testing');
		
		$credenciales['testing'] = $objCliente_Testing->obtenerCredenciales($idCliente);
		if($credenciales['testing']['cl_id']){ //-- Existe el cliente en testing
			if(empty($credenciales['testing']['cl_clientID']) || empty($credenciales['testing']['cl_clientSecret'])){
				$aux = $objApi_Testing->generarCredenciales();	
				$credenciales['testing']['cl_clientID'] = $aux['clientID'];
				$credenciales['testing']['cl_clientSecret'] = $aux['clientSecret'];
				unset($aux);
				$objApi_Testing->setCredenciales($credenciales['testing']);
			}	
		}
		else{//-- No existe cliente en testing, por lo que se procede a crearlo.
			$aux = $objApi_Testing->generarCredenciales();	
			$credenciales['testing']['cl_clientID'] = $aux['clientID'];
			$credenciales['testing']['cl_clientSecret'] = $aux['clientSecret'];
			
			if(!$objApi_Testing->createClientTesting($arrCliente, $aux)){
				$mensaje = 'No se pudo generar las credenciales.';	
			}
		}
		
		//-- Se crea perfil satelital en caso de que no exista --//
		$idSatelital = $objApi_Testing->createUserPerfilSatelital($email);
		if(!$idSatelital){
			$mensaje = 'La dirección de correo indicada ya se encuentra en uso para otro perfil.';
		}
		//-- --//
				
		
		$objSQLServer_Testing->dbDisconnect();
		$objSQLServer->dbConnect();
		
		if(empty($mensaje)){
			$aux = explode('@',$email);
			$arrSend = array('idUsuario'=>$idSatelital, 'nombre'=>$aux[0], 'email'=>$email, 'fileAdjunto'=>$_POST['adjunto'], 'clientID'=>$credenciales['testing']['cl_clientID'], 'clientSecret'=>$credenciales['testing']['cl_clientSecret']);
			if($objApi->generarEnvio($arrSend)){
				$mensaje = $lang->message->ok->email_enviado->__toString();
			}
			else{
				$mensaje = $lang->message->error->email_enviado->__toString();	
			}
			
			$objCliente = new Cliente($objSQLServer);
			$credenciales['produccion'] = $objCliente->obtenerCredenciales($idCliente);	
			if(empty($credenciales['produccion']['cl_clientID']) || empty($credenciales['produccion']['cl_clientSecret'])){
				$objApi = new Api($objSQLServer,$idCliente,'produccion');
				$aux = $objApi->generarCredenciales();	
				$credenciales['produccion']['cl_clientID'] = $aux['clientID'];
				$credenciales['produccion']['cl_clientSecret'] = $aux['clientSecret'];
				unset($aux);
				$objApi->setCredenciales($credenciales['produccion']);
			}	
		}	
	}
	
	index($objSQLServer, $seccion, $mensaje);
}



?>