<?php

require_once 'clases/clsAbms.php';
class Viajes extends Abm{

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'kccinter_viajes','al');
	}
	
	function obtenerRegistros($id){
		$strSQL = " SELECT vi.vi_id, us.us_nombreUsuario,vi.* ";
		$strSQL.= " FROM tbl_viajes vi WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios us WITH(NOLOCK) ON (us.us_id = vi.vi_us_id) ";
		$strSQL = " WHERE vi_id = ".(int)$id;
		$objRegistros = $this->objSQL->dbQuery($strSQL);
		$arrRegistros = $this->objSQL->dbGetAllRows($objRegistros,3);
		return $arrRegistros;
	}
		
	function modificarViaje($vi_id,$sqlQuery){
		$res=$this->objSQL->dbQuery($sqlQuery);
		return $res;
	}
	
	function insertarDestino($campos, $valores){
		$strSQL="INSERT INTO kccinter_viajes_destinos ({$campos}) VALUES ({$valores})";
		$this->objSQL->dbQuery($strSQL);
	}
	
	function obtenerDatos($idMovil = '',$inicio = '',$fin = '',$idReferencia = 0){
		if($inicio == '') $inicio = date("Y/m/d");
		if($fin == '') $fin = date("Y/m/d");
		$inicio = $inicio.' 00:00:00';
		$fin = $fin.' 23:59:59';
		
		$strSQL = " select DISTINCT vi_id, vi_codigo, mo.mo_matricula as vi_mo_id,re_nombre as vd_re_id
				,dbo.FormatearFecha(vd_ini) as vd_ini, dbo.FormatearFecha(vd_fin) as vd_fin, dbo.FormatearFecha(vd_ini_real) as vd_ini_real
				,dbo.FormatearFecha(vd_fin_real) as vd_fin_real, DATEDIFF(ss, vd_ini, vd_ini_real) as diferenciaIngreso
				,DATEDIFF(ss, vd_fin, vd_fin_real) as diferenciaEgreso, vd.vd_orden, mo.mo_otros ";
		$strSQL.= " , us.us_nombreUsuario as us_nombreUsuario ";
		$strSQL.= " ,CAST(
             		CASE 
                  		WHEN vi_us_id = ".$_SESSION["idUsuario"]." THEN 1 
                  		ELSE 0 
             		END AS bit) as editable ";
		//$strSQL.= " , 1 as editable ";
		$strSQL.= " FROM kccinter_viajes vi WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_usuarios us WITH(NOLOCK) ON (us.us_id = vi.vi_us_id) ";
		$strSQL.= " LEFT JOIN tbl_moviles mo WITH(NOLOCK) ON (mo.mo_id = vi.vi_mo_id) ";
		$strSQL.= " INNER JOIN kccinter_viajes_destinos vd WITH(NOLOCK) ON (vd.vd_vi_id = vi.vi_id) ";
		$strSQL.= " INNER JOIN tbl_referencias re WITH(NOLOCK) ON (re.re_id = vd.vd_re_id) ";
		$strSQL.= " WHERE vi_borrado=0 
				AND ((vd_ini > '".$inicio."' AND vd_ini < '".$fin."') OR (vd_ini_real > '".$inicio."' AND vd_ini_real < '".$fin."') 
				OR (vd_creado > '".$inicio."' AND vd_creado < '".$fin."'  AND vd_ini IS null))
				AND vi_mo_id= case ".(int)$idMovil." when 0 then vi_mo_id else ".(int)$idMovil." end
				AND vd_re_id= case ".(int)$idReferencia." when 0 then vd_re_id else ".(int)$idReferencia." end ";
		$strSQL.= " ORDER BY vi_id ";
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$res = $this->objSQL->dbGetAllRows($objRes, 3);
		return $res;
	}
	
	function obtenerArribos($inicio,$fin){
		
		$strSQL = " SELECT mo.mo_matricula as Vehiculo ,re.re_id as IdReferencia,re.re_nombre as NombreCorto, vi.vi_codigo as Nombre
				,dbo.unix_timestamp(vd.vd_ini) as FechaProgramada, dbo.unix_timestamp(vd.vd_ini_real) as ev_fecha, null AS TiempoEstadia
				,vd.vd_id, sh.sh_latitud, sh.sh_longitud, rc.rc_latitud, rc.rc_longitud, cl.cl_razonSocial as RazonSocial
				, sh.sh_fechaRecepcion as UltimoReporte, vi.vi_observaciones as Observaciones ";
		$strSQL.= " FROM kccinter_viajes vi WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios us WITH(NOLOCK) ON us.us_id = vi.vi_us_id ";
		$strSQL.= " INNER JOIN tbl_moviles mo WITH(NOLOCK) ON mo.mo_id = vi.vi_mo_id ";
		$strSQL.= " LEFT JOIN tbl_unidad un WITH(NOLOCK) ON mo.mo_id = un.un_mo_id ";
		$strSQL.= " LEFT JOIN tbl_sys_heart sh WITH(NOLOCK) ON sh.sh_un_id = un.un_id ";
		$strSQL.= " INNER JOIN kccinter_viajes_destinos vd WITH(NOLOCK) ON vd.vd_vi_id = vi.vi_id ";
		$strSQL.= " INNER JOIN tbl_referencias re WITH(NOLOCK) ON re.re_id = vd.vd_re_id ";
		$strSQL.= " INNER JOIN tbl_referencias_coordenadas rc WITH(NOLOCK) ON re.re_id = rc.rc_re_id ";
		$strSQL.= " LEFT JOIN kccinter_viajes_destinos vd2 WITH(NOLOCK) ON vd2.vd_vi_id = vd.vd_vi_id AND (vd.vd_id) - 1 = vd2.vd_id ";
		$strSQL.= " LEFT JOIN tbl_referencias re2 WITH(NOLOCK) ON vd2.vd_re_id = re2.re_id ";
		$strSQL.= " INNER JOIN tbl_clientes cl WITH(NOLOCK) ON cl.cl_id = mo.mo_id_cliente_facturar ";
		$strSQL.= " WHERE vi_borrado = 0 AND re.re_tr_id = 1
				AND vd.vd_id =	(SELECT MIN(vd_id) FROM kccinter_viajes_destinos	WHERE vd_vi_id = vi_id AND vd_fin_real IS NULL)
				AND vd.vd_ini_real is null 
				AND ((vd.vd_ini > '".$inicio."' AND vd.vd_ini < '".$fin."') OR (vd.vd_creado > '".$inicio."' AND vd.vd_creado < '".$fin."')) ";
		$strSQL.= " ORDER BY vd.vd_orden ASC ";
		
		$objRes = $this->objSQL->dbQuery($strSQL);
		$res = $this->objSQL->dbGetAllRows($objRes, 3);
		return $res;
	}
	
	function obtenerCondutores(){
		$strSQL = " SELECT co.*,cl.cl_razonSocial as razon_social ";
		$strSQL.= " FROM tbl_conductores co WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_clientes cl WITH(NOLOCK) ON (cl.cl_id = co.co_cl_id) ";
		$strSQL.= " WHERE co_borrado = 0 AND co_cl_id = ".(int)$_SESSION["idEmpresa"];
		$strSQL.= " ORDER BY co_nombre, co_apellido, razon_social ";
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$res = $this->objSQL->dbGetAllRows($objRes, 3);
		return $res;
	}
	
	function obtenerDestinos($vi_id){
		$strSQL = " SELECT * FROM kccinter_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = ".(int)$vi_id;
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$res=$this->objSQL->dbGetAllRows($objRes, 3);
		return $res;
	}
	
	function borrarViajes($vi_id){
		$strSQL = "UPDATE kccinter_viajes SET vi_borrado='1' WHERE vi_id = ".(int)$vi_id;
		$this->objSQL->dbQuery($strSQL);			
	}
	
	function borrarDestinos($vi_id){
		$strSQL = "DELETE FROM kccinter_viajes_destinos WHERE vd_vi_id = ".(int)$vi_id." AND vd_ini_real is NULL AND vd_fin_real is NULL ";
		$this->objSQL->dbQuery($strSQL);			
	}
}
