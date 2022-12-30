<?php
function index($objSQLServer, $seccion, $mensaje = "") {
    $action = isset($_GET['action']) ? $_GET['action'] : 'listar';
    $operacion = 'listar';
	//$filtro = isset($_POST['txtFiltro'])?trim($_POST['txtFiltro']):NULL;
	
	$tipoBotonera = 'LI';
	
	require_once 'clases/clsGeneradorDeInformes.php';
    $objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
	$arrInformes = $objGeneradorDeInformes->getInformesPersonalizados();
	 	
	require("includes/template.php");
}

function editarInforme($objSQLServer, $seccion, $mensaje = "", $id = 0){
  	$id = (isset($_POST["hidId"]))?$_POST["hidId"]:(($id)?$id:0);
	
	require_once 'clases/clsGeneradorDeInformes.php';
    $objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
	
	require_once $rel.'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	
	$datos['filtro'] = 'getAllReg';
	$datos['idEmpresa'] = (int)$_SESSION['idEmpresa'];
	$arrUsuarios = $objUsuario->obtenerUsuariosListado($datos);
	
	$informe = $objGeneradorDeInformes->getInformesPersonalizados($id);
	$informe = $informe[0];
	
	if($mensaje){
		$informeUsuarios = array();
	}
	else{
		$informeUsuarios = explode(',',$informe['ipc_us_id']);
	}
	
	$operacion = 'modificar';
    $tipoBotonera = 'AM';
    require("includes/template.php");
}

function activarInforme($objSQLServer, $seccion){
	global $lang;
	$id = isset($_POST["hidId"])?(int)$_POST["hidId"]:NULL;
	require_once 'clases/clsGeneradorDeInformes.php';
    $objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
	
	if($id){
		if(!is_array($_POST['checkEnviarA'])){
			$msjError = checkString($_POST['txtEnviarA'], 0, 9999999 ,NULL,1);
			if($msjError){ 
				$mensaje.='* '.$lang->message->msj_destinatarios_informes.'<br/> ';
			}
		}
		else{
			$objGeneradorDeInformes->setInformePersonalizado($id, true, $_POST['checkEnviarA']);
			$mensaje = $lang->message->ok->msj_modificar;
			index($objSQLServer, $seccion, $mensaje);
			exit;
		}
	}
	editarInforme($objSQLServer, $seccion, $mensaje, $id );
}

function desactivarInforme($objSQLServer, $seccion){
	global $lang;
	$id = isset($_POST["hidId"])?(int)$_POST["hidId"]:NULL;
	require_once 'clases/clsGeneradorDeInformes.php';
    $objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
	
	if($id){
		$objGeneradorDeInformes->setInformePersonalizado($id, false);
		$mensaje = $lang->message->ok->msj_modificar;
	}
	index($objSQLServer, $seccion, $mensaje);
}

function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}