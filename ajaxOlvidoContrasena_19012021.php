<?php
session_start();
$_GET['config'] = strrev($_POST['config']);
include('includes/config_clientes.php');
include "includes/funciones.php";
$arr_resp = array();

switch($_POST['action']){
	case 'enviar_mail':
		include ('clases/clsIdiomas.php');
		$objIdioma = new Idioma();
		$lang = $objIdioma->getIdiomas($_SESSION['idioma']);

		if(validarEmail($_POST['mail'])){
			include "includes/conn.php";
			include "clases/clsUsuarios.php";
			$objUsuario = new Usuario($objSQLServer);
			$arr_usuario = $objUsuario->obtenerUsuarioPorMail($_POST['mail']);
			
			$enviarEmail = false;
			if(@$_POST['ignorar_reset_previo']){// Se obliga a enviar mail de reset desde el panel AllInOne
				$enviarEmail = true;	
			}
			elseif($arr_usuario['us_id']){
				$reset_count = 0;
				if(strtotime(date('Y-m-d',strtotime($arr_usuario['us_reset_date']))) < strtotime(date('Y-m-d'))){
					//La ultima solicitud de reset es MENOR al día dactual//
					$reset_count = 1;
					$enviarEmail = true;
				}
				elseif($arr_usuario['us_reset_count'] < 3){
					//La ultima solicitud de reset es IGUAL al día actual y la cant de reset es menor a 3//
					$reset_count = (int)$arr_usuario['us_reset_count'] + 1;
					$enviarEmail = true;
				}
				else{
					$arr_resp['error'] = $lang->message->recupero_supero_limite->__toString();
					echo json_encode($arr_resp);
					exit;
				}
			}
			else{
				$arr_resp['ok'] = true; //-- Si no encuentra mail registrada, devuelve todo ok --//
			}
			
			if($enviarEmail){
				$idioma = !empty($_SESSION['idioma'])?$_SESSION['idioma']:getIdiomaBrowser();
				$langEmail = $objIdioma->getEmails($idioma);
				
				if(file_exists('includes/config_'.$_POST['config'].'.php')){
					require('includes/config_'.$_POST['config'].'.php');
				}
				else{
					require('includes/config_conexion.php');
				}		
				
				$arrValid = codificarURL($arr_usuario['us_id']);
				$url_encode = $arrValid['url_encode'];
				$code = $arrValid['reset_code'];
				$link_activacion = $_POST['config'].'/cambiarPass.php?ref='.$url_encode;
				/*
				$code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
				$url = 'idUsuario='.$arr_usuario['us_id'].'&reset_code='.$code;
				$palabra_clave = substr(md5($code),0,5);//-- se genera palabra clave de 5 caracteres con md5 x seguridad. --//
				$url_encode = base64_encode($url).$palabra_clave;
				$link_activacion = $_POST['config'].'/cambiarPass.php?ref='.$url_encode;
				*/
				$cl_msg = empty($langEmail->recupero_password->$_POST['config']->data)?'localizart':$_POST['config'];
				$mensaje = $langEmail->recupero_password->$cl_msg->data;
				$mensaje = str_replace('[USUARIO]',$arr_usuario['us_nombre'], $mensaje);
				$mensaje = str_replace('[LINK_ACTIVACION]',$link_activacion, $mensaje);
				$mensaje = idiomaHTML($mensaje);
				
				//-- --//
				$envioEmail = false;
				require ('clases/clsEmailer.php');
				$objEmailer = new Emailer($objSQLServer, $arrMail['smtp_envio'], $arrMail['logo']?$arrMail['logo']:NULL);
				
				$emailerMsg['asunto'] = ($_POST['ignorar_reset_previo']?'Mesa de Soporte - ':'').$langEmail->recupero_password->$cl_msg->subject;
				$emailerMsg['contenido'] = decode($objEmailer->getContenidoHTML($mensaje));
				
				$id_contenido = $objEmailer->setContenidoEmailer($emailerMsg);
				if($id_contenido){
					$emailerInfo['id_contenido'] = $id_contenido;
					$emailerInfo['id_usuario'] = $arr_usuario['us_id'];
					$emailerInfo['remitente_mail'] = $arrMail['remitente'];
					$emailerInfo['remitente_name'] = $arrMail['nombre_remitente'];
					$emailerInfo['responder_a'] = $arrMail['responder_a'];
					$emailerInfo['destinatario_mail'] = $_POST['mail'];
					$emailerInfo['destinatario_name'] = $arr_usuario['us_nombre'];
					$emailerInfo['prioridad'] = 1;	
					if($objEmailer->setInfoEmailer($emailerInfo)){
						$envioEmail = true;	
					}
				}
				
				if($envioEmail){
					$objUsuario->habilitarCambioPassword($arr_usuario['us_id'], $reset_count, $code);		
					$arr_resp['ok'] = true;
				}
				else{
					$arr_resp['error'] = $lang->message->error->email_enviado->__toString();	
				}
				//-- --//
			}
		}
		else{
			$arr_resp['error'] = $lang->message->email_incorrecto->__toString();	
		}
		echo json_encode($arr_resp);
		
	break;
	
	
	
}
?>