<?php

if($_POST['accion']){
	switch($_POST['accion']){
		case 'autocomplete-imei':
			set_time_limit(0);
			$rel = '../';
			include_once $rel.'includes/funciones.php';
			include_once $rel.'includes/conn.php';
			$strSQL = " buscarMovil '".$_POST['buscar']."' ";
			$result = $objSQLServer->dbQuery($strSQL);
			$arr_moviles = $objSQLServer->dbGetAllRows($result);
			$arr_data = array();
			foreach($arr_moviles as $item){
				array_push($arr_data,array('movil'=>encode($item['mo_matricula']." - ".$item['mo_otros']), 'imei' =>$item['mo_identificador']));
			}	
			echo json_encode($arr_data);
			exit;
		break;
		case 'getInfoGateway':
			session_start();
			$rel = '../';
			include_once $rel.'includes/funciones.php';
			include_once $rel.'includes/conn.php';
			set_time_limit(2900);
			
			##-- Obtener Lat y Lng de Celltawer --##	
			$arr_cell = array();
			$ant_cell = '';
			$ant_lac = '';
			$count = 0;
			foreach($arr_cellids as $items){
				if($ant_cell != $items['cellid'] || $ant_lac != $items['lac']){
					$ant_cell = $items['cellid'];
					$ant_lac = $items['lac'];
					$sql = " SELECT ct_lat, ct_lng FROM tbl_celltower WHERE ct_cellid = '".$items['cellid']."' AND ct_lac = '".$items['lac']."'";		
					$rs = $objSQLServer->dbQuery($sql);
					$res = $objSQLServer->dbGetAllRows($rs,3);
					if($res){
						$arr_cell[$count]['lat'] = $res[0]['ct_lat'];
						$arr_cell[$count]['lng'] = $res[0]['ct_lng'];
						$count++;
					}
				}
			}
			##-- --##	
			
			##-- Obtener Lat y Lng de History --##	
			//$historyID = ((strtotime(date('d-m-Y')) - strtotime($_POST['fecha'])) / (60 * 60 * 24)) + 1; //historico viejo
			$historyID = str_replace('-','',$_POST['fecha']);
			$arr_history = array();
			$sql = " SELECT hy_latitud, hy_longitud FROM tbl_history_".$historyID." WHERE hy_un_id IN (SELECT un_id FROM tbl_unidad WHERE un_nro_serie = '".$_POST['imei']."')";		
			$rs = $objSQLServer->dbQuery($sql);
			$arr_history = $objSQLServer->dbGetAllRows($rs);
			##-- --##	
			
			
			##-- LECTURA DE *.txt DE ACELGA Y APIO --##
			include_once $rel.'clases/class.curl_url.php';
			$objCurl = new curl_url();
			$arc = 'controladores/abmLogGatewayControlador.php';
			$header = array('Accept: application/json', 'application/x-www-form-urlencoded');
			$datos = array(
				'accion' => 'getLogGateway'
				,'equipo' => $_POST['equipo']
				,'fecha' => $_POST['fecha']
				,'imei' => $_POST['imei']
			);
			
			$arrServer[0] = json_decode($objCurl->post('https://200.32.10.146/produccion/'.$arc,$datos,$header));
			
			//-- Unificar datos de Cellid de ambos servidores, y ordenarlos segun el horario --//
			$arr_sort = array();
			/*
			foreach($arrServer[1]->cellids as $k => $item){
				if($item->cellid != -1 && $item->lac != -1){
					$arrDataCell = getDataCellTower($item->cellid, $item->lac);
					if(!empty($arrDataCell['lat']) && !empty($arrDataCell['lng'])){
						$arr_sort[$k]['cellid'] = $item->cellid;
						$arr_sort[$k]['lac'] = $item->lac;
						$arr_sort[$k]['lat'] = $arrDataCell['lat'];
						$arr_sort[$k]['lng'] = $arrDataCell['lng'];
					}
				}
           	}
			*/
		    foreach($arrServer[0]->cellids as $k => $item){
				if($item->cellid != -1 && $item->lac != -1){
					$arrDataCell = getDataCellTower($item->cellid, $item->lac);
					if(!empty($arrDataCell['lat']) && !empty($arrDataCell['lng'])){
						$arr_sort[$k]['cellid'] = $item->cellid;
						$arr_sort[$k]['lac'] = $item->lac;
						$arr_sort[$k]['lat'] = $arrDataCell['lat'];
						$arr_sort[$k]['lng'] = $arrDataCell['lng'];
					}
				}
           	}
			ksort($arr_sort);
			$arr_cell = array();
			foreach($arr_sort as $item){
				array_push($arr_cell, $item);
           	}
			//-- --//
			
			//-- Unificar datos de LatLong GPS de ambos servidores, y ordenarlos segun el horario --//
			$arr_sort = array();
		    foreach($arrServer[1]->latLng as $k => $item){
				$arr_sort[$k]['gps_latitude'] = $item->gps_latitude;
				$arr_sort[$k]['gps_longitude'] = $item->gps_longitude;
           	}
		    foreach($arrServer[0]->latLng as $k => $item){
				$arr_sort[$k]['gps_latitude'] = $item->gps_latitude;
				$arr_sort[$k]['gps_longitude'] = $item->gps_longitude;
           	}
			ksort($arr_sort);
			$arr_latLng = array();
			foreach($arr_sort as $item){
				array_push($arr_latLng, $item);
           	}
			//-- --//
			
			//-- Unificar datos de LOG de ambos servidores, y ordenarlos segun el horario --//
			$arr_sort = array();
			foreach($arrServer[1]->log as $k => $item){
				$arr_sort[$k] = $item->datos;
           	}
		    foreach($arrServer[0]->log as $k => $item){
				$arr_sort[$k] = $item->datos;
           	}
			ksort($arr_sort);
			$log = '';
			$cantReg = 0;
			foreach($arr_sort as $item){
				$class = ($cantReg % 2 == 0)? 'filaPar' : 'filaImpar';
				$log.= '<span class="'.$class.'">'.$item.'</span>';
				$cantReg++;
           	}
			//-- --//
			##-- --##
			
			$arrEncode = array();
			$arrEncode['cantRegLog'] = ((int)$arrServer[1]->cantRegLog?((int)$arrServer[1]->cantRegLog - 1):0) + ((int)$arrServer[0]->cantRegLog?((int)$arrServer[0]->cantRegLog - 1):0);
			
			if(empty($arrServer[0]->log)){
				$arrEncode['resp'] = '<span class="filaPar" style="text-align:center">No se encontraton resultados</span>';	
			}
			else{
				$arrEncode['resp'] = $log;
			}
			$arrEncode['ptosLog'] = $arr_latLng;
			$arrEncode['ptosCell'] = $arr_cell;
			$arrEncode['ptosHistory'] = $arr_history;
			echo json_encode($arrEncode);
			exit;	
		break;
		case 'getLogGateway':
			set_time_limit(2900);
			$cantReg = 0;
			$arr_latLng = array();
			$arr_cellids = array();
			$arr_log = array();
			if($_POST['equipo'] == 'Nokia'){
				$dir = '../../gateway/log/'.$_POST['equipo'].'_'.str_replace('-','',$_POST['fecha']).'.txt';
				getDatosLog($dir, $_POST);
			}
			else{
				for($hour = 0; $hour < 24; $hour++){
					$dir = '../../gateway/log/'.str_replace('-','',$_POST['fecha']).'/'.$_POST['equipo'].'_'.(($hour< 10)?'0'.$hour:$hour).'.txt';
					getDatosLog($dir, $_POST);
				}
			}
			
			$arrEncode = array();
			$arrEncode['log'] = $arr_log;
			$arrEncode['cantRegLog'] = $cantReg;
			$arrEncode['cellids'] = $arr_cellids;
			$arrEncode['latLng'] = $arr_latLng;
			echo json_encode($arrEncode);
			exit;
		break;
		case 'getInfoMovil':
			session_start();
			$rel = '../';
			include_once $rel.'includes/funciones.php';
			include_once $rel.'includes/conn.php';
			
			$arrEncode = array();
			$arrEncode['modelo'] = '';
			$sql = " SELECT mo_modelo FROM tbl_moviles ";
			$sql.= " INNER JOIN tbl_unidad ON mo_id = un_mo_id ";
			$sql.= " where un_nro_serie = '".$_POST['imei']."' ";
			$rs = $objSQLServer->dbQuery($sql);
			$res = $objSQLServer->dbGetRow($rs,0,3);
			if($res){
				$arrEncode['modelo'] = $res['mo_modelo'];
			}	
			echo json_encode($arrEncode);
			exit;
		break;
	}
}


