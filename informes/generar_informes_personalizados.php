<?
//set_time_limit(7200); //2 hs de ejecución.
ini_set('max_execution_time',0);
ini_set('memory_limit', -1);
error_reporting(E_ERROR);
$rel = '';

$_GET['tipo_envio'] = isset($_GET['tipo_envio'])?$_GET['tipo_envio']:NULL;
$_GET['hora_envio'] = isset($_GET['hora_envio'])?$_GET['hora_envio']:'06:00';

//--IMPORTANTE! ==> el parámetro $_GET['id_test_envio'], es utilizado para realizar pruebas de envio desde la plataforma del usuario, modulo Generador de Informes.
if(empty($_GET['tipo_envio']) || empty($_GET['hora_envio'])){
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
	require_once 'clases/clsInformesPersonalizados.php';
	$objInformes = new InformesPersonalizados($objSQLServer);
	
	$filtro = array('tipo_envio'=>$_GET['tipo_envio'],'hora_envio'=>$_GET['hora_envio']);
	$filtro = escapear_array($filtro);
	$arrInformes = $objInformes->getInformesAEnviar($filtro);
	
	if($arrInformes){
		foreach($arrInformes as $itemInforme){
			
			$objInformes->setLog($itemInforme['in_consulta'], $itemInforme['in_subject'], $_GET['tipo_envio']);
			
			$adjunto = $objInformes->nameFile = $objInformes->getNombreAdjunto($itemInforme['asunto'], $itemInforme['ipc_cl_id']);
			if($objInformes->generarAdjunto($itemInforme['ip_id'],$itemInforme['ipc_cl_id'])){
				$email['cc'] = array();
				if(!empty($itemInforme['ipc_us_id'])){
					$arrUsuarios = $objInformes->obtenerUsuariosEnvio($itemInforme['ipc_us_id']);
					if($arrUsuarios){
						foreach($arrUsuarios as $user){
							$email['cc'][] = array('ID' => $user['us_id'], 'mail' => trim($user['us_nombreUsuario']), 'name' => trim($user['us_nombre'].' '.$user['us_apellido']));	
						}	
					}
				}
					
				if(isset($email['cc'][0])){
					$objInformes->generarEnvio($itemInforme['asunto'], 'MENSAJE', $email, true);
					$params['ipc_ultimo_envio'] = date('Y-m-d H:i:s');
					$objSQLServer->dbQueryUpdate($params, 'tbl_informes_personalizados_clientes', 'ipc_id ='.(int)$itemInforme['ipc_id']);
				}
			}
			else{
				echo "Error: "."===><pre>".print_r($itemInforme, true)."</pre><===";	
			}
		}
	}
	
$objSQLServer->dbDisconnect();
?>