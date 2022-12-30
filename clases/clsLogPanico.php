<?php
class LogPanico {
	var $objSQLServer;

	function LogPanico($objSQLServer) {
		$this->objSQL = $objSQLServer;
		return TRUE;
	}

   function getLogPanico($arr_filtro){
   		
		$strSQL = " SELECT hp_id, un_nro_serie, mo_matricula, hp_latitud, hp_longitud, hp_tecnologia, hp_medio, hp_fecha_recibido ";
		$strSQL.= " , hp_nomenclado, hp_wifi_mac, hp_wifi_name, hp_evento, hp_ticket";
		$strSQL.= " FROM tbl_history_panic WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_id = hp_un_id ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = un_mo_id ";
		$strSQL.= " WHERE 1=1 ";
		if($arr_filtro['medio'] != ''){
			$strSQL.= " AND hp_medio = ".(int)$arr_filtro['medio'];	
		}
		if(!empty($arr_filtro['movil'])){
			$strSQL.= " AND (mo_matricula LIKE '%".$arr_filtro['movil']."%' OR un_nro_serie LIKE '%".$arr_filtro['movil']."%') ";	
		}
		
		if(!empty($arr_filtro['fecha'])){
			$arr_filtro['fecha'] = str_replace('/','-',$arr_filtro['fecha']);	
			$strSQL.= " AND convert(varchar, hp_fecha_recibido, 103) = '".date('d/m/Y',strtotime($arr_filtro['fecha']))."' ";	
		}
		
		$strSQL.= " ORDER BY hp_fecha_recibido DESC ";
		
		$result = $this->objSQL->dbQuery($strSQL);
		$rs = $this->objSQL->dbGetAllRows($result);
		$this->cantReg = $this->objSQL->dbNumRows($result);
		return $rs;
   }
}  
?>
