<?php

class Intermill {

    protected $objSQL;
    protected $link;
    protected $provedores;

    /*
      4475	NICOLITA
      4463	PAPELERA SAN ANDRES DE GILES
      4460	PLASTAR
      4467	FREUDENBERG
      4465	IPESA
     */
     
	protected $result = null;
	
	private $rows = array(); 

    function __construct($objSQLServer){
        $this->objSQL = $objSQLServer;

		$this->proveedores[] = 4475;
        $this->proveedores[] = 4463;
        $this->proveedores[] = 4460;
        $this->proveedores[] = 4467;
        $this->proveedores[] = 4465;
		
		/*
		//PRIMA
		$this->proveedores[] = 133; // PLASTAR
        $this->proveedores[] = 135; // PAPELERA SAN ANDRES DE GILES
		$this->proveedores[] = 136; // TLP
		// $this->proveedores[] = 137; // ALQUIMAC
		// $this->proveedores[] = 139; // EXCELLENCE
		// $this->proveedores[] = 140; // NICOLITA
		// $this->proveedores[] = 140; // EXOLOGISTICA S.A.
		$this->proveedores[] = 142; // PGI
		$this->proveedores[] = 144; // BOSTIK
        $this->proveedores[] = 145; // FREUDENBERG
		// $this->proveedores[] = 146; // BILAGUN
		// $this->proveedores[] = 147; // GARGANO
		$this->proveedores[] = 149; // IPESA
		
		
		$this->referencias['proveedor'] = array(133,135,136,137,139,140,141,142,144,145,146,147,149,152);
		$this->referencias['planta'] = array(134,143);
		$this->referencias['deposito'] = array(148,138);
		$this->referencias['deposito_2'] = array(138/*,143);
		/**/
		
		//PRIMA
		$this->proveedores[] = 1807; // PLASTAR
        $this->proveedores[] = 1809; // PAPELERA SAN ANDRES DE GILES
		$this->proveedores[] = 1812; // TLP
		// $this->proveedores[] = 1818; // ALQUIMAC
		// $this->proveedores[] = 1815; // EXCELLENCE
		// $this->proveedores[] = 1817; // NICOLITA
		// $this->proveedores[] = 5754; // EXOLOGISTICA S.A.
		$this->proveedores[] = 1808; // PGI
		$this->proveedores[] = 1810; // BOSTIK
        $this->proveedores[] = 1813; // FREUDENBERG
		// $this->proveedores[] = 1814; // BILAGUN
		// $this->proveedores[] = 1816; // GARGANO
		$this->proveedores[] = 1811; // IPESA
		
		$this->referencias['proveedor'] = array(1807,1809,1812,1818,1815,1817,5754,1808,1810,1813,1814,1816,1811,29657);
		$this->referencias['planta'] = array(2051,3484);
		$this->referencias['deposito'] = array(1822,3487);
		//$this->referencias['deposito_2'] = array(3487/*,3484*/);
		$this->referencias['cliente'] = array();
		
    }
	
	function iplan() {
    	$this->kccSQL = new SqlServer();
		$this->kccSQL->rel = '';
		$this->kccSQL->dirConfig = 'kccinter_cliente';
		$this->kccSQL->dbConnect();
    }
	
	
	function obtenerDatos($inicio = '', $fin = ''){
		$data1 = $data2 = array();
		$data1 = $this->_obtenerDatos($inicio, $fin, false);
		$data2 = $this->_obtenerDatos($inicio, $fin, true);
		
		if ($data1 || $data2){
			$data = array_merge((array) $data1, (array) $data2);
			
			foreach ($data as $val){
				$sortVehiculo[] = $val['Vehiculo'];
			}

			foreach ($data as $val){
				$sortIngreso[] = $val['Ingreso'];
			}
			
			array_multisort($sortVehiculo, SORT_ASC, $sortIngreso, SORT_ASC, $data);
			
			return $data;
		}
		
		return array();
	}

	function getRow($iplan = true) {
		static $i = 0;
		static $total = null;
		
		if ($iplan === true) {
			return $this->kccSQL->dbGetRow($this->result);
		} else {
			if ($total === null) {
				$total = count($this->rows); 
			}

			if (isset($this->rows[$i])) {
				$row = $this->rows[$i];
				$i++;
				return $row;
			} else {
				return false;
			}
		}
	}

