<?php
require_once 'clases/clsAbms.php';
class Referencia extends Abm{

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_referencias','re');
	}

	function obtenerReferencias($id = 0, $filtro = "", $filtroUsuarios = "") {
        $strSQL = " SELECT re.*,tr.tr_nombre ";
		$strSQL.= " FROM tbl_referencias re WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_tipo_referencia tr WITH(NOLOCK) ON (tr.tr_id = re.re_tr_id) ";
		$strSQL.= " WHERE re.re_borrado = 0 ";
		if($id){
			 $strSQL.= " AND re.re_id = ".(int)$id;
		}
		if(!empty($filtro)){
			$strSQL.= " AND (re.re_nombre like '%".$filtro."%') ";
		}
		
		if($filtroUsuarios){
			 $strSQL.= " AND (re.re_us_id IN (".$filtroUsuarios.")) ";
		}
		
		$objReferencias = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias);
		return $objRow;
    }
	
    function eliminarCoordenadas($id) {
        if ($id) {
            $strSQL = " DELETE tbl_referencias_coordenadas WHERE rc_re_id =".(int)$id;
            if ($this->objSQL->dbQuery($strSQL)){
                return true;
			}
        }
        return false;
    }

    function obtenerCoordenadas($id) {
		$strSQL = "  SELECT re_nombre, re_numboca, rc_latitud, rc_longitud, rc_re_id, rc_id FROM tbl_referencias "; 
		$strSQL.= " INNER JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON re_id = rc_re_id ";
		$strSQL.= " WHERE re_id = ".(int)$id;
	    $objReferencias = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias);
		return $objRow;
    }

    function insertarCoordenadas($campos = "", $valorCampos = "") {
        if ($campos && $valorCampos) {
           return $this->insertarRegistro($campos, $valorCampos, NULL, 'tbl_referencias_coordenadas');
		}
        return false;
    }
	
	function getReferencia($id) {
       	$strSQL = " SELECT * FROM tbl_referencias WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON re_id = rc_re_id ";
		$strSQL.= " WHERE re_id = ".(int)$id;
		$objReferencias = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias);
		return $objRow;
    }

    function obtenerReferenciasEmpresa($idEmpresa = 0, $tipoAlerta = 0, $filtro = null) {

       $sql = " SELECT tr.tr_nombre,re.re_us_id, re.re_id,re.re_nombre,re.re_descripcion,re.re_radioIngreso,re.re_tr_id, re.re_rg_id, re.re_numboca ";
	   $sql.= " FROM tbl_referencias re WITH(NOLOCK) ";
	   $sql.= " INNER JOIN tbl_usuarios us WITH(NOLOCK) ON (us.us_id = re.re_us_id) ";
	   $sql.= " INNER JOIN tbl_tipo_referencia tr WITH(NOLOCK) ON (tr.tr_id = re.re_tr_id) ";
	   $sql.= " LEFT JOIN tbl_referencias_grupos rg WITH(NOLOCK) ON (rg.rg_id=re.re_rg_id) ";
	   $sql.= " WHERE re.re_borrado = 0 ";
	   //$sql.= " AND re.re_inteligente = 0 ";
	   $sql.= " AND ((re.re_validar_usuario IS NULL AND re.re_inteligente = 0) OR (re.re_inteligente = 1 AND re.re_validar_usuario = 1)) ";
	   $sql.= " AND (re.re_panico IS NULL OR re.re_panico = 0) ";
	   if(!empty($filtro)){
	   		$sql.= " AND re.re_nombre like '%".$filtro."%' ";
	   }
	   
	   if($tipoAlerta === 'c'){
			$sql.= " AND re.re_tr_id = 1"; //acotar a referencias del tipo circular
	   }
	   
	   $sql.= " AND us.us_cl_id = ".(int)$idEmpresa;
	   $sql.= " ORDER BY re_nombre ";
	   
	    $objReferencias = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias);
		return $objRow;
    }

    function obtenerReferenciasPorEmpresa3($idEmpresa = 0, $filtro = '') {
      	global $lang;
		$sql = " SELECT DISTINCT(re.re_id),rc_id
			,re.re_id as id
			,CASE WHEN (re.re_inteligente = 1 AND re.re_validar_usuario = 0) THEN '".($lang->system->punto_interes?$lang->system->punto_interes->__toString():'punto_interes')."' ELSE re.re_nombre END AS re_nombre
			,re.re_descripcion
			,re.re_radioIngreso as radio
			,re.re_tr_id as tipo, re.re_rg_id, rg.rg_nombre as grupo, rg.rg_imagen
			,rc.rc_latitud as lat,rc.rc_longitud as lng
			,tv.tv_color as color ,tr.tr_nombre";
		$sql.= " FROM tbl_referencias re WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias_coordenadas rc WITH(NOLOCK) ON (re.re_id = rc.rc_re_id) ";
		$sql.= " INNER JOIN tbl_usuarios us WITH(NOLOCK) ON (us.us_id = re.re_us_id) ";
		$sql.= " INNER JOIN tbl_tipo_referencia tr WITH(NOLOCK) ON (tr.tr_id = re.re_tr_id) ";
		$sql.= " LEFT JOIN tbl_referencias_grupos rg WITH(NOLOCK) ON (rg.rg_id = re.re_rg_id) ";
		$sql.= " LEFT JOIN tbl_tipo_velocidad tv WITH(NOLOCK) ON tv.tv_id = re.re_tv_id ";
		$sql.= " WHERE re.re_borrado = 0 ";
		
		//-- habilitar 2da linea y quitar 1ra para habilitar la recomendación de zonas inteligentes --//
		//$sql.= " AND ((re.re_validar_usuario IS NULL AND re.re_inteligente = 0) OR (re.re_inteligente = 1 AND re.re_validar_usuario = 1)) ";
		$sql.= " AND ((re.re_validar_usuario IS NULL AND re.re_inteligente = 0) OR (re.re_inteligente = 1 AND re.re_validar_usuario != 2)) ";
		//-- --//
		
		//-- Obtener Zonas de Panico Vinculadas --//
		if($this->objSQL->dirConfig != 'chapelco'){
		$sql.= " AND (
				(re_panico IS NULL OR re.re_panico < 0)
				OR (re.re_id IN (SELECT hp_re_id FROM tbl_historial_probador_panico WHERE hp_borrado = 0 AND hp_Estado = 1))
			)";
		}
		//-- --//
		
		if(!empty($filtro)){
			$sql.= " AND (re_nombre like '%".$filtro."%')";
		}	
		
		if((int)$idEmpresa > 0){
			$sql.= "AND us.us_cl_id = ".(int)$idEmpresa;
		}
		$sql.= " ORDER BY rg.rg_nombre ASC, re_nombre ASC, re.re_rg_id ASC, rc_id ASC ";
		
		$objReferencias = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias, 3);
		return $objRow;
    }

	function getReferenciasGrupos($segmentacion = NULL){
		$sql = " SELECT COUNT(*) as cant FROM tbl_referencias_grupos_agentes WITH(NOLOCK) WHERE rga_cl_id =".(int)$_SESSION['idAgente'];
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);
		if($rs['cant'] > 0){
			$sql = " SELECT rg_id, rg_nombre ";
			$sql.= " FROM tbl_referencias_grupos WITH(NOLOCK) ";
			$sql.= " INNER JOIN tbl_referencias_grupos_agentes WITH(NOLOCK) ON rga_rg_id = rg_id ";
			$sql.= " WHERE rga_cl_id = ".(int)$_SESSION['idAgente']." AND rg_borrado = 0 ";
			$sql.= " AND rg_id != 33 ";
			
			if($segmentacion){
				$sql.= " AND rg_nombre = '".$segmentacion."'";	
			}
			
			$sql.= " ORDER BY rg_nombre ";
		}
		elseif(!$segmentacion){
			$sql = " SELECT rg_id, rg_nombre ";
			$sql.= " FROM tbl_referencias_grupos WITH(NOLOCK) ";
			$sql.= " WHERE rg_default = 1 AND rg_borrado = 0 ";
			$sql.= " ORDER BY rg_nombre ";	
		}
		else{
			return false;	
		}
		$objReferencias = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias,3);
		return $objRow;
	}
	
	function obtenerGrupoPanico() {
       /*
	   $sql = " SELECT TOP 1 rg_id FROM tbl_referencias_grupos WHERE rg_nombre like '%Zona Segura ADT%' ";	   
        $objReferencias = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetRow($objReferencias, 0, 3);
		if($objRow){
			return $arrReferencias['rg_id'];
		} 
		return false;
		*/
		return 31;
    }
		
    function obtenerReferenciasPorEmpresa2($idEmpresa = 0, $filtro =  NULL, $idModifica = NULL) {
		$selectTop = ' TOP 30 ';
		if($filtro == 'getAllReg'){
			$selectTop = $filtro = '';
		}
		elseif(!empty($filtro)){
			$selectTop = '';
		}
	   
		$sql = " SELECT ".$selectTop;
		$sql.= " tr_id, tr.tr_nombre , re.re_id ,re.re_nombre ,re.re_ubicacion, rg.rg_nombre, re.re_numboca, pr_nombre, lo_nombre, re_email, re_stock_actual, re_recoleccion_re_id ";
		$sql.= " , CASE WHEN re_tr_id = 1 
					THEN  (SELECT CONVERT(VARCHAR,rc_latitud)+','+CONVERT(VARCHAR,rc_longitud) FROM tbl_referencias_coordenadas WITH(NOLOCK) WHERE rc_re_id  = re.re_id)
					ELSE '-'
				END 'LatLng' ";
	   /*
	   $host = '200.32.10.146/localizart';
	   $sql.= " , CASE 
				WHEN re.re_tr_id = 1 THEN(
	SELECT 'http://maps.google.com/maps/api/staticmap?center='+cast(rc_latitud as varchar)+','+cast(rc_longitud as varchar)+'&zoom=12&size=100x100&maptype=roadmap&markers=icon:http://".$host."/imagenes/iconos/markersRastreo/1/referencias/ref-zona.png|label:|'+cast(rc_latitud as varchar)+','+cast(rc_longitud as varchar)+'&sensor=false' FROM tbl_referencias_coordenadas WHERE rc_re_id = re_id)
				ELSE NULL END AS img ";
	   */
	   
	   $sql.= " FROM tbl_referencias re WITH(NOLOCK) ";
	   $sql.= " INNER JOIN tbl_usuarios us WITH(NOLOCK) ON (us.us_id = re.re_us_id) ";
	   $sql.= " INNER JOIN tbl_tipo_referencia tr WITH(NOLOCK) ON (tr.tr_id = re.re_tr_id) ";
	   $sql.= " LEFT JOIN tbl_referencias_grupos rg WITH(NOLOCK) ON (rg.rg_id=re.re_rg_id) ";
	   $sql.= " LEFT JOIN tbl_provincias WITH(NOLOCK) ON re_provincia = pr_id ";
	   $sql.= " LEFT JOIN tbl_localidad WITH(NOLOCK) ON re_localidad = lo_id ";
	   $sql.= " WHERE re.re_borrado = 0 ";
	   $sql.= " AND ((re.re_validar_usuario IS NULL AND re.re_inteligente = 0) /*--*/) ";/*OR (re.re_inteligente = 1 AND re.re_validar_usuario = 1)*/
	   $sql.= " AND (re.re_panico IS NULL OR re.re_panico = 0) ";
	   
	   if(!empty($idModifica))
	   {
		  $sql.="AND re.re_id=".$idModifica;   
	   }
	   
	   
	   if(!empty($filtro)){
	   		$sql.= " AND (re.re_nombre like '%".$filtro."%' OR re.re_numboca like '%".$filtro."%') ";
	   }
	   $sql.= " AND us.us_cl_id = ".(int)$idEmpresa;
	   $sql.= " ORDER BY re_nombre ";
	    $objReferencias = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias, 3);
		return $objRow;
    }

   	function getRenderReferencias($filtro = NULL){
	
		$arrReferencias = array();
		$arrReferencias = $this->obtenerReferenciasPorEmpresa3((int)$_SESSION["idEmpresa"]);
		$temp = array();
		
		if($arrReferencias){
			$count = count($arrReferencias);
			$lastId = 0;
			$indice = -1;
			$iCont = -1;
			for($i = 0; $i < $count; $i++) {
				
				$arrReferencias[$i]['grupo'] = $arrReferencias[$i]['grupo'];
				
				if ($lastId != $arrReferencias[$i]['id']) {
					$iCont++;
					$indice = -1;
					$temp[$iCont] 			= $arrReferencias[$i];
					$lastId 				= $arrReferencias[$i]['id'];
				}
				$indice++;
				$temp[$iCont]['coords'][$indice]['lat'] 	= $arrReferencias[$i]['lat'];
				$temp[$iCont]['coords'][$indice]['lng'] 	= $arrReferencias[$i]['lng'];
			}		
		}
		
		return $temp;
	}
	
	function getTipoReferencias(){
		$sql = "SELECT tr_id as id, tr_nombre as nombre FROM tbl_tipo_referencia WITH(NOLOCK) ";
		if(!tienePerfil(19)){
			$sql.= " WHERE tr_id != 4 ";	
		}
		$objReferencias = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetAllRows($objReferencias);
		return $objRow;
	}
	
	function getTipoCamino(){
		$sql = "SELECT tc_id as id, tc_descripcion as nombre FROM tbl_tipo_camino WITH(NOLOCK) ORDER BY tc_descripcion";
		$objReferencias = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias);
		return $objRow;
	}
	
	function obtenerOtrasZonasSegurasADT($usuario, $panicoExcluido){
		$sql = "
				SELECT re_panico, rc_latitud, rc_longitud 
				FROM tbl_referencias WITH(NOLOCK) 
				INNER JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON rc_re_id = re_id
				WHERE re_borrado = 0 
				AND re_us_id = ".$usuario."
				AND re_panico > 0
				AND re_panico <> ".$panicoExcluido."
				ORDER BY re_panico";
		$objReferencias = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias);
		return $objRow;
	}
	
	function eliminarHistoricosZonaPanico($idRef) {
        if ($idRef) {
            $strSQL = "UPDATE tbl_historial_probador_panico
					   SET hp_borrado = 1
					   WHERE hp_re_id =".$idRef;
			if ($this->objSQL->dbQuery($strSQL)){
                return true;
			}
        }
        return false;
    }
	
	function obtenerReferenciaPorLT($ltCode, $idReferencia=0){
		$sql = "
				SELECT re_lt
				FROM tbl_referencias WITH(NOLOCK)				
				WHERE re_borrado = 0 
				AND re_lt = '".$ltCode."'";
		if($idReferencia > 0){
			$sql .= " AND re_id <> ".$idReferencia;
		}
		
		$objReferencias = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias);
		return $objRow;
	}

	function obtenerTrafico(){
		$sql = "select re_nombre , re_tv_ultima_actualizacion  , re_tv_id from tbl_referencias WITH(NOLOCK) where re_tr_id = 4 and re_borrado=0 order by re_nombre";
		$objReferencias = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetAllRows($objReferencias);
		return $objRow;
	}
	
	function setCancelInteligencia($id_referencia){
		$sql = " UPDATE tbl_referencias SET re_validar_usuario = 2 WHERE re_id = ".(int)$id_referencia;
		$this->objSQL->dbQuery($sql);
	}
	
	function validarReferencia($arr_datos){
		$sql = " SELECT COUNT(*) as cant FROM tbl_clientes WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_us_id = us_id ";
		$sql.= " WHERE cl_id = ".(int)$arr_datos['cl_id']." AND cl_id_distribuidor = ".(int)$arr_datos['idEmpresa'];
		$sql.= " AND re_id = ".(int)$arr_datos['re_id']." AND re_us_id = ".(int)$arr_datos['us_id'];	
		$objReferencias = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetRow($objReferencias);
		return (int)$objRow['cant'];
	}
	
	function getPais($id_pais){
		$sql = " SELECT pa_id, pa_nombre FROM tbl_pais WITH(NOLOCK) ";
		$sql.= "WHERE pa_borrado = 0 AND pa_id = ".(int)$id_pais;
		$rs = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetRow($rs,0,3);
		return $objRow['pa_nombre'];
	}
	
	function getProvincia($id_pais, $provincia = NULL){
		$sql = " SELECT pr_id, pr_nombre FROM tbl_provincias WITH(NOLOCK) WHERE pr_pa_id = ".(int)$id_pais;
		if($provincia){
			$sql.= " AND pr_nombre = '".$provincia."'";
		}
		$sql.= " ORDER BY pr_nombre ";
		$res = $this->objSQL->dbQuery($sql);
		$prov = $this->objSQL->dbGetAllRows($res,3);
		return $prov;
	}
	
	function getLocalidad($id_provincia, $localidad = NULL){
		$sql = " SELECT lo_id, lo_nombre FROM tbl_localidad WITH(NOLOCK) WHERE lo_pr_id = ".(int)$id_provincia;
		if($localidad){
			$sql.= " AND lo_nombre = '".$localidad."'";
		}
		$sql.= " ORDER BY lo_nombre ";
		$res = $this->objSQL->dbQuery($sql);
		$prov = $this->objSQL->dbGetAllRows($res,3);
		return $prov;
	}
	
	function importarReferencias($files){
		require_once('clases/PHPExcel/IOFactory.php');
		$objExcel = PHPExcel_IOFactory::load($files['tmp_name']);
		
		try{
			$objHoja[0] = $objExcel->getSheet(0)->toArray(NULL,true,false,true);
		}
		catch(Exception $e){
			echo $e;
			return 'La Hoja 1, de la planilla de excel que intenta importar genera un error. Verifique que la misma no contenga columnas calculadas.';
		}
		/*
		try{
			$objHoja[1] = $objExcel->getSheet(1)->toArray(NULL,true,false,true);
		}
		catch(Exception $e){
			return 'La Hoja 2, de la planilla de excel que intenta importar genera un error. Verifique que la misma no contenga columnas calculadas.';
		}*/
		
		unset($objHoja[0][1]);
		$mensaje = '';
		$cantErrores = 0;
		
		foreach($objHoja[0] as $k => $hoja1){
			
			$arrReferencia = array();
			$error = false;
			
			$arrReferencia['num_boca'] = trim($hoja1['A']);
			if(empty($arrReferencia['num_boca'])){
				$mensaje.= '<br>El campo Código del Cliente es requerido (Ver Hoja1, A:'.$k.')';
				$cantErrores++;
				$error = true;
			}
			
			$arrReferencia['nombre'] = trim($hoja1['B']);
			if(empty($arrReferencia['nombre'])){
				$mensaje.= '<br>El campo Nombre o Identificación es requerido (Ver Hoja1, B:'.$k.')';
				$cantErrores++;
				$error = true;
			}
			
			$arrReferencia['direccion'] = trim($hoja1['C']);
			if(empty($arrReferencia['direccion'])){
				$mensaje.= '<br>El campo Dirección o Ubicación es requerido (Ver Hoja1, C:'.$k.')';
				$cantErrores++;
				$error = true;
			}
			
			$arrReferencia['lat'] = trim($hoja1['F']);
			if(empty($arrReferencia['lat'])){
				$mensaje.= '<br>El campo Latitud es requerido (Ver Hoja1, F:'.$k.')';
				$cantErrores++;
				$error = true;
			}
			
			$arrReferencia['lng'] = trim($hoja1['G']);
			if(empty($arrReferencia['lng'])){
				$mensaje.= '<br>El campo Longitud es requerido (Ver Hoja1, G:'.$k.')';
				$cantErrores++;
				$error = true;
			}
			
			$arrReferencia['rango'] = trim($hoja1['H']);
			if(empty($arrReferencia['rango'])){
				$mensaje.= '<br>El campo Rango es requerido (Ver Hoja1, H:'.$k.')';
				$cantErrores++;
				$error = true;
			}
			
			if(!$error){
				$arrReferencia['ciudad'] = !empty($hoja1['D'])?trim($hoja1['D']):NULL;
				$arrReferencia['provincia'] = !empty($hoja1['E'])?trim($hoja1['E']):NULL;
				$arrReferencia['segmentacion'] = !empty($hoja1['I'])?trim($hoja1['I']):NULL;
				$arrReferencia['gba'] = !empty($hoja1['J'])?trim($hoja1['J']):NULL;
				///-----
				$arrDatos = array();
				$arrDatos['re_us_id'] = (int)$_SESSION['idUsuario'];
				$arrDatos['re_pais'] = (int)$_SESSION['idPais'];
				$arrDatos['re_nombre'] = $arrReferencia['nombre'];
				$arrDatos['re_ubicacion'] = $arrReferencia['direccion'];
				$arrDatos['re_tr_id'] = 1;
				$arrDatos['re_radioEgreso'] = $arrDatos['re_radioIngreso'] = (int)$arrReferencia['rango']; 
				$arrDatos['re_numboca'] = $arrReferencia['num_boca'];
				
				$arrDatosCoord = array();
				$arrDatosCoord['rc_latitud'] = $arrReferencia['lat'] ;
				$arrDatosCoord['rc_longitud'] = $arrReferencia['lng'];
				
	
				if($arrReferencia['provincia']){
					$prov = $this->getProvincia($arrDatos['re_pais'], $arrReferencia['provincia']);
					if($prov[0]){
						$arrDatos['re_provincia'] = $prov[0]['pr_id'];
					}
				}
				
				if($arrReferencia['ciudad'] && $arrDatos['re_provincia']){
					$loc = $this->getLocalidad($arrDatos['re_provincia'], $arrReferencia['ciudad']);
					if($loc[0]){
						$arrDatos['re_localidad'] = $loc[0]['lo_id'];
					}
				}
				
				if($arrReferencia['segmentacion']){
					$segmentacion = $this->getReferenciasGrupos($arrReferencia['segmentacion']);
					if($segmentacion[0]){
						$arrDatos['re_rg_id'] = $segmentacion[0]['rg_id'];
					}
				}
				
				if($arrReferencia['gba']){
					$strSQL = " SELECT gba_id FROM tbl_referencias_gba WITH(NOLOCK) ";
					$strSQL.= " WHERE gba_borrado = 0 AND gba_nombre = '".$arrReferencia['gba']."' AND gba_cl_id = ".(int)$_SESSION['idAgente'];		
					$res = $this->objSQL->dbQuery($strSQL);
					$gba = $this->objSQL->dbGetRow($res,0,3);
					if($gba['gba_id']){
						$id_gba = $gba['gba_id'];
					}
				}
				
				$strSQL = " SELECT re_id FROM tbl_referencias WITH(NOLOCK) ";
				$strSQL.= " WHERE re_numboca = '".$arrReferencia['num_boca']."' AND re_borrado = 0 
							AND re_us_id IN (SELECT us_id FROM tbl_usuarios WHERE us_cl_id = ".(int)$_SESSION['idEmpresa'].") ";
				$res = $this->objSQL->dbQuery($strSQL);
				$ref = $this->objSQL->dbGetRow($res,0,3);
				if($ref['re_id']){
					if($this->objSQL->dbQueryUpdate($arrDatos, 'tbl_referencias', 're_id = '.$ref['re_id'])){
						$this->objSQL->dbQueryUpdate($arrDatosCoord, 'tbl_referencias_coordenadas', 'rc_re_id = '.$ref['re_id']);
					}
					
					if($id_gba){
						$strSQL = " SELECT COUNT(*) AS cant FROM tbl_referencias_rel_gba WITH(NOLOCK) WHERE rel_re_id = ".(int)$ref['re_id']." AND rel_gba_id = ".(int)$id_gba;
						$res = $this->objSQL->dbQuery($strSQL);
						$relGba = $this->objSQL->dbGetRow($res,0,3);
						if(!$relGba['cant']){
							$arrGba = array('rel_re_id' => $ref['re_id'], 'rel_gba_id' => $id_gba);
							$this->objSQL->dbQueryInsert($arrGba,'tbl_referencias_rel_gba');	
						}
					}
				}
				else{
					$idReferencia = $this->objSQL->dbQueryInsert($arrDatos,'tbl_referencias');
					if($idReferencia){
						$arrDatosCoord['rc_re_id'] = $idReferencia;
						$this->objSQL->dbQueryInsert($arrDatosCoord,'tbl_referencias_coordenadas');	
						
						if($id_gba){
							$arrGba = array('rel_re_id' => $idReferencia, 'rel_gba_id' => $id_gba);
							$this->objSQL->dbQueryInsert($arrGba,'tbl_referencias_rel_gba');	
						}
						
					}
				}
			}
		}
		
		$msg = array();
		if(!$cantErrores){
			$msg['result'] = 'ok';
			$msg['msg'] = 'La información se ha procesado con éxito.';
		}
		else{
			$msg['result'] = 'error';
			$msg['msg'] = 'Se han detectado '.$cantErrores.' errores, <a href="boot.php?c=abmReferencias&action=importarExcel&content_file='.urlencode(base64_encode($mensaje)).'">descargar</a> documento con los errores.';
		}
		
		return $msg;	
	}
	
	function getReferenciasAjustar($id = NULL, $txtFiltro = NULL){
		$sql = " SELECT  re_nombre, re_numboca, re_ubicacion, re_radioIngreso as radio, vi_codigo, us_nombre, us_apellido, iar_estado, tbl_IA_referencias.* ";
		$sql.= " FROM tbl_IA_referencias WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = iar_re_id ";
		$sql.= " INNER JOIN tbl_viajes WITH(NOLOCK) ON vi_id = iar_vi_id ";
		$sql.= " LEFT JOIN tbl_usuarios WITH(NOLOCK) ON us_id = iar_us_id ";
		$sql.= " WHERE re_tr_id = 1 ";	
		if(!tienePerfil(19)){
			$sql.= " AND re_us_id IN (SELECT us_id FROM tbl_usuarios WHERE us_cl_id = ".(int)$_SESSION['idEmpresa'].") ";
		}
		if($id){
			$sql.= " AND iar_id = ".(int)$id;	
		}
		
		if(!empty($txtFiltro)){
			$sql.= " AND (
						re_nombre LIKE '%".$txtFiltro."%' 
						OR vi_codigo LIKE '%".$txtFiltro."%'
						OR us_nombre LIKE '%".$txtFiltro."%'
						OR us_apellido LIKE '%".$txtFiltro."%'
				) ";		
		}
		
		$sql.= " ORDER BY iar_fecha_recomendada DESC"; 
		
		$res = $this->objSQL->dbQuery($sql);
		if($id){
			return $this->objSQL->dbGetRow($res,0,3);
		}
		else{
			return $this->objSQL->dbGetAllRows($res,3);
		}
	}
	
	function setReferenciasAjustar($id, $type){
		global $lang;
		//--$type = 1 ## Se ajusto referencia recomendada
		//--$type = 2 ## NO volver a recomendar el ajuste
		//--$type = 3 ## Ignorar esta recomendación, volver a recomendar más adelante.
		
		if($type == 1){
			$ajustar = $this->getReferenciasAjustar($id);
			
			$idReferencia = $ajustar['iar_re_id'];
			$coordGuardadas = $this->obtenerCoordenadas($idReferencia);
			
			$arrDatosCoord = array();
			$arrDatosCoord['rc_latitud'] = $ajustar['iar_latitud'] ;
			$arrDatosCoord['rc_longitud'] = $ajustar['iar_longitud'];
			if($this->objSQL->dbQueryUpdate($arrDatosCoord, 'tbl_referencias_coordenadas', 'rc_re_id = '.$idReferencia)){
				
				//-- Log System --//
				$strCoord = array();
				if($coordGuardadas){
					foreach($coordGuardadas as $kCoord => $coord){
						array_push($strCoord,$coord['rc_latitud'].', '.$coord['rc_longitud']);		
					}
				}
				
				$newCoord = $ajustar['iar_latitud'].', '.$ajustar['iar_longitud'];
				if(implode(';',$strCoord) != $newCoord){
					$this->generarLog(2,$idReferencia,str_replace('[DATOS_EDITADOS]','['.$newCoord.']',str_replace('[DATOS_ACTUALES]',$coordGuardadas[0]['re_nombre'].(!empty($coordGuardadas[0]['re_numboca'])?' - ID #'.$coordGuardadas[0]['re_numboca']:'').' ['.implode(';',$strCoord).']',$lang->system->edicion_referencia))); 						
				}
				//-- --//
			}
			else{
				return false;	
			}
		}
		
		$sql = " UPDATE tbl_IA_referencias SET iar_estado = ".(int)$type.", iar_us_id = ".(int)$_SESSION['idUsuario']." WHERE iar_id = ".(int)$id;
		if($this->objSQL->dbQuery($sql)){
			return true;		
		}
		return false;
	}
	
	function exiteNumBoca($numboca){
		$strSQL = " SELECT re_id FROM tbl_referencias WITH(NOLOCK) ";
		$strSQL.= " WHERE re_numboca = '".trim($numboca)."' AND re_borrado = 0 
					AND re_us_id IN (SELECT us_id FROM tbl_usuarios WHERE us_cl_id = ".(int)$_SESSION['idEmpresa'].") ";
		$res = $this->objSQL->dbQuery($strSQL);
		$ref = $this->objSQL->dbGetRow($res,0,3);
		if($ref['re_id']){
			return true;
		}
		return false;
	}
}
