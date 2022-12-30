<?php
class Verificacion{
	var $objSQLServer;

	public function __construct($objSQL){
		$this->objSQL=$objSQL;
	}

	public function iniciarVerificacion($idEquipo,$idGrupoComandos, $idUsuario, $comandoAdicional){
		
		$strSQL = "
			declare @ve_id int
			declare @fecha datetime
			set @fecha = CURRENT_TIMESTAMP
			
			insert into tbl_verificaciones (ve_ug_id, ve_gc_id, ve_fecha_inicio, ve_us_id)
			select top(1) ug_id, ".(int)$idGrupoComandos." , @fecha, ".(int)$idUsuario." 
			from tbl_unidad_gprs where ug_un_id = ".(int)$idEquipo." 
			
			SET @ve_id = (SELECT SCOPE_IDENTITY());
			
			insert into tbl_verificaciones_comandos (vc_ve_id, vc_co_id, vc_ce_id, vc_estado, vc_fecha, vc_respuesta)
			select @ve_id, gc_co_id, 0, 0, @fecha,'' 
			from  tbl_grupo_comando
			inner join tbl_comando on (gc_co_id = co_id)
			where gc_gr_id = ".(int)$idGrupoComandos." 
			
			insert into tbl_verificaciones_comandos (vc_ve_id, vc_co_id, vc_ce_id, vc_estado, vc_fecha,vc_respuesta)
			select @ve_id, co_id, 0, 0, @fecha,'' from tbl_comando
			where co_id = ".(int)$comandoAdicional."
			
			select @ve_id as id ";
			
		$objConsulta=$this->objSQL->dbQuery($strSQL);
		$result=$this->objSQL->dbGetRow($objConsulta,0,3);
		return $result['id'];
	}

	function iniciarComando($idVerificacion, $comando, $tipo){
		
		$strSQL = " 
			declare @ce_id int
			declare @co_id int
			set @co_id = (select isnull(co_id, -1) from tbl_comando where co_id = ".(int)$comando.")
			
			IF @co_id != -1
			BEGIN
				insert into tbl_comando_enviado (ce_ug_id, ce_comando, ce_enviar)
				select ve_ug_id, co_codigo, ".(int)$tipo." from tbl_verificaciones, tbl_comando
				where ve_id = ".(int)$idVerificacion." and co_id=@co_id 
			
				set @ce_id= (SELECT SCOPE_IDENTITY());
			
				update tbl_verificaciones_comandos set vc_ce_id=@ce_id, vc_estado=0, vc_fecha=CURRENT_TIMESTAMP where vc_ve_id = ".(int)$idVerificacion." and vc_co_id=@co_id
				
				update tbl_comando_enviado set ce_ticket=REPLICATE('0',4-LEN(CONVERT(nvarchar(4),@ce_id)))+CONVERT(nvarchar(4),@ce_id) where ce_id=@ce_id
				
				select 1 as res
			END
			ELSE
			BEGIN	
				select 0 as res
			END ";
		
		$result=$this->objSQL->dbQuery($strSQL);
		return $result;
	}

	public function controlarComando($idVerificacion, $comando){
		$strSQL = " 
			DECLARE @resultado varchar(100)
			
			SELECT @resultado = isnull(ce_respuesta,'')
			FROM tbl_comando_enviado 
			INNER JOIN tbl_verificaciones_comandos 
				ON vc_ce_id = ce_id 
				AND ce_tomadoPorModulo = 1
				AND vc_ve_id = ".(int)$idVerificacion."
				AND vc_co_id = ".(int)$comando."
			
			IF @resultado != ''
				UPDATE tbl_verificaciones_comandos SET vc_estado = 1, vc_respuesta = @resultado WHERE vc_ve_id=".(int)$idVerificacion." AND vc_co_id = ".(int)$comando."
			
			SELECT @resultado AS ce_respuesta ";
			 
		$objConsulta=$this->objSQL->dbQuery($strSQL);
		$result=$this->objSQL->dbGetRow($objConsulta,0,3);
		return trim($result['ce_respuesta']);
	}

	public function cancelarComando($idVerificacion, $comando){
		
		$strSQL = " 
			delete from tbl_comando_enviado
			from tbl_verificaciones_comandos where
			ce_id=vc_ce_id and
			vc_ve_id = ".(int)$idVerificacion." and
			vc_co_id = ".(int)$comando." 
			
			update tbl_verificaciones_comandos set vc_estado=2 where vc_ve_id = ".(int)$idVerificacion." and vc_co_id = ".(int)$comando."
			";
		$result = $this->objSQL->dbQuery($strSQL);
		return $result;
	}
	
	public function controlarRespuesta($comando){
		
		$strSQL = " 
			DECLARE @resultado varchar(100)
			
			SELECT @resultado = isnull(co_respuesta_ok,'')
			FROM tbl_comando WITH(NOLOCK) WHERE co_id = ".(int)$comando."
			
			SELECT @resultado AS co_respuesta_ok ";
			
		$objConsulta=$this->objSQL->dbQuery($strSQL);
		$result=$this->objSQL->dbGetRow($objConsulta,0,3);
		return trim($result['co_respuesta_ok']);
	}

}