<?php
function index($objSQLServer, $seccion, $mensaje="", $filtroCol = false){
	
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	if ($action == 'proof_delivery'){
        proof_delivery($objSQLServer, $seccion);
		exit;
    }
	
	if($_GET['idViaje']){
		verDetalle($objSQLServer, $seccion, "", (int)$_GET['idViaje']);
		exit;	
	}
	
	global $lang;
	$idUsuario = (int)$_SESSION['idUsuario'];
	
	require_once 'clases/clsViajes.php';
	require_once 'clases/clsViajesDelivery.php';
	$objViaje = new ViajesDelivery($objSQLServer);

	$col['transportista'] = 'colidTransportista';
	$col['movil'] = 'colidMovil';
	$col['referencia'] = 'colidReferencia';
	$col['iniReal'] = 'colIniReal';
	$col['finReal'] = 'colFinReal';
	$col['pod'] = 'colPod';

	if($_POST){
		$_SESSION['auxFilter'] = array();
		if(!isset($_POST[$col['transportista'].'CheckAll'])){
			$_SESSION['auxFilter'][$col['transportista']] = implode(',',$_POST[$col['transportista']]);
		}
		if(!isset($_POST[$col['movil'].'CheckAll'])){
			$_SESSION['auxFilter'][$col['movil']] = implode(',',$_POST[$col['movil']]);
		}
		if(!isset($_POST[$col['referencia'].'CheckAll'])){
			$_SESSION['auxFilter'][$col['referencia']] = implode(',',$_POST[$col['referencia']]);
		}
		if(!isset($_POST[$col['iniReal'].'CheckAll'])){
			$_SESSION['auxFilter'][$col['iniReal']] = implode(',',$_POST[$col['iniReal']]);
		}
		if(!isset($_POST[$col['finReal'].'CheckAll'])){
			$_SESSION['auxFilter'][$col['finReal']] = implode(',',$_POST[$col['finReal']]);
		}
		if(!isset($_POST[$col['pod'].'CheckAll'])){
			$_SESSION['auxFilter'][$col['pod']] = implode(',',$_POST[$col['pod']]);
		}
	}
	else{
		$filtroCol = true;
		$_POST[$col['transportista']] = !empty($_SESSION['auxFilter'][$col['transportista']])?explode(',',$_SESSION['auxFilter'][$col['transportista']]):NULL;
		$_POST[$col['movil']] = !empty($_SESSION['auxFilter'][$col['movil']])?explode(',',$_SESSION['auxFilter'][$col['movil']]):NULL;
		$_POST[$col['referencia']] = !empty($_SESSION['auxFilter'][$col['referencia']])?explode(',',$_SESSION['auxFilter'][$col['referencia']]):NULL;
		$_POST[$col['iniReal']] = !empty($_SESSION['auxFilter'][$col['iniReal']])?explode(',',$_SESSION['auxFilter'][$col['iniReal']]):NULL;
		$_POST[$col['finReal']] = !empty($_SESSION['auxFilter'][$col['finReal']])?explode(',',$_SESSION['auxFilter'][$col['finReal']]):NULL;
		$_POST[$col['pod']] = !empty($_SESSION['auxFilter'][$col['pod']])?explode(',',$_SESSION['auxFilter'][$col['pod']]):NULL;
		foreach($col as $item){
			$filtroCol = count($_POST[$item])?true:$filtroCol;
		}
	}

	require_once 'clases/clsFiltrosCol.php';
	$objFiltroCol = new FiltrosCol($col);
	
	$filtros = array();	
	if(!empty($_POST['buscar'])){
		$filtros['buscar'] = $_POST['buscar'];	
		unset($_POST['fdesde']);
		unset($_POST['fhasta']);
	}
	else{
		$borroFechaHasta = false;
		if($_POST){
			if($_POST['fdesde']){
				setcookie('filtro_desde', $_POST['fdesde'], time()+3600);
			}
			else{
				setcookie('filtro_desde', "", time()-3600); 
			}
			
			if(isset($_POST['fhasta'])){
				if($_POST['fhasta']){
					setcookie('filtro_hasta', $_POST['fhasta'], time()+3600);
				}
				else{
					setcookie('filtro_hasta', "", time()-3600); 
					$borroFechaHasta = true;
				}
			}
		}

		$_POST['fdesde'] = $_POST['fdesde']?$_POST['fdesde']:($_COOKIE['filtro_desde']?$_COOKIE['filtro_desde']:getFechaServer('d-m-Y'));
		//$_POST['fhasta'] = $_POST['fhasta']?$_POST['fhasta']:($borroFechaHasta?NULL:($_COOKIE['filtro_hasta']?$_COOKIE['filtro_hasta']:NULL));
		$_POST['fhasta'] = $_POST['fhasta']?$_POST['fhasta']:($borroFechaHasta?NULL:($_COOKIE['filtro_hasta']?$_COOKIE['filtro_hasta']:getFechaServer('d-m-Y')));
		
		if(!empty($_POST['fdesde'])){
			$filtros['f_ini'] = date('Y-m-d',strtotime($_POST['fdesde']));	
		}
		if(!empty($_POST['fhasta'])){
			$filtros['f_fin'] = date('Y-m-d',strtotime($_POST['fhasta']));	
		}
		else{
			$filtros['f_fin'] = $filtros['f_ini'];	
		}
	}
	
	##-- ini.GRAFICOS --##
	$en_tiempo = (string)$lang->system->en_tiempo;
	$atrasado = (string)$lang->system->atrasado;
	$grafico1 = array();## GRAFICO 1 -- Ingresos a tiempo vs Atrasados ##
	$grafico1[$en_tiempo]['valor'] = 0;
	$grafico1[$atrasado]['valor'] = 0;	
	
	$grafico2 = array();## GRAFICO 2 -- Ingresos a tiempo vs Atrasados por transportista ##
	
	$rangos = array((string)$lang->system->rango_menos_1,(string)$lang->system->rango_entre_1_y_2,(string)$lang->system->rango_entre_2_y_4,(string)$lang->system->rango_entre_4_y_24,(string)$lang->system->rango_mas_24);
	$grafico3 = array();## GRAFICO 3 -- Estadías ##
	$grafico3[$rangos[0]]['valor'] = 0;
	$grafico3[$rangos[1]]['valor'] = 0;	
	$grafico3[$rangos[2]]['valor'] = 0;	
	$grafico3[$rangos[3]]['valor'] = 0;	
	$grafico3[$rangos[4]]['valor'] = 0;	
	
	$grafico4 = array();## GRAFICO 4 -- Ingresos a tiempo vs Atrasados por conductor ##

	$sin_pod = 'Viajes sin POD';
	$con_pod = 'Viajes con POD';
	$grafico5 = array();## GRAFICO 5 -- Confirmación de entregas ##
	$grafico5[$con_pod]['valor'] = 0;	
	$grafico5[$sin_pod]['valor'] = 0;
	##-- fin.GRAFICOS --##
	
	##-- ini.ESTADISTICAS --##
	$stats['en_origen'] = array('detectados'=>0, 'total'=>0, 'promedio'=>0);
	$stats['en_destino'] = array('detectados'=>0, 'total'=>0, 'promedio'=>0);
	$stats['moviles'] = array('reportando'=>0, 'total'=>0, 'promedio'=>0);
	$auxViaje = NULL;
	##-- fin.ESTADISTICAS --##
	
	$arrViajes = $objViaje->getListadoViajesDelivery($filtros);
	if($arrViajes){
        $auxStatsMoviles = array();
		foreach($arrViajes as $k => $row){
			
			##--ini.FILTROS --##
			$objFiltroCol->value($col['movil'],$row['vi_movil'],$row['id_movil']);
			$objFiltroCol->value($col['referencia'],$row['re_nombre'],$row['re_id']);
			$objFiltroCol->value($col['transportista'],$row['transportista'],$row['vi_transportista']);
			##--fin.FILTROS --##
		
		
			##-- ini.GRAFICOS --##
			if(trim($row['fecha_ini_real']) == true && trim($row['fecha_ini']) == true){
				$diffIng = (strtotime($row['fecha_ini_real']) - strtotime($row['fecha_ini']));
				if($diffIng > 0){
					$grafico1[$atrasado]['valor'] = (int)$grafico1[$atrasado]['valor'] + 1;
					$grafico2[$row['transportista']]['atrasado'] = (int)$grafico2[$row['transportista']]['atrasado'] + 1;
					$grafico4[$row['co_conductor']]['atrasado'] = (int)$grafico4[$row['co_conductor']]['atrasado'] + 1;
				}
				elseif($diffIng <= 0  && trim($row['fecha_ini_real']) == true){
					$grafico1[$en_tiempo]['valor'] = (int)$grafico1[$en_tiempo]['valor'] + 1;
					$grafico2[$row['transportista']]['en_tiempo'] = (int)$grafico2[$row['transportista']]['en_tiempo'] + 1;
					$grafico4[$row['co_conductor']]['en_tiempo'] = (int)$grafico4[$row['co_conductor']]['en_tiempo'] + 1;
				}
			}
			
			if(trim($row['fecha_ini_real']) == true && trim($row['fecha_fin_real']) == true){
				$hs = (((strtotime($row['fecha_fin_real']) - strtotime($row['fecha_ini_real']))/60)/60);
				if($hs < 1){
					$grafico3[$rangos[0]]['valor'] = (int)$grafico3[$rangos[0]]['valor']+1;	
				}
				elseif($hs >= 1 && $hs < 2){
					$grafico3[$rangos[1]]['valor'] = (int)$grafico3[$rangos[1]]['valor']+1;	
				}
				elseif($hs >= 2 && $hs < 4){
					$grafico3[$rangos[2]]['valor'] = (int)$grafico3[$rangos[2]]['valor']+1;	
				}
				elseif($hs >= 4 && $hs < 24){
					$grafico3[$rangos[3]]['valor'] = (int)$grafico3[$rangos[3]]['valor']+1;	
				}
				else{
					$grafico3[$rangos[4]]['valor'] = (int)$grafico3[$rangos[4]]['valor']+1;	
				}
			}
			
			if($_SESSION['idAgente'] == 4835){
				if($row['vd_estado'] === 1){
					$grafico5[$con_pod]['valor']++;
				}
				else{
					$grafico5[$sin_pod]['valor']++;
				}
			}
			##-- fin.GRAFICOS --##
			
			##-- ini.ESTADISTICAS --##
			if(!tienePerfil(array(8,12))){
				if($row['vd_orden'] == 0){
					$stats['en_origen']['total']++;	
					if(trim($row['fecha_ini_real']) == true){
						$stats['en_origen']['detectados']++;	
					}
				}
				elseif($row['vd_orden'] > 0){
					$stats['en_destino']['total']++;	
					if(trim($row['fecha_ini_real']) == true){
						$stats['en_destino']['detectados']++;	
					}
				}
				
                                if(!in_array($row['id_movil'],$auxStatsMoviles) && !empty($row['id_movil'])){
                                    $stats['moviles']['total']++;	
                                    array_push($auxStatsMoviles,$row['id_movil']);
                                    
                                    if($row['sh_rd_id'] != 75 && $row['sh_rd_id'] != 76){ //echo "==>reporta";
                                        $stats['moviles']['reportando']++;	
                                    }
				}
			}
			##-- fin.ESTADISTICAS --##
		}
                
		##--ini.FILTROS --##
		$objFiltroCol->value($col['iniReal'],$lang->system->ingreso_realizado,1);
		$objFiltroCol->value($col['iniReal'],$lang->system->ingreso_pendiente,2);
		$objFiltroCol->value($col['finReal'],$lang->system->egreso_realizado,1);
		$objFiltroCol->value($col['finReal'],$lang->system->egreso_pendiente,2);
		$objFiltroCol->value($col['pod'],'Con entrega confirmada',1);
		$objFiltroCol->value($col['pod'],'Sin entrega confirmada',2);
		
		if(!$filtroCol){
			foreach($col as $item){
				unset($_POST[$item]);
			}
		}
		
		if($objFiltroCol->validar()){ 
			$filtros['transportista'] = $_POST[$col['transportista']]?implode(',',$_POST[$col['transportista']]):NULL;
			$filtros['movil'] = $_POST[$col['movil']]?implode(',',$_POST[$col['movil']]):NULL;
			$filtros['referencia'] = $_POST[$col['referencia']]?implode(',',$_POST[$col['referencia']]):NULL;
			$filtros['iniReal'] = $_POST[$col['iniReal']]?implode(',',$_POST[$col['iniReal']]):NULL;
			$filtros['finReal'] = $_POST[$col['finReal']]?implode(',',$_POST[$col['finReal']]):NULL;
			$filtros['pod'] = $_POST[$col['pod']]?implode(',',$_POST[$col['pod']]):NULL;
			$arrViajes = $objViaje->getListadoViajesDelivery($filtros);
		}
		##--fin.FILTROS --##
		
		##-- ini.GRAFICOS --##
		if(count($grafico1) > 0 && ($grafico1[$en_tiempo]['valor'] > 0 || $grafico1[$atrasado]['valor'] > 0)){
			$data1 = "['".$lang->system->estado."', '".$lang->system->valores."']";
			foreach($grafico1 as $k => $item){
				$data1.= ",['".encode($k)."',".(int)$item['valor']."]";
			}
		}
					
		if(count($grafico2) > 0 && (!$filtros['transportista'] && !tienePerfil(array(8,12)))){
			$data2 = "['".$lang->system->tiempo."','".$en_tiempo."','".$atrasado."']";
			foreach($grafico2 as $k => $item){
				$data2.= ",['".encode($k)."',".(int)$item['en_tiempo'].",".(int)$item['atrasado']."]";
			}
		}
		
		if(count($grafico3) > 0 && 
				($grafico3[$rangos[0]]['valor'] > 0 
					|| $grafico3[$rangos[1]]['valor'] > 0
					|| $grafico3[$rangos[2]]['valor'] > 0
					|| $grafico3[$rangos[3]]['valor'] > 0
					|| $grafico3[$rangos[4]]['valor'] > 0)
		){
			$data3 = "['".$lang->system->estadia_hora."','".$lang->system->cant_viajes."']";
			foreach($grafico3 as $k => $item){
				$data3.= ",['".encode($k)."',".(int)$item['valor']."]";
			}
		}
		
		if(count($grafico4) > 0 && ($filtros['transportista'] || tienePerfil(array(8,12)))){
			$data4 = "['".$lang->system->tiempo."','".$en_tiempo."','".$atrasado."']";
			foreach($grafico4 as $k => $item){
				$data4.= ",['".encode($k)."',".(int)$item['en_tiempo'].",".(int)$item['atrasado']."]";
			}
		}

		if(count($grafico5) > 0 && ($grafico5[$sin_pod]['valor'] > 0 || $grafico5[$con_pod]['valor'] > 0)){
			$data5 = "['".$con_pod."','".$sin_pod."']";
			foreach($grafico5 as $k => $item){
				$data5.= ",['".encode($k)."',".(int)$item['valor']."]";
			}
		}
		##-- fin.GRAFICOS --##
		
		##-- ini.ESTADISTICAS --##
		if(!tienePerfil(array(8,12))){
			$stats['en_origen']['promedio'] = round(($stats['en_origen']['detectados']/$stats['en_origen']['total'])*100);
			$stats['en_destino']['promedio'] = round(($stats['en_destino']['detectados']/$stats['en_destino']['total'])*100);
			$stats['moviles']['promedio'] = round(($stats['moviles']['reportando']/$stats['moviles']['total'])*100);
		}
		##-- fin.ESTADISTICAS --##
	}
	
	//-- VALIDACION PARA HABILITAR BOTON DE COTEJAR VIAJES HISTORICOS--//
	global $objPerfil;
	$procesarViajes = false;
	if($objPerfil->validarSeccion('abmViajesDeliveryAltaMasiva')){
		$procesarViajes = true;
		$arrProcesarViajes = $objViaje->getProcesarViajes();
	}
	//-- --//

	//--Ini. Visualización de SMS recibidos
	//$arrViajes = $objViaje->getSMSRecibido($arrViajes);
	//--Fin.
	
	
	$extraJS[] = 'js/abmViajes.js';
	$extraCSS[]='css/abmViajes.css';
	$extraCSS[] = 'css/ui/jquery.ui.datepicker.css';
	$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$extraJS[] = 'js/jquery/jquery.datepicker.js';
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] = 'js/popupHostFunciones.js';
        
        //$extraJS[] = 'js/jquery/colorbox/jquery.colorbox-min.js';
        //$extraCSS[] = 'js/jquery/colorbox/colorbox.css';
        
	$extraJS[] = 'js/filtrosCol.js';
	
	$operacion = 'listar';
	include_once('includes/template.php');
	$objFiltroCol->aplicar();
}

