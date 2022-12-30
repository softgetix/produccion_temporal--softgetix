<?php
class ViajesDelivery extends Viajes {
	
	function __construct($objSQLServer, $id_viaje = 0) {
		parent::__construct($objSQLServer,'tbl_viajes','al');
		$this->id_viaje = (int)$id_viaje;
		$this->objSQL = $objSQLServer;
	}

	function importarTxt_KCCPERU($files){
		define('ERR_SIN_PERMISOS', 4);
		
		//-- Subida de Archivo --//
		$serverFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
		$rutaServer = $barra = '';
		foreach($serverFile as $item){
			if(strpos(strtolower($item),'.php') === false){
				$rutaServer.= $barra.$item;
				$barra = '/';
			}
		}
		$nameFile = getFechaServer('dmYHi').'-'.rand(1000,9999);
		$rutaFile =  PATH_ATTACH.'/viajes_delivery/'.$_SESSION['idEmpresa'].'/';
		
		if(!file_exists($rutaFile)){
			mkdir($rutaFile);				
		}
		foreach($files['name'] as $k => $file){
			$archivo = $rutaFile.$nameFile.'-'.$k.'.txt'; 				
			copy($files['tmp_name'][$k], $archivo);
			chown($rutaServer.'/'.$archivo, 'root');
		}
		$cantArchivos = count($files['name']);
		//-- --//
		
		$ix_1 = 'arc_01';
		$ix_2 = 'arc_02';
		$ix_3 = 'arc_03';
		
		$arc[$ix_1]['col'] = array(
			'Transporte' //$cod_viaje
			,'HorPlReg' //$vd_ini
			,'Fe.pl.reg.' //$vd_ini
			,'HPrInT' //$vd_fin
			,'InTranPlan' //$vd_fin
		);
		
		$arc[$ix_2]['col'] = array(
			'Transporte' //$cod_viaje
			,'ClTr' //$tipo_viaje
			,'PTrp' //$re_numboca (planta)
			,'AgServTran' //$transportista
			,'Nombre 1'// antes era 'Nombre agente servicios transp.'
			,'Texto adicional 3'
			,'Texto adicional 4'
			,'Id. 2 UMp' //$idMovil
			,'Conductor' //
			,'FinPlTrans' 
			,'HrPFTr' //$vdd_ini
			,'Entrega' //$arrDelivery['vdd_delivery']
			,'Cliente' //$re_numboca (delivery)
			,'Ctd.entr.'
			,'Volumen' //$arrDelivery['vdd_volumen']
			,'FePrefEnt.' //$arrDelivery['vdd_ini']
		);
	     
		$arc[$ix_3]['col'] = array(
			'Delivery # on Shipme' //$item_pedido[0]
			,'Pedido' //$item_pedido[1]
			,'Condición de Pago' //$item_pedido[2]
			,'Disponib. Material' //$item_pedido[3]
			//,'Cant.Conf.'
			,'Valor Neto' //$item_pedido[5]
		);		

		$arc[$ix_1]['datos'] = $arc[$ix_2]['datos'] = $arc[$ix_3]['datos'] = array();
		
		for($i=0; $i<$cantArchivos; $i++){
			$fp = fopen($rutaFile.$nameFile.'-'.$i.'.txt', 'r');
			$equals = NULL;
			while(!feof($fp)) {
				$linea = fgets($fp);
				$aux = explode('|', $linea);
				
				if(!$equals){
					foreach($arc[$ix_1]['col'] as $k => $item){
						if(!empty($item)){
							if($item == trim($aux[$k+1])){
								$equals = $ix_1;	
							}
							else{
								$equals = NULL;
								break;
							}
						}
					}
				}
				
				if(!$equals){
					foreach($arc[$ix_2]['col'] as $k => $item){
						if(!empty($item)){
							if($item == trim($aux[$k+1])){
								$equals = $ix_2;	
							}
							else{
								$equals = NULL;
								break;
							}
						}
					}
				}
				
				if(!$equals){	
					foreach($arc[$ix_3]['col'] as $k => $item){
						if(!empty($item)){
							if($item == trim($aux[$k+1])){
								$equals = $ix_3;	
							}
							else{
								$equals = NULL;
								break;
							}
						}
					}
				}
				
				if($equals){	
					unset($aux[0]);
					unset($aux[count($aux)]);
					$aux = array_merge($aux);
					if(count($aux) && $arc[$equals]['col'][0] != trim($aux[0])){
						array_push($arc[$equals]['datos'],$aux); 
					}
				}
			}
			fclose($fp);
			if(!$equals){
				for($i=0; $i<$cantArchivos; $i++){
					$fp = @unlink($rutaFile.$nameFile.'-'.$i.'.txt');
				}
				$return['msg'] = 'Las campos del documento '.($files['name'][$i-1]).' son incorrectos.';	
				return $return;
			}
		}
		
		$msg = NULL;
		$saltoline = '';
		$viajes_procesados = array('ok'=>0, 'error'=>0);
		
		foreach($arc[$ix_1]['datos'] as $arc1){
			$arrDestinosImpactados = array();
			$arrDestinosDeliveryImpactados = array();
			$vi = array();
			
			$vi['cod_viaje'] = escapear_string(trim($arc1[0]));
			
			$banderaViaje = true;
			$numboca_ant = NULL;
			$vd_orden = 0;
			
			$vd_ini = $this->getFormatFechaHora($arc1[2],$arc1[1]);
			if ($vd_ini == false){
				$msg.= $saltoline.'Error en la definicion de los campos ['.$arc[$ix_1]['col'][2].'] y/o ['.$arc[$ix_1]['col'][1].'] del Viaje ['.$vi['cod_viaje'].']';
				$saltoline = '<br />';
				$banderaViaje = false;
			}
			
			$vd_fin = $this->getFormatFechaHora($arc1[4],$arc1[3]);
			if ($vd_fin == false){
				$msg.= $saltoline.'Error en la definicion de los campos ['.$arc[$ix_1]['col'][4].'] y/o ['.$arc[$ix_1]['col'][3].'] del Viaje ['.$vi['cod_viaje'].']';
				$saltoline = '<br />';
				$banderaViaje = false;
			}
			
			if($banderaViaje){
				$breakDestinos = false;
				foreach($arc[$ix_2]['datos'] as $k2 => $arc2){
					
					if($vi['cod_viaje'] == trim($arc2[0])){
						if(!$breakDestinos){//-- Inicio .datos del viaje --//
							$arrTipoViaje = $this->getViajesTipo(escapear_string(trim($arc2[1])));
							$vi['tipo_viaje'] = !empty($arrTipoViaje[0]['vt_id'])?$arrTipoViaje[0]['vt_id']:NULL;
						
							$transportista = $this->getIdTransportista(escapear_string(trim($arc2[3])));
							$vi['transportista'] = !empty($transportista)?$transportista:NULL;
							$vi['txt1'] = escapear_string(trim($arc2[5]));
							$vi['txt1'] = $vi['txt1']?$vi['txt1']:NULL;
							$vi['txt2'] = escapear_string(trim($arc2[6]));
							$vi['txt2'] = $vi['txt2']?$vi['txt2']:NULL;
						
							$re_numboca = escapear_string(trim($arc2[2]));
							$idReferencia = $this->getReferenciaNumBoca($re_numboca);
							if(!$idReferencia){
								$msg.= $saltoline.'El C&oacute;digo de Cliente ['.$re_numboca.'] no se encuentra definida en la BD para el viaje ['.$vi['cod_viaje'].']';
								$saltoline = '<br />';
								$banderaViaje = false;
							}
							
							if(!$vi['tipo_viaje']){
								$msg.= $saltoline.'El tipo de viaje '.trim($arc2[1]).' no se encuentra definido en la BD para el viaje ['.$vi['cod_viaje'].']';	
								$saltoline = '<br />';
								$banderaViaje = false;
							}
							
							$arrMovil = $this->getMovilViaje(escapear_string(trim($arc2[7])));
							$vi['id_conductor'] = $this->getConductorViaje(escapear_string(trim($arc2[8])));
							$vi['id_movil'] = $arrMovil['mo_id'] ?$arrMovil['mo_id']:NULL;
							$vi['id_conductor'] = $vi['id_conductor']?$vi['id_conductor']:NULL;
							
							$idReferencia = $idReferencia?$idReferencia:6464; //Referencia sin definir: [Sin Definir]
							$vi['destinos'] = array();
							$vi['destinos'][$vd_orden] = array('re_id'=>$idReferencia, 'vd_ini'=>$vd_ini, 'vd_fin'=>$vd_fin);
						}//-- Fin .datos del viaje --//
						
						//-- Inicio .datos de los destinos del viaje --//
							$re_numboca = escapear_string(trim($arc2[12]));
							if($re_numboca != $numboca_ant){
								$numboca_ant = $re_numboca;
								$vd_orden++;
								$idReferencia = $this->getReferenciaNumBoca($re_numboca);
								$idReferencia = $idReferencia?$idReferencia:6464; //Referencia sin definir: [Sin Definir]
								$vi['destinos'][$vd_orden] = array('re_id'=>$idReferencia, 'vd_ini'=>NULL, 'vd_fin'=>NULL, 'delivery' => array());
							}
							
							$cod_delivery = escapear_string(trim($arc2[11]));
							$vdd_ini = $this->getFormatFechaHora($arc2[15],$arc2[10]);
							if ($vdd_ini == false){
								$msg.= $saltoline.'Error en la definicion de los campos ['.$arc[$ix_2]['col'][15].'] y/o ['.$arc[$ix_2]['col'][10].'] para el Delivery ['.$cod_delivery.']';
								$saltoline = '<br />';
								$banderaViaje = false;
							}
							else{
								$vdd_fin = date('Y-m-d H:i:s',strtotime('+2 hours',strtotime($vdd_ini)));
								$idMovil = $vi['id_movil'];
								$idConductor = $vi['id_conductor'];
								$idTransportista = $vi['transportista'];
								$volumen = escapear_string(trim($arc2[14]));
								
								$arrDelivery = array('delivery'=>$cod_delivery, 'vdd_ini'=>$vdd_ini, 'vdd_fin'=>$vdd_fin, 'id_movil'=>$idMovil, 'id_conductor'=>$idConductor, 'volumen'=>$volumen, 'id_trans'=>$idTransportista);		
								array_push($vi['destinos'][$vd_orden]['delivery'],$arrDelivery);
							}
						//-- Fin .datos de los destinos del viaje --//
						
						$breakDestinos = true;
						unset($arc[$ix_2]['datos'][$k2]);	
					}
					elseif($breakDestinos){
						break; //Para no seguir buscando el mismo viaje ya q los encontro (si no esta ordenado x codigo de viaje no funciona)
					}
				}//fin.foreach. $arc[$ix_2]['datos']
				
				if(!$breakDestinos){
					$msg.= $saltoline.'El Viaje ['.$vi['cod_viaje'].'] no contiene destinos.';
					$saltoline = '<br />';
					$banderaViaje = false;
				}
			}
			
			##-- INICIO PROCESAMIENTO DE VIAJES --##
			if($banderaViaje){
				//-- Procesamos en tbl_viajes --//
				$campos = array();
				$valorCampos = array();
				array_push($campos,'vi_codigo');
				array_push($valorCampos,"'".$vi['cod_viaje']."'");
				array_push($campos,'vi_us_id');
				array_push($valorCampos,$_SESSION['idUsuario']);
				array_push($campos,'vi_sensibilidad');
				array_push($valorCampos,0);
				array_push($campos,'vi_dador');
				array_push($valorCampos,$_SESSION['idEmpresa']);
				array_push($campos,'vi_vt_id');
				array_push($valorCampos,$vi['tipo_viaje']);
				array_push($campos,'vi_observaciones');
				array_push($valorCampos,"'".$vi['txt1']." / ".$vi['txt2']."'");
				array_push($campos,'vi_transportista');
				array_push($valorCampos,$vi['transportista']);
				array_push($campos,'vi_mo_id');
				array_push($valorCampos,$vi['id_movil']);
				array_push($campos,'vi_co_id');
				array_push($valorCampos,$vi['id_conductor']);
				array_push($campos,'vi_delivery');
				array_push($valorCampos,1);
					
				$this->id_viaje = NULL;
				$sqlAux = " SELECT vi_id ";
				$sqlAux.= " FROM tbl_viajes WITH(NOLOCK) ";
				$sqlAux.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_id = vi_us_id ";
				$sqlAux.= " WHERE vi_borrado = 0 AND vi_codigo = '".$vi['cod_viaje']."' AND us_cl_id = ".$_SESSION["idEmpresa"];
				$res = $this->objSQL->dbQuery($sqlAux);
				$existe = $this->objSQL->dbGetAllRows($res);
				if($existe[0]['vi_id']){ //si esta repetido actualizo
					$this->id_viaje = $existe[0]['vi_id'];
					$this->updateViajes($campos,$valorCampos);
				}
				else{//no estaba repetido, tengo que insertarlo
					$this->id_viaje = $this->setViajes($campos,$valorCampos);
				}
				//-- fin. tbl_viajes --//
					
				//-- Procesamos en tbl_viajes_destinos --//
				if($this->id_viaje){
					foreach($vi['destinos'] as $vd_i => $vd){
						$sql = " SELECT vd_id FROM tbl_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = ".$this->id_viaje." AND vd_orden = ".(int)$vd_i;
						$res = $this->objSQL->dbQuery($sql);				
						$arrOrigen = $this->objSQL->dbGetRow($res,0,3);
						
						$arrDestino = array();	 
						$arrDestino[0]['vd_re_id'] = $vd['re_id'];
						$arrDestino[0]['vd_orden'] = $vd_i;
						$arrDestino[0]['vd_ini'] = $vd['vd_ini']?date('Y-m-d H:i:s',strtotime(str_replace('/','-',$vd['vd_ini']))):NULL;
						$arrDestino[0]['vd_fin'] = $vd['vd_fin']?date('Y-m-d H:i:s',strtotime(str_replace('/','-',$vd['vd_fin']))):NULL;
						if((int)$arrOrigen['vd_id']){					
							$vd_id = $arrDestino[0]['vd_id'] = $arrOrigen['vd_id'];
							$this->insertDestino = false;
							$this->setViajesDestinos($arrDestino);
						}
						else{
							$this->insertDestino = true;
							$vd_id = $this->setViajesDestinos($arrDestino);
						}
						
						if((int)$vd_id){
							array_push($arrDestinosImpactados, $vd_id);
							
							//-- Procesamos los delivery en tbl_viajes_destinos_delivery --//
							if($vd['delivery']){
								foreach($vd['delivery'] as $vdd){
									$arrDelivery = array();
									$arrDelivery['vdd_vd_id'] = (int)$vd_id;
									$arrDelivery['vdd_delivery'] = $vdd['delivery'];
									$arrDelivery['vdd_ini'] = $vdd['vdd_ini'];
									$arrDelivery['vdd_fin'] = $vdd['vdd_fin'];
									$arrDelivery['vdd_nro_factura'] = NULL;
									$arrDelivery['vdd_volumen'] = $vdd['volumen'];
									$arrDelivery['vdd_co_id'] = $vdd['id_conductor']?$vdd['id_conductor']:'NULL';
									$arrDelivery['vdd_mo_id'] = $vdd['id_movil']?$vdd['id_movil']:'NULL';
									$arrDelivery['vdd_cl_id'] = $vdd['id_trans']?$vdd['id_trans']:'NULL';
									$id_delivery = $this->setViajesDelvery($arrDelivery);	
									if((int)$id_delivery){
										array_push($arrDestinosDeliveryImpactados, $id_delivery);
										
										$this->borrarViajesDelveryPedidos($id_delivery);
										foreach($arc[$ix_3]['datos'] as $vddp_i => $vddp){
											$vddp[0] = trim($vddp[0]);
											if($vddp[0]){
												if($arrDelivery['vdd_delivery'] == $vddp[0]){
													$arrPedido = array();
													$arrPedido['vddp_vdd_id'] = (int)$id_delivery;
													$arrPedido['vddp_pedido'] = trim($vddp[1]); 
													$arrPedido['vddp_condicion_pago'] = $vddp[2]; 
													$arrPedido['vddp_fecha'] = $vddp[3];
													//$arrPedido['vddp_cant_conf'] = $vddp[4];
													$arrPedido['vddp_valor_neto'] = $vddp[4];
													$id_pedido = $this->setViajesDelveryPedidos($arrPedido);	
													unset($arc[$ix_3]['datos'][$vddp_i]);
												}
											}
										}//fin.foreach. $arc[$ix_3]['datos']
									}
									//-- fin. tbl_viajes_destinos_delivery_pedidos --//
								}
							}//-- fin. tbl_viajes_destinos_delivery --//	
						}
					}
					$viajes_procesados['ok']++;
				}
				else{
					$viajes_procesados['error']++;	
				}
				//-- fin. tbl_viajes_destinos --//
			}//fin.if $banderaViaje
			else{
				$viajes_procesados['error']++;
			}
			##-- FIN PROCESAMIENTO DE VIAJES --##
			
			//-- Limpio destinos y deliverys q fueron dados de baja mediante la planilla --//
			$stringDelivery = implode(',',$arrDestinosDeliveryImpactados);
			if(!empty($stringDelivery) && $this->id_viaje){
				$sql = " SELECT DISTINCT(vdd_id) as vdd_id, vdd_vd_id FROM tbl_viajes_destinos_delivery WITH(NOLOCK) ";
				$sql.= " INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_id = vdd_vd_id ";
				$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vdd_id NOT IN (".$stringDelivery.") ";
				$res = $this->objSQL->dbQuery($sql);				
				$arrDeliveryBaja = $this->objSQL->dbGetAllRows($res,3);
				
				foreach($arrDeliveryBaja as $deliveryBaja){
					$sql = " DELETE FROM tbl_viajes_destinos_delivery_pedidos WHERE vddp_vdd_id = ".(int)$deliveryBaja['vdd_id'];	
					$this->objSQL->dbQuery($sql);
					
					$sql = " DELETE FROM tbl_viajes_destinos_delivery WHERE vdd_id = ".(int)$deliveryBaja['vdd_id'];	
					$this->objSQL->dbQuery($sql);
					
					$sql = " SELECT COUNT(*) AS cant FROM tbl_viajes_destinos_delivery WITH(NOLOCK) WHERE vdd_vd_id = ".(int)$deliveryBaja['vdd_vd_id'];
					$res = $this->objSQL->dbQuery($sql);	
					$totalDeliveryMismoDestino = $this->objSQL->dbGetRow($res,0, 3);
					if($totalDeliveryMismoDestino['cant'] == 0){
						$sql = " DELETE FROM tbl_viajes_destinos WHERE vd_id = ".(int)$deliveryBaja['vdd_vd_id']; 
						$this->objSQL->dbQuery($sql);
					}
				}
			}
			
			$stringDestinos = implode(',',$arrDestinosImpactados);
			if($stringDestinos){
				$sql = " DELETE FROM tbl_viajes_destinos WHERE vd_vi_id = ".$this->id_viaje." AND vd_id NOT IN (".$stringDestinos.") ";
				$res = $this->objSQL->dbQuery($sql);				
			}
			//--fin. limpieza --//
			
		} //fin.foreach. $arc[$ix_1]['datos']
		
		$msg = str_replace('<br>',"\r\n",$msg);
		$msg = str_replace('<br />',"\r\n",$msg);
		if($viajes_procesados['ok']){
			$mensaje = '<p>Se procesaron correctamente '.$viajes_procesados['ok'].' viajes.</p>';		
		}
		if($viajes_procesados['error']){
			$mensaje.= '<p>Se detectaron problemas en '.$viajes_procesados['error'].' viajes que no pudieron ser procesados.<br>';		
			$mensaje.= 'Click <a href="javascript:enviar(\'export_errorLog\');">agui</a> para descargar log de errores.</p>';
		}
		
		if(!empty($msg)){
			$archivo = $rutaFile.$nameFile.'-error_log.txt'; 				
			$log = fopen($archivo,'w');
			fwrite($log,$msg);
			fclose($log);
			chown($rutaServer.'/'.$archivo, 'root');	
		}
		$return['msg'] = $mensaje;
		$return['errorLog'] = $archivo;
		return $return;
	}
	
	
	function importarExcel_TASA($files){
		ini_set('memory_limit', '3072M');
		error_reporting(E_ERROR);
		define('ERR_SIN_PERMISOS', 4);
		
		/**/
		//-- Subida de Archivo --//
		$serverFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
		$this->rutaServer = $barra = '';
		foreach($serverFile as $item){
			if(strpos(strtolower($item),'.php') === false){
				$this->rutaServer.= $barra.$item;
				$barra = '/';
			}
		}
		$this->nameFile = getFechaServer('dmYHi').'-'.rand(1000,9999);
		$this->rutaFile = PATH_ATTACH.'/viajes_delivery/'.$_SESSION['idEmpresa'].'/';
		
		if(!file_exists($this->rutaFile)){
			mkdir($this->rutaFile);				
		}
		//$archivo = $rutaFile.$nameFile.'-'.$files['name']; 				
		
		$this->extensFile = explode('.',$files['name']);
		$this->extensFile = end($this->extensFile);
		$archivo = $this->rutaFile.$this->nameFile.'.'.$this->extensFile;   
		
		copy($files['tmp_name'], $archivo);
		chown($this->rutaServer.'/'.$archivo, 'root');
		
		$this->errorLogImportacion('ATENCION!! Los viajes en cuestión no fueron ser procesados, en caso que el error persista comuníquese con Localizar-T.');
		//-- --//
		/**/
		
		require_once('clases/PHPExcel/IOFactory.php');
		$objExcel = PHPExcel_IOFactory::load($files['tmp_name']);
		/*try{
			$objHoja[0] = $objExcel->getSheet(0)->toArray(NULL,true,false,true);
		}
		catch(Exception $e){
			echo 'La Hoja 1, de la planilla de excel que intenta importar genera un error. Verifique que la misma no contenga columnas calculadas.';
		}
		/**/
		
		//-- Se implementa esto porque el archivo contiene columnas calculadas --//
		$celdas = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V');
		$objHojaExcel = $objExcel->getSheet(0);
		$iRow = 0;
		foreach ($objHojaExcel->getRowIterator() as $row){
			
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
			$iCell = 0;
			$objHoja[0][$iRow] = array();
			foreach ($cellIterator as $cell){
				
				if(!is_null($cell)){
                	$value = $cell->getValue();
					if(strstr($value,'=')==true){
						$value = $cell->getOldCalculatedValue();
					}
					elseif(PHPExcel_Shared_Date::isDateTime($cell)){
                		$value = $cell->getFormattedValue();
					}
				}
			
				$objHoja[0][$iRow][$celdas[$iCell]] = $value;
				$iCell++;
			}
			$iRow++;
		}
		//-- --//
		
		$msg = NULL;
		$cols = $objHoja[0][0];
		unset($objHoja[0][0]);
		$saltoline = '';
		
		$arrViajes = array();
		$viCodigo_ant = NULL;
		$vi_orden = -1;
		foreach($objHoja[0] as $k => $hoja1){
			
			$banderaViaje = true;
			$cod_viaje = escapear_string(trim($hoja1['R']));
			if(empty($cod_viaje)){
				$msg.= $saltoline.'Viaje sin identificación [R'.($k+1).']';
				$saltoline = '<br />';
				$banderaViaje = false;
			}
			
			if($banderaViaje){
				if($viCodigo_ant != $cod_viaje){
					$vi_orden++; 
					//-- Inicio .datos del viaje --//
					$vd_orden = 0;
					$numboca_ant = $viDelivery_ant = NULL;
					$vi = array();
					$vi['cod_viaje'] = $viCodigo_ant = $cod_viaje;
					$vd_ini = $this->getFormatFechaHora($hoja1['A'],$hoja1['N']);
					if ($vd_ini == false){
						$msg.= $saltoline.'Error en la definicion de los campos ['.$cols['A'].'] y/o ['.$cols['N'].'] del Viaje ['.$vi['cod_viaje'].']';
						$saltoline = '<br />';
						$banderaViaje = false;
					}
					
					$vd_fin = date('Y-m-d H:i',strtotime('+2 hours',strtotime($vd_ini)));
					
					$tipo = !empty($hoja1['C'])?escapear_string(trim($hoja1['C'])):'Sin definir';
					$arrTipoViaje = $this->getViajesTipo($tipo);
					$vi['tipo_viaje'] = !empty($arrTipoViaje[0]['vt_id'])?$arrTipoViaje[0]['vt_id']:NULL;
					
					$transportista = $this->getIdTransportista(NULL, escapear_string(trim($hoja1['K'])));
					$vi['transportista'] = !empty($transportista)?$transportista:5758;
									
					$vi['observaciones'] = !empty($hoja1['S'])?escapear_string(trim($hoja1['S'])):NULL;	
					
					$vi['id_conductor'] = $this->getConductorViaje(escapear_string(trim($hoja1['M'])));
					$vi['id_conductor'] = $vi['id_conductor']?$vi['id_conductor']:NULL;
					
					$vi['id_movil'] = NULL;
					if($vi['id_conductor']){
						$arrMovil = $this->obtenerMovilesRecomendados(NULL,NULL,$vi['id_conductor']);
						$vi['id_movil'] = $arrMovil[0]['id']?$arrMovil[0]['id']:NULL;
					}
					
					$idReferencia = 33181; //Referencia de Origen
					$vi['destinos'] = array();
					$vi['destinos'][$vd_orden] = array('re_id'=>$idReferencia, 'vd_ini'=>$vd_ini, 'vd_fin'=>$vd_fin);
					//-- Fin .datos del viaje --//
				}
				
				//-- Inicio .datos de los destinos del viaje --//
				$re_numboca = escapear_string(trim($hoja1['V']));
				if($re_numboca != $numboca_ant){
					$vdd_orden = -1;
					$numboca_ant = $re_numboca;
					$vd_orden++;
					$idRefAnt = $idReferencia;
					$idReferencia = $this->getReferenciaNumBoca($re_numboca);
					$idReferencia = $idReferencia?$idReferencia:6464; //Referencia sin definir: [Sin Definir]
					$vi['destinos'][$vd_orden] = array('re_id'=>$idReferencia, 'vd_ini'=>NULL, 'vd_fin'=>NULL, 'delivery' => array());
					
					##-- CACLULAR DISTANCIA ENTRE Referencia Anterior y la Actual,
					##-- de la distancia obtenid, tederminar tiempo de recorrido para definir la hora programada de inicio en Delivery.
						$vdd_ini = $vdd_fin = NULL;
						if($idRefAnt != 6464 && $idReferencia != 6464){
							$trayectoEstimado = $this->getTrayectoEstimadoEntreDosReferencias($idRefAnt, $idReferencia);
							$vdd_ini = date('Y-m-d H:i:s',strtotime('+ '.$trayectoEstimado.' second', strtotime($vd_fin)));
							$vdd_fin = date('Y-m-d H:i:s',strtotime('+2 hours',strtotime($vdd_ini)));
						}
					##-- --##	
				}
								
				$cod_delivery = escapear_string(trim($hoja1['P']));
				if($viDelivery_ant != $cod_delivery){
					$vdd_orden++;
					$idMovil = $vi['id_movil'];
					$idConductor = $vi['id_conductor'];
					$idTransportista = $vi['transportista'];
					//$volumen = escapear_string(trim($arc2[14]));
										
					$arrDelivery = array('delivery'=>$cod_delivery, 'vdd_ini'=>$vdd_ini, 'vdd_fin'=>$vdd_fin, 'id_movil'=>$idMovil, 'id_conductor'=>$idConductor, /*'volumen'=>$volumen,*/ 'id_trans'=>$idTransportista);		
					$vi['destinos'][$vd_orden]['delivery'][$vdd_orden] = $arrDelivery;
				}
				
				//-- Inicio .datos de los pedidos del delivery --//
				$arrPedido = array('pedido' => escapear_string(trim($hoja1['O']))/*, 'condicion_pago' => NULL, 'fecha' => NULL, 'cant_conf' => NULL, 'valor_neto' => NULL*/);
				$vi['destinos'][$vd_orden]['delivery'][$vdd_orden]['pedido'][] = $arrPedido;
				//-- Fin .datos de los pedidos del delivery --//
				
				//-- Fin .datos de los destinos del viaje --//
				$vi['process'] = $banderaViaje?'ok':'error';
				$arrViajes[$vi_orden] = $vi;
			}
		}

		$return = $this->setProcesarViajes($arrViajes, $msg, true);
		return $return;
	}
	