    function _obtenerDatos($inicio = '', $fin = '', $iplan = false) {
		$dateformat = "d/m H:i";
        $date = substr($inicio, 8,2) . "/" . substr($inicio, 5, 2) . "/" . substr($inicio, 0,4);
		$dateFin = substr($fin, 8,2) . "/" . substr($fin, 5, 2) . "/" . substr($fin, 0,4);
		
		if ($iplan == true) {
			$this->iplan();
			// La consulta se ordena por vehiculo y por ingreso.
			$strSQL = "EXEC sp_obtenerInformeIntermillOrdenes '".$inicio."', '".$fin."', NULL";
			$this->result = $this->kccSQL->dbQuery($strSQL);	
			//$this->kccSQL->dbDisconnect();
		}else {
			// PRIMA
			$strSQL = " SELECT isnull(NumeroOrden,0) as  NumeroOrden, Vehiculo,IdReferencia,NombreCorto, Nombre,FechaIngreso, FechaEgreso, FechaIngresoProgramado, FechaEgresoProgramado, Ingreso, Egreso, TiempoEstadia, noPoseeIngreso , nombrecliente, Conductor 
				FROM (SELECT null as NumeroOrden, v.mo_matricula as Vehiculo ,r.re_id as IdReferencia,re_nombre as NombreCorto, r.re_nombre as Nombre 
					,dbo.unix_timestamp(e1.ev_ingreso)+10800 as FechaIngreso,dbo.unix_timestamp(e1.ev_egreso)+10800 as FechaEgreso
					,dbo.unix_timestamp(e1.ev_ingreso)+10800 as FechaIngresoProgramado,dbo.unix_timestamp(e1.ev_egreso)+10800 as FechaEgresoProgramado
					,dbo.unix_timestamp(e1.ev_ingreso)+10800 as Ingreso, dbo.unix_timestamp(e1.ev_egreso)+10800 as Egreso
					,datediff(minute,e1.ev_ingreso,e1.ev_egreso) AS TiempoEstadia,0 as noPoseeIngreso , clientes.cl_razonSocial as nombrecliente, RTRIM(LTRIM(co_nombre+' '+co_apellido)) as Conductor
					FROM kccinter_eventos_doble e1
					INNER JOIN tbl_moviles v ON v.mo_id = e1.ev_ve_id
					INNER JOIN tbl_referencias r ON r.re_id = e1.ev_re_id
					INNER JOIN tbl_clientes clientes ON v.mo_id_cliente_facturar = clientes.cl_id
					LEFT JOIN tbl_conductores on co_id = v.mo_co_id_primario
					WHERE e1.ev_ingreso BETWEEN '".date('Y-m-d H:i:s', strtotime($inicio))."' AND '".date('Y-m-d H:i:s', strtotime($fin))."' ) a
				ORDER by Vehiculo,Ingreso ASC "; 
			$objRes = $this->objSQL->dbQuery($strSQL);	
			$this->rows = $this->objSQL->dbGetAllRows($objRes, 3);
			
		}
		
        $i = 0;
        $arrReportes2['sEcho'] = 1;
		$arrReportes2['iTotalRecords'] = 0;
		$arrReportes2['iTotalDisplayRecords'] = 0;
		
		while ($row = $this->getRow($iplan)) {
			$row['NumeroOrden'] = intval($row['NumeroOrden']);
            $row['FechaIngreso'] = ($row['FechaIngreso']) ? date($dateformat, $row['FechaIngreso']) : $row['FechaIngreso'];
            $row['FechaIngresoProgramado'] = ($row['FechaIngresoProgramado']) ? date($dateformat, $row['FechaIngresoProgramado']) : $row['FechaIngresoProgramado'];
            
            $row['noPoseeEgreso'] = 0;
            if (empty($row['FechaEgreso'])) {
				$row['noPoseeEgreso'] = 1;
				$row['FechaEgreso'] = ($row['FechaEgresoProgramado']) ? date($dateformat, $row['FechaEgresoProgramado']) : $row['FechaEgresoProgramado'];
			} else {
				$row['FechaEgreso'] = ($row['FechaEgreso']) ? date($dateformat, $row['FechaEgreso']) : $row['FechaEgreso'];
			}
			
			// Si no posee ingreso real, tampoco tiene egreso real.
			if ($row['noPoseeIngreso'] == 1) {
				$row['noPoseeEgreso'] = 1;
			}
			
            $row['FechaEgresoProgramado'] = ($row['FechaEgresoProgramado']) ? date($dateformat, $row['FechaEgresoProgramado']) : $row['FechaEgresoProgramado'];
            
            // Si se busca un solo dia se puede remover la parte de la fecha.
            // Si se busca en un rango de fechas no aplica.
            if ($date == $dateFin) {
				if (substr($row['FechaIngreso'], 0, 10) == $date) {
					$row['FechaIngreso'] = substr($row['FechaIngreso'], 11);
				}
				if (substr($row['FechaEgreso'], 0, 10) == $date) {
					$row['FechaEgreso'] = substr($row['FechaEgreso'], 11);
				}
				if (substr($row['FechaIngresoProgramado'], 0, 10) == $date) {
					$row['FechaIngresoProgramado'] = substr($row['FechaIngresoProgramado'], 11);
				}
				if (substr($row['FechaEgresoProgramado'], 0, 10) == $date) {
					$row['FechaEgresoProgramado'] = substr($row['FechaEgresoProgramado'], 11);
				}
			}
            $arrReportes[$i] = $row;
            $i++;
        }
        if ($i == 0) {
            $salida = false;
        } else {
            $salida = $arrReportes;
        }
		
		return $salida;
    }