function filtrarCol($objSQLServer, $seccion){
   index($objSQLServer, $seccion, '', true);
}


function verDetalle($objSQLServer, $seccion, $mensaje = NULL, $idViaje = false){
	$operacion = 'verDetalle';
	$id_viaje = (int)$_POST['hidId']?(int)$_POST['hidId']:(int)$idViaje;
	$_POST['hidId'] = $id_viaje;
	
	global $objSeguridad;
	if(!$objSeguridad->validar($id_viaje)){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: boot.php?c=".$seccion); 
		exit;
	}

	require_once 'clases/clsViajes.php';
    require_once 'clases/clsViajesDelivery.php';
	$objViaje = new ViajesDelivery($objSQLServer, $id_viaje);
	
	$datosViaje = $objViaje->getDatosViajeDelivery();
	$arrViaje = $objViaje->getDestinosDelivery();	
	
	$historial = $objViaje->getHistorial();
	$instancias = $objViaje->getInstanciaViaje();
	
	$distViaje = $objViaje->getDistanciaViaje(); 
	
	if($arrViaje[0]['vi_finalizado']){
		$tiempoViaje = $objViaje->getTiempoViaje();
	}
	
	//--Ini. Visualización de SMS recibidos
	//$arrViaje = $objViaje->getSMSRecibido($arrViaje);
	$POD = $objViaje->getPOD();
	//--Fin.

	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] ='js/popupHostFunciones.js';
	
	$extraJS[] = 'js/abmViajes.js';
	$extraCSS[] = 'css/estilosAbmViajes.css';
	$extraCSS[] = 'css/abmViajes.css';
	require("includes/template.php");
}

