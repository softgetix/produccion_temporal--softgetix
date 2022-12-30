<?php
define('INDOOR_EVT_EN_LINEA_CON_TOKEN', 27);
define('INDOOR_EVT_EN_LINEA_CON_TOKEN_NO_LOCALIZADO', 22);
define('INDOOR_EVT_CONEXION', 26);
define('INDOOR_EVT_DESCONEXION', 24);
define('INDOOR_EVT_OPERACION_EN_SUCURSAL', 25);
define('INDOOR_EVT_OPERACION_FUERA_SUCURSAL', 23);
define('INDOOR_ESTADO_CONECTADO', 1);
define('INDOOR_ESTADO_NO_CONECTADO', 2);

require_once 'clases/clsAbms.php';
class Equipo extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_unidad','un');
	}

    function eliminarEquipoSH($id) {
        if($id){
            $strSQL = " DELETE FROM tbl_sys_heart WHERE sh_un_id IN (".$id.") ";
            if ($this->objSQL->dbQuery($strSQL)){
                return true;
			}
        }
        return false;
    }

    function modificarEquipo($set, $id, $campoValidador = NULL) {
        if ($set && $id) {
            $intRows = count($this->obtenerEquipos($id,'getAllReg'));
			if($intRows){
				if($this->modificarRegistro($set, $id)){
                    return true;
				}
            }
        }
        return false;
    }

	function validarIdentificadorInterno($campoValidador = "", $idEquipo = "") {
       	$strSQL = " SELECT uc_identificador
					FROM tbl_unidad_orbcomm WITH(NOLOCK)
					WHERE uc_identificador = '".$campoValidador."' AND uc_un_id != '".$idEquipo."'";
		$objEquipos = $this->objSQL->dbQuery($strSQL);
		$intRows = $this->objSQL->dbNumRows($objEquipos);	
		if (!$intRows) {				
			return true;
		}
		return false;
    }
	
    function obtenerEquipos($id = 0, $filtro = "", $idMovil = 0, $idDistribuidor = 0) {
        $selectTop = ' TOP 30 ';
		if($filtro == 'getAllReg'){
			$selectTop = $filtro = '';
		}
		elseif(!empty($filtro)){
			$selectTop = '';
		}
		
		$strSQL = " SELECT ".$selectTop;
		$strSQL.= " un_id,un_me_id,un_mostrarComo,un_mo_id,un_esPrimaria,
				ug_telefono, ug_simcard, ug_ca_id, ug_identificador,
				uc_identificador,
				me_nombre,un_mod_id,mo_nombre,
				cl_razonSocial,
				un_ds_id, un_de_id, un_nro_serie,
				mo_puerto, mo_ip ";
		$strSQL.= " FROM tbl_unidad WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_unidad_gprs WITH(NOLOCK) ON (un_id = ug_un_id) ";
		$strSQL.= " LEFT JOIN tbl_unidad_orbcomm WITH(NOLOCK) ON (un_id = uc_un_id) ";
		$strSQL.= " LEFT JOIN tbl_marca_equipo WITH(NOLOCK) ON (me_id = un_me_id) ";
		$strSQL.= " LEFT JOIN tbl_modelos_equipo WITH(NOLOCK) ON (mo_id = un_mod_id) ";
		$strSQL.= " LEFT JOIN tbl_clientes WITH(NOLOCK) ON (un_ds_id = cl_id) ";
		$strSQL.= " WHERE un_borrado=0  ";
		if($id){
			$strSQL.= " AND un_id = ".(int)$id;
		}
		if(!empty($filtro)){
			$strSQL.= " AND un_mostrarComo like '%".$filtro."%'";
		}
		//$strSQL.= " AND un_mostrarComo = case @campoValidador when '' then un_mostrarComo else @campoValidador end
		if($idMovil){
			$strSQL.= " AND un_mo_id = ".(int)$idMovil;
		}
		if($idDistribuidor){
			$strSQL.= " AND un_ds_id = ".(int)$idDistribuidor;
		}
		/*if($idValidador){
			$strSQL.= " AND un_id <> ".$idValidador;
		}*/
		
		$strSQL.= " ORDER BY un_mostrarComo ASC ";
		$objEquipos = $this->objSQL->dbQuery($strSQL);
        $arrEquipos = $this->objSQL->dbGetAllRows($objEquipos, 1);
        return $arrEquipos;
    }

    function obtenerEquiposUsuario($idUsuario, $filtro = NULL){
		$strSQL = " SELECT un_mostrarComo, un_id ";
		$strSQL.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_unidad WITH(NOLOCK) ON (un_mo_id = um_mo_id) ";
		$strSQL.= " WHERE un_borrado=0 AND um_us_id = ".(int)$idUsuario;
		if(!empty($filtro)){
			$strSQL.= " AND un_mostrarComo like '%".$filtro."%' ";	
		}
        $objEquipos = $this->objSQL->dbQuery($strSQL);
        $arrEquipos = $this->objSQL->dbGetAllRows($objEquipos, 1);
        return $arrEquipos;
    }

    function obtenerEquiposListado($idEmpresa = 0, $idTipoEmpresaExcuyente = NULL, $idDistribuidor = 0) {
	   $sql = " SELECT un_id,un_mostrarComo, me_nombre ,moe.mo_nombre, ug_simcard, ug_telefono, clientes.cl_razonSocial as ag_nombre, un_nro_serie,cl.cl_razonSocial as cl_nombre, (mov.mo_identificador + ' / ' + mov.mo_matricula + ' / ' + mov.mo_otros) as mo_identificador, mov.mo_id ";
	   $sql.= " FROM tbl_unidad WITH(NOLOCK) ";
	   $sql.= " LEFT JOIN tbl_marca_equipo WITH(NOLOCK) ON (me_id = un_me_id and me_borrado=0) ";
	   $sql.= " LEFT JOIN tbl_modelos_equipo as moe WITH(NOLOCK) ON (moe.mo_id = un_mod_id and moe.mo_borrado=0) ";
	   $sql.= " INNER JOIN tbl_clientes clientes WITH(NOLOCK) on (un_ds_id=clientes.cl_id) ";
	   $sql.= " INNER JOIN tbl_unidad_gprs WITH(NOLOCK) ON (un_id = ug_un_id) ";
	   $sql.= " LEFT JOIN tbl_moviles as mov WITH(NOLOCK) on (mov.mo_id=un_mo_id) ";
	   $sql.= " LEFT JOIN tbl_clientes cl WITH(NOLOCK) ON cl.cl_id = mo_id_cliente_facturar ";
	   $sql.= " WHERE un_borrado=0 ";
	   if ($idDistribuidor != 0) $sql.= " AND un_ds_id=$idDistribuidor ";
	   $sql.= " ORDER BY un_mostrarComo ";
	   $objEquipos = $this->objSQL->dbQuery($sql);
	   $arrEquipos = $this->objSQL->dbGetAllRows($objEquipos, 3);
       return $arrEquipos;
    }

    function obtenerDatosGprs($idEquipo) {
		$strSQL = " SELECT ug_id FROM tbl_unidad_gprs WITH(NOLOCK) WHERE ug_un_id = ".(int)$idEquipo;
        $objEquipos = $this->objSQL->dbQuery($strSQL);
        $intRows = $this->objSQL->dbNumRows($objEquipos);
        if ($intRows){
            return true;
		}	
        return false;
    }

    function obtenerDatosOrbcomm($idEquipo){
		$strSQL = " SELECT uc_id FROM tbl_unidad_orbcomm WITH(NOLOCK) WHERE uc_un_id = ".(int)$idEquipo;
        $objEquipos = $this->objSQL->dbQuery($strSQL);
        $intRows = $this->objSQL->dbNumRows($objEquipos);
        if ($intRows){
            return true;
		}	
        return false;
    }

    function insertarEntradaEquipo($idEquipo = 0, $numeroEntrada = 0, $idEntrada = 0) {
        if ($idEquipo && $numeroEntrada){
			$intRows = count($this->obtenerEntradasEquipos($idEquipo, $numeroEntrada, $idEntrada));
			if(!$intRows){
				$strSQL = " INSERT INTO tbl_entradas_equipos(ee_id_equipo, ee_numeroEntrada, ee_id_entrada) ";
				$strSQL = " VALUES(".(int)$idEquipo.", ".(int)$numeroEntrada.", ".(int)$idEntrada.")";
                if ($objEquipos = $this->objSQL->dbQuery($strSQL)){
				    return true;
                }
            }
        }
        return false;
    }
	
	function buscarEntradaEquipo($idEquipo,$numeroEntradaEquipo){
		$strSQL = " SELECT 1 FROM tbl_entradas_equipos WITH(NOLOCK) ";
		$strSQL.= " WHERE ee_id_equipo = ".(int)$idEquipo." AND ee_numeroEntrada = ".(int)$numeroEntradaEquipo;
        $objEquipos = $this->objSQL->dbQuery($strSQL);
        $arrEquipos = $this->objSQL->dbGetAllRows($objEquipos, 1);
        return $arrEquipos[0][0];
	}
	
    function obtenerEntradasEquipos($idEquipo, $numeroEntrada = 0, $idEntrada = 0){
		$strSQL = " SELECT ee_id_equipo, ee_numeroEntrada, ee_id_entrada ";
		$strSQL.= " FROM tbl_entradas_equipos WITH(NOLOCK) ";
		$strSQL.= " WHERE ee_borrado=0 AND ee_id_equipo = ".(int)$idEquipo;
		 
		if($numeroEntrada){
			$strSQL.= " AND ee_numeroEntrada = ".(int)$numeroEntrada;
		}
		
		if($idEntrada){	
			$strSQL.= " AND ee_id_equipo = ".(int)$idEntrada;
		}
		
        $objEquipos = $this->objSQL->dbQuery($strSQL);
        $arrEquipos = $this->objSQL->dbGetAllRows($objEquipos, 1);
        return $arrEquipos;
    }

    function modificarEntradaEquipo($idEquipo, $numeroEntrada = 0, $idEntrada = 0) {
        if($idEquipo && $numeroEntrada){
            $strSQL = " UPDATE tbl_entradas_equipos SET ee_id_entrada = ".(int)$idEntrada;
			$strSQL.= " WHERE ee_id_equipo = ".(int)$idEquipo." AND ee_numeroEntrada = ".(int)$numeroEntrada;
            if ($this->objSQL->dbQuery($strSQL)){
                return true;
			}
        }
        return false;
    }

    function obtenerEquiposMoviles($idMovil = 0, $idEquipo = 0) {
        $strSQL = " SELECT * FROM tbl_moviles WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id ";
		if($idMovil){
			$strSQL.= " WHERE mo_id = ".(int)$idMovil;
		}
		elseif($idEquipo){
			$strSQL.= " WHERE un_id = ".(int)$idEquipo;
		}
		else{
			return false;
		}
		$objEquipos = $this->objSQL->dbQuery($strSQL);
       	$arrEquipos = $this->objSQL->dbGetRow($objEquipos, 0, 3);
        return $arrEquipos;
    }

    function eliminarAsignacionesEquiposMovil($idMovil) {
        if ($idMovil) {
            $strSQL = " UPDATE tbl_unidad set un_mo_id = 0 WHERE un_mo_id = ".(int)$idMovil;
            if ($this->objSQL->dbQuery($strSQL)){
                return true;
			}
        }
        return false;
    }

    function insertarAsignacionEquipoMovil($idMovil, $idEquipo) {
        if ($idMovil && $idEquipo) {
            $intRows = count($this->obtenerEquipos($idEquipo, 'getAllReg', $idMovil, NULL));
			if (!$intRows) {
				$strSQL = " UPDATE tbl_unidad set un_mo_id = ".(int)$idMovil." WHERE un_id = ".(int)$idEquipo;
                if ($objEquipos = $this->objSQL->dbQuery($strSQL)){
                    return true;
				}
            }
        }
        return false;
    }

    function asignarEquipoPrimario($idEquipo) {
        if($idEquipo){
            $strSQL = " UPDATE tbl_unidad SET un_esPrimaria = 1 WHERE un_id = ".(int)$idEquipo;
            if ($this->objSQL->dbQuery($strSQL)){
                return true;
			}
        }
        return false;
    }

    function desasignarEquipoPrimario($idEquipo) {
        if ($idEquipo) {
            $strSQL = " UPDATE tbl_unidad SET un_esPrimaria = 0 WHERE un_id = ".(int)$idEquipo;
            if ($this->objSQL->dbQuery($strSQL)){
                return true;
			}
        }
        return false;
    }

    function validarUnidadUsuario($unidad) {
        if ($unidad) {
            $strSQL = " SELECT moviles.mo_matricula , heart.sh_latitud , heart.sh_longitud , heart.sh_Fecharecepcion , unidad.un_id";
			$strSQL.= " FROM tbl_moviles moviles WITH(NOLOCK) ";
			$strSQL.= " INNER JOIN tbl_usuarios_moviles usuario_moviles WITH(NOLOCK) ON usuario_moviles.um_mo_id = moviles.mo_id ";
			$strSQL.= " INNER JOIN tbl_unidad unidad WITH(NOLOCK) ON unidad.un_mo_id = moviles.mo_id ";
			$strSQL.= " INNER JOIN tbl_sys_heart heart WITH(NOLOCK) ON heart.sh_un_id = unidad.un_id ";
			$strSQL.= " WHERE unidad.un_mostrarComo = '".$unidad."' AND usuario_moviles.um_us_id = ".(int)$_SESSION['idUsuario'];
            $r = $this->objSQL->dbQuery($strSQL);
            $row = $this->objSQL->dbGetAllRows($r);
            return $row[0];
        }
        return false;
    }
    
    function modificarUnidad($idUnidad, $arrDatos) {
        $strSQL = "UPDATE tbl_unidad SET ";
        foreach ($arrDatos as $field => $value) {
            if (is_numeric($value)) {
                $fields[] = "{$field}=".$value;
            } else {
                $fields[] = "{$field}='{$value}'";
            }
        }
        $strSQL .= implode(", ", $fields);
        $strSQL .= " WHERE un_id={$idUnidad}";
        
        $r = $this->objSQL->dbQuery($strSQL);
    }
    

    function validarUnidadUsuario2($unidad) {
        $macAddress = array();
        $arrEquipos = $this->obtenerEquiposUsuario($_SESSION['idUsuario']);


        foreach ($arrEquipos as $equipo) {
            $macAddress[] = $equipo['un_mostrarComo'];
        }
        return in_array($unidad, $macAddress);
    }

    function insertarEvento($evento, $latitud, $longitud, $un_id, $estado) {
        $numTabla = 1;
        $fecha = date("Y-m-d H:i:s");
        $tipoUnidad = 1; // GPRS
        $ticketPaquete = date("dHms");

        $strSQL = "EXEC dbo.pa_insertar_historial_indoor {$evento}, '{$fecha}', {$latitud}, {$longitud}, {$un_id}, '{$ticketPaquete}', {$estado}";
        $r = $this->objSQL->dbQuery($strSQL);
        if ($r) {
            return true;
        }
        return false;
    }
	
	function getEquiposNoReportan($idDistribuidor = 0) {
		$sql = " SELECT un_id,un_mostrarComo, me_nombre, moe.mo_nombre, ug_simcard, ug_telefono, cl_razonSocial as ag_nombre, un_nro_serie, cl_razonSocial, (mov.mo_identificador + ' / ' + mov.mo_matricula + ' / ' + mov.mo_otros) as mo_identificador,mov.mo_id, sh_fechaRecepcion ";
		$sql.= " FROM tbl_unidad WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_sys_heart WITH(NOLOCK) ON un_id = sh_un_id ";
		$sql.= " LEFT JOIN tbl_marca_equipo WITH(NOLOCK) ON (me_id = un_me_id and me_borrado=0) ";
		$sql.= " LEFT JOIN tbl_modelos_equipo as moe WITH(NOLOCK) ON (moe.mo_id = un_mod_id and moe.mo_borrado=0) ";
		$sql.= " INNER JOIN tbl_unidad_gprs WITH(NOLOCK) ON (un_id = ug_un_id) ";
		$sql.= " LEFT JOIN tbl_moviles as mov WITH(NOLOCK) on (mov.mo_id=un_mo_id) ";
		$sql.= " INNER JOIN tbl_clientes WITH(NOLOCK) on (mo_id_cliente_facturar =cl_id) ";
		$sql.= " WHERE un_borrado=0 AND sh_fechaRecepcion < = (CURRENT_TIMESTAMP - 1) ";
		if ($idDistribuidor != 0) $sql .= " AND un_ds_id=$idDistribuidor ";
		$sql.= " ORDER BY un_mostrarComo ";
		$rs = $this->objSQL->dbQuery($sql);
        $res = $this->objSQL->dbGetAllRows($rs);
		return $res;
	}
	
	function getEquiposStatus($idDistribuidor = 0) {
		$sql = " SELECT un_id,un_mostrarComo, me_nombre, moe.mo_nombre, ug_simcard, ug_telefono, cl_razonSocial as ag_nombre, un_nro_serie, cl_razonSocial, (mov.mo_identificador + ' / ' + mov.mo_matricula + ' / ' + mov.mo_otros) as mo_identificador,mov.mo_id, sh_fechaGeneracion, CASE WHEN sh_fechaGeneracion < = (CURRENT_TIMESTAMP - 1) THEN 'sin reportar' ELSE 'ONLINE' END as estatus ";
		$sql.= " FROM tbl_unidad WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_sys_heart WITH(NOLOCK) ON un_id = sh_un_id ";
		$sql.= " LEFT JOIN tbl_marca_equipo WITH(NOLOCK) ON (me_id = un_me_id and me_borrado=0) ";
		$sql.= " LEFT JOIN tbl_modelos_equipo as moe WITH(NOLOCK) ON (moe.mo_id = un_mod_id and moe.mo_borrado=0) ";
		$sql.= " INNER JOIN tbl_unidad_gprs WITH(NOLOCK) ON (un_id = ug_un_id) ";
		$sql.= " LEFT JOIN tbl_moviles as mov WITH(NOLOCK) on (mov.mo_id=un_mo_id) ";
		$sql.= " INNER JOIN tbl_clientes WITH(NOLOCK) on (mo_id_cliente_facturar =cl_id) ";
		$sql.= " WHERE un_borrado=0 AND cl_borrado=0 AND mov.mo_borrado=0 ";
		if ($idDistribuidor != 0) $sql .= " AND un_ds_id=$idDistribuidor ";
		$sql.= " ORDER BY un_mostrarComo ";
		
		$rs = $this->objSQL->dbQuery($sql);
        $res = $this->objSQL->dbGetAllRows($rs);
		return $res;
	}
	
	function getEntradaEquipos() {
		$sql = " SELECT en_id as id, en_descripcion dato ";
		$sql.= " FROM tbl_entradas WITH(NOLOCK) ";
		$sql.= " WHERE en_borrado = 0 ";
		$sql.= " ORDER BY dato ";
		
		$rs = $this->objSQL->dbQuery($sql);
        $res = $this->objSQL->dbGetAllRows($rs);
		return $res;
	}
	
	
	//-- TELEMETRIA --//
	function getExpresionAlgebraica() {
		$sql = " SELECT op_id, op_simbolo ";
		$sql.= " FROM tbl_operaciones WITH(NOLOCK) ";
		$sql.= " ORDER BY op_id ";
		$rs = $this->objSQL->dbQuery($sql);
        $res = $this->objSQL->dbGetAllRows($rs,3);
		return $res;
	}
	
	function getTelemetria($idEquipo, $nro_orden = false){
		$sql = " SELECT * FROM tbl_unidad_telemetria WITH(NOLOCK) ";
		$sql.= " WHERE ut_un_id = ".(int)$idEquipo;
		if($nro_orden){
			$sql.= " AND ut_orden = ".(int)$nro_orden;
		}
		$sql.= " ORDER BY ut_orden ";
		$rs = $this->objSQL->dbQuery($sql);
        $res = $this->objSQL->dbGetAllRows($rs,3);
		return $res;
	}
	
	function altaTelemetria($datos){
		$sql = " INSERT INTO tbl_unidad_telemetria (ut_un_id, ut_orden, ut_min, ut_max, ut_op_id, ut_factor, ut_unidad, ut_visible) ";
		$sql.= " values(".(int)$datos['un_id'].", ".(int)$datos['orden'].", ".$datos['ut_min'].", ".$datos['ut_max'].", ".(int)$datos['op_id'].", ".(int)$datos['ut_factor'].", '".$datos['ut_unidad']."', ".(int)$datos['ut_visible'].") ";
		$rs = $this->objSQL->dbQuery($sql);
        return $this->objSQL->dbLastInsertId(); 
	}
	
	function updateTelemetria($datos){
		$sql = " UPDATE tbl_unidad_telemetria  SET ";
		$sql.= " ut_orden = ".(int)$datos['orden'].", ut_min = ".$datos['ut_min'].", ut_max = ".$datos['ut_max'].", ut_op_id = ".(int)$datos['op_id'].", ut_factor = ".(int)$datos['ut_factor'].", ut_unidad = '".$datos['ut_unidad']."', ut_visible = ".(int)$datos['ut_visible'];
		$sql.= " WHERE ut_un_id = ".(int)$datos['un_id']." and ut_id = ".(int)$datos['ut_id'];
		if($rs = $this->objSQL->dbQuery($sql)){
			return $datos['ut_id'];
		}
	}
	
	function bajaTelemetria($idEquipo, $ut_id){
		$sql = " DELETE FROM tbl_unidad_telemetria ";
		$sql.= " WHERE ut_un_id = ".(int)$idEquipo." and ut_id = ".(int)$ut_id;
		if($rs = $this->objSQL->dbQuery($sql)){
			return true;
		}
	}
	
	//-- --//
	
	function obtenerUnidadTelemetria($idMovil, $orden, $visible = true){
        $strSQL = " SELECT ut_unidad FROM tbl_unidad_telemetria WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_id = ut_un_id ";
		$strSQL.= " WHERE un_mo_id = ".(int)$idMovil;
		$strSQL.= " AND ut_visible = ".(int)$visible." AND ut_orden = ".(int)$orden;
		
		$objUnidad = $this->objSQL->dbQuery($strSQL);
		$arr = $this->objSQL->dbGetRow($objUnidad, 0, 3);
		return $arr;
    }
	
	function obtenerModeloEquipos($idMarca = 0){
		$strSQL = " SELECT mo_id as id, mo_nombre as dato ";
		$strSQL.= " FROM tbl_modelos_equipo WITH(NOLOCK) ";
		$strSQL.= " WHERE mo_borrado = 0 ";
		if($idMarca){
			$strSQL.= " AND mo_id_marca = ".(int)$idMarca;
		}
		$obj = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($obj,1);	
	}
	
}
