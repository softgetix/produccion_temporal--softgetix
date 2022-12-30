<?php
require_once 'clases/clsAbms.php';
class Comando extends Abm{
	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_comando','co');
	}
	
	function obtenerRegistros($id = 0, $filtro="", $campoValidador='', $idValidador=0){
		 $strSQL = " SELECT co_id, co_nombre, co_codigo, co_tipo, co_instrucciones, co_respuesta_ok ";
		 $strSQL.= " FROM tbl_comando WITH(NOLOCK) ";
		 $strSQL.= " WHERE co_borrado=0 ";
		
		 if($id){
			 $strSQL.= " AND AND co_id = ".(int)$id;
		 }
		
		 if(!empty($filtro)){
			 $strSQL.= " AND (co_nombre like '%".$filtro."%') ";
		 }
		 
		 if($campoValidador){
			 $strSQL.= " AND (co_codigo = '".$campoValidador."')" ;
		 }
		 
		 if($idValidador){
			 $strSQL.= " AND co_id <> = ".(int)$idValidador ;
		 }
			 
		$rs=$this->objSQL->dbQuery($strSQL);
		$res=$this->objSQL->dbGetAllRows($rs);
		return $res;
	}
	
	function obtenerComandoGrupoTodos($grupo){
		$objRes=$this->objSQL->dbQuery("
            select 
                gc_gr_id, 
                co_id, 
                co_nombre, 
                co_codigo, 
                co_tipo, 
                co_instrucciones 
            from 
                tbl_grupo_comando gc WITH(NOLOCK)
                inner join tbl_comando co WITH(NOLOCK) ON gc.gc_co_id = co.co_id 
            where gc.gc_gr_id = {$grupo}");
		$res=$this->objSQL->dbGetAllRows($objRes);
		return $res;
	}
	
	function getModeloEquipos(){
		$sql = " SELECT mo_id, mo_nombre ";
		$sql.= " FROM tbl_modelos_equipo WITH(NOLOCK) ";
		$sql.= " WHERE mo_borrado = 0 ";
		$sql.= " ORDER BY mo_nombre ";	
		
		$rs=$this->objSQL->dbQuery($sql);
		$res=$this->objSQL->dbGetAllRows($rs);
		return $res;
	}
}