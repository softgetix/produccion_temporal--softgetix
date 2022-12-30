<?php
require_once 'clases/clsAbms.php';
class ModeloEquipo extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_modelos_equipo','mo');
	}

   function obtenerRegistros($id=0, $filtro="" , $campoValidador='' ,$idValidador=0, $soloCantidad=false){
   	  	if($filtro == 'getAllReg'){
			$filtro = '';
		}
		
		$strSQL = " SELECT tbl_modelos_equipo.*,tbl_marca_equipo.*,tbl_usuarios.us_nombreUsuario ";
	  	$strSQL.= " FROM tbl_modelos_equipo WITH(NOLOCK) ";
	  	$strSQL.= " INNER JOIN tbl_marca_equipo WITH(NOLOCK) ON (me_id = mo_id_marca) ";
	  	$strSQL.= " LEFT OUTER JOIN tbl_usuarios WITH(NOLOCK) ON tbl_modelos_equipo.mo_us_id=tbl_usuarios.us_id ";
		$strSQL.= " WHERE mo_borrado = 0 ";
		
		if($id){
			$strSQL.= " AND mo_id = ".(int)$id;
		}
		
		if($filtro){
			$strSQL.= " AND (mo_nombre like '%".$filtro."%')";
		}	
		
		if(!empty($campoValidador)){
			$strSQL.= " AND mo_nombre = '".$campoValidador."'";	
		}
		
		if(!empty($idValidador)){
			$strSQL.= " AND mo_id <> ".(int)$idValidador;	
		}
		
		$strSQL.= " ORDER BY mo_nombre ";	
		
		$objModeloEquipos= $this->objSQL->dbQuery($strSQL);
		if (!$soloCantidad){
			$arrModeloEquipos = $this->objSQL->dbGetAllRows($objModeloEquipos);
			return $arrModeloEquipos;
		}
		else{
			$intRows = $this->objSQL->dbNumRows($objModeloEquipos);
			return $intRows;
		}
	}
   
	function getBitMotor($strMoviles){
	   	if(!empty($strMoviles)){
			$sql = " SELECT un_mo_id as mo_id, mo_bit_motor as bit, mo_matricula, mo_motor_encendido as motorEncendido ";
			$sql.= " FROM tbl_unidad WITH(NOLOCK) ";
			$sql.= " INNER JOIN tbl_modelos_equipo WITH(NOLOCK) ON un_mod_id = mo_id ";
			$sql.= " INNER JOIN tbl_moviles ve WITH(NOLOCK) ON ve.mo_id = un_mo_id ";
			$sql.= " WHERE un_mo_id IN (".$strMoviles.") ";
			$rs = $this->objSQL->dbQuery($sql);
			$res = $this->objSQL->dbGetAllRows($rs);
			
			$arrModeloEquipos = array();
			foreach($res as $item){
				$arrModeloEquipos[$item['mo_id']] = array('bit' => $item['bit'], 'motor_encendido' => $item['motorEncendido']); 			   
			}
		   
			return $arrModeloEquipos;
		}
		return false;
   }
   
}
?>