	function importarExcel_ARAUCO($files){
		ini_set('memory_limit', '3072M');
		set_time_limit(7200);
		define('ERR_SIN_PERMISOS', 4);
		
		//-- Subida de Archivo --//
		$serverFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
		$this->rutaServer = $barra = '';
		foreach($serverFile as $item){
			if(strpos(strtolower($item),'.php') === false){
				$this->rutaServer.= $barra.$item;
				$barra = '/';
			}
		}
		$this->rutaFile = PATH_ATTACH.'/viajes_delivery/'.$_SESSION['idEmpresa'].'/';
		
		if(!file_exists($this->rutaFile)){
			mkdir($this->rutaFile);				
		}
		
		$this->extensFile = extension_archivo($files['name']);
		$aux = explode('.',$files['name']);
		$this->nameFile = getFechaServer('dmYHi').'-'.str_replace(' ','',trim(strtolower($aux[0])));
		//$archivo = $rutaFile.$nameFile.'.'.$extens; 
						
		copy($files['tmp_name'], $this->rutaFile.$this->nameFile.'.'.$this->extensFile);
		chown($this->rutaServer.'/'.$this->rutaFile.$this->nameFile.'.'.$this->extensFile, 'root');
		$this->errorLogImportacion('ATENCION!! Los viajes en cuestión no fueron ser procesados, en caso que el error persista comuníquese con Localizar-T.');
		
		//-- --// 
		require_once('clases/PHPExcel/IOFactory.php');
		$objExcel = PHPExcel_IOFactory::load($files['tmp_name']);
		/*
		$objPHPExcel = new PHPExcel();
		$objReader = PHPExcel_IOFactory::createReader($extens=='xlsx'?'Excel2007':'Excel5');
		$objReader->setReadDataOnly(true);
		$objExcel = $objReader->load($files['tmp_name']);
		//-- --//
		try{
			$objHoja[0] = $objExcel->getSheet(0)->toArray(NULL,true,false,true);
		}
		catch(Exception $e){
			return 'La Hoja 1, de la planilla de excel que intenta importar genera un error. Verifique que la misma no contenga columnas calculadas.';
		}*/
		
		//-- Se implementa esto porque el archivo contiene columnas calculadas --//
		
		$celdas = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V');
		$objHojaExcel = $objExcel->getSheet(0);
		
		$iRow = 0;
		foreach ($objHojaExcel->getRowIterator() as $row){
			
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(true); // This loops all cells,
			
			$iCell = 0;
			$objHoja[0][$iRow] = array();
			$banderaViaje = true;
			foreach ($cellIterator as $cell){
				if(!is_null($cell)){

					$value = $cell->getValue();
					if($iRow > 0){
						switch($iCell){
							case 10:
								if($value != 'CPT'/* || $value != "") && $objHoja[0][$iRow][$celdas[2]] != ""*/){//-- Solo se procesan viajes transportados por Arauco --//	
									$banderaViaje = false;
									break;
								}
							break;
							case 13:
							case 14:
								if((int)$value){
									$value = date('d-m-Y',strtotime('+1 day',PHPExcel_Shared_Date::ExcelToPHP($value)));	
								}
								else{
									//$value = $cell->getCalculatedValue();
									$value = $cell->getOldCalculatedValue();
									//$value = $cell->getFormattedValue();
									if((int)$value){
										$value = date('d-m-Y',strtotime('+1 day',PHPExcel_Shared_Date::ExcelToPHP($value)));	
									}
									else{
										$value = NULL;
									}
								}
							break;	
						}
					}
				}
				$objHoja[0][$iRow][$celdas[$iCell]] = $value;
				$iCell++;
			}
			
			if(!$banderaViaje){
				unset($objHoja[0][$iRow][$celdas[$iCell]]);
			}
			else{
				$iRow++;
			}
		}
		//-- --//
		
		$msg = NULL;
		$cols = $objHoja[0][0];
		unset($objHoja[0][0]);
		$saltoline = '';
		
		$auxControlViajes = array();
		$arrViajes = array();
		$vi_orden = -1;
		foreach($objHoja[0] as $k => $hoja1){
			
			$banderaViaje = true;
			$cod_viaje = escapear_string(trim($hoja1['B']));
			
			if(empty($cod_viaje)){
				$msg.= $saltoline.'Viaje sin identificación [Entrega: '.trim($hoja1['A']).']';
				$saltoline = '<br />';
				$banderaViaje = false;
			}
			
			if($banderaViaje){
				
				if(in_array($cod_viaje,$auxControlViajes)){
					$vi_orden_existente = array_search($cod_viaje,$auxControlViajes);
					$vi = $arrViajes[$vi_orden_existente];
					$vd_orden = max(array_keys($vi['destinos']));
					$numboca_ant = $vi['destinos'][$vd_orden]['re_numboca'];
					$vd_fin = $vi['destinos'][0]['vd_fin'];
					$idReferencia = $vi['destinos'][0]['re_id'];
				}
				else{
					$vi_orden_existente = NULL;
					$vi_orden++; 
					//-- Inicio .datos del viaje --//
					$vd_orden = 0;
					$numboca_ant = $viDelivery_ant = NULL;
					$vi = array();
					$vi['cod_viaje'] = $cod_viaje;
					
					$vd_ini = $this->getFormatFechaHora($hoja1['N'],'00:00');
					if ($vd_ini == false){
						$msg.= $saltoline.'Error en la definicion del campo [N] de la Entrega ['.$hoja1['A'].']';
						$saltoline = '<br />';
						$banderaViaje = false;
					}
					
					$vd_fin = $this->getFormatFechaHora($hoja1['O'],'00:00');
					if ($vd_fin == false){
						$msg.= $saltoline.'Error en la definicion del campo [O] de la Entrega ['.$hoja1['A'].']';
						$saltoline = '<br />';
						$banderaViaje = false;
					}
					
					//$tipo = 'Sin definir';
					//$arrTipoViaje = $this->getViajesTipo($tipo);
					//$vi['tipo_viaje'] = !empty($arrTipoViaje[0]['vt_id'])?$arrTipoViaje[0]['vt_id']:NULL;
					$vi['tipo_viaje'] = 13; //Sin Definir
					
					$arrMovil = $this->getMovilViaje(escapear_string(trim($hoja1['H'])));
					$vi['id_movil'] = $arrMovil['mo_id'] ?$arrMovil['mo_id']:NULL;
					
					//$transportista = $this->getIdTransportista(NULL, escapear_string(trim($hoja1['F'])));
					//$vi['transportista'] = !empty($transportista)?$transportista:8226;
					$vi['transportista'] = $arrMovil['mo_id_cliente_facturar']?$arrMovil['mo_id_cliente_facturar']:8226;
									
					//$vi['observaciones'] = !empty($hoja1['S'])?escapear_string(trim($hoja1['S'])):NULL;	
					
					//$vi['id_conductor'] = $this->getConductorViaje(escapear_string(trim($hoja1['G'])));
					//$vi['id_conductor'] = $vi['id_conductor']?$vi['id_conductor']:NULL;
					$vi['id_conductor'] = NULL;
					
					$idReferencia = $this->getReferenciaNumBoca(escapear_string(trim($hoja1['M']))); //Referencia de Origen
					$vi['destinos'] = array();
					$vi['destinos'][$vd_orden] = array('re_id'=>$idReferencia, 'vd_ini'=>$vd_ini, 'vd_fin'=>$vd_fin);
					//-- Fin .datos del viaje --//
				
				}
				
				//-- Inicio .datos de los destinos del viaje --//
				$re_numboca = escapear_string(trim($hoja1['C']));
				if($re_numboca != $numboca_ant){
					$vdd_orden = -1;
					$numboca_ant = $re_numboca;
					$vd_orden++;
					$idRefAnt = $idReferencia;
					$idReferencia = $this->getReferenciaNumBoca($re_numboca);
					$idReferencia = $idReferencia?$idReferencia:6464; //Referencia sin definir: [Sin Definir]
					$vi['destinos'][$vd_orden] = array('re_id'=>$idReferencia, 're_numboca'=>$re_numboca, 'vd_ini'=>NULL, 'vd_fin'=>NULL, 'delivery' => array());
					
					##-- CACLULAR DISTANCIA ENTRE Referencia Anterior y la Actual,
					##-- de la distancia obtenida, tederminar tiempo de recorrido para definir la hora programada de inicio en Delivery.
						$vdd_ini = $vdd_fin = NULL;
						if($idRefAnt != 6464 && $idReferencia != 6464){
							$trayectoEstimado = $this->getTrayectoEstimadoEntreDosReferencias($idRefAnt, $idReferencia);
							$vdd_ini = date('Y-m-d H:i:s',strtotime('+ '.$trayectoEstimado.' second', strtotime($vd_fin)));
							$vdd_fin = date('Y-m-d H:i:s',strtotime('+2 hours',strtotime($vdd_ini)));
						}
					##-- --##	
				}
								
				$cod_delivery = escapear_string(trim($hoja1['A']));
				if($viDelivery_ant != $cod_delivery){
					$vdd_orden++;
					$idMovil = $vi['id_movil'];
					$idConductor = $vi['id_conductor'];
					$idTransportista = $vi['transportista'];
										
					$arrDelivery = array('delivery'=>$cod_delivery, 'vdd_ini'=>$vdd_ini, 'vdd_fin'=>$vdd_fin, 'id_movil'=>$idMovil, 'id_conductor'=>$idConductor, 'id_trans'=>$idTransportista);		
					$vi['destinos'][$vd_orden]['delivery'][$vdd_orden] = $arrDelivery;
				}
				
				//-- Fin .datos de los destinos del viaje --//
				$vi['process'] = $banderaViaje?'ok':'error';
			}
			else{
				$vi['process'] = 'error';
			}
			$arrViajes[empty($vi_orden_existente)?$vi_orden:$vi_orden_existente] = $vi;
			$auxControlViajes[empty($vi_orden_existente)?$vi_orden:$vi_orden_existente] = $vi['cod_viaje'];
		}
		unset($objHoja);
		unset($auxControlViajes);
		$return = $this->setProcesarViajes($arrViajes, $msg, false);
		return $return;
	}
	/****/
	
