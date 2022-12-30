<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

error_reporting(0);	
function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	//$method 	= (isset($_GET['method'])) ? $_GET['method'] : NULL;
	$action = isset($_GET['action']) ? $_GET['action'] : 'listar';
	require_once 'clases/clsAllInOne.php';
	$clsAllInOne = new AllInOne($objSQLServer);
	$_POST['sms_max'] = 20;
					
	if($_GET['idCliente']){
		$arr_cliente = $clsAllInOne->getCliente($_GET['idCliente']);
		$arrMoviles = $clsAllInOne->getMoviles($_GET['idCliente']);
		if(!$arrMoviles){// Si no tiene moviles dados de alta, puede editar mail cliente.
			$_POST['editMail'] = true;
		}
		//$_POST['idCliente'] = (int)$_GET['idCliente'];
		$_POST['codigo_usuario'] = $arr_cliente['cl_razonSocial'];
		$_POST['email'] = $arr_cliente['cl_email'];
		$_POST['cant_licencias'] = $arr_cliente['us_cant_licencias'];
		$_POST['sms_enviados'] = $arr_cliente['cl_sms_enviados'];
	}
	elseif(isset($_POST['generar'])){
		if(!empty($_POST['email']) && !empty($_POST['codigo_usuario'])){
			if((int)$_POST['cant_licencias'] > 0 && (int)$_POST['cant_licencias'] <= 4){
				if(validarEmail($_POST['email'])){
					
					if(($clsAllInOne->validarCodigoADT($_POST) && $_POST['editMail'] == false) || $_POST['editMail'] == true){
						$resp = $clsAllInOne->validarLicencias($_POST);			
						if(!$resp){
							$resp = $clsAllInOne->setClientes($_POST);
							if($resp){
								$codigoValidacion = generarCodigoValidacion($_POST['email']);
								//$_POST['codigo_usuario'] = '';
								
								//-- --//
								$arrTelefonos = explode(',',$_POST['nro_cel']);
								if(is_array($arrTelefonos) && !empty($arrTelefonos[0])){
									if((count($arrTelefonos) + (int)$_POST['sms_enviados']) <= $_POST['sms_max']){
										foreach($arrTelefonos as $item){
											if((int)$item){
												$datos['un_id'] = trim($item);
												$datos['us_id'] = $_SESSION['idUsuario'];
												$datos['nro_tel'] = trim($item);
												$datos['mensaje'] = str_replace('[CODIGO-VALIDACION]',$codigoValidacion,$lang->system->msg_descarga_findu);
												$datos['medio'] = 'SMS';
												$clsAllInOne->enviarSMS($datos);
												$clsAllInOne->sumarSMS($_POST['idCliente']);
											}
										}
									}
									else{
										$mensaje = $lang->system->msg_sms_supero_cant_envios;
									}
								}
								//-- --//
							}
							else{$mensaje = $lang->system->msg_codigo_no_generado;}
						}
						else{$mensaje = str_replace('[CANT-LICENCIAS]',$resp,$lang->system->msg_cant_licencias_error);}
					}
					else{$mensaje = $lang->system->msg_nro_entidad_error;}				
				}
				else{$mensaje = $lang->system->msg_email_error;}
			}
			else{$mensaje = $lang->system->msg_cant_licencias_permitidas;}
		}
		else{$mensaje = $lang->system->msg_datois_obligatorios_error;}
	}
		
	if ($action!='popup') {
        require("includes/template.php");
    } else {
		$extraCSS[] = 'css/estilosAbmPopup.css';
        $extraCSS[] = 'css/popup.css';
		$extraCSS[] = 'css/estilosABMDefault.css';
        $extraJS[] = 'js/popupFunciones.js?1';
		$extraJS[] = 'js/jquery.blockUI.js';
        $popup = true;
		$operacion = 'Listar';
		$tipoBotonera = 'AM';
        require("includes/frametemplate.php");
    }
	
}
?>