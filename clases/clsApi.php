<?php
class Api{	
	function __construct($objSQLServer, $idCliente, $server){
		$this->clave = ($server=='produccion')?'local':'testing';
		$this->idCliente = (int)$idCliente;
		$this->objSQL = $objSQLServer;
	}
	
	function generarCredenciales(){
		$txt = str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
		
		$arrTemp[0] = str_pad(strlen($this->idCliente ), 2, 0, STR_PAD_LEFT); 
		$arrTemp[1] = substr($txt, rand(12,57), 5);
		$arrTemp[2] = str_pad($this->idCliente , 12, substr($txt, 0, 12), STR_PAD_LEFT); 
		$arrTemp[3] = substr($txt, rand(1,47), 15);
		$arrTemp[4] = substr(md5($this->clave.$this->idCliente ),0,5);
		
		$return['clientID'] = base64_encode($arrTemp[0].$arrTemp[1].$arrTemp[2].$arrTemp[3].$arrTemp[4]);
		$return['clientSecret']  = md5($arrTemp[1].$arrTemp[3]);	
		return $return;
	}
	
	function setCredenciales($arr){
		$params['cl_clientID'] = $arr['cl_clientID'];
		$params['cl_clientSecret'] = $arr['cl_clientSecret'];
		return $this->objSQL->dbQueryUpdate($params, 'tbl_clientes', 'cl_id = '.$this->idCliente);	
	}
	
	function getDatosCliente(){
		$strSQL = " SELECT TOP 1 cl_razonSocial, cl_email, cl_pai_id, cl_pr_id, cl_id_distribuidor, cl_tipo, cl_abbr, cl_tipo_cliente
				,cl_paquete, cl_cant_dadores, us_id, us_nombreUsuario, us_pass, us_nombre, us_apellido, us_cl_id, us_usuarioCreador, us_pe_id ";
		$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON cl_id = us_cl_id ";
		$strSQL.= " WHERE cl_id = ".$this->idCliente." AND us_pe_id IN (5,9,13) AND us_borrado = 0 ";
		$strSQL.= " ORDER BY us_id ASC ";
		$objRes = $this->objSQL->dbQuery($strSQL);
		$resRow = $this->objSQL->dbGetRow($objRes, 0,3);
		return $resRow;	
	}
	
	function createClientTesting($arrCliente, $credenciales){
		if($arrCliente){
			$this->objSQL->dbQuery("SET IDENTITY_INSERT tbl_clientes ON");
			$params = array(
				'cl_id' => $this->idCliente
				,'cl_razonSocial'=>$arrCliente['cl_razonSocial'] 
				,'cl_email'=>$arrCliente['cl_email']
				,'cl_pai_id'=>$arrCliente['cl_pai_id']
				,'cl_pr_id'=>$arrCliente['cl_pr_id']
				,'cl_id_distribuidor'=>$arrCliente['cl_id_distribuidor']
				,'cl_tipo'=>$arrCliente['cl_tipo']
				,'cl_abbr'=>$arrCliente['cl_abbr']
				,'cl_tipo_cliente'=>$arrCliente['cl_tipo_cliente']
				,'cl_paquete'=>$arrCliente['cl_paquete']
				,'cl_cant_dadores'=>$arrCliente['cl_cant_dadores']
				,'cl_clientID'=>$credenciales['clientID']
				,'cl_clientSecret'=>$credenciales['clientSecret']
			);
			if($this->objSQL->dbQueryInsert($params, 'tbl_clientes')){
				$this->objSQL->dbQuery("SET IDENTITY_INSERT tbl_clientes OFF");
				$this->objSQL->dbQuery("SET IDENTITY_INSERT tbl_usuarios ON");
				$params = array(
					'us_id'=>$arrCliente['us_id'] 
					,'us_nombreUsuario'=>$arrCliente['us_nombreUsuario'] 
					,'us_pass'=>$arrCliente['us_pass']
					,'us_nombre'=>$arrCliente['us_nombre']
					,'us_apellido'=>$arrCliente['us_apellido']
					,'us_cl_id'=>$arrCliente['us_cl_id']
					,'us_usuarioCreador'=>$arrCliente['us_usuarioCreador']
					,'us_pe_id'=>$arrCliente['us_pe_id']
				);
				$this->objSQL->dbQueryInsert($params, 'tbl_usuarios'); 
				$this->objSQL->dbQuery("SET IDENTITY_INSERT tbl_usuarios OFF");
				return true;
			}
			$this->objSQL->dbQuery("SET IDENTITY_INSERT tbl_clientes OFF");
		}
		return false;
	}
	