	function setProcesarViajes($arrViajes, $msg = NULL, $banPedidos = false){
		
		##-- FIN PROCESAMIENTO DE VIAJES --##
		$mensaje = NULL;
		$viajes_procesados = array('ok' => 0, 'error' => 0);
		foreach($arrViajes as $vi){
			
			$arrDestinosImpactados = array();
			$arrDestinosDeliveryImpactados = array();
			$this->id_viaje = NULL;
			
			if($vi['process'] == 'ok'){
				//-- Procesamos en tbl_viajes --//
				$campos = array();
				$valorCampos = array();
				array_push($campos,'vi_codigo');
				array_push($valorCampos,"'".$vi['cod_viaje']."'");
				array_push($campos,'vi_us_id');
				array_push($valorCampos,$_SESSION['idUsuario']);
				array_push($campos,'vi_sensibilidad');
				array_push($valorCampos,'0');
				array_push($campos,'vi_dador');
				array_push($valorCampos,$_SESSION['idEmpresa']);
				array_push($campos,'vi_vt_id');
				array_push($valorCampos,$vi['tipo_viaje']);
				array_push($campos,'vi_observaciones');
				array_push($valorCampos,!empty($vi['observaciones'])?"'".$vi['observaciones']."'":'NULL');
				array_push($campos,'vi_transportista');
				array_push($valorCampos,$vi['transportista']);
				array_push($campos,'vi_mo_id');
				array_push($valorCampos,$vi['id_movil']);
				array_push($campos,'vi_co_id');
				array_push($valorCampos,$vi['id_conductor']);
				array_push($campos,'vi_delivery');
				array_push($valorCampos,1);
				
				$sqlAux = " SELECT vi_id ";
				$sqlAux.= " FROM tbl_viajes WITH(NOLOCK)";
				$sqlAux.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_id = vi_us_id ";
				$sqlAux.= " WHERE vi_borrado = 0 AND vi_codigo = '".$vi['cod_viaje']."' AND us_cl_id = ".$_SESSION['idEmpresa'];
				$res = $this->objSQL->dbQuery($sqlAux);
				$existe = $this->objSQL->dbGetAllRows($res);
				if($existe[0]['vi_id']){ //si esta repetido actualizo
					$this->id_viaje = $existe[0]['vi_id'];
					$this->updateViajes($campos,$valorCampos);
				}
				else{//no estaba repetido, tengo que insertarlo
					$this->id_viaje = $this->setViajes($campos,$valorCampos);
				}
				//-- fin. tbl_viajes --//
					
				//-- Procesamos en tbl_viajes_destinos --//
				if($this->id_viaje){
					foreach($vi['destinos'] as $vd_i => $vd){
						$sql = " SELECT vd_id FROM tbl_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = ".$this->id_viaje." AND vd_orden = ".(int)$vd_i;
						$res = $this->objSQL->dbQuery($sql);				
						$arrOrigen = $this->objSQL->dbGetRow($res,0,3);
						
						$arrDestino = array();	 
						$arrDestino[0]['vd_re_id'] = $vd['re_id'];
						$arrDestino[0]['vd_orden'] = $vd_i;
						$arrDestino[0]['vd_ini'] = $vd['vd_ini']?date('Y-m-d H:i:s',strtotime(str_replace('/','-',$vd['vd_ini']))):NULL;
						$arrDestino[0]['vd_fin'] = $vd['vd_fin']?date('Y-m-d H:i:s',strtotime(str_replace('/','-',$vd['vd_fin']))):NULL;
						if((int)$arrOrigen['vd_id']){					
							$vd_id = $arrDestino[0]['vd_id'] = $arrOrigen['vd_id'];
							$this->insertDestino = false;
							$this->setViajesDestinos($arrDestino);
						}
						else{
							$this->insertDestino = true;
							$vd_id = $this->setViajesDestinos($arrDestino);
						}
						
						if((int)$vd_id){
							array_push($arrDestinosImpactados, $vd_id);
							
							//-- Procesamos los delivery en tbl_viajes_destinos_delivery --//
							if(@$vd['delivery']){
								foreach($vd['delivery'] as $vdd){
									$arrDelivery = array();
									$arrDelivery['vdd_vd_id'] = (int)$vd_id;
									$arrDelivery['vdd_delivery'] = $vdd['delivery'];
									$arrDelivery['vdd_ini'] = $vdd['vdd_ini']?date('Y-m-d H:i:s',strtotime(str_replace('/','-',$vdd['vdd_ini']))):NULL;
									$arrDelivery['vdd_fin'] = $vdd['vdd_fin']?date('Y-m-d H:i:s',strtotime(str_replace('/','-',$vdd['vdd_fin']))):NULL;
									$arrDelivery['vdd_volumen'] = isset($vdd['volumen'])?$vdd['volumen']:NULL;
									$arrDelivery['vdd_nro_factura'] = NULL;
									$arrDelivery['vdd_co_id'] = $vdd['id_conductor']?$vdd['id_conductor']:'NULL';
									$arrDelivery['vdd_mo_id'] = $vdd['id_movil']?$vdd['id_movil']:'NULL';
									$arrDelivery['vdd_cl_id'] = $vdd['id_trans']?$vdd['id_trans']:'NULL';
									
									$id_delivery = $this->setViajesDelvery($arrDelivery);	
									if((int)$id_delivery){
										array_push($arrDestinosDeliveryImpactados, $id_delivery);
										
										if($banPedidos){
										$this->borrarViajesDelveryPedidos($id_delivery);
										foreach($vdd['pedido'] as $vddp){
											$arrPedido = array();
											$arrPedido['vddp_vdd_id'] = (int)$id_delivery;
											$arrPedido['vddp_pedido'] = trim($vddp['pedido']); 
											$arrPedido['vddp_condicion_pago'] = NULL; 
											$arrPedido['vddp_fecha'] = NULL;
											$arrPedido['vddp_cant_conf'] = NULL;
											$arrPedido['vddp_valor_neto'] = NULL;
											$id_pedido = $this->setViajesDelveryPedidos($arrPedido);
										}}
									}
									//-- fin. tbl_viajes_destinos_delivery_pedidos --//
								}
							}//-- fin. tbl_viajes_destinos_delivery --//	
						}
					}
					$viajes_procesados['ok']++;
					
					//-- Limpio destinos y deliverys q fueron dados de baja mediante la planilla --//
					$stringDelivery = implode(',',$arrDestinosDeliveryImpactados);
					if(!empty($stringDelivery) && $this->id_viaje){
						$sql = " SELECT DISTINCT(vdd_id) as vdd_id, vdd_vd_id FROM tbl_viajes_destinos_delivery WITH(NOLOCK)";
						$sql.= " INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_id = vdd_vd_id ";
						$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vdd_id NOT IN (".$stringDelivery.") ";
						$res = $this->objSQL->dbQuery($sql);				
						$arrDeliveryBaja = $this->objSQL->dbGetAllRows($res,3);
							
						if($arrDeliveryBaja){	
							foreach($arrDeliveryBaja as $deliveryBaja){
								if($banPedidos){
									$sql = " DELETE FROM tbl_viajes_destinos_delivery_pedidos WHERE vddp_vdd_id = ".(int)$deliveryBaja['vdd_id'];	
									$this->objSQL->dbQuery($sql);
								}
								
								$sql = " DELETE FROM tbl_viajes_destinos_delivery WHERE vdd_id = ".(int)$deliveryBaja['vdd_id'];	
								$this->objSQL->dbQuery($sql);
									
								$sql = " SELECT COUNT(*) AS cant FROM tbl_viajes_destinos_delivery WITH(NOLOCK) WHERE vdd_vd_id = ".(int)$deliveryBaja['vdd_vd_id'];
								$res = $this->objSQL->dbQuery($sql);	
								$totalDeliveryMismoDestino = $this->objSQL->dbGetRow($res,0, 3);
								if($totalDeliveryMismoDestino['cant'] == 0){
									$sql = " DELETE FROM tbl_viajes_destinos WHERE vd_id = ".(int)$deliveryBaja['vdd_vd_id']; 
									$this->objSQL->dbQuery($sql);
								}
							}
						}
					}
						
					$stringDestinos = implode(',',$arrDestinosImpactados);
					if($stringDestinos){
						$sql = " DELETE FROM tbl_viajes_destinos WHERE vd_vi_id = ".$this->id_viaje." AND vd_id NOT IN (".$stringDestinos.") ";
						$res = $this->objSQL->dbQuery($sql);				
					}
					//--fin. limpieza --//
				}
				else{
					$viajes_procesados['error']++;	
				}
				//-- fin. tbl_viajes_destinos --//
			}//fin.if $vi['process'] == ok
			else{
				$viajes_procesados['error']++;
			}
		}
		##-- FIN PROCESAMIENTO DE VIAJES --##
		
		$msg = str_replace('<br>',"\r\n",$msg);
		$msg = str_replace('<br />',"\r\n",$msg);
		if($viajes_procesados['ok']){
			$mensaje = '<p>Se procesaron correctamente '.$viajes_procesados['ok'].' viajes.</p>';		
		}
		if($viajes_procesados['error']){
			$mensaje.= '<p>Se detectaron problemas en '.$viajes_procesados['error'].' viajes que no pudieron ser procesados.<br>';		
			$mensaje.= 'Click <a href="javascript:enviar(\'export_errorLog\');">agui</a> para descargar log de errores.</p>';
		}
		
		if(!empty($msg)){
			$this->errorLogImportacion($msg);
		}
		else{
			$this->errorLogImportacion(NULL); //Elimino archivo generado al inicio si no hay errores.
		}
		$return['msg'] = $mensaje;
		$return['errorLog'] = $this->rutaFile.$this->nameFile.'-error_log.txt';	
		
		return $return;
	}
	
	
	
	
	
