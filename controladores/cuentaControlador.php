<?php
$solapa = isset($_POST['solapa'])?$_POST['solapa']:(isset($_GET['solapa'])?$_GET['solapa']:NULL);
$solapa = $objPerfil->validarSeccion('cuenta_'.$solapa)?$solapa:'cambiar-password';

function index($objSQLServer, $seccion, $mensaje = "") {
	global $solapa;
	global $lang;
	
	switch($solapa){
		case 'cambiar-password':
			require_once 'clases/clsUsuarios.php';
			$objUsuario = new Usuario($objSQLServer);         
			       
			$arrUsuario = $objUsuario->obtenerUsuarios($_SESSION["idUsuario"]);
			
			if($_SESSION["ultimoAcceso"] == NULL){		
				$objUsuario->setearIngresoCuenta($_SESSION['idUsuario']);
			}
			
			$extraCSS[] = 'css/password/jquery.validate.password.css';
			$extraCSS[] = 'css/password.css';
			$extraJS[] = 'js/password/jquery.validate.js';
		break;
		case 'accesos_cuenta':
			set_time_limit(300);
			include 'clases/clsLog.php';
			$log = new Log($objSQLServer);
			
			$result = $log->getLog(100);
		break;
		case 'api':
			require_once 'controladores/cuenta_apiControlador.php';
			listado($objSQLServer, $seccion);
			global $arrEntidades;
			global $arrEntidades_Testing;
			global $doc;
		break;
		default:
			if(file_exists('controladores/cuenta_'.$solapa.'Controlador.php')){
				require_once 'controladores/cuenta_'.$solapa.'Controlador.php';
				listado($objSQLServer, $seccion);
				global $arrEntidades;
			}
			else{
				header('Location:boot.php?c=cuenta&solapa=cambiar-password');
				exit;
			}
		break;
	}


	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] ='js/popupHostFunciones.js';
	
	require("includes/template.php");
}

function alta($objSQLServer, $seccion = "", $mensaje = ""){
	global $solapa;
	
	if(file_exists('controladores/cuenta_'.$solapa.'Controlador.php')){
		include 'controladores/cuenta_'.$solapa.'Controlador.php';
		solapaAlta($objSQLServer, $seccion);
	}
	else{
		index($objSQLServer, $seccion);
	}
}

function modificar($objSQLServer, $seccion = "", $mensaje = ""){
	global $solapa;
	
	if(file_exists('controladores/cuenta_'.$solapa.'Controlador.php')){
		include 'controladores/cuenta_'.$solapa.'Controlador.php';
		solapaModificar($objSQLServer, $seccion);
	}
	else{
		index($objSQLServer, $seccion);
	}
}

function baja($objSQLServer, $seccion = "", $mensaje = ""){
	global $solapa;
	
	if(file_exists('controladores/cuenta_'.$solapa.'Controlador.php')){
		include 'controladores/cuenta_'.$solapa.'Controlador.php';
		solapaBaja($objSQLServer, $seccion);
	}
	else{
		index($objSQLServer, $seccion);
	}
}

function exportar_xls($objSQLServer, $seccion = ""){
	global $solapa;
	
	if(file_exists('controladores/cuenta_'.$solapa.'Controlador.php')){
		include 'controladores/cuenta_'.$solapa.'Controlador.php';
		solapaExportar_xls($objSQLServer, $seccion);
	}
	else{
		index($objSQLServer, $seccion);
	}
}

function guardarA($objSQLServer, $seccion) {
	global $solapa;
	
	switch($solapa){
		case 'api':
			include 'controladores/cuenta_apiControlador.php';
			apiGenerarApi($objSQLServer, $seccion, $_POST['server_credencial']);
		break;
		default:
			if(file_exists('controladores/cuenta_'.$solapa.'Controlador.php')){
				include 'controladores/cuenta_'.$solapa.'Controlador.php';
				solapaGuardarA($objSQLServer, $seccion);
			}
			else{
				index($objSQLServer, $seccion);
			}
		break;
	}
}

function guardarM($objSQLServer, $seccion) {
	global $solapa;
	
	if(file_exists('controladores/cuenta_'.$solapa.'Controlador.php')){
		include 'controladores/cuenta_'.$solapa.'Controlador.php';
		solapaGuardarM($objSQLServer, $seccion);
	}
	else{
		index($objSQLServer, $seccion);
	}
}

function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}

