<?php
if($_POST['accion']){
	switch($_POST['accion']){
		case 'getInfoLog':
			echo getDatosLog($_POST['ip']);	
		break;
		
		case 'saveIp':
			session_start();
			$rel = '../';
			
			require_once($rel.'clases/clsSqlServer.php');
			$objSQLServer = new SqlServer();
			$objSQLServer->rel = $rel;
			$objSQLServer->dbConnect();
			
			$strSQL = " SELECT * FROM tbl_satelital WHERE sa_us_id = ".(int)$_SESSION['idUsuario']." AND sa_cl_id = ".(int)$_SESSION['idAgente'];
			$objRow = $objSQLServer->dbQuery($strSQL);
			$row = $objSQLServer->dbGetRow($objRow,0,3);
			
			$params['sa_ip'] = $_POST['ip'];
			$params['sa_ultimo_test'] = date('Y-m-d H:i:s');
			if($row['sa_id']){
				$objSQLServer->dbQueryUpdate($params, 'tbl_satelital', 'sa_id = '.(int)$row['sa_id']);
			}
			else{
				$params['sa_us_id'] = $_SESSION['idUsuario'];
				$params['sa_cl_id'] = $_SESSION['idAgente'];
				$objSQLServer->dbQueryInsert($params, 'tbl_satelital'); 
			}
			$objSQLServer->dbDisconnect();
			echo "ok";
		break;
	}
	exit;
}

$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	
	$strSQL = " SELECT sa_ip, sa_ultimo_test, sa_ultima_conexion ";
	$strSQL.= " FROM tbl_satelital ";
	$strSQL.= " WHERE sa_us_id = ".(int)$_SESSION['idUsuario']." AND sa_cl_id =".(int)$_SESSION['idAgente'];
	$objRow = $objSQLServer->dbQuery($strSQL);
	$row = $objSQLServer->dbGetRow($objRow,0,3);
	
	$ip = $row['sa_ip'];
	$last_test = formatearFecha($row['sa_ultimo_test']);
	$last_conexion = formatearFecha($row['sa_ultima_conexion']);
	
	$extraCSS[] = 'css/webservicesControl.css';
	require 'includes/template.php';
}

function getDatosLog($ip){
	$return = array();
	$auxHoraServer = date('H:i:s');
	$dir = '../../webservices_v2/log/'.date('mY').'/'.date('dmY').'_webservices_control_data.txt';
	
	if (file_exists($dir)){
		$arch = @file($dir,FILE_IGNORE_NEW_LINES);
		
		$return = array('IP'=>false,'authentication'=>false, 'msg'=>array());
		$auxUltimaRecepción = '00:00:00';
		foreach($arch as $linea){
			if (strstr($linea,'##IP:'.$ip)){
				$return['IP'] = true;
				
				$aux_linea = explode('##',$linea);
				if(strtotime($aux_linea[0]) >= strtotime($auxHoraServer)-5){
					$aux_linea[1] = !empty($aux_linea[1])?:'uno';
					$return['msg'][$aux_linea[1]] = isset($return['msg'][$aux_linea[1]])?$return['msg'][$aux_linea[1]]:array();
				
					if($aux_linea[3] == 'RECEPTION:Incio'){
						//$return['msg'][$aux_linea[1]] = array();
						array_push($return['msg'][$aux_linea[1]],'Recepción: '.$aux_linea[0].' hs');	
					}
					/*elseif($aux_linea[3] == 'RECEPTION:Fin'){
						array_push($return['msg'][$aux_linea[1]],'Fin de la Recepción');
					}*/
					elseif(strstr($aux_linea[3],'ENCODE:')){
						$aux = explode(':',$aux_linea[3]);
						array_push($return['msg'][$aux_linea[1]],'Sistema de Codificación utilizado: '.trim($aux[1]));
					}
					elseif(strstr($aux_linea[3],'AUTHENTICATION:')){
						$aux = explode(':',$aux_linea[3]);
						if(trim($aux[1]) == 'Ok'){
							$return['authentication'] = true;
						}
					}
					elseif(strstr($aux_linea[3],'MESSAGE:')){
						$aux = explode(':',$aux_linea[3]);
						array_push($return['msg'][$aux_linea[1]],trim($aux[1]));
					}
				}
				else{
					$auxUltimaRecepción	= (strtotime($aux_linea[0]) > strtotime($auxUltimaRecepción))?$aux_linea[0]:$auxUltimaRecepción;
				}
			}
		}
		
		if(count($return['msg'])){
			$return['msg'] = array_reverse($return['msg']);
		}
		else{
			$return['msg'] = 'No se obtiene nueva información, última recepción: '.date('H:i:s',strtotime($auxUltimaRecepción)).' hs. Actualizado a las '.date('H:i:s',strtotime($auxHoraServer)).' hs.';	
		}
	}
	else{
		$return['msg'] = 'No se obtiene información de la IP: '.$ip.'. Actualizado a las '.date('H:i:s',strtotime($auxHoraServer)).' hs.';
	}	
	
	return json_encode($return);
}
?>