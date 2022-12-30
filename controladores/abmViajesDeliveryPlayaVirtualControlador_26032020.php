<?php
function index($objSQLServer, $seccion, $mensaje="", $filtroCol = false) {
	global $filtro;
    global $lang;
    
    if($_GET['action'] == 'sendmessage'){
        popupSendMessage($objSQLServer, $seccion);
        exit;
    }
    elseif($_GET['action'] == 'viewhistory'){
        popupViewHistory($objSQLServer, $seccion);
        exit;
    }
    elseif($_GET['action'] == 'viewinfoaditional'){
        popupViewInfoAditional($objSQLServer, $seccion);
        exit;
    }
    
	$idUsuario = (int)$_SESSION['idUsuario'];
	
    $col['transportista'] = 'colidTransportista';
	$col['movil'] = 'colidMovil';
	$col['referencia'] = 'colidReferencia';
	$col['arribo'] = 'colArribos';
	$col['partida'] = 'colPartidas';
	require_once 'clases/clsFiltrosCol.php';
    $objFiltroCol = new FiltrosCol($col);

    $solapaValues = array(
        /*'44429' => 'Planta Celulosa | Puerto Esperanza'
        ,'44423' => 'Aserradero/Planta MDF | Puerto Piray'
        ,'44424' => 'Centro Log&iacute;stico Bossetti'
	    ,'44430' => 'Planta Aglomerado | Zarate'
        ,'55964' => 'Planta Aglomerado | Zarate'
        ,'55969' => 'CD AMBA | Zarate'*/
        '44429' => 'Planta Celulosa | Puerto Esperanza'
        ,'44423' => 'Aserradero/Planta MDF | Puerto Piray'
        ,'44424' => 'Bossetti'
        ,'78748' => 'Planta Aglomerado'
        ,'44430' => 'CD AMBA'
        ,'all' => 'Todos'
    );    

    $solapa = isset($_REQUEST['solapa']) ? (isset($solapaValues[$_REQUEST['solapa']]) ? $_REQUEST['solapa'] : null ) : null;
    $solapa = empty($solapa) ? array_keys($solapaValues)[0] : $solapa;

    require_once 'clases/clsViajes.php';
	require_once 'clases/clsViajesDelivery.php';
	$objViaje = new ViajesDelivery($objSQLServer);
    
    $filtros['partidas'] = ($solapa == 'partidas')?1:0; //--Si estamos en "Partidas", el valor es 1, si estamos en "Arribos" el valor es 0
    $filtros['destinos'] = 0; //--Valor 0 para visualización "a Planta"
    $filtros['operacion'] = $_SESSION['idAgente'];
    $filtros['transportista'] = 'NULL';
	$filtros['movil'] = 'NULL';
    $filtros['referencia'] = ($solapa == 'all') ? 'NULL' : $solapa;
    $filtros['pendiente'] = -1; //Si quiero ver los casos en que hubo un ingreso o egreso el valor es 1. -1 no aplica filtro.
    $filtros['estado'] = -1;//Para ver lo que esta "En tiempo" el valor es 0, para ver lo que esta "Demorado" el valor es 1. -1 no aplica filtro.
    $filtros['playa'] = 1;
    $arrViajes = $objViaje->getProcedureArribesPartidas($filtros); 
    if($arrViajes){
		foreach($arrViajes as $k => $row){
			$objFiltroCol->value($col['transportista'],$row['transportista'],$row['id_transportista']);
			$objFiltroCol->value($col['movil'],$row['vi_movil'],$row['id_movil']);
		}
		$objFiltroCol->value($col['arribo'],'(Ver todos)',-1,true);
        $objFiltroCol->value($col['arribo'],$lang->system->ingreso_realizado,5,true);
		$objFiltroCol->value($col['arribo'],$lang->system->ingreso_pendiente,6,true);
		
		if(!$filtroCol){
			foreach($col as $item){
				unset($_POST[$item]);
			}
		}
		
		if($objFiltroCol->validar()){ 
			$filtros['transportista'] = $_POST[$col['transportista']]?"'".implode(',',$_POST[$col['transportista']])."'":'NULL';
			$filtros['movil'] = $_POST[$col['movil']]?"'".implode(',',$_POST[$col['movil']])."'":'NULL';
			
            $auxFilter = NULL;
            //$filtros['arribo'] = $_POST[$col['arribo']]?implode(',',$_POST[$col['arribo']]):NULL;
            if(count($_POST[$col['arribo']]) == 1){
                $auxFilter = $_POST[$col['arribo']][0];
            }
            switch($auxFilter){
               case 5: //Realizado
                    $filtros['pendiente'] = 0;
                break;
                case 6: //Pendiente
                    $filtros['pendiente'] = 1;
                break;
            }
            $arrViajes = $objViaje->getProcedureArribesPartidas($filtros);;
        }
    }
    
    $extraCSS[]='css/abmViajes.css';
	$extraCSS[]='css/estilosPopup.css';
    $extraJS[] ='js/abmViajes.js';
    $extraJS[] ='js/popupHostFunciones.js';
	$extraJS[] = 'js/filtrosCol.js';
    $extraJS[] = 'js/abmViajesDeliveryFunciones.js';
    $extraJS[] = 'js/abmViajesDeliveryPlantaVirtualFunciones.js';    
    $extraJS[] = 'js/abmViajesArribosPartidas.js';
	
	include_once('includes/template.php');
	$objFiltroCol->aplicar();
}

