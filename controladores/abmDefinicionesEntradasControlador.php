<?php

$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
	require_once 'clases/clsEntradas.php';
   	$operacion = 'listar';
   	$tipoBotonera='LI';
   	$objEntrada = new Entrada($objSQLServer);
   
   	$filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:"";
	
	$arrEntidades = $objEntrada->obtenerRegistros(0,$filtro);
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

function modificar($objSQLServer, $seccion="", $mensaje="", $idEntrada=0){
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idEntrada)? $idEntrada: 0); 
	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
	
	require_once 'clases/clsEntradas.php';
	$objEntrada = new Entrada($objSQLServer);	
	$arrEntidades = $objEntrada->obtenerRegistros($id);
   
   	$operacion = 'modificar';
   	$tipoBotonera='AM';
   	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	//ELIMINA UNO O VARIOS REGISTROS DE LA TABLA CORRESPONDIENTE
	require_once 'clases/clsEntradas.php';
   $arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0; 
   $objEntrada = new Entrada($objSQLServer);
   $idEntradas="";
   for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idEntradas.=$arrCheks[$i];	   	
		else $idEntradas.=$arrCheks[$i].",";
   }
   if($idEntradas){
   	if($objEntrada->eliminarRegistro($idEntradas)){
   		$mensaje = $lang->message->ok->msj_baja;		
   	}else{
   		$mensaje = $lang->message->error->msj_baja;
   	}
   }
   index($objSQLServer, $seccion, $mensaje);
}

function guardarA($objSQLServer, $seccion){
	global $lang;
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
   	require_once 'clases/clsEntradas.php';
   	$objEntrada = new Entrada($objSQLServer);
   	if($objEntrada->insertarRegistro($campos,$valorCampos,$campoValidador)){
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
   	$idEntrada = (isset($_POST["hidId"]))? $_POST["hidId"]:""; 
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
   	require_once 'clases/clsEntradas.php';
   	$objEntrada = new Entrada($objSQLServer);
	$cod = $objEntrada->modificarRegistro($set,$idEntrada, $campoValidador);
   	switch($cod){
   		case 0:
   			$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
   			modificar($objSQLServer, $seccion, $mensaje,$idEntrada);	
   			break;
   		case 1:
   			$mensaje = $lang->message->ok->msj_modificar;
   			index($objSQLServer, $seccion, $mensaje);
   			break;
   		case 2:
   			$mensaje = $lang->message->error->msj_modificar;
   			modificar($objSQLServer, $seccion, $mensaje,$idEntrada);	
   			break;
   	}
	}else{
		//redireccionar al alta con los datos cargados.
		modificar($objSQLServer, $seccion, $mensaje,$idEntrada);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
?>
