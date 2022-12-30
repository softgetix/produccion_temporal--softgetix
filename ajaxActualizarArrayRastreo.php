<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

session_start();
set_time_limit(300);
error_reporting(0);

include ('clases/clsIdiomas.php');
$objIdioma = new Idioma();
$lang = $objIdioma->getIdiomas($_SESSION['idioma']);

$nameVar = "rastreo_".$_SESSION["idUsuario"];
$nameVarConf = $nameVar.'_conf';

$conReferencias = isset($_POST['conReferencias'])?($_POST['conReferencias'] == 'true'?1:0):0;
$bIsUpdate = isset($_POST['isUpdate'])?($_POST['isUpdate'] == 'true'?1:0):0;
$iCriteria = isset($_POST['criteria'] )?$_POST['criteria']:0; // 0 = ordenamiento por grupo

//--Ini. Ajuste de ordenamiento para Forza--//
if($_SESSION["idAgente"] == 14121){
	$iCriteria = 5;
}
//--Fin. Ajuste de ordenamiento para Forza--//

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

require_once("clases/clsRastreo.php");

$return = "";
$tablaReportes = "";
$objRastreo = new Rastreo($objSQLServer);

$arrUTF8ReadyFields = array('movil','gm_nombre','tr_descripcion');

//SI EXISTE LA VARIABLE DE SESION ACTUALIZO CON LOS ULTIMOS REPORTES LLEGADOS SI NO ES PQ ES LA PRIMERA ACTUALIZACION Y CONSULTO TODOS LOS DATOS
$_SESSION[$nameVarConf]['updated_mov_ids'] = array();

$sinReportar60Dias = tienePerfil(16)?true:false;

