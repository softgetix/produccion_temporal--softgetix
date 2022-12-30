<?php

$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
   $filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:""; 
   $method 	= (isset($_GET['method'])) ? $_GET['method'] : null;
   
   require_once 'clases/clsInterfazGenerica.php';
   $objInterfazGenerica = new InterfazGenerica($objSQLServer);
   $arrEntidades = $objInterfazGenerica->obtenerRegistros(0,$filtro);
   
   $operacion = 'listar';
   $tipoBotonera='LI';
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

function modificar($objSQLServer, $seccion="", $mensaje="", $idInterfazGenerica=0){
   $id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idInterfazGenerica)? $idInterfazGenerica: 0); 
   
   require_once 'clases/clsInterfazGenerica.php';
   $objInterfazGenerica = new InterfazGenerica($objSQLServer);
   $arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion); 
   $arrEntidades = $objInterfazGenerica->obtenerRegistros($id);
   
   $operacion = 'modificar';
   $tipoBotonera='AM';
   require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsInterfazGenerica.php';
   	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0; 
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$idInterfazGenerica="";
   	for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idInterfazGenerica.=$arrCheks[$i];	   	
		else $idInterfazGenerica.=$arrCheks[$i].",";
   	}
   if($idInterfazGenerica){
   	if($objInterfazGenerica->eliminarRegistro($idInterfazGenerica)){
   		$mensaje = $lang->message->ok->msj_baja;		
   	}else{
   		$mensaje = $lang->message->error->msj_baja;
   	}
   }
   index($objSQLServer, $seccion, $mensaje);
}

function guardarA($objSQLServer, $seccion) {
   	global $lang;
   	global $campoValidador;
   
   	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
  
   	$campos="";
   $valorCampos="";
   $mensaje="";
   $coma = "";
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
		$campos.= $coma.$arrElementos[$i]["ig_value"];
		$valorCampos.= $coma."''".$_POST[$idCampo]."''";
		$coma = ', ';
		//--
   }
   
   	if(!empty($_POST['cmbSeccion'])){
		$campos.= $coma.'ig_seccion';
		$valorCampos.= $coma."''".trim($_POST['cmbSeccion'])."''";
	}
	else{
		$mensaje.="* Seleccione la Seccion al que pertenece el Item <br/> ";	
	}
	
   //FIN FRAGMENTO
   if(!$mensaje){
   if($objInterfazGenerica->insertarRegistro($campos,$valorCampos)){//,$campoValidador
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
	global $campoValidador;
	$idInterfazGenerica = (isset($_POST["hidId"]))? $_POST["hidId"]:""; 
  	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
   $mensaje="";
   $set="";
   $coma = "";
   for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];
		
        $msjError = "";		
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		$set.= $coma.$arrElementos[$i]["ig_value"]."="."''".$_POST[$idCampo]."''";
		$coma = ', ';
		//--
   }
   
   	if(!empty($_POST['cmbSeccion'])){
		$set.= $coma."ig_seccion="."''".trim($_POST['cmbSeccion'])."''";
	}
	else{
		$mensaje.="* Seleccione la Seccion al que pertenece el Item <br/> ";	
	}
  
   //FIN FRAGMENTO
   if(!$mensaje){
   	$cod = $objInterfazGenerica->modificarRegistro($set,$idInterfazGenerica);
	switch($cod){
   		case 0:
   			$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
   			modificar($objSQLServer, $seccion, $mensaje,$idInterfazGenerica);	
   			break;
   		case 1:
   			$mensaje = $lang->message->ok->msj_modificar;
   			index($objSQLServer, $seccion, $mensaje);
   			break;
   		case 2:
   			$mensaje = $lang->message->error->msj_modificar;
   			modificar($objSQLServer, $seccion, $mensaje,$idInterfazGenerica);	
   			break;
   	}
	}else{
		//redireccionar al alta con los datos cargados.
		modificar($objSQLServer, $seccion, $mensaje,$idInterfazGenerica);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
?>
