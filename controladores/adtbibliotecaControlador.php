<?php
$solapa = isset($_POST['solapa'])?$_POST['solapa']:(isset($_GET['solapa'])?$_GET['solapa']:NULL);
$solapa = $objPerfil->validarSeccion($solapa)?$solapa:'adt_manual_de_marca';

function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	global $solapa;

	$_GET['action'] = isset($_REQUEST['action'])?$_REQUEST['action'] : $popup;


	switch($solapa){
		case 'adt_manual_de_marca':
			$nro_accion = 1;
		break;
		case 'adt_terminos_y_condiciones':
			$nro_accion = 2;
		break;
		case 'adt_promociones':
			$nro_accion = 3;
		break;
		case 'adt_lista_de_precios':
			$nro_accion = 4;
		break;
		case 'adt_novedades':
			$nro_accion = 5;
		break;
		case 'adt_normas':
			$nro_accion = 6;
		break;
		case 'adt_panel':
			$nro_accion = 7;
		break;
		case 'adt_contrato':
			$nro_accion = 8;
		break;

	}

	$sql = " select * from tbl_agentes_adt_contenido ";
	$sql.= " WHERE ac_borrado = 0 AND ac_id_seccion = {$nro_accion}";

	$objRes = $objSQLServer->dbQuery($sql);
	$arrListado = $objSQLServer->dbGetAllRows($objRes, 3);
	
	require("includes/template.php");
}

?>