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
    elseif($_GET['action'] == 'attachfile'){
        popupAttachFile($objSQLServer, $seccion);
        exit;
    }
    elseif($_GET['action'] == 'authorize_entry'){
        popupAuthorizeEntry($objSQLServer, $seccion);
        exit;
    }
    
	$idUsuario = (int)$_SESSION['idUsuario'];
	
    $col['transportista'] = 'colidTransportista';
	$col['movil'] = 'colidMovil';
	$col['referencia'] = 'colidReferencia';
	$col['arribo'] = 'colArribos';
    $col['partida'] = 'colPartidas';
    $col['numerodt'] = 'colNumerodt';
	require_once 'clases/clsFiltrosCol.php';
    $objFiltroCol = new FiltrosCol($col);

    $solapaValues = array(
        /*'44429' => 'Planta Celulosa | Puerto Esperanza'
        ,'44423' => 'Aserradero/Planta MDF | Puerto Piray'
        ,'44424-63513' => 'Centro Log&iacute;stico Bossetti'
	    ,'44430' => 'Planta Aglomerado | Zarate'
        ,'55964' => 'Planta Aglomerado | Zarate'
        ,'55969' => 'CD AMBA | Zarate'*/

	/*'44423-55967' => 'Aserradero/Planta MDF|Puerto Piray'

        ,'44429' => 'Planta Celulosa|Puerto Esperanza'*/
        
        '44423' => 'Aserradero/Planta MDF|Puerto  Piray'
	,'64302' => 'CD Esperanza-APC1'
	,'44424'=>'Centro Logistico Bossetti'
	,'78748'=>'Planta Aglomerado|Zarate'
	,'44429'=>'Planta Celulosa|Puerto Esperanza'
	,'64301'=>'Planta MDF-Puerto Piray'
	,'83941'=>'Trafico APSA Tab Zte'
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
    $filtros['transportista'] = $_SESSION['idEmpresa'];
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
            $arrViajes = $objViaje->getProcedureArribesPartidas($filtros);
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
        ,'message' => str_replace('/','',$msg)
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

function popupAttachFile($objSQLServer, $seccion){
    $idusuario = $_SESSION['idUsuario'];
    $idviaje = $_GET['idviaje'];
    
    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';

    $vista = 'attachfile';    
    require("includes/frametemplate.php");
}


function popupPostAttachFile($objSQLServer, $seccion){
    $valid_files = array('pdf','png','jpg','jpeg','bmp','xls','xlsx','doc','docx','gif','mp4');

    $file = $_FILES['attach']['name'];
	if($file){
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		if(in_array($ext,$valid_files)){			
			$filename = 'images/proof_delivery/'.md5($_POST['idviaje'].'_'.$_POST['iddestino'].'_attach_front_').'.'.$ext;
            $filename_url = '../dashboard/viajes/'.$filename; 
            if(!is_file($filename_url) && !existeArchivo($_POST['idviaje'], $_POST['iddestino'])){
				if(copy($_FILES['attach']['tmp_name'], $filename_url)){				
                    
                    $params = array(
                        'vdoc_vi_id' => $_POST['idviaje']
                        ,'vdoc_re_id' => $_POST['iddestino']
                        ,'vdoc_clave' => 'playavirtual'
                        ,'vdoc_archivo' => str_replace('../','',$filename_url)
                    );
                    $objSQLServer->dbQueryInsert($params, 'tbl_viajes_documentos');
                    
                    require_once 'clases/clsAbms.php';
                    $objAbm = new Abm($objSQLServer, null, null);

                    $log = 'Ver archivo adjunto <a href="dashboard/'.$filename.'" target="_blank">aqui</a>';
                    $objAbm->generarLog(1,$_POST['idviaje'],$log);

                    $status = true;
                    $message = 'El archivo se adjunto correctamente.';
                    chown($filename_url, 'root');
                }
                else{
                    $status = false;
                    $message = 'El archivo no pudo ser cargado';
                }
            }
            else{
                $status = false;
                $message = 'El archivo ya existe';
           }
		}
    }
    else{
        $status = false;
        $message = 'Debe Selecionar un archivo';
    }
    
    $_GET['idviaje'] = $_POST['idviaje'];
    $_GET['iddestino'] = $_POST['iddestino'];

    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';

    $vista = 'attachfile';    
    require("includes/frametemplate.php");
}

function existeArchivo($idviaje, $idref, $returnFile = false){
    global $objSQLServer;

    $query = "SELECT ".($returnFile ? 'vdoc_archivo' : 'COUNT(*) as cant ')
            . " FROM tbl_viajes_documentos WHERE vdoc_vi_id = {$idviaje} AND vdoc_re_id = {$idref} AND vdoc_clave = 'playavirtual'";
    $cant = $objSQLServer->dbGetRow($objSQLServer->dbQuery($query),0, 3);
    if($returnFile){
        return $cant['vdoc_archivo'];
    }
    elseif($cant['cant']){
        return true;
    }
    else{
        return false;
    }
}

function popupAuthorizeEntry($objSQLServer, $seccion){
    $idusuario = $_SESSION['idUsuario'];
    $idviaje = $_GET['idviaje'];
    
    $popup = true;

    $strQuery = "EXEC db_playa_virtual_mensajes {$idusuario},{$idviaje}";
    $pr_playa_virtual_mensajes = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strQuery));
    
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';

    $vista = 'authorize_entry';    
    require("includes/frametemplate.php");
}

function popupPostAuthorizeEntry($objSQLServer, $seccion){
    $idusuario = $_SESSION['idUsuario'];
    $idviaje = (int)$_POST['idviaje'];

    $params = array(
        'vme_mensaje' => $_POST['autorizar_ingreso']
        //,'vme_fecha_Envio' => $_POST['iddestino']
        ,'vme_us_id' => $idusuario
        ,'vme_vi_id' => $idviaje
        //,'vme_leido' => 0
        ,'vme_vd_id' => (int)$_POST['iddestino']
    );

    if($objSQLServer->dbQueryInsert($params, 'tbl_viajes_mensajes_enviados')){
        $status = true;
        $message = 'Autorización enviada.';
    }
    else{
        $status = false;
        $message = 'La autorización no pudo ser enviada.';

        $strQuery = "EXEC db_playa_virtual_mensajes {$idusuario},{$idviaje}";
        $pr_playa_virtual_mensajes = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strQuery));
    }
                    
    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';

    $vista = 'authorize_entry';    
    require("includes/frametemplate.php");
}
?>