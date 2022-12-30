<?php

$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

function index($objSQLServer, $seccion, $mensaje = "") {
    global $lang;
    $action = isset($_GET['action']) ? $_GET['action'] : 'listar';
    $operacion = 'listar';

    $tipoBotonera = 'LI';
	require_once 'clases/clsCartografia.php';
    $objCartografia = new Cartografia($objSQLServer);
	
	$tituloFiltroBuscador = 'Ubicaci&oacute;n';
	$filtro = $_POST['txtFiltro'];
	$filtro_us['Partido'] = $filtro;
	
	$arrCartografia = $objCartografia->getCartografia($filtro_us);
	$cantidadCoincidencias = ($arrCartografia) ? count($arrCartografia) : 0;
	
	if ($action == 'popup') {
        alta($objSQLServer, 'abmReferencias', $mensaje = "", $popup = true);
    } else {
		$extraCSS = array('css/demo_page.css', 'css/demo_table_jui.css', 'css/TableTools.css', 'css/smoothness/jquery-ui-1.8.4.custom.css');
        $extraJS[] = 'js/popupHostFunciones.js';
        $extraCSS[] = 'css/estilosPopup.css';
        $extraJS[] = 'js/jquery/jquery-ui-1.8.14.autocomplete.min.js';
        $extraJS[] = 'js/jquery/combobox.js';
		$extraJS[] = 'js/jquery.blockUI.js';
        require("includes/template.php");
    }
}

function alta($objSQLServer, $seccion, $mensaje = "", $popup = false) {
    global $lang;
    $operacion = 'alta';
	$tipoBotonera = 'AM';
	
	require_once 'clases/clsCartografia.php';
    $objCartografia = new Cartografia($objSQLServer);
    $arrPais = $objCartografia->getPais();
	$datos = $_POST;
	
	if ($popup) {
        $extraCSS[] = 'css/estilosAbmPopup.css';
        $extraCSS[] = 'css/popup.css';
        $extraJS[] = 'js/popupFunciones.js?1';
		$extraJS[] = 'js/jquery.blockUI.js';
        if (isset($_GET['ref'])) {
            $tipoBotonera = 'A';
        }
        require("includes/frametemplate.php");
    }
	else {
        require("includes/template.php");
    }
}

function modificar($objSQLServer, $seccion = "", $mensaje = "", $idCartografia = 0) {
  	global $lang;
    $operacion = 'modificar';
    $tipoBotonera = 'AM';
    
	require_once 'clases/clsCartografia.php';
    $objCartografia = new Cartografia($objSQLServer);
    $arrPais = $objCartografia->getPais();
	
	$datos['ar_id'] = $id = (isset($_POST["chkId"]))?$_POST["chkId"][0]:(($idCartografia)?$idCartografia:0);
	
	if(isset($_POST["chkId"])){
		$arrCartografia = $objCartografia->getCartografia($datos);
		$arrCartografia = $arrCartografia[0];
		$datos['txtUbicacion'] = $arrCartografia['Partido'];
		$datos['txtProvincia'] = $arrCartografia['Provincia'];
		$datos['cmbPais'] = $arrCartografia['ar_pa_id'];
		$datos['hidPuntos'] = $arrCartografia['Latitud'].','.$arrCartografia['Longitud'];
	}
	else{
		$datos = $_POST;
	}
	
	require("includes/template.php");
}


function baja($objSQLServer, $seccion) {
	global $lang;
	$arrCheks = ($_POST["chkId"]) ? $_POST["chkId"] : 0;
	$idCartografias = "";
    for ($i = 0; $i < count($arrCheks) && $arrCheks; $i++) {
        if ($i + 1 == count($arrCheks))
            $idCartografias.=$arrCheks[$i];
        else
            $idCartografias.=$arrCheks[$i] . ",";
    }
	
	require_once 'clases/clsCartografia.php';
    $objCartografia = new Cartografia($objSQLServer);
	
	if($objCartografia->eliminarRegistro($idCartografias)){
		$mensaje = $lang->message->ok->msj_baja;
	}
	else{
		$mensaje = $lang->message->error->msj_baja;
	}
		
	index($objSQLServer, $seccion, $mensaje);	
}

function guardarA($objSQLServer, $seccion) {
	global $lang;
    $resp = validarCampos($_POST, 'alta');
	$mensaje = $resp['mensaje'];
	$campos = $resp['campos'];
	$valorCampos = $resp['valorCampos'] ;
	
	if(!$mensaje){
        require_once 'clases/clsCartografia.php';
    	$objCartografia = new Cartografia($objSQLServer);
		if ($objCartografia->insertarRegistro($campos, $valorCampos)){
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
    //GUARDA LAS MODIFICACIONES
    $idCartografia = (isset($_POST["hidId"])) ? $_POST["hidId"] : "";
    $resp = validarCampos($_POST, 'modificar');
	$mensaje = $resp['mensaje'];
	$set = $resp['set'];
	
	if(!$mensaje){
        require_once 'clases/clsCartografia.php';
    	$objCartografia = new Cartografia($objSQLServer);
		if ($objCartografia->modificarRegistro($set, $idCartografia)){
			$mensaje = $lang->message->ok->msj_modificar;
		}
		else{
			$mensaje = $lang->message->error->msj_modificar;
		}
		
		index($objSQLServer, $seccion, $mensaje);
	}
	else{
		 modificar($objSQLServer, $seccion, $mensaje, $idCartografia);
	}
}

function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}

function validarCampos($datos, $operacion){
	
	$campos = '';
    $valorCampos = '';
	$set = '';
    $mensaje = '';

	if(empty($datos["txtUbicacion"])){
		$mensaje .= 'Complete el campo Ubicaci&oacute;n <br/>';
	}
	if(empty($datos["txtProvincia"])){
		$mensaje .= 'Complete el campo Provincia <br/>';
	}
	if(empty($datos["cmbPais"])){
		$mensaje .= 'Seleccione una opci&oacute;n del campo Pais <br/>';
	}
	if(empty($datos["hidPuntos"])){
		$mensaje .= 'Indique el punto de referencia sobre el mapa <br/>';
	}
	
	if(empty($mensaje)){
		$point = explode(',',$datos["hidPuntos"]);
		$lat = $point[0];
		$lng = $point[1];
		
		switch($operacion){
			case 'alta':
				$campos.= "Partido";
				$valorCampos.= "'".trim($datos["txtUbicacion"])."'";
				
				$campos.= ",Provincia";
				$valorCampos.= ",'".trim($datos["txtProvincia"])."'";
				
				$campos.= ",ar_pa_id";
				$valorCampos.= ",'".trim($datos["cmbPais"])."'";
				
				$campos.= ",Latitud";
				$valorCampos.= ",'".trim($lat)."'";
				
				$campos.= ",Longitud";
				$valorCampos.= ",'".trim($lng)."'";
			break;
			case 'modificar':
				$set.= " Partido = '".trim($datos["txtUbicacion"])."'";
				
				$set.= " ,Provincia = '".trim($datos["txtProvincia"])."'";
				
				$set.= " ,ar_pa_id = '".trim($datos["cmbPais"])."'";
				
				$set.= " ,Latitud = '".trim($lat)."'";
				
				$set.= " ,Longitud = '".trim($lng)."'";
			break;
		}
	}
   	
	$resp = array('mensaje' => $mensaje, 'campos' => $campos, 'valorCampos' => $valorCampos, 'set' => $set);
	return $resp;
}