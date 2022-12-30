<?php



if( $_SESSION['idEmpresa'] == 74 ){


	if( $_SESSION['idEmpresa'] == 74){
	

		//include "includes/validarSesion.php";
		//include "includes/funciones.php";

		include "includes/conn.php";
		//include "includes/validarUsuario.php";
		
		$return = array();
		//$ultimoId = isset($_POST['ultimo_id'])?$_POST['ultimo_id']:0;
		require_once 'clases/clsNotificacionAlertas.php';

		$objNotificacion = new NotificacionAlertas($objSQLServer);

		if($_SESSION['idEmpresa'] == 74  || $_SESSION['idEmpresa'] == 4835){//Arauco
		
		
	
			$strSQL = "EXEC Robot_OEA_grilla_alertas {$_SESSION['idUsuario']}, 0, 100, 0";

			$arrAlertasNoConfirmadas = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL),3);
		}
		else{

			$arrAlertasNoConfirmadas = $objNotificacion->obtenerAlertasDelAgenteNoConfirmadas($ultimoId);	
		}
		
		$arrAlertasAgrupadas = $objNotificacion->obtenerAlertasAgrupadas($arrAlertasNoConfirmadas);
		

		
		foreach($arrAlertasAgrupadas as $item){
			$arr = array(
				'id'=>$item['id'],
				'id_movil'=>$item['movilid'],
				'movil'=>encode($item['matricula']),
				'evento'=>encode($item['descripcion']),
				'sentido'=>$item['sentido'],
				'velocidad'=>$item['velocidad'],
				'recibido'=>$item['recibido'],
				'generado'=>$item['generado'],
				'lat'=>$item['latitud'],
				'lng'=>$item['longitud'],
				'id_referencia'=>$item['re_id'],
				'nomenclado'=>encode($item['nomenclado']),// NO SACAR  "encode" PORQUE SE COMPROBO QUE ROMPE Y RETORNA null. -->tbl_mail_enviado->me_id=792460
				'ocurrencias'=>$item['ocurrencias'],
				'nombreEmpresa'=>encode($item['nombreEmpresa']),
				'tel'=>$item['tel'],
				'alerta'=>encode($item['alerta']),
				'arr_ids'=>$item['ids']
			);
			array_push($return, $arr);
		}
		echo json_encode($return);

	}
	exit;
}


$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
function index($objSQLServer, $seccion){
	/* Motivos de confirmacion */
    require_once 'clases/clsMotivoConfirmacion.php';
    $objMotConf = new MotivoConfirmacion($objSQLServer);
    $arrMotivosConfirmacion = $objMotConf->obtenerRegistros();
	
	$extraJS[] = 'js/sm2/soundmanager2-nodebug-jsmin.js';
	$extraJS[] = 'js/popupHostFunciones.js';
	$extraCSS[]='css/estilosPopup.css';
	require("includes/template.php");
}
?>