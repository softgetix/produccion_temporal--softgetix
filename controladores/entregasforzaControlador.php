<?php

if($_POST['hidOperacion'] == 'alta' 
    || $_POST['hidOperacion'] == 'guardarA' 
    || $_POST['hidOperacion'] == 'modificar' 
    || $_POST['hidOperacion'] == 'guardarM'){
    
    require_once 'clases/clsReferencias.php';
    $Referencia = new Referencia($objSQLServer);

    $fields = "re.re_id, (CASE WHEN re_numboca != '' THEN '('+LTRIM(RTRIM(re_numboca))+') ' ELSE '' END) 
				+ re_nombre 
				as re_nombre";
    $cbo_origen = $Referencia->search($fields, 'AND re.re_rg_id = 119');
}
else{
    $query = "SELECT dbo.Pallets_administracion_permiso (".(int)$_SESSION['idUsuario'].")";
    $permisoCarga = $objSQLServer->dbGetRow($objSQLServer->dbQuery($query),0,2);
    $permisoCarga = $permisoCarga[0] ? 1 : 0;
}

$forza_filtros = array('vt_id' => 29);
require 'agendaGPSControlador.php';
?>