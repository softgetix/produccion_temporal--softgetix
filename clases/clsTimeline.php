<?php
class Timeline{
	function __construct($objSQLServer){
		$this->objSQL = $objSQLServer;
	}
	
	function getItinerarioViajes(){
		
		$idUsuario = (int)$_SESSION['idUsuario'];
		
		$strSQL = " DECLARE @tipoEmpresa INT; DECLARE @empresa INT; 
				SELECT @tipoEmpresa = cl_tipo_cliente, @empresa = cl_id 
				FROM tbl_clientes WITH(NOLOCK)
				INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id 
				WHERE cl_tipo = 2 AND us_id = ".(int)$idUsuario;
				
		$strSQL.= " SELECT TOP 20 vi_id ,re_id ,re_nombre ,rg_color, vi_codigo, vdd_delivery
					,(CASE WHEN vdd_id IS NULL THEN vd_ini ELSE vdd_ini END) AS vd_ini
					,(CASE WHEN vdd_id IS NULL THEN vd_fin ELSE vdd_fin END) AS vd_fin 
					,mo_matricula ,cl_razonSocial,co_nombre, co_apellido, co_telefono
				";
		$strSQL.= " FROM tbl_viajes WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_vi_id = vi_id ";
		$strSQL.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$strSQL.= " INNER JOIN tbl_referencias_grupos WITH(NOLOCK) ON re_rg_id = rg_id ";
		$strSQL.= " LEFT JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vdd_vd_id = vd_id ";
		//$strSQL.= " LEFT JOIN tbl_viajes_destinos_delivery_pedidos WITH(NOLOCK) ON vddp_vdd_id = vdd_id ";
		
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = (CASE WHEN vdd_id IS NULL THEN vi_mo_id ELSE vdd_mo_id END) ";
		$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = (CASE WHEN vdd_id IS NULL THEN vi_transportista ELSE vdd_cl_id END) ";
		$strSQL.= " LEFT JOIN tbl_conductores WITH(NOLOCK) ON co_id = (CASE WHEN vdd_id IS NULL THEN vi_co_id ELSE vdd_co_id END) ";
		
		$strSQL.= " WHERE vi_borrado = 0 AND vi_delivery = 1 AND vi_crossdocking = 0 ";
		$strSQL.= " AND mo_id IN (SELECT um_mo_id FROM tbl_usuarios_moviles WITH(NOLOCK) WHERE um_us_id = ".(int)$idUsuario.")"; 
		$strSQL.= " AND vi_dador = 
					CASE @tipoEmpresa
						WHEN  1 THEN @empresa  	/*DADOR*/
						WHEN  2 THEN vi_dador  	/*TRANSPORTISTA*/
						ELSE vi_dador 			/*LOCALIZART O AGENTE*/
					END
				AND cl_id = 
					CASE @tipoEmpresa
						WHEN  1 THEN cl_id 		/*DADOR*/
						WHEN  2 THEN @empresa	/*TRANSPORTISTA*/
						ELSE cl_id				/*LOCALIZART O AGENTE*/
					END ";
		$strSQL.= " AND (CONVERT(DATE,vd_ini,103) = CONVERT(DATE,GETDATE(),103) OR CONVERT(DATE,vd_fin,103) = CONVERT(DATE,GETDATE(),103)) ";			
		$strSQL.= " ORDER BY vi_id, vd_ini ASC, vd_orden ASC ";
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$result = $this->objSQL->dbGetAllRows($objRes);
		
		$arrViajes = array();
		$auxValidInique = array();
		if($result){
			$today = new DateTime(getFechaServer('Y-m-d'));
			foreach($result as $row){
				if(!in_array($row['vi_id'].'-'.$row['re_id'],$auxValidInique)){//-- Permite no procesar un único valor de delivery por cliente
					array_push($auxValidInique, $row['vi_id'].'-'.$row['re_id']);
				
					$vd_ini = ($row['vd_ini'] == true)?new DateTime($row['vd_ini']):$today;
					$vd_fin = ($row['vd_fin'] == true)?new DateTime($row['vd_fin']):NULL;
					
					$aux = array('diaDiff'=>0, 'diaFin'=>NULL, 'diaActual'=>$today->format('d'));
					
					if(!empty($vd_fin)){
						$aux['diaDiff'] = (abs(strtotime($vd_ini->format('Y-m-d')) - strtotime($vd_fin->format('Y-m-d')))/60/60/24);
					}
					else{
						$vd_fin = new DateTime($vd_ini->format('Y-m-d H:i:s'));
						$vd_fin->modify('+20 minutes');
					}
					
					$aux['diaFin'] = $vd_fin->format('d');
					
					$aux['horaIni'] = $vd_ini->format('H');
					$aux['minIni'] = $vd_ini->format('i');
					$aux['horaFin'] = $vd_fin->format('H');
					$aux['minFin'] = $vd_fin->format('i');
						
					$colInicio = 0;
					$colfin = 0;
					if($aux['diaDiff'] > 0 && $aux['diaFin'] != $aux['diaActual']){
						$colfin = 144;
					}
					elseif($aux['diaDiff'] > 0 && $aux['diaFin'] == $aux['diaActual']){
						$colInicio = 1;
					}
					
					if($colInicio == 0){
						$colInicio = ($aux['horaIni'] != '00')?((int)$aux['horaIni'] * 6):$colInicio;	
						switch($aux['minIni']){
							case '00':
								$colInicio = $colInicio + 1;
							break;
							case '10':
								$colInicio = $colInicio + 2;
							break;
							case '20':
								$colInicio = $colInicio + 3;
							break;
							case '30':
								$colInicio = $colInicio + 4;
							break;
							case '40':
								$colInicio = $colInicio + 5;
							break;
							case '50':
								$colInicio = $colInicio + 6 ;
							break;	
						}
					}
					
					if($colfin == 0){
						$colfin = $aux['horaFin'] * 6; //Pasaje hora a posición de columna.
						
						switch($aux['minFin']){
							case '10':
								$colfin = $colfin + 1;
							break;
							case '20':
								$colfin = $colfin + 2;
							break;
							case '30':
								$colfin = $colfin + 3;
							break;
							case '40':
								$colfin = $colfin + 4;
							break;
							case '50':
								$colfin = $colfin + 5;
							break;
						}
					}
					else{
						$colfin = 144;//Tiene diferencia de un dia
					}
					
					$auxArray = array('color'=>$row['rg_color'],'ti'=>$colInicio,'tf'=>$colfin,'pto_visitar'=>$row['re_nombre']);
					if(!isset($arrViajes[$row['vi_id']])){
						$arrViajes[$row['vi_id']] = array(
							'destinos'=> array()
							, 'info'=> array(
								'viajeNombre'=>$row['vi_codigo']
								,'matricula'=>$row['mo_matricula']
								,'transportista'=>$row['cl_razonSocial']
								,'conductor'=>trim($row['co_nombre'].' '.$row['co_apellido'])
								,'conductortel'=>$row['co_telefono']
								,'hs_inicio'=>$vd_ini->format('d-m-Y H:i')
								,'ref_inicio'=>$row['re_nombre']
								,'hs_fin'=>NULL
								,'ref_fin'=>NULL
								,'horasViaje'=>NULL
								,'color1' => '#FF8000'
								,'color2' => '#BDBDBD'
								,'delivery' => array()
							)
						);
					}	
									
					array_push($arrViajes[$row['vi_id']]['destinos'],$auxArray);
					//- -//
					$arrViajes[$row['vi_id']]['info']['ref_fin'] = $row['re_nombre'];
					$arrViajes[$row['vi_id']]['info']['hs_fin'] = $vd_ini->format('d-m-Y H:i');
					$arrViajes[$row['vi_id']]['info']['horasViaje'] = round(abs(strtotime($arrViajes[$row['vi_id']]['info']['hs_fin']) - strtotime($arrViajes[$row['vi_id']]['info']['hs_inicio']))/3600);
					//- -//
					
					if(!empty($row['vdd_delivery'])){
						array_push($arrViajes[$row['vi_id']]['info']['delivery'],$row['vdd_delivery']);	
					}
					
					
				}
			}
		}
		return $arrViajes;
	}
	
