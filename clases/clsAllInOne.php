<?php
require_once 'clases/clsAbms.php';

class AllInOne extends Abm {
	
	function __construct($objSQLServer) {
		$this->objSQL = $objSQLServer;
	}
	
	function setClientes($arr_datos){
		
		if($arr_datos['idCliente']){
			$rs['cl_id'] = (int)$arr_datos['idCliente'];
		}
		else{
			$sql = " SELECT TOP 1 cl_id FROM tbl_clientes WITH(NOLOCK) WHERE cl_email = '".$arr_datos['email']."' AND cl_borrado = 0 ";
			$res = $this->objSQL->dbQuery($sql);
			$rs = $this->objSQL->dbGetRow($res);
		}
		
		if($rs['cl_id']){
			$sql = " UPDATE tbl_clientes SET cl_abbr = '".$arr_datos['codigo_usuario']."', cl_razonSocial='".$arr_datos['codigo_usuario']."'";
			$sql.= " ,cl_id_distribuidor = ".(int)$_SESSION['idEmpresa'];
			
			if(!$this->getMoviles($rs['cl_id'])){// Si no tiene moviles dados de alta, puede editar mail cliente.
				$sql.= " ,cl_email = '".$arr_datos['email']."'";
			}
			
			$sql.= " WHERE cl_id = '".$rs['cl_id']."'";
		
			if($this->objSQL->dbQuery($sql)){
				$sql = " UPDATE tbl_usuarios SET us_cant_licencias = ".(int)$arr_datos['cant_licencias']." WHERE us_cl_id = ".(int)$rs['cl_id'];
				if(!$this->objSQL->dbQuery($sql)){
					return false;	
				}
				
				
				//-- Para los casos en que los usuarios recibieron por mail el codigo de validacion
				if($rs['cl_id']){
					
					$sql = " SELECT mo_borrado, COUNT(*) as cant ";
					$sql.= " FROM tbl_usuarios WITH(NOLOCK) ";
					$sql.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_us_id = us_id ";
					$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
					$sql.= " WHERE us_cl_id = ".(int)$rs['cl_id'];
					$sql.= " GROUP BY mo_borrado ";
					$res = $this->objSQL->dbQuery($sql);
					$rs = $this->objSQL->dbGetAllRows($res,3);
					$cantActivos = ($rs[0]['mo_borrado'] == 0)?$rs[0]['cant']:$rs[1]['cant'];
					$cantBorrados = ($rs[0]['mo_borrado'] == 1)?$rs[0]['cant']:$rs[1]['cant'];
					if(!$cantActivos && $cantBorrados){
						$sql_us = " UPDATE tbl_usuarios SET us_borrado = 0 WHERE us_cl_id = ".(int)$rs['cl_id'];
						$this->objSQL->dbQuery($sql_us);
					}
				}
				//-- --//
				return true;
			}
		}
		return false;
	}
	
	function validarCodigoADT($arr_datos){
		
		$sql = " SELECT count(cl_id) cantidad FROM tbl_clientes WITH(NOLOCK) ";
		$sql.= " WHERE cl_borrado = 0 AND cl_abbr = '".$arr_datos['codigo_usuario']."' AND cl_email != '".$arr_datos['email']."' ";
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);
						
