<?php
require_once 'clases/clsAbms.php';
class Protocolo extends Abm{
	
	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_protocolos','pr');
	}
	
	function getMoviles($filtro = NULL){
		$arrMoviles = array();
		$sql = " SELECT mo_id as id, mo_matricula+' / '+mo_identificador as dato ";
		$sql.= " FROM tbl_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_mo_id = mo_id ";
		$sql.= " WHERE mo_borrado = 0 ";
		if((int)$filtro['pr_id']){
			$sql.= " AND mo_id NOT IN (SELECT pm_mo_id FROM tbl_protocolos_moviles WITH(NOLOCK) WHERE pm_pr_id = ".(int)$filtro['pr_id'].")";
		}
		$sql.= " AND um_us_id = ".(int)$_SESSION['idUsuario'];
		$sql.= " ORDER BY dato ";
		$rs = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($rs);
        return $objRow;
	}
	
	function getMovilesAsig($filtro = NULL){
		$arrMoviles = array();
		$sql = " SELECT mo_id as id, mo_matricula+' / '+mo_identificador as dato ";
		$sql.= " FROM tbl_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_protocolos_moviles WITH(NOLOCK) ON pm_mo_id = mo_id ";
		$sql.= " WHERE mo_borrado = 0 ";
		$sql.= " AND pm_pr_id = ".(int)$filtro['pr_id'];
		$sql.= " ORDER BY dato ";
		$rs = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($rs);
		return $objRow;
	}
	
	function getProtocolo($filtro = NULL){
		$arrProtocolo = array();
		$sql = " SELECT * FROM tbl_protocolos WITH(NOLOCK) ";
		$sql.= " LEFT JOIN tbl_protocolos_tipo WITH(NOLOCK) ON pt_id = pr_pt_id ";
		$sql.= " WHERE pr_borrado = 0 ";
		if(!empty($filtro['pr_nombre'])){
			$sql.= " AND pr_nombre LIKE '%".$filtro['pr_nombre']."%' ";
		}
		if((int)$filtro['pr_id']){
			$sql.= " AND pr_id = ".(int)$filtro['pr_id'];
		}
		
		$sql.= " ORDER BY pr_nombre ";
		$rs = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($rs);
		return $objRow;
	}

	function asignarMoviles($id, $moviles){
		$sql = " DELETE FROM tbl_protocolos_moviles WHERE pm_pr_id = ".(int)$id;
		$this->objSQL->dbQuery($sql);
		
		if(!empty($moviles)){
			$arrMoviles = explode(',',$moviles);
			foreach($arrMoviles as $item){
				if((int)$item){
				$sql = "INSERT INTO tbl_protocolos_moviles(pm_pr_id, pm_mo_id) ";
				$sql.=" VALUES(".(int)$id.", ".(int)$item.")";
				$this->objSQL->dbQuery($sql);	
				}
			}
		}
	}
	
	function getTipoProtocolo(){
		
		$sql = " SELECT * FROM tbl_protocolos_tipo WITH(NOLOCK) ";
		$sql.= " ORDER BY pt_nombre ";
		
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res);
		return $rs;
		
	}

}
