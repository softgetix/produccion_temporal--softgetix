<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

$method = (isset($_GET['method'])) ? $_GET['method'] : null;
if ($method == "ajax") {
    guardarA($objSQLServer, $seccion);
    die();
}

function index($objSQLServer, $seccion, $mensaje="",$noError=false){
	alta($objSQLServer, $seccion, $mensaje, true);
	exit();
}

function alta($objSQLServer, $seccion, $mensaje="", $popup=false){
	global $sinDefaultCSS;
	
	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
	
	$idEquipo = (int)$_REQUEST['idEquipo'];
	$arrEqquipo = $objEquipo->obtenerEquipos($idEquipo);
	$arrEqquipo = $arrEqquipo[0];
	$arrOperaciones = $objEquipo->getExpresionAlgebraica();
	
	$cantSensores = 3;
	for($i=1; $i<=$cantSensores; $i++){
		$arr = $objEquipo->getTelemetria($idEquipo, $i);	
		$arrTelemetria[$i] = $arr[0];
	}
	
	$operacion = 'alta';
	$tipoBotonera='AM';
	$sinDefaultCSS=true;
	if (!$popup){
		require("includes/template.php");
	}else{
		$extraCSS[]='css/estilosABMDefault.css';
		$extraCSS[] = 'css/estilosAbmPopup.css';
        $extraCSS[] = 'css/popup.css';
		$extraJS[] = 'js/popupFunciones.js?1';
		$extraJS[] = 'js/jquery.blockUI.js';
		require("includes/frametemplate.php");
	}
}


function guardarA($objSQLServer, $seccion){
	global $lang;
	$mensaje='';
	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
	
	foreach($_POST['hidIdUT'] as $nro){
		$arrVacio[$nro] = false;
		
		if(!empty($_POST['txtMin'][$nro]) 
			|| !empty($_POST['txtMax'][$nro])
			|| !empty($_POST['cmbOp'][$nro])
			|| !empty($_POST['txtFactor'][$nro])
			|| !empty($_POST['txtUnidad'][$nro])
			|| !empty($_POST['chkVisible'][$nro])
		){
			if(empty($_POST['txtMin'][$nro]) || empty($_POST['txtMax'][$nro])){
				$mensaje = "Ingrese el valor Min/Max para el registro nro: ".$nro;		 		
			}
		}
		else{
			$arrVacio[$nro] = true;
		}
	}
	
	if(empty($mensaje)){
		foreach($_POST['hidIdUT'] as $nro){
			
			$arrDatos['un_id'] = (int)$_POST['idEquipo'];
			$arrDatos['orden'] = (int)$nro;
			$arrDatos['ut_min'] = $_POST['txtMin'][$nro];
			$arrDatos['ut_max'] =  $_POST['txtMax'][$nro];
			$arrDatos['op_id'] = (int)$_POST['cmbOp'][$nro];
			$arrDatos['ut_factor'] = (int)$_POST['txtFactor'][$nro];
			$arrDatos['ut_unidad'] = utf8_decode($_POST['txtUnidad'][$nro]);
			$arrDatos['ut_visible'] = (int)$_POST['chkVisible'][$nro];
			
			$arrTelemetria = $objEquipo->getTelemetria($_POST['idEquipo'], $nro);
			if($arrTelemetria){
				$arrDatos['ut_id'] = $arrTelemetria[0]['ut_id'];
				if($arrVacio[$nro] == false){
					if(!$objEquipo->updateTelemetria($arrDatos)){
						$mensaje = 'El registro ('.$nro.') no se pudo modificar.';
						break;
					}
				}
				if($arrVacio[$nro] == true){
					if(!$objEquipo->bajaTelemetria($arrDatos['un_id'], $arrDatos['ut_id'])){
						$mensaje = $lang->message->error->msj_baja;
						break;
					}
				}
			}
			else{
				if($arrVacio[$nro] == false){
					if(!$objEquipo->altaTelemetria($arrDatos)){
						$mensaje = 'El registro ('.$nro.') no se ha podido dar de alta';
						break;
					}
				}
			}
		}
	}
	
	if(empty($mensaje)){
		$jsonData['ok'] = true;
		$mensaje = $lang->message->ok->msj_alta;
	}
	else{
		$jsonData['error'] = true;
		$jsonData['mensaje'] = $mensaje;
	}
	echo json_encode($jsonData);
	exit;
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
