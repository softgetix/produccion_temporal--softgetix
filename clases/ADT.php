<?php
class ADT extends Abm {
	function __construct($objSQLServer, $id_viaje = 0, $rel = null) {
		$this->objSQL = $objSQLServer;
		$this->rel = $rel;
	}
		
	function Importar_Excel($files){
		define('ERR_SIN_PERMISOS', 4);
		
		require_once($this->rel.'clases/PHPExcel/IOFactory.php');
		$objExcel = PHPExcel_IOFactory::load($files['tmp_name']);
		
		$error_solapas = array('0' => array(), '1' => array());
		$error_total = array('0' => false, '1' => false);
		$error = false;
		//-- Se implementa esto porque el archivo contiene columnas calculadas --//
		$celdas = array('B','C','F','G','H','I','L','O','N','R');
		$objHojaExcel = $objExcel->getSheet(0);
		$objHoja = $this->obtenerDatosExcel($objHojaExcel,$celdas);
		if(count($objHoja) < 2){//--Volvemos a intentar porq esta planilla trae filas vacias
			$objHoja = $this->obtenerDatosExcel($objHojaExcel,$celdas, true);
		}
		//-- --//

		if($objHoja){
			$strSQL = "TRUNCATE TABLE tbl_agentes_adt_aprobaciones";
			$this->objSQL->dbQuery($strSQL);
			foreach($objHoja as $k => $item){
				$idCliente = $this->getIdCliente($item['R']);
				if($idCliente){
					$params = array(
						'aap_solicitud' => escapear_string($item['B'])
						,'aap_sitio' => escapear_string($item['C'])
						,'Aap_cl_id' => $idCliente
						,'Aap_fecha_aprobacion' => escapear_string($item['F'])
						,'Aap_mes_aprobacion' => escapear_string($item['G'])
						,'Aap_semana_aprobacion' => escapear_string($item['H'])
						,'aap_estado_venta' => escapear_string($item['I'])
						,'Aap_motivo_rechazo' => NULL
						,'Aap_nombre_provincia' => escapear_string($item['L'])
						,'Aap_producto' => escapear_string($item['O'])
						,'aap_promocion' => escapear_string($item['N'])
					);

					if(!$this->objSQL->dbQueryInsert($params, 'tbl_agentes_adt_aprobaciones')){
						array_push($error_solapas[0],$k+1);
						$error = true;
					}
				}
			}

			if(count($error_solapas[0]) == (count($objHoja) - 1)){
				$error_total[0] = true;
			}
		}
		
		//-- Se implementa esto porque el archivo contiene columnas calculadas --//
		$celdas = array('B','C','I','M','P','O','T');
		$objHojaExcel = $objExcel->getSheet(1);
		$objHoja = $this->obtenerDatosExcel($objHojaExcel,$celdas);
		if(count($objHoja) < 2){//--Volvemos a intentar porq esta planilla trae filas vacias
			$objHoja = $this->obtenerDatosExcel($objHojaExcel,$celdas, true);
		}
		//-- --//

		if($objHoja){
			$strSQL = "TRUNCATE TABLE tbl_agentes_adt_activaciones";
			$this->objSQL->dbQuery($strSQL);

			foreach($objHoja as $k => $item){
				$idCliente = $this->getIdCliente($item['T']);
				if($idCliente){
					$params = array(
						'aac_solicitud' => escapear_string($item['B'])
						,'aac_sitio' => escapear_string($item['C'])
						,'aac_cl_id' => $idCliente
						,'aac_fecha_Activacion' => escapear_string($item['I'])
						,'aac_mes_activacion' => NULL
						,'aac_semana_activacion' => NULL
						,'aac_provincia' => escapear_string($item['M'])
						,'aac_producto' => escapear_string($item['P'])
						,'aac_promocion' => escapear_string($item['O'])
					);
					
					if(!$this->objSQL->dbQueryInsert($params, 'tbl_agentes_adt_activaciones')){
						array_push($error_solapas[1],$k+1);
						$error = true;
					}
				}
			}

			if(count($error_solapas[1]) == (count($objHoja) - 1)){
				$error_total[1] = true;
			}
		}

		if(!$error){
			
			//-- Subida de Archivo --//
			$serverFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
			$rutaServer = $barra = '';
			foreach($serverFile as $item){
				if(strpos(strtolower($item),'.php') === false){
					$rutaServer.= $barra.$item;
					$barra = '/';
				}
			}
			
			$ruta = PATH_ATTACH.'/adt/adtaltamasiva/'.$_SESSION['idEmpresa']."/";
			if(!file_exists($ruta)){
				mkdir($ruta);				
			}
			$archivo = $ruta.getFechaServer('Ymd_His').'.'.extension_archivo($files['name']);
			copy($files['tmp_name'], $archivo);
			@chown($rutaServer.'/'.$archivo, 'root');
			//-- --//
		
			return 'Los datos se han procesado correctamente.';
		}
		else{
			if($error_total[0] || $error_total[1]){
				if($error_total[0] && $error_total[1]){
					$return = 'Se produjo un error y no se pudo procesar ningun registro.';
				}
				elseif($error_total[0]){
					$return = 'Se produjo un error y no se pudo procesar ningun registro de la solapa #1.';
				}
				else{
					$return = 'Se produjo un error y no se pudo procesar ningun registro de la solapa #2.';
				}
			}
			else{
				$return = 'Se produjo un error, uno o más registros no pudieron procesarce.';
				$return.= ' Verifique del archivo adjunto ';
			
				if($error_solapas[0] && $error_solapas[1]){
					$return.= 'la solapa #1 lineas: '.json_encode($error_solapas[0]);
					$return.= ' y de la solapa #2 lineas: '.json_encode($error_solapas[1]);
				}
				else{
					if($error_solapas[0]){
						$return.= 'la solapa #1 lineas: '.json_encode($error_solapas[0]);
					}
					else{
						$return.= 'la solapa #2 lineas: '.json_encode($error_solapas[1]);
					}
				}
			}
			
			return $return;
		}
	}

