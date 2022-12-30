<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
   global $lang;
   require_once 'clases/clsCategoriaReferencias.php';
   
   $method 	= (isset($_GET['method'])) ? $_GET['method'] : null;
   
   if($method == 'export_prt'){}
   
   $operacion = 'listar';
   $tipoBotonera='LI';
   $objCategoriaReferencias = new CategoriaReferencias($objSQLServer);
   $filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:"";
   
   $arrEntidades = $objCategoriaReferencias->obtenerCategoriaReferencias(0,$filtro);
   
   $extraJS[] = 'js/abmCategoriaReferenciasFunciones.js';
   require("includes/template.php");
}

function alta($objSQLServer, $seccion, $mensaje=""){
	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
   	$operacion = 'alta';
   	$tipoBotonera='AM';
   	$extraJS[] = 'js/picker/jscolor.js';
   	require("includes/template.php");
}

function modificar($objSQLServer, $seccion="", $mensaje="", $idCategoriaReferencias=0){
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idCategoriaReferencias)? $idCategoriaReferencias: 0); 
	
	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	
	require_once 'clases/clsCategoriaReferencias.php';
	$objCategoriaReferencias = new CategoriaReferencias($objSQLServer);
	
	$arrEntidades = $objCategoriaReferencias->obtenerCategoriaReferencias($id);
	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
	$patch_privada = 'imagenes\categoria_referencias\\' . $arrEntidades[0]['rg_id'] . '.' . $arrEntidades[0]['rg_imagen'];
    $patch_publica = 'imagenes\categoria_referencias\\' . $arrEntidades[0]['rg_id'] . '.' . $arrEntidades[0]['rg_imagen'];
    $imagen_exists = file_exists($patch_privada);
   
	$tipoBotonera='AM';
	$operacion = 'modificar';
	$extraJS[] = 'js/picker/jscolor.js';
	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsCategoriaReferencias.php';
   	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0; 
   	$objCategoriaReferencias = new CategoriaReferencias($objSQLServer);
   	$idCategoriaReferencias="";
   	for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idCategoriaReferencias.=$arrCheks[$i];	   	
		else $idCategoriaReferencias.=$arrCheks[$i].",";
   	}
   
   	if($idCategoriaReferencias){
		$msj = "";
		
		for ($i=0; $i<count($arrCheks); $i++) {		
			$sql = "SELECT COUNT(*) as cant, rg_nombre FROM tbl_referencias INNER JOIN tbl_referencias_grupos ON (rg_id = re_rg_id) WHERE re_rg_id = ".$arrCheks[$i]." GROUP BY rg_nombre";
			$objCategoriaRef = $objSQLServer->dbQuery($sql);
			$objRow = $objSQLServer->dbGetRow($objCategoriaRef, 0);
			if ($objRow) { // SI ESTE REGISTRO POSEE ALGUNA ASIGNACION		
				$msj .= "- ".$objRow['rg_nombre']."<br>";
			}
			else { // SINO POSEE NINGUNA ASIGNACION, ENTONCES ELIMINA
				$objCategoriaReferencias->eliminarCategoriaReferencias($idCategoriaReferencias,"tbl_referencias_grupos","rg");
			}
		}
		
		if ($msj!="") {
			$mensaje = "<strong>Existen referencias que tienen asignadas la/s categoria/s a eliminar.</strong>";	
			$mensaje .= "<br><br>".$msj; // MENSAJE SI HUBO INCONVENIENTES PARA ELIMINARA ALGUNO DE LOS REGISTROS SELECCIONADOS
		} else {
			$mensaje = $lang->message->ok->msj_baja;
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
   
   if (isset($_POST['color'])) {
		if ($_POST['color']!="") {
			$campos .= ",rg_color";
			$valorCampos .= ",''".$_POST['color']."''";
		}
	}
   
   
   //FIN FRAGMENTO
   if(!$mensaje){
   	require_once 'clases/clsCategoriaReferencias.php';
   	$objCategoriaReferencias = new CategoriaReferencias($objSQLServer);
   	if($objCategoriaReferencias->insertarCategoriaReferencias($campos,$valorCampos,$campoValidador,"tbl_referencias_grupos")){
	
	
		$strSQL = "SELECT ident_current('tbl_referencias_grupos')";
		$objEquipos = $objSQLServer->dbQuery($strSQL);
		$objRow = $objSQLServer->dbGetRow($objEquipos, 0);
		$id = $objRow[0];
	
		 //CREO LA IMAGEN
		  $foto = (isset($_FILES['foto'])) ? $_FILES['foto'] : null;
		  $patch = 'imagenes/categoria_referencias/';
		  
		  if(!file_exists($patch)) {
		  	mkdir($patch);
		  }
		  
		  $extension = explode('.', $_FILES['foto']['name']);
		  $extension = end($extension);

		  if (strtoupper($extension)=="JPG") {
			  $destination = $patch . $id . '.' . $extension;
			  move_uploaded_file($foto['tmp_name'], $destination);
			  chmod($destination, 0755);

			  //AGREGO A LA BASE LA EXTENCION DE LA IMAGEN
			  /*echo "agrego<br>";
			  echo "id: ".$id."<br>";
			  echo "ext: ".$extension."<br>";
			  die();*/
			  $objCategoriaReferencias = new CategoriaReferencias($objSQLServer);
			  $objCategoriaReferencias->modificarImagen($id, $extension);
		  } else {
		  

		  
			  modificar($objSQLServer, $seccion, "Solo es posible subir archivos con extensi&oacute;n: jpg", $id);
			  die();
		  }
	
	
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
	$idCategoriaReferencias = (isset($_POST["hidId"]))? $_POST["hidId"]:""; 
   	
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
  
			
   if (isset($_POST['colorCategoriaReferencias'])) {
		if ($_POST['colorCategoriaReferencias']!="") {
			$valor="''#".$_POST["colorCategoriaReferencias"]."''";
		} else {
			$valor="NULL";
		}
   } else {
	   $valor="NULL";
   }
   
   
   $set.= ",rg_color = "."".$valor."";
   
   
   //FIN FRAGMENTO
   if(!$mensaje){
   	require_once 'clases/clsCategoriaReferencias.php';
   	$objCategoriaReferencias = new CategoriaReferencias($objSQLServer);
   	$cod = $objCategoriaReferencias->modificarCategoriaReferencias($set,$idCategoriaReferencias,"tbl_referencias_grupos","rg",$campoValidador);
	
	
	$extension = $objCategoriaReferencias->obtenerExtensionImagen($idCategoriaReferencias);
           //$patch_privada = 'C:\xampp\htdocs\localizart\imagenes\repositorio\\' . $idMovil .'.'. $extension;
		   $patch_privada = 'imagenes/categoria_referencias/' . $idCategoriaReferencias .'.'. $extension;

          $borrar_foto = (isset($_POST["borrar_foto"])) ? $_POST["borrar_foto"] : false;


	     if($borrar_foto=="true") {
			  if(file_exists($patch_privada)) {
			  	unlink($patch_privada);
			  }
          }

          if(isset($_FILES['foto']['tmp_name']) && !empty($_FILES['foto']['tmp_name'])) {
	
			  $patch = 'imagenes\categoria_referencias\\';
			  if (!file_exists($patch)) {
			   	mkdir($patch,0777);  
			  }
			  
			  $extension = explode('.', $_FILES['foto']['name']);
			  $extension = end($extension);
			  if (strtoupper($extension)=="JPG") {
				  $destination = $patch . $idCategoriaReferencias . '.' . $extension;
				  move_uploaded_file($_FILES['foto']['tmp_name'], $destination);
				  chmod($destination, 0755);
		
				  //AGREGO A LA BASE LA EXTENCION DE LA IMAGEN
				  $objCategoriaReferencias->modificarImagen($idCategoriaReferencias, $extension);
			  } else {
			  //	die();
			  	  modificar($objSQLServer, $seccion, "Solo es posible subir archivos con extensi&oacute;n: jpg", $idCategoriaReferencias);
				  die();
			  }
          }
	
	
   	switch($cod){
   		case 0:
   			$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
   			modificar($objSQLServer, $seccion, $mensaje,$idCategoriaReferencias);	
   			break;
   		case 1:
   			$mensaje = $lang->message->ok->msj_modificar;
   			index($objSQLServer, $seccion, $mensaje);
   			break;
   		case 2:
   			$mensaje = $lang->message->error->msj_modificar;
   			modificar($objSQLServer, $seccion, $mensaje,$idCategoriaReferencias);	
   			break;
   	}
	}else{
		//redireccionar al alta con los datos caados.
		modificar($objSQLServer, $seccion, $mensaje,$idCategoriaReferencias);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
?>
