<?php
class Emailer extends SqlServer{
	protected $objSQLServer;
	
	function __construct($objSQLServer, $id_smtp = 1, $logo = NULL) {
		$this->objSQL = $objSQLServer;		
		$this->logo = $logo;
		$this->id_smtp = (int)$id_smtp;
		$this->id_template = 1;
	}
	
	function getContenidoHTML($cuerpo_mail){
		if(empty($this->logo)){
			$sql = "SELECT ees_logo FROM tbl_emailer_envios_smtp WHERE ees_id = ".(int)$this->id_smtp;
			$query = $this->objSQL->dbQuery($sql);
			$result = $this->objSQL->dbGetRow($query,0,3);
			$this->logo = $result['ees_logo'];
		}
		
		$sql = "SELECT eet_template FROM tbl_emailer_envios_template WHERE eet_id = ".(int)$this->id_template;
		$query = $this->objSQL->dbQuery($sql);
		$result = $this->objSQL->dbGetRow($query,0,3);
		$template = $result['eet_template'];
		$search = array('{cuerpoppal}','{IMAGEN}');
		$replace = array($cuerpo_mail,$this->logo);
		return str_replace($search, $replace, $template);
	}
	
	function setContenidoEmailer($datos){
		$sql = " INSERT INTO tbl_emailer_envios_contenido(eec_asunto, eec_contenido) ";
		$sql.= " VALUES('".$datos['asunto']."', '".$this->limpiahtml($datos['contenido'])."') ";
		if($this->objSQL->dbQuery($sql)){
			$id_contenido = $this->objSQL->dbLastInsertId();
			return $id_contenido;
		}
		return false;
	}
	
	function setInfoEmailer($datos){
		$datos['id_smtp'] = $this->id_smtp;
		$datos['id_usuario'] = $datos['id_usuario']?$datos['id_usuario']:'NULL';
		$datos['remitente_mail'] = empty($datos['remitente_mail'])?'NULL':"'".$datos['remitente_mail']."'";
		$datos['remitente_name'] = empty($datos['remitente_name'])?'NULL':"'".$datos['remitente_name']."'";
		$datos['responder_a'] = empty($datos['responder_a'])?'NULL':"'".$datos['responder_a']."'";
		$datos['destinatario_name'] = empty($datos['destinatario_name'])?'NULL':"'".$datos['destinatario_name']."'";
		
		$sql = " INSERT INTO tbl_emailer_envios_info(eei_eec_id, eei_ees_id, eei_us_id, eei_remitente_mail, eei_remitente_name, eei_responder_a, eei_destinatario_mail, eei_destinatario_name, eei_prioridad) ";
		$sql.= " VALUES(".(int)$datos['id_contenido'].", ".$datos['id_smtp'].", ".$datos['id_usuario'].", ".$datos['remitente_mail'].", ".$datos['remitente_name'].", ".$datos['responder_a'].", '".$datos['destinatario_mail']."', ".$datos['destinatario_name'].", '".$datos['prioridad']."') ";
		if($this->objSQL->dbQuery($sql)){
			return $this->objSQL->dbLastInsertId();
		}
		return false;
	}
	
	function setMailBCC($idEnvio, $email, $name){
		$sql = " INSERT INTO tbl_emailer_envios_bcc(bcc_eei_id, bcc_email, bcc_name) ";
		$sql.= " VALUES(".(int)$idEnvio.", '".$email."', '".$name."') ";
		if($this->objSQL->dbQuery($sql)){
			return true;
		}
		return false;
	}
	
	function setAdjuntos($idContenido, $adjunto){
		$sql = " INSERT INTO tbl_emailer_envios_adjuntos(eea_eec_id, eea_adjunto) ";
		$sql.= " VALUES(".(int)$idContenido.", '".$adjunto."') ";
		if($this->objSQL->dbQuery($sql)){
			return true;
		}
		return false;
	}
	
	function limpiahtml($html){
		$buscar = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
		$reemplazar = array('>','<','\\1');
		$html = preg_replace($buscar, $reemplazar, $html);
		$html = str_replace("> <", "><", $html);
		return $html;
	}
}
?>
