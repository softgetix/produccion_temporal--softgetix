<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 
function index($objSQLServer, $seccion, $mensaje=""){
	 require_once("clases/clsWizards.php");
	 $operacion = 'listar';
     require("includes/template.php");
}
?>
