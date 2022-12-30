<?php
if($_POST['accion']){
	switch($_POST['accion']){
		case 'getInfoAltaMobile':
			session_start();
			$rel = '../';
			//include_once $rel.'includes/conn.php';
			set_time_limit(2900);
			
			##-- LECTURA DE *.txt DE ACELGA Y APIO --##
			include_once $rel.'clases/class.curl_url.php';
			$objCurl = new curl_url();
			$arc = 'controladores/abmLogAltaMobileControlador.php';
			$header = array('Accept: application/json', 'application/x-www-form-urlencoded');
			$datos = array(
				'accion' => 'getLogAltaMobile'
				,'email' => $_POST['email']
				,'fecha' => date('d-m-Y')
				//,'fecha' => '31-08-2014'
			);
			$arrServer[0] = json_decode($objCurl->post('https://200.32.10.146/produccion_temporal/'.$arc,$datos,$header));
			
			$log = '';
			$cantReg = 0;
			
			include_once $rel.'includes/funciones.php';
			foreach($arrServer[0]->log as $k => $item){
				$imgCodigo = '';
				if(!empty($item->code)){
					$codigo = generarCodigoValidacion($item->email);
					$imgCodigo = '<img src="imagenes/'.(($codigo == $item->code)?'resp_ok.png':'cruz.png').'">';
				}
				
				
				$class = ($cantReg % 2 == 0)? 'filaPar' : 'filaImpar';
				$log.= '<tr class="'.$class.'">';
				$log.= '	<td>&nbsp;</td>';
				$log.= '	<td style="text-align:center !important">'.substr($item->fecha,6,2).':'.substr($item->fecha,8,2).':'.substr($item->fecha,10,2).' hs</td>';
				$log.= '	<td>'.$item->email.'</td>';
				$log.= '	<td style="text-align:center !important">'.$item->imei.'</td>';
				$log.= '	<td style="text-align:center !important"><img src="imagenes/'.(validarNuevaContrasenna($item->pass)?'resp_ok.png':'cruz.png').'"></td>';
				$log.= '	<td>'.$item->mobilename.'</td>';
				$log.= '	<td>'.$item->mobilenumber.'</td>';
				$log.= '	<td style="text-align:center !important">'.trim($item->code.' '.$imgCodigo).'</td>';
				$log.= '</tr>';
				$cantReg++;
            }
		    ##-- --##
			if(empty($arrServer[0]->log)){
				$arrEncode['resp'] = '<tr class="filaPar"><td colspan="8" style="text-align:center">No se encontraton resultados</td></tr>';	
			}
			else{
				$arrEncode['resp'] = $log;
			}
			echo json_encode($arrEncode);
			exit;	
		break;
		case 'getLogAltaMobile':
			set_time_limit(2900);
			$arr_log = array();
			$dir = '../../gateway/log/registracion/'.str_replace('-','',$_POST['fecha']).'.txt';
			getDatosLog($dir, $_POST);
			
			$arrEncode = array();
			$arrEncode['log'] = $arr_log;
			echo json_encode($arrEncode);
			exit;
		break;
	}
}


function getDatosLog($dir, $post){
	global $arr_log;
		
	if (file_exists($dir)){
		$arch = @file($dir,FILE_IGNORE_NEW_LINES);
		
		$request = $k = ''; $arr = array();
		foreach($arch as $linea){
			//-- --//
			if(strstr($linea,'GET<pre>Array')){
				$request = 'get';
				$arr = array();
			}
			elseif(strstr($linea,'POST<pre>Array')){
				$request = 'post';	
				$arr = array();
			}
			elseif(strstr($linea,')') && !empty($arr['email'])){
				$request = '';	
				$arr['fecha'] = $fecha;
				array_push($arr_log,$arr);
				$arr = array();
			}
			elseif(strstr($linea,'/gateway/') || strstr($linea,'/Gateway/')){
				$request = '';	
				$arrHora = explode('/',$linea);
				$fecha = $arrHora[0];
			}
					
			if($request == 'post' || $request == 'get'){
				if(strstr($linea,'[email] => ') && strstr($linea,$post['email'])){
					$arr['email'] =  trim(str_replace('[email] =>','',$linea));
				}
				elseif(strstr($linea,'[IMEI] =>')){
					$arr['imei'] =  trim(str_replace('[IMEI] =>','',$linea));
				}
				elseif(strstr($linea,'[pass] =>')){
					$arr['pass'] = trim(str_replace('[pass] =>','',$linea));
				}
				elseif(strstr($linea,'[mobilename] =>')){
					$arr['mobilename'] = trim(str_replace('[mobilename] =>','',$linea));
				}
				elseif(strstr($linea,'[mobilenumber] =>')){
					$arr['mobilenumber'] = trim(str_replace('[mobilenumber] =>','',$linea));
				}
				elseif(strstr($linea,'[code] =>')){
					$arr['code'] = trim(str_replace('[code] =>','',$linea));
				}
			}
			//-- --//
		}
	}	
}

$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje=""){
	$method 	= (isset($_GET['method'])) ? $_GET['method'] : NULL;
	$extraCSS[] = 'css/ui/jquery.ui.autocomplete.css';
	
	require("includes/template.php");
}

?>