<?php
require_once 'clases/clsMoviles.php';
class Rastreo extends Movil {

    function Rastreo($objSQLServer) {
        $this->objSQL = $objSQLServer;
        return TRUE;
    }

    function obtenerReportesMovilesUsuario($idUsuario = 0, $idMovil = 0, $esActualizacion = 0, $orderingCriteria = 0, $sinLimite = false) {
        
		$vistaMovil = $this->getVistaMoviles((int)$idUsuario);
		
		require_once 'clases/clsIdiomas.php';
		$objIdioma = new Idioma();
		$eventos = $objIdioma->getEventos($_SESSION['idioma']);
		
		$strSQL = " DECLARE @temp_eventos TABLE (id INT,evento VARCHAR(50))";
		foreach($eventos->children() as $k => $ev){
			$idEv = explode('_',$k);
			if($idEv[1]){
				$strSQL.= " INSERT INTO @temp_eventos VALUES(".(int)$idEv[1].", '".trim($ev)."') ";
			}
		}
		
		$strSQL.= "  SELECT 
			DISTINCT(tbl_moviles.mo_id) AS mo_id, sh_latitud, sh_longitud 
			, mo_".$vistaMovil." as movil
			, mo_id_tipo_movil, dg_curso as curso
			, dbo.obtenerSentido(dg_curso) as dg_curso 
			,dbo.EnvioMail(sh_fechaMailAlerta) as flagEnvioMail
			,der.dr_valor
			, dg_velocidad
			, sh_fechaGeneracion
			,gm_estado
			, ISNULL(evento,'".$eventos->default->__toString()." ('+CONVERT(VARCHAR,der.dr_id)+')') as tr_descripcion 
			, mo_motor_encendido, mod.mo_bit_motor, dbo.enteroBinario(dbo.funHexaToInt(dg_entradas)) byteEncendido, der.dr_id as tr_id_reporte
			,sh_estado_gps,sh_estado_wifi,sh_senial,sh_presicion,sh_wifi_name
			, dg_entradas as entradas 
		";
		if($orderingCriteria == 1 || $orderingCriteria == 3){ //ordeno por cliente a facturar
			$strSQL.= " ,cl.cl_id as um_grupo
					,CASE 
					WHEN fletero.cl_abbr IS NOT NULL 
						THEN (CASE WHEN cl.cl_abbr != '' THEN cl.cl_abbr ELSE cl.cl_razonsocial END)+' - '+fletero.cl_abbr 
						ELSE (CASE WHEN cl.cl_abbr != '' THEN cl.cl_abbr ELSE cl.cl_razonsocial END) 
					END as gm_nombre 
			";
		}
		elseif($orderingCriteria == 2){ //ordeno por marca de equipo
			$strSQL.= " ,mod.mo_id as um_grupo, mod.mo_nombre as gm_nombre ";
		}
		elseif($orderingCriteria == 5){ //--Definición para Forza-Arauco
			$strSQL.= " ,'99' as um_grupo, 'Moviles' as gm_nombre	";
		}
		else{//ordeno por grupo de usuario
			$strSQL.= " ,um_grupo, gm_nombre ";
		}

		if($idMovil > 0){
			$strSQL.= "  
				,dg_infoGPS as sh_datoGPS
				, sh_fechaRecepcion
				, un_mostrarComo, dr_gasoilConsumido as edadgps
				, cl.cl_razonsocial as nombreEmpresa, cl.cl_telefono as telEmpresa
				, mo_matricula, mo_marca, mo_modelo, mo_identificador,mo_otros
				, um_velocidadMaxima, dbo.obtenerSentido(dg_curso) as dg_curso
				, un_mostrarComo, der.dr_id,der.dr_icono
				, dg_entradas as entradas, un_me_id, un_nro_serie 
				,co1.co_nombre+' '+co1.co_apellido+(case when co1.co_telefono IS NOT NULL then ' tel:'+ co1.co_telefono else null END) as conductor1 
				,co2.co_nombre+' '+co2.co_apellido+(case when co2.co_telefono IS NOT NULL then ' tel:'+ co2.co_telefono else null END) as conductor2
				,mo_aux1
				, dr_telemetria_1, dr_telemetria_2, dr_telemetria_3 ";
		}
		
		$strSQL.= " 
			FROM tbl_usuarios_moviles WITH(NOLOCK)	
			INNER JOIN tbl_moviles WITH(NOLOCK) ON (tbl_moviles.mo_id = um_mo_id)
			INNER JOIN tbl_clientes cl WITH(NOLOCK) ON (cl.cl_id =  mo_id_cliente_facturar)
			INNER JOIN tbl_unidad WITH(NOLOCK) ON (un_mo_id = um_mo_id)
			INNER JOIN tbl_sys_heart WITH(NOLOCK) ON (sh_un_id = un_id)
			INNER JOIN tbl_dato_gp WITH(NOLOCK) ON (dg_sh_id = sh_id)
			INNER JOIN tbl_dato_rax as dar WITH(NOLOCK) ON (dar.dr_sh_id = sh_id)
			INNER JOIN tbl_definicion_reportes der WITH(NOLOCK) ON (der.dr_id = sh_rd_id)
			LEFT JOIN @temp_eventos ON der.dr_id = id
			INNER JOIN tbl_modelos_equipo mod WITH(NOLOCK) ON tbl_unidad.un_mod_id = mod.mo_id
			LEFT JOIN tbl_grupos_moviles WITH(NOLOCK) ON (gm_id = um_grupo)
			LEFT JOIN tbl_clientes fletero WITH(NOLOCK) ON fletero.cl_id = cl.cl_id_fletero
		";
		
		if($idMovil > 0){
			$strSQL.= " 
				LEFT JOIN tbl_conductores co1 WITH(NOLOCK) on (tbl_moviles.mo_co_id_primario = co1.co_id AND co1.co_borrado = 0)
				LEFT JOIN tbl_conductores co2 WITH(NOLOCK) on (tbl_moviles.mo_co_id_secundario = co2.co_id AND co2.co_borrado = 0)
			";
		}
		
		$strSQL.= "  
			WHERE tbl_moviles.mo_borrado = 0
			AND un_borrado = 0
			AND dr_borrado = 0	
			AND un_esPrimaria = 1 
			AND der.dr_id != 79 
		";		
		
		if($idUsuario > 0){
			$strSQL.= " AND um_us_id = ".(int)$idUsuario;
		}
		
		if($idMovil > 0){
			$strSQL.= " AND um_mo_id = ".(int)$idMovil;
		}
		
		if(!$sinLimite && $orderingCriteria != 3){
			if($esActualizacion != 1){ 
				$strSQL.= " AND (sh_fechaRecepcion > (SELECT DATEADD(hour,server,GETDATE()) FROM zonaHoraria(NULL,".(int)$_SESSION['idUsuario'].")) - 7) ";
				///le agregamos current_timestamp - 7 para que en rastreo no traiga si no reporto hace mas de 7 días
			}
			elseif($esActualizacion == 1){ 
				$strSQL.= " AND (sh_fechaRecepcion > (SELECT DATEADD(hour,server,GETDATE()) FROM zonaHoraria(NULL,".(int)$_SESSION['idUsuario'].")) - 0.0017361) ";
			}
		}
		
		$strSQL.= " ORDER BY gm_nombre ASC, movil ASC ";	
		
		$objRastreos = $this->objSQL->dbQuery($strSQL);
        $objRow = $this->objSQL->dbGetAllRows($objRastreos, 3);
		
		return $objRow;
    }

