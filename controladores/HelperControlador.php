<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
	 global $arrPermisos;
	 
	 require_once("clases/clsWizards.php");
	 $objWizard = new Wizards($objSQLServer);
	 
	 	 
	 require_once("clases/clsClientes.php");
	 $objCliente = new Cliente($objSQLServer);
	 
	 if($_SESSION['idTipoEmpresa']==2){
		$arrCliente=$objCliente->obtenerAgentes(); //Si es un cliente busco el agente
		$cliente=$arrCliente['cl_id_distribuidor'];
	 }
	 elseif(
		$_SESSION['idTipoEmpresa']==1){ 
		$cliente=$_SESSION['idEmpresa'];
	 }
	 elseif(
		$_SESSION['idTipoEmpresa']==3){ 
		$cliente=$_SESSION['idEmpresa'];
	 }
	 	 
	 
	 $session_keys = "'".implode("','",$arrPermisos)."'";
	 $permitido = $objWizard->getPermisosWizards($session_keys,$cliente);
     $extraJS[] = 'js/wizardsFunciones.js';
	 $extraCSS[] = 'css/estilosWizards.css';
	 
	 $operacion = 'listar';
	 require("includes/template.php");
  
}





?>