    function obtenerTableroIplan($inicio = '', $fin = '', $sp = 'pa_reporteArribos', $arribo = 0){
        $this->iplan();
        $strSQL = "EXEC ".$sp." '".$inicio."', '".$fin."', NULL";
		$objProc = $this->kccSQL->dbQuery($strSQL);	
		$arrRows = $this->kccSQL->dbGetAllRows($objProc,3);
		
		$i = 0;
		$moviles = array();
        foreach($arrRows as $row){
			if ($arribo == 1) {
                /*
                  1)	Se detecto el ingreso unicamente. C3
                  2)	Lo ultimo que se detecto fue el egreso de un proveedor, implica mostrar el estimado a San Luis 2. C4+C5
                  3)	Se muestran todos los cruces para los cuales no se detecto el ingreso. C5
                 */
                $continuar = false;
                $row['FechaEstimada'] = '';
                $row['FechaProgramada'] = '';
				$row['Procedencia'] = '';
				
				// El vehiculo estaba en un proveedor y tiene fecha de egreso... 
                $condicion1 = in_array($row['IdReferencia'], $this->proveedores) && $row['FechaEgreso']; // si la referencia es un proveedor y tiene fecha de egreso.				
				$condicion2 = (strlen($row['FechaEgreso']) > 3); // si tiene fecha de egreso.
				if (!in_array($row['IdVehiculo'],$moviles) && ($condicion1 || !$condicion2)) { // si el vehiculo no esta en el listado y 
				$continuar = true;
				$moviles[] = $row['IdVehiculo'];
				}
                if ($continuar) {
					if ($condicion1) { // Si ya egresó de la referencia y es un proveedor de materia prima
						$continuar = true; //2)
						//simulo el registro con estimado a SAN LUIS 2
						$distancia = distancia($row['sh_lat'], $row['sh_longt'], -33.2765386870725, -66.3130044937134)-50;
						$horas = $distancia/50;
						$row['FechaEstimada'] 	= date("d/m H:i", time() + round(($horas+1)*3600));
						$row['FechaProgramada'] = date("d/m H:i", $row["FechaEgresoTs"] + 16*3600);// .' (E:'.date("H:i",$row["FechaEgresoTs"]).')';
						$row['IdReferencia'] 	= 4337;
						$row['Procedencia']		= $row['NombreCorto'];
						$row['NombreCorto'] 	= 'KC SAN LUIS 2 ('.(round($distancia)+50).' kms)';
						$row['FechaIngreso'] 	= '';
						$row['ev_fecha'] 		= '';
					}
					if ($row['FechaIngreso']) { // si está en la zona y todavía no egresó independientemente si es proveedor, planta o depósito.
						$row['FechaIngresoTs'] = $row['FechaIngreso'];
						$row['FechaIngreso'] = date("d/m H:i",$row["FechaIngreso"]);
					}
                    $arrReportes[$i] = $row;
                    $i++;
                } else {
					// Salio de la referencia pero es o Planta o un depósito.
				}
            } else if ($arribo == 0){
				$continuar = false;
				if (!in_array($row['IdVehiculo'],$moviles)) {
					$continuar = true;
					$moviles[] = $row['IdVehiculo'];
				}
				if ($continuar) {
					$row['FechaEstimada'] = $row['Ingreso'];
					$row['FechaIngresoTs'] = $row['FechaIngreso'];
					$row['FechaIngreso'] = date("d/m H:i",$row["FechaIngreso"]);
					if ($row['FechaEgreso']) {
						$row['FechaEgreso'] = date("d/m H:i",$row["FechaEgreso"]);
					} else {
						$row['FechaEgreso'] = "-";
					}
                    $arrReportes[$i] = $row;
                    $i++;
                }
            }
        }
        if ($i == 0) {
            $salida = false;
        } else {
            $salida = $arrReportes;
        }
		
		$this->kccSQL->dbDisconnect();
        return $salida;
    }
    
    
	function obtenerTableroIntermill($inicio, $fin, $accion, $arribo){
    	
		if($accion == 'arribos'){
			$strSQL = " SELECT DISTINCT v.mo_matricula as Vehiculo ,v.mo_id as IdVehiculo, r.re_id as IdReferencia,re_nombre as NombreCorto
					,r.re_nombre as Nombre ,dbo.unix_timestamp(e1.ev_ingreso)+10800 as FechaIngreso ,e1.ev_ingreso as Ingreso,e1.ev_egreso as FechaEgreso
					,dbo.unix_timestamp(e1.ev_egreso)+10800 as FechaEgresoTs, sh.sh_latitud,sh.sh_longitud, rc.rc_latitud, rc.rc_longitud
					, cl.cl_razonSocial as RazonSocial, sh.sh_fechaRecepcion as UltimoReporte, RTRIM(LTRIM(co_nombre+' '+co_apellido)) as Conductor";//v.mo_otros as Conductor
			$strSQL.= " FROM kccinter_eventos_doble e1 ";
			$strSQL.= " INNER JOIN tbl_moviles v ON v.mo_id = e1.ev_ve_id ";
			$strSQL.= " INNER JOIN tbl_referencias r ON r.re_id = e1.ev_re_id ";
			$strSQL.= " INNER JOIN tbl_referencias_coordenadas rc ON rc.rc_re_id = r.re_id ";
			$strSQL.= " LEFT JOIN tbl_unidad un ON un.un_mo_id = v.mo_id ";
			$strSQL.= " INNER JOIN tbl_sys_heart sh ON sh.sh_un_id = un.un_id ";
			$strSQL.= " INNER JOIN tbl_clientes cl ON cl.cl_id = v.mo_id_cliente_facturar ";
			$strSQL.= " LEFT JOIN tbl_conductores on co_id = v.mo_co_id_primario ";
			$strSQL.= " WHERE (e1.ev_ingreso BETWEEN CURRENT_TIMESTAMP - 4 AND CURRENT_TIMESTAMP + 0.25
						OR e1.ev_egreso BETWEEN CURRENT_TIMESTAMP - 4 AND CURRENT_TIMESTAMP + 0.25)
						AND (DATEDIFF(minute,e1.ev_ingreso,e1.ev_egreso) > 20 OR e1.ev_egreso is null) ";
			$strSQL.= " ORDER by Vehiculo,Ingreso DESC ";
		}
	  	elseif($accion == 'partidas'){
			$strSQL = " SELECT DISTINCT v.mo_matricula as Vehiculo ,v.mo_id as IdVehiculo, r.re_id as IdReferencia,re_nombre as NombreCorto
					, r.re_nombre as Nombre ,dbo.unix_timestamp(e1.ev_ingreso)+10800 as FechaIngreso ,dbo.unix_timestamp(e1.ev_egreso)+10800 as FechaEgreso
					,dbo.unix_timestamp(e1.ev_egreso)+10800 as FechaEgresoTs ,e1.ev_ingreso as Ingreso,e1.ev_egreso as Egreso
					, cl.cl_razonSocial as RazonSocial, RTRIM(LTRIM(co_nombre+' '+co_apellido)) as Conductor ";
			$strSQL.= " FROM kccinter_eventos_doble e1 ";
			$strSQL.= " INNER JOIN tbl_moviles v ON v.mo_id = e1.ev_ve_id ";
			$strSQL.= " INNER JOIN tbl_referencias r ON r.re_id = e1.ev_re_id ";
			$strSQL.= " INNER JOIN tbl_clientes cl ON cl.cl_id = v.mo_id_cliente_facturar ";
			$strSQL.= " LEFT JOIN tbl_conductores on co_id = v.mo_co_id_primario ";
			$strSQL.= " WHERE e1.ev_ingreso BETWEEN CURRENT_TIMESTAMP - 4 AND CURRENT_TIMESTAMP + 0.25
						AND (DATEDIFF(minute,e1.ev_ingreso,e1.ev_egreso) > 20 OR e1.ev_egreso is null) ";
			$strSQL.= " ORDER by Vehiculo,Ingreso DESC ";
		}
		