    function getSenalGPS($id_edad = 0, $posicion = 0){
		if($id_edad == 0 && $posicion == 3){
			return 'buena';	
		}
		elseif($id_edad == 0 && ($posicion == 2 || $posicion == 1)){
			return 'regular';	
		}
		else{
			return 'mala';	
		}
	}
	
	function getPosicionPto($idPto) {
		$sql = "";
		if((int)$idPto > 0){//-- Es Movil --//
			$sql = " SELECT sh_latitud, sh_longitud FROM tbl_sys_heart sh WITH(NOLOCK) ";
			$sql.= " INNER JOIN tbl_unidad un WITH(NOLOCK) ON un.un_id = sh.sh_un_id ";
			$sql.= " INNER JOIN tbl_moviles mo WITH(NOLOCK) ON mo.mo_id = un.un_mo_id ";
			$sql.= " WHERE mo_id = ".(int)$idPto;
		}
		
		if((int)$idPto < 0){//-- Es Referencia --//
			$idPto = $idPto * -1;
			$sql = " SELECT rc_latitud as sh_latitud, rc_longitud as sh_longitud ";
			$sql.= " FROM tbl_referencias_coordenadas WITH(NOLOCK) WHERE rc_re_id = ".(int)$idPto;
		}
		
		if($sql != ""){
        	$objRastreos = $this->objSQL->dbQuery($sql);
        	$objRow = $this->objSQL->dbGetAllRows($objRastreos);
        	return $objRow[0];
		}
		return false;
    }
	