// Si es una actualizacion
if ($bIsUpdate){
    ##-- Array de Moviles --##
	$arrMovilesUsuarioUpdate = $objRastreo->obtenerReportesMovilesUsuario($_SESSION["idUsuario"], 0, $bIsUpdate, $iCriteria, $sinReportar60Dias);
    ##-- --##
	$_SESSION[$nameVarConf]['is_data_update'] = true;
    $iUpdatedRecords = 0;

	for($j = 0; $j < count($arrMovilesUsuarioUpdate); $j++ ){
        // Sanitizacion de datos: Preparo como UTF-8 los strings.
        for($jf = 0; $jf < count($arrUTF8ReadyFields); $jf++){
            $sUTF8ReadyField = $arrUTF8ReadyFields[$jf];
            $arrMovilesUsuarioUpdate[$j][$sUTF8ReadyField] = encode($arrMovilesUsuarioUpdate[$j][$sUTF8ReadyField]);
		}
		$arrMovilesUsuarioUpdate[$j]['tipo_data'] = 1;//-- Tipo Movil --//

		for($i = 0; $i < count($_SESSION[$nameVar]); $i++){
			if($_SESSION[$nameVar][$i]['mo_id'] == $arrMovilesUsuarioUpdate[$j]['mo_id']){
				if($_SESSION[$nameVar][$i]['sh_fechaGeneracion'] != $arrMovilesUsuarioUpdate[$j]['sh_fechaGeneracion']){
                    $iUpdatedRecords++;
                    $_SESSION[$nameVarConf]['updated_mov_ids'][] = $arrMovilesUsuarioUpdate[$j]['mo_id'];
                    $_SESSION[$nameVar][$i] = $arrMovilesUsuarioUpdate[$j];
                    
					if(isset($_SESSION[$nameVar][$i]['nivelUbicacion'])){ 
                        $_SESSION[$nameVar][$i]['nivelUbicacion'] = '';
                    }
                    
					if(isset($_SESSION[$nameVar][$i]['ubicacion'])){
                        $_SESSION[$nameVar][$i]['ubicacion'] = '';
                    }
				}
				break;	
			}
		}
	}
}
else{ // es la primera tanda de datos para la seccion izquierda
	$arrMovilesUsuario = $objRastreo->obtenerReportesMovilesUsuario($_SESSION["idUsuario"], 0, $bIsUpdate, $iCriteria, $sinReportar60Dias);
    for($i = 0; $i < count($arrMovilesUsuario); $i++){
        for($j = 0; $j < count($arrUTF8ReadyFields); $j++){
            $sUTF8ReadyField = $arrUTF8ReadyFields[$j];
            $arrMovilesUsuario[$i][$sUTF8ReadyField] = encode($arrMovilesUsuario[$i][$sUTF8ReadyField]);
		}
		$arrMovilesUsuario[$i]['tipo_data'] = 1;//-- Tipo Movil --//
    }

	$_SESSION[$nameVar] = $arrMovilesUsuario;
    $_SESSION[$nameVarConf]['is_data_update'] = false;
    
    // Obtengo la preferencia de usuario para la vista de moviles
    if(!isset($_SESSION[$nameVarConf]["vistamovil"])){
        require_once('clases/clsUsuarios.php');
        $objUsuario = new Usuario($objSQLServer);
        $arrPrefsVis = $objUsuario->obtenerVistasMoviles($_SESSION["idUsuario"]);
		$iVistaMovil = $arrPrefsVis[0]['vm_id'];
        $_SESSION[$nameVarConf]["vistamovil"] = $iVistaMovil;
    }
    
    if(!isset($_SESSION[$nameVarConf]['groups_ready'])){
        // Por ser la primera tanda fijo a TODOS los grupos como NO EXPANDIDOS
        // Luego el "ajaxGuardarEstadoGrupo.php" se ocupa de cambiar estos valores

        $arrGroupIDs = array();
        for($i=0; $i < count( $_SESSION[$nameVar] ); $i++){
            $objMovil = $_SESSION[$nameVar][$i];
            $groupID = $objMovil['um_grupo'];
            if(!in_array($groupID, $arrGroupIDs)){
                $arrGroupIDs[] = $groupID;
            }
        }

        $_SESSION[$nameVarConf]['groups'] = array();
        for($g=0; $g < count($arrGroupIDs); $g++){
            $groupID = $arrGroupIDs[$g];
            $_SESSION[$nameVarConf]['groups'][$groupID] = array('expanded' => 0);
        }
        $_SESSION[$nameVarConf]['groups_ready'] = true;
    }

	##-- Se agregan referencias al Array --##
	if($conReferencias){
		require_once("clases/clsReferencias.php");
		$objReferencia = new Referencia($objSQLServer);
		$arrReferencias = $objReferencia->getRenderReferencias();
		foreach($arrReferencias as $item){
			if($item['tipo'] == 1){
				$item['coords'] = array(); // La vacio para  q el array no tenga valores q no usara.	
			}
			
			$newRef = array(
				'mo_id' => '-'.$item['re_id'],
				'sh_latitud' => $item['lat'],
				'sh_longitud' => $item['lng'],
				'movil' => encode($item['re_nombre']),
				'mo_id_tipo_movil' => $item['tipo'],
				'curso' => 0,
				'flagEnvioMail' => 0,
				'dr_valor' => 0,
				'dg_velocidad' => 0,
				'sh_fechaGeneracion' => $item['tr_nombre'], //-- Nombre del tipo de referencia #Circular-Recta-Poligono --//
				'gm_estado' => 0,
				'tr_descripcion' => encode($item['re_descripcion']),
				'mo_motor_encendido' => 0,
				'mo_bit_motor' => 0,
				'byteEncendido' => '00000000',
				'tr_id_reporte' => 0,
				'um_grupo' => '-'.$item['re_rg_id'],
				'gm_nombre' => encode($item['grupo']),
				'tipo_data' => 2, //-- Tipo Referencias --//
				'coords' => $item['coords'],
				'radio_circulo' => $item['radio'],
				'color' => $item['color'],
				'rg_imagen' => $item['rg_imagen']
		   );	
		   
		   array_push($_SESSION[$nameVar], $newRef);
		}
	}
	##-- --##
}
$objSQLServer->dbDisconnect();
