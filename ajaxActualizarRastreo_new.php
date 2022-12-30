<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

session_start();
set_time_limit(300);
error_reporting(0);
define("TIEMPO_LIMITE_ALERTA_MAIL", 7200);

include ('clases/clsIdiomas.php');
$objIdioma = new Idioma();
$lang = $objIdioma->getIdiomas($_SESSION['idioma']);

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";
require_once('includes/tipomovil.inc.php');

global $objSQLServer;

require_once 'includes/navbar_permisos.php';
require_once 'clases/clsPerfiles.php';
$objPerfil = new Perfil($objSQLServer);

$nameVar = "rastreo_" . $_SESSION["idUsuario"];
$nameVarConf = $nameVar.'_conf';

// Inicializo la transaccion AJAX
$arrTransaction = array( 'status' => 'ok' );
$arrData = array();


//CONSTANTE QUE DEFINE EL TIEMPO LIMITE A TENER EN CUENTA PARA MOSTRAR UNA ALERTA DE ENVIO DE MAIL. DEBE VALIDARSE EN EL STORE Y EN PHP.
$enSeguimiento = isset($_POST['enSeguimiento']) ? $_POST['enSeguimiento'] : -1;
$orden = isset($_POST['orden']) ? $_POST['orden'] : "";
$esPrimera = isset($_POST['esPrimera'])?$_POST['esPrimera']:0;
$bIsFirstLoad = isset($_POST['firstLoad'])?($_POST['firstLoad'] == 'true'?1:0):0;

if($esPrimera == 1){
    $arrData["esPrimera"] = 0;
}

$arrGrayedEvents = array(
    980, // Falta de reporte
    987, // Falta de reporte de mas de 24hs
    //999, // Evento no definido
);

$invertirOrden = isset($_POST['invertirOrden']) ? $_POST['invertirOrden'] : 0;
$movilesSerialized = isset($_POST['strMoviles']) ? trim($_POST['strMoviles']) : "";
$arrMoviles = explode(",", $movilesSerialized);
// Si es la primera tanda de datos
if ($bIsFirstLoad == 0){
    // Seteo los checks
    array_walk($arrMoviles, function(&$elem, $key)
    {
        $elem = intval($elem);
    });
   $_SESSION[$nameVarConf]['checked_mov_ids'] = $arrMoviles;
}


$arrData['checked_mov_ids'] = isset($_SESSION[$nameVarConf]['checked_mov_ids']) ? $_SESSION[$nameVarConf]['checked_mov_ids'] : array();
sort($arrMoviles);
require_once('clases/clsRastreo.php');
$radSeleccionado = isset($_POST['radSeleccionado']) ? $_POST['radSeleccionado'] : "";
$time = time();
$tablaReportes = "";
$objRastreo = new Rastreo($objSQLServer);
$arrData['vistamovil'] = $_SESSION[$nameVarConf]['vistamovil'];
$arrMovilesUsuario = "";
$arrData['search_filter'] = '';

if(isset($_SESSION[$nameVar])){
    $arrMovilesUsuario = $_SESSION[$nameVar];
}

if($invertirOrden == 1){
    if($orden == 1){
        $arrData['orden'] = 2;
        $orden = 2;
    } 
    else{
        $arrData['orden'] = 1;
        $orden = 1;
    }
}

//-----------------------------------------------------------------------------------------------------------------------------

// BEGIN codigo grupos
$arrGroups = array();
$arrGroupIDs = array();

if($_SESSION[$nameVarConf]['is_data_update']){
    if(count($_SESSION[$nameVarConf]['updated_mov_ids'] ) > 0){
        $arrData['updated_mov_ids'] = $_SESSION[$nameVarConf]['updated_mov_ids'];
    }
    else{
        $arrData['updated_mov_ids'] = array();
    }
}



