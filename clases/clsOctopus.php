<?php
//require_once 'clases/clsAbms.php';
class Octopus/* extends Abm*/{
	
	function __construct($objSQLServer) {
		$this->objSQL = $objSQLServer;
	}
	
	function getWS($filtro = NULL){
		
		$sql = " SELECT tbl_octopus.*, cl_razonSocial ";
		$sql.= " FROM tbl_octopus ";
		$sql.= " INNER JOIN tbl_clientes ON cl_id = oc_cl_id ";
		$sql.= " WHERE oc_borrado = 0 ";
		
		if($filtro['txt']){
			$sql.= " AND (oc_nombre LIKE '%".$filtro['txt']."%' OR cl_razonSocial LIKE '%".$filtro['txt']."%')";	
		}
		elseif($filtro['id']){
			$sql.= " AND oc_id = ".(int)$filtro['id'];	
		}
		
		$rs = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($rs);
        return $objRow;
	}
	
	function getWSParameter($id){
		$sql = " SELECT * FROM tbl_octopus_parameter WHERE op_oc_id = ".(int)$id;
		$rs = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($rs);
        return $objRow;
	}
	
	function setWSParameter($id, $array){
		
		$sql = "DELETE FROM tbl_octopus_parameter WHERE op_oc_id = ".(int)$id;
		$this->objSQL->dbQuery($sql);	
		
		foreach($array as $opt => $item){
			if($item){
				$params = array('op_oc_id'=>$id,'op_parametro'=>$opt,'op_valor'=>$item);
				$this->objSQL->dbQueryInsert($params, 'tbl_octopus_parameter');
			}
		}	
	}
}
