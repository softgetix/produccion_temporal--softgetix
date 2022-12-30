<?php
$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

if($_GET['downloadLog']){
	$url = "";
	$date = date('d-m-Y');
	switch($_GET['downloadLog']){
		case 1:
			$name = 'udp_'.date('Ymd').'.txt';
			$url = 'http://200.32.10.146/log_DTS/envioUDP'.$date.'.txt';
		break;
		case 2:
			$name = 'webservice_'.date('Ymd').'.txt';
			$url = 'http://200.32.10.146/crons/DTS/logenvioWebService'.$date.'.txt';
		break;
		case 3:
			$name = 'tcp_'.date('Ymd').'.txt';
			$url = 'http://200.32.10.146/crons/DTS/log/envioTCP'.$date.'.txt';
		break;	
	}
	if(!empty($url)){
		//$url = $_SERVER['DOCUMENT_ROOT'].$url;
		header("Content-type: application/x-file");
		header("Content-Disposition: attachment; filename=".$name);
		readfile($url);
	}
	else{
		header('Location: boot.php?c='.$seccion);	
	}
	exit;
}


function index($objSQLServer, $seccion, $mensaje = "") {
    $action = isset($_GET['action']) ? $_GET['action'] : 'listar';
    $operacion = 'listar';

    $tipoBotonera = 'LI';
	require_once 'clases/clsProtocolo.php';
    $objProtocolo = new Protocolo($objSQLServer);
	
	$filtro = $_POST['txtFiltro'];
    $filtro_us['pr_nombre'] =  $filtro;
	
	$arrProtocolo = $objProtocolo->getProtocolo($filtro_us);
	
	$extraCSS = array('css/demo_page.css', 'css/demo_table_jui.css', 'css/TableTools.css', 'css/smoothness/jquery-ui-1.8.4.custom.css');
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/jquery/jquery-ui-1.8.14.autocomplete.min.js';
    $extraJS[] = 'js/jquery/combobox.js';
	$extraJS[] = 'js/jquery.blockUI.js';
    require("includes/template.php");
}

function alta($objSQLServer, $seccion, $mensaje = "", $popup = false) {
    require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);

	require_once 'clases/clsProtocolo.php';
    $objProtocolo = new Protocolo($objSQLServer);
    $arrMoviles = $objProtocolo->getMoviles();
	$arrTipoProtocolo = $objProtocolo->getTipoProtocolo();

	$extraJS[] = 'js/jqBoxes.js';
	$operacion = 'alta';
	$tipoBotonera = 'AM';
	require("includes/template.php");
}

function modificar($objSQLServer, $seccion = "", $mensaje = "", $id = 0) {
  	$datos['pr_id'] = $id = (isset($_POST['hidId']))?$_POST['hidId']:(($id)?$id:0);
	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);

	require_once 'clases/clsProtocolo.php';
    $objProtocolo = new Protocolo($objSQLServer);
    $arrMoviles = $objProtocolo->getMoviles($datos);
	$arrMovilesAsig = $objProtocolo->getMovilesAsig($datos);
	$arrTipoProtocolo = $objProtocolo->getTipoProtocolo();
	
	$hidArrMovilesAsignados = "";
	if($arrMovilesAsig){
		foreach($arrMovilesAsig as $item){
			$hidArrMovilesAsignados.= $item['id'].",";
		}
	}
	
	if(isset($_POST['hidId'])){
		$arrProtocolo = $objProtocolo->getProtocolo($datos);
		$arrProtocolo = $arrProtocolo[0];
		
		foreach($arrElementos as $item){
			$_POST[$item['ig_idCampo']] = $arrProtocolo[trim($item['ig_value'])];
		}
		$arrProtocolo['pr_consulta'] = str_replace('&#039',"'",$arrProtocolo['pr_consulta']);
		$arrProtocolo['pr_consulta'] = str_replace('&#39',"'",$arrProtocolo['pr_consulta']);
		$_POST['txtConsulta'] =  str_replace('"',"'",$arrProtocolo['pr_consulta']);
		$_POST['cmbTipoProtocolo'] = $arrProtocolo['pr_pt_id'];
		$_POST['txtNombre'] = $arrProtocolo['pr_nombre'];
		$_POST['txtIp'] = $arrProtocolo['pr_ip'];
		$_POST['txtPuerto'] = $arrProtocolo['pr_puerto'];
	}
	
	$extraJS[] = 'js/jqBoxes.js';
	$operacion = 'modificar';
    $tipoBotonera = 'AM';
    require("includes/template.php");
}


