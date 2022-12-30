<?php
function index($objSQLServer, $seccion, $mensaje = "") {
	global $lang;

	$query = " EXEC db_dashboard {$_SESSION['idUsuario']}";

	$result = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);

	require("includes/template.php");

}
?>