function guardarObservaciones($objSQLServer, $seccion){
	global $lang;
	
	$operacion = $_POST['hidOperacion'];
	$id_viaje = (int)$_POST['hidId'];

	global $objSeguridad;

	if(!$objSeguridad->validar($id_viaje)){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: boot.php?c=".$seccion); 
		exit;
	}
	
	require_once 'clases/clsViajes.php';
    require_once 'clases/clsViajesDelivery.php';
	$objViaje = new ViajesDelivery($objSQLServer, $id_viaje);
	
	$observaciones = !empty($_POST['observaciones'])?"'".$_POST['observaciones']."'":NULL;
	$observaciones_2 = !empty($_POST['observaciones_2'])?"'".$_POST['observaciones_2']."'":NULL;
	$campos = array('vi_observaciones','vi_observaciones_2','vi_finalizado');
	$valorCampos = array($observaciones,$observaciones_2,(int)$_POST['vi_finalizado']);
	
	//-- Ini. Verificar si hay q realizar envio de mail --//
	$enviarMail = false;
	$arrViaje = $objViaje->getViajes();
	$arrViaje = $arrViaje[0];
	if(trim($_POST['observaciones']) != trim($arrViaje['vi_observaciones']) && !empty($observaciones)){
		$enviarMail = true;
	}
	//-- Fin. --//
	
	if($objViaje->updateViajes($campos,$valorCampos)){
		$mensaje = $lang->message->ok->msj_modificar;	
	
		if($enviarMail){
			//-- Enviar Mail al Transportista Notificando cambio en las observaciones --//
			$arrTransportista = $objViaje->getTransportistaViaje();
			$arrEnviarMail = array();
			if($arrTransportista){
				foreach($arrTransportista as $item){
					if(!empty($item['cl_email'])){
						array_push($arrEnviarMail,$item['cl_email']);	
					}	
				}	
			}
			
			if(count($arrEnviarMail)){
				$objIdioma = new Idioma();
				$idioma = !empty($_SESSION['idioma'])?$_SESSION['idioma']:getIdiomaBrowser();
				$langEmail = $objIdioma->getEmails($idioma);
					
				$cuerpo_mail = $langEmail->novedades_delivery->data;
				$cuerpo_mail = str_replace('[VIAJE]',$lang->system->viaje, $cuerpo_mail);
				$cuerpo_mail = str_replace('[COD_VIAJE]',$arrViaje['vi_codigo'], $cuerpo_mail);
				$cuerpo_mail = str_replace('[NOVEDAD]',$_POST['observaciones'], $cuerpo_mail);
				$cuerpo_mail = idiomaHTML($cuerpo_mail);
				
				$subject_mail = $langEmail->novedades_delivery->subject;
				$subject_mail = str_replace('[VIAJE]',$lang->system->viaje, $subject_mail);
				$subject_mail = str_replace('[COD_VIAJE]',$arrViaje['vi_codigo'], $subject_mail);
				
				//-- --//
				require ('clases/clsEmailer.php');
				$objEmailer = new Emailer($objSQLServer);
					
				$emailerMsg['asunto'] = $subject_mail;
				$emailerMsg['contenido'] = decode($objEmailer->getContenidoHTML($cuerpo_mail));
				$id_contenido = $objEmailer->setContenidoEmailer($emailerMsg);
				
				if($id_contenido){
					foreach($arrEnviarMail as $cc){
						$emailerInfo['id_contenido'] = $id_contenido;
						$emailerInfo['id_usuario'] = NULL;
						$emailerInfo['remitente_mail'] = NULL;
						$emailerInfo['remitente_name'] = NULL;
						$emailerInfo['responder_a'] = NULL;
						$emailerInfo['destinatario_mail'] = $cc;
						$emailerInfo['destinatario_name'] = NULL;
						$emailerInfo['prioridad'] = 5;	
						$objEmailer->setInfoEmailer($emailerInfo);
					}
				}
				//-- --//
			}
		}
		//-- --//
	}
	else{
		$mensaje = $lang->message->error->msj_modificar;	
	}
	verDetalle($objSQLServer, $seccion, $mensaje);
}

