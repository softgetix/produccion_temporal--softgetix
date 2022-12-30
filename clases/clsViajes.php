<?php
if (!isset($rel)) { $rel = ""; }
require_once $rel.'clases/clsAbms.php';

class Viajes extends Abm {
	function __construct($objSQLServer, $id_viaje = 0) {
		parent::__construct($objSQLServer,'tbl_viajes','vi');
		$this->objSQL = $objSQLServer;
		$this->id_viaje = (int)$id_viaje;
		$this->velocidad_promedio = 70;
		$this->hora = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','00');
		$this->min = array('00','10','20','30','40','50');
	}
	
	function getListadoViajes($filtros = NULL, $exportacion = false){
		
		//, DATEDIFF(ss, vd_ini, vd_ini_real) as diferenciaIngreso, DATEDIFF(ss, vd_fin, vd_fin_real) as diferenciaEgreso -->sE REMPLAZO POR CAST A BIGINT XQ SE RIMPIA CON FECHAS FUTURAS
		$selectTop = '';
		if($filtros['filtrar_rows']){
			$selectTop = ' TOP 30 ';
		}
		
		$sql= " SELECT DISTINCT ".$selectTop." trans.cl_razonSocial as transportista,  dador.cl_razonSocial as dador, vi_id, vi_codigo
		, us.us_nombreUsuario as us_nombreUsuario, mo.mo_matricula as vi_movil, re_id, re_nombre, re_numboca, re_rg_id, vd_stock
		, dbo.Pallets_por_viaje_destino (vd_id) as vd_stock_function,  dbo.Pallets_estado_int (vi_id, re_id) as vd_checked_function
		, dbo.Pallets_administracion_permiso (".(int)$_SESSION['idUsuario'].") as permiso_vales_asociados
		, (CAST(DATEDIFF(MINUTE,vd_ini,vd_ini_real)AS BIGINT) * 60) as diferenciaIngreso, (CAST(DATEDIFF(MINUTE, vd_fin,vd_fin_real)AS BIGINT) * 60) as diferenciaEgreso
		, vd.vd_orden
		, (co_nombre + ' ' + co_apellido ) as co_conductor, co_telefono, vi_finalizado
		, rc_latitud, rc_longitud, sh_latitud, sh_longitud, sh_fechaRecepcion, sh_fechaGeneracion";
		$sql.= " , vd_id , vd_ini, vd_fin, vd_ini_real, vd_fin_real, re_vel_promedio, sh_rd_id, dr_valor";
		$sql.= " ,trans.cl_id as id_transportista, dador.cl_id as id_dador, co_id as id_conductor, mo.mo_id as id_movil, re_descripcion ";
		$sql.= " ,um_us_id, vi_contenedor, vd.vd_checked, dbo.Pallets_entregados_url (".(int)$_SESSION['idAgente'].",vd_id) as 'link'"; //-- verifico si tiene movil asignado para visalizar mapa --//
		
		if($exportacion == true ){
			##-- obtengo cual es mi ultima referencia visitada --##
			$sql.= " ,(SELECT TOP 1 re_nombre FROM tbl_viajes_destinos r1 WITH(NOLOCK)
					INNER JOIN tbl_viajes_destinos r2  WITH(NOLOCK)	ON (r1.vd_vi_id = r2.vd_vi_id AND r2.vd_orden < r1.vd_orden";
			$sql.= ") 
					INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = r2.vd_re_id 
					WHERE r1.vd_id = vd.vd_id ORDER BY r2.vd_orden DESC
					) as ";
			$sql.= "procedencia ";
			##-- --##
		
			$sql.= " ,(SELECT TOP 1 re_nombre FROM tbl_viajes_destinos r1 WITH(NOLOCK) ";
			$sql.= " 	INNER JOIN tbl_viajes_destinos r2 WITH(NOLOCK) ON (r1.vd_vi_id = r2.vd_vi_id AND r2.vd_orden > r1.vd_orden) ";
			$sql.= " 	INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = r2.vd_re_id "; 
			$sql.= " 	WHERE r1.vd_id = vd.vd_id ORDER BY r2.vd_orden DESC) as destino ";
			
			$sql.= " ,(SELECT re2.re_nombre+' '+re2.re_descripcion 
					FROM tbl_viajes_destinos vd2 WITH(NOLOCK) 
					INNER JOIN tbl_referencias re2 WITH(NOLOCK) ON re2.re_id=vd2.vd_re_id 
					WHERE vd2.vd_vi_id=vi.vi_id AND vd2.vd_orden=0) as o_origen ";
			
			$sql.= " ,(SELECT vd2.vd_ini FROM tbl_viajes_destinos vd2 WITH(NOLOCK) WHERE vd2.vd_vi_id=vi.vi_id AND vd2.vd_orden = 0) AS o_ini ";
			$sql.= " ,(SELECT vd2.vd_ini_real FROM tbl_viajes_destinos vd2 WITH(NOLOCK) WHERE vd2.vd_vi_id=vi.vi_id AND vd2.vd_orden = 0) AS o_ini_real ";
			$sql.= " ,(SELECT vd2.vd_fin FROM tbl_viajes_destinos vd2 WITH(NOLOCK) WHERE vd2.vd_vi_id=vi.vi_id AND vd2.vd_orden = 0) AS o_fin ";
			$sql.= " ,(SELECT vd2.vd_fin_real FROM tbl_viajes_destinos vd2 WITH(NOLOCK) WHERE vd2.vd_vi_id=vi.vi_id AND vd2.vd_orden =0 ) AS o_fin_real ";
			
			$sql.= " ,(SELECT CAST(DATEDIFF(MINUTE,vd2.vd_ini,vd2.vd_ini_real)AS BIGINT) * 60 FROM tbl_viajes_destinos vd2 WITH(NOLOCK) WHERE vd2.vd_vi_id = vi.vi_id AND vd2.vd_orden = 0) as o_diferenciaIngreso ";
			$sql.= " ,(SELECT CAST(DATEDIFF(MINUTE,vd2.vd_fin,vd2.vd_fin_real)AS BIGINT) * 60 FROM tbl_viajes_destinos vd2 WITH(NOLOCK) WHERE vd2.vd_vi_id = vi.vi_id AND vd2.vd_orden = 0) as o_diferenciaEgreso ";
			$sql.= " ,(SELECT CAST(DATEDIFF(MINUTE,vd2.vd_ini_real,vd2.vd_fin_real)AS BIGINT) * 60 FROM tbl_viajes_destinos vd2 WITH(NOLOCK) WHERE vd2.vd_vi_id = vi.vi_id AND vd2.vd_orden = 0) as o_diferencia ";
			$sql.= " ,(CAST(DATEDIFF(MINUTE,vd_ini_real,vd_fin_real)AS BIGINT) * 60) as d_diferencia ";
			//$sql.= " ,(SELECT DATEDIFF(ss, vd2.vd_ini, vd2.vd_ini_real) FROM tbl_viajes_destinos vd2 WHERE vd2.vd_vi_id = vi.vi_id  AND vd2.vd_orden = 0) as o_diferenciaIngreso ";
			//$sql.= " ,(SELECT DATEDIFF(ss, vd2.vd_fin, vd2.vd_fin_real) FROM tbl_viajes_destinos vd2 WHERE vd2.vd_vi_id = vi.vi_id AND vd2.vd_orden = 0) as o_diferenciaEgreso ";
			//$sql.= " ,(SELECT DATEDIFF(ss, vd2.vd_ini_real, vd2.vd_fin_real) FROM tbl_viajes_destinos vd2 WHERE vd2.vd_vi_id = vi.vi_id AND vd2.vd_orden = 0) as o_diferencia ";
			//$sql.= " ,DATEDIFF(ss, vd_ini_real, vd_fin_real) as d_diferencia ";
		}
				
		$sql.= " FROM tbl_viajes vi WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes_destinos vd WITH(NOLOCK) ON vd.vd_vi_id = vi.vi_id ";
		$sql.= " INNER JOIN tbl_referencias re WITH(NOLOCK) ON re.re_id = vd.vd_re_id ";
		$sql.= " LEFT JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON re_id = rc_re_id ";
		$sql.= " left JOIN tbl_usuarios us WITH(NOLOCK) ON us.us_id = vi.vi_us_id ";
		$sql.= " LEFT JOIN tbl_clientes trans WITH(NOLOCK) ON vi.vi_transportista = trans.cl_id "; //--pASO A SER LEFT JOIN PORQ SIST. PALLETS NO TIENE TRANSP.
		
		$sql.= " LEFT JOIN tbl_moviles mo WITH(NOLOCK) ON mo.mo_id = vi.vi_mo_id ";
		$sql.= " LEFT JOIN tbl_unidad WITH(NOLOCK) ON vi.vi_mo_id = un_mo_id ";
		$sql.= " LEFT JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		$sql.= " LEFT JOIN tbl_definicion_reportes WITH(NOLOCK) ON dr_id = sh_rd_id ";
		
		$sql.= " LEFT JOIN tbl_clientes dador WITH(NOLOCK) ON vi.vi_dador = dador.cl_id ";
		$sql.= " LEFT JOIN tbl_conductores cond WITH(NOLOCK) ON cond.co_id = vi.vi_co_id ";
		
		$sql.= " LEFT JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_us_id = ".(int)$_SESSION['idUsuario']." AND um_mo_id = vi.vi_mo_id";
		
		$sql.= $this->filtrosViajes($filtros);
		
		$sql.= " ORDER BY vi_codigo , vi_id  , vd_ini ASC "; 



