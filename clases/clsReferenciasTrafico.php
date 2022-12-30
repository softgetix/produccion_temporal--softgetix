<?php
require_once 'clases/clsAbms.php';
class ReferenciasTrafico extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_referencias_trafico','rt');
	}

   function insertarReferenciasTrafico($campos, $valorCampos, $datos=NULL){
   	if($campos && $valorCampos){
   		$strSQL = "
			SELECT rt_id, rt_tc_id, tc_descripcion, rt_tm_id, tv_nombre, rt_value_min, rt_value_max
			FROM tbl_referencias_trafico v	WITH(NOLOCK)		
			INNER JOIN tbl_tipo_camino tc WITH(NOLOCK) ON ( v.rt_tc_id = tc.tc_id )
			INNER JOIN tbl_tipo_movil tm WITH(NOLOCK) ON ( v.rt_tm_id = tm.tv_id )
			WHERE rt_borrado = 0	
			";
		$strSQL .= " AND rt_id = 0 ";			
		$strSQL .= " AND (tv_nombre like '') ";			
			
		if(isset($datos['campoValidador'])){
			$strSQL .= " AND (tv_nombre = '".$datos['campoValidador']."')";
		}			
		$strSQL .= " ORDER BY tv_nombre ASC";
		$objReferenciasTrafico = $this->objSQL->dbQuery($strSQL);
   		$intRows = $this->objSQL->dbNumRows($objReferenciasTrafico);
		
		if(!$intRows){
			if($this->insertarRegistro($campos, $valorCampos)){
				return true;
			}	
      	}
   	}
   	return false;
   }
      
	function modificarReferenciasTrafico($set, $id, $campoValidador = NULL){
		if($set && $id){
			$strSQL = "
				SELECT rt_id, rt_tc_id, tc_descripcion, rt_tm_id, tv_nombre, rt_value_min, rt_value_max
				FROM tbl_referencias_trafico v WITH(NOLOCK) 
				INNER JOIN tbl_tipo_camino tc WITH(NOLOCK) ON ( v.rt_tc_id = tc.tc_id )
				INNER JOIN tbl_tipo_movil tm  WITH(NOLOCK) ON ( v.rt_tm_id = tm.tv_id )
				WHERE rt_borrado = 0	
				";
			$strSQL .= " AND rt_id = 0 ";			
			$strSQL .= " AND (tv_nombre like '') ";
			if(isset($campoValidador)){
				$strSQL .= " AND (tv_nombre = '".$campoValidador."')";
			}
				
			$strSQL .= " AND v.rt_id <> ".$id;
			$strSQL .= " ORDER BY tv_nombre ASC";
		
			$objReferenciasTrafico = $this->objSQL->dbQuery($strSQL);
			$intRows = $this->objSQL->dbNumRows($objReferenciasTrafico);
			if(!$intRows){				
				if($this->modificarRegistro($set, $id)){
					return 1;
				}
				else{
					return 2;
				}
			}
		}
		return 0;
	}
   
   
   function obtenerReferenciasTrafico($datos=null){
   	$strSQL = "
			SELECT rt_id, rt_tc_id, tc_descripcion, rt_tm_id, tv_nombre, rt_value_min, rt_value_max
			FROM tbl_referencias_trafico v WITH(NOLOCK)
			INNER JOIN tbl_tipo_camino tc WITH(NOLOCK) ON ( v.rt_tc_id = tc.tc_id )
			INNER JOIN tbl_tipo_movil tm WITH(NOLOCK) ON ( v.rt_tm_id = tm.tv_id )
			WHERE rt_borrado = 0	
	";
	if(isset($datos['id'])){
	$strSQL .= " AND rt_id = ".(int) $datos['id'];
	}
	
	if(isset($datos['filtro'])){
	$strSQL .= " AND (tv_nombre like '%".$datos['filtro']."%')";
	}
		
	$strSQL .= " ORDER BY tc_descripcion, tv_nombre ASC";

      $objReferenciasTrafico = $this->objSQL->dbQuery($strSQL);
	  $objRow = $this->objSQL->dbGetAllRows($objReferenciasTrafico);
	  return $objRow;
   }
}
?>