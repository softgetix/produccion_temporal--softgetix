<?php
class Wizards {
	var $objSQLServer;

	function Wizards($objSQLServer) {
		$this->objSQL = $objSQLServer;
	}

   function obtenerPrimerNodo($wizard,$cliente){
   		$strSQL = "SELECT wi_wn_inicial FROM tbl_wizards wi WITH(NOLOCK) 
			   INNER JOIN tbl_wizards_empresas we WITH(NOLOCK) ON we.we_wi_id = wi.wi_id
			   WHERE wi_id=".(int)$wizard." AND wi_borrado=0 AND we.we_cl_id=".(int)$cliente;
		$query=$this->objSQL->dbQuery($strSQL);
		if($idNodoInicial = $this->objSQL->dbGetRow($query, 0)){return $idNodoInicial;}else{ return false; }
	}
   
    function obtenerWizardDefecto($cliente){
		$strSQL = "SELECT wi_id FROM tbl_wizards wi  WITH(NOLOCK) 
		INNER JOIN tbl_wizards_empresas we WITH(NOLOCK) ON we.we_wi_id = wi.wi_id
		WHERE wi.wi_default='true' AND we.we_cl_id=".$cliente;
		$query=$this->objSQL->dbQuery($strSQL);
		if($idWizardDefault = $this->objSQL->dbGetRow($query, 0)){return $idWizardDefault;}else{ return false; }
	}
   
   function obtenerElementos($nodo,$wizard,$cliente,$ruta=0){
   ///NOTA: Transicion son los elementos que permiten el paso de un wizard a otro: botones y radios.	   
	    $strSQL = "SELECT pasos.wp_transicion, pasos.wp_wn_siguiente, pasos.wp_barra_control, pasos.wp_transicion_texto, pasos.wp_transicion_orden, nodos.wn_texto, nodos.wn_titulo, nodos.wn_imagen, nodos.wn_id FROM tbl_wizards_pasos as pasos
			INNER JOIN tbl_wizards_nodos as nodos WITH(NOLOCK) ON wp_wn_id=wn_id
			INNER JOIN tbl_wizards as wizards WITH(NOLOCK) ON wn_wi_id = wi_id
			INNER JOIN tbl_wizards_empresas we WITH(NOLOCK) ON we.we_wi_id = wizards.wi_id
			WHERE wn_id=".(int)$nodo." AND wn_wi_id = ".(int)$wizard." AND we_cl_id=".(int)$cliente;
		
		$strSQL .=" AND pasos.wp_ruta=".$ruta=$ruta?$ruta:0;
		/*if(!empty($ruta))
		{
		  $strSQL .=" AND pasos.wp_ruta=".$ruta;	
		}else
		{
		   $strSQL .=" AND pasos.wp_ruta=0";
		}*/
		
		/*echo $strSQL;
		die();*/
		$query=$this->objSQL->dbQuery($strSQL);
   		if($elementos = $this->objSQL->dbGetAllRows($query)){return $elementos;}else{ return false; }	   
	   
   }
   
   
  function getPermisosWizards($session_keys,$cliente) {
	  $strSQL = " SELECT wi_id, wi_descripcion ";
	  $strSQL.= " FROM tbl_wizards wi WITH(NOLOCK) ";
	  $strSQL.= " INNER JOIN tbl_wizards_empresas we WITH(NOLOCK) ON we.we_wi_id = wi.wi_id ";
	  $strSQL.= " WHERE wi.wi_borrado = 0 AND we.we_cl_id=".(int)$cliente;
	  $strSQL.= " AND wi.wi_seccion_asignada IN(".$session_keys.",'seccion_default') ";
	  $strSQL.= " ORDER BY wi_orden ";
	  $query=$this->objSQL->dbQuery($strSQL);
	  if($arrWizardsHabilitados=$this->objSQL->dbGetAllRows($query)){return $arrWizardsHabilitados;}else{return false;}
	 
   }
   
   
   function getBotoneraTyC(){
	   @session_start();
	   $strSQL = " SELECT COUNT(*) as cant FROM tbl_usuarios WITH(NOLOCK) WHERE us_id = ".(int)$_SESSION['idUsuario']." AND us_ultimo_acceso IS NOT NULL ";
	   $query = $this->objSQL->dbQuery($strSQL);
	   $result = $this->objSQL->dbGetAllRows($query);
	   return $result[0]['cant'];
   }
   
   function getTerminosCondiciones($cliente) {
		$strSQL = " SELECT wn_imagen ";
		$strSQL.= " FROM tbl_wizards wi WITH(NOLOCK) "; 
		$strSQL.= " INNER JOIN tbl_wizards_empresas we WITH(NOLOCK) ON we.we_wi_id = wi.wi_id  ";
		$strSQL.= " INNER JOIN tbl_wizards_nodos as nodos WITH(NOLOCK) ON wn_wi_id = wi_id ";
		$strSQL.= " WHERE wi.wi_borrado = 0 AND we.we_cl_id = ".(int)$cliente." AND wi.wi_seccion_asignada IN('wizards')  ";
		$strSQL.= " ORDER BY wi_orden ";
		$query=$this->objSQL->dbQuery($strSQL);
		if($arrWizardsHabilitados=$this->objSQL->dbGetAllRows($query)){
			return $arrWizardsHabilitados;
		}
		return false;
	}
}
?>