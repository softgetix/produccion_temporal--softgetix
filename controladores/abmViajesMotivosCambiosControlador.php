<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
   require_once 'clases/clsViajesMotivosCambios.php';
   
   $operacion = 'listar';
   $tipoBotonera='LI';
   $objViajesMotivosCambios = new ViajesMotivosCambios($objSQLServer);
   $filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:""; 
   $arrEntidades = $objViajesMotivosCambios->obtenerViajesMotivosCambios(0,$filtro);
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

function modificar($objSQLServer, $seccion="", $mensaje="", $idViajesMotivosCambios=0){
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idViajesMotivosCambios)? $idViajesMotivosCambios: 0); 
	
	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
	require_once 'clases/clsViajesMotivosCambios.php';
   	$objViajesMotivosCambios = new ViajesMotivosCambios($objSQLServer);
   	$arrEntidades = $objViajesMotivosCambios->obtenerViajesMotivosCambios($id);
   	
	$operacion = 'modificar';
   	$tipoBotonera='AM';
   	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsViajesMotivosCambios.php';
   	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0; 
   	$objViajesMotivosCambios = new ViajesMotivosCambios($objSQLServer);
   	$idViajesMotivosCambios="";
  	for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idViajesMotivosCambios.=$arrCheks[$i];	   	
		else $idViajesMotivosCambios.=$arrCheks[$i].",";
   	}
   	
	if($idViajesMotivosCambios){
		if($objViajesMotivosCambios->eliminarRegistro($idViajesMotivosCambios)){
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
	 	$msjError = checkAll($arrElementos[$i], @$_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = escapear_string($arrElementos[$i]["ig_value"]);
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
	
		//--
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		if($i+1==count($arrElementos)){
			$campos.= $arrElementos[$i]["ig_value"]; 			
			$valorCampos.= "''".$_POST[$idCampo]."''";	
		}else{
			$campos.= $arrElementos[$i]["ig_value"].","; 			
			$valorCampos.= "''".$_POST[$idCampo]."'',";	
		}
		
		//--
   }
  
   //FIN FRAGMENTO
   if(!$mensaje){
		require_once 'clases/clsViajesMotivosCambios.php';
		$objViajesMotivosCambios = new ViajesMotivosCambios($objSQLServer);
		if($objViajesMotivosCambios->insertarRegistro($campos,$valorCampos)){
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
  	$idViajesMotivosCambios = (isset($_POST["hidId"]))? $_POST["hidId"]:""; 
   	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   	$mensaje="";
   	$set="";
   	for($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];
		if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = $_POST[$idCampo]; 
		
		$msjError = "";
	 	$msjError = checkAll($arrElementos[$i], @$_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = escapear_string($arrElementos[$i]["ig_value"]);
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		
		//--
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		if($i+1 == count($arrElementos)){
			$set.= $arrElementos[$i]["ig_value"]."="."''".$_POST[$idCampo]."''"; 			
		}else{
			$set.= $arrElementos[$i]["ig_value"]."="."''".$_POST[$idCampo]."'',"; 			
		}
		//--
   }
   
   //FIN FRAGMENTO
   if(!$mensaje){
   	require_once 'clases/clsViajesMotivosCambios.php';
   	$objViajesMotivosCambios = new ViajesMotivosCambios($objSQLServer);
   	$cod = $objViajesMotivosCambios->modificarRegistro($set,$idViajesMotivosCambios);
   	switch($cod){
   		case 0:
   			$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
   			modificar($objSQLServer, $seccion, $mensaje,$idViajesMotivosCambios);	
   		break;
   		case 1:
   			$mensaje = $lang->message->ok->msj_modificar;
   			index($objSQLServer, $seccion, $mensaje);
   		break;
   		case 2:
   			$mensaje = $lang->message->error->msj_modificar;
   			modificar($objSQLServer, $seccion, $mensaje,$idViajesMotivosCambios);	
   		break;
   		}
	}
	else{
		modificar($objSQLServer, $seccion, $mensaje,$idViajesMotivosCambios);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
?>