function modificarInstancia($objSQLServer, $seccion){
	$id_viaje = (int)$_POST['hidId'];

	global $objSeguridad;
	if(!$objSeguridad->validar($id_viaje)){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: boot.php?c=".$seccion); 
		exit;
	}
	
	$return = 'detalle';	
	if(isset($_POST['viaje_instancia'])){
		$return = 'listado_viajes';
		$paso_instancia = (int)$_POST['viaje_instancia'];
	}
	else{
		$paso_instancia = (int)$_POST['paso_instancia'];
	}
	
	require_once 'clases/clsViajes.php';
    require_once 'clases/clsViajesDelivery.php';
	$objViaje = new ViajesDelivery($objSQLServer, $id_viaje);
	
	$objViaje->setInstanciaViaje($id_viaje, $paso_instancia);
	
	if($return == 'listado_viajes'){
		index($objSQLServer, $seccion);
	}
	else{
		verDetalle($objSQLServer, $seccion);
	}
}

function volver(){
	header('Location: boot.php?c=abmViajesDelivery');
}

function proof_delivery($objSQLServer, $seccion){
	global $lang;
	$extraCSS[] = 'css/estilosAbmPopup.css';
    $extraCSS[] = 'css/popup.css';
    $extraJS[] = 'js/popupFunciones.js?1';
	$extraJS[] = 'js/jquery.blockUI.js';
    $tipoBotonera = 'A';
	$operacion = 'proof_delivery';
    require("includes/frametemplate.php");
}

