<?php
require_once 'clases/clsAbms.php';
class Entrada extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_entradas','en');
	}
	
	function obtenerRegistros($id=0, $filtro="", $campoValidador='' ,$idValidador=0, $soloCantidad=false){
		if($filtro == 'getAllReg'){
			$filtro = '';
		}
		
		$strSQL = " SELECT en_id,en_descripcion,en_numero_entrada_equipo ";
		$strSQL.= " FROM tbl_entradas WITH(NOLOCK) ";
		$strSQL.= "	WHERE en_borrado = 0 ";
		
		if($id){
			$strSQL.= " AND en_id = ".(int)$id;
		}
		
		if(!empty($filtro)){
			$strSQL.= " AND (en_descripcion like '%".$filtro."%') ";
		}
		
		if(!empty($campoValidador)){
			$strSQL.= " AND en_descripcion = '".$campoValidador."'";	
		}
		
		if(!empty($idValidador)){
			$strSQL.= " AND en_id <> ".(int)$idValidador;	
		}
		
		$strSQL.= " ORDER BY en_descripcion ";	
		
		$objEntradas = $this->objSQL->dbQuery($strSQL);
		if (!$soloCantidad){
			$arrEntradas = $this->objSQL->dbGetAllRows($objEntradas);
			return $arrEntradas;
		}
		else{
			$intRows = $this->objSQL->dbNumRows($objEntradas);
			return $intRows;
		}
	}
}