<?php
require_once 'clases/clsAbms.php';
class ViajesMotivosCambios extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_viajes_motivos_cambios','vmc');
	}
	
    function obtenerViajesMotivosCambios($id=0,$filtro=""){
		$strSQL = "SELECT * FROM tbl_viajes_motivos_cambios WITH(NOLOCK) WHERE vmc_borrado = 0 ";
		if($id > 0){
			$strSQL .= " AND vmc_id = ".$id;
		}
		if($filtro!=""){
			$strSQL .= " AND vmc_descripcion like '%".$filtro."%'";
		}
		$strSQL .= " ORDER BY vmc_descripcion ";
		
		$objViajesMotivosCambios = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objViajesMotivosCambios);
		return $objRow;
	}
}
?>