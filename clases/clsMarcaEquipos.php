<?php
require_once 'clases/clsAbms.php';
class MarcaEquipo extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_marca_equipo','me');
	}
	
	function obtenerRegistros($id=0, $filtro="", $campoValidador='' ,$idValidador=0, $soloCantidad=false){
		if($filtro == 'getAllReg'){
			$filtro = '';
		}
		
		$strSQL = " SELECT me_id,me_nombre,me_descripcion ";
	  	$strSQL.= " FROM tbl_marca_equipo WITH(NOLOCK) ";
	  	$strSQL.= " WHERE me_borrado = 0 ";
	  	if($id){
	  		$strSQL.= " AND me_id = ".(int)$id;
		}
		
		if(!empty($filtro)){
	  		$strSQL.= " AND  AND (me_nombre like '%".$filtro."'%) ";
		}
		
		if(!empty($campoValidador)){
			$strSQL.= " AND me_nombre = '".$campoValidador."'";	
		}
		
		if(!empty($idValidador)){
			$strSQL.= " AND me_id <> ".(int)$idValidador;	
		}
		
		$strSQL.= " ORDER BY me_nombre ";	
		
		$objMarcaEquipos = $this->objSQL->dbQuery($strSQL);
		if (!$soloCantidad){
			$arrMarcaEquipos = $this->objSQL->dbGetAllRows($objMarcaEquipos);
			return $arrMarcaEquipos;
		}
		else{
			$intRows = $this->objSQL->dbNumRows($objMarcaEquipos);
			return $intRows;
		}
	}
}
?>