	function errorLogImportacion($msg = NULL){
				
		$file = $this->rutaFile.$this->nameFile.'-error_log.txt';
		if(!empty($msg)){
			$log = fopen($file,'w');
			fwrite($log,$msg);
			fclose($log);
			chown($this->rutaServer.'/'.$file, 'root');
		}
		else{
			@unlink($this->rutaServer.'/'.$file);		
		}
	}
	
	function setViajesDelvery($item){
		global $lang;
		$item['vdd_volumen'] = str_replace('.','',$item['vdd_volumen']);
		$item['vdd_volumen'] = str_replace(',','.',$item['vdd_volumen']);
		
		$sql_select = " SELECT vdd_id, vdd_ini, vdd_fin, vdd_nro_factura, vdd_volumen, vdd_mo_id ";
		$sql_select.= " FROM tbl_viajes_destinos_delivery  WITH(NOLOCK) ";
		$sql_select.= " WHERE  vdd_vd_id = '".(int)$item['vdd_vd_id']."'";
		$sql_select.= " AND vdd_delivery = '".$item['vdd_delivery']."'";
		$res = $this->objSQL->dbQuery($sql_select);
		$arr_vd = $this->objSQL->dbGetRow($res,0,3);
		
		$movil_actual = $movil_new = '- '.$lang->system->sin_asignar.' -';
		if((int)$arr_vd['vdd_mo_id']){
			$sql = " SELECT mo_matricula FROM tbl_moviles WITH(NOLOCK) WHERE mo_id = ".(int)$arr_vd['vdd_mo_id'];
			$res = $this->objSQL->dbQuery($sql);
			$rs = $this->objSQL->dbGetRow($res,0,3);
			$movil_actual = !empty($rs['mo_matricula'])?$rs['mo_matricula']:$movil_actual;
		}
		if((int)$item['vdd_mo_id']){
			$sql = " SELECT mo_matricula FROM tbl_moviles WITH(NOLOCK) WHERE mo_id = ".(int)$item['vdd_mo_id'];
			$res = $this->objSQL->dbQuery($sql);
			$rs = $this->objSQL->dbGetRow($res,0,3);
			$movil_new = !empty($rs['mo_matricula'])?$rs['mo_matricula']:$movil_new;
		}
		
		
		$resp = true;
		$esInsert = false;
		$sql = $msg_log = $data_actual = $data_update = $coma = '';
		if($arr_vd['vdd_id']){
			$resp = $arr_vd['vdd_id'];
			
			if(date('d-m-Y H:i',strtotime($item['vdd_ini'])) != date('d-m-Y H:i',strtotime($arr_vd['vdd_ini']))){
				$data_actual = $lang->system->fecha_inicio.'['.formatearFecha($arr_vd['vdd_ini']).']';	
				$data_update = $lang->system->fecha_inicio.'['.formatearFecha($item['vdd_ini']).']';
				$coma = ', ';
			}	
			if(date('d-m-Y H:i',strtotime($item['vdd_fin'])) != date('d-m-Y H:i',strtotime($arr_vd['vdd_fin']))){
				$data_actual = $lang->system->fecha_fin.'['.formatearFecha($arr_vd['vdd_fin']).']';	
				$data_update = $lang->system->fecha_fin.'['.formatearFecha($item['vdd_fin']).']';
				$coma = ', ';
			}				
			if($arr_vd['vdd_nro_factura'] != $item['vdd_nro_factura']){
				$data_actual = $coma.'Nro de Factura ['.$arr_vd['vdd_nro_factura'].']';	
				$data_update = $coma.'Nro de Factura ['.$item['vdd_nro_factura'].']';
			}
			if($arr_vd['vdd_volumen'] != $item['vdd_volumen']){
				$data_actual = $coma.'Volumen ['.$arr_vd['vdd_volumen'].']';	
				$data_update = $coma.'Volumen ['.$item['vdd_volumen'].']';
			}
			if((int)$arr_vd['vdd_mo_id'] != (int)$item['vdd_mo_id']){
				$data_actual = $coma.$lang->system->matricula.' ['.$movil_actual.']';	
				$data_update = $coma.$lang->system->matricula.' ['.$movil_new.']';
			}
		
			if(!empty($data_update)){
				##-- TXT Log --##
				$msg_log = ' '.str_replace('[DATOS_ACTUALES]',($item['vdd_delivery'].', '.$data_actual),$lang->system->edicion_delivery->__toString());
				$msg_log = str_replace('[DATOS_EDITADOS]',$data_update,$msg_log);
				##-- --##
						
				$item['vdd_ini'] = !empty($item['vdd_ini'])?"'".$item['vdd_ini']."'":'NULL';
				$item['vdd_fin'] = !empty($item['vdd_fin'])?"'".$item['vdd_fin']."'":'NULL';
					
				$sql = " UPDATE tbl_viajes_destinos_delivery SET ";
				$sql.= " vdd_ini = ".$item['vdd_ini'];
				$sql.= " ,vdd_fin = ".$item['vdd_fin'];
				$sql.= " ,vdd_nro_factura = '".$item['vdd_nro_factura']."'";
				$sql.= " ,vdd_volumen = '".$item['vdd_volumen']."'";
				$sql.= " ,vdd_mo_id = ".$item['vdd_mo_id'];
				$sql.= " WHERE vdd_id = ".(int)$arr_vd['vdd_id'];
			}
		}
		else{
			##-- TXT Log --##
			$msg_log = $lang->system->alta_delivery.': '.$item['vdd_delivery'];
			$msg_log.= !empty($item['vdd_ini'])?', '.$lang->system->fecha_inicio.'['.formatearFecha($item['vdd_ini']).']':'';
			$msg_log.= !empty($item['vdd_fin'])?', '.$lang->system->fecha_fin.'['.formatearFecha($item['vdd_fin']).']':'';
			if(!empty($item['vdd_nro_factura'])){
				$msg_log.=', Nro de Factura ['.$item['vdd_nro_factura'].']';
			}
			if(!empty($item['vdd_volumen'])){
				$msg_log.=', Volumen ['.$item['vdd_volumen'].']';
			}
			if(!empty($movil_new)){
				$msg_log.=', '.$lang->system->matricula.' ['.$movil_new.']';
			}
			##-- --##
		
			$item['vdd_ini'] = !empty($item['vdd_ini'])?"'".$item['vdd_ini']."'":'NULL';
			$item['vdd_fin'] = !empty($item['vdd_fin'])?"'".$item['vdd_fin']."'":'NULL';
				
			$sql = " INSERT INTO tbl_viajes_destinos_delivery(vdd_vd_id, vdd_delivery, vdd_ini,  vdd_fin, vdd_nro_factura, vdd_volumen, vdd_mo_id) ";
			$sql.= " VALUES(".(int)$item['vdd_vd_id'].", '".$item['vdd_delivery']."',".$item['vdd_ini'].",".$item['vdd_fin'].",'".$item['vdd_nro_factura']."','".$item['vdd_volumen']."',".$item['vdd_mo_id'].")";	
			$esInsert = true;
		}

		if(!empty($sql)){
			if(!$this->objSQL->dbQuery($sql)){
				$resp = false;	
			}
			else{
				if($esInsert){
					$resp = $this->objSQL->dbLastInsertId();
				}
				$this->setLog($msg_log);
			}
		}
			
		return $resp;
	}
	
