<?php
function index($objSQLServer, $seccion, $mensaje=NULL) {
	global $lang;
	require_once 'clases/clsMoviles.php';
    $objMovil = new Movil($objSQLServer);
	
	$arrMoviles = $objMovil->obtenerRegistros(NULL,'getAllReg',NULL,NULL,NULL,$_SESSION['idEmpresa']);
	
	require_once 'clases/clsViajes.php';
    $objViaje = new Viajes($objSQLServer);
	
	$fecha = isset($_POST['fecha'])?date('d-m-Y', strtotime($_POST['fecha'])):getFechaServer('d-m-Y');
	$arrDisponibilidad = $objViaje->getDisponibilidadTransportistas($fecha);
	
	$arrMovilesDisponibilidad = array();
	$checkAllSelect = true;
	foreach($arrMoviles as $movil){
		$auxDisponibilidad = false;
		foreach($arrDisponibilidad as $disponibilidad){
			if($movil['mo_id'] == $disponibilidad['vdtm_mo_id']){
				$auxDisponibilidad = true;
				break;
			}
		}
		$checkAllSelect = (!$auxDisponibilidad)?false:$checkAllSelect;
		array_push($arrMovilesDisponibilidad, array('idMovil' => $movil['mo_id'], 'movil' => $movil['mo_matricula'], 'disponibilidad' => $auxDisponibilidad));
	}
	$checkDisabled = (strtotime($fecha) < strtotime(getFechaServer('d-m-Y')))?true:false;

	$extraCSS[] = 'css/ui/jquery.ui.datepicker.css';
	$extraJS[] = 'js/calendario.js';
	$extraJS[] = 'js/jquery/jquery.datepicker.js';
	include_once('includes/template.php');
} 

function guardarDisponibilidad($objSQLServer, $seccion, $mensaje = NULL){
	global $lang;
	require_once 'clases/clsViajes.php';
    $objViaje = new Viajes($objSQLServer);
	
	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
	$arrCliente = $objCliente->obtenerRegistros($_SESSION['idEmpresa']);
	
	
	if($_POST['movil']){
		require_once 'clases/clsMoviles.php';
		$objMovil = new Movil($objSQLServer);
		$arrMoviles = $objMovil->obtenerMatriculas(implode(',',$_POST['movil']));
		$coma = '';
		foreach($arrMoviles as $item){
			$txtAux.= $coma.'<strong style="font-family:Arial,sans-serif;color:#404040;font-size:10.0pt;">'.$item['mo_matricula'].'</strong>';
			$coma = ', ';
		}
	
		$mensajeMail = ' <p style="font-family:Arial,sans-serif;color:#404040;font-weight:normal;font-size:10.0pt;">';
		$mensajeMail.= ' El transportista '.$arrCliente[0]['cl_razonSocial'].' ha actualizado la disponibilidad de '.$txtAux.' para el '.$_POST['fecha'];
		$mensajeMail.= ' </p>';
	}
	else{
		$mensajeMail = ' <p style="font-family:Arial,sans-serif;color:#404040;font-weight:normal;font-size:10.0pt;">';
		$mensajeMail.= ' El transportista '.$arrCliente[0]['cl_razonSocial'].' no tiene disponibilidad para el '.$_POST['fecha'];
		$mensajeMail.= ' </p>';
	}
	
	if($objViaje->setDisponibilidadTransportistas($_POST['fecha'], $_POST['movil'])){
		$mensaje = $lang->message->ok->msj_modificar;
		
		//-- --//
		require ('clases/clsEmailer.php');
		$objEmailer = new Emailer($objSQLServer);
					
		$emailerMsg = array();
		$emailerMsg['asunto'] = 'Disponibilidad de '.$arrCliente[0]['cl_razonSocial'].' para el '.$_POST['fecha'];
		$emailerMsg['contenido'] = decode($objEmailer->getContenidoHTML($mensajeMail));
		
		$id_contenido = $objEmailer->setContenidoEmailer($emailerMsg);
					
		if($id_contenido){
			$objUsuario = new Usuario($objSQLServer);
			$arrUsuarios = $objUsuario->obtenerUsuariosPorPerfil(array(6,10), $_SESSION['idAgente']);
			foreach($arrUsuarios as $usuario){
				$emailerInfo = array();
				$emailerInfo['id_contenido'] = $id_contenido;
				$emailerInfo['id_usuario'] = (int)$usuario['us_id'];
				$emailerInfo['destinatario_mail'] = $usuario['us_nombreUsuario'];
				$emailerInfo['destinatario_name'] = trim(encode($usuario['us_nombre'].(!empty($usuario['us_apellido'])?(' '.$usuario['us_apellido']):'')));
				$emailerInfo['prioridad'] = 4;	
				$objEmailer->setInfoEmailer($emailerInfo);
			}
		}
		//-- --//		
	}
	else{
		$mensaje = $lang->message->error->msj_modificar;
	}
	index($objSQLServer, $seccion, $mensaje);
}
?>