	function Importar_Excel2($files){
		define('ERR_SIN_PERMISOS', 4);
		
		require_once($this->rel.'clases/PHPExcel/IOFactory.php');
		$objExcel = PHPExcel_IOFactory::load($files['tmp_name']);
		
		$error = false;

		//-- Se implementa esto porque el archivo contiene columnas calculadas --//
		$celdas = array('A','B','C','D','E','F','G','H');
		$objHojaExcel = $objExcel->getSheet(0);
		$objHoja = $this->obtenerDatosExcel($objHojaExcel,$celdas);
		//-- --//
		
		unset($objHoja[0]); //Quito titulos

		if(!$error && $objHoja){
			$strSQL = "TRUNCATE TABLE tbl_agentes_adt_pendientes";
			$this->objSQL->dbQuery($strSQL);

			foreach($objHoja as $item){

				if(empty($item['A']) && empty($item['B']) && empty($item['C']) && empty($item['D']) && empty($item['E']) && empty($item['F']) && empty($item['G'])){
					break;
				}

				$idCliente = $this->getIdClienteCUIT($item['H']);
				//if($idCliente){
					$params = array(
						'aap_sitio' => escapear_string($item['A'])
						,'aap_estado_trabajo' => escapear_string($item['B'])
						,'aap_fecha_aprobacion' => escapear_string($item['C'])
						,'aap_zona' => escapear_string($item['D'])
						,'aap_observaciones' => escapear_string($item['E'])
						,'aap_Rechazo' => escapear_string($item['F'])
						,'aap_motivo' => escapear_string($item['G'])
						,'aap_cl_id' => $idCliente
					);
					if(!$this->objSQL->dbQueryInsert($params, 'tbl_agentes_adt_pendientes')){
						$error = true;
						break;
					}
				//}
			}
		}

		//-- Se implementa esto porque el archivo contiene columnas calculadas --//
		$celdas = array('A','B','C','D','E','F','G');
		$objHojaExcel = $objExcel->getSheet(1);
		$objHoja = $this->obtenerDatosExcel($objHojaExcel,$celdas);
		//-- --//
		
		unset($objHoja[0]); //Quito titulos
		
		if(!$error && $objHoja){
			$strSQL = "TRUNCATE TABLE tbl_agentes_adt_coordinadas";
			$this->objSQL->dbQuery($strSQL);

			foreach($objHoja as $item){
			
				if(empty($item['A']) && empty($item['B']) && empty($item['C']) && empty($item['D']) && empty($item['E']) && empty($item['F']) && empty($item['G'])){
					break;
				}
			
				$idCliente = $this->getIdClienteCUIT($item['F']);
				//if($idCliente){

					$auxFecha = explode('/',$item['D']);
					$fecha = $auxFecha[2].'-'.$auxFecha[0].'-'.$auxFecha[1];
					
					$params = array(						
						'aac_solicitud' => escapear_string($item['A'])
						,'aac_sitio_principal' => escapear_string($item['B'])
						,'aac_trabajo' => escapear_string($item['C'])
						,'aac_fecha_hora' => escapear_string($fecha).' '.escapear_string($item['E'])
						,'aac_canal_venta' => escapear_string($item['G'])
						,'aac_cl_id' => $idCliente
					);
					if(!$this->objSQL->dbQueryInsert($params, 'tbl_agentes_adt_coordinadas')){
						$error = true;
						break;
					}
				//}
			}
		}

		if(!$error){
			
			//-- Subida de Archivo --//
			$serverFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
			$rutaServer = $barra = '';
			foreach($serverFile as $item){
				if(strpos(strtolower($item),'.php') === false){
					$rutaServer.= $barra.$item;
					$barra = '/';
				}
			}
			
			$ruta = PATH_ATTACH.'/adt/adtaltamasiva2/'.$_SESSION['idEmpresa']."/";
			if(!file_exists($ruta)){
				mkdir($ruta);				
			}
			$archivo = $ruta.getFechaServer('Ymd_His').'.'.extension_archivo($files['name']);
			copy($files['tmp_name'], $archivo);
			@chown($rutaServer.'/'.$archivo, 'root');
			//-- --//
		
			return 'Los datos se han procesado correctamente.';
		}
		else{
			return 'No se han procesado ningún dato. Ferifique el archivo adjunto.';
		}
	}