	function borrarViajesDelveryPedidos($id_delivery){
		$sql_select = " DELETE FROM tbl_viajes_destinos_delivery_pedidos ";
		$sql_select.= " WHERE vddp_vdd_id = '".(int)$id_delivery."'";
		$res = $this->objSQL->dbQuery($sql_select);
		if($this->objSQL->dbGetRow($res,0,3)){
			return true;	
		}
		else{
			return false;	
		}
	}
	
	function setViajesDelveryPedidos($item){
		
		$item['vddp_condicion_pago'] = trim($item['vddp_condicion_pago']); 
		$item['vddp_fecha'] = str_replace('.','-',trim($item['vddp_fecha']));
		$item['vddp_fecha'] = !empty($item['vddp_fecha'])?"'".date('Y-m-d',strtotime($item['vddp_fecha']))."'":'NULL';
		
		//$item['vddp_cant_conf'] = str_replace('.','',trim($item['vddp_cant_conf']));
		//$item['vddp_cant_conf'] = str_replace(',','.',trim($item['vddp_cant_conf']));
		
		$item['vddp_valor_neto'] = str_replace('.','',trim($item['vddp_valor_neto']));
		$item['vddp_valor_neto'] = str_replace(',','.',trim($item['vddp_valor_neto']));
		$item['vddp_valor_neto'] = str_replace('-','',trim($item['vddp_valor_neto']));
		
		$sql = " INSERT INTO tbl_viajes_destinos_delivery_pedidos(vddp_vdd_id, vddp_pedido, vddp_condicion_pago, vddp_fecha/*, vddp_cant_conf*/, vddp_valor_neto) ";
		$sql.= " VALUES(".(int)$item['vddp_vdd_id'].", ".(int)$item['vddp_pedido'].",'".$item['vddp_condicion_pago']."',".$item['vddp_fecha']."/*,'".$item['vddp_cant_conf']."'*/,'".$item['vddp_valor_neto']."')";	
		if(!$this->objSQL->dbQuery($sql)){
			$resp = false;	
		}
		else{
			$resp = $this->objSQL->dbLastInsertId();
		}
			
		return $resp;
	}
		
	function getListadoViajesDelivery($filtros = NULL){
	

// 24102019 Se agreg� la funci�n EstadoApp para facilitar el an�lisis.

		$sql = " SELECT vi_id, vi_codigo, vd_id, vdd_id,  vdd_delivery, re_id
                        , CASE vt_nombre WHEN 'ZCAD' THEN 1 WHEN 'ZTSL' THEN 1 ELSE 0 END vi_crossdocking
		-- // 22102021 lo cambiamos para arauco
			,case when vi_dador = 4835 then dbo.AraucoComentarios (vt_nombre , 2) else   CASE WHEN re_numboca != '' THEN '('+re_numboca+') '+vt_nombre ELSE vt_nombre END end AS re_nombre
			,mo_matricula + dbo.db_asignacion_semi (vi_id) as vi_movil , mo_id as id_movil, co_id as vdd_co_id ,vdd_cl_id , co_nombre+' '+co_apellido as co_conductor, convert(varchar(max), co_telefono)  as co_telefono
			, vi_finalizado, vi_co_id
			,CASE WHEN vd_orden = 0 THEN vd_ini ELSE vdd_ini END AS 'fecha_ini' 
			,CASE WHEN vd_orden = 0 THEN vd_fin ELSE vdd_fin END AS 'fecha_fin'
			,CASE WHEN vd_orden = 0 THEN vd_ini_real ELSE vdd_ini_real END AS 'fecha_ini_real'
			,CASE WHEN vd_orden = 0 THEN vd_fin_real ELSE vdd_fin_real END AS 'fecha_fin_real' 
			
			,CASE WHEN vd_orden = 0 AND vi_finalizado = 0 THEN 	
				(SELECT CONVERT(VARCHAR,vie_id)+'#'+vie_descripcion FROM tbl_viajes_instancias_estados 
				WHERE vie_id IN (
						SELECT TOP 1 CASE WHEN vie_siguiente_paso = 9999 THEN 1 ELSE vie_siguiente_paso END AS vie_siguiente_paso FROM(
							SELECT TOP 1 vie_siguiente_paso 
							FROM tbl_viajes_instancias_estados WITH(NOLOCK)
							INNER JOIN tbl_viajes_instancias WITH(NOLOCK) ON vin_vie_id = vie_id AND vin_vi_id = vi_id ORDER BY vie_id DESC
							UNION
							SELECT 9999 AS vie_siguiente_paso
						) as instancia
					)
				)ELSE NULL END AS 'paso_instancia'
				
			-- lo ocultamos para Arauco 2021,CASE WHEN vd_orden = 0 THEN trans.cl_razonSocial ELSE (CASE WHEN  vdd_cl_id IS NULL THEN trans.cl_razonSocial ELSE transDelivery.cl_razonSocial END) END AS 'transportista' 
			,case when trans.cl_cuit = '9876' then '' else  '('+ convert(varchar(max),trans.cl_cuit)  +  ') ' + trans.cl_razonSocial end  AS 'transportista' 

			,CASE WHEN vd_orden = 0 THEN trans.cl_id ELSE (CASE WHEN  vdd_cl_id IS NULL THEN trans.cl_id ELSE transDelivery.cl_id END) END AS 'vi_transportista' 
			, case when vi_dador = 4835 then '' else  dador.cl_razonSocial end as dador
			,vd_orden
			,sh_rd_id
            ,CASE WHEN vd_orden = 0 THEN vd_retroactivo ELSE vdd_retroactivo END AS 'retroactivo' 
			,vd_estado
			";
		//-- Mensaje de disponibilidad de vehículos por parte del transportista --//
		$sql.= " , CASE WHEN mo_id IS NULL 
					THEN 
						CASE WHEN vd_orden = 0 
							THEN 
								(SELECT COUNT(vdtm_mo_id)  
								FROM tbl_viajes_disponibilidad_transportistas WITH(NOLOCK) 
								INNER JOIN tbl_viajes_disponibilidad_transportistas_moviles WITH(NOLOCK) ON vdtm_vdt_id = vdt_id
								WHERE vdt_cl_id = trans.cl_id AND CONVERT(VARCHAR,vdt_fecha,106) = CONVERT(VARCHAR,vd_ini,106))
							ELSE 
								(SELECT COUNT(vdtm_mo_id)  
								FROM tbl_viajes_disponibilidad_transportistas WITH(NOLOCK)
								INNER JOIN tbl_viajes_disponibilidad_transportistas_moviles WITH(NOLOCK) ON vdtm_vdt_id = vdt_id
								WHERE vdt_cl_id IN (trans.cl_id,transDelivery.cl_id)  AND CONVERT(VARCHAR,vdt_fecha,106) = CONVERT(VARCHAR,vdd_ini,106))
							END 
					ELSE NULL END 'disponibilidad' ";
		//-- --//	
			
		$sql.= " FROM tbl_viajes WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vi_id = vd_vi_id ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " INNER JOIN tbl_clientes trans WITH(NOLOCK) on vi_transportista = trans.cl_id ";
		$sql.= " LEFT JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vd_id = vdd_vd_id ";
		$sql.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON mo_id = (CASE WHEN vdd_id IS NULL THEN vi_mo_id ELSE vdd_mo_id END) ";
		$sql.= " LEFT JOIN tbl_unidad WITH(NOLOCK) ON mo_id = un_mo_id ";
		$sql.= " LEFT JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		$sql.= " LEFT JOIN tbl_conductores WITH(NOLOCK) ON co_id = (CASE WHEN (CASE WHEN vdd_id IS NULL THEN vi_co_id ELSE vdd_co_id END) IS NULL THEN mo_co_id_primario ELSE (CASE WHEN vdd_id IS NULL THEN vi_co_id ELSE vdd_co_id END) END) ";
		$sql.= " LEFT JOIN tbl_clientes dador WITH(NOLOCK) on vi_dador = dador.cl_id ";		
		$sql.= " LEFT JOIN tbl_clientes transDelivery WITH(NOLOCK) on vdd_cl_id = transDelivery.cl_id ";
                $sql.= " LEFT JOIN tbl_viajes_tipo WITH(NOLOCK) ON vt_id = vi_vt_id ";
		$sql.= $this->filtrosViajes($filtros);
		
		$sql.= " ORDER BY vi_codigo, vd_orden, vdd_delivery ASC, vd_ini ASC ";



		$objRes = $this->objSQL->dbQuery($sql);	
		$res = $this->objSQL->dbGetAllRows($objRes,3);
		
		return $res;
	}
	
	function getListadoViajesDeliveryExportar($filtros = NULL){
		
		$sql = " SELECT vd_orden, vi_codigo, vdd_delivery, re_numboca, mo_matricula as vi_movil, cl_razonSocial, vdd_volumen, vdd_id, rg_nombre, gba_nombre
			
			,CASE WHEN vd_orden = 0 THEN re_nombre ELSE NULL END AS 'vi_origen'
			,CASE WHEN vd_orden > 0 THEN re_nombre ELSE NULL END AS 'vi_destino' 

			,CASE WHEN vd_orden = 0 THEN vd_ini ELSE NULL END AS 'vd_ini' 
			,CASE WHEN vd_orden = 0 THEN vd_fin ELSE NULL END AS 'vd_fin'
			,CASE WHEN vd_orden = 0 THEN vd_ini_real ELSE NULL END AS 'vd_ini_real'
			,CASE WHEN vd_orden = 0 THEN vd_fin_real ELSE NULL END AS 'vd_fin_real' 
			
			,CASE WHEN vd_orden > 0 THEN vdd_ini ELSE NULL END AS 'vdd_ini' 
			,CASE WHEN vd_orden > 0 THEN vdd_fin ELSE NULL END AS 'vdd_fin'
			,CASE WHEN vd_orden > 0 THEN vdd_ini_real ELSE NULL END AS 'vdd_ini_real'
			,CASE WHEN vd_orden > 0 THEN vdd_fin_real ELSE NULL END AS 'vdd_fin_real' 
			
			,CASE WHEN vd_orden = 0 THEN (SELECT vin_fecha from tbl_viajes_instancias WITH(NOLOCK) where vin_vi_id = vi_id AND vin_vie_id = 1) END as 'inicio_preparacion'
			,CASE WHEN vd_orden = 0 THEN (SELECT vin_fecha from tbl_viajes_instancias WITH(NOLOCK) where vin_vi_id = vi_id AND vin_vie_id = 2) END as 'fin_preparacion'
			,CASE WHEN vd_orden = 0 THEN (SELECT vin_fecha from tbl_viajes_instancias WITH(NOLOCK) where vin_vi_id = vi_id AND vin_vie_id = 2) END as 'fin_carga'
			,CASE WHEN vd_orden = 0 THEN (SELECT vin_fecha from tbl_viajes_instancias WITH(NOLOCK) where vin_vi_id = vi_id AND vin_vie_id = 2) END as 'entrega_documentos'
		";
		$sql.= " FROM tbl_viajes WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vi_id = vd_vi_id ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " INNER JOIN tbl_referencias_grupos WITH(NOLOCK) ON re_rg_id = rg_id ";
		$sql.= " INNER JOIN tbl_clientes trans WITH(NOLOCK) ON vi_transportista = trans.cl_id ";
		$sql.= " LEFT JOIN tbl_referencias_rel_gba WITH(NOLOCK) ON rel_re_id = re_id ";
		$sql.= " LEFT JOIN tbl_referencias_gba WITH(NOLOCK) ON rel_gba_id = gba_id ";
		$sql.= " LEFT JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vd_id = vdd_vd_id ";
		$sql.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON mo_id = (CASE WHEN vdd_id IS NULL THEN vi_mo_id ELSE vdd_mo_id END) ";
		//$sql.= "  --left JOIN tbl_viajes_destinos_delivery_pedidos WITH(NOLOCK) ON vddp_vdd_id = vdd_id ";
		
		$sql.= $this->filtrosViajes($filtros);
		
		$sql.= " ORDER BY vi_codigo, vd_orden, vdd_delivery ASC, vd_ini ASC ";
		
		$objRes = $this->objSQL->dbQuery($sql);	
		$res = $this->objSQL->dbGetAllRows($objRes,3);
		
		return $res;
	}
	
