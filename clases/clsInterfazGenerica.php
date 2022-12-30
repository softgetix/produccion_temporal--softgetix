<?php
require_once 'clases/clsAbms.php';
class InterfazGenerica extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_interfaz_generica','ig');
	}
	
	function obtenerRegistros($id, $filtro = "", $campoValidador = ""){
	
		$sql = " SELECT * ";
		$sql.= " FROM tbl_interfaz_generica AS ig WITH(NOLOCK) ";
		$sql.= " WHERE ig.ig_borrado=0 ";
		
		if($id){
			$sql.= " AND ig_id = ".(int)$id;
		}
		
		if(!empty($filtro)){
			$sql.= " AND ig_nombre like '%".$filtro."%' ";
		}
		
		if(!empty($campoValidador)){
			$sql.= "  AND ig_value = '".$campoValidador."' ";
		}
		
		$obj = $this->objSQL->dbQuery($sql);
		$rows = $this->objSQL->dbGetAllRows($obj,3);
		return $rows;
	}
	
	function obtenerInterfazGrafica($seccion){
		$sql = " SELECT ig_id, ig_nombre, ig_ti_id, ig_idCampo, ig_requerido, ig_min, ig_max, ig_soloLectura, ig_gr_id, 
				gi_nombre, ic_store,ig_value, ig_tipoDato, ig_validacionExistencia,ig_evento, ic_esConsulta ";
		$sql.= " FROM tbl_interfaz_generica WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_interfaz_grupos WITH(NOLOCK) ON (ig_gr_id = gi_id) ";
		$sql.= " LEFT JOIN tbl_interfaz_combos WITH(NOLOCK) ON (ig_id = ic_ig_id AND ic_borrado = 0) ";
		$sql.= " WHERE ig_borrado = 0 AND ig_seccion = '".$seccion."' ";
		$sql.= " ORDER BY ig_gr_id, ig_orden ";
		$objUsuarios = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetALLRows($objUsuarios,3);
		
		return $objRow;
	}
	
	function obtenerDatosCombo($store="", $tipo = 3, $isConsulta = false) {
		$arrUsuarios = array();
		if ($store) {
			if($isConsulta){
         		$strSQL = $store;
			}
			else{
				$strSQL = 'EXEC '.$store;
			}
			$obj = $this->objSQL->dbQuery($strSQL);
			$arrRows = $this->objSQL->dbGetAllRows($obj,$tipo);
			return $arrRows;
		}
		return false;
	}
	
}
?>