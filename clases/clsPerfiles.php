<?php
class Perfil {
	
	function Perfil($objSQLServer) {
		$this->objSQL = $objSQLServer;
	}

	function validarSeccion($seccion){
		global $arrPermisos;
		if(in_array($seccion,$arrPermisos)){
			return true;
		}
		return false;
	}

	function obtenerGrupoPerfiles(){
		$strSQL = " SELECT * ";
		$strSQL.= " FROM tbl_perfiles WITH(NOLOCK) ";
		$strSQL.= " WHERE pe_id_padre = 0 AND pe_borrado = 0 ";
		$strSQL.= " ORDER BY pe_nombre ";
		$obj = $this->objSQL->dbQuery($strSQL);
	  	$objRow = $this->objSQL->dbGetAllRows($obj,3);
	  	return $objRow;
	}
	
	function obtenerPerfiles($idGrupoPerfil){
		$strSQL = " SELECT * ";
		$strSQL.= " FROM tbl_perfiles WITH(NOLOCK) ";
		$strSQL.= " WHERE pe_borrado = 0 ";
		if($idGrupoPerfil == 19){
			$strSQL.= " AND pe_id_padre = 0 AND pe_id = 19 ";	
		}
		else{
			$strSQL.= " AND pe_id_padre = (SELECT pe_id_padre FROM tbl_perfiles WHERE pe_id = ".(int)$idGrupoPerfil.") ";	
		}
		$strSQL.= " ORDER BY pe_nombre ";
		$obj = $this->objSQL->dbQuery($strSQL);
	  	$objRow = $this->objSQL->dbGetAllRows($obj,3);
	  	return $objRow;
	}
	
	function obtenerPerfilesHijos($idPaquete, $idTipoEmpresa = 0, $idPerfil = 0){
		$strSQL = " SELECT * ";
		$strSQL.= " FROM tbl_perfiles WITH(NOLOCK) ";
		$strSQL.= " WHERE pe_borrado = 0 AND pe_id_padre = ".(int)$idPaquete;	
		if($idTipoEmpresa){
			$strSQL.= " AND pe_tipo_empresa = ".(int)$idTipoEmpresa;			
		}
		if($idPerfil == 5 || $idPerfil == 9){
			$strSQL.= " AND pe_id = ".(int)$idPerfil;				
		}
		elseif($_SESSION['idPerfil'] == 5 || $_SESSION['idPerfil'] == 9){
			$strSQL.= " AND pe_id NOT IN (".$_SESSION['idPerfil'].")";			
		}
		
		$strSQL.= " ORDER BY pe_nombre ";
		$obj = $this->objSQL->dbQuery($strSQL);
	  	$objRow = $this->objSQL->dbGetAllRows($obj,3);
	  	return $objRow;
	}
	
	function obtenerPaquetePorAgente($idAgente){
		$strSQL = " SELECT pe_id, pe_nombre, cl_tipo, cl_id_distribuidor ";
		$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_perfiles WITH(NOLOCK) ON cl_paquete = pe_id ";
		$strSQL.= " WHERE cl_id = ".(int)$idAgente." AND cl_borrado = 0 ";
		$obj = $this->objSQL->dbQuery($strSQL);
	  	$objRow = $this->objSQL->dbGetRow($obj,0,3);
	  	
		if(!$objRow['pe_id']){
			$cl_tipo = $objRow['cl_tipo'];
			$strSQL = " SELECT pe_id, pe_nombre, cl_tipo ";
			$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
			$strSQL.= " LEFT JOIN tbl_perfiles WITH(NOLOCK) ON cl_paquete = pe_id ";
			$strSQL.= " WHERE cl_id = ".(int)$objRow['cl_id_distribuidor']." AND cl_borrado = 0 "; 
			$obj = $this->objSQL->dbQuery($strSQL);
			$objRow = $this->objSQL->dbGetRow($obj,0,3);	
			$objRow['cl_tipo'] = $cl_tipo;
		}
		return $objRow;
	}
   
}