		$objRes = $this->objSQL->dbQuery($strSQL);
		$res = $this->objSQL->dbGetAllRows($objRes, 3);
        $i = -1;
		$moviles = array();
		for ($i = 0;$i < count($res) && $res;$i++){
			$row = $res[$i];
			if ($arribo == 1) {
				/*
                  1)	Se detecto el ingreso unicamente. C3
                  2)	Lo ultimo que se detecto fue el egreso de un proveedor, implica mostrar el estimado a San Luis 2. C4+C5
                  3)	Se muestran todos los cruces para los cuales no se detecto el ingreso. C5
                 */
                $continuar = false;
                $row['FechaEstimada'] = '';
                $row['FechaProgramada'] = '';
				$row['Procedencia'] = '';
				
				// El vehiculo estaba en un proveedor y tiene fecha de egreso... 
                $proveedorConEgreso = in_array($row['IdReferencia'], $this->proveedores) && $row['FechaEgreso']; // si la referencia es un proveedor y tiene fecha de egreso.				
				$tieneEgreso = (strlen($row['FechaEgreso']) > 3); // si tiene fecha de egreso.
				
				if (!in_array($row['IdVehiculo'],$moviles)){
					$moviles[] = $row['IdVehiculo'];
					if ($proveedorConEgreso == true || $tieneEgreso == false){ 
						if ($proveedorConEgreso){
							// Si ya egresó de la referencia y es un proveedor de materia prima
							$continuar = true; //2)
							//simulo el registro con estimado a SAN LUIS 2
							$distancia = distancia($row['sh_latitud'], $row['sh_longitud'], -33.2765386870725, -66.3130044937134)-50;
							$horas = $distancia/50;
							$row['FechaEstimada'] 	= date("d/m H:i", time() + round(($horas+1)*3600));
							$row['FechaProgramada'] = date("d/m H:i", $row["FechaEgresoTs"] + 16*3600);// .' (E:'.date("H:i",$row["FechaEgresoTs"]).')';
							$row['IdReferencia'] 	= 134;
							$row['Procedencia']		= $row['NombreCorto'];
							$row['NombreCorto'] 	= 'KC SAN LUIS 2 (a '.(round($distancia)+50).' kms)';
							$row['FechaIngreso'] 	= '';
							$row['ev_fecha'] 		= '';
						}

						if ($row['FechaIngreso']) {
							$row['FechaIngresoTs'] = $row['FechaIngreso'];
							$row['FechaIngreso'] = date("d/m H:i",$row["FechaIngreso"]);
						}
					
						// Si tiene egreso y no es proveedor no se muestra
						$arrReportes[$i] = $row;
					}
				}
            } 
			else if ($arribo == 0){
				$continuar = false;
				if (!in_array($row['IdVehiculo'],$moviles)) {
					$continuar = true;
					$moviles[] = $row['IdVehiculo'];
				}
				if ($continuar) {
					$row['FechaEstimada'] = $row['Ingreso'];
					$row['FechaIngresoTs'] = $row['FechaIngreso'];
					$row['FechaIngreso'] = date("d/m H:i",$row["FechaIngreso"]);
					if ($row['FechaEgreso']) {
						$row['FechaEgreso'] = date("d/m H:i",$row["FechaEgreso"]);
					} else {
						$row['FechaEgreso'] = "-";
					}
                    $arrReportes[$i] = $row;
                 }
            }
        }
        if ($i == -1) {
            $salida = false;
        } else {
            $salida = $arrReportes;
        }
        return $salida;
    }
	
	
	function getReferencias(){
		$sql = " SELECT * FROM( ";
		$sql.= " SELECT re_id, re_nombre 
				,CASE 
				WHEN re_id IN(".implode(',',$this->referencias['proveedor']).") THEN 'Proveedor'
				WHEN re_id IN(".implode(',',$this->referencias['planta']).") THEN 'Planta'
				WHEN re_id IN(".implode(',',$this->referencias['deposito']).") THEN 'Deposito'
				ELSE NULL END AS tipo ";
		$sql.= " FROM tbl_referencias ";
		$sql.= " WHERE re_borrado = 0 ";
		$sql.= " ) AS resp ";
		$sql.= " WHERE tipo IS NOT NULL ";
		$sql.= " ORDER BY tipo, re_nombre ";	
		$rs = $this->objSQL->dbQuery($sql);
		$res = $this->objSQL->dbGetAllRows($rs);
		
		return $res;
	}
	
	function getViajesPendienteIngreso($datos){
		$sql = " SELECT DISTINCT(vd_vi_id), mo_matricula ";
		$sql.= " FROM kccinter_viajes ";
		$sql.= " INNER JOIN kccinter_viajes_destinos ON vi_id = vd_vi_id ";
		$sql.= " INNER JOIN tbl_moviles ON mo_id = vi_mo_id ";
		$sql.= " WHERE vi_borrado = 0 AND vd_ini_real IS NULL AND vd_creado BETWEEN CURRENT_TIMESTAMP - 4 AND CURRENT_TIMESTAMP + 0.25";
		$sql.= " AND vd_re_id IN (".$datos['re_id'].")";
		
		$rs = $this->objSQL->dbQuery($sql);
		$res = $this->objSQL->dbGetAllRows($rs, 3);
		
		$matriculas = "";
		$coma = "";
		if($res){
			foreach($res as $item){
				$matriculas.= $coma.$item['mo_matricula']; 
				$coma = ",";
			}
			$cant = count($res);
		}
		$res = array('matriculas' => $matriculas, 'cant' => (int)$cant);
		return $res;
	}
	
	function getViajesEnTransito($ref_id_ini, $ref_id_fin){
		$sql.= " SELECT desde.vi_id, mo_matricula FROM ( ";
		$sql.= " SELECT vi_id, vi_mo_id FROM kccinter_viajes 
				INNER JOIN kccinter_viajes_destinos ON vi_id = vd_vi_id  
				WHERE vi_borrado = 0 
				AND vd_re_id = ".(int)$ref_id_ini." 
				AND vd_ini_real IS NOT NULL
				AND vd_fin_real IS NOT NULL AND vd_orden = 0
				AND vd_creado BETWEEN CURRENT_TIMESTAMP - 4 AND CURRENT_TIMESTAMP + 0.25 ";
		$sql.= " ) AS desde INNER JOIN ( ";
		$sql.= " SELECT vi_id FROM kccinter_viajes 
				INNER JOIN kccinter_viajes_destinos ON vi_id = vd_vi_id 
				WHERE vi_borrado = 0
				AND vd_re_id = ".(int)$ref_id_fin." 
				AND vd_ini_real IS NULL
				AND vd_fin_real IS NULL AND vd_orden = 1
				AND vd_creado BETWEEN CURRENT_TIMESTAMP - 4 AND CURRENT_TIMESTAMP + 0.25 ";
		$sql.= " ) AS hasta ON desde.vi_id = hasta.vi_id "; 
		$sql.= " INNER JOIN tbl_moviles ON mo_id = desde.vi_mo_id ";
		$rs = $this->objSQL->dbQuery($sql);
		$res = $this->objSQL->dbGetAllRows($rs);
		
		$matriculas = "";
		$coma = "";
		if($res){
			foreach($res as $item){
				$matriculas.= $coma.$item['mo_matricula']; 
				$coma = ",";
			}
			$cant = count($res);
		}
		$res = array('matriculas' => $matriculas, 'cant' => (int)$cant);
		return $res;
	}

}
