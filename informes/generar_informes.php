<?
//set_time_limit(7200); //2 hs de ejecución.
ini_set('max_execution_time',0);
ini_set('memory_limit', -1);
//error_reporting(0);
$rel = '';

$_GET['tipo_envio'] = isset($_GET['tipo_envio'])?$_GET['tipo_envio']:NULL;
$_GET['hora_envio'] = isset($_GET['hora_envio'])?$_GET['hora_envio']:NULL;
$_GET['id_test_envio'] = isset($_GET['id_test_envio'])?(int)$_GET['id_test_envio']:NULL;

//http://200.32.10.146:444/informes/generar_informes.php?tipo_envio=diaria&hora_envio=06:00
//http://200.32.10.146:444/informes/generar_informes.php?tipo_envio=semanal&hora_envio=06:00
//--IMPORTANTE! ==> el parámetro $_GET['id_test_envio'], es utilizado para realizar pruebas de envio desde la plataforma del usuario, modulo Generador de Informes.
if((empty($_GET['tipo_envio']) || empty($_GET['hora_envio'])) && empty($_GET['id_test_envio'])){
	echo "failed parameters";
	exit;	
}

switch($_GET['tipo_envio']){
	case 'diaria':
		$_GET['tipo_envio'] = 1;
	break;
	case 'semanal':
		$_GET['tipo_envio'] = 2;
	break;
	case 'mensual':
		$_GET['tipo_envio'] = 3;
	break;
	default:
		$_GET['tipo_envio'] = (int)$_GET['tipo_envio'];
	break;
}

require_once 'includes/funciones.php';
require_once 'clases/clsSqlServer.php';
$objSQLServer = new SqlServer();
$objSQLServer->rel = $rel;
$objSQLServer->dbConnect();

	require_once 'clases/clsInformes.php';
	$objInformes = new Informes($objSQLServer, false);
	
	$filtro = array('tipo_envio'=>$_GET['tipo_envio'] ,'hora_envio'=>$_GET['hora_envio'], 'id_test_envio' => (int)$_GET['id_test_envio']);
	$filtro = escapear_array($filtro);
	$arrInformes = $objInformes->getInformesAEnviar($filtro);
	
	if($arrInformes){
		foreach($arrInformes as $itemInforme){
			$objInformes->setLog($itemInforme['in_consulta'], $itemInforme['in_subject'], $_GET['tipo_envio']);
			
			$arrConsulta = $objInformes->ejecutarConsulta($itemInforme['in_consulta']);
			if($arrConsulta){
				$enviarEmail = true;
				$adjunto = false;
				if($itemInforme['in_adjunto']){
					$objInformes->nameFile = $itemInforme['in_adjunto_name'];
					if(!$objInformes->generarAdjunto($arrConsulta)){
						$enviarEmail = false;
					}
					$adjunto = $objInformes->nameFile;
					
					//--- Ferificar si hay que guardar copia del archivo ---//
					if($itemInforme['in_guardar_copia']){
						$dir = 'copy_files/'.$itemInforme['in_cl_id_agente'];
						if(!file_exists($dir)){
							mkdir($dir, 0777, true);
						}
						copy($adjunto, $dir.'/'.$itemInforme['in_adjunto_name'].'-'.date('Ymd').'.xlsx');
					}
					//--- fin copia ---//
				}
					
				$email['cc'] = array();
				if(!empty($itemInforme['in_enviar_a_us_id'])){
					$arrUsuarios = $objInformes->obtenerUsuariosEnvio($itemInforme['in_enviar_a_us_id']);
					if($arrUsuarios){
						foreach($arrUsuarios as $user){
							$email['cc'][] = array('ID' => $user['us_id'], 'mail' => trim($user['us_nombreUsuario']), 'name' => trim($user['us_nombre'].' '.$user['us_apellido']));	
						}	
					}
				}
				
				if(!empty($itemInforme['in_enviar_a_txt'])){
					$arrCC = explode(',',$itemInforme['in_enviar_a_txt']);
					foreach($arrCC as $cc){
						$email['cc'][] = array('mail' => trim($cc), 'name' => NULL);
					}
				}
					
				if(!empty($itemInforme['in_enviar_copia_a'])){
					$arrBCC = explode(',',$itemInforme['in_enviar_copia_a']);
					$email['bcc'] = array();
					foreach($arrBCC as $bcc){
						$email['bcc'][] = array('mail' => trim($bcc), 'name' => NULL);
					}
				}
				if($enviarEmail){
die ('envio ac a');
					$objInformes->generarEnvio($itemInforme['in_subject'], $itemInforme['in_mensaje'], $email, $adjunto);
					if(!(int)$_GET['id_test_envio']){
						$params['in_fecha_ultimo_envio'] = date('Y-m-d H:i:s');
						$objSQLServer->dbQueryUpdate($params, 'tbl_informes', 'in_id ='.(int)$itemInforme['in_id']);
					}
					else{
						echo 'ok';	
					}
				}
			}
		}
	}

$objSQLServer->dbDisconnect();
?>