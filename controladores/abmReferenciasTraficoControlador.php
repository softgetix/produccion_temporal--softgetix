<?php

$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
	require_once 'clases/clsReferenciasTrafico.php';
   	$objReferenciasTrafico = new ReferenciasTrafico($objSQLServer);
	
	$datos['filtro'] = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:""; 
	$datos['filtro'] = str_replace("`", "&#039",  $datos['filtro']); 
	$datos['filtro'] = str_replace("'", "&#039",  $datos['filtro']);
	$datos['filtro'] = str_replace("\"", "&quot;",  $datos['filtro']);
	$filtro = $datos['filtro'];
   	$arrEntidades = $objReferenciasTrafico->obtenerReferenciasTrafico($datos);
   	
	$extraCSS=array('css/demo_page.css','css/demo_table_jui.css','css/TableTools.css','css/smoothness/jquery-ui-1.8.4.custom.css');
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

function modificar($objSQLServer, $seccion="", $mensaje="", $idReferenciasTrafico=0){
   $id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idReferenciasTrafico)? $idReferenciasTrafico: 0); 
   $datos['id'] = $id;
   
   require_once 'clases/clsInterfazGenerica.php';
   $objInterfazGenerica = new InterfazGenerica($objSQLServer);
   $arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
   require_once 'clases/clsReferenciasTrafico.php';
   $objReferenciasTrafico = new ReferenciasTrafico($objSQLServer);
   $arrEntidades = $objReferenciasTrafico->obtenerReferenciasTrafico($datos);   
   
   $operacion = 'modificar';
   $tipoBotonera='AM';
   require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	//ELIMINA UNO O VARIOS REGISTROS DE LA TABLA CORRESPONDIENTE
	require_once 'clases/clsReferenciasTrafico.php';
   $arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0; 
   $objReferenciasTrafico = new ReferenciasTrafico($objSQLServer);
   $idReferenciasTrafico="";
   for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idReferenciasTrafico.=$arrCheks[$i];	   	
		else $idReferenciasTrafico.=$arrCheks[$i].",";
   }
   if($idReferenciasTrafico){
   	if($objReferenciasTrafico->eliminarRegistro($idReferenciasTrafico)){
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
	$datos['campoValidador'] = $campoValidador;
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
	
   $campos="";
   $valorCampos="";
   $mensaje="";
   for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];			
		if($arrElementos[$i]["ig_validacionExistencia"]) $datos['campoValidador'] = $_POST[$idCampo]; 
		$msjError = "";		
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		
		if($arrElementos[$i]["ig_idCampo"]=='txtVelocidadMax' && ($_POST["txtVelocidadMax"] < $_POST["txtVelocidadMin"])){ $mensaje.="Maximo no puede ser mas chico que Minimo"; }
		
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
   	require_once 'clases/clsReferenciasTrafico.php';
   	$objReferenciasTrafico = new ReferenciasTrafico($objSQLServer);
   	if($objReferenciasTrafico->insertarReferenciasTrafico($campos,$valorCampos,$datos)){
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
   	$idReferenciasTrafico = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
   	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   	$mensaje="";
   	$set="";
   	for($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];			
		if($arrElementos[$i]["ig_validacionExistencia"]) $datos['campoValidador'] = $_POST[$idCampo]; 
		$msjError = "";		
		$msjError = checkAll($arrElementos[$i], $_POST);
		if($msjError){
			$mensaje.="* ".$msjError."<br/> ";
		}
		
		if($arrElementos[$i]["ig_idCampo"]=='txtVelocidadMax' && ($_POST["txtVelocidadMax"] < $_POST["txtVelocidadMin"])){ $mensaje.="Maximo no puede ser mas chico que Minimo"; }
		
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
   	require_once 'clases/clsReferenciasTrafico.php';
   	$objReferenciasTrafico = new ReferenciasTrafico($objSQLServer);	
   	$cod = $objReferenciasTrafico->modificarReferenciasTrafico($set, $idReferenciasTrafico, $campoValidador);
   	switch($cod){
   		case 0:
   			$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
   			modificar($objSQLServer, $seccion, $mensaje,$idReferenciasTrafico);	
   			break;
   		case 1:
   			$mensaje = $lang->message->ok->msj_modificar;
   			index($objSQLServer, $seccion, $mensaje);
   			break;
   		case 2:
   			$mensaje = $lang->message->error->msj_modificar;
   			modificar($objSQLServer, $seccion, $mensaje,$idReferenciasTrafico);	
   			break;
   		}
	}else{
		modificar($objSQLServer, $seccion, $mensaje,$idReferenciasTrafico);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
?>
