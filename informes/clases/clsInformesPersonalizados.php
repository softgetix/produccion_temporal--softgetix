<?php
class InformesPersonalizados extends Informes{
	
	function __construct($objSQLServer){
		$this->objSQL = $objSQLServer;
		$this->idInforme;
		
	}
	
	function getInformesAEnviar($filtro){
		$strSQL = " SELECT ip_id, ip_nombre as asunto FROM tbl_informes_personalizados ";
		$strSQL = " SELECT ip_id, ip_nombre as asunto, ipc_id, ipc_cl_id, ipc_us_id FROM tbl_informes_personalizados WITH(NOLOCK)";
		$strSQL.= " INNER JOIN tbl_informes_personalizados_clientes WITH(NOLOCK) ON ipc_ip_id = ip_id ";
		$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = ipc_cl_id ";
		//$strSQL.= " WHERE ipc_activo = 1 AND cl_borrado = 0 AND ip_ite_id = ".(int)$filtro['tipo_envio'];
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$arrRows = $this->objSQL->dbGetAllRows($objRes,3);
		return $arrRows;
	}
	
	function generarAdjunto($idInforme, $idCliente){
		require_once 'clases/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		
		if($this->generarColumnasAdjunto($objPHPExcel, $idInforme, $idCliente)){
			$objPHPExcel->getProperties()
				->setCreator("Localizar-t")
				->setLastModifiedBy("Localizar-t")
				->setTitle('Informes')
				->setSubject('Informes')
				->setDescription('Informes')
				->setKeywords("Excel Office 2007 openxml php")
				->setCategory("Localizar-t");
				
			$this->nameFile.= date('dmY').'.xlsx';
			$objPHPExcel->setActiveSheetIndex(0);	
			$this->nameFile = '../emailer/adjuntos/'.$this->nameFile;
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->setIncludeCharts(true); 
			$objWriter->save($this->nameFile);
			return true;
		}
		return false;
	}
	
