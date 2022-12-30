<?php
require_once 'clases/clsAbms.php';
class Cartografia extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'Argentina','ar','');
	}

	function getPais(){
		$arrPais = array();
		$sql = "SELECT pa_id, pa_nombre FROM tbl_pais WITH(NOLOCK) WHERE pa_borrado = 0 ORDER BY pa_nombre";
		$rs = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetAllRows($rs);
		return $objRow;
	}
	
	function getCartografia($filtro = NULL){
		$arrCatografia = array();
		$sql = " SELECT * FROM Argentina WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_pais WITH(NOLOCK) ON ar_pa_id = pa_id ";
		$sql.= " WHERE ar_borrado = 0 ";
		$sql.= " AND ar_pa_id > 2 ";//-- Todos los registros menos Argentina y Chile
		if((int)$filtro['ar_id']){
			$sql.= " AND ar_id = ".(int)$filtro['ar_id'];
		}
		if(isset($filtro['Partido'])){
			$sql.= " AND Partido LIKE '%".$filtro['Partido']."%'";
		}
		$sql.= " ORDER BY pa_nombre, Provincia, Partido ";
		$rs = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetAllRows($rs);
		return $objRow;
	}
	
	 function obtenerTotalRegistros() {
	   	$sql = " SELECT COUNT(ar_id) FROM Argentina WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_pais WITH(NOLOCK) ON ar_pa_id = pa_id ";
		$sql.= " WHERE ar_borrado = 0 ";
		$sql.= " AND ar_pa_id > 2 ";  
        $objReferencias = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetRow($objReferencias, 0);
        return $objRow[0];
    }
}
