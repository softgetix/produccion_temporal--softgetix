<?php
class Emailer extends SqlServer{
	
	protected $objSQL;
	function __construct($objSQLServer) {
		$this->objSQL = $objSQLServer;
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
		
		$datos['id_smtp'] = $datos['id_smtp']?$datos['id_smtp']:1;
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