	function Importar_Excel3($files){
		define('ERR_SIN_PERMISOS', 4);
		
		require_once($this->rel.'clases/PHPExcel/IOFactory.php');
		$objExcel = PHPExcel_IOFactory::load($files['tmp_name']);
		
		$error = false;

		//-- Se implementa esto porque el archivo contiene columnas calculadas --//
		$celdas = array('A','C','D','E','G','H','I','J','K','L','M','N');
		$objHojaExcel = $objExcel->getSheet(0);
		$objHoja = $this->obtenerDatosExcel($objHojaExcel,$celdas);
		//-- --//

		unset($objHoja[0]); //Quito titulos

		if(!$error && $objHoja){
			$strSQL = "TRUNCATE TABLE tbl_agentes_adt_no_trabajables";
			$this->objSQL->dbQuery($strSQL);

			foreach($objHoja as $item){

				if(empty($item['A']) && empty($item['C']) && empty($item['D']) && empty($item['E']) && empty($item['G']) && empty($item['H']) && empty($item['I'])
					&& empty($item['J']) && empty($item['K']) && empty($item['L']) && empty($item['M']) /*&& empty($item['N'])*/
				){
					break;
				}
				
				$idCliente = $this->getIdCliente($item['H']);
				//if($idCliente){
					$params = array(
						'aan_solicitud' => escapear_string($item['A'])
						,'aan_sitio' => escapear_string($item['C'])
						,'aan_fecha_aprobacion' => escapear_string($item['D'])
						,'aan_trabajo' => escapear_string($item['E'])
						,'aan_Fecha_Trabajo' => escapear_string($item['G'])
						,'aan_nombre_cliente' => escapear_string($item['I'])
						,'aan_estado_venta' => escapear_string($item['J'])
						,'aan_nombre_provincia' => escapear_string($item['K'])
						,'aan_nombre_localidad' => escapear_string($item['L'])
						,'aan_Rechazo' => escapear_string($item['M'])
						,'aan_observaciones' => escapear_string($item['N'])
						,'aan_cl_id' => $idCliente
					);
					if(!$this->objSQL->dbQueryInsert($params, 'tbl_agentes_adt_no_trabajables')){
						$error = true;
						break;
					}
				//}
			}
		}

		if(!$error){			
			//-- Subida de Archivo --//
			$serverFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
			$rutaServer = $barra = '';
			foreach($serverFile as $item){
				if(strpos(strtolower($item),'.php') === false){
					$rutaServer.= $barra.$item;
					$barra = '/';
				}
			}
			
			$ruta = PATH_ATTACH.'/adt/adtaltamasiva3/'.$_SESSION['idEmpresa']."/";
			if(!file_exists($ruta)){
				mkdir($ruta);				
			}
			$archivo = $ruta.getFechaServer('Ymd_His').'.'.extension_archivo($files['name']);
			copy($files['tmp_name'], $archivo);
			@chown($rutaServer.'/'.$archivo, 'root');
			//-- --//
		
			return 'Los datos se han procesado correctamente.';
		}
		else{
			return 'No se han procesado ningún dato. Ferifique el archivo adjunto.';
		}
	}