function export_delivery($objSQLServer){
	global $lang;
	
	require_once 'clases/clsViajes.php';
    require_once 'clases/clsViajesDelivery.php';
	$objViaje = new ViajesDelivery($objSQLServer, $id_viaje);
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	if(!empty($_POST['buscar'])){
		$filtros['buscar'] = $_POST['buscar'];	
		unset($_POST['fdesde']);
		unset($_POST['fhasta']);
	}
	else{
		$_POST['fdesde'] = $_POST['fdesde']?$_POST['fdesde']:($_COOKIE['filtro_desde']?$_COOKIE['filtro_desde']:getFechaServer('d-m-Y'));
		//$_POST['fhasta'] = $_POST['fhasta']?$_POST['fhasta']:NULL;
		
		if(!empty($_POST['fdesde'])){
			$filtros['f_ini'] = date('Y-m-d',strtotime($_POST['fdesde']));	
		}
		if(!empty($_POST['fhasta'])){
			$filtros['f_fin'] = date('Y-m-d',strtotime($_POST['fhasta']));	
		}
	}
	
	$arrViajes = $objViaje->getListadoViajesDeliveryExportar($filtros);
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle('Viajes_Delivery')//$lang->botonera->exportar_estadias
		->setSubject('Viajes_Delivery')
		->setDescription('Viajes_Delivery')
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
		
	switch($_SESSION['idAgente']){
		case 4835:
			export_xls_arauco($objPHPExcel, $objViaje, $arrViajes);
		break;
		default:
			export_xls_default($objPHPExcel, $objViaje, $arrViajes);
		break;		
	}
		
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="viajes-delivery-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}

function export_xls_default($objPHPExcel, $objViaje, $arrViajes){
	global $lang;
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $lang->system->nro_orden)
		->setCellValue('B1', $lang->system->transportista)
		->setCellValue('C1', $lang->system->categoria)
		->setCellValue('D1', 'GBA')
		->setCellValue('E1', $lang->system->delivery)
		->setCellValue('F1', $lang->system->matricula)
		->setCellValue('G1', $lang->system->origen)
		->setCellValue('H1', 'Inicio de preparación')
		->setCellValue('I1', 'Fin preparación')
		->setCellValue('J1', 'Fin de carga')
		->setCellValue('K1', 'Entrega de documentos')
		->setCellValue('L1', $lang->system->ingreso_prog_origen)
		->setCellValue('M1', $lang->system->ingreso_real_origen)
		->setCellValue('N1', $lang->system->egreso_prog_origen)
		->setCellValue('O1', $lang->system->egreso_real_origen)
		->setCellValue('P1', $lang->system->status_origen)
		->setCellValue('Q1', $lang->system->estadia_origen)
		->setCellValue('R1', 'Sobreestadía en Origen')
		->setCellValue('S1', $lang->system->destino)
		->setCellValue('T1', $lang->system->ingreso_prog_destino)
		->setCellValue('U1', $lang->system->ingreso_real_destino)
		->setCellValue('V1', $lang->system->egreso_prog_destino)
		->setCellValue('W1', $lang->system->egreso_real_destino)
		->setCellValue('X1', $lang->system->status_destino)
		->setCellValue('Y1', $lang->system->estadia_destino)
		->setCellValue('Z1', 'Sobreestadía en Destino')
		->setCellValue('AA1', 'Cod. Cliente')
		->setCellValue('AB1', 'Volumen');
	$objPHPExcel->getActiveSheet()->setTitle(''.'Delivery');
						
	$arralCol = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','E','H','I','J','K','L','M','N','O','P','Q','R','T','U','V','W','X','Y','Z');
	$objPHPExcel->alignCenter($alingCenterCol);
	
	$arrDelivery = array();
	$i = 2;
	foreach($arrViajes as $row){
		
		if($row['vdd_id']){
			array_push($arrDelivery, $row['vdd_id']);
		}
		
		$status_origen = NULL;	
		$estadia_origen = NULL;	
		if(!empty($row['vd_ini_real'])){
        	$dif = strtotime($row['vd_ini_real']) - strtotime($row['vd_ini']);
            $status_origen = ($dif > 0)?$lang->system->atrasado:$lang->system->en_tiempo;
			
			if(!empty($row['vd_fin_real'])){
				//$estadia_origen = $objViaje->getTiempoHM(strtotime($row['vd_fin_real']) - strtotime($row['vd_ini_real']));
				$estadia_origen = strtotime($row['vd_fin_real']) - strtotime($row['vd_ini_real']);
			}
		}
		
		$status_detino = NULL;
		$estadia_detino	= NULL;
		if(!empty($row['vdd_ini_real'])){
        	$dif = strtotime($row['vdd_ini_real']) - strtotime($row['vdd_ini']);
            $status_detino = ($dif > 0)?$lang->system->atrasado:$lang->system->en_tiempo;
			
			if(!empty($row['vdd_fin_real'])){
				//$estadia_detino = $objViaje->getTiempoHM(strtotime($row['vdd_fin_real']) - strtotime($row['vdd_ini_real']));
				$estadia_detino = strtotime($row['vdd_fin_real']) - strtotime($row['vdd_ini_real']);
			}
		}
 		
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $row['vi_codigo'])
			->setCellValue('B'.$i, encode($row['cl_razonSocial']))
			->setCellValue('C'.$i, encode($row['rg_nombre']))
			->setCellValue('D'.$i, encode($row['gba_nombre']))
			->setCellValue('E'.$i, $row['vdd_delivery'])
			->setCellValue('F'.$i, $row['vi_movil'])							
			->setCellValue('G'.$i, encode($row['vi_origen']))
			->setCellValue('H'.$i, formatearFecha($row['inicio_preparacion']))							
			->setCellValue('I'.$i, formatearFecha($row['fin_preparacion']))
			->setCellValue('J'.$i, formatearFecha($row['fin_carga']))
			->setCellValue('K'.$i, formatearFecha($row['entrega_documentos']))
			->setCellValue('L'.$i, formatearFecha($row['vd_ini']))				
			->setCellValue('M'.$i, formatearFecha($row['vd_ini_real']))
			->setCellValue('N'.$i, formatearFecha($row['vd_fin']))
			->setCellValue('O'.$i, formatearFecha($row['vd_fin_real']))							
			->setCellValue('P'.$i, $status_origen)
			->setCellValue('Q'.$i, $estadia_origen)
			->setCellValue('R'.$i, (strtotime($row['vd_fin_real']) > strtotime($row['vd_ini']))?(strtotime($row['vd_fin_real']) - strtotime($row['vd_ini'])):NULL)
			->setCellValue('S'.$i, encode($row['vi_destino']))
			->setCellValue('T'.$i, formatearFecha($row['vdd_ini']))
			->setCellValue('U'.$i, formatearFecha($row['vdd_ini_real']))
			->setCellValue('V'.$i, formatearFecha($row['vdd_fin']))
			->setCellValue('W'.$i, formatearFecha($row['vdd_fin_real']))
			->setCellValue('X'.$i, $status_detino)
			->setCellValue('Y'.$i, $estadia_detino)
			->setCellValue('Z'.$i, (strtotime($row['vdd_fin_real']) > strtotime($row['vdd_ini']))?(strtotime($row['vdd_fin_real']) - strtotime($row['vdd_ini'])):NULL)
			->setCellValue('AA'.$i, $row['re_numboca'])
			->setCellValue('AB'.$i, $row['vdd_volumen']);
		$i++;
	}
	
	##-- Delivery Pedidos --##
	$objPHPExcel->createSheet(1);
	$objPHPExcel->setActiveSheetIndex(1)
		->setCellValue('A1', 'Delivery')
		->setCellValue('B1', 'Pedido')
		->setCellValue('C1', 'Condición Pago')
		->setCellValue('D1', 'Disponib. Material')
		->setCellValue('E1', 'Monto');
	$objPHPExcel->getActiveSheet()->setTitle(''.'Pedidos');
	
	$arralCol = array('A','B','C','D','E');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','B','D');
	$objPHPExcel->alignCenter($alingCenterCol);
	
	$arrPedidos = $objViaje->getViajesDeliveryPedidos($arrDelivery);
	$e = 2;
	if(count($arrPedidos) > 0){
		foreach($arrPedidos as $pe){
			$objPHPExcel->setActiveSheetIndex(1)
			->setCellValue('A'.$e, $pe['vdd_delivery'])
			->setCellValue('B'.$e, $pe['vddp_pedido'])
			->setCellValueExplicit('C'.$e, $pe['vddp_condicion_pago'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('D'.$e, $pe['vddp_fecha'])
			->setCellValue('E'.$e, number_format($pe['vddp_valor_neto'],2,',','.'));
			$e++;	
		}
	}
	##-- --##
	
	$objPHPExcel->setActiveSheetIndex(1);	
	$objPHPExcel->setActiveSheetIndex(0);	
}

function export_xls_arauco($objPHPExcel, $objViaje, $arrViajes){
	global $lang;
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $lang->system->nro_orden)
		->setCellValue('B1', $lang->system->transportista)
		->setCellValue('C1', $lang->system->categoria)
		->setCellValue('D1', $lang->system->matricula)
		->setCellValue('E1', $lang->system->origen)
		->setCellValue('F1', $lang->system->ingreso_real_origen)
		->setCellValue('G1', $lang->system->egreso_real_origen)
		->setCellValue('H1', $lang->system->destino)
		->setCellValue('I1', $lang->system->ingreso_real_destino)
		->setCellValue('J1', $lang->system->egreso_real_destino)
		->setCellValue('K1', 'Transit Time')
		->setCellValue('L1', 'Cod. Cliente')
		->setCellValue('M1', $lang->system->delivery);
		
	$objPHPExcel->getActiveSheet()->setTitle(''.'Delivery');
	
	$arralCol = array('A','B','C','D','E','F','G','H','I','J','K','L','M');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','F','G','I','J','K','L','M');
	$objPHPExcel->alignCenter($alingCenterCol);
	
	$i = $aux_i = 2;
	$vi_codigo = $re_numboca = NULL;
	foreach($arrViajes as $row){
		
		if($row['vd_orden'] === 0){
			$transitTime = NULL;
			$origen_vd_fin_real = !empty($row['vd_fin_real'])?$row['vd_fin_real']:NULL;	
			$vi_codigo = $row['vi_codigo'];
			$vd_delivery = $coma = NULL;
			$re_numboca = NULL;
			$aux_i = $i;
		}
		elseif($row['vd_orden'] > 0 && ($vi_codigo == $row['vi_codigo']) && ($re_numboca == $row['re_numboca'])){
			$i = $i-1;
			$vd_delivery.= $coma.$row['vdd_delivery'];
			$coma = ',';
		}
		elseif($row['vd_orden'] > 0 && ($vi_codigo == $row['vi_codigo']) && ($re_numboca != $row['re_numboca'])){
			$re_numboca = $row['re_numboca'];
			$vd_delivery = $row['vdd_delivery'];
			$coma = ',';
		}
		
		
		if(!empty($origen_vd_fin_real) && !empty($row['vdd_ini_real'])){
			$aux_transitTime = $objViaje->getTiempoHM(strtotime($row['vdd_ini_real']) - strtotime($origen_vd_fin_real), false);
			if(empty($transitTime)){
				$transitTime = $aux_transitTime;
			}
			else{
				list($hh_1,$mm_1,$ss_1) = explode(':', $aux_transitTime.'00');	
				list($hh_2,$mm_2,$ss_2) = explode(':', $transitTime.'00');	
				$transitTime = ((($hh_1*3600)+($mm_1*60)+$ss_1) > (($hh_2*3600)+($mm_2*60)+$ss_2))?$aux_transitTime:$transitTime;
			}
		}
				
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $row['vi_codigo'])
			->setCellValue('B'.$i, encode($row['cl_razonSocial']))
			->setCellValue('C'.$i, encode($row['rg_nombre']))
			->setCellValue('D'.$i, $row['vi_movil'])							
			->setCellValue('E'.$i, encode($row['vi_origen']))
			->setCellValue('F'.$i, formatearFecha($row['vd_ini_real']))
			->setCellValue('G'.$i, formatearFecha($row['vd_fin_real']))							
			->setCellValue('H'.$i, encode($row['vi_destino']))
			->setCellValue('I'.$i, formatearFecha($row['vdd_ini_real']))
			->setCellValue('J'.$i, formatearFecha($row['vdd_fin_real']))
			->setCellValue('K'.$aux_i, $transitTime)
			->setCellValue('L'.$i, $row['re_numboca'])
			->setCellValue('M'.$i, $vd_delivery);
			
		$i++;
	}
	
	$objPHPExcel->setActiveSheetIndex(0);	
}

?>
