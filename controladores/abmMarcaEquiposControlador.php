<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
   require_once 'clases/clsMarcaEquipos.php';
   
   $method 	= (isset($_GET['method'])) ? $_GET['method'] : null;
   
   $objMarcaEquipo = new MarcaEquipo($objSQLServer);
   
   	$filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:""; 
  
  	$arrEntidades = $objMarcaEquipo->obtenerRegistros(0,$filtro);
   	
	$operacion = 'listar';
  	$tipoBotonera='LI';
   	require("includes/template.php");
}

function alta($objSQLServer, $seccion, $mensaje=""){
	global $lang;
   	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
    $operacion = 'alta';
	$tipoBotonera='AM';
  	require("includes/template.php");
}

function modificar($objSQLServer, $seccion="", $mensaje="", $idMarcaEquipo=0){
	global $lang;
   	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idMarcaEquipo)? $idMarcaEquipo: 0); 
	
	require_once 'clases/clsInterfazGenerica.php';
   $objInterfazGenerica = new InterfazGenerica($objSQLServer);
   $arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   	
	require_once 'clases/clsMarcaEquipos.php';
   	$objMarcaEquipo = new MarcaEquipo($objSQLServer);
   	$arrEntidades = $objMarcaEquipo->obtenerRegistros($id);
   	
	$operacion = 'modificar';
   	$tipoBotonera='AM';
   	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0; 
	
	require_once 'clases/clsMarcaEquipos.php';
   	$objMarcaEquipo = new MarcaEquipo($objSQLServer);
   	$idMarcaEquipos="";
   	for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idMarcaEquipos.=$arrCheks[$i];	   	
		else $idMarcaEquipos.=$arrCheks[$i].",";
 	}
   	if($idMarcaEquipos){
   		if($objMarcaEquipo->eliminarRegistro($idMarcaEquipos)){
   			$mensaje = $lang->message->ok->msj_baja;		
   		}
		else{
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
		$msjError="";
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
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
   	require_once 'clases/clsMarcaEquipos.php';
   	$objMarcaEquipo = new MarcaEquipo($objSQLServer);
	$campoValidador	= !empty($campoValidador)?("me_nombre = '".$campoValidador."'"):$campoValidador;
   	if($objMarcaEquipo->insertarRegistro($campos,$valorCampos,$campoValidador)){
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
   	$idMarcaEquipo = (isset($_POST["hidId"]))? $_POST["hidId"]:""; 
   	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   	$mensaje="";
   	$set="";
   	for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];
		if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = $_POST[$idCampo]; 
		$msjError="";
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
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
		require_once 'clases/clsMarcaEquipos.php';
		$objMarcaEquipo = new MarcaEquipo($objSQLServer);
		$campoValidador	= !empty($campoValidador)?("me_nombre = '".$campoValidador."'"):$campoValidador;
		$cod = $objMarcaEquipo->modificarRegistro($set,$idMarcaEquipo, $campoValidador);
		switch($cod){
			case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				modificar($objSQLServer, $seccion, $mensaje,$idMarcaEquipo);	
			break;
			case 1:
				$mensaje = $lang->message->ok->msj_modificar;
				index($objSQLServer, $seccion, $mensaje);
			break;
			case 2:
				$mensaje = $lang->message->error->msj_modificar;
				modificar($objSQLServer, $seccion, $mensaje,$idMarcaEquipo);	
			break;
		}
	}
	else{
		modificar($objSQLServer, $seccion, $mensaje,$idMarcaEquipo);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
?>
