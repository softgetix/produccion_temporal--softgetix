<?php
//$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

function listado($objSQLServer, $seccion, $mensaje = "") {
	//$method = (isset($_GET['method'])) ? $_GET['method'] : null;

	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	global $arrEntidades;

	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
   	$arrEntidades = $objMovil->obtenerGruposMovilesUsuario(0,$filtro,$_SESSION["idUsuario"],1);
	
	/*
	$operacion = 'listar';
    $tipoBotonera = 'LI';
    require("includes/template.php");
    */
}

function solapaAlta($objSQLServer, $seccion, $mensaje=""){
	global $solapa;
	global $lang;
	 
	require_once 'clases/clsMoviles.php';
   	$objMovil = new Movil($objSQLServer);
	$arrGrupos = $objMovil->obtenerGruposMovilesUsuario(0,"",$_SESSION["idUsuario"],1);
	   
	$extraJS[]='js/boxes.js';
 	$operacion = 'alta';
	$tipoBotonera='AM';	
	require("includes/template.php");
}

function solapaGuardarA($objSQLServer, $seccion){
	global $lang;
	
	$arrMoviles = isset($_POST["cmbMovilesAsignados"]) ? $_POST["cmbMovilesAsignados"]: array(); 
   	foreach($arrMoviles as $i => $item){
   		$arrMoviles[$i] = trim($item);
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
			index($objSQLServer, $seccion, $mensaje);
   		}
		else{
   			$mensaje = $lang->message->error->msj_alta;
			solapaAlta($objSQLServer, $seccion, $mensaje);
   		}
	}
	else{
		solapaAlta($objSQLServer, $seccion, $mensaje);
	}
}

function solapaModificar($objSQLServer, $seccion="", $mensaje="", $id=0){
	global $solapa;
	global $lang;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:($id?$id:0);
	 
	require_once 'clases/clsMoviles.php';
   	$objMovil = new Movil($objSQLServer);
	$arrGrupos = $objMovil->obtenerGruposMovilesUsuario(0,"",$_SESSION["idUsuario"],1);
	
	$arrEntidades = $objMovil->obtenerGruposMovilesUsuario($id,'',$_SESSION["idUsuario"],1);
   	$arrGrupos = $objMovil->obtenerGruposMovilesUsuario(0,"",$_SESSION["idUsuario"],1);
	$arrMovilesAsignados = $objMovil->obtenerMovilesGrupo($id,$_SESSION["idUsuario"]);

	$extraJS[]='js/boxes.js';
 	$operacion = 'modificar';
	$tipoBotonera='AM';	
	require("includes/template.php");
}

function solapaGuardarM($objSQLServer, $seccion){
	global $lang;
	$idGrupo = (isset($_POST["hidId"]))? $_POST["hidId"]:""; 
	
	$arrMoviles = isset($_POST["cmbMovilesAsignados"]) ? $_POST["cmbMovilesAsignados"]: array(); 
   	foreach($arrMoviles as $i => $item){
   		$arrMoviles[$i] = trim($item);
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
				
				index($objSQLServer, $seccion, $mensaje);
			}
			else{
				$mensaje = $lang->message->error->msj_modificar;
				solapaModificar($objSQLServer, $seccion, $mensaje,$idGrupo);	
			}
		}
		else{
			$mensaje = $lang->message->error->msj_modificar;
			solapaModificar($objSQLServer, $seccion, $mensaje,$idGrupo);	
	   	}
	}
	else{
		$mensaje = $lang->message->error->msj_modificar;
		solapaModificar($objSQLServer, $seccion, $mensaje,$idGrupo);
	}
}

function solapaBaja($objSQLServer, $seccion){
	global $lang;
	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0; 

	require_once 'clases/clsMoviles.php';
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