	private function getIdCliente($cliente){
		$strSQL = "SELECT cl_id FROM tbl_clientes WHERE cl_razonSocial = '".trim(escapear_string($cliente))."' AND cl_id_distribuidor = ".$_SESSION['idAgente'];

		$obj = $this->objSQL->dbQuery($strSQL);
		$row = $this->objSQL->dbGetRow($obj);
		return intval($row['cl_id']);
	}

	private function getIdClienteCUIT($cliente){
		$strSQL = "SELECT cl_id FROM tbl_clientes WHERE cl_cuit = '".trim(escapear_string($cliente))."' AND cl_id_distribuidor = ".$_SESSION['idAgente'];
		$obj = $this->objSQL->dbQuery($strSQL);
		$row = $this->objSQL->dbGetRow($obj);
		return intval($row['cl_id']);
	}

	private function obtenerDatosExcel($objHojaExcel,$celdas, $ignorebreak = false){
		$iRow = 0;
		$objHoja = NULL;
		foreach ($objHojaExcel->getRowIterator() as $row){
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
			$objHoja[$iRow] = array();
			$empty = 0;
			foreach ($cellIterator as $cell){
				if(!is_null($cell)){
					$column = $cell->getColumn();
					if(in_array($column,$celdas)){
						$value = $cell->getValue();
						if(empty($value)){
							$empty++;
						}
						elseif(strstr($value,'=')==true){//PHPExcel_Cell_Hyperlink
							$hyperlink = $cell->getHyperlink()->getUrl();
							if(empty($hyperlink)){
								$auxvalue = $cell->getOldCalculatedValue();
								$value = !empty($auxvalue) ? $auxvalue : $value;
							}
						}
						elseif(PHPExcel_Shared_Date::isDateTime($cell)){
							$value = $cell->getFormattedValue();
						}
						
						$objHoja[$iRow][$column] = $value;
					}
				}
			}

			if(count($objHoja[$iRow]) == $empty && !$ignorebreak){
				break;
			}

			$iRow++;
		} 
		return $objHoja;
	}

	function getContenidoTableroControl($tipo = 0, $fdesde = NULL, $fhasta = NULL){
		$tables = array();
		$number_resquest = 1;
		do{
			$strSQL = " EXEC db_agentes_estadisticas {$_SESSION['idUsuario']}, {$number_resquest}, {$tipo}, '{$fdesde}', '{$fhasta}'";

			$objRes = NULL;
			$objRes = $this->objSQL->dbQuery($strSQL);
			$res = $this->objSQL->dbGetAllRows($objRes,3);
			if(isset($res[0]['data'])){
				if($res[0]['data'] == '-1'){
					$number_resquest = '-1'; //--No hay mas consultas por buscar.
				}
				else{
					return false;
				}
			}
			elseif($tipo == 1){
				if($res){
					array_push($tables, $res);
				}
				$number_resquest++;
			}
			else{
				array_push($tables, $res);
				$number_resquest++;
			}
		}while($number_resquest != '-1');

		return $tables;
	}

	function getColumLetter($i){
		$arrayABC = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');	
		if($i > count($arrayABC) - 1){
			return $arrayABC[floor($i/count($arrayABC)) - 1].$arrayABC[$i-(floor($i/count($arrayABC)) * count($arrayABC))];
		}
		else{
			return $arrayABC[$i];
		}
	}

