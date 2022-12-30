<?php
require_once 'clases/clsAbms.php';
class DefinicionReporte extends Abm{
	
	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,null,null,null);
	}
	
	function obtenerEventosCombo2($idUsuario=0){
		$sql = " SELECT dr_id as id, dbo.properCase(tr_descripcion) as dato ";
		$sql.= " FROM tbl_definicion_reportes ";
		$sql.= " INNER JOIN tbl_traduccion_reportes ON (dr_id = tr_id_reporte) ";
		$sql.= " WHERE dr_borrado = 0 AND tr_borrado = 0 ";
		$sql.= " AND tr_id_lenguaje = 1 ";
		$sql.= " AND dr_id not in 
			(select pd.pd_dr_id from tbl_perfiles_definiciones pd
				inner join tbl_usuarios us
				on us.us_pe_id = pd.pd_pe_id
				where us.us_id = ".$idUsuario.") ";
		$sql.= " and dr_id <> 1 ";
		$sql.= " ORDER BY tr_descripcion ";
		$objDefinicionReportes = $this->objSQL->dbQuery($sql);
		$arrDefinicionReportes = $this->objSQL->dbGetAllRows($objDefinicionReportes,3);
		return $arrDefinicionReportes;
	}

	function obtenerAlertas($idUsuario) {
		$sSQL = "
			SELECT al_id, al_nombre, al_referencia, al_evento, al_confirmacion, us_nombre +' '+ us_apellido AS usuario 
			FROM tbl_alertas 
				INNER JOIN tbl_usuarios 
					ON us_id=al_us_id 
			WHERE 
				al_borrado=0 
				AND al_cl_id = (SELECT us_cl_id FROM tbl_usuarios WHERE us_id = ".(int)$idUsuario.")
			ORDER BY al_nombre";
		$busqueda = $this->objSQL->dbQuery($sSQL);
		$busqueda = $this->objSQL->dbGetAllRows($busqueda);
		return $busqueda;
	}

   	function obtenerEventosCombo(){
       	$sql = " SELECT dr_id as id ";
		$sql.= " FROM tbl_definicion_reportes WITH(NOLOCK) ";
		$sql.= " WHERE dr_borrado = 0 AND dr_id <> 1 ";
		$sql.= " ORDER BY dr_id ASC ";
		$objDefinicionReportes = $this->objSQL->dbQuery($sql);
      	$arrDefinicionReportes = $this->objSQL->dbGetAllRows($objDefinicionReportes,3);
	  	return $arrDefinicionReportes;
   	}
  
	function obtenerEventosAsignados($idAgente){
       	$sql = " SELECT dr_id as id ";
		$sql.= " FROM tbl_definicion_reportes WITH(NOLOCK) ";
		$sql.= " WHERE dr_borrado = 0 ";
		$sql.= " AND dr_id IN (SELECT dra_dr_id FROM tbl_definicion_reportes_agentes WITH(NOLOCK) WHERE dra_cl_id = ".(int)$idAgente.") ";
		$sql.= " ORDER BY dr_id ";
		$objDefinicionReportes = $this->objSQL->dbQuery($sql);
      	$arrDefinicionReportes = $this->objSQL->dbGetAllRows($objDefinicionReportes,3);
	  	
		if($arrDefinicionReportes){
			global $lang;
			require_once 'clases/clsIdiomas.php';
			$objIdioma = new Idioma();
			$eventos = $objIdioma->getEventos($_SESSION['idioma']);
			
			foreach($arrDefinicionReportes as $k => $item){
				$dato = 'evento_'.(int)$item['id'];
				$dato = $eventos->$dato->__toString()?$eventos->$dato->__toString():($eventos->default->__toString().' ('.$item['id'].')');
				$arrDefinicionReportes[$k]['dato'] = $dato;
			}	
		}
		return $arrDefinicionReportes;
   }
   
	function traducirEvento($idEvento){
		require_once 'clases/clsIdiomas.php';
		$objIdioma = new Idioma();
		$eventos = $objIdioma->getEventos($_SESSION['idioma']);
			
		$dato = 'evento_'.(int)$idEvento;
		$dato = $eventos->$dato->__toString()?$eventos->$dato->__toString():($eventos->default->__toString().' ('.$idEvento.')');
		return $dato;
	}
}
?>