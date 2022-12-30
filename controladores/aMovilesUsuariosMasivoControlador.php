<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
$sinDefaultCSS=$sinDefaultJS=true;
function index($objSQLServer, $seccion, $mensaje="",$noError=false){
	alta($objSQLServer,$seccion,$mensaje,array(),$noError, isset($_GET['popup']));
	exit();
}


function alta($objSQLServer, $seccion, $mensaje="", $arrEntidades=array(), $noError=false, $popup=false){
	global $lang;

	$operacion = 'alta';
	$tipoBotonera='AM';

	$idGrupo = isset($_GET['idG']) ? $_GET['idG'] : 0;
        
    require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
    if ($idGrupo == 0) {
        $Grupo['gm_nombre'] = $lang->system->sin_grupo;
    } else {
        $Grupo = $objMovil->obtenerGrupo($idGrupo);
    }
	
	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	if ($_SESSION["idTipoEmpresa"] == 2){ //CLIENTE
		$arrEntidades = $objUsuario->obtenerUsuarios(0,"","");
		usuario($arrEntidades,$_SESSION["idUsuario"],0);
		global $arrUsuarios;
	} elseif ($_SESSION["idTipoEmpresa"] == 1){ //AGENTE
		$arrUsuarios = $objUsuario->obtenerUsuariosPorEmpresa($_SESSION['idEmpresa'],0);
	} elseif ($_SESSION["idTipoEmpresa"] == 3) {
		$arrUsuarios = $objUsuario->obtenerUsuariosPorEmpresa(0, 4);
	} else {
		$arrUsuarios = $objUsuario->obtenerUsuarios(0,'',"");
	}
	
	$id = $_SESSION["idUsuario"];
	$arrMoviles = $objMovil->obtenerMovilesUsuarioCombo($_SESSION["idUsuario"]);
	
	$arrGrupos = $objMovil->obtenerGruposMovilesUsuario(0, "", $_SESSION['idUsuario']);
		
	$arrGrupos2 = array();
	foreach ($arrGrupos as $grupo) {
		$arrGrupos2[$grupo['gm_id']] = $grupo['gm_nombre'];
	}
	
	foreach ($arrGrupos2 as $id => $nombre) {
		$tmpMoviles = $objMovil->obtenerMovilesGrupo($id, $_SESSION['idUsuario']);
		foreach ($tmpMoviles as $tmpMovil) {
			$gid = $tmpMovil['um_grupo']; 
			$mid = $tmpMovil['id'];
			$dato = $tmpMovil['dato'];
			$Grupos[$gid][$mid] = $dato;
		}
	}
	
	$extraJS[]='js/jqBoxes.js';
	if (!$popup){
		require("includes/template.php");
	}else{
		echo '<script>var recargar = true;</script>';
		$extraCSS[] = 'css/estilosABMDefault.css';
		$extraCSS[] = 'css/estilosAbmPopup.css';
		$extraCSS[] = 'css/popup.css';
		$extraJS[] = 'js/popupFunciones.js?1';
		$extraJS[] = 'js/jquery.blockUI.js';
		require("includes/frametemplate.php");
	}
}

function guardarA($objSQLServer, $seccion){
	global $lang;
	$mensaje='';

	$arrMoviles = $_POST['hid_lstMovilesAsig']?explode(',',$_POST['hid_lstMovilesAsig']):'';
	$arrUsuarios = $_POST['hid_lstUsuariosAsig']?explode(',',$_POST['hid_lstUsuariosAsig']):'';
    $nuevoGrupo = (isset($_POST["txtNombreGrupo"]))? $_POST["txtNombreGrupo"]:null;

	if (!$arrMoviles){
		$mensaje.= $lang->message->msj_elegir_moviles.'<br/>';
	}
	if (!$arrUsuarios){
		$mensaje.= $lang->message->msj_elegir_usuarios.'<br/>';
	}

	if(!$mensaje){
		require_once 'clases/clsMoviles.php';
		$objMoviles=new Movil($objSQLServer);
		foreach($arrUsuarios as $usuario){
            $nuevoGrupoId = 0;
			$asignados=array();
			$arrAsignados=$objMoviles->obtenerAsignacionMovilUsuario($usuario);
            $usuario_param = trim($usuario) == '' ? 0 : $usuario;
            $arrGrupos = $objMoviles->obtenerGruposMovilesUsuario(0, '', $usuario_param);
            foreach ($arrGrupos as $tmp) {
                if (strtolower($tmp['gm_nombre']) == strtolower($nuevoGrupo)) {
                    $nuevoGrupoId = $tmp['gm_id'];
                }
            }

            if ($nuevoGrupo !== NULL && $nuevoGrupoId == 0) {
                $nuevoGrupoId = $objMoviles->crearGrupo($nuevoGrupo);
            }
                        
			if ($arrAsignados){
				foreach($arrAsignados as $asignado){
					$asignados[]=$asignado['um_mo_id'];
				}
			}
			$diff=array_diff($arrMoviles,$asignados);
			
			if($diff){
            	foreach($diff as $movil){
                	$objMoviles->insertarAsignacionMovilUsuario($usuario,$movil,$nuevoGrupoId);
                }
			}
		}

		$mensaje = $lang->message->ok->msj_alta;
		if (isset($_GET['method']) && $_GET['method'] == 'ajax') {
			$jsonData['ok']='ok';
			echo json_encode($jsonData);
			die();
		}
		index($objSQLServer, $seccion, $mensaje,true);
	}
	else{
		array_walk($ret['valorCampos'],function(&$v){$v=trim($v,"''");});
		$datosCargados[0]=array_combine($ret['campos'],$ret['valorCampos']);
		alta($objSQLServer, $seccion, $mensaje, $datosCargados);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
