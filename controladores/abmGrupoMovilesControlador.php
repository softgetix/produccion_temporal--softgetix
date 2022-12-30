<?php

$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
	require_once 'clases/clsMoviles.php';
   	$popup 	= isset($_GET['popup']) ? 1 : 0;
   	$idMovil = isset($_GET["idG"]) ? $_GET["idG"] : 0;
   	if ($popup && $idMovil) {
		modificarAsignacion($objSQLServer,$seccion,"",$idMovil,true);
		die();
   	} else if ($popup) {
		altaAsignacion($objSQLServer, $seccion, "", true);
		die();
   	}
   
	$extraCSS=array('css/demo_page.css','css/demo_table_jui.css','css/TableTools.css','css/smoothness/jquery-ui-1.8.4.custom.css');
	$extraJS=array('js/jquery.dataTables.js','js/jquery.ui.js','js/tableConvert.js');
	$extraJS[]='js/jquery/jquery-ui-1.8.14.autocomplete.min.js';
	$extraJS[]='js/jquery/combobox.js';
   	$operacion = 'listar';
   	$tipoBotonera='LIasignacion2';
   	$objMovil = new Movil($objSQLServer);
   	$filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:"";
   
   $arrEntidades = $objMovil->obtenerGruposMovilesUsuario(0,$filtro,$_SESSION["idUsuario"],1);
   require("includes/template.php");
}

function altaAsignacion($objSQLServer, $seccion, $mensaje="",$popup = false){
   global $lang;
   
   require_once 'clases/clsMoviles.php';
   $operacion = 'altaAsignacion';
   $tipoBotonera='AM';
   $objMovil = new Movil($objSQLServer);
   $arrGrupos = $objMovil->obtenerGruposMovilesUsuario(0,"",$_SESSION["idUsuario"],1);
   if($popup){
		$extraCSS[]='css/estilosAbmPopup.css';
		$extraCSS[]='css/popup.css';
		$extraJS[]='js/popupFunciones.js?1';
		$extraJS[]='js/jquery.blockUI.js';
		require("includes/frametemplate.php");
	}
	else{
		require("includes/template.php");
	}
}

function modificarAsignacion($objSQLServer, $seccion="", $mensaje="", $idMovil=0, $popup = false){
	global $lang;
	
	require_once 'clases/clsMoviles.php';
   	$operacion = 'modificarAsignacion';
   	$tipoBotonera='AM';
   	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idMovil)? $idMovil: 0); 
   	$objMovil = new Movil($objSQLServer);
   	$arrEntidades = $objMovil->obtenerGruposMovilesUsuario($id,'',$_SESSION["idUsuario"],1);
   	$arrGrupos = $objMovil->obtenerGruposMovilesUsuario(0,"",$_SESSION["idUsuario"],1);
	$arrMovilesAsignados = $objMovil->obtenerMovilesGrupo($id,$_SESSION["idUsuario"]);
	if($popup){
		$extraCSS[]='css/estilosAbmPopup.css';
		$extraCSS[]='css/popup.css';
		$extraJS[]='js/popupFunciones.js?1';
		$extraJS[]='js/jquery.blockUI.js';
		require("includes/frametemplate.php");
	}
	else{
		require("includes/template.php");
	}
}

function baja($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsMoviles.php';
   	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0; 
   	$objMovil = new Movil($objSQLServer);
   	$idGrupos="";
   for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idGrupos.=$arrCheks[$i];	   	
		else $idGrupos.=$arrCheks[$i].",";
   }
   if($idGrupos){
   	if($objMovil->eliminarRegistro($idGrupos,'tbl_grupos_moviles','gm')){
   		$objMovil->eliminarMovilesGrupo($idGrupos, $_SESSION["idUsuario"]);
   		$mensaje = $lang->message->ok->msj_baja;		
   	}else{
   		$mensaje = $lang->message->error->msj_baja;
   	}
   }
   index($objSQLServer, $seccion, $mensaje);
}