	function filtrosViajes($filtros){
		if(!empty($filtros['f_ini'])){
			$filtros['f_ini'].= ' 00:00:00';
		}
		if(!empty($filtros['f_fin'])){
			$filtros['f_fin'].= ' 23:59:59';
		}
		
// 28012020 agregamos vd_orden != 0 para buscar solo las entregas.
		$sql = " WHERE vd_orden =  case when vi_dador = 4835  then  0   else vd_orden end and vi_borrado = 0 AND vi_delivery = 1 ";
		
		if(!empty($filtros['f_ini'])){
			$sql.= " AND ((vd_ini >= '".$filtros['f_ini']."' AND vd_orden = 0) OR vdd_ini >= '".$filtros['f_ini']."')";		
		}
		if(!empty($filtros['f_fin'])){
			$sql.= " AND (vd_fin <= '".$filtros['f_fin']."' OR vdd_fin <= '".$filtros['f_fin']."')";
		}
		if(!empty($filtros['buscar'])){
			$filtros['buscar'] = str_replace('  ',' ',trim($filtros['buscar']));
			$auxfiltro = explode(' ',$filtros['buscar']);
			
			$sql.= " AND (";
			$auxOr = '';
			foreach($auxfiltro as $aux){
				if(!empty($aux)){
					$sql.= $auxOr." vi_codigo LIKE '%".$aux."%' 
					OR vdd_delivery LIKE '%".$aux."%'
					OR vdd_id IN (SELECT vddp_vdd_id FROM tbl_viajes_destinos_delivery_pedidos WITH(NOLOCK) WHERE vddp_pedido LIKE '%".$aux."%')";
					$auxOr = ' OR ';
				}
			}
			$sql.= " )";
		}
		
		/**/
		//@session_start();
		$sqlExec = "declare @usuario int 
				set @usuario = ".$_SESSION['idUsuario']." 				
				declare @tipoEmpresa int
				set @tipoEmpresa = -1				
				declare @tipoCliente int 
				set @tipoCliente = -1 
				declare @empresa int
				declare @agente int

				SET NOCOUNT ON
								
				SELECT @tipoCliente = cl_tipo_cliente, @tipoEmpresa= cl_tipo, @empresa = cl_id , @agente= cl_id_distribuidor

				FROM tbl_clientes WITH(NOLOCK)
				INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id 
				WHERE us_id = @usuario 
								
				SELECT @tipoEmpresa tipoEmpresa,@tipoCliente tipoCliente,@empresa empresa, @agente agente
				";
		
		$objRes=$this->objSQL->dbQuery($sqlExec);	
		$res=$this->objSQL->dbGetAllRows($objRes,3);
				
		//Si el cl_tipo es 2 (cliente), filtro por dador o transportista
		if($res[0]['tipoEmpresa'] == 1 && $res[0]['tipoCliente'] == 1){ //tipoEmpresa Agente && tipoCliente Dador
			$sql.= "AND vi_dador = ".$res[0]['empresa']." ";
		}
		
		if($res[0]['tipoEmpresa'] == 1 && $res[0]['tipoCliente'] != 1){ //tipoEmpresa Agente && tipoCliente no es dador
			$sql.= "AND trans.cl_id_distribuidor = ".$res[0]['empresa']." ";
		}
		
		if($res[0]['tipoEmpresa'] == 2){
			
		/*
			$sql.= "AND vi_dador = 
					CASE ".$res[0]['tipoCliente']."
						WHEN  1 THEN  ".$res[0]['empresa']."  -- ( DADOR )
						WHEN  2 THEN  vi_dador 			  -- TRANSPORTISTA
						ELSE  vi_dador         			  -- LOCALIZART O AGENTE
					END
					AND trans.cl_id = 
					CASE ".$res[0]['tipoCliente']."
						WHEN  1 THEN trans.cl_id 			-- DADOR
						WHEN  2 THEN ".$res[0]['empresa']." -- ( TRANSPORTISTA )
						ELSE trans.cl_id         			-- LOCALIZART O AGENTE
					END	
					";
		*/
		$sql.= "AND ( ( vi_dador = ".$res[0]['agente']. " AND vi_transportista = ".$res[0]['empresa']. ")   )   ";




		}
		
		/**/
			
				
		##-- FILTROS COL --##
		if(!empty($filtros['transportista'])){
			if(strpos($filtros['transportista'],',-1')){
				$sql.= " AND (vi_transportista IN(".$filtros['transportista'].") OR vi_transportista IS NULL)";
			}
			elseif($filtros['movil'] == '-1'){
				$sql.= " AND vi_transportista IS NULL";
			}
			else{
				$sql.= " AND vi_transportista IN(".$filtros['transportista'].")";
			}
		}
		
		if(!empty($filtros['movil'])){
			if(strpos($filtros['movil'],',-1')){
				$sql.= " AND (mo_id IN(".$filtros['movil'].") OR mo_id IS NULL)";
			}
			elseif($filtros['movil'] == '-1'){
				$sql.= " AND mo_id IS NULL";
			}
			else{
				$sql.= " AND mo_id IN(".$filtros['movil'].")";
			}
		}
		
		if(!empty($filtros['referencia'])){
			$sql.= " AND re_id IN(".$filtros['referencia'].")";
		}
		
		if(!empty($filtros['iniReal'])){
			if($filtros['iniReal'] == 1){ //Ingreso Realizado
				$sql.= " AND CASE WHEN vd_orden = 0 THEN vd_ini_real ELSE vdd_ini_real END IS NOT NULL ";
			}
			elseif($filtros['iniReal'] == 2){ //Ingreso Pendiente
				$sql.= " AND CASE WHEN vd_orden = 0 THEN vd_ini_real ELSE vdd_ini_real END IS NULL ";
			}
		}
		
		if(!empty($filtros['finReal'])){
			if($filtros['finReal'] == 1){ //Egreso Realizado
				$sql.= " AND CASE WHEN vd_orden = 0 THEN vd_fin_real ELSE vdd_fin_real END IS NOT NULL ";
			}
			elseif($filtros['finReal'] == 2){ //Egreso Pendiente
				$sql.= " AND CASE WHEN vd_orden = 0 THEN vd_fin_real ELSE vdd_fin_real END IS NULL ";
			}
		}

		if(!empty($filtros['pod'])){
			if($filtros['pod'] == 1){ //Con entrega confirmado
				$sql.= " AND vd_estado = 1 ";
			}
			elseif($filtros['pod'] == 2){ //Sin entrega confirmado
				$sql.= " AND (vd_estado = 0 OR vd_estado IS NULL)";
			}
		}
		##-- --##
		
		##-- Arribos y Partidas --##
		if(!empty($filtros['arribo'])){
			switch($filtros['arribo']){
				case '1': //Ingreso Realizado - Atrasado
					$sql.= " AND (DATEDIFF(ss, vd_ini, vd_ini_real) > 0 OR DATEDIFF(ss, vdd_ini, vdd_ini_real) > 0)";
				break;
				case '2': //Ingreso Realizado - En Tiempo
					$sql.= " AND (DATEDIFF(ss, vd_ini, vd_ini_real) <= 0 OR DATEDIFF(ss, vdd_ini, vdd_ini_real) <= 0)";
				break;
				case '3': //Ingreso Pendiente - Atrasado
				break;
				case '4': //Ingreso Pendiente - En Tiempo
				break;
			}
		}
		
		if(!empty($filtros['partida'])){
			$ids = explode(',',$filtros['partida']);
			$or = $aux = '';
			foreach($ids as $idFiltro){
				switch($idFiltro){
					case '1': //Egreso Realizado - Atrasado
						$aux.= $or." (DATEDIFF(ss, vd_fin, vd_fin_real) > 0 OR DATEDIFF(ss, vdd_fin, vdd_fin_real) > 0)";
					break;
					case '2': //Egreso Realizado - En Tiempo
						$aux.= $or." (DATEDIFF(ss, vd_fin, vd_fin_real) <= 0 OR DATEDIFF(ss, vdd_fin, vdd_fin_real) <= 0) ";
					break;
					case '3': //Egreso Pendiente - Atrasado
						$aux.= $or." (
							(DATEDIFF(ss, vd_fin, '".getFechaServer('Y-m-d H:i')."') > 0  AND vd_fin_real IS NULL)
							OR
							(DATEDIFF(ss, vdd_fin, '".getFechaServer('Y-m-d H:i')."') > 0  AND vdd_fin_real IS NULL)
						)";
					break;
					case '4': //Egreso Pendiente - En Tiempo
						$aux.= $or." (
							(DATEDIFF(ss, vd_fin, '".getFechaServer('Y-m-d H:i')."') <= 0  AND vd_fin_real IS NULL)
							OR
							(DATEDIFF(ss, vdd_fin, '".getFechaServer('Y-m-d H:i')."') <= 0  AND vdd_fin_real IS NULL)
						)";
					break;
					default;
						$aux = '';
					break;	
				}
				$or = ' OR ';
				if(empty($aux)){
					break;
				}
			}
			if(!empty($aux)){
				$sql.= " AND (".$aux.")";
			}
		}
		
		//-- Filtrar solo por planta o Cliente --//
		if($filtros['tipo_referencia'] == 'planta'){
			$sql.= " AND re_rg_id = 39 ";
		}
		elseif($filtros['tipo_referencia'] == 'cliente'){
			$sql.= " AND re_rg_id != 39 ";	
		}
		//-- --//
		##-- --##
		
		return $sql;	
	}
	
	function getViajesDeliveryPedidos($delivery){
		$sql = " SELECT vdd_delivery, p.* ";
		$sql.= " FROM tbl_viajes_destinos_delivery_pedidos p WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vdd_id = vddp_vdd_id ";
		$sql.= " WHERE vdd_id ";	
		if(is_array($delivery)){
			$sql.= " IN (".implode(',',$delivery).") ";		
		}
		else{
			$sql.= " = ".(int)$delivery;	 	
		}
		$objRes = $this->objSQL->dbQuery($sql);	
		$res = $this->objSQL->dbGetAllRows($objRes,3);
		return $res;
	}
	
	function getCodigoPedidos($id_delivery, $implode = false){
		$sql = " SELECT DISTINCT(vddp_pedido) ";
		$sql.= " FROM tbl_viajes_destinos_delivery_pedidos WITH(NOLOCK) ";
		$sql.= " WHERE vddp_vdd_id = ".(int)$id_delivery;
		$objRes = $this->objSQL->dbQuery($sql);	
		$res = $this->objSQL->dbGetAllRows($objRes,3);
		
		if($implode){
			if($res){
				foreach($res as $item){
					$pedidos.= $coma.$item['vddp_pedido'];
					$coma = $implode;	
				}	
			}
			
			$res = $pedidos;
		}
		
		return $res;
	}
	
	
	function getDatosViajeDelivery(){
		$sql = " SELECT vi_fechacreado, vi_codigo, dbo.AraucoComentarios (vt_nombre , 2) as vt_nombre, vi_id, vi_mo_id, vi_co_id, vi_transportista
			, mo_matricula +  dbo.db_asignacion_semi (vi_id)  as vi_movil, co_nombre+' '+co_apellido as co_conductor, convert(varchar(max), co_telefono) as co_telefono
			,vi_observaciones,vi_observaciones_2, vi_finalizado
                        , CASE vt_nombre WHEN 'ZCAD' THEN 1 WHEN 'ZTSL' THEN 1 ELSE 0 END vi_crossdocking
		";	
		$sql.= " FROM tbl_viajes WITH(NOLOCK) ";
		$sql.= " LEFT JOIN tbl_viajes_tipo WITH(NOLOCK) ON vt_id = vi_vt_id ";
		$sql.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON mo_id = vi_mo_id ";
		$sql.= " LEFT JOIN tbl_conductores WITH(NOLOCK) ON co_id = (CASE WHEN vi_co_id IS NULL THEN mo_co_id_primario ELSE vi_co_id END)";
		$sql.= " WHERE vi_id = ".$this->id_viaje;
		$objRes = $this->objSQL->dbQuery($sql);	
		$res = $this->objSQL->dbGetRow($objRes,0, 3);
		return $res;
	}
	
	function getDestinosDelivery(){
		
		$sql = " SELECT vd_id, vdd_id, vdd_delivery, vdd_rechazado, vdd_mo_id, re_id, vd_re_id, vd_orden, vd_pod_manual
			,CASE WHEN re_numboca != '' THEN '('+re_numboca+') '+re_nombre ELSE re_nombre END AS re_nombre
			,mo_matricula +  dbo.db_asignacion_semi (vi_id) as vi_movil ,co_id as vdd_co_id, vdd_cl_id, co_nombre+' '+co_apellido as co_conductor, convert(varchar(max), co_telefono) as co_telefono
			,CASE WHEN vd_orden = 0 THEN vd_ini ELSE vdd_ini END AS 'fecha_ini'
			,CASE WHEN vd_orden = 0 THEN vd_fin ELSE vdd_fin END AS 'fecha_fin'
			,CASE WHEN vd_orden = 0 THEN vd_ini_real ELSE vdd_ini_real END AS 'fecha_ini_real'
			,CASE WHEN vd_orden = 0 THEN vd_fin_real ELSE vdd_fin_real END AS 'fecha_fin_real'
                        ,CASE WHEN vd_orden = 0 THEN vd_retroactivo ELSE vdd_retroactivo END AS 'retroactivo'
		";	
		$sql.= " ,case when trans.cl_cuit = '9876' then '' else  '('+ convert(varchar(max),trans.cl_cuit)  +  ') ' + trans.cl_razonSocial end AS 'transportista', vi_finalizado";
		$sql.= " ,'' AS 'dador' ";		
		$sql.= " FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes WITH(NOLOCK) ON vi_id = vd_vi_id  left join tbl_clientes trans with (nolock) on vi_transportista = cl_id ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " LEFT JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vd_id = vdd_vd_id ";
		$sql.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON mo_id = vdd_mo_id ";
		$sql.= " LEFT JOIN tbl_conductores WITH(NOLOCK) ON co_id = (CASE WHEN vdd_co_id IS NULL THEN mo_co_id_primario ELSE vdd_co_id END)";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje;
		$sql.= " ORDER BY vd_orden, vdd_delivery ASC, vd_ini ASC ";
                        
		$objRes = $this->objSQL->dbQuery($sql);	
		$res = $this->objSQL->dbGetAllRows($objRes,3);
		return $res;	
	}
	
	function setConductorVehiculo($id_delivery, $id_transportista, $id_conductor, $id_movil){
		global $lang;
		$id_transportista = (int)$id_transportista?$id_transportista:'NULL';
		$id_movil = (int)$id_movil?$id_movil:'NULL';
		$id_conductor = (int)$id_conductor?$id_conductor:'NULL';
		$id_delivery = (int)$id_delivery?$id_delivery:'NULL';
		
		if($id_delivery){
			##-- TXT Log --##
			$sql = " SELECT mo_id, mo_matricula+' - '+convert(varchar,mo_identificador) as movil,co_id,co_nombre+' '+co_apellido as conductor ";
			$sql.= " FROM tbl_viajes_destinos_delivery WITH(NOLOCK) ";
			$sql.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON mo_id = vdd_mo_id ";
			$sql.= " LEFT JOIN tbl_conductores WITH(NOLOCK) ON co_id = vdd_co_id ";
			$sql.= " WHERE vdd_id = ".(int)$id_delivery;
			$res = $this->objSQL->dbQuery($sql);
			$arr_viaje = $this->objSQL->dbGetRow($res,0,3);	
		
			$sql = " SELECT co_nombre+' '+co_apellido as nombre FROM tbl_conductores WITH(NOLOCK) WHERE co_id = ".(int)$id_conductor;
			$res = $this->objSQL->dbQuery($sql);
			$arr_conductor = $this->objSQL->dbGetRow($res,0,3);
			
			$sql = " SELECT mo_matricula+' - '+mo_identificador as movil FROM tbl_moviles WITH(NOLOCK) WHERE mo_id = ".(int)$id_movil;
			$res = $this->objSQL->dbQuery($sql);
			$arr_movil = $this->objSQL->dbGetRow($res,0,3);
		
			$msg_log = ' Cambio Movil/Condudctor desde el atajo: '.$lang->system->movil.'['.($arr_viaje['mo_id']?$arr_viaje['movil']:'-'.$lang->system->sin_asignar.'-').'], '.$lang->system->conductor.'['.($arr_viaje['co_id']?$arr_viaje['conductor']:'-'.$lang->system->sin_asignar.'-').']';
			$msg_log.= ' por los siguientes datos: '.$lang->system->movil.'['.((int)$id_movil?$arr_movil['movil']:'-'.$lang->system->sin_asignar.'-').'], '.$lang->system->conductor.'['.((int)$id_conductor?$arr_conductor['nombre']:'-'.$lang->system->sin_asignar.'-').']';
			$msg_log = decode($msg_log);
			##-- --##		
		
			$sql = " UPDATE tbl_viajes_destinos_delivery SET vdd_mo_id = ".$id_movil.", vdd_co_id = ".$id_conductor.", vdd_cl_id = ".$id_transportista." WHERE vdd_id = ".(int)$id_delivery;
			if($res = $this->objSQL->dbQuery($sql)){
				$this->setLog($msg_log);
				return true;		
			}
		}
		return false;
	}
	
	function setConductorVehiculoCompleto($id_conductor, $id_movil, $id_transportista = NULL){
		global $lang;
		$id_movil = (int)$id_movil?$id_movil:'NULL';
		$id_conductor = (int)$id_conductor?$id_conductor:'NULL';
		
		##-- TXT Log --##
		$sql = " SELECT mo_id, mo_matricula+' - '+convert(varchar,mo_identificador) as movil,co_id,co_nombre+' '+co_apellido as conductor ";
		$sql.= " FROM tbl_viajes WITH(NOLOCK) ";
		$sql.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON mo_id = vi_mo_id ";
		$sql.= " LEFT JOIN tbl_conductores WITH(NOLOCK) ON co_id = vi_co_id ";
		$sql.= " WHERE vi_id = ".$this->id_viaje;
		$res = $this->objSQL->dbQuery($sql);
		$arr_viaje = $this->objSQL->dbGetRow($res,0,3);
		
		$sql = " SELECT co_nombre+' '+co_apellido as nombre FROM tbl_conductores WITH(NOLOCK) WHERE co_id = ".(int)$id_conductor;
		$res = $this->objSQL->dbQuery($sql);
		$arr_conductor = $this->objSQL->dbGetRow($res,0,3);
		
		$sql = " SELECT mo_matricula+' - '+mo_identificador as movil FROM tbl_moviles WITH(NOLOCK) WHERE mo_id = ".(int)$id_movil;
		$res = $this->objSQL->dbQuery($sql);
		$arr_movil = $this->objSQL->dbGetRow($res,0,3);
		
		$msg_log = ' Cambio Movil/Condudctor desde el atajo: '.$lang->system->movil.'['.($arr_viaje['mo_id']?$arr_viaje['movil']:'-'.$lang->system->sin_asignar.'-').'], '.$lang->system->conductor.'['.($arr_viaje['co_id']?$arr_viaje['conductor']:'-'.$lang->system->sin_asignar.'-').']';
		$msg_log.= ' por los siguientes datos: '.$lang->system->movil.'['.((int)$id_movil?$arr_movil['movil']:'-'.$lang->system->sin_asignar.'-').'], '.$lang->system->conductor.'['.((int)$id_conductor?$arr_conductor['nombre']:'-'.$lang->system->sin_asignar.'-').']';
		##-- --##		
		
		$sql = " UPDATE tbl_viajes SET vi_mo_id = ".$id_movil.", vi_co_id = ".$id_conductor;
		if($id_transportista){
			$sql.= " , vi_transportista = ".(int)$id_transportista;
		}
		$sql.= " WHERE vi_id = ".$this->id_viaje;
		if($res = $this->objSQL->dbQuery($sql)){
			
			$sql = " UPDATE tbl_viajes_destinos_delivery SET vdd_mo_id = ".$id_movil.", vdd_co_id = ".$id_conductor;
			if($id_transportista){
				$sql.= " , vdd_cl_id = ".(int)$id_transportista;
			}
			$sql.= " WHERE vdd_vd_id IN (SELECT vd_id FROM tbl_viajes_destinos WHERE vd_vi_id = ".$this->id_viaje.")";
			if($res = $this->objSQL->dbQuery($sql)){
				$this->setLog($msg_log);
				return true;		
			}
		}
		return false;
	}
	
	function setRechazoDelivery($id_destino, $id_delivery, $estado){
		##-- TXT Log --##
		$sql = " SELECT vdd_delivery FROM tbl_viajes_destinos_delivery WITH(NOLOCK) WHERE vdd_id = ".(int)$id_delivery." AND vdd_vd_id = ".(int)$id_destino;
		$res = $this->objSQL->dbQuery($sql);
		$arr = $this->objSQL->dbGetRow($res,0,3);	
		$msg_log = ' Delivery ['.$arr['vdd_delivery'].'] '.(($estado)?'fue Rechazado':'se paso de Rechazado a NO Rechazado');
		##-- --##		
		
		$sql = " UPDATE tbl_viajes_destinos_delivery SET vdd_rechazado = ".(int)$estado.", vdd_fin_real = NULL WHERE vdd_id = ".(int)$id_delivery." AND vdd_vd_id = ".(int)$id_destino;
		if($res = $this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return true;		
		}
		return false;
	}
	
	function getInstanciaViaje(){
		$sql = " SELECT vie_id, vie_descripcion, vin_fecha  ";	
		$sql.= " FROM tbl_viajes_instancias_estados WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes_instancias_estados_agentes WITH(NOLOCK) ON viea_vie_id = vie_id ";
		$sql.= " LEFT JOIN tbl_viajes_instancias WITH(NOLOCK) ON vin_vie_id = vie_id AND vin_vi_id = ".$this->id_viaje;
		$sql.= " WHERE viea_cl_id = ".(int)$_SESSION['idAgente'];
		$sql.= " ORDER BY vie_id ";
		$objRes = $this->objSQL->dbQuery($sql);	
		$res = $this->objSQL->dbGetAllRows($objRes,3);
		return $res;
	}
	
	function setInstanciaViaje($id_viaje, $paso_instancia){
		##-- TXT Log --##
		$sql = " SELECT act.vie_descripcion as actual
			, (SELECT sig.vie_descripcion FROM tbl_viajes_instancias_estados as sig where sig.vie_id = act.vie_siguiente_paso) as siguiente
			FROM tbl_viajes_instancias_estados  as act where act.vie_id = ".(int)$paso_instancia;
		$res = $this->objSQL->dbQuery($sql);
		$arr = $this->objSQL->dbGetRow($res,0,3);	
		$msg_log = ' Pasa de Instancia: '.$arr['actual'].' a '.$arr['siguiente'];
		##-- --##		
		
		$sql = " INSERT INTO tbl_viajes_instancias(vin_vi_id, vin_vie_id, vin_fecha) ";
		$sql.= " VALUES(".(int)$id_viaje.",".(int)$paso_instancia.",'".getFechaServer('Y-m-d H:i:s')."')";
		if($res = $this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return true;		
		}
		return false;
	}
	
	function assignFechaIngreso($id_destino, $id_delivery, $fecha){
		##-- TXT Log --##
		$sql = " SELECT vdd_delivery, vdd_id FROM tbl_viajes_destinos_delivery WITH(NOLOCK)
					WHERE vdd_vd_id IN (SELECT vdd_vd_id FROM tbl_viajes_destinos_delivery WITH(NOLOCK) WHERE vdd_id = ".(int)$id_delivery.")
					AND vdd_mo_id = (SELECT vdd_mo_id FROM tbl_viajes_destinos_delivery WITH(NOLOCK) WHERE vdd_id = ".(int)$id_delivery.")";
		$res = $this->objSQL->dbQuery($sql);
		$rows = $this->objSQL->dbGetAllRows($res);	
		$delivery = array('code'=>array(), 'id'=>array());
		foreach($rows as $item){
			array_push($delivery['code'],$item['vdd_delivery']);	
			array_push($delivery['id'],$item['vdd_id']);	
		}
		
		$sql = " SELECT re_nombre FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino;
		$res = $this->objSQL->dbQuery($sql);
		$arr_ref = $this->objSQL->dbGetRow($res,0,3);
		$msg_log = ' Asigna Fecha de Ingreso ['.date('d-m-Y H:i',strtotime($fecha)).'] para la Referencia: '.$arr_ref['re_nombre'].' correspondiente al Delivery ['.implode(',',$delivery['code']).']';
		##-- --##	
		
		$fecha = date('Y-m-d H:i:s',strtotime($fecha));
		$sql= " UPDATE tbl_viajes_destinos_delivery SET vdd_ini_real = '".$fecha."'";
		$sql.=" WHERE vdd_vd_id = (SELECT vd_id FROM tbl_viajes_destinos 
			WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino.") AND vdd_id IN (".implode(',',$delivery['id']).") ";
		if($this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return $fecha;
		}
		return false;
	}
	
	function assignFechaEgreso($id_destino, $id_delivery, $fecha){
		##-- TXT Log --##
		$sql = " SELECT vdd_delivery, vdd_id, vdd_ini_real FROM tbl_viajes_destinos_delivery WITH(NOLOCK)
					WHERE vdd_vd_id IN (SELECT vdd_vd_id FROM tbl_viajes_destinos_delivery WITH(NOLOCK) WHERE vdd_id = ".(int)$id_delivery.")
					AND vdd_mo_id = (SELECT vdd_mo_id FROM tbl_viajes_destinos_delivery WITH(NOLOCK) WHERE vdd_id = ".(int)$id_delivery.")";
		$res = $this->objSQL->dbQuery($sql);
		$rows = $this->objSQL->dbGetAllRows($res);	
		$delivery = array('code'=>array(), 'id'=>array());
		foreach($rows as $item){
			array_push($delivery['code'],$item['vdd_delivery']);	
			array_push($delivery['id'],$item['vdd_id']);	
		}
		
		$sql = " SELECT re_nombre FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino;
		$res = $this->objSQL->dbQuery($sql);
		$arr_ref = $this->objSQL->dbGetRow($res,0,3);
		$msg_log = ' Asigna Fecha de Egreso ['.date('d-m-Y H:i',strtotime($fecha)).'] para la Referencia: '.$arr_ref['re_nombre'].' correspondiente al Delivery ['.implode(',',$delivery['code']).']';
		##-- --##	
		
		$fecha = date('Y-m-d H:i:s',strtotime($fecha));
		$sql= " UPDATE tbl_viajes_destinos_delivery SET vdd_fin_real = '".$fecha."'";
		$sql.=" WHERE vdd_vd_id = (SELECT vd_id FROM tbl_viajes_destinos 
			WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino.") AND vdd_id IN (".implode(',',$delivery['id']).") ";
		
		if($this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return $this->getTiempoHM(strtotime($fecha) - strtotime($rows[0]['vdd_ini_real']));
		}
		return false;
	}

	function podFechaIngreso($id_destino, $fecha){
		$fecha = formatearFecha($fecha);
		
		##-- TXT Log --##
		$sql = " SELECT re_nombre FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino;
		$res = $this->objSQL->dbQuery($sql);
		$arr_ref = $this->objSQL->dbGetRow($res,0,3);
		$msg_log = ' Asigna Fecha de POD ['.$fecha.'] para la Referencia: '.$arr_ref['re_nombre'];
		##-- --##	
		
		$fecha = date('Y-m-d H:i:s',strtotime($fecha));
		$sql= " UPDATE tbl_viajes_destinos SET vd_pod_manual = '".$fecha."'";
		$sql.=" WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino;
		
		if($this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return $fecha;
		}
		return false;
	}
	
	function getDistanciaViaje(){
		global $lang;
		
		$sql = " SELECT TOP 1 rc_latitud, rc_longitud ";
		$sql.= " FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " INNER JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON rc_re_id = re_id ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_orden = 0 ";
		$sql.= " UNION ";
		$sql.= " SELECT rc_latitud, rc_longitud ";
		$sql.= " FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " INNER JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON rc_re_id = re_id ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_orden > 0 ";
		
		$res = $this->objSQL->dbQuery($sql);
		$rows = $this->objSQL->dbGetAllRows($res);	
		
		if($rows){
			$latAux = $lonAux = NULL;
			$km = 0;
			foreach($rows as $item){
				if(!$latAux || !$lonAux){
					$latAux = $item['rc_latitud'];
					$lonAux = $item['rc_longitud'];	
				}
				else{
					$km = $km + distancia($latAux, $lonAux, $item['rc_latitud'], $item['rc_longitud']);	
					$latAux = $item['rc_latitud'];
					$lonAux = $item['rc_longitud'];
				}
			}	
		}
		
		return formatearDistancia($km);
	}
	
	function getTiempoViaje(){
		global $lang;
		$sql = " SELECT MIN(ini) prog_ini, MAX(ini) prog_fin, MIN(ini_real) real_ini, MAX(ini_real) real_fin  ";
		$sql.= " FROM (";
		$sql.= " SELECT CASE vd_orden WHEN 0 THEN vd_ini ELSE vdd_ini END AS 'ini', CASE vd_orden WHEN 0 THEN vd_ini_real ELSE vdd_ini_real END AS 'ini_real' ";
		$sql.= " FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " LEFT JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vdd_vd_id = vd_id";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje;
		$sql.= " ) AS total ";
		
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res);	
		
		$programado = (strtotime($rs[0]['prog_fin']) - strtotime($rs[0]['prog_ini']));
		$time['programado'] = round($programado/3600).$lang->system->abrev_hora;
			
		$real = (strtotime($rs[0]['real_fin']) - strtotime($rs[0]['real_ini']));
		$time['real'] = round($real/3600).$lang->system->abrev_hora;
		
		return $time;
	}

	/*
	function getSMSRecibido($arrViajes){
		if($arrViajes){
			$aux = array();
			foreach($arrViajes as $k => $viaje){
				if(isset($aux[$viaje['vd_id']])){
					array_push($aux[$viaje['vd_id']],$k);
				}
				else{
					$aux[$viaje['vd_id']] = array($k);
				}
			}
			
			$aux_arr_vd_id = array_keys($aux);
			$aux_vd_id = implode(',',$aux_arr_vd_id);

			if(!empty($aux_vd_id)){
				$sql = " SELECT vd_id, bos_fecharespuesta, bos_respuesta FROM tbl_viajes_destinos AS vd
						INNER JOIN tbl_buffer_out_sms AS bos ON vd.vd_bos_id = bos.bos_id
						WHERE vd.vd_id IN ($aux_vd_id)";
				$objRes = $this->objSQL->dbQuery($sql);	
				$result = $this->objSQL->dbGetAllRows($objRes,3);
				if($result){
					foreach($result as $item){
						if(array_key_exists ($item['vd_id'], $aux)){
							foreach($aux[$item['vd_id']] as $key_viaje){
								$arrViajes[$key_viaje]['sms_fecha'] = $item['bos_fecharespuesta'];
								$arrViajes[$key_viaje]['sms_respuesta'] = $item['bos_respuesta'];
							}
						}
					}
				}
			}
		}
		return $arrViajes;
	}
	*/

	function getProcedureArribesPartidas($filtros){
		$filtros['playa'] = isset($filtros['playa']) ? $filtros['playa']: 0;
		$sql = "EXEC db_SegmentosViajes 0,{$filtros['partidas']},{$filtros['destinos']},{$filtros['operacion']},{$filtros['transportista']},{$filtros['movil']},'{$filtros['referencia']}',{$filtros['pendiente']},{$filtros['estado']},0,0";
		$objRes = $this->objSQL->dbQuery($sql);	
		$result =  $this->objSQL->dbGetAllRows($objRes,3);
		
if($result){
			foreach($result as $k => $item){
				//--Formateo datos
				$result[$k]['vi_codigo'] = $item['viaje'];
				unset($result[$k]['viaje']);
				
				$result[$k]['re_nombre'] = (!empty($item['re_numboca'])?"({$item['re_numboca']}) ":'').$item['re_nombre'];
				
				$result[$k]['vdd_delivery'] = str_replace(',','<br>',$item['entregas']);
				unset($result[$k]['entregas']);
				
				$result[$k]['vi_movil'] = $item['mo_matricula'];
				unset($result[$k]['mo_matricula']);

				$result[$k]['id_movil'] = $item['vi_mo_id'];
				unset($result[$k]['vi_mo_id']);

				$result[$k]['co_conductor'] = $item['conductor'];
				unset($result[$k]['conductor']);

				$result[$k]['transportista'] = $item['transportadora'];
				unset($result[$k]['transportadora']);

				$result[$k]['id_transportista'] = $item['id_transportadora'];
				unset($result[$k]['id_transportadora']);

				$result[$k]['fecha_ini'] = $item['vd_ini'];
				unset($result[$k]['vd_ini']);

				$result[$k]['fecha_fin'] = $item['vd_fin'];
				unset($result[$k]['vd_fin']);

				$result[$k]['fecha_ini_real'] = $item['vd_ini_real'];
				unset($result[$k]['vd_ini_real']);
				
				$result[$k]['fecha_fin_real'] = $item['vd_fin_real'];
				unset($result[$k]['vd_fin_real']);

				$result[$k]['dador'] = $item['operacion'];
				unset($result[$k]['operacion']);
			}
		}
		return $result;
	}
	
	/*** Arribos y Partidas ***/
	function getArribosPartidas($tipo, $filtros = NULL){
           	if($tipo != 'arribos' && $tipo != 'partidas'){return false;}
		
		$sql = " SELECT vi_id, vi_codigo, vdd_id,  vdd_delivery, re_id
			,CASE WHEN re_numboca != '' THEN '('+re_numboca+') '+re_nombre ELSE re_nombre END AS re_nombre
			,mo_matricula as vi_movil , mo_id as id_movil, co_id as vdd_co_id ,vdd_cl_id , co_nombre+' '+co_apellido as co_conductor, co_telefono
			,CASE WHEN vd_orden = 0 THEN vd_ini ELSE vdd_ini END AS 'fecha_ini' 
			,CASE WHEN vd_orden = 0 THEN vd_fin ELSE vdd_fin END AS 'fecha_fin'
			,CASE WHEN vd_orden = 0 THEN vd_ini_real ELSE vdd_ini_real END AS 'fecha_ini_real'
			,CASE WHEN vd_orden = 0 THEN vd_fin_real ELSE vdd_fin_real END AS 'fecha_fin_real' 
				
			,CASE WHEN vd_orden = 0 THEN trans.cl_razonSocial ELSE (CASE WHEN  vdd_cl_id IS NULL THEN trans.cl_razonSocial ELSE transDelivery.cl_razonSocial END) END AS 'transportista' 
			,CASE WHEN vd_orden = 0 THEN trans.cl_id ELSE (CASE WHEN  vdd_cl_id IS NULL THEN trans.cl_id ELSE transDelivery.cl_id END) END AS 'id_transportista' 
			, dador.cl_razonSocial as dador
			, sh_latitud, sh_longitud
			,vd_ini
                        ,sh_rd_id
			";
		//-- SE TUVO Q HACER ASÍ, PORQ LAS PLANTAS CON REFERENCIAS LINEALES.
		$sql.= " ,(SELECT TOP 1 rc_latitud FROM tbl_referencias_coordenadas WITH(NOLOCK) WHERE rc_re_id = re_id) AS rc_latitud ";	
		$sql.= " ,(SELECT TOP 1 rc_longitud FROM tbl_referencias_coordenadas WITH(NOLOCK) WHERE rc_re_id = re_id) AS rc_longitud ";
			
		$sql.= " FROM tbl_viajes AS vi WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vi_id = vd_vi_id ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " INNER JOIN tbl_clientes trans WITH(NOLOCK) ON vi_transportista = trans.cl_id ";
		$sql.= " LEFT JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vd_id = vdd_vd_id ";
		$sql.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON mo_id = (CASE WHEN vdd_id IS NULL THEN vi_mo_id ELSE vdd_mo_id END) ";
		$sql.= " LEFT JOIN tbl_unidad WITH(NOLOCK) ON mo_id = un_mo_id ";
		$sql.= " LEFT JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		$sql.= " LEFT JOIN tbl_conductores WITH(NOLOCK) ON co_id = (CASE WHEN (CASE WHEN vdd_id IS NULL THEN vi_co_id ELSE vdd_co_id END) IS NULL THEN mo_co_id_primario ELSE (CASE WHEN vdd_id IS NULL THEN vi_co_id ELSE vdd_co_id END) END) ";
		$sql.= " LEFT JOIN tbl_clientes dador WITH(NOLOCK) ON vi_dador = dador.cl_id ";		
		$sql.= " LEFT JOIN tbl_clientes transDelivery WITH(NOLOCK) ON vdd_cl_id = transDelivery.cl_id ";	
		
		$filtros['f_ini'] = $filtros['f_fin'] = getFechaServer('Y-m-d');
		
		$sql.= $this->filtrosViajes($filtros);
		if($tipo == 'partidas'){
 			$sql.= " AND (
					(vd_ini_real IS NOT NULL AND (vd_fin_real IS NULL OR vd_fin_real >= '".date('Y-m-d H:i:s',strtotime('-30 minutes',strtotime(getFechaServer('Y-m-d H:i'))))."')) 
					OR
				 	(vdd_ini_real IS NOT NULL AND (vdd_fin_real IS NULL OR vdd_fin_real >= '".date('Y-m-d H:i:s',strtotime('-30 minutes',strtotime(getFechaServer('Y-m-d H:i'))))."')) 
				)";
                    $sql.= " AND sh_rd_id != 76  ";
		}
		else{
			$sql.= " AND (
				((vd_ini_real IS NOT NULL AND vd_fin_real IS NULL) OR (vd_ini_real IS NULL AND vd_ini >= '".date('Y-m-d H:i:s',strtotime('-3 day',strtotime(getFechaServer('Y-m-d H:i'))))."'))	
				 OR 
				((vdd_ini_real IS NOT NULL AND vdd_fin_real IS NULL) OR (vdd_ini_real IS NULL AND vdd_ini >= '".date('Y-m-d H:i:s',strtotime('-3 day',strtotime(getFechaServer('Y-m-d H:i'))))."'))
			)";
                    
                     //-- Filtrar solo por planta o Cliente --//
                    if($filtros['tipo_referencia'] == 'cliente'){
                        //--Solamente se visualiza arribos si haya egresado del origen
                        $sql.= "AND vi_id IN (SELECT vd_vi_id FROM tbl_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = vi.vi_id AND vd_orden = 0 AND vd_fin_real IS NOT NULL)";
                    }
                    //-- --//
                }
                
                $sql.= " AND mo_id IS NOT NULL AND re_id != 6464  ";//AND sh_rd_id != 76 -->Se quito esta validación para arribos a clientes.
		
		$strSQL = " SELECT * FROM (".$sql.") AS result WHERE rc_latitud != 0 AND rc_longitud != 0 ";
		if($tipo == 'partidas'){
			$strSQL.= " ORDER BY fecha_fin ASC, fecha_fin_real DESC, vi_codigo "; 
		}
		else{
			$strSQL.= " ORDER BY fecha_ini ASC, fecha_ini_real DESC, vi_codigo "; 
		}
                
                $objRes=$this->objSQL->dbQuery($strSQL);
		$res=$this->objSQL->dbGetAllRows($objRes,3);
		
		return $res;
	}
	/***  ***/

	function resetFechaIngreso($vd_id){
		$esOrigen = true;
		##-- TXT Log --##
		$sql = " SELECT vdd_delivery FROM tbl_viajes_destinos_delivery WHERE vdd_vd_id = {$vd_id} ";
		$objRes = $this->objSQL->dbQuery($sql);
		$res=$this->objSQL->dbGetAllRows($objRes,3);
		if($res){
			$esOrigen = false;
			$entregas = array();
			foreach($res as $item){
				array_push($entregas, $item['vdd_delivery']);
			}
			$msg_log = ' Reseteo Fecha de Ingreso para la/s Entrega/s: '.implode(',',$entregas);
		}
		else{
			$sql = " SELECT re_nombre FROM tbl_viajes_destinos WITH(NOLOCK) ";
			$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
			$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$vd_id;
			$res = $this->objSQL->dbQuery($sql);
			$arr_ref = $this->objSQL->dbGetRow($res,0,3);
			$msg_log = ' Reseteo Fecha de Ingreso para la Referencia: '.$arr_ref['re_nombre'];
		}
		##-- --##	

		if($esOrigen){
			$sql= " UPDATE tbl_viajes_destinos SET vd_ini_real = NULL, vd_fin_real = NULL ";
			$sql.=" WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$vd_id;
		}
		else{
			$sql = "UPDATE tbl_viajes_destinos_delivery SET vdd_ini_real = NULL, vdd_fin_real = NULL WHERE vdd_vd_id =  {$vd_id}";
		}

		if($this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return true;	
		}
		return true;
	}
	
	function resetFechaEgreso($vd_id){
		$esOrigen = true;
		##-- TXT Log --##
		$sql = " SELECT vdd_delivery FROM tbl_viajes_destinos_delivery WHERE vdd_vd_id = {$vd_id} ";
		$objRes = $this->objSQL->dbQuery($sql);
		$res=$this->objSQL->dbGetAllRows($objRes,3);
		if($res){
			$esOrigen = false;
			$entregas = array();
			foreach($res as $item){
				array_push($entregas, $item['vdd_delivery']);
			}
			$msg_log = ' Reseteo Fecha de Egreso para la/s Entrega/s: '.implode(',',$entregas);
		}
		else{
			$sql = " SELECT re_nombre FROM tbl_viajes_destinos WITH(NOLOCK) ";
			$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
			$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$vd_id;
			$res = $this->objSQL->dbQuery($sql);
			$arr_ref = $this->objSQL->dbGetRow($res,0,3);
			$msg_log = ' Reseteo Fecha de Egreso para la Referencia: '.$arr_ref['re_nombre'];
		}
		##-- --##	

		if($esOrigen){
			$sql= " UPDATE tbl_viajes_destinos SET vd_fin_real = NULL ";
			$sql.=" WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$vd_id;
		}
		else{
			$sql = "UPDATE tbl_viajes_destinos_delivery SET vdd_fin_real = NULL WHERE vdd_vd_id =  {$vd_id}";
		}

		if($this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return true;	
		}
		return true;
	}
}
