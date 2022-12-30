<?php
require_once 'clases/clsAbms.php';
class Conductor extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_conductores','co','');
	}

   function obtenerConductores($id=0, $filtro=""){
	  	$idEmpresa = $_SESSION["idEmpresa"];
		
		$strSQL = " SELECT cl_tipo FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = ".(int)$idEmpresa;
		$objConductores = $this->objSQL->dbQuery($strSQL);
      	$objRow = $this->objSQL->dbGetRow($objConductores,0,3);
		$tipoCliente = $objRow['cl_tipo'];
		
		$sql = " SELECT dbo.tipoVistaMoviles(".(int)$_SESSION['idUsuario'].") ";
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetRow($res,0,1);
		$vistaMovil = $rs[0];
		
		
		$selectTop = ' TOP 30 ';
		if($filtro == 'getAllReg'){
			$selectTop = $filtro = '';
		}
		elseif(!empty($filtro)){
			$selectTop = '';
		}

		$strSQL = " SELECT ".$selectTop." co.*, dbo.EstadoApp (co_telefono , 1)  as co_Estado_app,  cl.cl_razonSocial as razon_social ";
		$strSQL.= " ,mo1.mo_".$vistaMovil." as movil_1, mo2.mo_".$vistaMovil." as movil_2 ";
		$strSQL.= " FROM tbl_conductores co WITH(NOLOCK) ";
	  	$strSQL.= " INNER JOIN tbl_clientes cl WITH(NOLOCK) ON (cl.cl_id = co.co_cl_id) ";
		$strSQL.= " LEFT JOIN tbl_moviles mo1 WITH(NOLOCK) ON (mo1.mo_co_id_primario = co_id AND mo1.mo_borrado = 0) ";
		$strSQL.= " LEFT JOIN tbl_moviles mo2 WITH(NOLOCK) ON (mo2.mo_co_id_secundario = co_id AND mo2.mo_borrado = 0) ";

	  	$strSQL.= " WHERE co_borrado = 0 ";
	  	
		if((int)$id){
			$strSQL.= " AND co_id = ".(int)$id;
		}
		
		if($tipoCliente == 1){
			$strSQL.= " AND cl_id_distribuidor = ".(int)$idEmpresa;
		}
		elseif($tipoCliente == 2){
			$strSQL.= " AND co.co_cl_id = ".(int)$idEmpresa;
		}
		
		if($filtro){
			$strSQL.= " AND (CO_dni like '%".$filtro."%' OR co_telefono like  '%".$filtro."%' OR co_nombre like '%".$filtro."%' OR co_apellido like '%".$filtro."%') ";	
		}


		$strSQL.= " order by co_nombre asc";
		$objConductores = $this->objSQL->dbQuery($strSQL);
      	$arrConductores = $this->objSQL->dbGetAllRows($objConductores,3);
			
		return $arrConductores;
   }
   
   function obtenerIbuttons($iButtonCode,$idcond=0){	  
   	  $sql = "
				SELECT co_ibutton 
				FROM tbl_conductores  WITH(NOLOCK)
				WHERE co_borrado = 0 
				AND co_ibutton = '".$iButtonCode."'";
		if($idcond > 0){
			$sql.= " AND co_id <> ".$idcond;
		}
				
		$objConductores = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objConductores);
		return $objRow;
   }
    
	function obtenerUltimoId(){
		return 	$this->objSQL->dbLastInsertId();		
	}	
	
	function obtenerConductoresPorEmpresa($idEmpresa){
		if($idEmpresa){
			$strSQL = " SELECT co_id, co_nombre, co_apellido ";
			$strSQL.= " FROM tbl_conductores WITH(NOLOCK) WHERE co_borrado = 0 ";
			$strSQL.= " AND co_cl_id = ".(int)$idEmpresa;
			$strSQL.= " ORDER BY co_nombre ASC ";
			$obj = $this->objSQL->dbQuery($strSQL);
			return $this->objSQL->dbGetAllRows($obj,3);
		}
		return false;
	}
}
?>