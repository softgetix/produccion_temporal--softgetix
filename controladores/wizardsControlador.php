<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
	require_once("clases/clsWizards.php");
	$objWizard = new Wizards($objSQLServer);
	$operacion = 'listar';
    
	 
	//------------------------->OBTENGO EL AGENTE que corresponde al cliente	 
	 require_once("clases/clsClientes.php");
	 $objCliente = new Cliente($objSQLServer);
	 
	 if($_SESSION['idTipoEmpresa']==2){
		$arrCliente=$objCliente->obtenerAgentes(); //Si es un cliente busco el agente
		$cliente=$arrCliente['cl_id_distribuidor'];
	 }
	 elseif($_SESSION['idTipoEmpresa']==1){ 
		$cliente=$_SESSION['idEmpresa'];
	 }
	 elseif($_SESSION['idTipoEmpresa']==3){ 
		$cliente=$_SESSION['idEmpresa'];
	 }
	//------------------------------------------------------------------------------	
	
	 if(!isset($_GET['wizard'])&&!isset($_POST['wizard'])){
	   $_GET['wizard']='default';
	 }
	 	 	 
	 $wizard = $_GET['wizard']?$_GET['wizard']:$_POST['wizard'];
	 $nodo = $_GET['nodo']?$_GET['nodo']:$_POST['nodo'];
	 
	 //////////////////////Si solo me pasaron wizard sin nodo, inicio desde el principio////////////////
	 if(isset($wizard)){
	    if($wizard=='default'){
			$id_wizard=$objWizard->obtenerWizardDefecto($cliente);
			
			if(empty($id_wizard)){ 
				if((isset($_GET['template']))&&($_GET['template']==1)){  // Si me estan llamando del helper y no tengo default cargo el primero de los que existan.
					global $arrPermisos;
					$objWizard = new Wizards($objSQLServer);
					$session_keys = "'".implode("','",$arrPermisos)."'";
					$permitido = $objWizard->getPermisosWizards($session_keys,$cliente);
					header("Location: boot.php?c=wizards&wizard=".$permitido[0]['wi_id']);
				}
				else{
					header("Location: boot.php?c=".$_SESSION['paginaDefecto']);//Si no encontre un wizard por default lo llevo a la pagina original.
				}
			} 
		    $wizard=$id_wizard['wi_id'];
		}
		
		if(empty($nodo)){
			$id=$objWizard->obtenerPrimerNodo($wizard,$cliente);
			$nodo=$id['wi_wn_inicial'];
		}
		
	 }
	if(isset($_GET['ruta'])){
	  $ruta = $_GET['ruta'];
	}
	elseif(isset($_POST['ruta'])){
 	  $ruta = $_POST['ruta'];	
	}
	else{
	  $ruta=0;
	}
		
	permisosWizards($wizard,$cliente,$objSQLServer); //Verifico que el wizard al que está tratando de acceder es permitido.
		
	$arrElementos=$objWizard->obtenerElementos($nodo,$wizard,$cliente,$ruta);
		
	$trace=$_POST['trace']; 
	if($_POST['curso']=='back'){	///Viene de volver
			$arrTrace=explode(",",$trace);//Divido a trace en sus partes
			unset($arrTrace[count($arrTrace)-1]);//resto la ultima parte
			$trace=implode(",",$arrTrace);//rearmo el string 
	}
	else{
		 if($trace!=''){
			$trace.=",".$nodo;//Le sumo el nodo actual
		 }
		 else{
			$trace=$nodo; 
		 }
	}
 
 	$TyC_sinBotonera = false;
 	if($_GET['wizard'] == 9 || $_GET['wizard'] == 13 || $_GET['wizard'] == 14){//-- Terminos y Condiciones --//
 		$TyC_sinBotonera = $objWizard->getBotoneraTyC();
	}
	
	$extraCSS[] = 'css/estilosAbmPopup.css';
	$extraCSS[] = 'css/popup.css';
	$extraJS[] = 'js/popupFunciones.js?1';
	$extraJS[] = 'js/jquery.blockUI.js';
	require("includes/frametemplate.php");
}

function permisosWizards($wizard,$cliente,$objSQLServer){
   global $arrPermisos;
   $session_keys = "'".implode("','",$arrPermisos)."'";
	
   require_once("clases/clsWizards.php");
   $objWizardPermisos=new Wizards($objSQLServer);
   $permitido=$objWizardPermisos->getPermisosWizards($session_keys,$cliente);
   
   //Agregar un for para cada posicion de $permitido?.
   $flag=0;
   foreach($permitido as $per){
		if($wizard==$per['wi_id']){
			////////El usuario tenía permisos para acceder.Pongo fLAG EN 1 Y Continuo con la ejecucción.   			
			$flag=1; 
		}
   }
   if($flag==0){
	   header("Location: boot.php?c=".$_SESSION['paginaDefecto']); //El usuario no tenía permisos. Lo retiro a su pagina por defecto.
   }
}

?>