function getDatosLog($dir, $post){
	$resp = '';
	$i = 0;
	global $cantReg;
	global $arr_latLng;
	global $arr_cellids;
	global $arr_log;
	
	if (file_exists($dir)){
		$arch = @file($dir,FILE_IGNORE_NEW_LINES);
	
		foreach($arch as $linea){
			if (strstr($linea,'imei='.$post['imei'])){
				$arr_linea = explode('/',$linea);
				$hora = substr($arr_linea[0],6,2).':'.substr($arr_linea[0],8,2).':'.substr($arr_linea[0],10,2);
				$fecha = substr($arr_linea[0],0,2).'-'.substr($arr_linea[0],2,2).'-20'.substr($arr_linea[0],4,2).' '.$hora;
							
				$i = strtotime(substr($arr_linea[0],6,2).':'.substr($arr_linea[0],8,2).':'.substr($arr_linea[0],10,2));
							
				##-- Obtener Lat y Lng // CellID --##
				$arr = explode('&',$arr_linea[2]);	
				foreach($arr as $items){
					$arrItems = explode('=',$items);
					if($arrItems[0] == 'gps_longitude' || $arrItems[0] == 'gps_latitude'){
						$arr_latLng[$i][$arrItems[0]] = $arrItems[1]; 
					}
					elseif($arrItems[0] == 'cellid' || $arrItems[0] == 'lac'){
						$arr_cellids[$i][$arrItems[0]] = $arrItems[1]; 
					}	
				}
				##-- --##
				$resp = $fecha.' hs&nbsp;&nbsp;<p>'.$arr_linea[2].'</p>';
				if($arr_latLng[$i]['gps_latitude'] != '' && $arr_latLng[$i]['gps_longitude'] != ''
					&& $arr_latLng[$i]['gps_latitude'] != '0.0' && $arr_latLng[$i]['gps_longitude'] != '0.0'
				){
					$resp.= '<a href="javascript:verMapa('.$arr_latLng[$i]['gps_latitude'].','.$arr_latLng[$i]['gps_longitude'].'); setSolapas(\'mapa\')">Ver</a>';
					$cantReg++;
				}
				$arr_log[$i]['datos'] = $resp;
				
			}
			elseif (strstr($linea,'RESPUESTA='.$post['imei'])){
				$arr_log[$i]['datos'].= '<p>'.$linea.'</p>';	
			}
			elseif (strstr($linea,'ES PANICO ('.$post['imei'].')')){
				$arr_log[$i]['datos'].= '<p>'.$linea.'</p>';	
			}
		}
	}	
}

function getDataCellTower($cellid, $lac){
	global $objSQLServer;
	$sql = " SELECT ct_lat, ct_lng FROM tbl_celltower WHERE ct_cellid = '".$cellid."' AND ct_lac = '".$lac."'";		
	$rs = $objSQLServer->dbQuery($sql);
	$res = $objSQLServer->dbGetRow($rs,0,3);
	if($res){
		$arr_cell['lat'] = $res['ct_lat'];
		$arr_cell['lng'] = $res['ct_lng'];
		return $arr_cell;
	}	
}

$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
function index($objSQLServer, $seccion, $mensaje=""){
	$method 	= (isset($_GET['method'])) ? $_GET['method'] : NULL;
	$extraCSS[] = 'css/ui/jquery.ui.autocomplete.css';
	
	//require_once("includes/google.v3.ini");
	$extraJS[] = 'js/openLayers/OpenLayers.js';
	$extraJS[] = 'js/defaultMap.js';
	require("includes/template.php");
}

?>