<?
class Estadisticas {
    private $objSQL;

    function __construct($objSQLServer) {
        $this->objSQL = $objSQLServer;
    }
	
	function getClientes($filtro, $getCantidad = false){
		$strSQL = " SELECT tbl_clientes.*, us_cant_licencias  FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id ";
		$strSQL.= " WHERE cl_borrado = 0 AND us_borrado = 0 AND cl_id_distribuidor = ".$filtro['id_distribuidor'];
		$strSQL.= " AND cl_id NOT IN (".implode(',',$filtro['excluir_clientes']).")";
		$objQuery = $this->objSQL->dbQuery($strSQL);
    	if($getCantidad){
			return $this->objSQL->dbNumRows($objQuery);
		}
		else{
			return $this->objSQL->dbGetAllRows($objQuery, 3);
		}
	}
	
	function getLicenciasVendidas($filtro, $getCantidad = false){
		
    	if($getCantidad){
			$strSQL = " SELECT SUM(us_cant_licencias) as cant ";
		}
		else{
			$strSQL = " SELECT cl_razonSocial, cl_email, us_cant_licencias, cl_fechaAlta ";
		}
		
		$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) On us_cl_id = cl_id ";
		$strSQL.= " WHERE cl_borrado = 0 AND us_borrado = 0 AND cl_id_distribuidor = ".$filtro['id_distribuidor'];
		$strSQL.= " AND cl_id NOT IN (".implode(',',$filtro['excluir_clientes']).")";
		$strSQL.= (!$getCantidad)?" ORDER BY cl_email ":"";
		$objQuery = $this->objSQL->dbQuery($strSQL);
		$rows = $this->objSQL->dbGetAllRows($objQuery, 3);
		
		if($getCantidad){
			return $rows[0]['cant'];
		}
		else{
			return $rows;	
		}
	}	
	
	function getLicenciasActivas($filtro, $getCantidad = false){
		
		$strSQL = " SELECT cl_razonSocial, cl_telefono, cl_email, us_fechaCreado, mo_matricula, mo_marca, mo_fecha_creacion ";
		$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id ";
		$strSQL.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) on um_us_id = us_id ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
		$strSQL.= " WHERE cl_borrado = 0 AND us_borrado = 0 AND mo_borrado = 0 ";
		$strSQL.= " AND cl_id_distribuidor = ".$filtro['id_distribuidor'];
		$strSQL.= " AND cl_id NOT IN (".implode(',',$filtro['excluir_clientes']).")";
		$strSQL.= " ORDER BY cl_email, us_fechaCreado ";
		$objQuery = $this->objSQL->dbQuery($strSQL);
    	if($getCantidad){
			return $this->objSQL->dbNumRows($objQuery);
		}
		else{
			return $this->objSQL->dbGetAllRows($objQuery, 3);
		}
	}
	
	function getLicenciasInactivas($filtro){
		
		$strSQL = " SELECT cl_id, us_cant_licencias, cl_razonSocial, cl_telefono, cl_email, cl_fechaAlta ";
		$strSQL.= " ,(SELECT COUNT(DISTINCT(mo_id))
			FROM tbl_usuarios WITH(NOLOCK)
			INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) on um_us_id = us_id
			INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id
			WHERE us_borrado = 0 AND mo_borrado = 0 AND us_cl_id = cl_id
			GROUP BY us_cl_id) as lic_activas ";
		$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id ";
		$strSQL.= " WHERE cl_borrado = 0 AND us_borrado = 0 AND cl_id_distribuidor = ".$filtro['id_distribuidor'];
		$strSQL.= " AND cl_id NOT IN (".implode(',',$filtro['excluir_clientes']).")";
		$strSQL.= " ORDER BY cl_email ";
		$objQuery = $this->objSQL->dbQuery($strSQL);
		$objRows = $this->objSQL->dbGetAllRows($objQuery, 3);
		
		foreach($objRows as $k => $item){
			if($item['us_cant_licencias'] == $item['lic_activas']){
				unset($objRows[$k]);	
			}	
		}
		return $objRows;
	}
	
	function getPanicosProbados($filtro, $getCantidad = false){
		$strSQL = " SELECT cl_razonSocial, cl_telefono, cl_email, us_fechaCreado, mo_matricula, mo_marca, hp_fecha_recepcion_panico , re_ubicacion ";
		$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id ";
		$strSQL.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) on um_us_id = us_id ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
		$strSQL.= " INNER JOIN tbl_historial_probador_panico WITH(NOLOCK) ON hp_mo_id = mo_id ";
		$strSQL.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = hp_re_id ";
		$strSQL.= " WHERE cl_borrado = 0 AND us_borrado = 0 AND mo_borrado = 0 AND hp_borrado = 0 AND hp_Estado = 1";
		$strSQL.= " AND cl_id_distribuidor = ".$filtro['id_distribuidor'];
		$strSQL.= " AND cl_id NOT IN (".implode(',',$filtro['excluir_clientes']).")";
		$strSQL.= " ORDER BY cl_email, us_fechaCreado ";
		$objQuery = $this->objSQL->dbQuery($strSQL);
    	if($getCantidad){
			return $this->objSQL->dbNumRows($objQuery);
		}
		else{
			return $this->objSQL->dbGetAllRows($objQuery, 3);
		}
	}
	
	function getMovilesSinReportar($filtro, $getCantidad = false){
		$strSQL = " SELECT cl_razonSocial, cl_telefono, cl_email, us_fechaCreado, mo_matricula, mo_marca, sh_fechaGeneracion ";
		$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id ";
		$strSQL.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) on um_us_id = us_id ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
		$strSQL.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id ";
		$strSQL.= " INNER JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";

		$strSQL.= " WHERE cl_borrado = 0 AND us_borrado = 0 AND mo_borrado = 0 ";
		$strSQL.= " AND cl_id_distribuidor = ".$filtro['id_distribuidor'];
		$strSQL.= " AND cl_id NOT IN (".implode(',',$filtro['excluir_clientes']).")";
		//$strSQL.= " AND sh_rd_id IN (76) " //sin reporte +24hs
		$strSQL.= " AND DATEDIFF(DAY, sh_fechaGeneracion, CURRENT_TIMESTAMP) > 30 "; //sin reporte +30 dias
		$strSQL.= " ORDER BY sh_fechaGeneracion, us_fechaCreado, cl_email";
		$objQuery = $this->objSQL->dbQuery($strSQL);
    	if($getCantidad){
			return $this->objSQL->dbNumRows($objQuery);
		}
		else{
			return $this->objSQL->dbGetAllRows($objQuery, 3);
		}
	}

}
?>