	function Importar_Excel_Facturas($files){
		define('ERR_SIN_PERMISOS', 4);
		
		require_once($this->rel.'clases/PHPExcel/IOFactory.php');
		$objExcel = PHPExcel_IOFactory::load($files['tmp_name']);
		$error = false;

		//-- Se implementa esto porque el archivo contiene columnas calculadas --//
		$celdas = array('A','B','C','D','E','F','G','H','I');
		$objHojaExcel = $objExcel->getSheet(0);
		$objHoja = $this->obtenerDatosExcel($objHojaExcel,$celdas);
		//-- --//

		if(!$error && $objHoja){

			//-- --//
			require_once $this->rel.'clases/clsCalendario.php';
			$objCalendario = new Calendario;
			$objCalendario->calendario();

			require ($this->rel.'clases/clsPhoneNumberAr.php');
			
			//$nro_factura = $objCalendario->meses[intval(date('m')) - 1].'-'.date('y');
			//-- --//
			unset($objHoja[0]);//--Elimino la fila de titulos.
			foreach($objHoja as $col){
				$col = escapear_array($col);

				$mes = $col['H'];
				$nro_factura = $mes;
				if(trim(strtolower($nro_factura)) == 't'){
					$nro_factura = 'T'; //--Factura de Servicio Tecnico
				}
				elseif(intval($mes)){
					if($mes >= 1 && $mes <= 12){
						$nro_factura = $objCalendario->meses[$mes-1].'-'.date('y');
					}
				}

				//--Ini. Formateo nro cel
				/*$phoneaux = $col['F'];
				if (substr($col['F'],0,2) == '15' && strlen ($col['F']) == 10 )
				{
				$phoneaux = substr($col['F'],2,8) ;
				$phoneaux = "11".$phoneaux;
				}

				
				$phone = new PhoneNumberAr(NULL,$phoneaux);
				$numbers = $phone->validateGuestNumber(); 
				if(empty($numbers['guest_number_e164'])){
					$phone_number = $col['F'];
					$phone_format = 0;
				}
				else{
					$phone_number = $numbers['guest_number_e164'];
					$phone_format = 1;
				}*/
				//--Fin. 

				$params = array(
					'cliente'=>$col['A']
					,'email'=>decode($col['B'])
					,'nro_factura'=>$nro_factura
					,'url'=>$col['I']
					,'direccion'=>$col['D'].' - '.$col['E']
					,'importe'=>is_float($col['G']) ? $col['G'] : (float)str_replace(',','.',filter_var($col['G'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND))
					,'identificador'=>$col['F']
					//,'telefono'=>$phone_number
					//,'telefono_e164'=>$phone_format
				);

				if($params['cliente']){
					/*$strSQL = " SELECT COUNT(*) AS cant FROM tbl_adt_facturas WHERE cliente = ".$params['cliente']." AND nro_factura = '".$params['nro_factura']."'";
					//$objSQL = $this->objSQL->dbQuery($strSQL);
					//$row = $this->objSQL->dbGetRow($objSQL,0,3);
					if(!$row['cant']){*/
						//----
						if(!$this->objSQL->dbQueryInsert($params, 'tbl_adt_facturas')){
							$error = true;
							break;
						}/**/
						//----
					//}
				}
			}
		}

		if(!$error){
			
			//-- Subida de Archivo --//
			$serverFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
			$rutaServer = $barra = '';
			foreach($serverFile as $item){
				if(strpos(strtolower($item),'.php') === false){
					$rutaServer.= $barra.$item;
					$barra = '/';
				}
			}
			
			$ruta = PATH_ATTACH.'/adt/adtcargafacturas/';
			if(!file_exists($ruta)){
				mkdir($ruta);				
			}
			$archivo = $ruta.getFechaServer('Ymd_His').'.'.extension_archivo($files['name']);
			copy($files['tmp_name'], $archivo);
			@chown($rutaServer.'/'.$archivo, 'root');
			//-- --//
		
			return 'Los datos se han procesado correctamente.';
		}
		else{
			return 'No se han procesado ningún dato. Ferifique el archivo adjunto.';
		}
	}
	
}