if (is_array($arrMovilesUsuario)){
    $movilesSerialized2 = "";
    //  Primer ciclo:
    //      - Clasificacion de grupos.
    //      - Cantidad de chequeados por grupo.
    
	foreach($arrMovilesUsuario as $movil){
    	if($movil['mo_id']){
          
		    $iGroupID = $movil['um_grupo']?$movil['um_grupo']:0;
            $iMovilID = $movil['mo_id'];

            if($iGroupID == 0){
                $sGroupName = $lang->system->sin_grupo->__toString();
            } 
            else{
                $sGroupName = $movil['gm_nombre'];
            }
            
            // Agrego cada grupo a una lista de grupos
            if(in_array($iGroupID, $arrGroupIDs) == false){
                $arrGroupIDs[] = $iGroupID;
                $arrGroups[$iGroupID] = array(
                    'id' => $iGroupID,
                    'nombre' => $lang->system->$sGroupName?$lang->system->$sGroupName->__toString():$sGroupName,
                    'flagEnvioMailGrupo' => 0,
                    'cantMovsSeleccionados' => 0,
                    'cantMovsTotal' => 0,
                    'movs' => array()
              );
            }
            
			if($bIsFirstLoad && tienePerfil(16)){//-- Seleccionar todos los moviles en el primer ingreso--//
				if(!is_array($_SESSION[$nameVarConf]['checked_mov_ids'])){
					$_SESSION[$nameVarConf]['checked_mov_ids'] = array();	
				}
				array_push($_SESSION[$nameVarConf]['checked_mov_ids'],$movil['mo_id']);	
			}
			
			
			if(isset($_SESSION[$nameVarConf]['checked_mov_ids']) && in_array( $movil['mo_id'], $_SESSION[$nameVarConf]['checked_mov_ids'] ) !== false){
                $arrGroups[$iGroupID]['cantMovsSeleccionados']++;
            }
            
            @$arrGroups[$iGroupID]['cantMovsTotal']++;
        }
    }
    ##-- END 1er ciclo --##
    
	
	##-- Segundo ciclo: --##
    //      - Clasificacion de moviles segun grupo.
    $arrData['cantMovil'] = 0;
	$mov = array();
	foreach($arrMovilesUsuario as $movil){
        $iGroupID = $movil['um_grupo'];
        $iMovilID = $movil['mo_id'];
		
        if(
			( $_SESSION[$nameVarConf]['is_data_update'] && in_array( $iMovilID, $_SESSION[$nameVarConf]['updated_mov_ids'] ) ) 
			|| ( !$_SESSION[$nameVarConf]['is_data_update'] )
        ){
			$tipopto = "";
            $carpetaImagen = "";
			$img = "unknown.png";
			
			// *****************************************
			//   Proceso la informacion de cada movil
			// *****************************************
				
            if($movil["tipo_data"] == 1){
				$movil['estado_motor']  = getEstadoMotor($movil);
				
				$arr = getDataMovil($movil);// en includes/tipomovil.inc.php
				$bEncendido = $arr['bEncendido']; 
				$bEsCelular = $arr['bEsCelular']; 
				$img = $arr['img'];
				$tipopto = $arr['tipopto'];
				$flagEnvioMailGrupo = $arr['flagEnvioMailGrupo'];
				$mostrarIconoMail = $arr['mostrarIconoMail'];
				$carpetaImagen = $arr['carpetaImagen'];
	
				if(in_array($movil['dr_valor'], $arrGrayedEvents)){//evento falta de reporte y evento falta de reporte mas de 24 hs
					$movil['estado_movil'] = 'gris';
				}
				elseif($bEsCelular){
					$movil['estado_movil'] = ($movil['dg_velocidad'] > 0)?'movimiento':'verde';
				}
				else{
					$movil['estado_movil'] = $movil['estado_motor']?(($movil['dg_velocidad'] > 0)?'movimiento':'verde'):'rojo';
				}
				
				 
				$movil['fechaFormateada'] = substr(formatearFecha($movil["sh_fechaGeneracion"],'short'), 0, 16 );
				$movil['velocidadFormateada'] = formatearVelocidad($movil["dg_velocidad"]);
				$movil['tipopto'] = $tipopto;
			}
			else{
				// *****************************************
				//   Proceso la informacion de cada referencia
				// *****************************************	
				switch($movil['mo_id_tipo_movil']){
					case 1:	
						if(!empty($movil['rg_imagen'])){
							//-- Grupo de Ref con imagen --//
							$img = $movil['rg_imagen'];}
						else{
							$img = "ref-wp.png";}
					break;
					case 2:	
						$img = "ref-zona.png";
					break;
					case 3:	
						$img = "ref-ruta.png";
					case 4:	
						$img = "ref-trafico.png";
					break;	
				}
				
				$carpetaImagen = "referencias";
				$movil['fechaFormateada'] = $movil["sh_fechaGeneracion"];
				$movil['velocidadFormateada'] = '';
				$movil['tipopto'] = 'referencia';
			}
			
			$movil['iconName'] = $img;
			$movil['iconFolder'] = $carpetaImagen;

            $arrGroups[$iGroupID]['movs'][] = $movil;
        }
		$arrData['cantMovil'] ++;
		array_push($mov,$iMovilID);
    }
	##-- End 2do ciclo --##
	
	$arrData['paso1'] = 'newTracer.sinFiltroBusq';
    $arrData['groupIDs'] = $arrGroupIDs;
    
    if($movilesSerialized){
        $arrData['paso1'] = 'newTracer.sinFiltroBusq';
        $arrData['marcadoresAgrupados'] = $movilesSerialized;
    } 
    elseif($movilesSerialized2){
        $arrData['paso1'] = 'newTracer.sinFiltroBusq';
        $arrData['marcadoresAgrupados'] = $movilesSerialized2;
    } 
    else{
        $arrData['marcadoresAgrupados'] = 'none';
    }
}

$arrData['canModifyGroups'] = false;
if($objPerfil->validarSeccion('abmGrupoMoviles')){
    $arrData['canModifyGroups'] = true;
}

$arrData['canAssignGroups'] = false;
if($objPerfil->validarSeccion('aMovilesUsuariosMasivo')){
    $arrData['canAssignGroups'] = true;
}

$arrData['groups'] = $arrGroups;
$arrData['enSeguimiento'] = $enSeguimiento;

if(!isset($_SESSION[$nameVarConf]['expanded_group_ids'])){
	$_SESSION[$nameVarConf]['expanded_group_ids'] = array();	
}
$arrData['expanded_group_ids'] = $_SESSION[$nameVarConf]['expanded_group_ids'];

// END codigo grupos

// ---------------------------------------------------------

$error = false;
if($error){
    $arrTransaction['status'] = 'error';
    $arrTransaction['message'] = '<inserte mensaje de error>';
}
else{
    $arrTransaction['packed'] = $arrData;
}

// Convierto a formato JSON y lo envio
$objSQLServer->dbDisconnect();

$sJSON = json_encode($arrTransaction);
header('Content-type: application/json');
echo $sJSON;