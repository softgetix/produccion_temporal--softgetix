<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");
header('Content-type: application/json');

set_time_limit(300);
$verificarEquipo = 1;

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

include 'clases/clsVerificarEquipo.php';

if (!isset($_GET['c'])){
	require_once 'clases/clsComandos.php';
	$objComando=new Comando($objSQLServer);
	$temp=$objComando->obtenerRegistros(0,'','QSN');//asignar lo que corresponda
	define('OBTENER_NRO_SERIE',$temp[0]['co_id']);
	unset($objComando, $temp);
}else{
	define('OBTENER_NRO_SERIE',2);
}


$accion=$_GET['a'];
$respuesta=array('ok'=>false);
$objVerificacion= new Verificacion($objSQLServer);

switch($accion){
	//iniciar verificacion, crea un nuevo registro en tbl_verificaciones y devuelve el id para futura referencia
	case 'ini':
		$idEquipo=$_GET['e'];
		$_SESSION["idEquipoCP"] = $idEquipo;
		$idGrupoComandos = ($_GET['g']) ? $_GET['g'] : $_SESSION["grupo"];
		$vId = $objVerificacion->iniciarVerificacion($idEquipo,$idGrupoComandos,$_SESSION['idUsuario'], 0);
		
		if ($vId!=0){
			$respuesta['ok']=true;
			$respuesta['vId']=$vId;
		}
		break;
	case 'prb': //iniciar la prueba de un comando
		$vId=$_GET['vId'];
                //$idEquipo=$_SESSION["idEquipoCP"];
		//require_once 'clases/clsEquipos.php';
		//$objEquipo=new Equipo($objSQLServer);
		//$arrEquipo = $objEquipo->obtenerEquipos($idEquipo);
                
		if (isset($_GET['c'])){
			$comando=$_GET['c'];
			$tipo=$_GET['t'];
		}else{
			$comando=OBTENER_NRO_SERIE;
			$tipo=1;
			break;
		}
                //,$arrEquipo[0]['un_me_id']
		$respuesta['ok']=(boolean)$objVerificacion->iniciarComando($vId,$comando,$tipo);
		$res = enviarCP($objSQLServer);
		$respuesta['puertoCP']=(string) $res;
		break;
	case 'ctr': //controlar estado de comando
		$vId=$_GET['vId'];
		if(isset($_GET['c'])){
			$comando=$_GET['c'];
		}else{
			$comando=OBTENER_NRO_SERIE;
		}
		$ans=$objVerificacion->controlarComando($vId,$comando);
		$respuesta['ok']=(boolean) $ans;
		$respuesta['res']=(string) $ans;

		if (!$respuesta['ok'] && isset($_GET["c"])){
			$res = enviarCP($objSQLServer);
			$respuesta['puertoCP']=(string) $res;
		}else{
			require_once 'clases/clsComandos.php';
			$objComando=new Comando($objSQLServer);
			$temp=$objVerificacion->controlarRespuesta($comando);
			//$respuesta['kk']=$temp[0];
			//if (trim($temp[0]['co_respuesta_ok'])!== '' && $respuesta['res']!==$temp[0]['co_respuesta_ok']){
			if (trim($temp)!== '' && $respuesta['res']!==$temp){
				$respuesta['bad']=true;
			}else{
				$respuesta['bad']=false;
			}
		}
		break;
	case 'stp': //cancelar el control de un comando
		$vId=$_GET['vId'];
		if(isset($_GET['c'])){
			$comando=$_GET['c'];
		}else{
			$comando=OBTENER_NRO_SERIE;
		}
		$respuesta['ok']=true;//$objVerificacion->cancelarComando($vId,$comando);
		break;
}
echo json_encode($respuesta);
exit;





function enviarCP($objSQLServer){
	
	$idEquipo = isset($_SESSION["idEquipoCP"]) ? $_SESSION["idEquipoCP"] : 0;
	
	if ($idEquipo) {
		require_once 'clases/clsEquipos.php';
		$objEquipo=new Equipo($objSQLServer);
		
		$arrEquipo = $objEquipo -> obtenerEquipos($idEquipo);
		if ($arrEquipo[0]['mo_puerto']) {
                        //pr($arrEquipo);
			$buf = ">CP<";
			$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
			socket_sendto($socket, $buf, strlen($buf), 0, $arrEquipo[0]['mo_ip'], $arrEquipo[0]['mo_puerto']);
			socket_close($socket);
		}
		
		return $arrEquipo[0]['mo_puerto'];
	}
}