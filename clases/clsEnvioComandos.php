<?php
class EnvioComando {
	private $objSQL;

	function __construct($objSQLServer) {
		$this->objSQL = $objSQLServer;
	}

	function agregarComando($equipo,$comando){
		$strSQL = " 
			DECLARE @idEquipo VARCHAR(10) = NULL;
			DECLARE @idComando INT = 0;
		
			SELECT @idEquipo = ug_id 
			FROM tbl_unidad_gprs WITH(NOLOCK) 
			INNER JOIN tbl_unidad WITH(NOLOCK) ON ug_un_id = un_id 
			WHERE un_id = ".(int)$equipo." and un_borrado = 0
	
			IF @idEquipo IS NOT NULL
			BEGIN
				INSERT INTO tbl_comando_enviado (ce_ug_id, ce_comando, ce_enviar, ce_ticket) 
				VALUES (@idEquipo, ".$comando.", 1, dbo.CompletarConCero(@idComando,4))	
			END
		";
		if($this->objSQL->dbQuery($strSQL)){
			return true;
		}	
		return false;
	}

	function obtenerEnvioComando(){
		$strSQL = " SELECT ce_id,ce_ug_id, ce_respuesta, ce_ticket,ce_comando, dbo.FormatearFecha(ce_fechaEnviado) ce_fechaEnviado, dbo.FormatearFecha(ce_fechaRespuesta) ce_fechaRespuesta, ug_identificador ";
		$strSQL.= " FROM tbl_comando_enviado WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_unidad_gprs WITH(NOLOCK) ON (ce_ug_id = ug_id) ";
		$objEnvioComando = $this->objSQL->dbQuery($strSQL);
		$arrEnvios = $this->objSQL->dbGetAllRows($objEnvioComando,1);
		return $arrEnvios;
	}
}