		$objRes=$this->objSQL->dbQuery($sql);	
		$res=$this->objSQL->dbGetAllRows($objRes,3);
		return $res;
	}
	
	function filtrosViajes($filtros){
		if(!empty($filtros['f_ini'])){
			$filtros['f_ini'].= ' 00:00:00';
		}
		if(!empty($filtros['f_fin'])){
			$filtros['f_fin'].= ' 23:59:59';
		}
		
		$sql = " WHERE vi_borrado = 0 AND vi_delivery = 0 ";
		// esto es para que MSC vea solo el dato del cliente en el listado.
		$sql.= " and  re_rg_id =  case when vi_vt_id in (29,30) then 120 else re_rg_id end  ";

		if(!empty($filtros['f_ini'])){
			$sql.= " AND vd_ini >= '".$filtros['f_ini']."' ".(!empty($filtros['f_fin'])?" AND vd_ini <= '".$filtros['f_fin']."'":'');
		}

		if(!empty($filtros['buscar'])){
			$sql.= " AND (vi_codigo LIKE '%".$filtros['buscar']."%' OR vd_id LIKE '%".$filtros['buscar']."%')";
		}

		if(isset($filtros['vt_id']) && !empty($filtros['vt_id'])){
			$sql.= " AND vi.vi_vt_id = {$filtros['vt_id']} ";
		}
		
		/**/
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
		$res = $this->objSQL->dbGetAllRows($objRes,3);
		
		//Si el cl_tipo es 2 (cliente), filtro por dador o transportista
		if($res[0]['tipoEmpresa'] == 1 && $res[0]['tipoCliente'] == 1){ //tipoEmpresa Agente && tipoCliente Dador
			$sql.= "AND vi.vi_dador = ".$res[0]['empresa']." ";
		}
		
		if($res[0]['tipoEmpresa'] == 1 && $res[0]['tipoCliente'] != 1){ //tipoEmpresa Agente && tipoCliente no es dador
			$sql.= "AND trans.cl_id_distribuidor = ".$res[0]['empresa']." ";
		}
		
		if($res[0]['tipoEmpresa'] == 2){
			
			
		/*
			$sql.= "AND vi.vi_dador = 
					CASE ".$res[0]['tipoCliente']."
						WHEN  1 THEN  ".$res[0]['empresa']."  -- ( DADOR )
						WHEN  2 THEN  vi.vi_dador 			  -- TRANSPORTISTA
						ELSE  vi.vi_dador         			  -- LOCALIZART O AGENTE
					END
					AND trans.cl_id = 
					CASE ".$res[0]['tipoCliente']."
						WHEN  1 THEN trans.cl_id 			-- DADOR
						WHEN  2 THEN ".$res[0]['empresa']." -- ( TRANSPORTISTA )
						ELSE trans.cl_id         			-- LOCALIZART O AGENTE
					END	
					";
		
		*/

			$sql.= "AND ( ( vi.vi_dador = ".$res[0]['agente']. " AND vi_transportista = ".$res[0]['empresa']. ") or ( vi.vi_dador = ".$res[0]['agente']. " AND vi_transportista is NULL  )  )   ";

		}
		
		##-- FILTROS COL --##
		if(!empty($filtros['transportista'])){
			if(strpos($filtros['transportista'],',-1')){
				$sql.= " AND (trans.cl_id IN(".$filtros['transportista'].") OR trans.cl_id IS NULL)";
			}
			elseif($filtros['movil'] == '-1'){
				$sql.= " AND trans.cl_id IS NULL";
			}
			else{
				$sql.= " AND trans.cl_id IN(".$filtros['transportista'].")";
			}
		}

		if(!empty($filtros['dador'])){
			if(strpos($filtros['dador'],',-1')){
				$sql.= " AND (dador.cl_id IN(".$filtros['dador'].") OR dador.cl_id IS NULL)";
			}
			else{
				$sql.= " AND dador.cl_id IN(".$filtros['dador'].")";
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
		
		if(!empty($filtros['ini'])){
			switch($filtros['ini']){
				case '1': //Realizado
					if ($filtros['vt_id'] == 29)
					{
					$sql.= " AND vd_checked != 1 ";
					} else {
					$sql.= " AND vd_fin_Real IS NOT NULL ";
					}
				break;
				case '2': //En Curso
					if ($filtros['vt_id'] == 29)
					{
					$sql.= " AND vd_checked = 1 ";
					} else {
					$sql.= " AND vd_fin_Real IS NULL  ";
					}
				break;
			}
		}

		if(!empty($filtros['iniReal'])){
			switch($filtros['iniReal']){
				case '1': //Ingreso Realizado
					//$sql.= " AND vd_ini_real IS NOT NULL ";
				break;
				case '2': //Ingreso Pendiente
					//$sql.= " AND vd_ini_real IS NULL  ";
				break;
			}
		}
		
		if(!empty($filtros['finReal'])){
			switch($filtros['finReal']){
				case '1': //Egreso Realizado
					
					$sql.= " AND vd_fin_real IS NOT NULL ";
					
				break;
				case '2': //Egreso Pendiente
					
					$sql.= " AND vd_fin_real IS NULL ";
					
				break;
			}
		}
		
		if(!empty($filtros['facturado'])){
			switch($filtros['facturado']){
				case '1': //Facturado
					$sql.= " AND vi_facturado = 1  ";
				break;
				case '2': //No facturado
					$sql.= " AND (vi_facturado IS NULL OR vi_facturado = 0) ";
				break;
			}
		}
		##-- --##
		
		##-- Arribos y Partidas --##
		if(!empty($filtros['arribo'])){
			switch($filtros['arribo']){
				case '1': //Ingreso Realizado - Atrasado
					$sql.= " AND DATEDIFF(ss, vd_ini, vd_ini_real) > 0 ";
				break;
				case '2': //Ingreso Realizado - En Tiempo
					$sql.= " AND DATEDIFF(ss, vd_ini, vd_ini_real) <= 0 ";
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
						$aux.= $or." DATEDIFF(ss, vd_fin, vd_fin_real) > 0 ";
					break;
					case '2': //Egreso Realizado - En Tiempo
						$aux.= $or." DATEDIFF(ss, vd_fin, vd_fin_real) <= 0 ";
					break;
					case '3': //Egreso Pendiente - Atrasado
						$aux.= $or." (DATEDIFF(ss, vd_fin, '".getFechaServer('Y-m-d H:i')."') > 0  AND vd_fin_real IS NULL)";
					break;
					case '4': //Egreso Pendiente - En Tiempo
						$aux.= $or." (DATEDIFF(ss, vd_fin, '".getFechaServer('Y-m-d H:i')."') <= 0  AND vd_fin_real IS NULL)";
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
		##-- --##

		if(isset($filtros['checked'])){
			if(!empty($filtros['checked'])){
				$sql.= " AND (vd.vd_checked IN (".$filtros['checked'].")";
				if(strpos($filtros['checked'], '0') !== false){
					$sql.= " OR vd.vd_checked IS NULL "; 	
				}
				$sql.= ")"; 
			}
			elseif($filtros['checked'] == '0'){
				$sql.= " AND (vd.vd_checked IS NULL OR vd.vd_checked = 0)";
			}
		}		
		
		return $sql;
	}
	
	function tieneMovilAsignado($id_movil){
		$sql = " SELECT COUNT(*) as cant FROM tbl_usuarios_moviles WITH(NOLOCK) WHERE um_us_id = ".(int)$_SESSION['idUsuario']." AND um_mo_id = ".(int)$id_movil;
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);	
		if($rs['cant']){
			return 	true;
		}
		return false;
	}
	
	function getTiempoHM($segundos, $abrev = true){
		global $lang;
		$estadia['min'] = ($segundos/60);
		$estadia['hora'] = (int)($estadia['min']/60);										
		if($estadia['hora'] > 0){
			$min = $estadia['hora'] * 60;
			$estadia['min'] = $estadia['min'] - $min;	
		}
										
		if($estadia['hora'] && $estadia['min']){
			$tiempo = round($estadia['hora'],0).":".((round($estadia['min']) < 9)?'0':'').round($estadia['min'],0).($abrev?$lang->system->abrev_hora:'');	
		}
		elseif($estadia['hora']){
			$tiempo = round($estadia['hora'],0).($abrev?$lang->system->abrev_hora:'');
		}
		elseif($estadia['min']){
			$tiempo = round($estadia['min'],0).($abrev?$lang->system->abrev_minutos:'');		
		}
		
		return $tiempo;	
	}
	
	function obtenerCondutoresPorEmpresa($idEmpresa){
		
		$strSQL = " SELECT cl_tipo FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = ".(int)$idEmpresa;
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$rs = $this->objSQL->dbGetRow($objRes,0 ,3);
		$tipoCliente = $rs['cl_tipo'];
			
		$strSQL = " SELECT co.*,cl.cl_razonSocial as razon_social ";
		$strSQL.= " FROM tbl_conductores co WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_clientes cl WITH(NOLOCK) ON (cl.cl_id = co.co_cl_id) ";
		$strSQL.= " WHERE co_borrado = 0 ";
		
		if($tipoCliente == 1){
			$strSQL.= " AND cl_id_distribuidor = ".(int)$idEmpresa;
		}
		
		if($tipoCliente == 2){
			$strSQL.= " AND co.co_cl_id = ".(int)$idEmpresa;
		}
								
		$strSQL.= " ORDER BY co_nombre ";
		
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$res = $this->objSQL->dbGetAllRows($objRes);
		return $res;
	}
	
	function obtenerMovilesUsuario($transportista = 0, $idUsuario = 0) {
		
		if (isset($_SESSION['idUsuario'])) {
			$idUsuario = $_SESSION['idUsuario'];	
		}
		
		$sql = " SELECT mo_id as id, mo_matricula as dato ";
		$sql.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON (mo_id = um_mo_id) ";
		$sql.= " WHERE mo_borrado = 0 ";
		$sql.= "  AND um_us_id = ".(int)$idUsuario;
		if($transportista){
			$sql.= " AND mo_id_cliente_facturar = ".(int)$transportista;}
		$sql.= " ORDER BY dato ";
		$objRes=$this->objSQL->dbQuery($sql);	
		$res=$this->objSQL->dbGetAllRows($objRes,3);
		
		return $res;
	}
	
	function obtenerMovilesRecomendados($transportista = 0, $idUsuario = 0, $idConductor = 0){
		if (isset($_SESSION['idUsuario'])) {
			$idUsuario = $_SESSION['idUsuario'];	
		}
		if((int)$idConductor){
			$sql = " SELECT dbo.tipoVistaMoviles(".$idUsuario .") ";
			$res = $this->objSQL->dbQuery($sql);
			$rs=$this->objSQL->dbGetAllRows($res,1);
			$campoMovil = $rs[0][0];
			
			$sql = " SELECT mo_id as id, mo_".$campoMovil." as dato ";
			$sql.= " FROM tbl_moviles WITH(NOLOCK) ";
			$sql.= " WHERE mo_borrado = 0 ";
			$sql.= "  AND mo_co_id_primario = ".(int)$idConductor;
			
			$objRes=$this->objSQL->dbQuery($sql);	
			$res=$this->objSQL->dbGetAllRows($objRes,3);
			
			return $res;
		}
	}
	
	function getMotivoViajes($idMotivo = 0){
            if($idMotivo){
                $sql = " SELECT * FROM tbl_viajes_motivos_cambios WITH(NOLOCK) ";
		$sql.= " WHERE vmc_borrado = 0 AND vmc_id = ".(int)$idMotivo;
                $res = $this->objSQL->dbQuery($sql);
                return $this->objSQL->dbGetAllRows($res,3);
            }
            else{
                $sql = " SELECT vmc_id, vmc_descripcion FROM tbl_viajes_motivos_cambios WITH(NOLOCK) 
                    WHERE vmc_borrado = 0 AND vmc_id IN (73,74) ORDER BY vmc_id DESC ";
                $res = $this->objSQL->dbQuery($sql);
                $aux_1 = $this->objSQL->dbGetAllRows($res,3);
                
                $sql = " SELECT vmc_id, vmc_descripcion FROM tbl_viajes_motivos_cambios WITH(NOLOCK) 
                    WHERE vmc_borrado = 0 AND vmc_id NOT IN (73,74) ORDER BY lTRIM(vmc_descripcion) ASC ";
                $res = $this->objSQL->dbQuery($sql);
                $aux_2 = $this->objSQL->dbGetAllRows($res,3);
                
                $result = $aux_1;
                foreach($aux_2 as $item){
                   array_push($result, $item);
                }
                return $result;
            }		
	}
	
	function setConductorVehiculo($id_conductor, $id_movil, $id_transportista = NULL){
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
			$this->setLog($msg_log);
			return true;		
		}
		return false;
	}
	
	function setVehiculo($id_movil, $id_transportista = NULL){
		global $lang;
		$id_movil = (int)$id_movil?$id_movil:'NULL';
		$id_conductor = (int)$id_conductor?$id_conductor:'NULL';
		
		##-- TXT Log --##
		$sql = " SELECT mo_id, mo_matricula+' - '+convert(varchar,mo_identificador) as movil ";
		$sql.= " FROM tbl_viajes WITH(NOLOCK) ";
		$sql.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON mo_id = vi_mo_id ";
		$sql.= " WHERE vi_id = ".$this->id_viaje;
		$res = $this->objSQL->dbQuery($sql);
		$arr_viaje = $this->objSQL->dbGetRow($res,0,3);
		
		$sql = " SELECT mo_matricula+' - '+mo_identificador as movil FROM tbl_moviles WITH(NOLOCK) WHERE mo_id = ".(int)$id_movil;
		$res = $this->objSQL->dbQuery($sql);
		$arr_movil = $this->objSQL->dbGetRow($res,0,3);
		
		$msg_log = ' '.str_replace('[DATOS_ACTUALES]',($lang->system->movil.'['.($arr_viaje['mo_id']?$arr_viaje['movil']:'-'.$lang->system->sin_asignar.'-').']'),$lang->system->edicion_atajo->__toString());
		$msg_log = str_replace('[DATOS_EDITADOS]',($lang->system->movil.'['.((int)$id_movil?$arr_movil['movil']:'-'.$lang->system->sin_asignar.'-').']'),$msg_log);
		##-- --##		
		
		$sql = " UPDATE tbl_viajes SET vi_mo_id = ".$id_movil;
		if($id_transportista){
			$sql.= " , vi_transportista = ".(int)$id_transportista;
		}
		$sql.= " WHERE vi_id = ".$this->id_viaje;
		if($res = $this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return true;		
		}
		return false;
	}
	
	function setLog($msg,$idMotivo=0){
		if(!empty($msg)){
			$this->generarLog(1,(int)$this->id_viaje,$msg);
		}
	}
	
	function getRefInfo($filtro){
		/*
		global $lang;
		if($filtro['fdesde'] != $filtro['fhasta']){
			$fecha = $filtro['fdesde'].' a '.$filtro['fhasta'];}
		else{
			$fecha = $filtro['fdesde'];}
				
		return '<span class="ref-info"><strong>**</strong>'.str_replace('[FECHA]',$fecha,$lang->message->msj_viajes_filtro_aplicado).'</span>';	
		*/
	}
	
	function getViajesTipo($txtTipo = NULL){
		$sql = " SELECT COUNT(*) as cant FROM tbl_viajes_tipo_agentes WITH(NOLOCK) WHERE vta_cl_id =".(int)$_SESSION['idAgente'];
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);
		if($rs['cant'] > 0){
			$sql = " SELECT vt_id, vt_nombre ";
			$sql.= " FROM tbl_viajes_tipo WITH(NOLOCK) ";
			$sql.= " INNER JOIN tbl_viajes_tipo_agentes WITH(NOLOCK) ON vta_vt_id = vt_id ";
			$sql.= " WHERE vta_cl_id = ".(int)$_SESSION['idAgente']." AND vt_borrado = 0 ";
			if(!empty($txtTipo)){
				$sql.= " AND vt_nombre ='".$txtTipo."'";	
			}
			$sql.= " ORDER BY vt_nombre ";
		}
		else{
			$sql = " SELECT vt_id, vt_nombre ";
			$sql.= " FROM tbl_viajes_tipo WITH(NOLOCK) ";
			$sql.= " WHERE vt_default = 1 AND vt_borrado = 0 ";
			if(!empty($txtTipo)){
				$sql.= " AND vt_nombre ='".$txtTipo."'";	
			}
			$sql.= " ORDER BY vt_nombre ";	
		}
		
		$objTipoViajes = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetAllRows($objTipoViajes,3);
		return $objRow;
	}
	
	function getDador($id = NULL){
		
		$arrEmpresa = $this->getDatosEmpresa();
		
		// DADORES:	
		
		$sql = " SELECT cl_id as da_id, cl_razonSocial as da_nombre ";
		$sql.= " FROM tbl_clientes WITH(NOLOCK) ";
		$sql.= " WHERE cl_borrado = 0 ";
		
		if((int)$id){
			$sql.= " AND cl_id = ".(int)$id;
		}		
			
		$sql.= " AND cl_tipo_cliente = 1 "; //traigo solo dadores
			
		if($arrEmpresa['tipoEmpresa'] == 1){ 	
			if($arrEmpresa['tipoCliente'] == 1){ //Agente-Dador			
				$sql.= " AND (cl_id = ".$arrEmpresa['empresa']." OR cl_id_distribuidor = ".$arrEmpresa['empresa']." )"; //trae mi empresa o dadores que me tenga como agente
			}
			else{ //Agente-no dador
				$sql.= " AND cl_id_distribuidor = ".$arrEmpresa['empresa']." "; //trae dadores que me tenga como agente
			}
		}
						
		$sql.= " ORDER BY cl_razonSocial ";
		
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res,3);
		return $rs;
	}
	
	function getDatosEmpresa(){
		$sql = "declare @usuario int 
				set @usuario = ".$_SESSION['idUsuario']." 				
				declare @tipoEmpresa int
				set @tipoEmpresa = -1				
				declare @tipoCliente int 
				set @tipoCliente = -1 
				declare @empresa int
				declare @miAgente int 
				set @miAgente = -1 
				
				SET NOCOUNT ON
								
				SELECT @tipoCliente = cl_tipo_cliente, @tipoEmpresa= cl_tipo, @empresa = cl_id, @miAgente = cl_id_distribuidor
				FROM tbl_clientes WITH(NOLOCK)
				INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = cl_id 
				WHERE us_id = @usuario 
								
				SELECT @tipoEmpresa tipoEmpresa,@tipoCliente tipoCliente,@empresa empresa,@miAgente miAgente
		";
		$objRes = $this->objSQL->dbQuery($sql);	
		$res = $this->objSQL->dbGetRow($objRes,0,3);
		return $res;
	}
	
	function getDestinos($referencias){
		$arrRef = array();
		$i = 0;
		$id_geozonas = "";
		$coma="";
		foreach($referencias as $item){
			$arrRef[$i] = $item;
			$arrRef[$i]['re_id'] = $item['vd_re_id'];
			$arrRef[$i]['fecha'] = date('d-m-Y',strtotime($item['vd_ini']));
			$arrRef[$i]['hora'] = date('H',strtotime($item['vd_ini']));
			$arrRef[$i]['min'] = date('i',strtotime($item['vd_ini']));
			$arrRef[$i]['fecha_egreso'] = date('d-m-Y H:i',strtotime($item['vd_fin']));
			$id_geozonas.=$coma.$item['vd_re_id'];
			$coma=",";
			
			$segundos=strtotime($item['vd_fin']) - strtotime($item['vd_ini']);
			$arrRef[$i]['duracion'] = $this->getTiempo($segundos);
			$i++;
		}
		
		$datos['ref'] = $arrRef;
		$datos['id_geozonas'] = $id_geozonas;
		return $datos;	
	}
	
	function getRuteo($datos = NULL, $esAdmin = false){
		$sql = " SELECT * FROM tbl_referencias WITH(NOLOCK) ";
		$sql.= " LEFT JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON rc_re_id = re_id ";
		$sql.= " WHERE re_borrado = 0 ";

		if(!$esAdmin){
			$sql.= " AND ((re_us_id IN (SELECT us_id FROM tbl_usuarios WITH(NOLOCK) WHERE us_cl_id = ".(int)$_SESSION['idEmpresa'].")";
		}
		else{
			$sql.= " AND (( 1 = 1";
		}

		if($datos['idgrupo']){
			$sql.= " AND re_rg_id = ".(int)$datos['idgrupo'];
		}

		$sql.= " )";

		if($datos['zona_compartida']){
			$sql.= " OR re_id IN (".$datos['zona_compartida'].")";
		}

		$sql.= " )";
		
		/*if($datos['zona_compartida']){
			$sql.= " WHERE (us_cl_id = ".(int)$_SESSION['idEmpresa']. " OR re_id IN (".$datos['zona_compartida']."))";
		}
		else{
			$sql.= " WHERE us_cl_id = ".(int)$_SESSION['idEmpresa'];
		}
				
		$sql.= " )";

		if($datos['idgrupo']){
			$sql.= " AND re_rg_id = ".(int)$datos['idgrupo'];
		}*/

		if((int)$datos['re_id']){
			$sql.= " AND re_id = ".(int)$datos['re_id'];
		}
		if($datos['zona']){
			$sql.= " AND re_nombre = '".$datos['zona']."'";
		}
		if($datos['no_id_zona']){
			$sql.= " AND re_id NOT IN (".$datos['no_id_zona'].")";
		}
		
		$sql.= " ORDER BY re_nombre "; 

		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res,3);
		return $rs;
	}
	
	function getTiempo($segundos){
		$tiempo = "";
		$min = intval($segundos/60);
		$hs = intval($segundos/60/60);
		$dias = intval($segundos/60/60/24);
		if($min < 60){
			$tiempo	= $min." min";}
		elseif($hs <= 12 || ($dias == 0 && $hs <= 23)){
			$tiempo	= $hs." hs";}
		elseif($dias <= 6){
			$tiempo	= $dias." d&iacute;as";}
		else{
			$tiempo = "1 semana";}		
		
		return $tiempo;	
	}
	
	function filaRuteo($datos, $movil = NULL){
	global $lang;	
	global $seccion;
	global $operacion;
	
	##Inicio. Calculo tiempo real##
	$none = "";
	if(!empty($datos['vd_ini_real'])){
		$f_ini_real = date('d-m-Y H:i',strtotime($datos['vd_ini_real']));
		$segundos=strtotime($datos['vd_ini_real']) - strtotime($datos['vd_ini']);
		$ini_min = intval($segundos/60);
		$none = "none";
	}
	if(!empty($datos['vd_fin_real'])){
		//$f_fin_real = date('d-m-Y H:i',strtotime($datos['vd_fin_real']));
		$none = "none";
			
		if(!empty($datos['vd_fin'])){	
			$segundos=strtotime($datos['vd_fin_real']) - strtotime($datos['vd_fin']);
			$fin_min = intval($segundos/60);
		}
	}
	if(trim($datos['vd_ini_real']) == true && trim($datos['vd_fin_real']) == true){
		$f_estadia = $this->getTiempoHM(strtotime($datos['vd_fin_real']) - strtotime($datos['vd_ini_real']));
	}
	##Fin. Calculo tiempo real##
	?>

	<tr id="n_<?=$datos['re_id']?>">
		<input type="hidden" id="re_rg_id_<?=$datos['re_id']?>" value="<?=$datos['re_rg_id']?>">
		<td width="55" style="vertical-align:middle;">
			<?php if(!tienePerfil(array(8,12,28)) && ($seccion != 'retirosforza' || ($seccion == 'retirosforza' && !isset($datos['vi_id'])))){?>
            <a href="javascript:deleteRow(<?=$datos['re_id']?>);" class="float_l <?=$none?>" id="btn-delete-<?=$datos['re_id']?>"><span class="sprite eliminar"></span></a>
        	<a href="javascript:;" class="float_r handle" id="handle"><span class="sprite ordenar"></span></a>
            <?php }?>
        </td>
        
        <?php if(tienePerfil(array(9,10,11,12))){?>
        <td style="vertical-align:middle;"><?=$datos['vd_id']?></td>
        <?php }?>
		<td width="33%" style="vertical-align:middle;"><?=$datos['re_nombre']?>
		<?php if(tienePerfil(array(27,28)) || ($_SESSION['seccion'] == 'entregasforza' && tienePerfil(array(19,29)))){?>
				<?php 
				$txt = ''; $coma = '';
				if(!empty($datos['re_ubicacion'])){ $txt.= '  - '.$datos['re_ubicacion']; $coma = '<br>';}
				if(!empty($datos['re_identificador'])){ $txt.= '<br>CUIT: '.$datos['re_identificador'].'   '; $coma = '';} 
				if(!empty($datos['re_numboca'])){ $txt.= $coma.' Sucursal: '.$datos['re_numboca'];}
				echo $txt; }?>
		</td>
		<?php if(tienePerfil(array(19,27,28,29))){?>
			<td width="20%" style="vertical-align:middle;">
				<?php 
				$txt = ''; $coma = '';
				if(!empty($datos['re_contacto'])){ $txt.= $datos['re_contacto']; $coma = '<br>';}
				if(!empty($datos['re_whatsapp'])){ $txt.= $coma.'Whatsapp: '.$datos['re_whatsapp']; $coma = '<br>';} 
				if(!empty($datos['re_email'])){ $txt.= $coma.$datos['re_email'];}
				echo $txt; ?>
			</td>
			<td width="60" style="vertical-align:middle;">
				<?php if($_SESSION['seccion'] == 'entregasforza'){?>
				<input type="text" name="pallets_stock_<?=$datos['re_id']?>" id="pallets_stock_<?=$datos['re_id']?>" value="<?=$datos['vd_stock']?>" onkeypress="javascript:only_number(event);" <?=((!$_SESSION['cargamanualok'] && !tienePerfil(19)) ? 'disabled="true"' : '')?> >
				<?php } elseif(isset($datos['vd_id'])) {
					echo $datos['vd_stock_function'];
				}?>
			</td>
		<?php }?>
		
		<td width="205" align="center">
			<?php if(tienePerfil(array(8,28))){
				echo '<center>'.formatearFecha($datos['fecha'],'date').' '.formatearFecha($datos['fecha'].' '.$datos['hora'].':00','hour').':'.$datos['min'].' hs</center>';
			}else{?>
			<input type="text" name="fecha_<?=$datos['re_id']?>" id="fecha_<?=$datos['re_id']?>" value="<?=formatearFecha($datos['fecha'],'date')?>" class="no_margin date float_l">
            <span class="float_l" style="margin:0 2px 0 2px">&nbsp;</span>
            <select class="float_l no_margin" name="hora_<?=$datos['re_id']?>" id="hora_<?=$datos['re_id']?>" style="width:42px;" onchange="javascript:onChanges(<?=$datos['re_id']?>, 'comboFecha')">
            	<?php foreach($this->hora as $item){?>
                	<option value="<?=$item?>" <?php if($item == $datos['hora']){?>selected="selected"<?php }?>><?=formatearFecha($datos['fecha'].' '.$item.':00','hour')?></option>
                <?php }?>
            </select>
            <span class="float_l" style="margin:0 2px 0 2px">:</span>
            <select class="float_l no_margin" name="min_<?=$datos['re_id']?>" id="min_<?=$datos['re_id']?>" style="width:42px;" onchange="javascript:onChanges(<?=$datos['re_id']?>, 'comboFecha')">
            	<?php foreach($this->min as $item){?>
                	<option value="<?=$item?>" <?php if($item == $datos['min']){?>selected="selected"<?php }?>><?=$item?></option>
                <?php }?>
            </select>
            <span class="float_l" id="curso_horario_<?=$datos['re_id']?>" style="margin:2px 0 0 2px"><?=formatearFecha($datos['fecha'].' '.$datos['hora'].':00','pref_hour')?></span>
            <span id="error_<?=$datos['re_id']?>" class="clear block error"> </span>
			<?php }?>
		</td>
        
		<?php if(!tienePerfil(array(19,29))){?>
        <td style="vertical-align:middle;">
        <?php if(!empty($f_ini_real)){?>
            <div id="reset-ingreso-<?=$datos['re_id']?>" class="box_reset_datetime">
            <span class="campo1 block"><?=formatearFecha($f_ini_real)?></span>
            <span class="campo2 block" style="line-height:12px;"><?=$lang->system->ingreso?> <?=($ini_min <= 0)?$lang->system->en_tiempo:$lang->system->atrasado?> 
				<!-- -->
				<? if(!tienePerfil(array(27,28)) && ($_SESSION['seccion'] != 'entregasforza' && $_SESSION['seccion'] != 'retirosforza')){?>
				<a href="javascript:resetIngreso(<?=$datos['re_id']?>);" class="resetDates">
            		<span class="sprite restart no_margin"></span>
				</a>
				<? }?>
				<!-- -->
            </span>	
            </div>
         <?php }
         
		 if($movil['sh_rd_id'] && $datos['re_id'] > 0 && $datos['re_id'] != 6464){?>
         	<?php if($this->esFaltaDeReporte(NULL, $movil['sh_rd_id']) && !tienePerfil(array(8,12,27,28))){?>
                <div id="assign-datetime-ingreso-<?=$datos['re_id']?>" class="box_assign_datetime" style="display:none">
                    <input type="text" id="assign_fecha_ingreso_<?=$datos['re_id']?>" class="no_margin date2 float_l" value="<?=formatearFecha(getFechaServer('d-m-Y'),'date')?>">
                    <span class="float_l" style="margin:0 2px 0 2px">&nbsp;</span>
                    <select class="float_l no_margin" id="assign_hora_ingreso_<?=$datos['re_id']?>" style="width:42px;" onchange="javascript:calcularFecha(0,<?=$datos['re_id']?>,<?=$datos['re_id']?>,'assign_ingreso');">
                        <?php foreach($this->hora as $item){?>
                            <option value="<?=$item?>" <?=($item==getFechaServer('H'))?'selected="selected"':''?> ><?=formatearFecha(getFechaServer('d-m-Y').' '.$item.':00','hour')?></option>
                        <?php }?>
                    </select>
                    <span class="float_l" style="margin:0 2px 0 2px">:</span>
                    <select class="float_l no_margin" id="assign_min_ingreso_<?=$datos['re_id']?>" style="width:42px;">
                        <?php foreach($this->min as $item){?>
                            <option value="<?=$item?>" <?php if(substr(getFechaServer('i'),0,1) == substr($item,0,1)){?>selected="selected"<?php }?>><?=$item?></option>
                        <?php }?>
                    </select>
                    <span class="float_l" id="assign_curso_horario_ingreso_<?=$datos['re_id']?>" style="margin:2px 0 0 2px"><?=formatearFecha(getFechaServer('d-m-Y H:i'),'pref_hour')?></span>
                    <a href="javascript:assignIngreso(<?=$datos['re_id']?>,<?=$datos['vd_id']?>);" class="float_l">
                        <span class="sprite guardar no_margin"></span>
                    </a>
                    <span class="clear"></span>
                </div>
            <?php }?>
		<?php }?>
        </td>
        
        <?php if(!tienePerfil(array(27,28))){?>
        <td <?=(!tienePerfil(array(9,10,11,12))?'class="td-last"':'')?> style="vertical-align:middle;">
        	<?php if(!empty($f_estadia)){?>
			<div id="reset-egreso-<?=$datos['re_id']?>" class="box_reset_datetime">
            <span class="campo1 block"><?=$f_estadia?></span>
            <span class="campo2 block" style="line-height:12px;">
				<?=$datos['vd_fin']?($lang->system->egreso.' '.($fin_min <= 0)?$lang->system->en_tiempo:$lang->system->atrasado):'';?>
            	<!-- -->
				<a href="javascript:resetEgreso(<?=$datos['re_id']?>);" class="resetDates">
                	<span class="sprite restart no_margin"></span>
                </a>
				<!-- -->
            </span>
            </div>
            <?php }?>	
            
           <?php if($movil['sh_rd_id'] && $datos['re_id'] > 0 && $datos['re_id'] != 6464){?>
            	<?php if($this->esFaltaDeReporte(NULL, $movil['sh_rd_id']) && !tienePerfil(array(8,12))){?>
                <div id="assign-datetime-egreso-<?=$datos['re_id']?>" class="box_assign_datetime" style="display:none">
                    <input type="text" id="assign_fecha_egreso_<?=$datos['re_id']?>" class="no_margin date2 float_l" value="<?=formatearFecha(getFechaServer('d-m-Y'),'date')?>">
                    <span class="float_l" style="margin:0 2px 0 2px">&nbsp;</span>
                    <select class="float_l no_margin" id="assign_hora_egreso_<?=$datos['re_id']?>" style="width:42px;" onchange="javascript:calcularFecha(0,<?=$datos['re_id']?>,<?=$datos['re_id']?>,'assign_egreso');">
                        <?php foreach($this->hora as $item){?>
                            <option value="<?=$item?>" <?=($item==getFechaServer('H'))?'selected="selected"':''?> ><?=formatearFecha(getFechaServer('d-m-Y').' '.$item.':00','hour')?></option>
                        <?php }?>
                    </select>
                    <span class="float_l" style="margin:0 2px 0 2px">:</span>
                    <select class="float_l no_margin" id="assign_min_egreso_<?=$datos['re_id']?>" style="width:42px;">
                        <?php foreach($this->min as $item){?>
                            <option value="<?=$item?>" <?php if(substr(getFechaServer('i'),0,1) == substr($item,0,1)){?>selected="selected"<?php }?>><?=$item?></option>
                        <?php }?>
                    </select>
                    <span class="float_l" id="assign_curso_horario_egreso_<?=$datos['re_id']?>" style="margin:2px 0 0 2px"><?=formatearFecha(getFechaServer('d-m-Y H:i'),'pref_hour')?></span>
                    <a href="javascript:assignEgreso(<?=$datos['re_id']?>,<?=$datos['vd_id']?>);" class="float_l">
                        <span class="sprite guardar no_margin"></span>
                    </a>
                    <span class="clear"></span>
                </div>
            	<?php }?>
			<?php }?>
            <input type="hidden" id="lat_<?=$datos['re_id']?>" value="<?=$datos['rc_latitud']?>" />
            <input type="hidden" id="long_<?=$datos['re_id']?>" value="<?=$datos['rc_longitud']?>" />
            <input type="hidden" id="vel_promedio_<?=$datos['re_id']?>" value="<?=$datos['re_vel_promedio']?$datos['re_vel_promedio']:$this->velocidad_promedio?>" />
    	</td>
		<?php }?>
		
        <?php if(tienePerfil(array(9,10,11,12,27,28))){?>
        <td class="td-last">
        	<center>
            	<?php if($datos['vie_descripcion'] == 'entregado'){?>
				    <a href="javascript:mostrarPopup('boot.php?c=agendaGPS&action=proof_delivery&status=<?=$datos['vie_descripcion']?>&name=<?=$datos['vi_id'].'_'.$datos['vd_id']?>&date=<?=$datos['vin_fecha']?>',370,380);"><span class="sprite_hand like"></span></a>
				<?php }
				elseif($datos['vie_descripcion'] == 'rechazado'){?>
					<a href="javascript:mostrarPopup('boot.php?c=agendaGPS&action=proof_delivery&status=<?=$datos['vie_descripcion']?>&name=<?=$datos['vi_id'].'_'.$datos['vd_id']?>&date=<?=$datos['vin_fecha']?>',370,380);"><span class="sprite_hand unlike"></span></a>
				<?php }?>
        	</center>
        </td>
		<?php }?>
		
		<?php } else{?><!-- fin. !tienePerfil(19) -->
		<td style="vertical-align:middle;">
			<center>	
				<input type="text" name="fecha_vencimiento_<?=$datos['re_id']?>" id="fecha_vencimiento_<?=$datos['re_id']?>" value="<?=$datos['vd_vencimiento'] ? formatearFecha($datos['vd_vencimiento'],'date') : '' ?>" class="no_margin date2" style="width: 80px;" autocomplete="off">
			</center>
		</td>
		<td>
		<?php if( $seccion == 'retirosforza' && tienePerfil(array(19,29))){
			$arr_estado = array('0' => 'Pendiente', '1' => 'Confirmado', '-1' => 'Rechazado');
		} else{
			$arr_estado = array('0' => 'Sin Documento', '1' => 'Pendiente de verificación', '2' => 'Acreditado', '-1' => 'Rechazado');
		}

		foreach($arr_estado as $k => $estado){
			$datos['vd_checked'] = empty($datos['vd_checked']) ? 0 : $datos['vd_checked']?>
			<label for="estado_<?=$datos['re_id']?>_<?=$k?>" class="clear no_margin" style="line-height:20px;">
			<input type="radio" name="estado_<?=$datos['re_id']?>" id="estado_<?=$datos['re_id']?>_<?=$k?>" value="<?=$k?>" class="float_l no_margin" style="width: auto; height: auto; margin: 4px 6px 4px 0!important;" <?=($datos['vd_checked'] == $k) ? 'checked="checked"' : ''?> >		
				<?=$estado?>
			</label>
		<?php }?>			
		</td>
		<td class="td-last"></td>
		<?php }?>

	</tr>
    <?php }
	
	function esFaltaDeReporte($dr_valor = NULL, $dr_id = NULL){
		$id_evento = 0;
		
		if($dr_valor){
			$arr_eventos = array(980,987);
			$id_evento = $dr_valor;	
		}
		elseif($dr_id){
			$arr_eventos = array(75,76);	
			$id_evento = $dr_id;		
		}
		
		if($id_evento){
			if(in_array($id_evento, $arr_eventos)){
				return true	;
			}
		}
		return false;	
	}
		
	function get_geozonas($datos = NULL){
		@session_start();
		$sql = " SELECT ";
		if($datos['top']){
			$sql.= "TOP ".(int)$datos['top'];
		}
		$sql.= " (CASE WHEN re_numboca != '' THEN '('+LTRIM(RTRIM(re_numboca))+') ' ELSE '' END) 
				+ re_nombre 
				+ (CASE WHEN re_ubicacion != '' THEN ', '+LTRIM(RTRIM(re_ubicacion)) ELSE '' END) as etiqueta
				,re_nombre, re_id ";
		$sql.= " FROM tbl_referencias re WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios us WITH(NOLOCK) ON (us.us_id = re.re_us_id) ";
		$sql.= " INNER JOIN tbl_tipo_referencia tr WITH(NOLOCK) ON (tr.tr_id = re.re_tr_id) ";
		
		$sql.= " WHERE re_borrado = 0 ";
		if($datos['zona_like']){
			$sql.= " AND (re_nombre LIKE '%".$datos['zona_like']."%' OR re_numboca LIKE '%".$datos['zona_like']."%')";
		}
		if($datos['no_id_zona']){
			$sql.= " AND re_id NOT IN (".$datos['no_id_zona'].")";
		}

		$sql.= " AND ((us.us_cl_id = ".(int)$_SESSION['idEmpresa'];

		if($datos['idgrupo']){
			$sql.= " AND re.re_rg_id = ".(int)$datos['idgrupo'];
		}

		$sql.= " )";

		if($datos['zona_compartida']){
			$sql.= " OR re_id IN (".$datos['zona_compartida'].")";
		}

		$sql.= " )";

		$sql.= " ORDER BY re_nombre "; 

		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res,3);
		return $rs;
	}	
	
	function validarMovil($idMovil, $f_ini, $f_fin){
		$f_ini = date('Y-m-d H:i',strtotime($f_ini));
		$f_fin = date('Y-m-d H:i',strtotime($f_fin));
		
		$sql = " SELECT COUNT(DISTINCT(vi_id)) as cant FROM(";
		$sql.= " SELECT MIN(vd_ini) as ini, MAX(vd_fin) as fin, vi_id ";
		$sql.= " FROM tbl_viajes WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vi_id = vd_vi_id ";
		$sql.= " WHERE vi_mo_id = ".(int)$idMovil." AND vi_finalizado = 0  AND vi_borrado = 0 "	;
		if($this->id_viaje){
			$sql.= " AND vi_id != ".$this->id_viaje;	
		}
		$sql.= " GROUP BY vi_id ";
		$sql.= " ) minmax";
		$sql.= " WHERE  ('".$f_ini."' BETWEEN ini AND fin) OR ('".$f_fin."' BETWEEN ini AND fin)";
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res);
		
		return $rs[0]['cant'];
	}
	
	function obtenerMenorDistancia($orden, $referencias){
		$menor['distancia'] = 9999999999999999999999999;
		$menor['re_id'] = 0;
		foreach($referencias as $k => $item){
			if($item['orden'] > $orden){
				if($item['dist'] < $menor['distancia']){
					$menor['distancia'] = $item['dist'];
					$menor['re_id'] = $item['id'];
				}
			}
		}
		return $menor['re_id'];
	}
	
	function getTransportista($datos = NULL){
		$sql = " SELECT DISTINCT(cl_id) as cl_id, cl_razonSocial FROM ( ";
		$sql.= " SELECT cl_id, cl_razonSocial FROM tbl_clientes cliente WITH(NOLOCK) WHERE (cliente.cl_id_fletero IS NULL OR cliente.cl_id_fletero = 0) AND cliente.cl_borrado = 0 ";
		$sql.= " UNION ";
		$sql.= " SELECT fletero.cl_id, (cliente.cl_razonSocial+' - '+fletero.cl_razonSocial) as cl_razonSocial FROM tbl_clientes cliente WITH(NOLOCK) INNER JOIN tbl_clientes fletero WITH(NOLOCK) ON fletero.cl_id_fletero = cliente.cl_id WHERE fletero.cl_borrado = 0 ";
		$sql.= " ) as cliente_fletero ";
		$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id_cliente_facturar =cl_id ";
		$sql.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_mo_id = mo_id ";
		
		if($_SESSION['idTipoEmpresa'] != 3){
			$sql.= " WHERE um_us_id = ".(int)$datos['id_usuario'];
		}
		else{
			$sql.= "WHERE 1=1";
		}
		
		if (isset($datos['id'])) {
			if($datos['id']){
				$sql.= " AND cl_id = ".(int)$datos['id'];
			}
		}
		
		$sql.= " ORDER BY cl_razonSocial ";
		
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetAllRows($res,3);
		if(!$rs){
			//--Si el usuario no tiene moviles asociados, se muestra los transportistas sin moviles.
			$query = "SELECT cl_id, cl_razonSocial FROM tbl_clientes WITH(NOLOCK) "
				." WHERE cl_id_distribuidor IN (SELECT us_cl_id FROM tbl_usuarios WHERE us_id = ".(int)$datos['id_usuario'].") AND cl_borrado = 0;";
			$rs = $this->objSQL->dbGetAllRows($this->objSQL->dbQuery($query),3);
		}

		return $rs;
	}
	
	function getTransportistaViaje(){
		$sql = " SELECT cl_email, cl_razonSocial FROM tbl_moviles WITH(NOLOCK)
				 INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = mo_id_cliente_facturar
				 WHERE mo_borrado = 0 AND cl_borrado = 0 AND cl_por_defecto = 0 AND mo_id IN(
						SELECT vi_mo_id AS mo_id FROM tbl_viajes WITH(NOLOCK) WHERE vi_id = ".$this->id_viaje."
						UNION
						SELECT DISTINCT(vdd_mo_id) AS mo_id  FROM tbl_viajes_destinos_delivery WITH(NOLOCK)
						INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_id = vdd_vd_id
						WHERE vd_vi_id = ".$this->id_viaje."
					) ";
		$res = $this->objSQL->dbQuery($sql);
		return $this->objSQL->dbGetAllRows($res,3);
	}
	
	function getMovilTipo($idTansportadora = NULL){
		$sql = " SELECT distinct tv.tv_id, tv.tv_nombre FROM tbl_tipo_movil tv WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON tv.tv_id = mo_id_tipo_movil ";
		$sql.= " WHERE tv.tv_borrado = 0 AND mo_borrado = 0 ";
		if($idTansportadora){
			$sql.= " AND mo_id_cliente_facturar = ".(int)$idTansportadora;
		}
		$sql.= " ORDER BY tv.tv_nombre ";

		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetAllRows($res,3);
		
		global $lang;
		foreach($rs as $k => $item){
			$rs[$k]['tv_nombre'] = $lang->system->$item['tv_nombre']?$lang->system->$item['tv_nombre']->__toString():$item['tv_nombre'];	
		}
		return $rs;
	}
	
	function getConductor($datos){
		$sql = " SELECT * FROM tbl_conductores WITH(NOLOCK) ";
		$sql.= " WHERE co_borrado = 0 AND co_cl_id = ".(int)$datos['id_transportista'];
		if($datos['id']){
			$sql.= " AND co_id = ".(int)$datos['id'];
		}
		$sql.= " ORDER BY co_nombre ";
		
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res,3);
		return $rs;
	}
	
	function getMovil($datos = NULL){
		
		$sql = " SELECT tbl_moviles.*, sh_rd_id FROM tbl_moviles WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
		$sql.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = mo_id ";
		$sql.= " INNER JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		$sql.= " WHERE mo_borrado = 0 AND um_us_id = ".(int)$datos['id_usuario'];
		if($datos['id']){
			$sql.= " AND mo_id = ".(int)$datos['id'];
		}
		if($datos['tipo_movil']){
			$sql.= " AND mo_id_tipo_movil = ".(int)$datos['tipo_movil'];
		}
		if((int)$datos['transportista']){
			$sql.= " AND mo_id_cliente_facturar = ".(int)$datos['transportista'];
		}
		
		$sql.= " ORDER BY mo_matricula ";

		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res,3);
		return $rs;
	}
	
	function getViajes($filtros = NULL){
		$sql = " SELECT * FROM tbl_viajes WITH(NOLOCK) ";
		$sql.= " WHERE vi_borrado = 0 ";
		if($this->id_viaje){
			$sql.= " AND vi_id = ".$this->id_viaje;
		}
		
		if(!empty($filtros['cod_viaje'])){
			$sql.= " AND vi_codigo = '".$filtros['cod_viaje']."'";
		}
		if($filtros['us_id']){
			$sql.= " AND vi_us_id = ".(int)$filtros['us_id'];
		}
		
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res,3);
		return $rs;
	}

	function getReferencias($datos = NULL){
		$sql = " SELECT vd_id, vd_re_id, re_nombre, vd_ini, vd_fin, vd_ini_real, vd_fin_real, vd_stock, dbo.Pallets_por_viaje_destino (vd_id) as vd_stock_function, re_contacto, re_whatsapp, re_email, re_identificador, re_ubicacion, re_numboca, vd_checked, vd_vencimiento ";
		$sql.= " ,(SELECT TOP 1 rc_latitud FROM tbl_referencias_coordenadas WITH(NOLOCK) WHERE rc_re_id = ref.re_id ORDER BY rc_id) AS rc_latitud ";
		$sql.= " ,(SELECT TOP 1 rc_longitud FROM tbl_referencias_coordenadas WITH(NOLOCK) WHERE rc_re_id = ref.re_id ORDER BY rc_id) AS rc_longitud ";
		$sql.= " ,vie_descripcion, vin_fecha, ref.re_rg_id ";
		$sql.= " FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias ref WITH(NOLOCK) ON vd_re_id = re_id ";
		$sql.= " LEFT JOIN tbl_viajes_instancias WITH(NOLOCK) ON vin_vi_id = vd_vi_id AND vin_vd_id = vd_id ";
		$sql.= " LEFT JOIN tbl_viajes_instancias_estados WITH(NOLOCK) ON vie_id = vin_vie_id ";
		$sql.= " WHERE vd_vi_id = ".(int)$this->id_viaje;
		
		if((int)$datos['re_id']){
			$sql.=" AND re_id = ".(int)$datos['re_id'];	
		}
		
		$sql.= " ORDER BY vd_orden ";
		
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res,3);
		return $rs;
	}
	
	function validarMovilesAsignados($id_movil = NULL){
		@session_start();
		
		if($id_movil){		
			$sql = " SELECT COUNT(*) as cant FROM tbl_usuarios_moviles WITH(NOLOCK) ";
			$sql.= " WHERE um_us_id = ".(int)$_SESSION['idUsuario']." AND um_mo_id = ".(int)$id_movil;
			
			$res = $this->objSQL->dbQuery($sql);
			$rs = $this->objSQL->dbGetAllRows($res,3);
			
			return $rs[0]['cant'];
		}
		else{
			return true;
		}
	}
	
	function getHistorial(){
		
		$strSQL = " SELECT sl_descripcion, sl_us_nombre, sl_fecha_alta FROM ( ";
		$strSQL.= " SELECT * FROM tbl_system_log WITH(NOLOCK) WHERE sl_st_id = 1 AND sl_rel_id = ".(int)$this->id_viaje;
		$strSQL.= " UNION ";
		$strSQL.= " SELECT * FROM tbl_system_log WITH(NOLOCK) WHERE sl_st_id = 2 AND sl_rel_id IN (
					SELECT vd_re_id FROM tbl_viajes WITH(NOLOCK)
					INNER JOIN tbl_viajes_destinos WITH(NOLOCK) ON vd_vi_id = vi_id
					WHERE vi_id = ".(int)$this->id_viaje.") ";
		$strSQL.= " ) AS logViajes ORDER BY sl_fecha_alta ";
		$strSQL = 'EXEC db_viajes_solapa_historial '.$_SESSION['idUsuario'].', '.(int)$this->id_viaje;
		$res = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($res,3);		
	}

	function getSeguridad(){
		$strSQL = 'EXEC db_viajes_solapa_seguridad '.$_SESSION['idUsuario'].', '.(int)$this->id_viaje;
		return $this->objSQL->dbGetAllRows($this->objSQL->dbQuery($strSQL),3);	
	}

	
	function assignFechaIngreso($id_destino, $fecha){
		$fecha = formatearFecha($fecha);
		
		##-- TXT Log --##
		$sql = " SELECT re_nombre FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino;
		$res = $this->objSQL->dbQuery($sql);
		$arr_ref = $this->objSQL->dbGetRow($res,0,3);
		$msg_log = ' Asigna Fecha de Ingreso ['.$fecha.'] para la Referencia: '.$arr_ref['re_nombre'];
		##-- --##	
		
		$fecha = date('Y-m-d H:i:s',strtotime($fecha));
		$sql= " UPDATE tbl_viajes_destinos SET vd_ini_real = '".$fecha."'";
		$sql.=" WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino;
		
		if($this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return $fecha;
		}
		return false;
	}
	
	function assignFechaEgreso($id_destino, $fecha){
		##-- TXT Log --##
		$sql = " SELECT re_nombre, vd_ini_real FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino;
		$res = $this->objSQL->dbQuery($sql);
		$arr_ref = $this->objSQL->dbGetRow($res,0,3);
		$msg_log = ' Asigna Fecha de Egreso ['.date('d-m-Y H:i',strtotime($fecha)).'] para la Referencia: '.$arr_ref['re_nombre'];
		##-- --##	
		
		$fecha = date('Y-m-d H:i:s',strtotime($fecha));
		$sql= " UPDATE tbl_viajes_destinos SET vd_fin_real = '".$fecha."'";
		$sql.=" WHERE vd_vi_id = ".$this->id_viaje." AND vd_id = ".(int)$id_destino;
		if($this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			$arr['vd_ini_real'] = $arr_ref['vd_ini_real'];
			$arr['fecha'] = $fecha;
			return $arr;
		}
		return false;
	}
	
	function resetFechaIngreso($idReferencia){
		##-- TXT Log --##
		$sql = " SELECT re_nombre FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_re_id = ".(int)$idReferencia;
		$res = $this->objSQL->dbQuery($sql);
		$arr_ref = $this->objSQL->dbGetRow($res,0,3);
		$msg_log = ' Reseteo Fecha de Ingreso para la Referencia: '.$arr_ref['re_nombre'];
		##-- --##	
		
		$sql= " UPDATE tbl_viajes_destinos SET vd_ini_real = NULL, vd_fin_real = NULL ";
		$sql.=" WHERE vd_vi_id = ".$this->id_viaje." AND vd_re_id = ".(int)$idReferencia;
		if($this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return true;	
		}
		return false;
	}
	
	function resetFechaEgreso($idReferencia){
		
		##-- TXT Log --##
		$sql = " SELECT re_nombre FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_re_id = ".(int)$idReferencia;
		$res = $this->objSQL->dbQuery($sql);
		$arr_ref = $this->objSQL->dbGetRow($res,0,3);
		$msg_log = ' Reseteo Fecha de Egreso para la Referencia: '.$arr_ref['re_nombre'];
		##-- --##		
		
		$sql= " UPDATE tbl_viajes_destinos SET vd_fin_real = NULL ";
		$sql.=" WHERE vd_vi_id = ".$this->id_viaje." AND vd_re_id = ".(int)$idReferencia;
		if($this->objSQL->dbQuery($sql)){
			$this->setLog($msg_log);
			return true;	
		}
		return false;
	}
	
	function validarCodViaje($codViaje){
		
		$sql= " SELECT COUNT(DISTINCT(vi_id)) as cant ";
		$sql.= " FROM tbl_viajes WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_id = vi_us_id ";
		$sql.= " WHERE vi_id != ".$this->id_viaje." AND vi_finalizado = 0  AND vi_borrado = 0 "	;
		$sql.= " AND vi_codigo LIKE '%".$codViaje."%'";
		$sql.= " AND us_cl_id = ".(int)$_SESSION['idEmpresa'];
		
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res,3);
		
		return $rs[0]['cant'];
	}
	
	function setViajes($relcampos, $relvalorCampos){
		
		array_push($relcampos,'vi_fechacreado');
		array_push($relvalorCampos,"'".getFechaServer('Y-m-d H:i:s')."'");
				
		global $lang;
		$campos = implode(',', $relcampos);
    	
		foreach($relvalorCampos as $k => $item){
			if($item === "" || $item === NULL){
				$relvalorCampos[$k] = "NULL";	
			}	
		}
		$valorCampos = implode(',', $relvalorCampos);
    			
		ini_set('mssql.charset', 'UTF-8');
		$sql = "INSERT INTO tbl_viajes(".$campos.") VALUES(".$valorCampos.")";
		if($this->objSQL->dbQuery($sql)){
			$this->id_viaje = $this->objSQL->dbLastInsertId();
			$this->setLog($lang->system->alta_viaje);
			return 	$this->id_viaje;
		}
		else{
			return false;	
		}
	}
	
	function updateViajes($relcampos, $relvalorCampos){
		global $lang;
		$campos = $relcampos;
    	$valorCampos = $relvalorCampos;
		
		##-- TXT Log --##
		$sql = " SELECT vt_nombre,dador.cl_razonSocial as dador,trans.cl_razonSocial as transp,mo_matricula,co_nombre+' '+co_apellido as conductor,tbl_viajes.* ";
		$sql.= " FROM tbl_viajes WITH(NOLOCK) ";
		$sql.= " LEFT JOIN tbl_viajes_tipo WITH(NOLOCK) ON vi_vt_id = vt_id ";
		$sql.= " LEFT JOIN tbl_clientes as dador WITH(NOLOCK)  ON dador.cl_id = vi_dador ";
		$sql.= " LEFT JOIN tbl_clientes as trans WITH(NOLOCK)  ON trans.cl_id = vi_transportista ";
		$sql.= " LEFT JOIN tbl_moviles WITH(NOLOCK) ON mo_id = vi_mo_id ";
		$sql.= " LEFT JOIN tbl_conductores WITH(NOLOCK) ON co_id = vi_co_id ";
		$sql.= " WHERE vi_id = ".$this->id_viaje;
		$res = $this->objSQL->dbQuery($sql);
		$arr_viaje = $this->objSQL->dbGetRow($res,0,3); 
				
		$msg_log = $data_actual  = $data_update = $coma = '';
		foreach($campos as $k => $item){
			if(str_replace("'","",$valorCampos[$k]) != trim($arr_viaje[$item]) && $item != 'vi_us_id'){
				if($item == 'vi_codigo'){
					$data_actual.= $coma.$lang->system->codigo_viaje.'['.$arr_viaje[$item].']';
					$data_update.= $coma.$lang->system->codigo_viaje.'['.str_replace("'","",$valorCampos[$k]).']';
					$coma = ", ";
				}
				if($item == 'vi_vt_id'){
					$sql_op = " SELECT vt_nombre FROM tbl_viajes_tipo WITH(NOLOCK) WHERE vt_id = ".(int)$valorCampos[$k];
					$res_op = $this->objSQL->dbQuery($sql_op);
					$rs_op = $this->objSQL->dbGetRow($res_op,0,3);
					$data_actual.= $coma.$lang->system->tipo_viaje.'['.$arr_viaje[$item].'#'.$arr_viaje['vt_nombre'].']';
					$data_update.= $coma.$lang->system->tipo_viaje.'['.$valorCampos[$k].'#'.$rs_op['vt_nombre'].']';
					$coma = ", ";
				}
				if($item == 'vi_dador'){
					$sql_op = " SELECT cl_razonSocial FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = ".(int)$valorCampos[$k];
					$res_op = $this->objSQL->dbQuery($sql_op);
					$rs_op = $this->objSQL->dbGetRow($res_op,0,3);
					$data_actual.= $coma.$lang->system->dador.'['.($arr_viaje[$item]?$arr_viaje[$item].'#'.$arr_viaje['dador']:'-'.$lang->system->sin_asignar.'-').']';
					$data_update.= $coma.$lang->system->dador.'['.($valorCampos[$k]?$valorCampos[$k].'#'.$rs_op['cl_razonSocial']:'-'.$lang->system->sin_asignar.'-').']';
					$coma = ", ";
				}
				if($item == 'vi_transportista'){
					$sql_op = " SELECT cl_razonSocial FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = ".(int)$valorCampos[$k];
					$res_op = $this->objSQL->dbQuery($sql_op);
					$rs_op = $this->objSQL->dbGetRow($res_op,0,3);
					$data_actual.= $coma.$lang->system->transportista.'['.($arr_viaje[$item]?$arr_viaje[$item].'#'.$arr_viaje['transp']:'-'.$lang->system->sin_asignar.'-').']';
					$data_update.= $coma.$lang->system->transportista.'['.($valorCampos[$k]?$valorCampos[$k].'#'.$rs_op['cl_razonSocial']:'-'.$lang->system->sin_asignar.'-').']';
					$coma = ", ";
				}
				if($item == 'vi_mo_id'){
					$sql_op = " SELECT mo_matricula FROM tbl_moviles WITH(NOLOCK) WHERE mo_id = ".(int)$valorCampos[$k];
					$res_op = $this->objSQL->dbQuery($sql_op);
					$rs_op = $this->objSQL->dbGetRow($res_op,0,3);
					$data_actual.= $coma.$lang->system->movil.'['.($arr_viaje[$item]?$arr_viaje[$item].'#'.$arr_viaje['mo_matricula']:'-'.$lang->system->sin_asignar.'-').']';
					$data_update.= $coma.$lang->system->movil.'['.($valorCampos[$k]?$valorCampos[$k].'#'.$rs_op['mo_matricula']:'-'.$lang->system->sin_asignar.'-').']';
					$coma = ", ";
				}
				if($item == 'vi_co_id'){
					$sql_op = " SELECT co_nombre+' '+co_apellido as conductor FROM tbl_conductores WITH(NOLOCK) WHERE co_id = ".(int)$valorCampos[$k];
					$res_op = $this->objSQL->dbQuery($sql_op);
					$rs_op = $this->objSQL->dbGetRow($res_op,0,3);
					$data_actual.= $coma.$lang->system->conductor.'['.($arr_viaje[$item]?$arr_viaje[$item].'#'.$arr_viaje['conductor']:'-'.$lang->system->sin_asignar.'-').']';
					$data_update.= $coma.$lang->system->conductor.'['.($valorCampos[$k]?$valorCampos[$k].'#'.$rs_op['conductor']:'-'.$lang->system->sin_asignar.'-').']';
					$coma = ", ";
				}
				if($item == 'vi_observaciones'){
					$auxObs = trim(str_replace("'","",$valorCampos[$k]));
					$auxObs = ($auxObs=='NULL')?NULL:$auxObs;
					if(trim($arr_viaje[$item]) != $auxObs){
					$data_actual.= $coma.$lang->system->observaciones.'['.$arr_viaje[$item].']';
					$data_update.= $coma.$lang->system->observaciones.'['.str_replace("'","",$valorCampos[$k]).']';
					$coma = ", ";
					}
				}
				if($item == 'vi_observaciones_2'){
					$auxObs = trim(str_replace("'","",$valorCampos[$k]));
					$auxObs = ($auxObs=='NULL')?NULL:$auxObs;
					if(trim($arr_viaje[$item]) != $auxObs){
					$data_actual.= $coma.$lang->system->observaciones.'['.$arr_viaje[$item].']';
					$data_update.= $coma.$lang->system->observaciones.'['.str_replace("'","",$valorCampos[$k]).']';
					$coma = ", ";
					}
				}
				if($item == 'vi_finalizado'){
					$data_actual.= $coma.$lang->system->viaje_finalizado.'['.($arr_viaje[$item]?$lang->system->si:$lang->system->no).']';
					$data_update.= $coma.$lang->system->viaje_finalizado.'['.($valorCampos[$k]?$lang->system->si:$lang->system->no).']';
					$coma = ", ";
				}
				if($item == 'vi_facturado'){
					if((int)$arr_viaje[$item] != (int)$valorCampos[$k]){
						$data_actual.= $coma.$lang->system->facturado.'['.($arr_viaje[$item]?$lang->system->si:$lang->system->no).']';
						$data_update.= $coma.$lang->system->facturado.'['.($valorCampos[$k]?$lang->system->si:$lang->system->no).']';
						$coma = ", ";
					}
				}
				if($item == 'vi_contenedor'){
					$data_actual.= $coma.'Contendor['.$arr_viaje[$item].']';
					$data_update.= $coma.'Contendor['.str_replace("'","",$valorCampos[$k]).']';
					$coma = ", ";
				}
			}
		}
		
		if(!empty($data_update)){
			$msg_log = ' '.str_replace('[DATOS_ACTUALES]',$data_actual,decode($lang->system->edicion_viaje->__toString()));
			$msg_log = str_replace('[DATOS_EDITADOS]',$data_update,$msg_log);
			##-- --##
		
			foreach($relvalorCampos as $k => $item){
				if($item === "" || $item === NULL){
					$relvalorCampos[$k] = "NULL";	
				}	
			}
			$valorCampos = $relvalorCampos;
		
			ini_set('mssql.charset', 'UTF-8');
			$sql = "UPDATE tbl_viajes SET ";
			$coma = "";
			foreach($campos as $k => $item){
				$sql.= $coma.$item."=".$valorCampos[$k];
				$coma = ", ";
			}
			$sql.= " WHERE vi_id = ".$this->id_viaje;
			
			if($this->objSQL->dbQuery($sql)){
				$this->setLog($msg_log);
				return true;
			}
			return false;
		}
		
		return true;
	}
	
	function deleteViajesDestinos($not_geozona){
		global $lang;
		if($this->id_viaje){
			
			##-- TXT Log --##
			$sql = " SELECT re_nombre FROM tbl_viajes_destinos WITH(NOLOCK) ";
			$sql.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
			$sql.= " WHERE vd_vi_id = ".$this->id_viaje;	
			if($not_geozona){
				$sql.= " AND vd_re_id NOT IN (".$not_geozona.")";	
			}
			$res = $this->objSQL->dbQuery($sql);
			$rs = $this->objSQL->dbGetAllRows($res,3);
			$referencias = $coma = '';
			foreach($rs as $item){
				$referencias.= $coma.$item['re_nombre'];
				$coma = ', ';
			}
			if(!empty($referencias)){
				$msg_log = $lang->system->baja_referencia.': '.$referencias;
			}
			##-- --##					
			
			$sql = " DELETE FROM tbl_viajes_destinos WHERE vd_vi_id = ".$this->id_viaje;	
			if($not_geozona){
				$sql.= " AND vd_re_id NOT IN (".$not_geozona.")";	
			}

			if($this->objSQL->dbQuery($sql)){
				$this->setLog($msg_log);
				return true;
			}
		}
		return false;
	}
	
	function setViajesDestinos($destinos){
		global $lang;
		$resp = true;
		$esInsert = false;
		foreach($destinos as $item){
			$sql_select = " SELECT top 1 re_nombre, vd_id, vd_ini, vd_fin, vd_orden, re_email, rc_latitud, rc_longitud, vd_stock, vd_vencimiento, vd_checked ";
			$sql_select.= " FROM tbl_referencias  WITH(NOLOCK) ";
			$sql_select.= " LEFT JOIN tbl_referencias_coordenadas  WITH(NOLOCK) ON rc_re_id = re_id ";
			$sql_select.= " LEFT JOIN tbl_viajes_destinos WITH(NOLOCK) ON (re_id = vd_re_id AND vd_vi_id = ".(int)$this->id_viaje.")";
			$sql_select.= " WHERE  re_id = ".(int)$item['vd_re_id'];
			if((int)$item['vd_id']){
				$sql_select.= " AND vd_id = ".(int)$item['vd_id'];
			}
			
			$res = $this->objSQL->dbQuery($sql_select);
			$arr_vd = $this->objSQL->dbGetRow($res,0,3);
			
			$sql = $msg_log = $data_actual  = $data_update = $coma = '';
			if($arr_vd['vd_id'] && !$this->insertDestino){
				if(date('d-m-Y H:i',strtotime($item['vd_ini'])) != date('d-m-Y H:i',strtotime($arr_vd['vd_ini'])) && !empty($item['vd_ini'])){
					$data_actual = decode($lang->system->fecha_inicio).'['.formatearFecha($arr_vd['vd_ini']).']';	
					$data_update = decode($lang->system->fecha_inicio).'['.formatearFecha($item['vd_ini']).']';
					$coma = ', ';
				}				
				if(date('d-m-Y H:i',strtotime($item['vd_fin'])) != date('d-m-Y H:i',strtotime($arr_vd['vd_fin'])) && !empty($item['vd_fin'])){
					$data_actual = $coma.decode($lang->system->fecha_fin).'['.formatearFecha($arr_vd['vd_fin']).']';	
					$data_update = $coma.decode($lang->system->fecha_fin).'['.formatearFecha($item['vd_fin']).']';
					$coma = ', ';
				}
				if($arr_vd['vd_orden'] != (int)$item['vd_orden']){
					$data_actual = $coma.decode($lang->system->orden).'['.$arr_vd['vd_orden'].']';	
					$data_update = $coma.decode($lang->system->orden).'['.(int)$item['vd_orden'].']';
					$coma = ', ';
				}
				if(isset($item['vd_stock']) && $arr_vd['vd_stock'] != (int)$item['vd_stock']){
					$data_actual = $coma.'Stock ['.$arr_vd['vd_stock'].']';	
					$data_update = $coma.'Stock ['.(int)$item['vd_stock'].']';
					$coma = ', ';
				}
				else{
					$item['vd_stock'] = $arr_vd['vd_stock'];
				}

				if($item['vd_vencimiento'] != $arr_vd['vd_vencimiento']){
					$data_actual = $coma.'Fecha de Vencimiento ['.formatearFecha($arr_vd['vd_vencimiento']).']';	
					$data_update = $coma.'Fecha de Vencimiento ['.formatearFecha($item['vd_vencimiento']).']';
					$coma = ', ';
				}
				if(isset($item['vd_checked']) && $arr_vd['vd_checked'] != (int)$item['vd_checked']){
					$data_actual = $coma.'Estado ['.$arr_vd['vd_checked'].']';	
					$data_update = $coma.'Estado ['.(int)$item['vd_checked'].']';
					$coma = ', ';
				}
				if(!empty($data_update)){
					##-- TXT Log --##
					$msg_log = ' '.str_replace('[DATOS_ACTUALES]',($arr_vd['re_nombre'].' ('.$arr_vd['rc_latitud'].','.$arr_vd['rc_longitud'].'), '.$data_actual),decode($lang->system->edicion_referencia->__toString()));
					$msg_log = str_replace('[DATOS_EDITADOS]',$data_update,$msg_log);
					##-- --##
					
					$item['vd_ini'] = !empty($item['vd_ini'])?"'".$item['vd_ini']."'":'NULL';
					$item['vd_fin'] = !empty($item['vd_fin'])?"'".$item['vd_fin']."'":'NULL';
					$item['vd_stock'] = !empty($item['vd_stock'])?"'".$item['vd_stock']."'":'NULL';
					$item['vd_vencimiento'] = !empty($item['vd_vencimiento'])?"'".$item['vd_vencimiento']."'":'NULL';
					$item['vd_checked'] = !empty($item['vd_checked'])?"'".$item['vd_checked']."'":'NULL';
				
					$sql = " UPDATE tbl_viajes_destinos SET ";
					$sql.= " vd_orden = ".(int)$item['vd_orden'];
					if($item['vd_ini'] != 'NULL'){
					$sql.= ", vd_ini = ".$item['vd_ini'];
					}
					if($item['vd_fin'] != 'NULL'){
					$sql.= ", vd_fin = ".$item['vd_fin'];
					}
					$sql.= ", vd_stock = ".$item['vd_stock'];
					$sql.= ", vd_vencimiento = ".$item['vd_vencimiento'];
					$sql.= ", vd_checked = ".$item['vd_checked'];
					$sql.= " WHERE vd_vi_id = ".$this->id_viaje." AND vd_re_id = ".(int)$item['vd_re_id'];	
					if((int)$item['vd_id']){
						$sql_select.= " AND vd_id = ".(int)$item['vd_id'];
					}
				}
			}
			else{
				##-- TXT Log --##
				$msg_log = $lang->system->alta_referencia.': '.$arr_vd['re_nombre'].' ('.$arr_vd['rc_latitud'].','.$arr_vd['rc_longitud'].')';
				$msg_log.= !empty($item['vd_ini'])?', '.$lang->system->fecha_inicio.'['.formatearFecha($item['vd_ini']).']':'';
				$msg_log.= !empty($item['vd_fin'])?', '.$lang->system->fecha_fin.'['.formatearFecha($item['vd_fin']).']':'';
				$msg_log.=', '.$lang->system->orden.'['.(int)$item['vd_orden'].']';
				##-- --##
				$item['vd_ini'] = !empty($item['vd_ini'])?"'".$item['vd_ini']."'":'NULL';
				$item['vd_fin'] = !empty($item['vd_fin'])?"'".$item['vd_fin']."'":'NULL';
				$item['vd_stock'] = !empty($item['vd_stock'])?"'".$item['vd_stock']."'":'NULL';
				
				$sql = " INSERT INTO tbl_viajes_destinos(vd_vi_id, vd_re_id, vd_ini, vd_fin, vd_orden, vd_estado, vd_stock) ";
				$sql.= " VALUES(".$this->id_viaje.", ".(int)$item['vd_re_id'].",".$item['vd_ini'].",".$item['vd_fin'].",".(int)$item['vd_orden'].",0,".$item['vd_stock'].")";	
				$esInsert = true;
			}

			if(!empty($sql)){
				if(!$this->objSQL->dbQuery($sql)){
					$resp = false;	
				}
				else{
					if($esInsert){
						$resp = $this->objSQL->dbLastInsertId();
						
						//-- Enviar Mail  en caso que la referencia tenga un email cargado. --//
						if($arr_vd['re_email']){
							
							$auxEncode = codificarURL($this->id_viaje);
							$axu1 = $auxEncode['url_encode'];
							
							$auxEncode = codificarURL($resp);
							$axu2= $auxEncode['url_encode'];

							$link_seguimiento = 'https://www.localizar-t.com/shootup/dashboard/traking/'.$axu1.'/'.$axu2;
							$this->sendEmailDestinos($arr_vd['re_email'],$resp, $link_seguimiento);
						}
						//-- --//
					}
					$this->setLog($msg_log);
				}
			}
			
		}
		
		return $resp;
	}
	
	function sendEmailDestinos($email,$cod_traking, $link_traking){
		$objIdioma = new Idioma();
		$idioma = !empty($_SESSION['idioma'])?$_SESSION['idioma']:getIdiomaBrowser();
		$langEmail = $objIdioma->getEmails($idioma);
					
		$cuerpo_mail = $langEmail->info_traking_viajes->data;
		if(!empty($cuerpo_mail)){
			$cuerpo_mail = str_replace('[COD_TRAKING]',$cod_traking, $cuerpo_mail);
			$cuerpo_mail = str_replace('[LINK_TRAKING]',$link_traking, $cuerpo_mail);
			$cuerpo_mail = idiomaHTML($cuerpo_mail);
					
			$subject_mail = $langEmail->info_traking_viajes->subject;
			$subject_mail = str_replace('[COD_TRAKING]',$cod_traking, $subject_mail);
			
			require_once ('clases/clsEmailer.php');
			global $objSQLServer;
			$objEmailer = new Emailer($objSQLServer);
			
			$emailerMsg['asunto'] = $subject_mail;
			$emailerMsg['contenido'] = decode($objEmailer->getContenidoHTML($cuerpo_mail));
			$id_contenido = $objEmailer->setContenidoEmailer($emailerMsg);
		
			if($id_contenido){
				$emailerInfo['id_contenido'] = $id_contenido;
				$emailerInfo['id_usuario'] = NULL;
				$emailerInfo['remitente_mail'] = NULL;
				$emailerInfo['remitente_name'] = NULL;
				$emailerInfo['responder_a'] = NULL;
				$emailerInfo['destinatario_mail'] = $email;
				$emailerInfo['destinatario_name'] = NULL;
				$emailerInfo['prioridad'] = 4;	
				$objEmailer->setInfoEmailer($emailerInfo);
			}
		}
	}
	
	function deleteViaje(){
		global $lang;
		if($this->id_viaje){
			$sql = " UPDATE tbl_viajes SET vi_borrado = 1 WHERE vi_id = ".$this->id_viaje;	
			if($this->objSQL->dbQuery($sql)){
				$this->setLog($lang->system->baja_viaje);
				return true;
			}
		}
		return false;
	}
	
	function getTiempoViaje(){
		global $lang;
		$sql = " SELECT MIN(vd_ini) prog_ini, MAX(vd_ini) prog_fin, MIN(vd_ini_real) real_ini, MAX(vd_ini_real) real_fin ";
		$sql.= " FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje;
		
		$res = $this->objSQL->dbQuery($sql);
		$rs=$this->objSQL->dbGetAllRows($res);	
		
		$programado = (strtotime($rs[0]['prog_fin']) - strtotime($rs[0]['prog_ini']));
		$time['programado'] = round($programado/3600).$lang->system->abrev_hora;
			
		$real = (strtotime($rs[0]['real_fin']) - strtotime($rs[0]['real_ini']));
		$time['real'] = round($real/3600).$lang->system->abrev_hora;
		
		return $time;
	}
	
	function getEstadoViaje($id_viaje, $simple = false){
		$status = "";
		
		$sql = " SELECT TOP 1 vd_ini_real, vd_fin_real, vd_orden, re_nombre, rc_latitud, rc_longitud, vi_finalizado, re_radioIngreso ";
		$sql.= " FROM tbl_viajes_destinos WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes WITH(NOLOCK) ON vi_id = vd_vi_id ";
		$sql.= " LEFT JOIN tbl_referencias WITH(NOLOCK) ON re_id = vd_re_id ";
		$sql.= " LEFT JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON rc_re_id = re_id ";
		$sql.= " WHERE vd_vi_id = ".(int)$id_viaje;
		$sql.= " AND (vd_ini_real IS NULL OR vd_fin_real IS NULL) ";
		$sql.= " ORDER BY vd_orden ";
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);
		
		$arr_estado = array('iniciado' => NULL
					, 'pendiente_inicio' => NULL
					, 'en_transito' => NULL
					, 'en_cliente' => NULL
					, 'en_origen' => NULL
					, 'finalizado' => NULL
					, 'referencia' => $rs['re_nombre']
					, 'radio' => $rs['re_radioIngreso']
					, 'latitud' => $rs['rc_latitud']
					, 'longitud' => $rs['rc_longitud']
			);
		
		
		if($rs['vi_finalizado'] == 1 || !$rs){
			$arr_estado['finalizado'] = 'on';
			$status = 'viaje_finalizado';
		}
		elseif(empty($rs['vd_ini_real']) && empty($rs['vd_fin_real']) && $rs['vd_orden'] == 0){//pendiente de inicio
			$arr_estado['pendiente_inicio'] = 'on';
			$status = 'pendiente_inico';	
		}
		elseif(empty($rs['vd_ini_real']) && empty($rs['vd_fin_real']) && $rs['vd_orden'] > 0){
			$arr_estado['iniciado'] = 'on';
			$arr_estado['en_transito'] = 'on';
			$status = 'en_transito';	
		}
		elseif(!empty($rs['vd_ini_real']) && empty($rs['vd_fin_real']) && $rs['vd_orden'] == 0){
			$arr_estado['iniciado'] = 'on';	
			$arr_estado['en_origen'] = 'on';
			$status = 'en_origen';
		}
		elseif(!empty($rs['vd_ini_real']) && empty($rs['vd_fin_real']) && $rs['vd_orden'] > 0){
			$arr_estado['iniciado'] = 'on';
			$arr_estado['en_cliente'] = 'on';
			$status = 'en_cliente';	
		}
		
			return ($simple == true)?$status:$arr_estado;
	}
	
	function getTrasnportistaPorMovil($idMovil){
		$sql = " SELECT mo_id_cliente_facturar FROM tbl_moviles WITH(NOLOCK) WHERE mo_id = ".(int)$idMovil;
		$objRes = $this->objSQL->dbQuery($sql);	
		$res = $this->objSQL->dbGetRow($objRes,0,3);
		return $res['mo_id_cliente_facturar'];
	}
	
	/*
	function getFechaDesdeHastaHistoricoViaje(){
		$sql = " DECLARE @hoy DATETIME = (SELECT DATEADD(HOUR,server,CURRENT_TIMESTAMP) FROM zonaHoraria(NULL,".$_SESSION['idUsuario']."))";
		$sql.= " SELECT 
			CASE
				WHEN MIN(vd_ini_real) IS NULL THEN MIN(vd_ini)
				WHEN MIN(vd_ini) < MIN(vd_ini_real) THEN MIN(vd_ini)
				ELSE MIN(vd_ini_real)
			END 'desde' ";
		$sql.= ", 
			CASE
				WHEN MAX(vd_fin) > MAX(vd_fin_real) AND MAX(vd_fin) < @hoy THEN MAX(vd_fin)
				WHEN MAX(vd_fin_real) > MAX(vd_fin) AND MAX(vd_fin_real) < @hoy THEN MAX(vd_fin_real)
				ELSE @hoy
			END 'hasta' ";
		
		$sql.= " FROM tbl_viajes_destinos ";
		$sql.= " WHERE vd_vi_id = ".$this->id_viaje;
		
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);	
		$desde = $rs['desde'];		
		$hasta = $rs['hasta'];		
		
		return array('desde'=>$desde, 'hasta'=>$hasta);
	}*/
	
	
	function getFormatFechaHoraPHPExcel($fechaExcel, $horaExcel){
		/*
		$fechaExcel = trim(str_replace('/','-',str_replace('.','-',$fechaExcel)));
		$horaExcel = trim($horaExcel);
		$fecha = (date('Y-m-d', strtotime($fechaExcel)));
		*/
		
		$strpos = strpos($fechaExcel,'.');
		if($strpos !== false){
			return false;
		}
			
		//-- LA FECHA TIENE Q VENIR CON BARRA PARA Q LA LIBRERIA PUEDA FORMATEARLA, +1 PORQ RETORNA UN DIA MENOS --//
		//$fecha = date('Y-m-d',strtotime('+1 day',strtotime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($fechaExcel)))));
		$fecha = date('Y-m-d',strtotime('+1 day',strtotime($fechaExcel)));
		//--//
		
		//$hora = (isset($horaExcel) && $horaExcel != '00:00:00')?$horaExcel:'00:00:01';
		$hora = (isset($horaExcel) && $horaExcel != '00:00:00')?$horaExcel:'23:59:59';
		$fechaHora = $fecha.' '.$hora;
		
		if (strtotime($fechaHora) === false){
			//$hora = date('H:i:s',strtotime('+3 hours',strtotime(date('H:i:s', PHPExcel_Shared_Date::ExcelToPHP($horaExcel)))));
			$hora = date('H:i:s',strtotime('+3 hours',strtotime($horaExcel)));
			$fechaHora = $fecha.' '.$hora;
		}
		return $fechaHora;
	}
	
	function getFormatFechaHora($fecha, $hora){
		$fecha = trim(str_replace('/','-',str_replace('.','-',$fecha)));
		$hora = trim($hora);
		$hora = empty($hora)?'00:00:00':$hora;
		
		
		if(!empty($fecha) && !empty($hora)){
			$fecha = (date('Y-m-d', strtotime($fecha)));
			
			//$hora = (isset($hora) && $hora != '00:00:00')?$hora:'00:00:01';
			$hora = (isset($hora) && $hora != '00:00:00')?$hora:'23:59:59';
			$fechaHora = $fecha.' '.$hora;
			
			if (strtotime($fechaHora) === false){
				$hora = date('H:i:s',getFechaServer());
				$fechaHora = $fecha.' '.$hora;
			}
			return $fechaHora;
		}
		return false;
	}		
	
	function getConductorViaje($conductor){
		$sql = " SELECT co_id FROM tbl_conductores WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON cl_id = co_cl_id ";
		$sql.= " WHERE co_borrado = 0 AND cl_id_distribuidor = ".$_SESSION["idEmpresa"];
		$sql.= " AND (co_nombre = '".$conductor."' OR co_apellido = '".$conductor."' OR co_nombre+' '+co_apellido = '".$conductor."' OR co_apellido+' '+co_nombre = '".$conductor."')";
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);
		if($this->objSQL->dbNumRows($res)){
			return $rs['co_id'];
		}
		return false;			
	}
		
	function Importar_Excel_SAP($files){
		define('ERR_SIN_PERMISOS', 4);
		require_once('clases/PHPExcel/IOFactory.php');
		$objExcel = PHPExcel_IOFactory::load($files['tmp_name']);
		/*
		try{
			$objHoja[0] = $objExcel->getSheet(0)->toArray(NULL,true,false,true);
		}
		catch(Exception $e){
			return 'La Hoja 1, de la planilla de excel que intenta importar genera un error. Verifique que la misma no contenga columnas calculadas.';
		}

		try{
			$objHoja[1] = $objExcel->getSheet(1)->toArray(NULL,true,false,true);
		}
		catch(Exception $e){
			return 'La Hoja 2, de la planilla de excel que intenta importar genera un error. Verifique que la misma no contenga columnas calculadas.';
		}
		
		unset($objHoja[0][1]);
		unset($objHoja[1][1]);
		*/

		//-- Se implementa esto porque formatea mal las fechas --//
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
					if(PHPExcel_Shared_Date::isDateTime($cell)){
						if(strtotime($cell->getFormattedValue())){
							$value = $cell->getFormattedValue();
						}
						else{
							$value = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
						}
					}
				}
			
				$objHoja[0][$iRow][$celdas[$iCell]] = $value;
				$iCell++;
			}
			$iRow++;
		}

		$objHojaExcel = $objExcel->getSheet(1);
		$iRow = 0;
		foreach ($objHojaExcel->getRowIterator() as $row){
			
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
			$iCell = 0;
			$objHoja[1][$iRow] = array();
			foreach ($cellIterator as $cell){
				
				if(!is_null($cell)){
					$value = $cell->getValue();
					if(PHPExcel_Shared_Date::isDateTime($cell)){
						if(strtotime($cell->getFormattedValue())){
							$value = $cell->getFormattedValue();
						}
						else{
							$value = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
						}						
					}
				}
			
				$objHoja[1][$iRow][$celdas[$iCell]] = $value;
				$iCell++;
			}
			$iRow++;
		}
		//-- --//
		unset($objHoja[0][0]);
		unset($objHoja[1][0]);

		$viajes = array();
		$registros['repetidos'] = array(); //RDS
		$registros['sinDestinos'] = array();
		$registros['sinTransportistas'] = array();
		$cantidad['repetidos'] = 0;
		$cantidad['sinDestinos'] = 0;
		$cantidad['sinTransportistas'] = 0;
		$cantidad['insertados'] = 0;
		
		//// ETAPA 1 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		foreach($objHoja[0] as $k => $hoja1){
			if(empty($hoja1['A'])){
				unset($objHoja[0][$k]);	
			}
		}
		
		foreach($objHoja[0] as $k => $hoja1){
			
			$cod_viaje = trim($hoja1['A']);
			if(!isset($cod_viaje)){
				return 'Error Codigo del Viaje (Hoja1, A:'.$k.')';
			}
			$viajes[$cod_viaje] = array("codigo" => $cod_viaje);

			// -- Fecha hora Inicial del Viaje (Fe.pl.reg.)--//
			if (!isset($hoja1['N'])){
				return 'Error en la definicion de Fecha Inicial del Viaje (Hoja1, N:'.$k.')';
			}
			
			$viajes[$cod_viaje]['tiempo_inicial_orden_0'] = $this->getFormatFechaHoraPHPExcel($hoja1['N'], $hoja1['B']);
			if (strtotime($viajes[$cod_viaje]["tiempo_inicial_orden_0"]) === false){
				return 'Error en la definicion de Fecha-Hora Inicial del Viaje (Hoja1, N-B:'.$k.'). Formato v&aacute;lido: dd/mm/yyyy.';
			} 
			
			//-- Número de boca, para el orden 0: --//
			if (!isset($hoja1['E'])){
				return 'Número de Boca Inexistente (Hoja1, E:'.$k.')';
			}
			$viajes[$cod_viaje]['numboca_orden_0'] = trim($hoja1['E']);
			
			//-- Transportista (obtiene momentáneamente el CUIT) --//
			if (!isset($hoja1['G'])){
				return 'Transportista Inexistente (Hoja1, G:'.$k.')';
			}
			$viajes[$cod_viaje]["transportista"] = trim($hoja1['G']);
			
			//-- Texto adicional 1: --//
			$viajes[$cod_viaje]["texto_adicional_1"] = trim(isset($hoja1['I'])?$hoja1['I']:'');
			
			//-- Texto adicional 2: --//
			$viajes[$cod_viaje]["texto_adicional_2"] = trim(isset($hoja1['J'])?$hoja1['J']:'');
			
			//-- Fecha y hora final, para el orden 0: --//
			if (!isset($hoja1['O'])){
				return 'Error en la definicion de Fecha-Hora Final del Viaje (Hoja1, O:'.$k.')';
			}
			$viajes[$cod_viaje]['tiempo_final_orden_0'] = $this->getFormatFechaHoraPHPExcel($hoja1['O'], $hoja1['K']);
			if (strtotime($viajes[$cod_viaje]['tiempo_final_orden_0']) === false){
				return 'Error en la definicion de Fecha-Hora Final del Viaje (Hoja1, O-K:'.$k.'). Formato v&aacute;lido: dd/mm/yyyy.';
			}
			
			if (!isset($hoja1['C'])){
				return 'Error Tipo de Viaje (Hoja1, C:'.$k.')';
			}
			$tipoViaje = trim($hoja1['C']);
			
			if(!empty($tipoViaje)){
				$sql = " SELECT TOP 1 vt_id FROM tbl_viajes_tipo WITH(NOLOCK) WHERE vt_nombre = '".$tipoViaje."'";
 				$res = $this->objSQL->dbQuery($sql);
				$rs = $this->objSQL->dbGetRow($res,0,3);
				$idTipoViaje = $rs['vt_id'];
			}
			$viajes[$cod_viaje]['id_tipo_viaje'] = $idTipoViaje?(int)$idTipoViaje:1;
			
			//Movil:
			$matricula = trim($hoja1['Q']);
			if($matricula){
				$arrMovil = $this->getMovilViaje($matricula);
				if($arrMovil['mo_id']){
					$viajes[$cod_viaje]["movil"] = $arrMovil['mo_id'];	
				}
			}
			$viajes[$cod_viaje]["movil"] = isset($viajes[$cod_viaje]["movil"])?$viajes[$cod_viaje]["movil"]:'NULL';	
		}

		// LECTURA DE DESTINOS ------------------------------------------------------
		foreach($objHoja[1] as $k => $hoja2){
		
			//Código del viaje (Embarque):
			$cod_viaje = trim($hoja2['A']);
			if(!isset($cod_viaje)){
				return 'Error Código del Viaje (Hoja2, A:'.$k.')';
			}
			
			if(isset($viajes[$cod_viaje])){
				if (!isset($viajes[$cod_viaje]["referencias"])){
					$viajes[$cod_viaje]["referencias"] = array(); //creo el array referencias
				}
				
				if(!isset($hoja2['C'])){
					return 'El Cliente se encuentra vacio (Hoja2, B:'.$k.')';		
				}
				
				//busco que el destino a agregar no este repetido:
				$yaExiste = 0;
				$hub_client = isset($hoja2['C'])?$hoja2['C']:'';
				for($fdr = 0; $fdr<count($viajes[$cod_viaje]["referencias"]); $fdr++){					
					if($viajes[$cod_viaje]["referencias"][$fdr]["cliente_hub"] == $hub_client){
						$yaExiste = 1;					  
					}					
				}
				
				//agrego el destino:
				if($yaExiste == 0){
					$vd_ini = $this->getFormatFechaHoraPHPExcel($hoja2['E'], $hoja2['F']);
					if (strtotime($vd_ini) === false){
						return 'Error en la definicion de Fecha-Hora en el siguiente Destino (Hoja2, E-F:'.$k.'). Formato v&aacute;lido: dd/mm/yyyy.';
					}
					$vd_fin = date('Y-m-d H:i:s',strtotime('+2 hours',strtotime($vd_ini)));
					$viajes[$cod_viaje]['referencias'][] = array('cliente_hub'=>$hub_client,'vd_ini'=>$vd_ini,'vd_fin'=>$vd_fin);
				}				
			}
		}

		//// ETAPA 2 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Valida que no haya quedado ningún viaje sin referencias y que estas existan en tbl_referencias:
		foreach ($viajes as $cod_clave => $viaje) {
			
			//Revisa si el transportista existe, a base de su Nº de CUIT:
			$resultadoTransportista = !empty($viaje['transportista'])?$this->getIdTransportista($viaje['transportista']):0;
						
			if (!isset($viaje["referencias"])){ // viajes sin destino -----------------------------------------------------------
				$registros['sinDestinos'][] = "'".$viaje["codigo"]."'";
				$cantidad['sinDestinos']++;	
			}
			elseif($resultadoTransportista == 0 ){ // viajes sin transportista -------------------------------------------------
				$registros['sinTransportistas'][] = "'".$viaje["codigo"]."'";
				$cantidad['sinTransportistas']++;	
			}
			else{ // viaje correcto ---------------------------------------------------------------------------------------------
				$viajes[$cod_clave]["transportista"] = $resultadoTransportista;
				
				$rs = $this->existeReferencia("'".$viaje["numboca_orden_0"]."'", 0);
				if (!isset($rs['total'])){
					return 'No existe en la BD la referencia :'.$viaje['numboca_orden_0'];
				}
			}
		}
			
		//¿Hay usuario?:
		if (!isset($_SESSION["idUsuario"]) || ($_SESSION["idUsuario"] * 1 < 1)){
			return ERR_SIN_PERMISOS;			
		} 
		
		//====================================================================
		//Todo está validado y es posible practicar la inserción/update para cada viaje: 
		//====================================================================
		foreach($viajes as $viaje) {
			if (isset($viaje["referencias"])){ //solo si tiene destinos, procedera
				
				//----------------- si un Viaje ya existe, lo actualizo e informo --------------------------------------------
				$sqlAux = " SELECT vi_id ";
				$sqlAux.= " FROM tbl_viajes WITH(NOLOCK) ";
				$sqlAux.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_id = vi_us_id ";
				$sqlAux.= " WHERE vi_borrado = 0 AND vi_codigo = '".$viaje['codigo']."' AND us_cl_id = ".$_SESSION['idEmpresa'];
				$res = $this->objSQL->dbQuery($sqlAux);
				$rs = $this->objSQL->dbGetRow($res,0,3);
				if($rs['vi_id']){
					$registros['repetidos'][] = "'".$viaje['codigo']."'"; 
					$cantidad['repetidos'] ++;					
				}		
				$cantidad['insertados'] ++;

				##-- Alta/Update --##
				$campos = array();
				$valorCampos = array();
				array_push($campos,'vi_codigo');
				array_push($valorCampos,"'".$viaje['codigo']."'");
				array_push($campos,'vi_us_id');
				array_push($valorCampos,$_SESSION['idUsuario']);
				array_push($campos,'vi_mo_id');
				array_push($valorCampos,$viaje['movil']);
				array_push($campos,'vi_co_id');
				array_push($valorCampos,0);
				array_push($campos,'vi_observaciones');
				array_push($valorCampos,"'".$viaje['texto_adicional_1'].' / '.$viaje['texto_adicional_2']."'");
				array_push($campos,'vi_finalizado');
				array_push($valorCampos,0);
				array_push($campos,'vi_borrado');
				array_push($valorCampos,0);
				array_push($campos,'vi_dador');
				array_push($valorCampos,$_SESSION['idEmpresa']);
				array_push($campos,'vi_vt_id');
				array_push($valorCampos,$viaje['id_tipo_viaje']);
				array_push($campos,'vi_transportista');
				array_push($valorCampos,$viajes[$viaje['codigo']]['transportista']);
				array_push($campos,'vi_delivery');
				array_push($valorCampos,0);
				
				if($rs['vi_id']){ //si esta repetido actualizo
					$this->id_viaje = $rs['vi_id'];
					$this->updateViajes($campos,$valorCampos);
				}
				else{//no estaba repetido, tengo que insertarlo
					$this->id_viaje = $this->setViajes($campos,$valorCampos);
				}
				$cod_insercion_viaje = $this->id_viaje;
						
				//Buscamos el destino orden 0 del viaje (si lo tiene)
				$sql = " SELECT vd_id FROM tbl_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = ".$this->id_viaje." AND vd_orden = 0 ";
				$res = @$this->objSQL->dbQuery($sql);				
				$destBDD = $this->objSQL->dbGetRow($res,0,3); //id del destino orden 0 en la base de datos
				$destBDD['vd_id'] = isset($destBDD['vd_id'])?$destBDD['vd_id']:0;
				//---- Ahora Insertamos/actualizamos destinos del viaje ----------------------------------------------------------------
				$destinosImpactados = array();
				
				//Primeramente, la referencia de orden 0:
				$idReferencia = $this->getReferenciaNumBoca($viaje['numboca_orden_0']);
				$idReferencia = $idReferencia?$idReferencia:6464;
				$arr_destinos[0]['vd_re_id'] = $idReferencia;
				$arr_destinos[0]['vd_orden'] = 0;
				$arr_destinos[0]['vd_ini'] = date('Y-m-d H:i:s',strtotime(str_replace('/','-',$viaje['tiempo_inicial_orden_0'])));
				$arr_destinos[0]['vd_fin'] = date('Y-m-d H:i:s',strtotime(str_replace('/','-',$viaje['tiempo_final_orden_0'])));
				
				if($destBDD['vd_id']){					
					$arr_destinos[0]['vd_id'] = $destBDD['vd_id'];
					if($this->setViajesDestinos($arr_destinos)){
						$destinosImpactados[] = $destBDD['vd_id'];
					}
				}
				else{
					$vd_id = $this->setViajesDestinos($arr_destinos);
					if($vd_id){
						$destinosImpactados[] = $vd_id;
					}
				}
				
				$conteo_referencias = 1;
				foreach($viaje["referencias"] as $referencia){
					
					$idReferencia = $this->getReferenciaNumBoca($referencia["cliente_hub"]);
					$idReferencia = $idReferencia?$idReferencia:6464;
					
					$vd_ini = $referencia['vd_ini'];	
					$vd_fin = $referencia['vd_fin'];
					
					//---------------------------------------------------------
					//Buscamos el destino orden n del viaje (si lo tiene)
					$sql = " SELECT vd_id FROM tbl_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = ".$cod_insercion_viaje." AND vd_re_id = ".$idReferencia;
					$res = $this->objSQL->dbQuery($sql);				
					$destBDD = $this->objSQL->dbGetRow($res,0,3); //id del destino orden n en la base de datos					
					$destinoExistente = $destBDD['vd_id'];
					
					// INSERTO/ACTUALIZO :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
					$arr_destinos[0]['vd_re_id'] = $idReferencia;
					$arr_destinos[0]['vd_orden'] = $conteo_referencias;
					$arr_destinos[0]['vd_ini'] = $vd_ini;
					$arr_destinos[0]['vd_fin'] = $vd_fin;
					
					if($destinoExistente){
						$arr_destinos[0]['vd_id'] = $destinoExistente;
						if($this->setViajesDestinos($arr_destinos)){
							$destinosImpactados[] = $destinoExistente; 
						}
					}
					else{
						$vd_id = $this->setViajesDestinos($arr_destinos);
						if($vd_id){
							$destinosImpactados[] = $vd_id;
						}
					}
					
					$conteo_referencias++;
				}

				// Ahora debo borrar los destinos que hay en la base de datos que no existian en el excel.
				// busco los destinos del viaje
				$sql = "SELECT TOP 1 vi_id FROM tbl_viajes WITH(NOLOCK) WHERE vi_codigo = '".$viaje['codigo']."'";
				$res = $this->objSQL->dbQuery($sql);				
				$idDelViaje = $this->objSQL->dbGetRow($res,0,3);			
				
				$sql = "SELECT vd_id FROM tbl_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = ".(int)$idDelViaje['vi_id'];
				$res = $this->objSQL->dbQuery($sql);				
				$destinosDataBase = $this->objSQL->dbGetAllRows($res);
			
				//busco los destinos impactados en los destinos de la base
				for($r=0;$r<count($destinosDataBase);$r++){
					for($s=0;$s<count($destinosImpactados);$s++){
						if($destinosDataBase[$r]['vd_id']==$destinosImpactados[$s]){
							$destinosDataBase[$r]['vd_id'] = 0; //borro los id impactados
						}
					}
				}
				$destinosParaBorrar='';
				for($r=0;$r<count($destinosDataBase);$r++){
					if($destinosDataBase[$r]['vd_id']){$destinosParaBorrar .= ','.$destinosDataBase[$r]['vd_id'];}
				}								
				if($destinosParaBorrar!=''){
					$destinosParaBorrar=substr($destinosParaBorrar,1);
					// borro los destinos que sobran
					$sql = "DELETE FROM tbl_viajes_destinos WHERE vd_id IN(".$destinosParaBorrar.") AND vd_vi_id = ".(int)$idDelViaje['vi_id'];
					$this->objSQL->dbQuery($sql);
				}
			}				
		}		
		
		//// ETAPA 3 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//-- Subida de Archivo --//
		$serverFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
		$rutaServer = $barra = '';
		foreach($serverFile as $item){
			if(strpos(strtolower($item),'.php') === false){
				$rutaServer.= $barra.$item;
				$barra = '/';
			}
		}
		
		$ruta = PATH_ATTACH.'/viajes/'.$_SESSION['idEmpresa']."/";
		if(!file_exists($ruta)){
			mkdir($ruta);				
		}
		$archivo = $ruta.getFechaServer('Ymd_His').' - '.$files['name']; 				
		copy($files['tmp_name'], $archivo);
		chown($rutaServer.'/'.$archivo, 'root');
							
		//-------------------- GENERO EL MSJ ------------
		$totalRegistros = $cantidad['insertados'] + $cantidad['sinDestinos'] + $cantidad['sinTransportistas'];
	
		$resumen  = "Se han procesado ".$cantidad['insertados']." de ".$totalRegistros." viajes. ";
		$resumen .= "<br><br>Se actualizaron ".$cantidad['repetidos']." viajes que ya exist&iacute;an. <br>";	
			
		if($cantidad['sinTransportistas'] !=0 ){
			$resumen .= "<br><br>Los viajes con transportistas invalidos (".$cantidad['sinTransportistas'].") que no se procesaron son los siguientes:";
			
			for($ixi=0;$ixi<count($registros['sinTransportistas']);$ixi++){				
				$resumen .= " <br>".$registros['sinTransportistas'][$ixi];
			}
		}
			
		if($cantidad['sinDestinos'] != 0){
			$resumen .= "<br><br>Los viajes sin destinos asociados (".$cantidad['sinDestinos'].") que no se procesaron son los siguientes:";
				
			for($ixi=0;$ixi<count($registros['sinDestinos']);$ixi++){				
				$resumen .= " <br>".$registros['sinDestinos'][$ixi];
			}
		}
		
		return $resumen;
	}
	
	function getMovilViaje($matricula){
		$aux = str_replace('  ','',str_replace('-','',trim($matricula)));

		$sql = " SELECT mo_id, mo_id_cliente_facturar FROM tbl_moviles WITH(NOLOCK) ";
		$sql.= " WHERE mo_borrado = 0 AND mo_id_distribuidor = ".(int)$_SESSION['idAgente'];
		$sql.= " AND (mo_matricula = '".trim($matricula)."' OR mo_matricula = '".$aux."')";
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetRow($res,0,3);
		return $rs;
	}
	
	function getIdTransportista($cuit = NULL, $razonSocial = NULL, $create = false){
		
		$sql = "SELECT cl_id FROM tbl_clientes WITH(NOLOCK) WHERE cl_id_distribuidor = ".(int)$_SESSION['idAgente'];
		if(!empty($cuit)){
			$sql.= " AND cl_cuit = '".$cuit."'";	
		}
		elseif(!empty($razonSocial)){
			$sql.= " AND cl_razonSocial = '".$razonSocial."'";	
		}
		else{
			return false;	
		}
			
		$res = $this->objSQL->dbQuery($sql);
		$rs = $this->objSQL->dbGetAllRows($res);
		if($rs[0]){
			return $rs[0]['cl_id'];
		}
		elseif($create == true){
			$params = array(
				'cl_id_distribuidor' => $_SESSION['idAgente']
				,'cl_cuit' => !empty($cuit) ? trim($cuit) : NULL
				,'cl_razonSocial' => !empty($razonSocial) ? trim($razonSocial) : NULL
				,'cl_tipo' => 2
				,'cl_tipo_empresa' => 2
			);	
			return $this->objSQL->dbQueryInsert($params, 'tbl_clientes');
		}
		
		return false;
	}
	
	function existeReferencia($idsReferencias, $esHub = 0){
		if(!$esHub){
			$strSQL = " SELECT COUNT(re_id) as total FROM tbl_referencias WITH(NOLOCK) ";
			$strSQL.= " WHERE re_numboca in (".$idsReferencias.") ";
		}
		else{
			$strSQL = " SELECT COUNT(re_id) as total FROM tbl_referencias WITH(NOLOCK) ";
			$strSQL.= " INNER JOIN tbl_hubs WITH(NOLOCK) ON tbl_referencias.re_id = tbl_hubs.hu_re_id ";
			$strSQL.= " WHERE tbl_hubs.hu_nombre in (".$idsReferencias.") ";
		}
		
		$res = $this->objSQL->dbQuery($strSQL);
		$rs = $this->objSQL->dbGetRow($res,0,3);
		return $rs;
	}
	
	function getReferenciaNumBoca($numboca){
		if(!empty($numboca)){
			$sql = "SELECT re_id FROM tbl_referencias WITH(NOLOCK) WHERE re_numboca = '".$numboca."' AND re_borrado = 0";
			$sql.= " AND re_us_id IN (SELECT us_id FROM tbl_usuarios WITH(NOLOCK) WHERE us_cl_id = ".(int)$_SESSION['idAgente'].") ";
			$res = $this->objSQL->dbQuery($sql);				
			$result = $this->objSQL->dbGetRow($res,0,3);
			if($result['re_id']){
				return $result['re_id'];	
			}
			else{
				//-- Ayuda a encontrar referencias cuando el nro boca inicia con Cero.
				$sql = "SELECT re_id FROM tbl_referencias WITH(NOLOCK) WHERE re_numboca = '".(int)$numboca."' AND re_borrado = 0";
				$sql.= " AND re_us_id IN (SELECT us_id FROM tbl_usuarios WITH(NOLOCK) WHERE us_cl_id = ".(int)$_SESSION['idAgente'].") ";
				$res = $this->objSQL->dbQuery($sql);				
				$result = $this->objSQL->dbGetRow($res,0,3);
				if($result['re_id']){
					return $result['re_id'];	
				}
			}
		}
		
		return false;	
	}

	function setReferencia($numboca, $nombre){
		if(!empty($numboca)){
			$params = array(
				're_nombre' => $nombre
				,'re_numboca' => $numboca
				,'re_radioIngreso' => 5000
				, 're_radioEgreso' => 5000
				,'re_tr_id' => 1
				,'re_us_id' => $_SESSION['idUsuario']
			);	
			$id = $this->objSQL->dbQueryInsert($params, 'tbl_referencias');
			if($id){
				$params = array(
					'rc_re_id' => $id
					,'rc_latitud' => 0
					,'rc_longitud' => 0
				);	
				$this->objSQL->dbQueryInsert($params, 'tbl_referencias_coordenadas');
				return $id;
			}
		}
		
		return null;
	}
	
	/*** Arribos y Partidas ***/
	function getArribosPartidas($tipo, $filtros = NULL){
		if($tipo != 'arribos' && $tipo != 'partidas'){return false;}
		
		$sql= " SELECT DISTINCT trans.cl_razonSocial as transportista,  dador.cl_razonSocial as dador, vi_id, vi_codigo
		, mo.mo_matricula as vi_movil, re_id, re_nombre
		, DATEDIFF(ss, vd_ini, vd_ini_real) as diferenciaIngreso, DATEDIFF(ss, vd_fin, vd_fin_real) as diferenciaEgreso
		, vd.vd_orden
		, (co_nombre + ' ' + co_apellido ) as co_conductor, co_telefono
		, rc_latitud, rc_longitud, sh_latitud, sh_longitud
		,vi_facturado, vi_observaciones , sh_rd_id ";
		//$sql.= " , sh_fechaRecepcion, sh_fechaGeneracion ,dr_valor";
		$sql.= " , vd_id , vd_ini, vd_fin, vd_ini_real, vd_fin_real, re_vel_promedio";
		$sql.= " ,trans.cl_id as id_transportista, co_id as id_conductor, mo.mo_id as id_movil, re_descripcion ";
		//$sql.= " ,um_us_id"; //-- verifico si tiene movil asignado para visalizar mapa --//
		
		$sql.= " FROM tbl_viajes vi WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_viajes_destinos vd WITH(NOLOCK) ON vd.vd_vi_id = vi.vi_id ";
		$sql.= " INNER JOIN tbl_referencias re WITH(NOLOCK) ON re.re_id = vd.vd_re_id ";
		$sql.= " INNER JOIN tbl_referencias_coordenadas WITH(NOLOCK) ON re_id = rc_re_id ";
		$sql.= " INNER JOIN tbl_usuarios us WITH(NOLOCK) ON us.us_id = vi.vi_us_id ";
		$sql.= " INNER JOIN tbl_clientes trans WITH(NOLOCK) ON vi.vi_transportista = trans.cl_id ";
		
		$sql.= " LEFT JOIN tbl_moviles mo WITH(NOLOCK) ON mo.mo_id = vi.vi_mo_id ";
		$sql.= " LEFT JOIN tbl_unidad WITH(NOLOCK) ON vi.vi_mo_id = un_mo_id ";
		$sql.= " LEFT JOIN tbl_sys_heart WITH(NOLOCK) ON sh_un_id = un_id ";
		//$sql.= " LEFT JOIN tbl_definicion_reportes WITH(NOLOCK) ON dr_id = sh_rd_id ";
		$sql.= " LEFT JOIN tbl_clientes dador WITH(NOLOCK) ON vi.vi_dador = dador.cl_id ";
		$sql.= " LEFT JOIN tbl_conductores cond WITH(NOLOCK) ON cond.co_id = vi.vi_co_id ";
		//$sql.= " LEFT JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_us_id = ".(int)$_SESSION['idUsuario']." AND um_mo_id = vi.vi_mo_id";
		
		$filtros['f_fin'] = getFechaServer('Y-m-d');
		$filtros['f_ini'] = date('Y-m-d',strtotime('-7 day',strtotime($filtros['f_fin'])));
		
		$sql.= $this->filtrosViajes($filtros);
		
		if($tipo == 'partidas'){
 			$sql.= " AND (vd_ini_real IS NOT NULL AND (vd_fin_real IS NULL OR vd_fin_real >= '".date('Y-m-d H:i:s',strtotime('-30 minutes',strtotime(getFechaServer('Y-m-d H:i'))))."')) ";
		}
		else{
			$sql.= " AND ((vd_ini_real IS NOT NULL AND vd_fin_real IS NULL) OR (vd_ini_real IS NULL AND vd_ini >= '".date('Y-m-d H:i:s',strtotime('-3 day',strtotime(getFechaServer('Y-m-d H:i'))))."'))";	
		}
		$sql.= " AND mo.mo_id IS NOT NULL  AND re_id != 6464  ";///*AND sh_rd_id != 76*/
		
		if($tipo == 'partidas'){
			$sql.= " ORDER BY vd_fin ASC, vd_fin_real DESC, vi_codigo "; 
		}
		else{
			$sql.= " ORDER BY vd_ini ASC, vd_ini_real DESC, vi_codigo "; 
		}
		
		$objRes=$this->objSQL->dbQuery($sql);
		$res=$this->objSQL->dbGetAllRows($objRes,3);
		
		return $res;
	}
	
	function getTrayectoEstimado($row){
		$km = distancia($row['sh_latitud'],$row['sh_longitud'], $row['rc_latitud'], $row['rc_longitud']);
		$velPromedio=$row['re_vel_promedio']?$row['re_vel_promedio']:$this->velocidad_promedio;
		
		$tiempoHH = ($km/$velPromedio);
		$tiempoMM = ($tiempoHH)*60;
		$tiempoSS = ($tiempoMM)*60;
		
		$calc = round($tiempoMM).' minute';
		$fecha = date('Y-m-d H:i',strtotime('+'.$calc, strtotime(getFechaServer('Y-m-d H:i'))));
		
		$return['km'] = $km;
		$return['segundos'] = $tiempoSS;
		$return['minutos'] = $tiempoMM;
		$return['fecha'] = $fecha;
		return $return;
	}
	/***  ***/	
	
	function getTrayectoEstimadoEntreDosReferencias($idRef_1, $idRef_2){
		$strSQL = " SELECT TOP 1 rc_latitud, rc_longitud FROM tbl_referencias_coordenadas WITH(NOLOCK) WHERE rc_re_id = ";
		$aux = $strSQL.' '.(int)$idRef_1." UNION ".$strSQL.' '.(int)$idRef_2;
		$res = $this->objSQL->dbQuery($aux);
		$arrCoord = $this->objSQL->dbGetAllRows($res,3);	
		
		$km = distancia($arrCoord[0]['rc_latitud'],$arrCoord[0]['rc_longitud'], $arrCoord[1]['rc_latitud'], $arrCoord[1]['rc_longitud']);
		return round((($km/$this->velocidad_promedio) * 60) * 60); // retorna en Segundos
	}
	
	function getDisponibilidadTransportistas($fecha){
		$fecha = date('Y-m-d', strtotime($fecha));
		$strSQL = " SELECT vdtm_mo_id FROM tbl_viajes_disponibilidad_transportistas WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_viajes_disponibilidad_transportistas_moviles WITH(NOLOCK) ON vdt_id = vdtm_vdt_id ";
		$strSQL.= " WHERE vdt_fecha = '".$fecha."' AND vdt_cl_id = ".(int)$_SESSION['idEmpresa'];
		$objRes = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($objRes,3);	
	}
	
	function setDisponibilidadTransportistas($fecha, $arrMoviles){
		$params = array();
		$params['vdt_cl_id'] = $_SESSION['idEmpresa']; 
		$params['vdt_fecha'] = date('Y-m-d', strtotime($fecha)); 
		$params['vdt_us_id'] = $_SESSION['idUsuario']; 
		
		$strSQL = " SELECT vdt_id FROM tbl_viajes_disponibilidad_transportistas WITH(NOLOCK) ";
		$strSQL.= " WHERE vdt_fecha = '".$params['vdt_fecha']."' AND vdt_cl_id = ".(int)$params['vdt_cl_id'];
		$objRes = $this->objSQL->dbQuery($strSQL);
		$res = $this->objSQL->dbGetRow($objRes,0,3);
		if($res['vdt_id']){
			$id = $res['vdt_id'];
			$strSQL = " DELETE FROM tbl_viajes_disponibilidad_transportistas_moviles WHERE vdtm_vdt_id = ".(int)$id;
			$this->objSQL->dbQuery($strSQL);
		}
		else{
			$id = $this->objSQL->dbQueryInsert($params, 'tbl_viajes_disponibilidad_transportistas');	
		}
		
		
		if($id){
			if(count($arrMoviles)){
				foreach($arrMoviles as $idMovil){
					$params = array();
					$params['vdtm_vdt_id'] = $id;
					$params['vdtm_mo_id'] = $idMovil;
					$this->objSQL->dbQueryInsert($params, 'tbl_viajes_disponibilidad_transportistas_moviles');
				}	
			}	
			return true;
		}
		return false;
	}
	
	function obtenerMovilesDisponibles($idTransportista, $id_viaje = NULL, $id_delivery = NULL){
		$fecha = NULL;
		
		if($id_delivery){
			$strSQL = " SELECT vdd_ini FROM tbl_viajes_destinos_delivery WITH(NOLOCK) where vdd_id = ".(int)$id_delivery;
			$objRes = $this->objSQL->dbQuery($strSQL);
			$res = $this->objSQL->dbGetRow($objRes,0,3);
			if(!empty($res['vdd_ini'])){
				$fecha = date('Y-m-d', strtotime($res['vdd_ini']));
			}
		}
		elseif($id_viaje){
			$strSQL = " SELECT vd_ini FROM tbl_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = ".(int)$id_viaje." AND vd_orden = 0 ";
			$objRes = $this->objSQL->dbQuery($strSQL);
			$res = $this->objSQL->dbGetRow($objRes,0,3);
			if(!empty($res['vd_ini'])){
				$fecha = date('Y-m-d', strtotime($res['vd_ini']));
			}
		}
		
		if(!empty($fecha)){
			$strSQL = " SELECT vdtm_mo_id as id, mo_matricula as dato ";
			$strSQL.= " FROM tbl_viajes_disponibilidad_transportistas WITH(NOLOCK) ";
			$strSQL.= " INNER JOIN tbl_viajes_disponibilidad_transportistas_moviles WITH(NOLOCK) ON vdt_id = vdtm_vdt_id ";
			$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = vdtm_mo_id ";
			$strSQL.= " WHERE vdt_fecha = '".$fecha."' AND vdt_cl_id = ".(int)$idTransportista." ORDER BY dato ";
			$objRes = $this->objSQL->dbQuery($strSQL);
			return $this->objSQL->dbGetAllRows($objRes,3);
		}
		return false;
	}
	
	function getProcesarViajes(){
		$strSQL = " SELECT * FROM tbl_viajes_a_procesar WITH(NOLOCK) WHERE vp_cl_id = ".(int)$_SESSION['idAgente']." AND vp_procesado = 0 ";
		$objRes = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetRow($objRes,0,3);
	}
	
	function setRetroactivoViajes($ini, $fin, $delivery = 0){
		 $params['vp_us_id'] = $_SESSION['idUsuario'];
		 $params['vp_cl_id'] = $_SESSION['idAgente'];
		 $params['vp_ini'] = $ini;
		 $params['vp_fin'] = $fin;
		 $params['vp_delivery'] = $delivery;
		 
		 return $this->objSQL->dbQueryInsert($params, 'tbl_viajes_a_procesar');
	}

	function getPOD(){
		$strSQL = " SELECT sl_descripcion, sl_us_nombre, sl_fecha_alta";
		$strSQL.= " FROM tbl_system_log WITH(NOLOCK) ";
		$strSQL.= " WHERE sl_st_id = 1 AND sl_us_id = 6434 AND sl_rel_id = ".(int)$this->id_viaje;
		$strSQL.= " ORDER BY sl_fecha_alta ";
		$res = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($res,3);	
	}

	

	function importarExcel_MSC($files){ 
		define('ERR_SIN_PERMISOS', 4);
		require_once('clases/PHPExcel/IOFactory.php');
		$objExcel = PHPExcel_IOFactory::load($files['tmp_name']);
		
		//-- Se implementa esto porque formatea mal las fechas --//
		$celdas = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V');
		$objHojaExcel = $objExcel->getSheet(0);
		$iRow = 0;
		foreach ($objHojaExcel->getRowIterator() as $row){
			
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
			$iCell = 0;
			$objHoja[0][$iRow] = array();
			foreach ($cellIterator as $cell){
				if(!isset($celdas[$iCell])){
					break;
				}

				if(!is_null($cell)){
					$value = $cell->getValue();
					if(PHPExcel_Shared_Date::isDateTime($cell)){
						$value = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
					}
				}
			
				$objHoja[0][$iRow][$celdas[$iCell]] = $value;
				$iCell++;
			}

			//--validar fin del procesamiento
			$isNotEmpty = false;
			foreach($objHoja[0][$iRow] as $item){
				if(!empty($item)){
					$isNotEmpty = true;
				}
			}
			if(!$isNotEmpty){
				unset($objHoja[0][$iRow]);
				break;
			}
			//--

			$iRow++;
		}
		
		unset($objHoja[0][0]);
		
		$viajes = array();
		$cantidad = array(
			'repetidos' => 0
			,'insertados' => 0
		);

		//// ETAPA 1 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		foreach($objHoja[0] as $k => $hoja1){

			if(trim($hoja1['C']) == 'BOOKING' 
				|| trim($hoja1['B']) == 'CONTR' 
				|| strtolower(trim($hoja1['A'])) == 'importacion internacionales' 
				|| strtolower(trim($hoja1['A'])) == 'importacion nacional'
			){
				continue;
			}

			//--vi_codigo
			$cod_viaje = NULL;
			$separador = NULL;
			$fila = $k + 1;
			if(!empty($hoja1['C'])){
				$cod_viaje = trim($hoja1['C']);
				$separador = '-';
			}
			if(!empty($hoja1['B'])){
				$cod_viaje.= $separador.trim($hoja1['B']);
			}
			if(empty($hoja1['C']) && empty($hoja1['B'])){
				return 'Código de viaje inexistente (Hoja1, C-B:'.$fila.')';
			}
			$viajes[$cod_viaje] = array("vi_codigo" => escapear_string($cod_viaje));
			

			//--vi_transportista
			if(empty($hoja1['F'])){
				return 'Transportista Inexistente (Hoja1, G:'.$fila.')';
			}
			$viajes[$cod_viaje]["vi_transportista"] = escapear_string(trim($hoja1['F']));
			
			$viajes[$cod_viaje]['re_numboca_0'] = 'mscvacio';
			$viajes[$cod_viaje]['vi_ini_0'] = strtotime($hoja1['N']) ? date('Y-m-d',strtotime($hoja1['N'])) : NULL;
			if (!$viajes[$cod_viaje]["vi_ini_0"]){
				return 'Error en la definicion de Fecha Inicial del Viaje (Hoja1, N:'.$fila.'). Formato v&aacute;lido: dd/mm/yyyy.';
			} 

			$viajes[$cod_viaje]['re_numboca_1'] = escapear_string(trim($hoja1['I'].$hoja1['J']));
			$viajes[$cod_viaje]['re_nombre_1'] = escapear_string(trim($hoja1['I']));
			$viajes[$cod_viaje]['vi_ini_1'] = strtotime($hoja1['O']) ? date('Y-m-d',strtotime($hoja1['O'])) : NULL;

			$viajes[$cod_viaje]['re_numboca_2'] = escapear_string($hoja1['H']);
			$viajes[$cod_viaje]['vi_ini_2'] = strtotime($hoja1['P']) ? date('Y-m-d',strtotime($hoja1['P'])) : NULL;
			
		}

		//// ETAPA 2 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		foreach ($viajes as $k => $viaje) {			
			$viajes[$k]['vi_transportista'] = !empty($viaje['vi_transportista'])?$this->getIdTransportista(null, $viaje['vi_transportista'], true): NULL;
			$viajes[$k]['vi_transportista'] = !empty($viajes[$k]['vi_transportista']) ? $viajes[$k]['vi_transportista'] : 0;

			//--INI. Definición de tres referencias
			$viajes[$k]['referencias'][0] = array(
				're_id' => $this->getReferenciaNumBoca($viaje['re_numboca_0'])
				,'vi_ini' => $viaje['vi_ini_0']
			);

			$aux_re_id = $this->getReferenciaNumBoca($viaje['re_numboca_1']);
			$aux_re_id = !$aux_re_id ? $this->setReferencia($viaje['re_numboca_1'], $viaje['re_nombre_1']) : $aux_re_id;
			$viajes[$k]['referencias'][1] = array(
				're_id' => !empty($aux_re_id) ? $aux_re_id : 6464
				,'vi_ini' => $viaje['vi_ini_1']
			);

			$aux_re_id = $this->getReferenciaNumBoca($viaje['re_numboca_2']);
			$aux_re_id = !$aux_re_id ? $this->setReferencia($viaje['re_numboca_2'], $viaje['re_numboca_2']) : $aux_re_id;
			$viajes[$k]['referencias'][2] = array(
				're_id' => !empty($aux_re_id) ? $aux_re_id : 6464
				,'vi_ini' => $viaje['vi_ini_2']
			);
			//--FIN.


			##-- Alta/Update --##
			$campos = array();
			$valorCampos = array();
			array_push($campos,'vi_codigo');
			array_push($valorCampos,"'".$viaje['vi_codigo']."'");
			array_push($campos,'vi_us_id');
			array_push($valorCampos,$_SESSION['idUsuario']);
			array_push($campos,'vi_dador');
			array_push($valorCampos,$_SESSION['idEmpresa']);
			array_push($campos,'vi_transportista');
			array_push($valorCampos,$viajes[$k]['vi_transportista']);
			array_push($campos,'vi_delivery');
			array_push($valorCampos,0);
			
			$query = " SELECT vi_id FROM tbl_viajes WITH(NOLOCK) "
				." INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_id = vi_us_id "
				." WHERE vi_borrado = 0 AND vi_codigo = '".$viaje['vi_codigo']."' AND us_cl_id = ".$_SESSION['idEmpresa'];
			$result = $this->objSQL->dbGetRow($this->objSQL->dbQuery($query),0,3);
			$idviaje = 0;
			if($result['vi_id']){
				$idviaje = $rs['vi_id'];
				$this->updateViajes($campos,$valorCampos);
				$cantidad['repetidos'] ++;					
			}
			else{
				$idviaje = $this->setViajes($campos,$valorCampos);
				$cantidad['insertados'] ++;
			}

			//--Alta de referencias
			$destinosImpactados = array();
			foreach($viajes[$k]['referencias'] as $i => $item){
				$arr_destinos[0] = array(
					'vd_vi_id' => $idviaje
					,'vd_re_id' => $item['re_id']
					,'vd_orden' => $i
					,'vd_ini' => $item['vi_ini']
					,'vd_fin' => NULL
				);

				$query = " SELECT vd_id FROM tbl_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = {$idviaje} AND vd_orden = {$i}";
				$destBDD = $this->objSQL->dbGetRow($this->objSQL->dbQuery($query),0,3); //id del destino orden 0 en la base de datos
				$destBDD['vd_id'] = isset($destBDD['vd_id'])?$destBDD['vd_id']:0;
				if($destBDD['vd_id']){					
					$arr_destinos[0]['vd_id'] = $destBDD['vd_id'];
					if($this->setViajesDestinos($arr_destinos)){
						$destinosImpactados[] = $destBDD['vd_id'];
					}
				}
				else{
					$vd_id = $this->setViajesDestinos($arr_destinos);
					if($vd_id){
						$destinosImpactados[] = $vd_id;
					}
				}
			}

			//--INI. Borrar destinos no impactados
			if($destinosImpactados){
				$query = "SELECT vd_id FROM tbl_viajes_destinos WITH(NOLOCK) WHERE vd_vi_id = {$idviaje} AND vd_id NOT IN (".implode(',', $destinosImpactados).")";
				$borrarDestinos = $this->objSQL->dbGetAllRows($this->objSQL->dbQuery($query),3);
				if($borrarDestinos){
					$auxborrar = array();
					foreach($borrarDestinos as $item){
						array_push($auxborrar, $item['vd_id']);
					}
					
					$query = "DELETE FROM tbl_viajes_destinos WHERE vd_id IN (".implode(',',$auxborrar).") AND vd_vi_id = {$idviaje} ";
					$this->objSQL->dbQuery($query);
				}				
			}
			//--FIN.
		}
		
		//// ETAPA 3 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//-- Subida de Archivo --//
		$serverFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
		$rutaServer = $barra = '';
		foreach($serverFile as $item){
			if(strpos(strtolower($item),'.php') === false){
				$rutaServer.= $barra.$item;
				$barra = '/';
			}
		}
		
		$ruta = PATH_ATTACH.'/viajes/'.$_SESSION['idEmpresa']."/";
		if(!file_exists($ruta)){
			mkdir($ruta);				
		}
		$archivo = $ruta.getFechaServer('Ymd_His').' - '.$files['name']; 				
		copy($files['tmp_name'], $archivo);
		chown($rutaServer.'/'.$archivo, 'root');
		//-- --//

		//-------------------- GENERO EL MSJ ------------
		$resumen  = "Se han creado ".$cantidad['insertados']." de ".($cantidad['insertados'] + $cantidad['repetidos'])." viajes. ";
		$resumen .= "<br><br>Se actualizaron ".$cantidad['repetidos']." viajes que ya exist&iacute;an. <br>";	
			
		/*if($cantidad['sinTransportistas'] !=0 ){
			$resumen .= "<br><br>Los viajes con transportistas invalidos (".$cantidad['sinTransportistas'].") que no se procesaron son los siguientes:";
			
			for($ixi=0;$ixi<count($registros['sinTransportistas']);$ixi++){				
				$resumen .= " <br>".$registros['sinTransportistas'][$ixi];
			}
		}
			
		if($cantidad['sinDestinos'] != 0){
			$resumen .= "<br><br>Los viajes sin destinos asociados (".$cantidad['sinDestinos'].") que no se procesaron son los siguientes:";
				
			for($ixi=0;$ixi<count($registros['sinDestinos']);$ixi++){				
				$resumen .= " <br>".$registros['sinDestinos'][$ixi];
			}
		}*/
		
		return $resumen;
	}
}