function filtrarCol($objSQLServer, $seccion){
	index($objSQLServer, $seccion, '', true);
}

function popupSendMessage($objSQLServer, $seccion){
    $idusuario = $_SESSION['idUsuario'];
    $idviaje = $_GET['idviaje'];
    $strQuery = "EXEC db_envio_push_notification_viaje {$idusuario},{$idviaje}";
    $results = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strQuery));

    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';
    $extraJS[] = 'js/abmViajesDeliveryPlantaVirtualFunciones.js';
    
    $vista = 'sendmessage';
    require("includes/frametemplate.php");
}

function popupViewHistory($objSQLServer, $seccion){
    $idusuario = $_SESSION['idUsuario'];
    $idviaje = $_GET['idviaje'];
    $strQuery = "EXEC db_envio_push_notification_viaje_historial {$idusuario},{$idviaje}";
    $results = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strQuery));

    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';

    $vista = 'viewhistory';    
    require("includes/frametemplate.php");
}

function popupSendSMS($objSQLServer, $seccion){
    include_once 'clases/class.curl_url.php';
	$objCurl = new curl_url();
    
    $msg = $_POST['hidMessage'].(!empty($_POST['hidAdicional']) ? ' - '.$_POST['hidAdicional'] : '');
    $datos = array(
        'number' => $_POST['hidNumber']  
        //'number' => '+541169551010'  
        ,'message' => $msg
        ,'title' => $_POST['hidTitle']
        ,'url' => !empty($_POST['hidPath']) ? $_POST['hidPath'] : '--'
        ,'type' => 1
    );

    $path = "https://www.localizar-t.com:81/pod/push/".http_build_query($datos);
    $header = array('Content-type: multipart/form-data', 'application/x-www-form-urlencoded');
    $datos = NULL;

    $response = $objCurl->get($path,$datos,$header);
    $status = false;
    if($response['transaction_status'] == 'ok'){
        $status = true;

        //--Se registra SMS enviado
        $idusuario = $_SESSION['idUsuario'];
        $idviaje = intval($_POST['hidIdViaje']);
        $params = array(
            'vme_mensaje' => $msg
            ,'vme_us_id' => $idusuario
            ,'vme_vi_id' => $idviaje 
        );
        $objSQLServer->dbQueryInsert($params, 'tbl_viajes_mensajes_enviados');
        //--
    }
    $message = decode($response['message']);


    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';

    $vista = 'sendtosms';    
    require("includes/frametemplate.php");
}

function popupViewInfoAditional($objSQLServer, $seccion){
    $idusuario = $_SESSION['idUsuario'];
    $idviaje = $_GET['idviaje'];
    $strQuery = "EXEC db_info_adicional_viaje {$idusuario},{$idviaje}";
    $results = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strQuery));

    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';

    $vista = 'viewinfoaditional';    
    require("includes/frametemplate.php");
}

?>