<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
error_reporting(0);	
function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	$action = isset($_GET['action']) ? $_GET['action'] : 'listar';
	require_once 'clases/clsAllInOne.php';
	$clsAllInOne = new AllInOne($objSQLServer);
	
	
	if($_POST){
		if($_POST['id_servicio']){
			$resp = $clsAllInOne->setServicioUnidad($_POST['idMovil'], $_POST['id_servicio'], $_POST['estado']);	
			if($resp === 'error'){
				$mensaje = $lang->system->msg_contratar_licencias;
			}
		}
		else{
			$resp = $clsAllInOne->setEstadoMovil($_POST['idMovil'], $_POST['idCliente'], $_POST['estado']);	
			if($resp === 'error'){
				$mensaje = $lang->system->msg_contratar_licencias;
			}
		}
	}
	
	$_REQUEST['idCliente'] = isset($_REQUEST['idCliente'])?$_REQUEST['idCliente']:$_SESSION['idEmpresa'];
	if($_REQUEST['idCliente']){
		$idUsuario = NULL;
		if($_SESSION['idAgente'] == 9048){//mediante la implementacion de fibercorp se relaciona los moviles por usuarios y no por clientes. (se debería hacer lo mismo para ADT)
			$idUsuario = $_SESSION['idUsuario'];
		}
		$arr_moviles = $clsAllInOne->getMoviles($_REQUEST['idCliente'],$idUsuario);
	}
	
	if ($action!='popup') {
        require("includes/template.php");
    } else {
		$extraCSS[] = 'css/estilosAbmPopup.css';
        $extraCSS[] = 'css/popup.css';
		$extraCSS[] = 'css/estilosABMDefault.css';
		$extraJS[] = 'js/popupFunciones.js?1';
		$extraJS[] = 'js/jquery.blockUI.js';
        $popup = true;
		$operacion = 'Listar';
		$tipoBotonera = 'AM';
        require("includes/frametemplate.php");
    }
	
}
?>