		if($rs['cantidad']>0){
			return false;
		}
		return true;
	}
	
	function validarLicencias($arr_datos){
		
		$sql = " SELECT COUNT(mo_id) cant_moviles ";
		$sql.= " FROM tbl_clientes WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id ";
		$sql.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON us_id = um_us_id ";
		$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
		$sql.= " WHERE cl_borrado = 0 AND us_borrado = 0 AND mo_borrado = 0 ";
		if($arr_datos['idCliente']){
			$sql.= " AND cl_id = ".(int)$arr_datos['idCliente']; 
		}
		else{
			$sql.= " AND cl_email = '".$arr_datos['email']."'"; 
		}
		
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);
		
		if($arr_datos['sumarMovil']){
			$rs['cant_moviles'] = $rs['cant_moviles']+1;
		}
						
		if($rs['cant_moviles'] > $arr_datos['cant_licencias']){
			return $rs['cant_moviles'];
		}
		return false;
	}
	
	function getCliente($idCliente){
		$sql = " SELECT cl_email, us_cant_licencias, cl_razonSocial, cl_sms_enviados";
		$sql.= " FROM tbl_clientes WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id ";
		$sql.= " WHERE cl_id = ".(int)$idCliente." AND cl_borrado = 0 ";
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);
		return $rs;	
	}
	
	function getMoviles($idCliente, $idUsuario = NULL){
		$sql = " SELECT  mo_id, mo_matricula, sh_fechaRecepcion, mo_fecha_creacion, mo_borrado ";
		$sql.= " ,(SELECT us_estado FROM tbl_unidad_servicios WITH(NOLOCK) WHERE us_un_id = un_id AND us_ust_id = 1) as 'panic_atajo'
				,(SELECT us_estado FROM tbl_unidad_servicios WITH(NOLOCK) WHERE us_un_id = un_id AND us_ust_id = 4) as 'panic_app'
				,(SELECT us_estado FROM tbl_unidad_servicios WITH(NOLOCK) WHERE us_un_id = un_id AND us_ust_id = 5) as 'panic_retardo'
				,(SELECT us_estado FROM tbl_unidad_servicios WITH(NOLOCK) WHERE us_un_id = un_id AND us_ust_id = 6) as 'medica'
				,(SELECT us_estado FROM tbl_unidad_servicios WITH(NOLOCK) WHERE us_un_id = un_id AND us_ust_id = 7) as 'incendio'
				,(SELECT us_estado FROM tbl_unidad_servicios WITH(NOLOCK) WHERE us_un_id = un_id AND us_ust_id = 8) as 'adt_acompana'
				,(SELECT us_estado FROM tbl_unidad_servicios WITH(NOLOCK) WHERE us_un_id = un_id AND us_ust_id = 9) as 'acceso_sistema'
				";
		$sql.= " FROM tbl_clientes WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios WITH(NOLOCK) on us_cl_id = cl_id ";
		$sql.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_us_id = us_id ";
		$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
		$sql.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id ";
		$sql.= " INNER JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		$sql.= " WHERE cl_borrado = 0 AND cl_id = ".(int)$idCliente;
		if($idUsuario){
			$sql.= " AND us_id = ".(int)$idUsuario;	
		}
		
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetAllRows($res,3);
		return $rs;	
	}
	
	function setEstadoMovil($idMovil,$idCliente, $borrado){
		
		if(!$borrado){
			$sql = " SELECT us_cant_licencias FROM tbl_usuarios WITH(NOLOCK) WHERE us_cl_id = ".(int)$idCliente;
			$res = $this->objSQL->dbQuery($sql);
			$rs = $this->objSQL->dbGetRow($res,0,3);
			
			$arr_datos['cant_licencias'] = $rs['us_cant_licencias'];
			$arr_datos['idCliente'] = $idCliente;
			$arr_datos['sumarMovil'] = 1;
			if($this->validarLicencias($arr_datos)){		
				return 'error';
			}
		}
		
		$sql = " UPDATE tbl_moviles SET mo_borrado = ".$borrado;
		$sql.= " WHERE mo_id = ".(int)$idMovil." AND mo_id_cliente_facturar = ".(int)$idCliente;
		if($this->objSQL->dbQuery($sql)){
			$sql = " UPDATE tbl_unidad SET un_borrado = ".$borrado;
			$sql.= " WHERE un_mo_id = ".(int)$idMovil;
			if($this->objSQL->dbQuery($sql)){
				return true;
			}
		}
		
		return false;
	}
	
	function setServicioUnidad($idMovil,$idServicio, $estado){
	
		$sql = " SELECT un_id FROM tbl_unidad WITH(NOLOCK) WHERE un_mo_id = ".(int)$idMovil;
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);
		$idUnidad = $rs['un_id'];
		
		if($idUnidad){
			$sql = " SELECT us_id FROM tbl_unidad_servicios WITH(NOLOCK) WHERE us_un_id = ".(int)$idUnidad." AND us_ust_id = ".(int)$idServicio;
			$res = $this->objSQL->dbQuery($sql);
			$rs = $this->objSQL->dbGetRow($res,0,3);
			if($rs['us_id']){
				$sql = " UPDATE tbl_unidad_servicios SET us_estado = ".(int)$estado;
				$sql.= " WHERE us_id = ".(int)$rs['us_id'];
			}
			else{
				$sql = " INSERT INTO tbl_unidad_servicios (us_un_id,us_ust_id,us_estado) ";
				$sql.= " VALUES(".(int)$idUnidad.",".(int)$idServicio.",".(int)$estado.")";
			}
			
			if($this->objSQL->dbQuery($sql)){
				return true;
			}
		}
		return false;
	}
	
	
	function enviarSMS($datos){
		$strSQL = " INSERT INTO tbl_buffer_out_sms(bos_un_id,bos_us_id,bos_nro_telefono,bos_mensaje,bos_medio) ";
		$strSQL.= " VALUES(".(int)$datos['un_id'].",".(int)$datos['us_id'].",".(int)$datos['nro_tel'].",'".$datos['mensaje']."','".$datos['medio']."')"; 
		$this->objSQL->dbQuery($strSQL);
	}
	
	function sumarSMS($idCliente){
		$strSQL = " UPDATE tbl_clientes SET cl_sms_enviados = (CASE WHEN cl_sms_enviados IS NULL  THEN 1 ELSE cl_sms_enviados+1 END) ";
		$strSQL.= " WHERE cl_id = ".(int)$idCliente; 
		$this->objSQL->dbQuery($strSQL);
	}
	
}
?>