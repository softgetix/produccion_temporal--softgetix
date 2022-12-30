<?php
require_once 'clases/clsAbms.php';
class MotivoConfirmacion extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_motivos_confirmacion','mc');
	}
	
	function obtenerRegistros($id=0, $filtro=""){
		$strSQL = " SELECT * FROM tbl_motivos_confirmacion WITH(NOLOCK) ";
		$strSQL.= " WHERE mc_borrado = 0 ";
		if($id){
			$strSQL.= " mc_id = ".(int)$id;
		}
		
		if($filtro){
			$strSQL.= " mc_descripcion LIKE '%".$filtro."%'";
		}
		$strSQL.= " ORDER BY mc_descripcion";
		
	  	$obj = $this->objSQL->dbQuery($strSQL);
      	$objRows = $this->objSQL->dbGetAllRows($obj, 3);
      	if($objRows){
	  		return $objRows;
      	}
      	return false;
	}
}
?>