function cambiarPassword($objSQLServer, $seccion){
	global $lang;
	$passActual = trim($_POST['txtPassActual']);
	$passNuevo = trim($_POST['txtPassNuevo']);
	$passNuevo2 = trim($_POST['txtPassNuevo2']);
	
	$mensaje = '';
	if(!validarNuevaContrasenna($passNuevo)){ 
		$mensaje = $lang->message->password_longitud;
	}
	
	if($passActual && $passNuevo && $passNuevo2 && empty($mensaje)){
		require_once('clases/clsUsuarios.php');
		$objUsuario = new Usuario($objSQLServer);
		
                //--Ini. Se implementa HASH256 y que conviva con md5 hasta que todos migren a HASH256 mediante el cambio de clave..
                $passActualEncriptado = hash('sha256',trim($passActual));
                $validPassActual = $objUsuario->obtenerPassActual($_SESSION["idUsuario"],$passActualEncriptado);
                
                if($validPassActual == false){//-- si es false, verificamos si no posee hash256 (borrar cuando se desida sacar por completo md5)
                    $passActualEncriptado = md5($passActual);
                    $validPassActual = $objUsuario->obtenerPassActual($_SESSION["idUsuario"],$passActualEncriptado);
                }
                //--Fin.
                
                if($validPassActual){
			if($passNuevo != $passNuevo2){
				$mensaje = $lang->message->password_distintos;
			}
			else{
				$msjError = checkString($_POST['txtNombre'], 0, 30,$lang->system->nombre,true);
				if($msjError){
					$mensaje.="* ".$msjError."<br/>";
				}
				$msjError = checkString($_POST['txtApellido'], 0, 30,$lang->system->apellido,false);
				if($msjError){
					$mensaje.="* ".$msjError."<br/>";
				}
				
				if(empty($mensaje)){
					//$passNuevoEncriptado = md5($passNuevo);
                                        $passNuevoEncriptado = hash('sha256',trim($passNuevo));
                                    	if ($objUsuario->actualizarPassword($_SESSION['idUsuario'],$passNuevoEncriptado)) {
						
						$params['us_nombre'] = trim($_POST['txtNombre']);
						$params['us_apellido'] = trim($_POST['txtApellido']);
						$objSQLServer->dbQueryUpdate($params, 'tbl_usuarios', 'us_id = '.(int)$_SESSION['idUsuario']);
						
						$_SESSION['us_nombre'] = $params['us_nombre'];
						$_SESSION['us_apellido'] = $params['us_apellido'];
						$mensaje = $lang->message->ok->guardar_datos;
					}
					else {
						$mensaje = $lang->message->error->guardar_datos;
					}
				}
			}
		}
		else{
			$mensaje = $lang->message->password_actual_error;
		}
	}
		
	index($objSQLServer, $seccion, $mensaje);
}

function popup($objSQLServer, $seccion) {
	global $solapa;
	
	if(file_exists('controladores/cuenta_'.$solapa.'Controlador.php')){
		include 'controladores/cuenta_'.$solapa.'Controlador.php';
		solapaPopup($objSQLServer, $seccion);
	}
	else{
		index($objSQLServer, $seccion);
	}
}

function popupSendSMS($objSQLServer, $seccion){
    include_once 'clases/class.curl_url.php';
	$objCurl = new curl_url();
    
    $msg = $_POST['hidMessage'].(!empty($_POST['hidAdicional']) ? ' - '.$_POST['hidAdicional'] : '');
    $datos = array(
        'number' => $_POST['hidNumber'] 
        ,'message' => str_replace('/','',$msg)
        ,'title' => $_POST['hidTitle']
        ,'url' => !empty($_POST['hidPath']) ? $_POST['hidPath'] : '--'
		,'type' => !empty($_POST['hidMessagetype']) ? $_POST['hidMessagetype'] : 1
		,'app' => 'avanti'
    );

	$path = "https://www.localizar-t.com:81/pod/push/".http_build_query($datos);
    $header = array('Content-type: multipart/form-data', 'application/x-www-form-urlencoded');
    $datos = NULL;

    $response = $objCurl->get($path,$datos,$header);
    /*$status = false;
    if($response['transaction_status'] == 'ok'){
        $status = true;

        //--Se registra SMS enviado
        $idusuario = $_SESSION['idUsuario'];
        $idviaje = intval($_POST['attridmovil']);
        $params = array(
            'vme_mensaje' => $msg
            ,'vme_us_id' => $idusuario
            ,'vme_vi_id' => $idviaje 
        );
        $objSQLServer->dbQueryInsert($params, 'tbl_viajes_mensajes_enviados');
        //--
	}*/
	$status = true;
    $message = decode($response['message']);


    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';

	$seccion = 'cuenta_moviles';
    $vista = 'sendtosms';    
    require("includes/frametemplate.php");
}
?>
