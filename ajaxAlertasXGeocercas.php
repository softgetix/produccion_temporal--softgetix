<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
error_reporting(0);

include "includes/validarSesion.php";
include 'includes/funciones.php';
include "includes/conn.php";
include "includes/validarUsuario.php";

switch($_POST['accion']){
	case 'get-validarTecnologia':
		$sql = " SELECT DISTINCT(modelo.mo_id) ";
		$sql.= " FROM tbl_moviles mo ";
		$sql.= " INNER JOIN tbl_unidad ON un_mo_id = mo_id ";
		$sql.= " INNER JOIN tbl_modelos_equipo as modelo ON modelo.mo_id = un_mod_id ";
		$sql.= " WHERE mo.mo_id IN (".$_POST['moviles'].")";
		
		$objMoviles = $objSQLServer->dbQuery($sql);
		$intRows = $objSQLServer->dbNumRows($objMoviles);
		if ($intRows > 1) {
			echo true;}
		else{
			echo false;}	
		
		exit;	
	break;
}