	function getMovilPosicion($idViaje){
	
		$viajeFinalizado = 0;
		$deltaTiempo = 0;
	
		// obtengo la hora de ingreso real al origen.
		$strSQL = " SELECT (CASE WHEN vdd_id IS NULL THEN vd_ini ELSE vdd_ini END) AS vd_ini
					,(CASE WHEN vdd_id IS NULL THEN vd_ini_real ELSE vdd_ini_real END) AS vd_ini_real ";
		$strSQL.= " FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vdd_vd_id = vd_id ";
		$strSQL.= " WHERE vd_orden = 0 and vd_vi_id = ".(int)$idViaje;
		$objRes = $this->objSQL->dbQuery($strSQL);
		$result = $this->objSQL->dbGetRow($objRes,0,3);
		if($result['vd_ini_real'] != ''){ // pudo haber iniciado a partir del 2do 
			$horainicio = $result['vd_ini_real'];}
		else{
			$horainicio = $result['vd_ini'];
		}	
		$aux['now'] = NULL; // colocamos por defecto la hora actual en blanco
		
	
		// obtengo el destino final: latitud , longitud el nro de orden de visita y el horario programado de arribo, para calcular tiempo estiamdo de finalizacion del vaije
		$strSQL = " SELECT TOP 1 re_nombre,rc_latitud,rc_longitud,vd_orden
				,(CASE WHEN vdd_id IS NULL THEN vd_ini ELSE vdd_ini END) AS vd_ini
 				,(CASE WHEN vdd_id IS NULL THEN vd_fin_real ELSE vdd_fin_real END) AS vd_fin_real ";
		$strSQL.=" FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$strSQL.=" INNER JOIN tbl_referencias WITH(NOLOCK) ON vd_re_id = re_id ";
		$strSQL.=" INNER JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON rc_re_id = re_id ";
		$strSQL.=" LEFT JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vdd_vd_id = vd_id ";
		$strSQL.=" WHERE vd_vi_id = ".(int)$idViaje." ORDER BY vd_orden DESC";
		$objRes = $this->objSQL->dbQuery($strSQL);
		$result = $this->objSQL->dbGetRow($objRes,0,3);
		$latitudDestino  = $result['rc_latitud']; 
		$longitudDestino = $result['rc_longitud'];
		$ultimoPto['orden'] = $result['vd_orden'];
		$ultimoPto['fin_real'] = $result['vd_fin_real'];
		$finProgramado = $result['vd_ini']; // el fin programado del movimiento es cuando se llega al destino final

		$strSQL = ' SELECT vi_id, vd_id, vd_orden, un_id, vi_mo_id, vd_re_id, re_nombre, sh_latitud, sh_longitud, re_id, mo_id_cliente_facturar, mo_id_tipo_movil
				,(CASE WHEN vdd_id IS NULL THEN vd_ini ELSE vdd_ini END) AS vd_ini
				,(CASE WHEN vdd_id IS NULL THEN vd_ini_real ELSE vdd_ini_real END) AS vd_ini_real
				,(CASE WHEN vdd_id IS NULL THEN vd_fin ELSE vdd_fin END) AS vd_fin
				,(CASE WHEN vdd_id IS NULL THEN vd_fin_real ELSE vdd_fin_real END) AS vd_fin_real 
				';
		$strSQL.=' FROM tbl_viajes_destinos WITH(NOLOCK) ';
		$strSQL.=' INNER JOIN tbl_viajes WITH(NOLOCK) ON vd_vi_id = vi_id ';
		$strSQL.=' INNER JOIN tbl_unidad WITH(NOLOCK) ON vi_mo_id = un_mo_id ';
		$strSQL.=' INNER JOIN tbl_moviles WITH(NOLOCK) ON un_mo_id = mo_id ';
		$strSQL.=' INNER JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ';
		$strSQL.=' INNER JOIN tbl_referencias WITH(NOLOCK) ON vd_re_id = re_id ';
		$strSQL.= " LEFT JOIN tbl_viajes_destinos_delivery WITH(NOLOCK) ON vdd_vd_id = vd_id ";
		$strSQL.=' WHERE vi_borrado = 0 AND vi_id = '.(int)$idViaje.' ';
		$strSQL.=' ORDER BY vi_id, vi_mo_id, vd_orden ASC ';
		$objRes = $this->objSQL->dbQuery($strSQL);
		$result = $this->objSQL->dbGetAllRows($objRes,3);
	
		$return = array();
		if($result){
			$latitudActual = $result['sh_latitud'];
			$longitudActual = $result['sh_longitud'];
			// calculo la distancia desde la posicion actual del movil al ultimo punto
			$distanciaDestino = distancia($latitudDestino,$longitudDestino,$latitudActual,$longitudActual);
			// tomo velocidad promedio y calculo tiempo restante al punto final
			$tiempoDestino = ($distanciaDestino / 70);
			// tiempo restante + hora actual es la hora de llegada etimada
			//$FechaEstimadaLlegada = date ( 'd-m H:i', ( time() + $tiempoDestino * 3600));
		
			
			$colInicio = 1;
			$today = new DateTime(getFechaServer('Y-m-d H:i'));
			foreach($result as $row){
				$aux = array();
				$aux['label'] = 'Vehiculo en Viaje';
				$aux['vi_id'] = $row['vi_id'];
				
				$vd_ini = ($row['vd_ini'] == true)?new DateTime($row['vd_ini']):$today;
				$vd_fin = ($row['vd_fin'] == true)?new DateTime($row['vd_fin']):NULL;
				$vd_ini_real = ($row['vd_ini_real'] == true)?new DateTime($row['vd_ini_real']):NULL;
				$vd_fin_real = ($row['vd_fin_real'] == true)?new DateTime($row['vd_fin_real']):NULL;
				
				$aux['iniReal'] = !empty($vd_ini_real)?$vd_ini_real->format('d-m-Y H:i'):NULL; 
				$aux['finReal'] = !empty($vd_fin_real)?$vd_fin_real->format('d-m-Y H:i'):NULL; 
				if(!empty($vd_ini)){
					$aux['horaIni'] = $vd_ini->format('H');
					$aux['minIni'] = $vd_ini->format('i');
					$aux['iniTeorico'] = (abs(strtotime($today->format('Y-m-d')) - strtotime($vd_ini->format('Y-m-d')))/60/60/24);
				}
				if(!empty($vd_fin)){
					$aux['horaFin'] = $vd_fin->format('H');
					$aux['minFin'] = $vd_fin->format('i');
					$aux['finTeorico'] = (abs(strtotime($today->format('Y-m-d')) - strtotime($vd_fin->format('Y-m-d')))/60/60/24);;
				}
				
				if($aux['iniTeorico'] == 1 || $aux['finTeorico'] == 1){ // si el ingreso o egreso prog es mañana dejo ingreso y egreso programado para hoy 2350
					$aux['horaFin'] = 23;
					$aux['minFin'] = 50;
					$aux['horaIni'] = 23;
					$aux['minIni'] = 50;
				}
				
				if(!empty($aux['iniReal'])){
					if(!empty($aux['finReal'])){
						$aux['label'] = 'Egreso de '.$row['re_nombre'].' '.date('H:i',strtotime($aux['finReal'])).' '; 
						$aux['now'] = $aux['finReal']; // tomamos como hora actual  la hora del egreso del punto
						$deltaTiempo =  strtotime($aux['finReal']) - strtotime($row['vd_fin']);// calculo la diferencia entre la partida real y la teorica para ver si esta adelantado o atrasado
						$aux['ubicacion_txt'] = 'Egreso: '.$row['re_nombre'];
						$aux['ubicacion_hs'] = date('d-m H:i',strtotime($aux['finReal']));
						if ($ultimoPto['orden'] == $row['vd_orden']){
							$viajeFinalizado = 1;
						}
					
						// si es una referencia que finaliza teoricamente hoy o mañana
						if ($aux['finTeorico'] == 0 || $aux['finTeorico'] == 1){
							$colInicio = ($aux['horaFin'] != '00')?($aux['horaFin'] * 6):$colInicio;//	Determino posicion del movil en la linea de tiempo
						}
					
						switch($aux['minFin']){
							case '00':
								$colInicio = $colInicio + 1;
							break;
							case '10':
								$colInicio = $colInicio + 2;
							break;
							case '20':
								$colInicio = $colInicio + 3;
							break;
							case '30':
								$colInicio = $colInicio + 4;
							break;
							case '40':
								$colInicio = $colInicio + 5;
							break;
							case '50':
								$colInicio = $colInicio + 6;
							break;
						}
					}
				}
				else{
					// Entro pero no salio de la referencia
					$aux['label'] = 'Ingreso a '.$row['re_nombre'].' '.date('H:i',strtotime($aux['iniReal'])).' ';
					$aux['now'] = $aux['iniReal'];
					// calculo la diferencia entre el arribo real y el teorico para ver si esta adelantado o atrasado
					$deltaTiempo =  strtotime($aux['iniReal']) - strtotime($row['vd_ini']);
					$aux['ubicacion_txt'] = 'Ingreso:'.$row['re_nombre'];
					$aux['ubicacion_hs'] = date('H:i',strtotime($aux['iniReal']));
					
					// si es una refrencia que tiene ingreso teorico hoy
					if ($aux['iniTeorico'] == 0 || $aux['iniTeorico'] == 1){
						
						$colInicio = ($aux['horaIni'] != '00')?($aux['horaIni'] * 6):$colInicio;
						switch($aux['minIni']){
							case '00':
								$colInicio = $colInicio + 1;
							break;
							case '10':
								$colInicio = $colInicio + 2;
							break;
							case '20':
								$colInicio = $colInicio + 3;
							break;
							case '30':
								$colInicio = $colInicio + 4;
							break;
							case '40':
								$colInicio = $colInicio + 5;
							break;
							case '50':
								$colInicio = $colInicio + 6;
							break;	
						}
					}
				}
		
				$aux['imagen'] = ($row['mo_id_tipo_movil'] == 6)?'imagenes\iconos\markersRastreo\misc\package_32x32.png':'imagenes\iconos\markersRastreo\1\verde\2.png';
			
				$aux['hs_acumulada'] = '-';
				if($viajeFinalizado){
					$aux['hs_acumulada'] =  round((strtotime($ultimoPto['fin_real'])-strtotime($horainicio))/3600) ;// Calculo la fecha q estuvo en marcha si el fiaje finalizo
				}
				elseif(strtotime($aux['now']) != ''){
					$aux['hs_acumulada'] =  round((time() - strtotime($horainicio))/3600);
				}
			
				$aux['ubicacion_txt'] = ($aux['ubicacion_txt'] == '')?' ':$aux['ubicacion_txt'];
				$aux['ubicacion_hs'] = ($aux['ubicacion_hs'] == '')?' ':$aux['ubicacion_hs'];
				
				//calculo la fecha de llegada estimada a partir del horario de ingreso teorico +/- delta
				$FechaEstimadaLlegada = date('d-m H:i',strtotime($finProgramado) + $deltaTiempo);
				$aux['duracion_estimada'] = round ( (strtotime($finProgramado) + $deltaTiempo -  strtotime($horainicio))/3600 ) ; //
				
				//si el viaje finalizo no calculo estimado
				$FechaEstimadaLlegada = ($viajeFinalizado == 1)?NULL:$FechaEstimadaLlegada;
		
				// si  el viaje no comenzo lo identifico		
				if ($aux['ubicacion_txt'] == ' ' || $aux['ubicacion_txt'] == '-' || $aux['ubicacion_txt'] == ''){
					$aux['ubicacion_txt'] = '';
					$aux['duracion_estimada'] = 'Sin iniciar';
					$aux['hs_acumulada'] = 'Sin iniciar';
					$FechaEstimadaLlegada = '';
					$aux['label'] = 'Sin iniciar';
				}
				
				// determino el estado del viaje
				$aux['estado'] = '';
				if ($FechaEstimadaLlegada != '' ){
					$aux['estado'] = ($deltaTiempo > 0)?'Atrasado':'En tiempo';
				}
				
				$aux['hs_acumulada'] = ($aux['hs_acumulada'] < 0)?'!':$aux['hs_acumulada'];
				$aux['duracion_estimada'] = ($aux['duracion_estimada'] < 0)?'!':$aux['duracion_estimada'];
		 		
				$return[] = array(
					"viajeId" => $aux['vi_id'],
					"imagen" =>$aux['imagen'],
					"label" => $aux['label'],
					"pos" => $colInicio,
					"ubicacion" => $aux['ubicacion_txt'],
					"horaUbicacion" => $aux['ubicacion_hs'],
					"horaMarcha" => $aux['hs_acumulada'],
					"horaEstimada" => $aux['duracion_estimada'],
					"fechaEstimada" => $FechaEstimadaLlegada,
					"estadoViaje" => $aux['estado'],
					"vi_finalizado" => $viajeFinalizado
				);
			}
		}
		return $return;
	}
	
	
	
	
}