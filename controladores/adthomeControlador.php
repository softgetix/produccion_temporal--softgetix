<?php
function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	
	$query = " SELECT * FROM tbl_agentes_adt_contenido WITH(NOLOCK) "
		. " WHERE ac_borrado = 0 AND ac_id_seccion = 0 "
		. "ORDER BY ac_posicion ASC ";
	$result = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query), 3);
	
	$arrListado = array();
	if($result){
		foreach($result as $item){
			if(!isset($arrListado[$item['ac_posicion']])){
				$arrListado[$item['ac_posicion']] = array();
			}
			
			array_push($arrListado[$item['ac_posicion']], $item);
		}
	}

	$extraCSS[] = 'css/slider.css';
    $extraJS[] = 'js/jquery.emiSlider.js';
	
	require("includes/template.php");
}
?>