function guardarAltaAsignacion($objSQLServer, $seccion){
	global $lang;
   	$list2Serialised = (isset($_POST["hidMovilesSerializados"]))? $_POST["hidMovilesSerializados"]:""; 
   	$arrMoviles = explode(",", $list2Serialised);
   	for($i=0; $i < count($arrMoviles) && $arrMoviles; $i++){
   		$arrMoviles[$i]=trim($arrMoviles[$i]);
   	}
   	sort($arrMoviles);
   
	$mensaje="";
	$mensaje= checkString($_POST["txtGrupo"], 3, 30,'Nombre del Grupo',1);
	$campos= "gm_nombre"; 			
	$valorCampos= "''".$_POST["txtGrupo"]."''";	
   	if(!$mensaje){
   		require_once 'clases/clsMoviles.php';
   		$objMovil = new Movil($objSQLServer);
   		if($idGrupo = $objMovil->insertarRegistro($campos, $valorCampos, NULL, 'tbl_grupos_moviles')){
   			for($i=0; $i < count($arrMoviles) && $arrMoviles ;$i++){
   				$objMovil->insertarMovilGrupo($idGrupo,$_SESSION["idUsuario"],$arrMoviles[$i]);	
   			}
   			$mensaje = $lang->message->ok->msj_alta;
			if(isset($_GET["method"]) && $_GET["method"] == 'ajax') {
				$jsonData['id']=$idGrupo;
				$jsonData['ok']='ok';
				echo json_encode($jsonData);
				die();
			}
   			index($objSQLServer, $seccion, $mensaje);
   		}
		else{
   			$mensaje = $lang->message->error->msj_alta;
			if(isset($_GET["method"]) && $_GET["method"] == 'ajax') {
				$jsonData['error'] = 'ok';
            	$jsonData['mensaje'] = trim($mensaje);
            	echo json_encode($jsonData);
				die();
			}
			altaAsignacion($objSQLServer, $seccion, $mensaje);
   		}
	}
	else{
		if(isset($_GET["method"]) && $_GET["method"] == 'ajax') {
			$jsonData['error'] = 'ok';
            $jsonData['mensaje'] = trim($mensaje);
            echo json_encode($jsonData);
			die();
		}
		altaAsignacion($objSQLServer, $seccion, $mensaje);
	}
}

function guardarA($objSQLServer, $seccion) {
	guardarAsignacion($objSQLServer, $seccion, $_POST);
}

function guardarAsignacion($objSQLServer, $seccion, $post = ''){
	global $lang;
   	$idGrupo = (isset($_POST["hidId"]))? $_POST["hidId"]:""; 
   	$list2Serialised = (isset($_POST["hidMovilesSerializados"]))? $_POST["hidMovilesSerializados"]:""; 
   	$arrMoviles = explode(",", $list2Serialised);
   	for($i=0; $i < count($arrMoviles) && $arrMoviles; $i++){
   		$arrMoviles[$i]=trim($arrMoviles[$i]);
   	}
   	sort($arrMoviles);
	$mensaje="";
	$mensaje = checkString($_POST["txtGrupo"], 3, 30,'Nombre del grupo',1);
	
   
   if(!$mensaje){
   	require_once 'clases/clsMoviles.php';
   	$objMovil = new Movil($objSQLServer);
   	$flag=0;

		$set="gm_nombre=''".$_POST["txtGrupo"]."''";
		if($objMovil->modificarRegistro($set, $idGrupo, NULL, 'tbl_grupos_moviles', 'gm')){
	   	if($objMovil->eliminarMovilesGrupo($idGrupo,$_SESSION["idUsuario"])){	
	   		for($i=0; $i < count($arrMoviles) && $arrMoviles; $i++){
	   			$objMovil->insertarMovilGrupo($idGrupo,$_SESSION["idUsuario"],$arrMoviles[$i]);	
	   		}
			
			if (isset($_GET["method"])) {
				if ($_GET["method"] == 'ajax') {
					$jsonData['id']=$idGrupo;
					$jsonData['ok']='ok';
					
					echo json_encode($jsonData);
					die();
				}
			}
	   		index($objSQLServer, $seccion, $mensaje);
	   	}else{
	   		$mensaje = $lang->message->error->msj_modificar;
	   		modificarAsignacion($objSQLServer, $seccion, $mensaje,$idGrupo);	
	   	}
	   }else{
		if ($_GET["method"] == 'ajax') {
			$jsonData['id']=$idGrupo;
			$jsonData['ok']='ok';
			
			echo json_encode($jsonData);
			die();
		}
	   	$mensaje = $lang->message->error->msj_modificar;
	   	modificarAsignacion($objSQLServer, $seccion, $mensaje,$idGrupo);	
	   }
	}else{
		$mensaje = $lang->message->error->msj_modificar;
		modificarAsignacion($objSQLServer, $seccion, $mensaje,$idGrupo);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}