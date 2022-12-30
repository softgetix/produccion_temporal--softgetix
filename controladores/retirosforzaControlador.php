<?php
//define('FILTROS',array('vt_id' => 30));

$cargamanualok = isset($_GET['cargamanualok']) ? (int)$_GET['cargamanualok'] : NULL;
if($cargamanualok == 1){
	$_SESSION['cargamanualok'] = 1;
}   

$query = "SELECT dbo.Pallets_administracion_permiso (".(int)$_SESSION['idUsuario'].")";
$permisoCarga = $objSQLServer->dbGetRow($objSQLServer->dbQuery($query),0,2);
$permisoCarga = $permisoCarga[0] ? 1 : 0;

$forza_filtros = array('vt_id' => 30);
require 'agendaGPSControlador.php';
?>