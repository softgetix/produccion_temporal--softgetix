<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function listado($objSQLServer, $seccion, $mensaje = ""){
	
	if(isset($_POST['hidAction'])){
		if($_POST['hidAction'] == 'subscription'){
			subscriptionPayment($_POST['parameter']);
		}
		elseif($_POST['hidAction'] == 'cancel_subscription'){
			cancelSubscriptionPayment($_POST['parameter']);
		}
	}
	
	//$method 	= (isset($_GET['method'])) ? $_GET['method'] : null;
	//$filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:"";
	global $arrEntidades;
	
	$strSQL = " SELECT top 1 p_suscripcion, p_id FROM temp_payment WHERE p_us_id = ".(int)$_SESSION['idUsuario']." ORDER BY p_id DESC ";
	$objRegistros = $objSQLServer->dbQuery($strSQL);
	$arrRegistros = $objSQLServer->dbGetRow($objRegistros,0,3);
	
	$arrEntidades['status'] = 'inactive';
	if($arrRegistros['p_suscripcion'] == 'Active'){
		$arrEntidades['status'] = 'active';
		
		$auxEncode = codificarURL($arrRegistros['p_id']);
		$arrEntidades['parameter'] = $auxEncode['url_encode'];
	}
	else{
		$arrEntidades['monto'] = 0.01;
		$arrEntidades['periodo'] = 'Month';
		
		$idPeriodo = 1;
		$item = 'Servicio Mensual';
		$description = 'Servicio MensualSuscripcion a Servicio Mensual de Traking.';
		
		$aux = 'idUsuario#'.$_SESSION['idUsuario'].'@monto#'.$arrEntidades['monto'].'@periodo#'.$idPeriodo.'@item#'.$item.'@description#'.$description;
		$auxEncode = codificarURL($aux);
		$arrEntidades['parameter'] = $auxEncode['url_encode'];
	}
	
	//require("includes/template.php");
}

function subscriptionPayment($parameter){
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: https://www.localizar-t.com:81/paypal/payment_subscription.php?c=".$parameter); 
	exit;
}

function cancelSubscriptionPayment($parameter){
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: https://www.localizar-t.com:81/paypal/cancel_subscription.php?c=".$parameter); 
	exit;
}
?>