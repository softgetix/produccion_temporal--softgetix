<?php

if($_POST['accion']){
	switch($_POST['accion']){
		case 'getInfo':
			session_start();
			
			$rel = '../';
			/*include_once $rel.'includes/funciones.php';
			include_once $rel.'includes/conn.php';*/
			set_time_limit(2900);
			
			##-- LECTURA DE *.txt DE ACELGA Y APIO --##
			include_once $rel.'clases/class.curl_url.php';
			$objCurl = new curl_url();
			$arc = 'controladores/logGenericoControlador.php';
			$header = array('Accept: application/json', 'application/x-www-form-urlencoded');
			$datos = array(
				'accion' => 'getLogGenerico'
				,'buscar' => $_POST['buscar']
				,'fecha' => $_POST['fecha']
				,'fuente' => $_POST['fuente']
			);
			
			//$arrServer[0] = json_decode($objCurl->post('https://200.32.10.146:81/produccion/'.$arc,$datos,$header));
			//$arrServer[0] = json_decode($objCurl->post('https://200.32.10.146/produccion_temporal/'.$arc,$datos,$header));
			//$arrServer[0] = json_decode($objCurl->post('http://localhost/localizart/'.$arc,$datos,$header));
			$arrServer[0] = seleccionarArchivo($_POST);
			//$arrServer[0] = json_encode($arrServer[0]);
			//var_dump($arrServer[0]['log']);
			//exit;
			//-- Unificar datos de LOG de ambos servidores, y ordenarlos segun el horario --//
			$arr_sort = array();
			/*foreach($arrServer[1]->log as $k => $item){
				$arr_sort[$k] = $item->datos;
			}*/
			   
			if($arrServer[0]['log']){
				foreach($arrServer[0]['log'] as $k => $item){
					$arr_sort[$k] = $item['datos'];
				}
			}   
			ksort($arr_sort);

			$log = '';
			$cantReg = 0;
			foreach($arr_sort as $item){
				$class = ($cantReg % 2 == 0)? 'filaPar' : 'filaImpar';
				$log.= '<span class="'.$class.'">'.$item.'</span><br>';
				$cantReg++;
           	}
			//-- --//
			##-- --##
			
			$arrEncode = array();
			//$arrEncode['cantRegLog'] = ((int)$arrServer[1]->cantRegLog?((int)$arrServer[1]->cantRegLog - 1):0) + ((int)$arrServer[0]->cantRegLog?((int)$arrServer[0]->cantRegLog - 1):0);
			
			if(empty($arrServer[0]['log'])){
				$arrEncode['resp'] = '<span class="filaPar" style="text-align:center">No se encontraton resultados</span>';	
			}
			else{
				$arrEncode['resp'] = $log;
			}

			//---
			function utf8_converter($array){
				array_walk_recursive($array, function(&$item){
					$item = utf8_encode( $item ); 
				});
				return json_encode( $array );
			}
			//---
			
			//var_dump('ssss',utf8_converter($arrEncode));exit;

			echo utf8_converter($arrEncode);
			exit;	
		break;
		case 'getLogGenerico':
			echo json_encode(seleccionarArchivo($_POST));
			exit;
		break;
	}
}

function seleccionarArchivo($post){
	set_time_limit(30);
	$arr_log = array();
			
	switch($post['fuente']){
		case 'webservices':
			$dir = dirname(dirname(dirname(__FILE__))).'/webservices/log/'.date('mY',strtotime($post['fecha'])).'/'.str_replace('-','',$post['fecha']).'.txt';
			$arr_log = getDatosLog($dir, $post);
		break;
		case 'Sistema':
			$dir = dirname(dirname(dirname(__FILE__))).'/log/system/web/'.date('Ym',strtotime($post['fecha'])).'/'.$post['fecha'].'.txt';
			$arr_log = getDatosLog($dir, $post);


		break;

		case 'Satelital':
			$dir = dirname(dirname(dirname(__FILE__))).'/webservices_v2/log/'.date('mY',strtotime($post['fecha'])).'/'.str_replace('-','',$post['fecha']).'.txt';
			$arr_log = getDatosLog($dir, $post);
		break;
		case 'avisamecuandollegues':
			$dir = dirname(dirname(dirname(__FILE__))).'/gateway/log/api/'.str_replace('-','',$post['fecha']).'.txt';
			$arr_log = getDatosLog($dir, $post);
		break;
		case 'SAP':
			$dir = dirname(dirname(dirname(__FILE__))).'/webservices_v3/log/'.date('mY',strtotime($post['fecha'])).'/soap/v1/shipment/'.str_replace('-','',$post['fecha']).'.txt';
			$arr_log = getDatosLog($dir, $post);


			
			if(is_array($arr_log) && is_array($respaux)){
				$arr_log = array_merge($arr_log,$respaux);
			}
			else{
				$arr_log = $respaux?:$arr_log;
			}
		break;
		
	}
	
			/*else{
				for($hour = 0; $hour < 24; $hour++){
					$dir = '../../gateway/log/'.str_replace('-','',$_POST['fecha']).'/'.$_POST['equipo'].'_'.(($hour< 10)?'0'.$hour:$hour).'.txt';
					getDatosLog($dir, $_POST);
				}
			}*/
			
	$arrEncode = array();
	$arrEncode['log'] = $arr_log;
	
	return $arrEncode;
}

function getDatosLog($dir, $post){ 
	$arr_log = array();
	if (file_exists($dir)){
		$arch = @file($dir,FILE_IGNORE_NEW_LINES);
	
		foreach($arch as $linea){
			if (strstr($linea,(String)$post['buscar'])){
				$arr_log[]['datos'] = '<p>'.$linea.'</p>';
			}
		}
	}

	//var_dump($arr_log); exit;
	return $arr_log;
}

$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
function index($objSQLServer, $seccion, $mensaje=""){
	$method 	= (isset($_GET['method'])) ? $_GET['method'] : NULL;
	$extraCSS[] = 'css/ui/jquery.ui.autocomplete.css';
	require("includes/template.php");
}

?>