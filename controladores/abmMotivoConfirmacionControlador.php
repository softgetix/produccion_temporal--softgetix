<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
   require_once 'clases/clsMotivoConfirmacion.php';
   
   $method 	= (isset($_GET['method'])) ? $_GET['method'] : null;
   if($method == 'export_prt') {}
   
   $operacion = 'listar';
   $tipoBotonera='LI';
   $objMotivoConfirmacion = new MotivoConfirmacion($objSQLServer);
   $filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:"";
   $arrEntidades = $objMotivoConfirmacion->obtenerRegistros(0,$filtro);
   $extraCSS=array('css/demo_page.css','css/demo_table_jui.css','css/TableTools.css','css/smoothness/jquery-ui-1.8.4.custom.css');
   
   require("includes/template.php");
}

function alta($objSQLServer, $seccion, $mensaje=""){
   require_once 'clases/clsInterfazGenerica.php';
   $objInterfazGenerica = new InterfazGenerica($objSQLServer);
   $arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
   $operacion = 'alta';
   $tipoBotonera='AM';
   require("includes/template.php");
}

function modificar($objSQLServer, $seccion="", $mensaje="", $idMotivoConfirmacion=0){
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idMotivoConfirmacion)? $idMotivoConfirmacion: 0); 
	
	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
	require_once 'clases/clsMotivoConfirmacion.php';
   	$objMotivoConfirmacion = new MotivoConfirmacion($objSQLServer);
   	$arrEntidades = $objMotivoConfirmacion->obtenerRegistros($id);
   	
   	$operacion = 'modificar';
   	$tipoBotonera='AM';
   	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsMotivoConfirmacion.php';
   	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0; 
   	$objMotivoConfirmacion = new MotivoConfirmacion($objSQLServer);
   	$idMotivoConfirmacion="";
   	for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idMotivoConfirmacion.=$arrCheks[$i];	   	
		else $idMotivoConfirmacion.=$arrCheks[$i].",";
   }
   if($idMotivoConfirmacion){
   	
	if($objMotivoConfirmacion->eliminarRegistro($idMotivoConfirmacion)){
   		$mensaje = $lang->message->ok->msj_baja;		
   	}else{
   		$mensaje = $lang->message->error->msj_baja;
   	}
   }
   index($objSQLServer, $seccion, $mensaje);
}

function guardarA($objSQLServer, $seccion){
	global $lang;
	global $campoValidador;
   	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   $campos="";
   $valorCampos="";
   $mensaje="";
   for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];
		if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = $_POST[$idCampo]; 

		        $msjError = "";		
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		$campos.= $arrElementos[$i]["ig_value"].",";
		$valorCampos.= "''".$_POST[$idCampo]."'',";
		//--
   }
   //FIN FRAGMENTO
   if(!$mensaje){
   	require_once 'clases/clsMotivoConfirmacion.php';
   	$objMotivoConfirmacion = new MotivoConfirmacion($objSQLServer);
	$campoValidador	= !empty($campoValidador)?("mc_descripcion = '".$campoValidador."'"):$campoValidador;
   	if($objMotivoConfirmacion->insertarRegistro($campos,$valorCampos,$campoValidador)){
   		$mensaje = $lang->message->ok->msj_alta;
   		index($objSQLServer, $seccion, $mensaje);
   	}else{
   		$mensaje = $lang->message->error->msj_alta;
   		alta($objSQLServer, $seccion, $mensaje);
   	}
	}else{
		alta($objSQLServer, $seccion, $mensaje);
	}
}

function guardarM($objSQLServer, $seccion){
	global $lang;
  	$idMotivoConfirmacion = (isset($_POST["hidId"]))? $_POST["hidId"]:""; 
   	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   	$mensaje="";
   	$set="";
   	for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];
		if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = $_POST[$idCampo]; 
		
		        $msjError = "";		
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		$set.= $arrElementos[$i]["ig_value"]."="."''".$_POST[$idCampo]."'',";
		//--
   }
   //FIN FRAGMENTO
   if(!$mensaje){
   	require_once 'clases/clsMotivoConfirmacion.php';
   	$objMotivoConfirmacion = new MotivoConfirmacion($objSQLServer);
	$campoValidador	= !empty($campoValidador)?("mc_descripcion = '".$campoValidador."'"):$campoValidador;
   	$cod = $objMotivoConfirmacion->modificarRegistro($set,$idMotivoConfirmacion, $campoValidador);
   	switch($cod){
   		case 0:
   			$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
   			modificar($objSQLServer, $seccion, $mensaje,$idMotivoConfirmacion);	
   			break;
   		case 1:
   			$mensaje = $lang->message->ok->msj_modificar;
   			index($objSQLServer, $seccion, $mensaje);
   			break;
   		case 2:
   			$mensaje = $lang->message->error->msj_modificar;
   			modificar($objSQLServer, $seccion, $mensaje,$idMotivoConfirmacion);	
   			break;
   	}
	}else{
		//redireccionar al alta con los datos cargados.
		modificar($objSQLServer, $seccion, $mensaje,$idMotivoConfirmacion);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
?>
