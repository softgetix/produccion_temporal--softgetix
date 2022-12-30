<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje="", $noError=false){
	global $sinDefaultJS,$sinDefaultCSS;
	require_once 'clases/clsGruposComandos.php';

	$objGruposComandos=new GrupoComandos($objSQLServer);
	$arrGruposComandos=$objGruposComandos->obtenerRegistros();

	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
	
	$IdEmpresa = ($_SESSION["idTipoEmpresa"] <= 2) ? $_SESSION["idEmpresa"] : $_SESSION["idEmpresa"];
	$idTipoEmpresaExcuyente= ($_SESSION["idTipoEmpresa"] <= 3) ? 4 : 0; //todos excluyen localizart
	$idTipoEmpresaExcuyente = 0;
	$arrEquipos = $objEquipo->obtenerEquiposListado($_SESSION["idEmpresa"],$idTipoEmpresaExcuyente);
	
	$tipoBotonera='AM';
	$operacion='alta';
	$extraCSS=array('css/flick/jquery-ui-1.8.14.autocomplete.css');
	$extraJS[]='js/jquery/jquery-ui-1.8.14.autocomplete.min.js';
	$extraJS[]='js/jquery/combobox.js';
	$sinDefaultCSS=$sinDefaultJS=true;
	$extraCSS[]='css/estilosVerificarEquipo.css?1';
	$extraJS[]='js/verificarEquipoFunciones.js?1';
	$extraJS[]='js/ajaxfileupload.js';
	
	
    $popup 	= isset($_GET['action']) && $_GET['action'] == 'popup';
	$idMovil = isset($_GET["idM"]) ? $_GET["idM"] : 0;
	if (!$arrEquipos) die('Ingreso no autorizado.');
	if($popup){
            $tipoBotonera='visualizacion';
			$continuar = false;
			foreach($arrEquipos as $equipo){
				if ($equipo['mo_id'] == $idMovil){
					$arrEquiposAux[0] = $equipo;
					$continuar = true;
				}
			}
			if (!$continuar) die('Ingreso no autorizado.');
			$arrEquipos = $arrEquiposAux;
			$extraCSS[] = 'css/estilosAbmPopup.css';
       		$extraCSS[] = 'css/popup.css';
			$extraCSS[] = 'css/estilosABMDefault.css';
			$extraJS[] = 'js/popupFunciones.js?1';
       		require("includes/frametemplate.php");
	}else{
			require("includes/template.php");
	}
}

function guardarM($objSQLServer, $seccion, $mensaje=''){
	global $lang;
	die();
	require_once 'clases/clsEquipos.php';

	$idEquipo= $_POST['txtEquipo'];

	$objEquipo = new Equipo($objSQLServer);

	$equipo= $objEquipo->obtenerEquipos($idEquipo);

	$set='un_de_id=2';

	if ($_POST['txtNroSerie']== 'COMANDO FALLÓ' ){
		$_POST['txtNroSerie']='';
	}
		$set.=",un_nro_serie=''".trim($_POST['txtNroSerie'])."''";

	$res=$objEquipo->modificarEquipo($set, $idEquipo, $equipo[0]['un_mostrarComo']);

	$mensaje = $lang->message->ok->msj_alta;
	index($objSQLServer,$seccion,$mensaje, true);
}

function volver($objSQLServer, $seccion){
	index($objSQLServer, $seccion);
}