	function createUserPerfilSatelital($email){
		$strSQL = " SELECT us_id, us_pe_id FROM tbl_usuarios WHERE us_nombreUsuario = '".$email."' AND us_borrado = 0 ";
		$objRes = $this->objSQL->dbQuery($strSQL);
		$resRow = $this->objSQL->dbGetRow($objRes, 0,3);
		if($resRow['us_id']){
			if($resRow['us_pe_id'] == 20){
				return $resRow['us_id'];	
			}
			else{
				return false;	
			}
		}
		else{
			$aux = explode('@',$mail);
			$params = array(
				'us_nombreUsuario'=>$email
				,'us_pass'=> md5('nueva_pass_satelital_webservices')
				,'us_nombre'=>$aux[0]
				//,'us_apellido'=>
				,'us_cl_id'=>$this->idCliente
				,'us_usuarioCreador'=>$_SESSION['idUsuario']
				,'us_pe_id'=>20
			);
			return $this->objSQL->dbQueryInsert($params, 'tbl_usuarios');
		}
	}
	
	function generarEnvio($arrSend){
		global $lang;
		global $objIdioma;
		global $hosting_testing;
		$envioEmail = false;
		require_once ('clases/clsEmailer.php');
		$objEmailer = new Emailer($this->objSQL,1);
		
		//require_once('clases/clsIdiomas.php');
		//$objIdioma = new Idioma();
		
		$idioma = !empty($_SESSION['idioma'])?$_SESSION['idioma']:getIdiomaBrowser();
		$langEmail = $objIdioma->getEmails($idioma);
		
		$arrValid = codificarURL($arrSend['idUsuario']);
		$url_encode = $arrValid['url_encode'];
		$code = $arrValid['reset_code'];
		$link_activacion = 'https://www.localizar-t.com:81/localizart/cambiarPass.php?ref='.$url_encode;
		
		$msg = $langEmail->cuenta_satelital->data;
		$msg = str_replace('[USUARIO]',$arrSend['nombre'], $msg);
		$msg = str_replace('[LINK_ACTIVACION]',$link_activacion, $msg);
		$msg = str_replace('[CLIENT_ID]',$arrSend['clientID'], $msg);
		$msg = str_replace('[CLIENT_SECRET]',$arrSend['clientSecret'], $msg);
		$msg = idiomaHTML($msg);
			
		$emailerMsg['asunto'] = $langEmail->cuenta_satelital->subject;
		$emailerMsg['contenido'] = decode($objEmailer->getContenidoHTML($msg));			
						
		$id_contenido = $objEmailer->setContenidoEmailer($emailerMsg);
		if($id_contenido){
			foreach($arrSend['fileAdjunto'] as $file){
				$adjunto = '../emailer/adjuntos/'.$file;
				copy('templates/'.$file,$adjunto);
				$objEmailer->setAdjuntos($id_contenido, '../'.$adjunto);
			}
			
			$emailerInfo['id_contenido'] = $id_contenido;
			$emailerInfo['id_usuario'] = $arrSend['idUsuario'];
			$emailerInfo['destinatario_mail'] = $arrSend['email'];
			$emailerInfo['destinatario_name'] = $arrSend['nombre'];
			$emailerInfo['prioridad'] = 5;	
			if($objEmailer->setInfoEmailer($emailerInfo)){
				$envioEmail = true;	
			}
		}
		return $envioEmail;
	}
}