	function getDataPicture($strMoviles){
		require_once 'clases/clsIdiomas.php';
		$objIdioma = new Idioma();
		$eventos = $objIdioma->getEventos($_SESSION['idioma']);
		
		$sql = " DECLARE @temp_eventos TABLE (id INT,evento VARCHAR(50))";
		foreach($eventos->children() as $k => $ev){
			$idEv = explode('_',$k);
			if($idEv[1]){
				$sql.= " INSERT INTO @temp_eventos VALUES(".(int)$idEv[1].", '".trim($ev)."') ";
			}
		}
		
		$sql.= " SELECT mo.mo_id, mo_id_tipo_movil, mo_matricula, mo_bit_motor, mo_motor_encendido ";
		$sql.= " ,sh_latitud, sh_longitud, sh_fechaRecepcion ";
		$sql.= " ,dg_velocidad ,dbo.enteroBinario(dbo.funHexaToInt(dg_entradas)) byteEncendido, dg_curso as curso ";
		$sql.= " , ISNULL(evento,'".$eventos->default->__toString()." ('+CONVERT(VARCHAR,der.dr_id)+')') as tr_descripcion  ";
		$sql.= " FROM tbl_moviles mo WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id ";
		$sql.= " INNER JOIN tbl_modelos_equipo me WITH(NOLOCK) ON me.mo_id = un_mod_id ";
		$sql.= " INNER JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		$sql.= " INNER JOIN tbl_dato_gp WITH(NOLOCK) ON dg_sh_id = sh_id ";
		$sql.= " INNER JOIN tbl_definicion_reportes der WITH(NOLOCK) ON (der.dr_id = sh_rd_id) ";
		$sql.= " LEFT JOIN @temp_eventos ON der.dr_id = id ";
		$sql.= " WHERE mo.mo_id IN (".$strMoviles.")";
		$objRastreos = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetAllRows($objRastreos);
        return $objRow;
	}
	
	function getBuscadorMovilesReferencias($txtBuscar){
		global $lang;
		$idUsuario = $_SESSION['idUsuario'];
		$idEmpresa = $_SESSION["idEmpresa"];
		
		$vistaMovil = $this->getVistaMoviles((int)$idUsuario);	
			
		$sql = " (";
		$sql.= " SELECT DISTINCT(tbl_moviles.mo_id) AS id, mo_".$vistaMovil." as valor ";
		$sql.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON (tbl_moviles.mo_id = um_mo_id) ";
		$sql.= " INNER JOIN tbl_clientes cl WITH(NOLOCK) ON (cl.cl_id =  mo_id_cliente_facturar) ";
		$sql.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON (un_mo_id = um_mo_id) ";
		$sql.= " WHERE tbl_moviles.mo_borrado = 0 AND un_borrado = 0 AND un_esPrimaria = 1 AND um_us_id = ".(int)$idUsuario;
		$sql.= " AND mo_".$vistaMovil." LIKE '%".$txtBuscar."%'";
		$sql.= " )UNION( ";
		$sql.= " SELECT DISTINCT(re.re_id) as id,
				CASE WHEN (re.re_inteligente = 1 AND re.re_validar_usuario = 0) THEN '".($lang->system->punto_interes?$lang->system->punto_interes->__toString():'punto_interes')."' ELSE re.re_nombre END AS valor ";
		$sql.= " FROM tbl_referencias re WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios us WITH(NOLOCK) ON (us.us_id = re.re_us_id) ";
		$sql.= " WHERE re.re_borrado = 0 ";
		$sql.= " AND ((re.re_validar_usuario IS NULL AND re.re_inteligente = 0) OR (re.re_inteligente = 1 AND re.re_validar_usuario != 2)) ";
		$sql.= " AND us.us_cl_id = ".(int)$idEmpresa;
		$sql.= " AND re_nombre like '%".$txtBuscar."%' ";
		$sql.= " )";
		$sql.= " ORDER BY valor ASC "; 
		
		$objRastreos = $this->objSQL->dbQuery($sql);
        $objRow = $this->objSQL->dbGetAllRows($objRastreos);
        return $objRow;
	}
}
?>
