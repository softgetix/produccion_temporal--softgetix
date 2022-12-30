<?php
require_once 'clases/clsAbms.php';
class GeneradorDeInformes extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_informes','in');
		$this->nameFile = 'name-default';
	}

	function obtenerRegistros($filtros){
		$strSQL = " SELECT in_id, in_nombre, ite_nombre AS in_tipo_envio, in_adjunto, in_hora_envio, in_fecha_ultimo_envio, in_estado
				, c1.cl_razonSocial AS agente, c2.cl_razonSocial AS cliente
				FROM tbl_informes WITH(NOLOCK)
				INNER JOIN tbl_clientes c1 WITH(NOLOCK) ON c1.cl_id = in_cl_id_agente
				INNER JOIN tbl_informes_tipo_envio WITH(NOLOCK) ON ite_id = in_ite_id
				LEFT JOIN tbl_clientes c2 WITH(NOLOCK) ON c2.cl_id = in_cl_id_cliente
				WHERE in_borrado = 0 ";
		if(!empty($filtros['txtBuscador'])){
			$strSQL.= " AND in_nombre LIKE '%".$filtros['txtBuscador']."%'";	
		}
			
		if(!empty($filtros['cmbTipoEnvio'])){
			$strSQL.= " AND in_ite_id = ".(int)$filtros['cmbTipoEnvio'];	
		}	
		
		if(!empty($filtros['cmbAgente'])){
			$strSQL.= " AND in_cl_id_agente = ".(int)$filtros['cmbAgente'];	
		}	
		

		if ( $_SESSION['idPerfil'] == 37) {
		$strSQL.= "  and in_cl_id_agente =  15440";


		}

		
		$strSQL.= " ORDER BY in_fecha_alta DESC "; 
		$objRes = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($objRes, 3);
	}
	
	function duplicarRegistros($id){
		if($id){
			$txtCols = "in_nombre, in_cl_id_agente, in_cl_id_cliente, in_consulta, in_ite_id, in_hora_envio, in_subject, in_mensaje, in_adjunto, in_enviar_a_txt, in_enviar_a_us_id, in_enviar_copia_a";
			$strSQL = " INSERT INTO tbl_informes(".$txtCols.") SELECT ".$txtCols." FROM tbl_informes WHERE in_id = ".(int)$id;
			return $this->objSQL->dbQuery($strSQL);
		}	
		return false;
	}
	
	function cambiarEstadoInforme($id){
		if($id){
			$strSQL = 'UPDATE tbl_informes SET in_estado=ABS(in_estado-1) WHERE in_id = '.(int)$id; 
			return $this->objSQL->dbQuery($strSQL);
		}
		return false;
	}
	
	function generarAdjunto($strSQL){
		if(!empty($strSQL)){
			$strSQL = str_replace('&#039',"'",$strSQL);
			$strSQL = str_replace('&#39',"'",$strSQL);
			$strSQL = str_replace('&lt;',"<",$strSQL);
			$strSQL = str_replace('&gt;',">",$strSQL);
			
			$objRes = $this->objSQL->dbQuery($strSQL);
			$arrConsulta = $this->objSQL->dbGetAllRows($objRes,3);
			
			require_once 'clases/PHPExcel.php';
			$objPHPExcel = new PHPExcel();
		
			$encabezados = array_keys($arrConsulta[0]);
			
			$objPHPExcel->getProperties()
				->setCreator("Localizar-t")
				->setLastModifiedBy("Localizar-t")
				->setTitle('Informes')
				->setSubject('Informes')
				->setDescription('Informes')
				->setKeywords("Excel Office 2007 openxml php")
				->setCategory("Localizar-t");
			
			foreach($encabezados as $k => $item){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->getABC($k).'1', encode($item));
				$arralCol = array($this->getABC($k));
				$objPHPExcel->setFormatoRows($arralCol);
				//$alingCenterCol = array('A','E','F','G','H','L','M','N','O');
				//$objPHPExcel->alignCenter($alingCenterCol);
			}
			
			$i = 2;
			foreach($arrConsulta as $row){
				$k = 0;
				foreach($row as $item){
					//$objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->getABC($k).$i, encode($item));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit($this->getABC($k).$i, encode($item),PHPExcel_Cell_DataType::TYPE_STRING);
					$k++;
				}
				$i++;
			}
			
			$this->nameFile.= date('dmY').'.xlsx';
			$objPHPExcel->setActiveSheetIndex(0);	
			if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
			header('Content-Type: application/force-download');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$this->nameFile.'"');
			header('Cache-Control: max-age=0');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			header('Pragma: private');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
		}
	}
	
	
	function getABC($i){
		$arrayABC = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');	
		if($i > count($arrayABC) - 1){
			return $arrayABC[floor($i/count($arrayABC)) - 1].$arrayABC[$i-(floor($i/count($arrayABC)) * count($arrayABC))];
		}
		else{
			 return $arrayABC[$i];
		}
	}
	
	function getTipoEnvio(){
		$strSQL = " SELECT ite_id, ite_nombre FROM tbl_informes_tipo_envio WITH(NOLOCK) ORDER BY ite_nombre ";
		$objRes = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($objRes, 3);	
	}
	
	function getInformesPersonalizados($id = NULL){
		$idAgente = $_SESSION['idAgente'];
		
		$strSQL = " SELECT ip_id, ip_nombre, ite_nombre, ip_descripcion,  ipc_nombre, cl.* ";
		$strSQL.= " FROM tbl_informes_personalizados WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_informes_tipo_envio WITH(NOLOCK) ON ite_id = ip_ite_id ";
		$strSQL.= " INNER JOIN tbl_informes_personalizados_categorias WITH(NOLOCK) ON ipc_id = ip_ipc_id ";
		$strSQL.= " INNER JOIN tbl_informes_personalizados_agente WITH(NOLOCK) ON ipa_ip_id = ip_id ";
		$strSQL.= " LEFT JOIN tbl_informes_personalizados_clientes cl WITH(NOLOCK) ON ipc_ip_id = ip_id AND ipc_cl_id = ".(int)$_SESSION['idEmpresa'];
		$strSQL.= " WHERE ipa_cl_id = ".(int)$idAgente;
		if($id){
			$strSQL.= " AND ip_id = ".(int)$id;	
		}
		$objRes = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($objRes, 3);
	}
	
	function setInformePersonalizado($id, $status, $arrUsuarios = NULL){
		$strSQL = " SELECT ipc_id FROM tbl_informes_personalizados_clientes WITH(NOLOCK) WHERE ipc_ip_id = ".(int)$id." AND ipc_cl_id = ".(int)$_SESSION['idEmpresa'];
		$objRes = $this->objSQL->dbQuery($strSQL);
		$row =  $this->objSQL->dbGetRow($objRes, 0, 3);
		
		$arr = array();
		if($row['ipc_id']){
			if($status){//Editar Informe
				$us = implode(',',$arrUsuarios);
				$arr['ipc_activo'] = 1;
				$arr['ipc_us_id'] = $us?$us:NULL;
				if($this->objSQL->dbQueryUpdate($arr, 'tbl_informes_personalizados_clientes', 'ipc_id = '.(int)$row['ipc_id'])){
					return true;
				}
			}
			else{//Desactivar Informe
				$arr['ipc_activo'] = 0;
				if($this->objSQL->dbQueryUpdate($arr, 'tbl_informes_personalizados_clientes', 'ipc_id = '.(int)$row['ipc_id'])){
					return true;
				}
			}
		}
		elseif($status){//Alta Informe
			$us = implode(',',$arrUsuarios);
			$arr['ipc_ip_id'] = $id;
			$arr['ipc_cl_id'] = (int)$_SESSION['idEmpresa'];
			$arr['ipc_activo'] = 1;
			$arr['ipc_us_id'] = $us?$us:NULL;
			if($this->objSQL->dbQueryInsert($arr, 'tbl_informes_personalizados_clientes')){
				return true;	
			}	
		}
		return false;
	}
	
	
}