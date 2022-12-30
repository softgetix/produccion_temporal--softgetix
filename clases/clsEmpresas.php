<?php
require_once 'clases/clsAbms.php';
class Empresa extends Abm{

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_clientes','cl');
	}

	function obtenerRegistros($id=0, $filtro = NULL, $idEmpresa=0){
		$strSQL = "	SELECT 
			clientes.cl_id, clientes.cl_razonSocial , clientes.cl_cuit, clientes.cl_telefono, 
			clientes.cl_fax, clientes.cl_email, clientes.cl_direccion, clientes.cl_direccion_nro, clientes.cl_direccion_piso, clientes.cl_direccion_dpto,
			clientes.cl_habilitado, clientes.cl_id_distribuidor, usuarios.us_nombreUsuario, 
			clientes.cl_pai_id, tbl_pais.pa_nombre,	clientes.cl_pr_id, pr_nombre, clientes.cl_localidad, 
			clientes.cl_tipo, clientes2.cl_razonSocial distribuidor, 
			clientes.cl_ibrutos, clientes.cl_iva, clientes.cl_cpostal, clientes.cl_abbr ";
		$strSQL.= "	FROM tbl_clientes clientes WITH(NOLOCK) ";
		$strSQL.= "	LEFT JOIN tbl_pais WITH(NOLOCK) on (clientes.cl_pai_id = tbl_pais.pa_id) ";
		$strSQL.= "	LEFT JOIN tbl_provincias WITH(NOLOCK) on (clientes.cl_pr_id = pr_id) ";
		$strSQL.= "	LEFT JOIN tbl_usuarios usuarios	WITH(NOLOCK) ON (cl_id_distribuidor = us_id) ";
		$strSQL.= "	LEFT JOIN tbl_usuarios usuarios2 WITH(NOLOCK) ON (usuarios2.us_id = cl_id_distribuidor) ";
		$strSQL.= "	LEFT JOIN tbl_clientes clientes2 WITH(NOLOCK) ON (clientes2.cl_id = clientes.cl_id_distribuidor) ";
		$strSQL.= "	WHERE clientes.cl_borrado = 0 AND clientes.cl_id = ".(int)$id;
		
		if(!empty($filtro)){
			$strSQL.= "	AND clientes.cl_razonSocial LIKE '%".$filtro."%'";
		}
		
		if($idEmpresa){
			$strSQL.= "	AND clientes.cl_id_distribuidor = ".(int)$idEmpresa;
		}
		
		$strSQL.= "	ORDER BY clientes.cl_razonSocial ";

		$objRegistros = $this->objSQL->dbQuery($strSQL);
		$arrRegistros = $this->objSQL->dbGetAllRows($objRegistros,3);
		return $arrRegistros;
	}     
}
