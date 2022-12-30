<?php 
$rel = '';
include_once $rel.'includes/validarSesion.php';
include_once $rel.'includes/funciones.php';
if(isset($_POST['accion'])) {
	switch ($_POST['accion']){
		case 'get-fechaHora-server':
				include_once $rel.'clases/clsCalendario.php';
				$objCalendario = new Calendario();
				$fecha = $objCalendario->getFormatoFecha(getFechaServer('Y-m-d H:i:s'));
				echo $fecha;
				exit;
		break;
		case 'get-fechaHora-server-format':
				echo formatearFecha(getFechaServer(), isset($_POST['format'])?$_POST['format']:NULL);
				exit;
		break;
		case 'get-provincia':
			include_once $rel.'includes/conn.php';
			$sql = " SELECT pr_id, pr_nombre FROM tbl_provincias WHERE pr_pa_id = ".(int)$_POST['id_pais']." ORDER BY pr_nombre ";
			$res = $objSQLServer->dbQuery($sql);
			$prov = $objSQLServer->dbGetAllRows($res,3);
			foreach($prov as $k => $p){
				$prov[$k]['pr_nombre'] = encode($p['pr_nombre']);
			}
			echo json_encode($prov);
		break;
		case 'get-localidad':
			include_once $rel.'includes/conn.php';
			$sql = " SELECT lo_id, lo_nombre FROM tbl_localidad WHERE lo_pr_id = ".(int)$_POST['id_provincia']." ORDER BY lo_nombre ";
			$res = $objSQLServer->dbQuery($sql);
			$prov = $objSQLServer->dbGetAllRows($res,3);
			foreach($prov as $k => $p){
				$prov[$k]['lo_nombre'] = encode($p['lo_nombre']);
			}
			echo json_encode($prov);
		break;
		case 'get-clientes-por-tipoEmpresa':
			include_once $rel.'includes/conn.php';
			$sql = "SELECT cl_id, cl_razonSocial FROM tbl_clientes WHERE cl_tipo = ".(int)$_POST['cl_tipo']." AND cl_borrado = 0 ORDER BY cl_razonSocial ";
			$res = $objSQLServer->dbQuery($sql);
			$cl = $objSQLServer->dbGetAllRows($res,3);
			foreach($cl as $k => $p){
				$cl[$k]['cl_razonSocial'] = encode($p['cl_razonSocial']);
			}
			echo json_encode($cl);
		break;
		case 'get-clientes-tipo-2':
			$arrCliente = array();
			if($_POST['id_agente']){
				include_once $rel.'includes/conn.php';
				require_once $rel.'clases/clsClientes.php';
				$objCliente = new Cliente($objSQLServer);
				$arrCliente = $objCliente->obtenerClientesFletes($_POST['id_agente']);
				foreach($arrCliente as $k => $p){
					$arrCliente[$k]['cl_razonSocial'] = encode($p['cl_razonSocial']);
				}
			}
			echo json_encode($arrCliente);
		break;
		case 'get-usuarios-por-cliente':
			$arrUsuarios = array();
			if($_POST['idCliente']){
				include_once $rel.'includes/conn.php';
				require_once $rel.'clases/clsUsuarios.php';
				$objUsuario = new Usuario($objSQLServer);
				$datos['filtro'] = 'getAllReg';
				$datos['idEmpresa'] = (int)$_POST['idCliente'];
				$arrUsuarios = $objUsuario->obtenerUsuariosListado($datos);
				foreach($arrUsuarios  as $k => $p){
					$arrUsuarios[$k] = array(
							us_id => $p['us_id']
							,us_nombre => encode($p['us_nombre'])
							,us_apellido => encode($p['us_apellido'])
							,us_nombreUsuario => $p['us_nombreUsuario']
							//,us_mailContacto => $p['us_mailContacto']
							//,us_mailAlertas => $p['us_mailAlertas']
						);
				}
			}
			echo json_encode($arrUsuarios);
		break;
		case 'get-calendario':
			include($rel.'clases/clsCalendario.php');
			$calendario = new Calendario();
			$mes = $_POST['mes'];
			$anio = $_POST['anio'];
			$ide = $_POST['ide'];
				
			if(!empty($_POST['activo'])){
				$act_dia = date('d',strtotime($_POST['activo']));
				$act_mes = date('m',strtotime($_POST['activo']));
				$act_anio = date('Y',strtotime($_POST['activo']));	
			}
				
			for($i=1; $i<=31; $i++){
				$id = $ide.'#'.$i.'-'.$mes.'-'.$anio;
				$contenido[$i][$mes][$anio]['contenido'] = '<a href="javascript:clickDate(\''.$id .'\');" id="'.$id.'">&nbsp;</a>'; 
					
				if( ($i.'-'.(int)$mes.'-'.$anio) == ((int)$act_dia.'-'.(int)$act_mes.'-'.$act_anio) ){
					$contenido[$i][$mes][$anio]['class'] = 'activa'; 
				}
			}
			$calendar['calendar'] = $calendario->getCalendario($ide, $mes, $anio, $contenido);
			$calendar['meses'] = $calendario->meses[$mes-1];
			echo json_encode($calendar);
			exit;
		break;
		case 'get-calendario-60-dias':
			include($rel.'clases/clsCalendario.php');
			$calendario = new Calendario();
			$mes = $_POST['mes'];
			$anio = $_POST['anio'];
			$ide = $_POST['ide'];
				
			if(!empty($_POST['activo'])){
				$act_dia = date('d',strtotime($_POST['activo']));
				$act_mes = date('m',strtotime($_POST['activo']));
				$act_anio = date('Y',strtotime($_POST['activo']));	
			}
		
			$fechaHasta = getFechaServer('Y-m-d');
			$fechaDesde = date('Y-m-d', strtotime('-60 day',strtotime($fechaHasta)));
						
			for($i=1; $i<=31; $i++){
				if(strtotime($fechaDesde) <= strtotime($anio.'-'.$mes.'-'.$i) && strtotime($fechaHasta) >= strtotime($anio.'-'.$mes.'-'.$i)){
					$id = $ide.'#'.$i.'-'.$mes.'-'.$anio;
					$contenido[$i][$mes][$anio]['contenido'] = '<a href="javascript:clickDate(\''.$id .'\');" id="'.$id.'">&nbsp;</a>'; 
						
					if( ($i.'-'.(int)$mes.'-'.$anio) == ((int)$act_dia.'-'.(int)$act_mes.'-'.$act_anio) ){
						$contenido[$i][$mes][$anio]['class'] = 'activa'; 
					}
				}
			}
			$calendar['calendar'] = $calendario->getCalendario($ide, $mes, $anio, $contenido);
			$calendar['meses'] = $calendario->meses[$mes-1];
			echo json_encode($calendar);
			exit;
		break;
		case 'get-buscador-rastreo':
			include_once $rel.'includes/conn.php';
			include_once $rel.'clases/clsRastreo.php';
			$objRastreo = new Rastreo($objSQLServer);
			$arr = $objRastreo->getBuscadorMovilesReferencias($_POST['buscar']);
			foreach($arr as $k => $item){
				$arr[$k]['valor'] = encode($item['valor']);	
			}
			$return['resultados'] = $arr;
			echo json_encode($return);
			exit;
		break;
		case 'get-buscador-movil':
			include_once $rel.'includes/conn.php';
			include_once $rel.'clases/clsMoviles.php';
			$objMovil = new Movil($objSQLServer);
			$arr = $objMovil->getMovilesUsuario($_POST['buscar']);
			foreach($arr as $k => $item){
				$arr[$k]['valor'] = encode($item['valor']);	
			}
			$return['resultados'] = $arr;
			echo json_encode($return);
			exit;
		break;
		case 'get-moviles':
			include_once $rel.'includes/conn.php';
			include_once $rel.'clases/clsMoviles.php';
			$objMovil = new Movil($objSQLServer);
			$arrMovilesCombo = $objMovil->obtenerRegistros(0,'getAllReg',NULL,NULL,FALSE,(int)$_POST['idDistribuidor']);
			echo json_encode($arrMovilesCombo);
			exit;
		break;
		case 'get-perfiles':
			include_once $rel.'includes/conn.php';
			require_once $rel.'clases/clsPerfiles.php';
			$objPerfil = new Perfil($objSQLServer);
			
			$arrPerfil = $objPerfil->obtenerPerfilesHijos($_POST['id_perfil']);
			foreach($arrPerfil as $k => $p){
				$arrPerfil[$k]['pe_nombre'] = encode($p['pe_nombre']);
			}
			echo json_encode($arrPerfil);
		break;
		case 'get-nomenclado-OpenStreetMaps':
			$result = array('status'=>false);
			require_once $rel.'clases/clsNomenclador.php';
			$objNomenclador = new Nomenclador($objSQLServer);
			$arrAux = explode(',',$_POST['nomenclar']);
			if(is_numeric(trim($arrAux[0])) && is_numeric(trim($arrAux[1]))){
				$result['status'] = true;
				$result['lat'] = $arrAux[0];
				$result['lon'] = $arrAux[1];
			}
			else{
				$arrAux = $objNomenclador->nomenclarOpenStreetMaps_Street($_POST['nomenclar']);
				$arrAux = json_decode($arrAux);
				if($arrAux->status == 'Ok'){
					$result['status'] = true;
					$result['lat'] = $arrAux->lat;
					$result['lon'] = $arrAux->lon;
					$result['street'] = $arrAux->street;
				}
			}
			echo json_encode($result);
		break;
		case 'set-cambiar-estado-informe':
			include_once $rel.'includes/conn.php';
			require_once 'clases/clsGeneradorDeInformes.php';
    		$objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
			echo $objGeneradorDeInformes->cambiarEstadoInforme($_POST['idInforme']);
		break;
		case 'test-informes':
			$fichero = 'http://192.168.0.100:444/informes/generar_informes.php?id_test_envio='.(int)$_POST['id_test_envio'];
			$resp = file_get_contents($fichero);
			echo $resp;
			exit;
		break;
		case 'get-formato-fecha':
			echo formatearFecha($_POST['fecha'], isset($_POST['formato'])?$_POST['formato']:NULL);
			exit;
		break;
		case 'timeline_posicion_movil':
			
			include_once $rel.'includes/conn.php';
			include('clases/clsTimeline.php');
			$objTimeline = new Timeline($objSQLServer);
	
			$i = 0;
			foreach(json_decode($_POST['viajeID']) as $item){
				
				$movil = $objTimeline->getMovilPosicion($item);
				
				$pos[$i]['id'] = $item;
				$pos[$i]['posicion'] = $movil[0]['pos'];
				$pos[$i]['label'] = $movil[0]['label'];
				$pos[$i]['imagen'] = str_replace("\\", "/",$movil[0]['imagen']);
				$pos[$i]['vi_finalizado'] = $movil[0]['vi_finalizado'];
				
				#actualizacion 1
				$pos[$i]['ubicacion'] = $movil[0]['ubicacion'];
				$pos[$i]['horaUbicacion'] = $movil[0]['horaUbicacion'];
				$pos[$i]['horaMarcha'] = $movil[0]['horaMarcha'];
				
				#actualizacion 2
				$pos[$i]['estadoViaje'] = $movil[0]['estadoViaje'];
				$pos[$i]['fechaEstimada'] = $movil[0]['fechaEstimada'];
				$pos[$i]['horaEstimada'] = $movil[0]['horaEstimada'];
				
				$i++;
			}
			echo json_encode($pos);
			exit;
		break;
		case 'set-cambiar-estado-octopus':
			include_once $rel.'includes/conn.php';
			$strSQL = 'UPDATE tbl_octopus SET oc_estado=ABS(oc_estado-1) WHERE oc_id = '.(int)$_POST['id']; 
			echo $objSQLServer->dbQuery($strSQL);
		break;
		case 'get-buscador-referencias-palled':
			include_once $rel.'includes/conn.php';
			include_once $rel.'clases/clsReferencias.php';
			$objReferencia = new Referencia($objSQLServer);
			//$arr = $objReferencia->obtenerReferenciasEmpresa($_SESSION['idEmpresa'], 0, $_POST['buscar']);
			$arr = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION['idEmpresa'], $_POST['buscar'], NULL, '119,120');
			$aux = array();
			foreach($arr as $k => $item){
				$aux[] = array('id' => $item['re_id'], 'valor' => encode($item['re_nombre']));	
			}
			$return['resultados'] = $aux;
			echo json_encode($return);
			exit;
		break;
		case 'change-distance':
			include_once $rel.'includes/conn.php';
			
			$idmovil = (int)$_POST['id_movil'];
			$valor = (int)$_POST['value'];
			if($idmovil && $valor){
				if($objSQLServer->dbQueryUpdate(array('un_tiempo' => $valor), 'tbl_unidad', 'un_mo_id = '.$idmovil)){
					echo 'true';
					exit;
				}
			}
			echo 'false';
			exit;
		break;
	}
}
exit;
?>
 