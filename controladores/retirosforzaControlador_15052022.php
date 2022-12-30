<?php
//define('FILTROS',array('vt_id' => 30));

$cargamanualok = isset($_GET['cargamanualok']) ? (int)$_GET['cargamanualok'] : NULL;
if($cargamanualok == 1){
	$_SESSION['cargamanualok'] = 1;
}   

$forza_filtros = array('vt_id' => 30);
require 'agendaGPSControlador.php';
?>