<?php
require_once 'clases/clsAbms.php';
class Movil extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_moviles','mo');
		$this->allData = false;
	}

	function obtenerRegistros($id=0, $filtro="" ,$campoValidador ='' ,$idValidador=0, $soloCantidad=false, $idEmpresa = ''){
		
		$idUsuario = $_SESSION["idUsuario"];
		if(!$idEmpresa){
			$idEmpresa = ($_SESSION["idTipoEmpresa"] <= 2) ? $_SESSION["idEmpresa"] : 0;
		}
		
		$selectTop = ' TOP 30 ';
		if($filtro == 'getAllReg'){
			$selectTop = $filtro = '';
		}
		elseif(!empty($filtro)){
			$selectTop = '';
		}
		
		$strSQL = " SELECT ".$selectTop;
		$strSQL.= " mo_id, mo_".$this->getVistaMoviles($idUsuario)." as movil,mo_matricula,mo_marca,mo_modelo,mo_id_tipo_movil, mo_id_distribuidor,mo_imagen,mo_anio,
				mo_color,mo_otros,mo_id_cliente_facturar,mo_identificador, usuarios.us_nombreUsuario, clientes.cl_razonSocial,clientes2.cl_razonSocial as agente , tv_nombre,un_mostrarComo, clientes2.cl_razonSocial distribuidor,un_id, un_tiempo, un_tipo_loc,mo_motor, mo_aux1,mo_fecha_activacion
				,mo_co_id_primario, mo_co_id_secundario, mo_frecuencia_reporte, tr_descripcion + ' ' + dbo.formatoFechaAmigable (sh_fechaRecepcion)  as sh_fechaRecepcion, mo_borrado ";
		$strSQL.= " FROM tbl_moviles WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_usuarios usuarios	WITH(NOLOCK) ON (usuarios.us_id = mo_id_distribuidor) ";
		$strSQL.= " LEFT JOIN tbl_clientes clientes	WITH(NOLOCK) ON (clientes.cl_id = mo_id_cliente_facturar) ";
		$strSQL.= " LEFT JOIN tbl_tipo_movil WITH(NOLOCK) ON (tv_id = mo_id_tipo_movil) ";
		$strSQL.= " LEFT JOIN tbl_clientes clientes2 WITH(NOLOCK) ON (mo_id_distribuidor = clientes2.cl_id) ";
		$strSQL.= " LEFT JOIN tbl_unidad WITH(NOLOCK) ON (mo_id = un_mo_id and un_esPrimaria = 1) ";
		$strSQL.= " LEFT JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		$strSQL.= " LEFT JOIN tbl_traduccion_reportes WITH (NOLOCK) ON sh_rd_id = tr_id_reporte  ";
		$strSQL.= " WHERE ".($this->allData?' mo_borrado IN (0,1) ':' mo_borrado = 0 ');
		
		if($id){
			$strSQL.= " AND mo_id = ".(int)$id;
		}
		if($idEmpresa){
			$strSQL.= " AND (mo_id_distribuidor = ".(int)$idEmpresa." or mo_id_cliente_facturar = ".(int)$idEmpresa.") ";
		}
		if(!empty($filtro)){
			$strSQL.= " AND (mo_matricula like '%".$filtro."%' OR mo_marca like '%".$filtro."%' OR mo_modelo like '%".$filtro."%')";
		}
		
		if(!empty($campoValidador)){
			$strSQL.= " AND mo_matricula = '".$campoValidador."'";	
		}
		
		if(!empty($idValidador)){
			$strSQL.= " AND mo_id <> ".(int)$idValidador;	
		}

		if ( $_SESSION['idPerfil'] == 37) {
		$strSQL.= "  and clientes2.cl_paquete in (31,1)   and clientes2.cl_habilitado = 1 ";


		}
		
		$strSQL.= " ORDER BY mo_borrado ASC, clientes.cl_habilitado,  mo_matricula, clientes.cl_razonSocial ASC ";

		$objMoviles = $this->objSQL->dbQuery($strSQL);
		if (!$soloCantidad){
			$arrMoviles = $this->objSQL->dbGetAllRows($objMoviles,3);
			return $arrMoviles;
		}
		else{
			$intRows = $this->objSQL->dbNumRows($objMoviles);
			return $intRows;
		}
	}
	
	function getVistaMoviles($idUsuario){
		$sql = " SELECT dbo.tipoVistaMoviles(".(int)$idUsuario.") ";
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetRow($res,0,1);
		if(empty($rs[0])){
			return 'matricula';	
		}
		return $rs[0];
	}
	
	function getMovilesUsuario($txtBuscar){
		$idUsuario = $_SESSION['idUsuario'];
		
		$vistaMovil = $this->getVistaMoviles($idUsuario);
			
		$sql = " SELECT DISTINCT(tbl_moviles.mo_id) AS id, mo_".$vistaMovil." as valor, mo_otros, mo_matricula ";
		$sql.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON (tbl_moviles.mo_id = um_mo_id) ";
		$sql.= " INNER JOIN tbl_clientes cl WITH(NOLOCK) ON (cl.cl_id =  mo_id_cliente_facturar) ";
		$sql.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON (un_mo_id = um_mo_id) ";
		$sql.= " WHERE tbl_moviles.mo_borrado = 0 AND un_borrado = 0 AND un_esPrimaria = 1 AND um_us_id = ".(int)$idUsuario;
		$sql.= " AND mo_".$vistaMovil." LIKE '%".$txtBuscar."%'";
		$sql.= " ORDER BY valor ASC "; 
		
		$objMovil = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetAllRows($objMovil, 3);
        return $objRow;
	}
	
	function getMovilUsuarioId($idMovil){
		$idUsuario = $_SESSION['idUsuario'];
		
		$vistaMovil = $this->getVistaMoviles($idUsuario);
			
		$sql = " SELECT mo_".$vistaMovil." as valor ";
		$sql.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON (tbl_moviles.mo_id = um_mo_id) ";
		$sql.= " INNER JOIN tbl_clientes cl WITH(NOLOCK) ON (cl.cl_id =  mo_id_cliente_facturar) ";
		$sql.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON (un_mo_id = um_mo_id) ";
		$sql.= " WHERE tbl_moviles.mo_borrado = 0 AND un_borrado = 0 AND un_esPrimaria = 1 AND um_us_id = ".(int)$idUsuario;
		$sql.= " AND mo_id = ".$idMovil;
		$sql.= " ORDER BY valor ASC "; 
		
		$objMovil = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetRow($objMovil,0,3);
        return $objRow;
	}
	
	function modificarImagen($id, $url) {
		$strSQL = "UPDATE tbl_moviles SET mo_imagen = '$url' WHERE mo_id = $id";
		$objMoviles = $this->objSQL->dbQuery($strSQL);
	}

	function obtenerExtensionImagen($idMovil) {
		$strSQL = "SELECT mo_imagen FROM tbl_moviles WITH(NOLOCK) WHERE mo_id = $idMovil";
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetRow($objMoviles,0,3);
		if($objRow){
			return $objRow['mo_imagen'];
		}
		return false;
	}
	
	function getMovilesEstado($datos) {
		## estado: 1 -> Movil reportando, 0 -> Movil Sin reportar
		$sql = " SELECT mo_id as id, mo_matricula as dato, (CASE WHEN dr_id IS NOT NULL THEN 0 else 1 END) AS estado ";
		$sql.= " FROM tbl_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id ";
		$sql.= " INNER JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		$sql.= " LEFT JOIN tbl_definicion_reportes WITH(NOLOCK) ON sh_rd_id = dr_id AND (dr_valor = 980 OR dr_valor = 987) ";
		$sql.= " WHERE mo_id NOT IN (SELECT um_mo_id FROM tbl_usuarios_moviles WITH(NOLOCK) WHERE um_us_id = ".(int)$datos['idUsuario'].") AND mo_borrado = 0 ";	
		if($datos['idCliente']){
			$sql.= " AND mo_id_cliente_facturar = ".(int)$datos['idCliente'];
		}
		if($datos['idDistribuidor']){
			$sql.= " AND mo_id_distribuidor = ".(int)$datos['idDistribuidor'];
		}
		$sql.= " ORDER BY dato ";
		
		$res = $this->objSQL->dbQuery($sql);
		$arrMoviles = $this->objSQL->dbGetAllRows($res);
		return $arrMoviles;
	}
	
	function verificarMovilesReportando($idMoviles) {
		$strSQL = "
			SELECT COUNT(mo_id)
			FROM tbl_moviles WITH(NOLOCK) 
			INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id
			INNER JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id
			INNER JOIN tbl_definicion_reportes WITH(NOLOCK) ON sh_rd_id = dr_id
			WHERE mo_id in($idMoviles) 
			AND (dr_valor = 980 OR dr_valor = 987)
		";
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetRow($objMoviles);
		if($objRow){
			return $arrMoviles[0];	
		}
		return false;
	}
	
	function obtenerMatriculas($idMoviles){
		$strSQL = "
			SELECT mo_matricula,mo_id,sh_fechaGeneracion, dbo.geoCodificar(sh_latitud,sh_longitud,0) as ubicacion, 
			       un_mostrarComo, sh_latitud, sh_longitud,dg_velocidad, dg_curso, dg_entradas, tv_nombre				   
			FROM tbl_moviles WITH(NOLOCK)
			INNER JOIN	tbl_tipo_movil WITH(NOLOCK) ON tv_id = mo_id_tipo_movil
			LEFT JOIN tbl_unidad WITH(NOLOCK) ON mo_id = un_mo_id
			LEFT JOIN tbl_sys_heart WITH(NOLOCK) ON un_id = sh_un_id	
			LEFT JOIN tbl_dato_gp WITH(NOLOCK) ON dg_sh_id = sh_id
			WHERE mo_id in(".$idMoviles.")				
		";
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objMoviles);
		return $objRow;
	}
	
	function obtenerMovilesUsuarioAlerta($id=0,$filtro=""){
		$idUsuario = $id?(int)$id:$_SESSION["idUsuario"];
		
		$sql = " SELECT mo.*, un_mostrarComo as equipo, modelo.mo_nombre as mod_equipo ";
		$sql.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_moviles mo WITH(NOLOCK) ON (mo_id = um_mo_id) ";
		$sql.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id ";
		$sql.= " INNER JOIN tbl_modelos_equipo as modelo WITH(NOLOCK) ON modelo.mo_id = un_mod_id ";
		$sql.= " WHERE mo.mo_borrado = 0 ";
		if ($idUsuario){
			$sql.= " AND um_us_id = ".(int)$idUsuario;}
		
		$sql.= " ORDER BY mo_matricula ";
		
		$objMoviles = $this->objSQL->dbQuery($sql);
		$arrMoviles = $this->objSQL->dbGetAllRows($objMoviles, 3);
		return $arrMoviles;
	}

	function eliminarAsignacionesMovilesUsuario($idUsuario){
		if($idUsuario){
			$strSQL = " DELETE FROM tbl_usuarios_moviles WHERE um_us_id = ".(int)$idUsuario;
			if($this->objSQL->dbQuery($strSQL)){
				return true;
			}	
		}
		return false;
	}

	function insertarAsignacionMovilUsuario($idUsuario=0,$idMovil=0,$grupo=0){
		if($idUsuario && $idMovil){
			$arrAssign = $this->obtenerAsignacionMovilUsuario($idUsuario,$idMovil);
			if(!is_array($arrAssign[0])){
				$strSQL="SELECT TOP 1 um_velocidadMaxima FROM tbl_usuarios_moviles WITH(NOLOCK) WHERE um_mo_id=$idMovil ORDER BY um_id DESC";
				if($objVelMax = $this->objSQL->dbQuery($strSQL)){
					$objRow=$this->objSQL->dbGetRow($objVelMax);
					if($objRow['um_velocidadMaxima']!=""){			
						$velMax=$objRow['um_velocidadMaxima'];
					    $strSQL = "INSERT INTO tbl_usuarios_moviles (um_us_id, um_mo_id,um_grupo,um_velocidadMaxima)
		                            VALUES ($idUsuario,$idMovil,$grupo,$velMax)";
					}
					else{
						$strSQL = "INSERT INTO tbl_usuarios_moviles (um_us_id, um_mo_id,um_grupo)
		                            VALUES ($idUsuario,$idMovil,$grupo)";
					}
					
					if($objEquipos = $this->objSQL->dbQuery($strSQL)){
						 return true;   
					}
				}
			}
		}
		return false;
	}
	
	function insertarAsignacionMovilReferencia($idReferencia, $idMovil) {
		if($idReferencia && $idMovil){
			$strSQL = "INSERT INTO tbl_referencias_vehiculos (rv_re_id, rv_ve_id) VALUES ({$idReferencia}, {$idMovil})";
			$objEquipos = $this->objSQL->dbQuery($strSQL);
			return true;
		}
		return false;
	}
	
	function eliminarAsignacionesMovilesReferencia($idReferencia) {
		if($idReferencia){
			$strSQL = "DELETE FROM tbl_referencias_vehiculos WHERE rv_re_id={$idReferencia}";
			$objMoviles = $this->objSQL->dbQuery($strSQL);
			return true;
		}
		return false;
	}

	function obtenerAsignacionMovilUsuario($idUsuario,$idMovil=0){
		$strSQL = " SELECT um_id, um_us_id, um_mo_id ";
		$strSQL.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$strSQL.= " WHERE um_id > 0 ";
		$strSQL.= " AND um_us_id = ".(int)$idUsuario;
		
		if($idMovil){	
			$strSQL.= " AND um_mo_id = ".(int)$idMovil;
		}	
		
		$objEquipos = $this->objSQL->dbQuery($strSQL);
		$arrMoviles = $this->objSQL->dbGetAllRows($objEquipos,3);
		return $arrMoviles;
	}
	
	function obtenerAsignacionMovilReferencia($idReferencia = null){
		if ($idReferencia) {
			$strSQL = "SELECT M.mo_matricula, M.mo_id FROM tbl_referencias_vehiculos R WITH(NOLOCK)
			INNER JOIN tbl_moviles M WITH(NOLOCK) ON M.mo_id=R.rv_ve_id AND R.rv_re_id={$idReferencia}";
			$objMoviles = $this->objSQL->dbQuery($strSQL);
			$arrMoviles=$this->objSQL->dbGetAllRows($objMoviles, 3);
			return $arrMoviles;
		}
		return false;
	}

	function obtenerMovilesUsuario($idUsuario=0,$filtro="",$idUsuarioMovil=0,$idMovil = 0){
		$vistaMovil = $this->getVistaMoviles($_SESSION['idUsuario']);
		$sql = "SELECT mo_id as id, mo_".$vistaMovil ." as dato,um_id,mo_".$vistaMovil ." as movil,mo_matricula
			,mo_identificador,mo_marca, mo_modelo,um_velocidadMaxima, um_id_icono,gm_nombre,um_grupo
			,sh_fechaRecepcion, cl_razonSocial, mo_otros, un_mostrarComo, un_push_id_estado, dbo.tipo_licencia(mo_matricula) as tipo_licencia, dbo.tag_asociado(mo_matricula) as tag,un_tiempo ";
		$sql.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON (mo_id = um_mo_id) ";
		$sql.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON mo_id_cliente_facturar = cl_id ";
		$sql.= " LEFT JOIN tbl_grupos_moviles WITH(NOLOCK) ON (gm_id = um_grupo) ";
		$sql.= " LEFT JOIN tbl_unidad WITH(NOLOCK) ON (mo_id = un_mo_id and un_esPrimaria = 1) ";
		$sql.= " LEFT JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		$sql.= " WHERE mo_borrado = 0 ";

		if($idUsuario){
			$sql.= " AND um_us_id = ".(int)$idUsuario;	
		}
		
		if($idUsuarioMovil){
			$sql.= " AND um_id =".(int)$idUsuarioMovil;	
		}
		
		if(!empty($filtro)){
			$sql.= " AND (mo_identificador like '%".$filtro."%' OR mo_otros like '%".$filtro."%')";	
		}
				
		if($idMovil){
			$sql.= " AND mo_id = ".(int)$idMovil;	
		}
	
		$sql.= " ORDER BY mo_identificador ";	

		$objCarriers = $this->objSQL->dbQuery($sql, false);
		$arrCarriers = $this->objSQL->dbGetAllRows($objCarriers, 3);
		return $arrCarriers;
	}
	
	function obtenerIdMovilesUsuario($idUsuario, $string = false){
		$sql = " SELECT mo_id ";
		$sql.= " FROM tbl_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
		$sql.= " WHERE um_us_id = ".(int)$idUsuario." AND mo_borrado = 0 ";
		$rs = $this->objSQL->dbQuery($sql);
		$result = $this->objSQL->dbGetAllRows($rs,3);
		if($string){
			$arrMoviles = $coma = '';
			foreach($result as $movil){
				$arrMoviles.= $coma.$movil['mo_id'];
				$coma = ',';	
			}
		}
		else{
			$arrMoviles	= $result;
		}
		return $arrMoviles;
	}

	function obtenerMovilesVelocidadMaxima($idUsuario=0,$idMovil=0){
		$strSQL = " SELECT um_velocidadMaxima ";
		$strSQL.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON (mo_id = um_mo_id) ";
		$strSQL.= " WHERE mo_borrado = 0 AND um_us_id = ".(int)$idUsuario;
		$strSQL.= " AND mo_id = ".(int)$idMovil;
		$strSQL.= " ORDER BY mo_identificador ";
		
		$objCarriers = $this->objSQL->dbQuery($strSQL);
		$arrCarriers = $this->objSQL->dbGetRow($objCarriers, 0,3);
		return $arrCarriers;
	}

	function obtenerMovilesUsuarioCombo($idUsuario, $idTransportista = 0){
		$vistaMovil = $this->getVistaMoviles($idUsuario);
		
		$strSQL = " SELECT mo_id as id, mo_".$vistaMovil." as dato ";
		$strSQL.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON (mo_id = um_mo_id) ";
		$strSQL.= " WHERE mo_borrado = 0 ";
		$strSQL.= " AND um_us_id = ".(int)$idUsuario;
		
		if($idTransportista){
			$strSQL.= " AND mo_id_cliente_facturar = ".(int)$idTransportista;
		}
		$strSQL.= " ORDER BY dato ";

		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$arrMoviles = $this->objSQL->dbGetAllRows($objMoviles, 1);
        return $arrMoviles;
	}
	
	function obtenerMovilesClientesCombo($idUsuario, $idCliente = 0, $idDistribuidor=0){
		$vistaMovil = $this->getVistaMoviles($idUsuario);
		
		$strSQL = " SELECT mo_id as id, mo_".$vistaMovil." as dato ";
		$strSQL.= " FROM tbl_moviles WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = mo_id_cliente_facturar ";
		$strSQL.= " WHERE cl_borrado = 0 AND mo_borrado = 0 AND mo_id NOT IN (
								SELECT um_mo_id FROM tbl_usuarios_moviles WITH(NOLOCK) 
								WHERE um_us_id = ".(int)$idUsuario."
						) AND mo_borrado = 0 ";
		
		if($idCliente){
			$strSQL.= " AND mo_id_cliente_facturar = ".(int)$idCliente;
		}
		
		if($idDistribuidor){
		 	$strSQL.= " AND mo_id_distribuidor = ".(int)$idDistribuidor;
		}
		$strSQL.= " ORDER BY dato ";
	
		$obj = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($obj, 3);
	}
	
	function obtenerMovilesConGrupo($idUsuario){
		
		$strSQL = " SELECT mo_id, um_grupo ";
		$strSQL.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON (mo_id = um_mo_id) ";
		$strSQL.= " WHERE mo_borrado = 0 AND um_grupo > 0";
		$strSQL.= " AND um_us_id = ".(int)$idUsuario;
		$strSQL.= " ORDER BY um_grupo ";

		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$arrMoviles = $this->objSQL->dbGetAllRows($objMoviles, 3);
        return $arrMoviles;
	}
		
	function obtenerMovilesPorGrupoListaHistorial($idUsuario){
		$vistaMovil = $this->getVistaMoviles($idUsuario);
		
		$strSQL = " SELECT cl_id as gm_id, cl_abbr as gm_nombre, gm_estado, mo_id, mo_".$vistaMovil." as movil, um_grupo ";
		$strSQL.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_grupos_moviles WITH(NOLOCK) ON (gm_id = um_grupo AND gm_borrado = 0) ";
		$strSQL.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON (mo_id = um_mo_id) ";
		$strSQL.= " LEFT JOIN tbl_unidad WITH(NOLOCK) ON (un_mo_id = mo_id) ";
		$strSQL.= " LEFT JOIN tbl_clientes WITH(NOLOCK) ON (cl_id = mo_id_cliente_facturar)  ";
		$strSQL.= " WHERE um_us_id = ".(int)$idUsuario." 
					AND mo_borrado = 0
					AND un_borrado = 0
					AND un_esPrimaria = 1
					"; //AND cl_habilitado = 1 
		 $strSQL.= " ORDER BY gm_nombre ASC, movil ASC  ";
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$arrMoviles = $this->objSQL->dbGetAllRows($objMoviles, 3);
		return $arrMoviles;
	}

	function obtenerGruposMovilesUsuario($idGrupo, $filtro, $idUsuario,$flagNoRepetido=0){
        $strSQL = " SELECT ";
		if($flagNoRepetido == 1){
			$strSQL.= " distinct(gm_id) ";
		}
		else{
			$strSQL.= " gm_id ";
		}
		$strSQL.= ", gm_nombre ";
		$strSQL.= " FROM tbl_grupos_moviles WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_usuarios_moviles WITH(NOLOCK) ON (gm_id = um_grupo) ";
		$strSQL.= " WHERE gm_borrado= 0 AND um_us_id = ".(int)$idUsuario;
		if($idGrupo > 0){
			$strSQL.= " AND gm_id = ".(int)$idGrupo;
		}
		
		if(!empty($filtro)){
			$strSQL.= " AND gm_nombre like '%".$filtro."%'";
		}
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$arrMoviles = $this->objSQL->dbGetAllRows($objMoviles, 1);
		if ($arrMoviles) {
			return $arrMoviles;
		}
		return array();
	}

	function obtenerMovilesGrupo($idGrupo,$idUsuario,$filtro=""){
		$vistaMovil = $this->getVistaMoviles($idUsuario);
		
		$strSQL = " SELECT mo_id as id, case when mo_otros != mo_matricula then mo_otros + '(' + mo_identificador + ')' else mo_otros end   + ' ' + cl_razonSocial  as dato,um_grupo ";
		//$strSQL = " SELECT mo_id as id, mo_".$vistaMovil." + ' - ' as dato,um_grupo ";
		$strSQL.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON (mo_id = um_mo_id) ";
		$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON (cl_id = mo_id_cliente_facturar) ";
		$strSQL.= " WHERE mo_borrado = 0 AND um_us_id = ".(int)$idUsuario;
		if($idGrupo >= 0){
			$strSQL.= " AND um_grupo = ".(int)$idGrupo;
		}
		if(!empty($filtro)){
			$strSQL.= " AND mo_identificador like '%".$filtro."%'";
		}
		$strSQL.= " ORDER BY dato ";
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$arrMoviles = $this->objSQL->dbGetAllRows($objMoviles, 1);
		return $arrMoviles;
	}

	function obtenerGrupo($id){
		$strSQL = "SELECT * FROM tbl_grupos_moviles WITH(NOLOCK) WHERE gm_id='{$id}'";
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		if($objRow = $this->objSQL->dbGetRow($objMoviles, 0, 1)){
			return $objRow;
		}else{
			return false;
		}
	}

	function crearGrupo($nombre) {
    	$strSQL = "INSERT INTO tbl_grupos_moviles (gm_nombre) VALUES ('{$nombre}')";
        if($this->objSQL->dbQuery($strSQL)){
			return $this->objSQL->dbLastInsertId();
		}
		return false;	
	}
        
	function eliminarMovilesGrupo($idGrupos, $idUsuario){
		if($idGrupos){
			$sql = " UPDATE tbl_usuarios_moviles SET um_grupo=0 WHERE um_grupo ";
			$sql.= " IN (SELECT Data FROM dbo.Split(".$idGrupos.",',')) AND um_us_id = ".(int)$idUsuario;
			if($this->objSQL->dbQuery($sql)) return 1;
			else return 2;
		}
		return 2;
	}

	function insertarMovilGrupo($idGrupo=0, $idUsuario=0, $idMovil=0){
		if($idUsuario && $idMovil){
			$sql = " UPDATE tbl_usuarios_moviles SET um_grupo= ".(int)$idGrupo;
			$sql.= " WHERE um_mo_id = ".(int)$idMovil." AND um_us_id = ".(int)$idUsuario;
			if($this->objSQL->dbQuery($sql)) return 1;
			else return 2;
		}
		return 2;
	}

	//HORARIOS
	function insertarHorarioMovil($id,$dia,$desde,$hasta,$tiempo,$tipo) {
		$strSQL = "	INSERT INTO tbl_unidad_configuracion (uc_un_id, uc_dia, uc_inicio, uc_fin, uc_tiempo, uc_tipo_loc) ";
		$strSQL.= "	VALUES (".(int)$id.", ".(int)$dia.", ".$desde.", ".$hasta.", ".$tiempo.", ".$tipo.") ";
		if($this->objSQL->dbQuery($strSQL)){
			 return $this->objSQL->dbLastInsertId();
		}
		else{
			 return "No se pudo Insertar";
		}
	}
	
	function borrarHorarioMovil($id) {
		$strSQL = " DELETE FROM tbl_unidad_configuracion WHERE uc_id = ".(int)$id;
		if($objReferencia=$this->objSQL->dbQuery($strSQL)){
			return true;
		}
		return false;
	}
	function obtenerHorariosMovil($id=0){ 
		$strSQL = " SELECT uc_id as Id, uc_un_id as Unidad, uc_dia as Dia, uc_inicio as Inicio,uc_fin as Fin, dbo.FormatearTiempoSegundos(uc_tiempo) as Tiempo, uc_tipo_loc as Tipo ";
		$strSQL.= " FROM tbl_unidad_configuracion WITH(NOLOCK) ";
		$strSQL.= " WHERE uc_un_id = ".(int)$id." AND uc_re_id_wp IS NULL ";
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$arrMoviles = $this->objSQL->dbGetAllRows($objMoviles, 1);
		return $arrMoviles;
	}

    function todosCelulares($strMoviles) {
    	$moviles = explode(",", $strMoviles);
        foreach ($moviles as $movil) {
			$infoMovil = $this->obtenerRegistros(trim($movil));
            if ($infoMovil[0]['mo_id_tipo_movil'] != 1) {
            	return false;
            }
        }
		return true;
	}
	
	function obtenerEquiposCombo($idMovil = NULL) {
		$sqlMovil = "";
		if((int)$idMovil){
			$sqlMovil = " OR un_mo_id = ".(int)$idMovil;	
		}
		$sql = " SELECT un_id, un_mostrarComo as equipo, un_mo_id";
		$sql.= " FROM tbl_unidad WITH(NOLOCK) ";
		$sql.= " WHERE (un_mo_id = 0 OR un_mo_id IS NULL ".$sqlMovil.") AND un_borrado = 0";
		if ($_SESSION["idTipoEmpresa"] <= 2) $sql.= " AND un_ds_id={$_SESSION["idEmpresa"]}";
		$objMoviles = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objMoviles);
		return $objRow;
	}
	
	function asignarEquipo($idMovil, $idEquipoNuevo = NULL, $idEquipoViejo = NULL) {
		if((int)$idEquipoViejo){
			$sql = " UPDATE tbl_unidad SET un_mo_id = NULL WHERE un_id = ".(int)$idEquipoViejo;
			$objMoviles = $this->objSQL->dbQuery($sql);
		}
		
		if((int)$idEquipoNuevo){
			$sql = " UPDATE tbl_unidad SET un_mo_id = ".(int)$idMovil." WHERE un_id = ".(int)$idEquipoNuevo;
			$objMoviles = $this->objSQL->dbQuery($sql);
		}
	}
	
	function bajaEquipo($idMovil) {
		$sql = " UPDATE tbl_unidad SET un_mo_id = NULL WHERE un_mo_id = ".(int)$idMovil;
		if($objMoviles = $this->objSQL->dbQuery($sql)){
			return true;
		}
		return false;
	}
	
	function obtenerDatoMovil($id) {
		$strSQL = " SELECT * FROM tbl_moviles WITH(NOLOCK) where mo_id = ".(int)$id;
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$arrMoviles = $this->objSQL->dbGetRow($objMoviles);
		return $arrMoviles;
	}
	
	function guardarConductorEmpresa($idMovil, $idConductor, $idClienteFacturar){
		if($idMovil && $idConductor && $idClienteFacturar){			
			$sql = " UPDATE tbl_moviles ";
			$sql.= " SET mo_co_id_primario = ".(int)$idConductor;
			$sql.= " , mo_id_cliente_facturar = ".(int)$idClienteFacturar;
			$sql.= " WHERE mo_id = ".(int)$idMovil;
			if($this->objSQL->dbQuery($sql)) return true;
		}
		return false;
	}
	
	function limpiarConductorEmpresa($idConductor){
		if($idConductor){			
			$sql = " UPDATE tbl_moviles ";
			$sql.= " SET mo_co_id_primario = NULL";
			$sql.= " WHERE mo_co_id_primario = ".(int)$idConductor;
			if($this->objSQL->dbQuery($sql)) return true;
		}
		return false;
	}
	
	function obtenerMovilAsignadoAlConductor($id){
		$strSQL = " SELECT TOP 1 mo_id FROM tbl_moviles WITH(NOLOCK) WHERE mo_co_id_primario = ".(int)$id;
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$arrMoviles = $this->objSQL->dbGetRow($objMoviles);
		return $arrMoviles;
	}
	
	function tiposUnidad($strMoviles){

		$tipos = array(
			'celular' => 0,
			'auto' => 0,
			'vehiculo' => 0, // en realidad son CAMIONES
			'token' => 0,
			'caja' => 0,
			'semi' => 0,
			'satelital' => 0
		);
	
		$moviles = explode(",", $strMoviles);
		foreach ($moviles as $_movil) {
			$movil = $this->obtenerDatoMovil($_movil);
			
			switch ($movil['mo_id_tipo_movil']) {
				case 1:
					$tipos['celular']++;
				break;
				case 2:
					$tipos['auto']++;
				break;
				case 3:
					$tipos['vehiculo']++;
				break;
				case 5:
					$tipos['token']++;
				break;
				case 6:
					$tipos['caja']++;
				break;
				case 7:
					$tipos['satelital']++;
				break;
				case 8:
					$tipos['semi']++;
				break;
			}
	
	
			// Me fijo si hay alguna combinacion de tipos 
			// de moviles o son todos iguales.
			$q1 = 0;
			foreach ($tipos as $k => $v) {
				if ($v > 0) {
					$actual = $k;
					$q1++;
				}
			}
	
			// Si hay 2 tipos distintos, ya no tiene mucho sentido averiguar el
			// tipo de movil de los moviles restantes.
			if ($q1 > 1) {
				if ($q1 == count($tipos) - 1) {
					if ($tipos['vehiculo'] > 0) {
						return 'vehiculo';
					} else if ($tipos['auto'] > 0) {
						return 'auto';
					} else if ($tipos['caja'] > 0) {
						return 'caja';
					} else if ($tipos['celular'] > 0) {
						return 'celular';
					} else {
						return 'token';
					}
				}
			}
		}
	
		if ($q1 == 1) {
			return $actual;
		} else {
			return 'error';
		}
	}
	
	function setVelocidadMax($idMovil, $velmax){
		if($idMovil){
			$sql = " UPDATE tbl_usuarios_moviles SET um_velocidadMaxima = ".(int)$velmax." WHERE um_mo_id = ".(int)$idMovil;
			if($this->objSQL->dbQuery($sql)){
				return true;	
			}
		}
		return false;
	}
	
	function asignarMovilesUsuarios($idUsuario, $arrAltaAsignacion = NULL, $arrBajaAsignacion = NULL){
		if(is_array($arrBajaAsignacion)){
			if(count($arrBajaAsignacion)){
				$strSQL = " DELETE FROM tbl_usuarios_moviles WHERE um_us_id = ".(int)$idUsuario." AND um_mo_id IN (".implode(',',$arrBajaAsignacion).") ";
				if(!$this->objSQL->dbQuery($strSQL)){
					return false;	
				}
			}	
		}
		
		if(is_array($arrAltaAsignacion)){
			if(count($arrAltaAsignacion)){
				foreach($arrAltaAsignacion as $idMovil){
					if(!$this->insertarAsignacionMovilUsuario($idUsuario,$idMovil)){
						return false;	
					}
				}
			}
		}
		
		return true;
	}
	
	
	function asignarMovilAUsuaruariosTransportistas($mo_id, $idTransportista){
		$strSQL = " DELETE FROM tbl_usuarios_moviles 
					WHERE um_mo_id = ".(int)$mo_id." 
					AND um_us_id NOT IN (SELECT us_id FROM tbl_usuarios WITH(NOLOCK) 
											WHERE us_cl_id IN (".(int)$_SESSION['idAgente'].",".(int)$idTransportista.") 
											AND us_borrado = 0)";
		if($this->objSQL->dbQuery($strSQL)){
			$strSQL = " INSERT INTO tbl_usuarios_moviles(um_us_id, um_mo_id)
						SELECT us_id, ".(int)$mo_id." FROM tbl_usuarios WITH(NOLOCK) where us_cl_id = ".(int)$idTransportista." AND us_borrado = 0
						AND us_id NOT IN (SELECT um_us_id FROM tbl_usuarios_moviles WITH(NOLOCK) WHERE um_mo_id = ".(int)$mo_id.") ";
			$this->objSQL->dbQuery($strSQL);
		}		 	
	}
	
}