<?php
session_start();
set_time_limit(300);

$arr_redirect = explode('/',$_SERVER['REQUEST_URI']);
$redirect = $arr_redirect[1];
include('includes/config_clientes.php');
require_once 'includes/funciones.php';
require_once 'includes/conn.php';
require_once 'includes/validarUsuario.php';
require_once 'clases/clsLog.php';

global $objSQLServer;

$ingresarComo = isset($_POST['ingresar_como']) ? $_POST['ingresar_como'] : false;
$id = $ingresarComo;

/////////////////////////////////////VERIFICACION CONTRA INYECCION JS EN ID DE USUARIO////////////////
require_once 'clases/clsUsuarios.php';

function obtenerListado($objSQLServer){	
		$criterioOrden = "";
		if (isset($_POST['hidCriterioOrden'])) {
			$criterioOrden = $_POST['hidCriterioOrden'];
		}
		
		$orden = "";
		if (isset($_POST['hidOrden'])) {
			$orden = $_POST['hidOrden'];
		}
		
		$filtro = 'getAllReg';
		
		$objUsuario = new Usuario($objSQLServer);
		if($_SESSION["idTipoEmpresa"] == 2){ //CLIENTE
			$arrEntidades = $objUsuario->obtenerUsuariosSP($_SESSION['idUsuario'], $filtro);
		}elseif ($_SESSION["idTipoEmpresa"] == 1){ //AGENTE
			$datos = array('idEmpresa' => $_SESSION['idEmpresa'], 'filtro' => $filtro);
			$arrEntidades = $objUsuario->obtenerUsuariosListado($datos);
		} elseif ($_SESSION["idTipoEmpresa"] == 3) {
			$datos = array('idTipoEmpresaExcluyente' => 4, 'filtro' => $filtro, 'criterioOrden' => $criterioOrden, 'orden' => $orden);
			$arrEntidades = $objUsuario->obtenerUsuariosListado($datos);
		} else {
			$arrEntidades = $objUsuario->obtenerUsuarios(0,$filtro,"");
		}
	return $arrEntidades;
}

$mPermitido = obtenerListado($objSQLServer);
$aciertos = 0;
foreach($mPermitido as $permitido){
	if($id == $permitido['us_id']){
		//Sumo al contador de aciertos y lo dejo seguir.
		$aciertos ++;	
		break;
	}
}
	
if($aciertos == 0){// el id que me quiere asignar el usuario no está en lo que devuelve el listado
	$mPermitido=NULL; //PASO un NULL para que entre en al rutina de inyección.
	validarModificar($mPermitido,$objSQLServer);		
}
//////////////////////////////////////////////////////////////////////////////////////////////////////

$hidPass = isset($_POST['hidPass']) ? $_POST['hidPass'] : "";
$hidPassDirect = isset($_POST['hidPassDirect']) ? $_POST['hidPassDirect'] : "";

$pass_inicioSession = $_SESSION["pass_inicioSession"];
$config_cliente = $_SESSION['DIRCONFIG'];

if(isset($hidPassDirect) && md5($hidPassDirect) != $pass_inicioSession){}
else{
	if(isset($hidPass) && md5($hidPass) != $pass_inicioSession) {
		session_unset();
		session_destroy();
		echo '<script>document.location.href="/'.$redirect.'"</script>';
		exit;
	}
}

$resolucion = $_SESSION["resolucion"];
$objUsuario = new Usuario($objSQLServer);
$arrUsuario = $objUsuario->obtenerPassword($ingresarComo);
$log 		= new Log($objSQLServer);
$ip 		= getRealIP();
$user_agent = $_SERVER['HTTP_USER_AGENT'];


//-- Generar Log --//
$objUsuario->generarLog(4,$arrUsuario[0]['us_id'],decode($lang->system->inicio_session_como.' '.trim($arrUsuario[0]['us_nombre'].' '.$arrUsuario[0]['us_apellido'])));
//-- fin. Generar Log --//


session_unset();

$_SESSION["pass"] 				= $arrUsuario[0]["us_pass"]; 
$_SESSION["pass_inicioSession"] = $pass_inicioSession;//LAS PASSWORD NO SE TIENE Q PISAR SI CAMBIO DE USUARIO X SISTEMA
$_SESSION["idUsuario"]			= $arrUsuario[0]["us_id"];
$_SESSION["us_nombre"]			= $arrUsuario[0]["us_nombre"];
$_SESSION["us_apellido"]		= $arrUsuario[0]["us_apellido"];
$_SESSION["idPerfil"] 			= $arrUsuario[0]["us_pe_id"];
$_SESSION["nombreUsuario"] 		= $arrUsuario[0]["us_nombreUsuario"];
$_SESSION["idEmpresa"] 			= $arrUsuario[0]["us_cl_id"];
$_SESSION["idAgente"] 			= ($arrUsuario[0]["cl_tipo"] == 1)?$arrUsuario[0]["us_cl_id"]:$arrUsuario[0]["cl_id_distribuidor"];
$_SESSION["idTipoEmpresa"] 		= $arrUsuario[0]["cl_tipo"];
$_SESSION["idPais"] 			= $arrUsuario[0]["cl_pai_id"];
$_SESSION['idioma'] = trim($arrUsuario[0]['cl_idioma_definida']);	
$aux = explode('_',$_SESSION['idioma']);
$_SESSION['language'] = $aux[0];
			
//centrado de mapas
$_SESSION["lat"] 			= $arrUsuario[0]["pr_lat"];
$_SESSION["lng"] 			= $arrUsuario[0]["pr_lng"];
$_SESSION["zoom"] 			= $arrUsuario[0]["pr_zoom"];
//centrado de mapas
$_SESSION["mailAlerta"] 		= $arrUsuario[0]["us_mailContacto"];

$aux 							= explode("@",$arrUsuario[0]["us_nombreUsuario"]);
$_SESSION["nombreUsuarioCorto"] = $aux[0];
$_SESSION["resolucion"] = $resolucion;

$_SESSION['DIRCONFIG'] = $config_cliente;

require_once 'includes/navbar_permisos.php';
$_SESSION['paginaDefecto'] = $paginaDefecto;

header('Location: /'.$redirect.'/boot.php?c='.$_SESSION["paginaDefecto"]);
?>