function baja($objSQLServer, $seccion) {
	global $lang;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:(($id)?$id:0);
	
	require_once 'clases/clsProtocolo.php';
    $objProtocolo = new Protocolo($objSQLServer);
	
	if ($objProtocolo->eliminarRegistro($id)){
		$mensaje = $lang->message->ok->msj_baja;
	}
	else{
		$mensaje = $lang->message->error->msj_baja;
	}
		
	index($objSQLServer, $seccion, $mensaje);	
}

function guardarA($objSQLServer, $seccion) {
	global $lang;
	global $campoValidador;
					
	$resp = validarCampos('alta');
	$mensaje = $resp['mensaje'];
	$campos = $resp['campos'];
	$valorCampos = $resp['valorCampos'] ;
	
	if(!$mensaje){
        require_once 'clases/clsProtocolo.php';
    	$objProtocolo = new Protocolo($objSQLServer);
		if ($id = $objProtocolo->insertarRegistro($campos, $valorCampos)){
			$objProtocolo->asignarMoviles($id, $_POST['hid_lstMovilesAsig']);
			$mensaje = $lang->message->ok->msj_alta;
		}
		else{
			$mensaje = $lang->message->error->msj_alta;
		}
		index($objSQLServer, $seccion, $mensaje);
	}
	else{
		alta($objSQLServer, $seccion, $mensaje);
	}
}

function guardarM($objSQLServer, $seccion) { 
   	global $lang;
    $id = (isset($_POST["hidId"])) ? $_POST["hidId"] : "";
   			
	$resp = validarCampos('modificar');
	$mensaje = $resp['mensaje'];
	$set = $resp['set'];
	
	if(!$mensaje){
        require_once 'clases/clsProtocolo.php';
    	$objProtocolo = new Protocolo($objSQLServer);
		if ($objProtocolo->modificarRegistro($set, $id)){
			$objProtocolo->asignarMoviles($_POST['hidId'], $_POST['hid_lstMovilesAsig']);
			$mensaje = $lang->message->ok->msj_modificar;
		}
		else{
			$mensaje = $lang->message->error->msj_modificar;
		}
		index($objSQLServer, $seccion, $mensaje);
	}
	else{
		 modificar($objSQLServer, $seccion, $mensaje, $id);
	}
}

function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}

function validarCampos($operacion){
	global $lang;
	$mensaje = '';
	$arr = array();
	
	$arr['pr_nombre'] = trim($_POST['txtNombre']);	
	$msjError = checkString($_POST['txtNombre'], 0, 50 ,'Nombre Protocolo',1);
	if($msjError){ 
		$mensaje.="* ".$msjError."<br/> ";
	}
		
	if(isset($_POST['txtIp'])){
		$arr['pr_ip'] =  trim($_POST['txtIp']);	
		$msjError = checkString($_POST['txtIp'], 0, 300 ,'IP Protocolo',1);
		if($msjError){ 
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	if(isset($_POST['txtPuerto'])){
		$arr['pr_puerto'] =  trim($_POST['txtPuerto']);	
		$msjError = checkString($_POST['txtPuerto'], 0, 6 ,'Puerto Protocolo',1);
		if($msjError){ 
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	if(isset($_POST['cmbTipoProtocolo'])){
		$arr['pr_pt_id'] = trim($_POST['cmbTipoProtocolo']);
		$msjError = checkCombo(trim($_POST['cmbTipoProtocolo']), 'Tipo Protocolo', 1, 0);
		if($msjError){ 
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	if(isset($_POST['txtConsulta'])){
		$arr['pr_consulta'] = str_replace("'",'"',trim($_POST['txtConsulta']));
	}
	
	$campos = '';
    $valorCampos = '';
	$set = '';
    $coma = '';
	
	foreach($arr as $k => $item){
		switch($operacion){
			case 'alta':
				$campos.= $coma.$k ;
				$valorCampos.= $coma."'".trim($item)."'";
				$coma = ',';
			break;
			case 'modificar':
				$set.= $coma.$k." = '".trim($item)."'";
				$coma = ',';
			break;
		}	
	}
	
	$resp = array('mensaje' => $mensaje, 'campos' => $campos, 'valorCampos' => $valorCampos, 'set' => $set);
	return $resp;
}