	function generarColumnasAdjunto($objPHPExcel, $idInforme, $idCliente){
		
		switch($idInforme){
			//-- Informe de Km Recorridos por Móvil
			case 1:
			case 15:
				$strSQL = "	DECLARE @tablaMoviles TABLE(
								idx INT IDENTITY(1,1),
								mo_id INT
							);
							INSERT INTO @tablaMoviles
							SELECT DISTINCT(um_mo_id) as idMovil
							FROM tbl_usuarios WITH(NOLOCK)
							INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_us_id = us_id
							INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id AND mo_borrado = 0
							INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id AND un_borrado = 0
							WHERE us_cl_id = ".(int)$idCliente."
							
							DECLARE @idx INT = 1;
							DECLARE @idxMax INT = (SELECT COUNT(idx) FROM @tablaMoviles);
							DECLARE @idMovil INT;
							
							DECLARE @strIdMovil VARCHAR(MAX) = ''
							DECLARE @coma VARCHAR(1) = ''
							WHILE (@idx <= @idxMax)
							BEGIN
								SELECT @idMovil = mo_id FROM @tablaMoviles WHERE idx = @idx
								SET @strIdMovil = @strIdMovil+@coma+CONVERT(VARCHAR,@idMovil); 
								SET @coma = ','
								SET @idx = @idx + 1;
							END	
							
							DECLARE @fechaIni DATETIME = CONVERT(NVARCHAR, GETDATE() - 7,106)+' 00:00'
							DECLARE @fechaFin DATETIME = CONVERT(NVARCHAR, GETDATE() - 1,106)+' 23:59'
							EXEC informeKMsRecorridos @fechaIni, @fechaFin,@strIdMovil,0,1
						";
				$objRes = $this->objSQL->dbQuery($strSQL);	
				$arrConsulta = $this->objSQL->dbGetAllRows($objRes,3);		
				
				//--Traducciones--//
				switch($idInforme){
					case 15:
						$lang = 'en';
						$langCol1 = 'Asset name';
						$langCol2 = 'Group';
						$langCol3 = 'Miles travelled';
						$langCol4 = 'Date';
						$langCol5 = 'Day';
						$diaSemana = array('Lunes'=>'Monday','Martes'=>'Tuesday','Miércoles'=>'Wednesday','Jueves'=>'Thursday','Viernes'=>'Friday','Sábado'=>'Saturday','Domingo'=>'Sunday');
					break;
					default:
						$lang = 'es';
						$langCol1 = 'Patente';
						$langCol2 = 'Flota';
						$langCol3 = 'Km Recorridos';
						$langCol4 = 'Fecha';
						$langCol5 = 'Día';
						$diaSemana = array('Lunes'=>'Lunes','Martes'=>'Martes','Miércoles'=>'Miércoles','Jueves'=>'Jueves','Viernes'=>'Viernes','Sábado'=>'Sábado','Domingo'=>'Domingo');
					break;	
				}
				//-- --//
				
				if($arrConsulta){
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($this->getABC(0).'1', $langCol1)
						->setCellValue($this->getABC(1).'1', $langCol2)
						->setCellValue($this->getABC(2).'1', $langCol3)
						->setCellValue($this->getABC(3).'1', $langCol4)
						->setCellValue($this->getABC(4).'1', $langCol5);
							
					for($i=0; $i<=4; $i++){	
						$arralCol = array($this->getABC($i));
						$objPHPExcel->setFormatoRows($arralCol);
					}
					
					$alingCenterCol = array('C','D');
					$objPHPExcel->alignCenter($alingCenterCol);
					
					$i = 2;
					foreach($arrConsulta as $row){
						$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($this->getABC(0).$i, encode($row['Patente']))
							->setCellValue($this->getABC(1).$i, encode($row['Flota']))
							->setCellValue($this->getABC(2).$i, encode($row['KmRecorrido']))
							->setCellValue($this->getABC(3).$i, formatearFecha($row['Fecha'], $lang, 'date'))
							->setCellValue($this->getABC(4).$i, $diaSemana[encode($row['Dia'])]);
						$i++;
					}
					return true;
				}
			break;
			//-- Informe de Costo Total por Tarea
			case 2:
			case 16:
				$strSQL = "	SELECT DISTINCT(vi_id), vi_codigo, cl_razonSocial ";
				$strSQL.= "	FROM tbl_viajes WITH(NOLOCK) ";
				$strSQL.= "	INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = vi_transportista ";
				$strSQL.= "	INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_vi_id = vi_id ";
				$strSQL.= "	WHERE vi_dador = ".(int)$idCliente." AND vi_borrado = 0 AND vi_delivery = 0 ";
				$strSQL.= "	AND vi_fechacreado >= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 7,106)+' 00:00') AND vi_fechacreado <= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 1,106)+' 23:59') ";
				$strSQL.= "	ORDER BY vi_codigo, cl_razonSocial ";
				$objRes = $this->objSQL->dbQuery($strSQL);	
				$arrRowsViajes = $this->objSQL->dbGetAllRows($objRes,3);		
				
				//--Traducciones--//
				switch($idInforme){
					case 16:
						$lang = 'en';
						$langCol1 = 'Task Number';
						$langCol2 = 'Group';
						$langCol3 = 'Stay perior (in hours)';
						$langCol4 = 'Miles traveled (miles)';
						$langCol5 = 'Total cost ($)';
						$langStat1 = 'Parameters (complete here)';
						$langStat2 = 'Cost per mile  ($/mile)';
						$langStat3 = 'Cost per stay hour ($/hour)';
					break;
					default:
						$lang = 'es';
						$langCol1 = 'Tarea N°';
						$langCol2 = 'Flota';
						$langCol3 = 'Estadía acumulada (hora)';
						$langCol4 = 'Distancia acumulada (km)';
						$langCol5 = 'Costo acumulado ($)';
						$langStat1 = 'Variables a Configurar';
						$langStat2 = 'Costo por km recorrido ($/km)';
						$langStat3 = 'Costo por hora de estadia ($/hora)';
					break;	
				}
				//-- --//
				
				if($arrRowsViajes){
					///---///
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($this->getABC(0).'7', $langCol1)
						->setCellValue($this->getABC(1).'7', $langCol2)
						->setCellValue($this->getABC(2).'7', $langCol3)
						->setCellValue($this->getABC(3).'7', $langCol4)
						->setCellValue($this->getABC(4).'7', $langCol5);
							
					for($i=0; $i<=4; $i++){	
						$arralCol = array($this->getABC($i));
						$objPHPExcel->setFormatoRows($arralCol,7);
					}
					
					//$alingCenterCol = array('A');
					//$objPHPExcel->alignCenter($alingCenterCol);
					///---///
					
					$i = 8;
					foreach($arrRowsViajes as $rowViaje){
						$distancia = 0;
						$estadia = 0;
						
						$strSQL = "	SELECT vd_ini_real, vd_fin_real, rc_latitud, rc_longitud ";
						$strSQL.= "	FROM tbl_viajes_destinos WITH(NOLOCK) ";
						$strSQL.= "	INNER JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON rc_re_id = vd_re_id ";
						$strSQL.= "	WHERE vd_vi_id = ".(int)$rowViaje['vi_id']." AND rc_re_id != 6464 ";
						$strSQL.= "	ORDER BY vd_orden ";
						$objRes = $this->objSQL->dbQuery($strSQL);	
						$arrRowsDestinos = $this->objSQL->dbGetAllRows($objRes,3);
						if($arrRowsDestinos){
							foreach($arrRowsDestinos as $k => $rowDestino){
								if($k > 0){
									if($rowDestino['vd_ini_real'] && $rowDestino['vd_fin_real']){
										$distancia = $distancia + distancia($arrRowsDestinos[$k-1]['rc_latitud'],$arrRowsDestinos[$k-1]['rc_longitud'],$rowDestino['rc_latitud'],$rowDestino['rc_longitud']);
									}
								}
								
								if($rowDestino['vd_ini_real'] && $rowDestino['vd_fin_real']){
									$estadia = $estadia + strtotime($rowDestino['vd_fin_real']) - strtotime($rowDestino['vd_ini_real']);
								}
							}	
						}
						
						$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($this->getABC(0).$i, encode($rowViaje['vi_codigo']))
							->setCellValue($this->getABC(1).$i, encode($rowViaje['cl_razonSocial']))
							->setCellValue($this->getABC(2).$i, floor($estadia/3600))
							->setCellValue($this->getABC(3).$i, formatearDistancia($distancia, $lang))
							->setCellValue($this->getABC(4).$i, '=(D'.$i.'*$B$3)+($B$4*C'.$i.')');
							
							$objPHPExcel->alignLeft(array('A'.$i));
						$i++;
					}	
					
					///---///
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $langStat1);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', $langStat2);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', '0');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', $langStat3);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B4', '0');
						
					$objPHPExcel->setFormatoRows(array('A','B'),2,'D8D8D8');
					$objPHPExcel->setFormatoRows(array('A','B'),3);
					$objPHPExcel->setFormatoRows(array('A','B'),4);
						
					$objPHPExcel->alignLeft(array('A2'));
					$objPHPExcel->alignLeft(array('A3'));
					$objPHPExcel->alignLeft(array('A4'));
					///---///
					
					return true;
				}
			break;
			//-- Informe de Costo Detallado por Waipoint Visitado
			case 3:
			case 17: 
				$strSQL = "	SELECT vi_id, re_id, vi_codigo, cl_razonSocial, re_nombre, vd_ini_real, vd_fin_real, rc_latitud, rc_longitud ";
				$strSQL.= "	FROM tbl_viajes WITH(NOLOCK) ";
				$strSQL.= "	INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = vi_transportista ";
				$strSQL.= "	INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_vi_id = vi_id ";
				$strSQL.= "	INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
				$strSQL.= "	INNER JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON rc_re_id = re_id ";
				$strSQL.= "	WHERE vi_dador = ".(int)$idCliente." AND vi_borrado = 0 AND vi_delivery = 0 ";
				$strSQL.= "	AND vi_fechacreado >= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 7,106)+' 00:00') AND vi_fechacreado <= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 1,106)+' 23:59') ";
				$strSQL.= "	ORDER BY vi_codigo, vd_orden, cl_razonSocial ";
				$objRes = $this->objSQL->dbQuery($strSQL);	
				$arrRowsViajes = $this->objSQL->dbGetAllRows($objRes,3);	
				
				//--Traducciones--//
				switch($idInforme){
					case 17:
						$lang = 'en';
						$langCol1 = 'Task Number';
						$langCol2 = 'Group';
						$langCol3 = 'Destination';
						$langCol4 = 'Real time of arrival';
						$langCol5 = 'Real time of departure';
						$langCol6 = 'Stay period (hour)';
						$langCol7 = 'Miles per task segment(miles)';
						$langCol8 = 'Cost per task segment ($)';
						$langStat1 = 'Parameter (complete here)';
						$langStat2 = 'Costo per mile ($/mile)';
						$langStat3 = 'Costo per stay hour ($/hour)';
					break;
					default:
						$lang = 'es';
						$langCol1 = 'Tarea N°';
						$langCol2 = 'Flota';
						$langCol3 = 'Waypoints';
						$langCol4 = 'Ingreso Real';
						$langCol5 = 'Egreso Real';
						$langCol6 = 'Estadía (hora)';
						$langCol7 = 'Distancia por tramo (km)';
						$langCol8 = 'Costo por tramo ($)';
						$langStat1 = 'Variables a Configurar';
						$langStat2 = 'Costo por km recorrido ($/km)';
						$langStat3 = 'Costo por hora de estadia ($/hora)';
					break;	
				}
				//-- --//
				
				if($arrRowsViajes){
					///---///
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($this->getABC(0).'7', $langCol1)
						->setCellValue($this->getABC(1).'7', $langCol2)
						->setCellValue($this->getABC(2).'7', $langCol3)
						->setCellValue($this->getABC(3).'7', $langCol4)
						->setCellValue($this->getABC(4).'7', $langCol5)
						->setCellValue($this->getABC(5).'7', $langCol6)
						->setCellValue($this->getABC(6).'7', $langCol7)
						->setCellValue($this->getABC(7).'7', $langCol8);
							
					for($i=0; $i<=7; $i++){	
						$arralCol = array($this->getABC($i));
						$objPHPExcel->setFormatoRows($arralCol,7);
					}
					
					$alingCenterCol = array('D','E');
					$objPHPExcel->alignCenter($alingCenterCol);
					///---///
					
					$vi_id = $lat = $lng = 0;
					$i = 8;
					foreach($arrRowsViajes as $rowViaje){
						
						if($rowViaje['vd_ini_real'] && $rowViaje['vd_fin_real']){
							if($vi_id != $rowViaje['vi_id']){
								$lat = $rowViaje['rc_latitud'];
								$lng = $rowViaje['rc_longitud'];
								$distancia = 0;
							}
							else{
								$distancia = distancia($lat,$lng,$rowViaje['rc_latitud'],$rowViaje['rc_longitud']);	
							}
						}
						elseif($vi_id != $rowViaje['vi_id']){
							$distancia = 0;
						}
						
						$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($this->getABC(0).$i, encode($rowViaje['vi_codigo']))
							->setCellValue($this->getABC(1).$i, encode($rowViaje['cl_razonSocial']))
							->setCellValue($this->getABC(2).$i, encode($rowViaje['re_nombre']))
							->setCellValue($this->getABC(3).$i, $rowViaje['vd_ini_real']?formatearFecha($rowViaje['vd_ini_real'], $lang):'')
							->setCellValue($this->getABC(4).$i, $rowViaje['vd_fin_real']?formatearFecha($rowViaje['vd_fin_real'], $lang):'')
							->setCellValue($this->getABC(5).$i, floor((strtotime($rowViaje['vd_fin_real']) - strtotime($rowViaje['vd_ini_real']))/3600))
							->setCellValue($this->getABC(6).$i, formatearDistancia($distancia, $lang))
							->setCellValue($this->getABC(7).$i, '=(G'.$i.'*$B$3)+($B$4*F'.$i.')');
							
							$objPHPExcel->alignLeft(array('A'.$i));
							
						$i++;
						$vi_id = $rowViaje['vi_id'];
					}
					
					///---///
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $langStat1);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', $langStat2);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', '0');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', $langStat3);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B4', '0');
						
					$objPHPExcel->setFormatoRows(array('A','B'),2,'D8D8D8');
					$objPHPExcel->setFormatoRows(array('A','B'),3);
					$objPHPExcel->setFormatoRows(array('A','B'),4);
						
					$objPHPExcel->alignLeft(array('A2'));
					$objPHPExcel->alignLeft(array('A3'));
					$objPHPExcel->alignLeft(array('A4'));
					///---/// 
					
					return true;
				}
			break;
			//-- Auditoria de Uso de Activos
			case 4:
			case 18:
				$strSQL = "	DECLARE @tablaMoviles TABLE(idx INT IDENTITY(1,1),mo_id INT);
							INSERT INTO @tablaMoviles
							SELECT DISTINCT(um_mo_id) as idMovil
							FROM tbl_usuarios WITH(NOLOCK)
							INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_us_id = us_id
							INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id AND mo_borrado = 0
							INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id AND un_borrado = 0
							WHERE us_cl_id = ".(int)$idCliente."
							
							DECLARE @idx INT = 1;
							DECLARE @idxMax INT = (SELECT COUNT(idx) FROM @tablaMoviles);
							DECLARE @idMovil INT;
							
							DECLARE @strIdMovil VARCHAR(MAX) = ''
							DECLARE @coma VARCHAR(1) = ''
							WHILE (@idx <= @idxMax)
							BEGIN
								SELECT @idMovil = mo_id FROM @tablaMoviles WHERE idx = @idx
								SET @strIdMovil = @strIdMovil+@coma+CONVERT(VARCHAR,@idMovil); 
								SET @coma = ','
								SET @idx = @idx + 1;
							END	
							
							DECLARE @fechaIni DATETIME = CONVERT(NVARCHAR, GETDATE() - 7,106)+' 00:00'
							DECLARE @fechaFin DATETIME = CONVERT(NVARCHAR, GETDATE() - 1,106)+' 23:59'
							EXEC informeKMsRecorridos @fechaIni, @fechaFin,@strIdMovil,1,1
						";
				$objRes = $this->objSQL->dbQuery($strSQL);	
				$arrConsulta = $this->objSQL->dbGetAllRows($objRes,3);	
				
				//--Traducciones--//
				switch($idInforme){
					case 18:
						$lang = 'en';
						$langCol1 = 'Asset name';
						$langCol2 = 'Group';
						$langCol3 = 'Miles traveled';
						$langCol4 = 'Top speed (miles/hour)';
						$langCol5 = 'Date';
						$langCol6 = 'Day';
						$diaSemana = array('Lunes'=>'Monday','Martes'=>'Tuesday','Miércoles'=>'Wednesday','Jueves'=>'Thursday','Viernes'=>'Friday','Sábado'=>'Saturday','Domingo'=>'Sunday');
					break;
					default:
						$lang = 'es';
						$langCol1 = 'Patente';
						$langCol2 = 'Flota';
						$langCol3 = 'Km Recorridos';
						$langCol4 = 'Velocidad Máxima (Km/h)';
						$langCol5 = 'Fecha';
						$langCol6 = 'Día';
						$diaSemana = array('Lunes'=>'Lunes','Martes'=>'Martes','Miércoles'=>'Miércoles','Jueves'=>'Jueves','Viernes'=>'Viernes','Sábado'=>'Sábado','Domingo'=>'Domingo');
					break;	
				}
				//-- --//
				
				if($arrConsulta){
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($this->getABC(0).'1', $langCol1)
						->setCellValue($this->getABC(1).'1', $langCol2)
						->setCellValue($this->getABC(2).'1', $langCol3)
						->setCellValue($this->getABC(3).'1', $langCol4)
						->setCellValue($this->getABC(4).'1', $langCol5)
						->setCellValue($this->getABC(5).'1', $langCol6);
							
					for($i=0; $i<=5; $i++){	
						$arralCol = array($this->getABC($i));
						$objPHPExcel->setFormatoRows($arralCol);
					}
					
					$alingCenterCol = array('C','D','E');
					$objPHPExcel->alignCenter($alingCenterCol);
					
					$i = 2;
					foreach($arrConsulta as $row){
						$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($this->getABC(0).$i, encode($row['Patente']))
							->setCellValue($this->getABC(1).$i, encode($row['Flota']))
							->setCellValue($this->getABC(2).$i, encode($row['KmRecorrido']))
							->setCellValue($this->getABC(3).$i, encode($row['VelMax']))
							->setCellValue($this->getABC(4).$i, formatearFecha($row['Fecha'], $lang, 'date'))
							->setCellValue($this->getABC(5).$i, $diaSemana[encode($row['Dia'])]);
						$i++;
					}
					return true;
				}
			break;
			//-- Informe de Calidad de Servicio por Flota
			case 5:
			case 19: 
				$strSQL = "	SELECT vi_id, re_id, vi_codigo, cl_razonSocial, re_nombre, vd_ini, vd_fin,  vd_ini_real, vd_fin_real ";
				$strSQL.= "	FROM tbl_viajes WITH(NOLOCK) ";
				$strSQL.= "	INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = vi_transportista ";
				$strSQL.= "	INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_vi_id = vi_id ";
				$strSQL.= "	INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
				$strSQL.= "	WHERE vi_dador = ".(int)$idCliente." AND vi_borrado = 0 AND vi_delivery = 0 ";
				$strSQL.= "	AND vi_fechacreado >= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 7,106)+' 00:00') AND vi_fechacreado <= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 1,106)+' 23:59') ";
				$strSQL.= "	ORDER BY vi_codigo, vd_orden, cl_razonSocial ";
				$objRes = $this->objSQL->dbQuery($strSQL);	
				$arrRowsViajes = $this->objSQL->dbGetAllRows($objRes,3);	
				
				//--Traducciones--//
				switch($idInforme){
					case 19:
						$lang = 'en';
						$langCol1 = 'Task number';
						$langCol2 = 'Group';
						$langCol3 = 'Destination';
						$langCol4 = 'Arrival to destination';
						$langCol5 = 'Departure from destinations';
						$langStat1 = 'Task status';
						$langStat2 = 'Arrivals to Destinations';
						$langStat3 = 'Departures from destinations';
						$langStat4 = 'On Time';
						$langStat5 = 'Delayed ';
						$langStat6 = 'No data';
						$langGraff1 = 'Quality of service per group';
					break;
					default:
						$lang = 'es';
						$langCol1 = 'Tarea N°';
						$langCol2 = 'Flota';
						$langCol3 = 'Waypoint';
						$langCol4 = 'Arribo en Destino';
						$langCol5 = 'Partida de Destino';
						$langStat1 = 'Estado de la Tarea';
						$langStat2 = 'Arribos a Waypoints';
						$langStat3 = 'Partidas de Waypoints';
						$langStat4 = 'En tiempo';
						$langStat5 = 'Atrasado';
						$langStat6 = 'Sin datos';
						$langGraff1 = 'Calidad de Servicio por Flota';
					break;	
				}
				//-- --//
				
				$i = 19;
				if($arrRowsViajes){
					///---///
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($this->getABC(0).$i, $langCol1)
						->setCellValue($this->getABC(1).$i, $langCol2)
						->setCellValue($this->getABC(2).$i, $langCol3)
						->setCellValue($this->getABC(3).$i, $langCol4)
						->setCellValue($this->getABC(4).$i, $langCol5);
							
					for($r=0; $r<=4; $r++){	
						$arralCol = array($this->getABC($r));
						$objPHPExcel->setFormatoRows($arralCol,$i);
					}
					
					//$alingCenterCol = array('D','E');
					//$objPHPExcel->alignCenter($alingCenterCol);
					///---///
					
					$stat['ingreso'] = $stat['egreso'] = array('en_tiempo' => 0, 'atrasado' => 0, 'sin_datos' => 0);
					$i++; 
					foreach($arrRowsViajes as $rowViaje){
						
						if($rowViaje['vd_ini_real']){
							if(strtotime($rowViaje['vd_ini_real']) > strtotime($rowViaje['vd_ini'])){
								$txt_ini = $langStat5;
								$stat['ingreso']['atrasado']++;
							}
							else{
								$txt_ini = $langStat4;
								$stat['ingreso']['en_tiempo']++;
							}
						}
						else{
							$txt_ini = $langStat6;
							$stat['ingreso']['sin_datos']++;
						}
						
						if($rowViaje['vd_fin_real']){
							if(strtotime($rowViaje['vd_fin_real']) > strtotime($rowViaje['vd_fin'])){
								$txt_fin = $langStat5;
								$stat['egreso']['atrasado']++;
							}
							else{
								$txt_fin = $langStat4;
								$stat['egreso']['en_tiempo']++;
							}
						}
						else{
							$txt_fin = $langStat6;
							$stat['egreso']['sin_datos']++;
						}
						
						$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($this->getABC(0).$i, encode($rowViaje['vi_codigo']))
							->setCellValue($this->getABC(1).$i, encode($rowViaje['cl_razonSocial']))
							->setCellValue($this->getABC(2).$i, encode($rowViaje['re_nombre']))
							->setCellValue($this->getABC(3).$i, $txt_ini)
							->setCellValue($this->getABC(4).$i, $txt_fin);
							
							$objPHPExcel->alignLeft(array('A'.$i));
						$i++;
					}
					
					///---///
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $langStat1 );
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $langStat2);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', $langStat3);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', $langStat4);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', $langStat5);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B4', $langStat6);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', $stat['ingreso']['en_tiempo']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', $stat['egreso']['en_tiempo']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', $stat['ingreso']['atrasado']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', $stat['egreso']['atrasado']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C4', $stat['ingreso']['sin_datos']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D4', $stat['egreso']['sin_datos']);
					
					$objPHPExcel->alignCenter(array('C2','C3','C4','D2','D3','D4'));
											
					$objPHPExcel->setFormatoRows(array('B','C','D'),1);
					$objPHPExcel->setBackground(array('B','C','D'),2,'EBEBEB');
					$objPHPExcel->setBackground(array('B','C','D'),3,'EBEBEB');
					$objPHPExcel->setBackground(array('B','C','D'),4,'EBEBEB');
					///---/// 
					$dataseriesLabels = array(
						new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', NULL, 1), 
						new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$D$1', NULL, 1)
					);
	
					$xAxisTickValues = array(
						new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$2:$B$4', NULL, 3)
					);
					
					$dataSeriesValues = array(
						new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$4', NULL, 3),
						new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$D$2:$D$4', NULL, 3)
					);
					
					$objWorksheet = $objPHPExcel->getActiveSheet();  
	
					$series = new PHPExcel_Chart_DataSeries(
						PHPExcel_Chart_DataSeries::TYPE_BARCHART //TYPE_LINECHART //TYPE_PIECHART (torta) //TYPE_AREACHART
						, PHPExcel_Chart_DataSeries::GROUPING_STANDARD
						, range(0, count($dataSeriesValues)-1)
						, $dataseriesLabels, $xAxisTickValues, $dataSeriesValues);
				
					$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
					$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
					$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
					$title = new PHPExcel_Chart_Title($langGraff1);
					//$yAxisLabel = new PHPExcel_Chart_Title('Value');
					//$chart = new PHPExcel_Chart( 'chart1', $title, $legend, $plotarea, true, 0, null, $yAxisLabel);
					$chart = new PHPExcel_Chart( 'chart1', $title, $legend, $plotarea, true, 0, NULL, NULL);
					$chart->setTopLeftPosition('B6');
					$chart->setBottomRightPosition('E17');
					$objWorksheet->addChart($chart);
					///---/// 
					
					return true;
				}
			break;
			//-- Informe Detallado de Tareas Finalizadas con y sin Demora
			case 6:
			case 20:
				$strSQL = "	SELECT vi_id, re_id, vi_codigo, cl_razonSocial, re_nombre, vd_ini, vd_fin,  vd_ini_real, vd_fin_real ";
				$strSQL.= "	FROM tbl_viajes WITH(NOLOCK) ";
				$strSQL.= "	INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = vi_transportista ";
				$strSQL.= "	INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_vi_id = vi_id ";
				$strSQL.= "	INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
				$strSQL.= "	WHERE vi_dador = ".(int)$idCliente." AND vi_borrado = 0 AND vi_delivery = 0 ";
				$strSQL.= "	AND vd_ini_real IS NOT NULL AND vd_fin_real IS NOT NULL ";
				$strSQL.= "	AND vi_fechacreado >= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 7,106)+' 00:00') AND vi_fechacreado <= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 1,106)+' 23:59') ";
				$strSQL.= "	ORDER BY vi_codigo, vd_orden, cl_razonSocial ";
				
				$objRes = $this->objSQL->dbQuery($strSQL);	
				$arrRowsViajes = $this->objSQL->dbGetAllRows($objRes,3);	
				
				//--Traducciones--//
				switch($idInforme){
					case 20:
						$lang = 'en';
						$langCol1 = 'Task number';
						$langCol2 = 'Group';
						$langCol3 = 'Destination';
						$langCol4 = 'Arrival to destination';
						$langCol5 = 'Departure to destination';
						$langStat1 = 'Task status';
						$langStat2 = 'Arrivals to destinations';
						$langStat3 = 'Departures from destinations';
						$langStat4 = 'On Time';
						$langStat5 = 'Delayed ';
						$langGraff1 = 'Finished tasks on time and delayed';
					break;
					default:
						$lang = 'es';
						$langCol1 = 'Tarea N°';
						$langCol2 = 'Flota';
						$langCol3 = 'Waypoint';
						$langCol4 = 'Arribo en Destino';
						$langCol5 = 'Partida de Destino';
						$langStat1 = 'Estado de la Tarea';
						$langStat2 = 'Arribos a Waypoints';
						$langStat3 = 'Partidas de Waypoints';
						$langStat4 = 'En tiempo';
						$langStat5 = 'Atrasado';
						$langGraff1 = 'Informe Detallado de Tareas Finalizadas';
					break;	
				}
				//-- --//
				
				$i = 18;
				if($arrRowsViajes){
					///---///
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($this->getABC(0).$i, $langCol1)
						->setCellValue($this->getABC(1).$i, $langCol2)
						->setCellValue($this->getABC(2).$i, $langCol3)
						->setCellValue($this->getABC(3).$i, $langCol4)
						->setCellValue($this->getABC(4).$i, $langCol5);
							
					for($r=0; $r<=4; $r++){	
						$arralCol = array($this->getABC($r));
						$objPHPExcel->setFormatoRows($arralCol,$i);
					}
					
					//$alingCenterCol = array('D','E');
					//$objPHPExcel->alignCenter($alingCenterCol);
					///---///
					
					$stat['ingreso'] = $stat['egreso'] = array('en_tiempo' => 0, 'atrasado' => 0);
					$i++; 
					foreach($arrRowsViajes as $rowViaje){
						
						if($rowViaje['vd_ini_real']){
							if(strtotime($rowViaje['vd_ini_real']) > strtotime($rowViaje['vd_ini'])){
								$txt_ini = $langStat5;
								$stat['ingreso']['atrasado']++;
							}
							else{
								$txt_ini = $langStat4;
								$stat['ingreso']['en_tiempo']++;
							}
						}
											
						if($rowViaje['vd_fin_real']){
							if(strtotime($rowViaje['vd_fin_real']) > strtotime($rowViaje['vd_fin'])){
								$txt_fin = $langStat5;
								$stat['egreso']['atrasado']++;
							}
							else{
								$txt_fin = $langStat4;
								$stat['egreso']['en_tiempo']++;
							}
							
						}
												
						$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($this->getABC(0).$i, encode($rowViaje['vi_codigo']))
							->setCellValue($this->getABC(1).$i, encode($rowViaje['cl_razonSocial']))
							->setCellValue($this->getABC(2).$i, encode($rowViaje['re_nombre']))
							->setCellValue($this->getABC(3).$i, $txt_ini)
							->setCellValue($this->getABC(4).$i, $txt_fin);
							
							$objPHPExcel->alignLeft(array('A'.$i));
						$i++;
					}
					
					///---///
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $langStat1);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $langStat2);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', $langStat3);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', $langStat4);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', $langStat5);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', $stat['ingreso']['en_tiempo']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', $stat['egreso']['en_tiempo']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', $stat['ingreso']['atrasado']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', $stat['egreso']['atrasado']);
					
					$objPHPExcel->alignCenter(array('C2','C3','D2','D3'));
											
					$objPHPExcel->setFormatoRows(array('B','C','D'),1);
					$objPHPExcel->setBackground(array('B','C','D'),2,'EBEBEB');
					$objPHPExcel->setBackground(array('B','C','D'),3,'EBEBEB');
					///---/// 
					$dataseriesLabels = array(
						new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$2', NULL, 1), 
						new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$3', NULL, 1)
					);
	
					$xAxisTickValues = array(
						new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1:$D$1', NULL, 2)
					);
					
					$dataSeriesValues = array(
						new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$D$2', NULL, 2),
						new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$3:$D$3', NULL, 2)
					);
					
					$objWorksheet = $objPHPExcel->getActiveSheet();  
	
					$series = new PHPExcel_Chart_DataSeries(
						PHPExcel_Chart_DataSeries::TYPE_BARCHART //TYPE_LINECHART //TYPE_PIECHART (torta) //TYPE_AREACHART
						, PHPExcel_Chart_DataSeries::GROUPING_STANDARD
						, range(0, count($dataSeriesValues)-1)
						, $dataseriesLabels, $xAxisTickValues, $dataSeriesValues);
				
					$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
					$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
					$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
					$title = new PHPExcel_Chart_Title($langGraff1);
					//$yAxisLabel = new PHPExcel_Chart_Title('Value');
					//$chart = new PHPExcel_Chart( 'chart1', $title, $legend, $plotarea, true, 0, null, $yAxisLabel);
					$chart = new PHPExcel_Chart( 'chart1', $title, $legend, $plotarea, true, 0, NULL, NULL);
					$chart->setTopLeftPosition('B5');
					$chart->setBottomRightPosition('E16');
					$objWorksheet->addChart($chart);
					///---/// 
					
					return true;
				}
			break;
			//-- Informe de Estadía Promedio por Tipo de tarea
			case 7:
			case 21:
				$strSQL = "	SELECT vi_id, re_id, vi_codigo, cl_razonSocial, re_nombre, vd_ini, vd_fin,  vd_ini_real, vd_fin_real, vt_nombre ";
				$strSQL.= "	FROM tbl_viajes WITH(NOLOCK) ";
				$strSQL.= "	INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = vi_transportista ";
				$strSQL.= "	INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_vi_id = vi_id ";
				$strSQL.= "	INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
				$strSQL.= "	INNER JOIN tbl_viajes_tipo WITH(NOLOCK) ON vt_id = vi_vt_id ";
				$strSQL.= "	WHERE vi_dador = ".(int)$idCliente." AND vi_borrado = 0 AND vi_delivery = 0 ";
				$strSQL.= "	AND vd_ini_real IS NOT NULL AND vd_fin_real IS NOT NULL ";
				$strSQL.= "	AND vi_fechacreado >= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 7,106)+' 00:00') AND vi_fechacreado <= CONVERT(DATETIME,CONVERT(NVARCHAR, GETDATE() - 1,106)+' 23:59') ";
				$strSQL.= "	ORDER BY vi_codigo, vd_orden, cl_razonSocial ";
				
				$objRes = $this->objSQL->dbQuery($strSQL);	
				$arrRowsViajes = $this->objSQL->dbGetAllRows($objRes,3);	
				
				//--Traducciones--//
				switch($idInforme){
					case 21:
						$lang = 'en';
						$langCol1 = 'Task number';
						$langCol2 = 'Group';
						$langCol3 = 'Destination';
						$langCol4 = 'Task type';
						$langCol5 = 'Stay period (hours)';
						$langStat1 = 'Task status';
						$langStat2 = 'Average stay period';
						$langGraff1 = 'Average stay period per type task';
					break;
					default:
						$lang = 'es';
						$langCol1 = 'Tarea N°';
						$langCol2 = 'Flota';
						$langCol3 = 'Waypoint';
						$langCol4 = 'Tipo de Tarea';
						$langCol5 = 'Estadía Destino (hs)';
						$langStat1 = 'Estado de la Tarea';
						$langStat2 = 'Estadia Promedio';
						$langGraff1 = 'Estadia Promedio por Tipo de Tarea';
					break;	
				}
				//-- --//
				
				$i = 18;
				if($arrRowsViajes){
					///---///
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($this->getABC(0).$i, $langCol1)
						->setCellValue($this->getABC(1).$i, $langCol2)
						->setCellValue($this->getABC(2).$i, $langCol3)
						->setCellValue($this->getABC(3).$i, $langCol4)
						->setCellValue($this->getABC(4).$i, $langCol5);
							
					for($r=0; $r<=4; $r++){	
						$arralCol = array($this->getABC($r));
						$objPHPExcel->setFormatoRows($arralCol,$i);
					}
					
					//$alingCenterCol = array('D','E');
					//$objPHPExcel->alignCenter($alingCenterCol);
					///---///
					
					$stat = array();
					$i++; 
					foreach($arrRowsViajes as $rowViaje){
						
						$tipo_tarea = encode($rowViaje['vt_nombre']);
						$estadia = floor((strtotime($rowViaje['vd_fin_real']) - strtotime($rowViaje['vd_ini_real']))/3600);
						
						$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($this->getABC(0).$i, encode($rowViaje['vi_codigo']))
							->setCellValue($this->getABC(1).$i, encode($rowViaje['cl_razonSocial']))
							->setCellValue($this->getABC(2).$i, encode($rowViaje['re_nombre']))
							->setCellValue($this->getABC(3).$i, $tipo_tarea)
							->setCellValue($this->getABC(4).$i, $estadia);
							
							$objPHPExcel->alignLeft(array('A'.$i));
							$objPHPExcel->alignCenter(array('E'.$i));
						$i++;
						
						if(array_key_exists($tipo_tarea,$stat)){
							$stat[$tipo_tarea]['cant']++;
							$stat[$tipo_tarea]['total'] = $stat[$tipo_tarea]['total'] + $estadia;
						}
						else{
							$stat[$tipo_tarea]['cant'] = 0;	
							$stat[$tipo_tarea]['total'] = $estadia;	
						}
					}
					
					///---///
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $langStat1);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $langStat2);
					
					ksort($stat);
					$l = 2;
					foreach($stat as $k => $item){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$l, $k);	
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$l, (($item['total']>0)?($item['total']/$item['cant']):0));	
						
						$objPHPExcel->alignCenter(array('C'.$l));
						$objPHPExcel->setBackground(array('B','C'),$l,'EBEBEB');
						
						$l++;
					}
					
					$objPHPExcel->setFormatoRows(array('B','C'),1);
					
					///---/// 
					$dataseriesLabels = array(
						new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', NULL, 1)
					);
	
					$xAxisTickValues = array(
						new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$2:$B$'.$l, NULL, 1)
					);
					
					$dataSeriesValues = array(
						new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$'.$l, NULL, 1)
					);
					
					$objWorksheet = $objPHPExcel->getActiveSheet();  
	
					$series = new PHPExcel_Chart_DataSeries(
						PHPExcel_Chart_DataSeries::TYPE_LINECHART //TYPE_BARCHART //TYPE_PIECHART (torta) //TYPE_AREACHART
						, PHPExcel_Chart_DataSeries::GROUPING_STANDARD
						, range(0, count($dataSeriesValues)-1)
						, $dataseriesLabels, $xAxisTickValues, $dataSeriesValues);
				
					$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
					$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
					$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
					$title = new PHPExcel_Chart_Title($langGraff1);
					//$yAxisLabel = new PHPExcel_Chart_Title('Value');
					//$chart = new PHPExcel_Chart( 'chart1', $title, $legend, $plotarea, true, 0, null, $yAxisLabel);
					$chart = new PHPExcel_Chart( 'chart1', $title, $legend, $plotarea, true, 0, NULL, NULL);
					$chart->setTopLeftPosition('E2');
					$chart->setBottomRightPosition('M15');
					$objWorksheet->addChart($chart);
					///---/// 
					
					return true;
				}
			break;
		}
		return false;	
	}	
		
	function getNombreAdjunto($subject, $cliente){
		/*
		$texto =  substr(trim(strtolower($subject)),0,25).'GUIONMEDIO'.$cliente;
		$texto =  ereg_replace("[^A-Za-z0-9]", "", $texto); //preg_replace('([^A-Za-z0-9])', '', $texto);
		$texto =  str_replace('GUIONMEDIO','-',$texto);
		return $texto;
		*/
		$texto =  trim(strtolower($subject)).'SEPARADOR'.$cliente;
		$texto =  ereg_replace(' ','SEPARADOR',$texto);
		$texto =  ereg_replace("[^A-Za-z0-9]","",$texto); 
		$texto =  str_replace('SEPARADOR','_',$texto);
		return $texto;
	}
}
?>
