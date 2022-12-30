<?php
function index($objSQLServer, $seccion, $mensaje="", $filtroCol = false) {
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	
	switch($action){
		case 'proof_delivery':
			proof_delivery($objSQLServer, $seccion);
			exit;
		break;
		case 'vales_asociados':
			vales_asociados($objSQLServer, $seccion, (int)$_GET['vi_id']);
			exit;
		break;
		
	}
	

	if($_GET['idViaje']){
		modificar($objSQLServer, $seccion, "", (int)$_GET['idViaje']);
		exit;	
	}
	
	global $lang;
	require_once 'clases/clsViajes.php';
    $objViaje = new Viajes($objSQLServer);
	
	$col['transportista'] = 'colidTransportista';
	$col['dador'] = 'colidDador';
	$col['movil'] = 'colidMovil';
	$col['referencia'] = 'colidReferencia';
	$col['iniReal'] = 'colIniReal';
	$col['finReal'] = 'colFinReal';
	$col['estado'] = 'colEstado';
	$col['estado_check'] = 'estado_check';
	require_once 'clases/clsFiltrosCol.php';
	$objFiltroCol = new FiltrosCol($col);

	global $forza_filtros;
	$filtros = isset($forza_filtros) ? $forza_filtros : array();
	//$filtros = defined('FILTROS') ? FILTROS : array();
	
	if(!empty($_POST['buscar'])){
		$filtros['buscar'] = $_POST['buscar'];	
		unset($_POST['fdesde']);
		unset($_POST['fhasta']);
	}
	else{

		if($_COOKIE['filtro_btn'] || $_POST['filtro_btn']){
			$filtros['ini'] = $_POST['filtro_btn'] ? $_POST['filtro_btn'] : $_COOKIE['filtro_btn'];
		}
		else{
			$_POST['fdesde'] = $_POST['fdesde']?$_POST['fdesde']:($_COOKIE['filtro_desde']?$_COOKIE['filtro_desde']:NULL);
			$_POST['fhasta'] = $_POST['fhasta']?$_POST['fhasta']:($_COOKIE['filtro_hasta']?$_COOKIE['filtro_hasta']:NULL);
			
			if(!empty($_POST['fdesde'])){
				$filtros['f_ini'] = date('Y-m-d',strtotime($_POST['fdesde']));	
			}
			if(!empty($_POST['fhasta'])){
				$filtros['f_fin'] = date('Y-m-d',strtotime($_POST['fhasta']));	
			}
			if(empty($_POST['fdesde']) && empty($_POST['fhasta'])){
				$filtros['f_ini'] = $filtros['f_fin'] = getFechaServer('Y-m-d');
			}
		}

		if(tienePerfil(array(27,28))){
			if($_POST['hidId'] == 'ultimo_mes'){
				$filtros['iniReal'] = 1;
			}
			/*elseif(empty($_POST['fdesde'])){
				$filtros['f_ini']  = date('Y-m-d',strtotime('-61 day',strtotime(getFechaServer('Y-m-d'))));
				$filtros['iniReal'] = 2;
			}*/
			else{
				$filtros['iniReal'] = 2;
			}

			if(tienePerfil(28) && $seccion == 'retirosforza'){
				$filtros['checked'] = 1;
			}
		}
	}
	
	$arrViajes = $objViaje->getListadoViajes($filtros);
	if($arrViajes){
		foreach($arrViajes as $k => $row){
			$objFiltroCol->value($col['transportista'],$row['transportista'],$row['id_transportista']);
			$objFiltroCol->value($col['dador'],$row['dador'],$row['id_dador']);
			$objFiltroCol->value($col['movil'],$row['vi_movil'],$row['id_movil']);
			$objFiltroCol->value($col['referencia'],$row['re_nombre'],$row['re_id']);
		}
		$objFiltroCol->value($col['iniReal'],$lang->system->ingreso_realizado,1);
		$objFiltroCol->value($col['iniReal'],$lang->system->ingreso_pendiente,2);
		$objFiltroCol->value($col['finReal'],$lang->system->egreso_realizado,1);
		$objFiltroCol->value($col['finReal'],$lang->system->egreso_pendiente,2);

		if($seccion == 'entregasforza'){
			$objFiltroCol->value($col['estado_check'],'Rechazado','-1');
			$objFiltroCol->value($col['estado_check'],'Sin documento','0');
			$objFiltroCol->value($col['estado_check'],decode('Pendiente de verificación'),'1');
			$objFiltroCol->value($col['estado_check'],'Acreditado','2');
			$objFiltroCol->value($col['estado_check'],'Asignado','3');
			$objFiltroCol->value($col['estado_check'],'Intercambiado','4');
		}
		else{
			$objFiltroCol->value($col['estado_check'],'Turno pendiente','0');
			$objFiltroCol->value($col['estado_check'],'Turno rechazado','-1');
			$objFiltroCol->value($col['estado_check'],'Turno confirmado','1');
			$objFiltroCol->value($col['estado_check'],'Retiro finalizado','2');
		}

		if(!$filtroCol){
			foreach($col as $item){
				unset($_POST[$item]);
			}
		}
		
		if($objFiltroCol->validar()){ 
			$filtros['transportista'] = $_POST[$col['transportista']]?implode(',',$_POST[$col['transportista']]):NULL;
			$filtros['dador'] = $_POST[$col['dador']]?implode(',',$_POST[$col['dador']]):NULL;
			$filtros['movil'] = $_POST[$col['movil']]?implode(',',$_POST[$col['movil']]):NULL;
			$filtros['referencia'] = $_POST[$col['referencia']]?implode(',',$_POST[$col['referencia']]):NULL;
			$filtros['iniReal'] = $_POST[$col['iniReal']]?implode(',',$_POST[$col['iniReal']]):NULL;
			$filtros['finReal'] = $_POST[$col['finReal']]?implode(',',$_POST[$col['finReal']]):NULL;
			$filtros['checked'] = $_POST[$col['estado_check']]?implode(',',$_POST[$col['estado_check']]):NULL;
			//$filtros['checked'] = str_replace('2','0',$filtros['checked']);
			//$filtros['checked'] = str_replace('-1','NULL',$filtros['checked']);
			$arrViajes = $objViaje->getListadoViajes($filtros);
		}
	}

	##-- GRAFICOS --##
	$en_tiempo = (string)$lang->system->en_tiempo;
	$atrasado = (string)$lang->system->atrasado;
	$grafico1 = array();## GRAFICO 1 -- Ingresos a tiempo vs Atrasados ##
	$grafico1[$en_tiempo]['valor'] = 0;
	$grafico1[$atrasado]['valor'] = 0;	
	
	$grafico2 = array();## GRAFICO 2 -- Ingresos a tiempo vs Atrasados por transportista ##
	
	$rangos = array((string)$lang->system->rango_menos_1,(string)$lang->system->rango_entre_1_y_2,(string)$lang->system->rango_entre_2_y_4,(string)$lang->system->rango_entre_4_y_24,(string)$lang->system->rango_mas_24);
	$grafico3 = array();## GRAFICO 3 -- Estadías ##
	$grafico3[$rangos[0]]['valor'] = 0;
	$grafico3[$rangos[1]]['valor'] = 0;	
	$grafico3[$rangos[2]]['valor'] = 0;	
	$grafico3[$rangos[3]]['valor'] = 0;	
	$grafico3[$rangos[4]]['valor'] = 0;	
	
	$grafico4 = array();## GRAFICO 4 -- Ingresos a tiempo vs Atrasados por conductor ##
	
	if(!tienePerfil(array(27,28))){
		foreach($arrViajes as $item){
			
			## Ingresos ##
			if($item['diferenciaIngreso'] > 0){
				$grafico1[$atrasado]['valor'] = (int)$grafico1[$atrasado]['valor'] + 1;
				$grafico2[$item['transportista']]['atrasado'] = (int)$grafico2[$item['transportista']]['atrasado'] + 1;
			}
			elseif($item['diferenciaIngreso'] <= 0  && trim($item['vd_ini_real']) == true){
				$grafico1[$en_tiempo]['valor'] = (int)$grafico1[$en_tiempo]['valor'] + 1;
				$grafico2[$item['transportista']]['en_tiempo'] = (int)$grafico2[$item['transportista']]['en_tiempo'] + 1;
			}
			
			## Egresos ##
			if(trim($item['vd_ini_real']) == true && trim($item['vd_fin_real']) == true){
				$hs = (((strtotime($item['vd_fin_real']) - strtotime($item['vd_ini_real']))/60)/60);
				
				if($hs < 1){
					$grafico3[$rangos[0]]['valor'] = (int)$grafico3[$rangos[0]]['valor']+1;	
				}
				elseif($hs >= 1 && $hs < 2){
					$grafico3[$rangos[1]]['valor'] = (int)$grafico3[$rangos[1]]['valor']+1;	
				}
				elseif($hs >= 2 && $hs < 4){
					$grafico3[$rangos[2]]['valor'] = (int)$grafico3[$rangos[2]]['valor']+1;	
				}
				elseif($hs >= 4 && $hs < 24){
					$grafico3[$rangos[3]]['valor'] = (int)$grafico3[$rangos[3]]['valor']+1;	
				}
				else{
					$grafico3[$rangos[4]]['valor'] = (int)$grafico3[$rangos[4]]['valor']+1;	
				}
			}
		}
	}
	
	if(count($grafico1) > 0 && ($grafico1[$en_tiempo]['valor'] > 0 || $grafico1[$atrasado]['valor'] > 0) 
		&& (!$filtros['transportista'] && !tienePerfil(12))
	){
		
		$data1 = "['".$lang->system->estado."', '".$lang->system->valores."']";
		foreach($grafico1 as $k => $item){
			$data1.= ",['".encode($k)."',".(int)$item['valor']."]";
		}
	}
				
	if(count($grafico2) > 0){
		$data2 = "['".$lang->system->tiempo."','".$en_tiempo."','".$atrasado."']";
		foreach($grafico2 as $k => $item){
			$data2.= ",['".encode($k)."',".(int)$item['en_tiempo'].",".(int)$item['atrasado']."]";
		}
	}
	
	if(count($grafico3) > 0 && 
			($grafico3[$rangos[0]]['valor'] > 0 
				|| $grafico3[$rangos[1]]['valor'] > 0
				|| $grafico3[$rangos[2]]['valor'] > 0
				|| $grafico3[$rangos[3]]['valor'] > 0
				|| $grafico3[$rangos[4]]['valor'] > 0)
	){
		$data3 = "['".$lang->system->estadia_hora."','".$lang->system->cant_viajes."']";
		foreach($grafico3 as $k => $item){
			$data3.= ",['".encode($k)."',".(int)$item['valor']."]";
		}
	}
	##-- --##
	
	//--Activar botonera filtros
	$active_btn = $filtros['iniReal'];
	if(empty($active_btn)){
		if($_SESSION['filtroscol']['hidId'] == 'ultimo_mes') {
			$active_btn = 1;
		}
		elseif($_SESSION['filtroscol']['hidId'] == 'hoy') {
			$active_btn = 2;
		}
		elseif(!empty($filtros['ini'])){
			$active_btn = $filtros['ini'];
		}
	}
	//--

	
	$extraJS[] = 'js/abmViajes.js';
	$extraJS[] = 'js/abmViajesFunciones.js';
	$extraCSS[]='css/abmViajes.css';
	$extraCSS[] = 'css/ui/jquery.ui.datepicker.css';
	$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$extraJS[] = 'js/jquery/jquery.datepicker.js';
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] = 'js/popupHostFunciones.js';
	$extraJS[] = 'js/filtrosCol.js';
	$operacion = 'listar';
	
	include_once('includes/template.php');
	$objFiltroCol->aplicar();
}

function filtrarCol($objSQLServer, $seccion){
	index($objSQLServer, $seccion, '', true);
}

function filtroFecha($objSQLServer, $seccion){
	$tipo = $_POST['hidId'];
	$hoy = getFechaServer('Y-m-d');
	
	/*if(tienePerfil(array(27,28))){
		switch($tipo){
			case 'hoy':
				$_POST['fdesde'] = date('Y-m-d',strtotime('-161 day',strtotime($hoy)));
				$_POST['fhasta'] = NULL;
			break;
			case 'ultimo_mes':
				$_POST['fdesde'] = date('Y-m-d',strtotime('-161 day',strtotime($hoy)));
				$_POST['fhasta'] = NULL;	
			break;
		}
	}
	else{
		switch($tipo){
			case 'hoy':
				$_POST['fdesde'] = $hoy;	
				$_POST['fhasta'] = NULL;
			break;
			case 'ultima_semana':
				$_POST['fdesde'] = date('Y-m-d',strtotime('-8 day',strtotime($hoy)));
				$_POST['fhasta'] = date('Y-m-d',strtotime('-1 day',strtotime($hoy)));
			break;
			case 'ultimo_mes':
				$_POST['fdesde'] = date('Y-m-d',strtotime('-161 day',strtotime($hoy)));
				$_POST['fhasta'] = date('Y-m-d',strtotime('-1 day',strtotime($hoy)));	
			break;
			case 'prox_dias':
				$_POST['fdesde'] = date('Y-m-d',strtotime('+1 day',strtotime($hoy)));	
				$_POST['fhasta'] = NULL;
			break;
		}
	}
	
	setcookie('filtro_desde', $_POST['fdesde'], time()+180);

	if(empty($_POST['fhasta'])){
		setcookie('filtro_hasta',NULL, time()-3600);
		unset($_COOKIE['filtro_hasta']);
	}
	else{
		setcookie('filtro_hasta', $_POST['fhasta'], time()+180);
	}
	*/

	switch($tipo){
		case 'hoy':
			$_POST['filtro_btn'] = 2;
			setcookie('filtro_btn', 2, time()+180); /* expira en 3 min */
		break;
		case 'ultimo_mes':
			$_POST['filtro_btn'] = 1;
			setcookie('filtro_btn', 1, time()+180);
		break;
	}

	if(isset($_POST['filtro_btn'])){
		setcookie('filtro_desde',NULL, time()-3600);
		setcookie('filtro_hasta',NULL, time()-3600);
	}
	
	index($objSQLServer, $seccion);
}

function alta($objSQLServer, $seccion, $mensaje = "", $datosCargados = array(), $popup = false, $arrDestinos = array()){
	if($seccion == 'entregasforza'){
		global $cbo_origen;
	}

   	$idUsuario = (int)$_SESSION["idUsuario"];
	
	require_once 'clases/clsViajes.php';
    $objViaje = new Viajes($objSQLServer);
	
	require_once 'includes/navbar_permisos.php';
	require_once 'clases/clsPerfiles.php';
	$objPerfil = new Perfil($objSQLServer);
	
	$tipo_viaje = $objViaje->getViajesTipo();
	
	require_once $rel.'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	
	$tipoUsuario = $objUsuario->get_tipoUsuario($idUsuario);
	
	$idCliente = $objUsuario->idCliente;
	$dador = $objViaje->getDador($idCliente);

	if(tienePerfil(19) && $seccion == 'retirosforza'){
		$query = "EXEC Pallets_Fabricantes_disponibles";
		$fabricante = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);
	}
	
	##-- GENERAR NUMBER TRACKING --##
	$arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','ñ','o','p','q','r','s','t','u','v','w','x','y','z');
	$aux = date('y'); 	
	//$aux_1 = $arr[substr($aux,0,1)].$arr[substr($aux,1,1)];
	$aux_1 = 'PS';
	$aux = date('H'); 	
	$aux_2 = ($aux < 10)?($arr[0].$arr[(int)$aux]):($arr[substr($aux,0,1)].$arr[substr($aux,1,1)]);
	$number_tracking = strtoupper($aux_1.date('WN').$aux_2.date('is'));
	##Inicio. Definición de parámetros
	$arrViaje['vi_codigo'] = isset($datosCargados[0]['vi_codigo'])?$datosCargados[0]['vi_codigo']:$number_tracking;
	$arrViaje['vt_id'] = isset($datosCargados[0]['vi_vt_id'])?$datosCargados[0]['vi_vt_id']:"";
	$arrViaje['vi_dador'] = isset($datosCargados[0]['vi_dador'])?$datosCargados[0]['vi_dador']:"";
	$arrViaje['vi_transportista'] = isset($_POST['transportista'])?$_POST['transportista']:"";
	$arrViaje['mo_id_tipo_movil'] = isset($_POST['movil_tipo'])?$_POST['movil_tipo']:"";
	$arrViaje['vi_mo_id'] = isset($datosCargados[0]['vi_mo_id'])?$datosCargados[0]['vi_mo_id']:"";
	$arrViaje['vi_co_id'] = isset($datosCargados[0]['vi_co_id'])?$datosCargados[0]['vi_co_id']:"";
	$arrViaje['vi_observaciones'] = isset($datosCargados[0]['vi_observaciones'])?$datosCargados[0]['vi_observaciones']:"";
	$arrViaje['vi_facturado'] = isset($datosCargados[0]['vi_facturado'])?$datosCargados[0]['vi_facturado']:0;
	$arrViaje['vi_finalizado'] = isset($datosCargados[0]['vi_finalizado'])?$datosCargados[0]['vi_finalizado']:0;
	$arrViaje['vi_stock_status'] = 0;
	##Fin. Definición de parámetros
		
	##Inicio. En caso de error al guardar, se levanta los destinos que ingreso el usuario.
	if($arrDestinos){
		$referencias = $arrDestinos;
		$datos = $objViaje->getDestinos($referencias);	
		$arrRef = $datos['ref'];
		$arrViaje['id_geozonas'] = $datos['id_geozonas']?$datos['id_geozonas']:"";
	}
	##Fin. En caso de error al guardar, se levanta los destinos que ingreso el usuario.
	
	if($seccion == 'retirosforza' || $seccion == 'entregasforza'){
		if($cbo_origen){
			$arrViaje['origen_viaje'] = $arrRef[0]['vd_re_id'];
			unset($arrRef[0]);
			$arrViaje['id_geozonas'] = str_replace($arrViaje['origen_viaje'].',','',$arrViaje['id_geozonas']);
		}
	}

	$extraCSS[] = 'css/estilosAbmViajes.css';
	$extraCSS[] = 'css/abmViajes.css';
	$extraCSS[] = 'css/ui/jquery.ui.datepicker.css';
	
   	$extraJS[] = 'js/jquery/jquery-ui-1.8.5.custom.min.js';
    $extraJS[] = 'js/jquery/jquery.placeholder.js';
	$extraJS[] = 'js/jquery/jquery.datepicker.js';
	$extraJS[] = 'js/abmViajes.js';
	$extraJS[] = 'js/abmViajesAM.js';
	
	##Necesarios para el popup de la ruedita de referencias##
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] = 'js/popupHostFunciones.js';
	
	$tieneMovilAsignado = 1;
	$operacion = 'alta';
	$sinDefaultJS = true;
	if (!$popup) {
        require("includes/template.php");
    } else {
		$extraCSS[] = 'css/estilosABMDefault.css';
		$extraCSS[] = 'css/estilosAbmPopup.css';
        $extraCSS[] = 'css/popup.css';
        $extraJS[] = 'js/popupFunciones.js?1';
        $popup = true;
		
        require("includes/frametemplate.php");
    }
}

function modificar($objSQLServer, $seccion = "", $mensaje = "", $idViaje = 0, $datosCargados = array(), $arrDestinos = array()) {
	if($seccion == 'entregasforza'){
		global $cbo_origen;
	}

	$idViaje = $idViaje ? $idViaje : ((int)$_POST['hidId']?(int)$_POST['hidId']:NULL);
	$idUsuario = (int)$_SESSION["idUsuario"];
	
	require_once 'clases/clsViajes.php';
    $objViaje = new Viajes($objSQLServer, $idViaje);
	
	require_once 'includes/navbar_permisos.php';
	require_once 'clases/clsPerfiles.php';
	$objPerfil = new Perfil($objSQLServer);
	
	$tipo_viaje = $objViaje->getViajesTipo();
	
	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	
	$tipoUsuario = $objUsuario->get_tipoUsuario($idUsuario);
	$idCliente = $objUsuario->idCliente;
	$dador = $objViaje->getDador($idCliente);

	if(tienePerfil(19) && $seccion == 'retirosforza'){
		$query = "EXEC Pallets_Fabricantes_disponibles";
		$fabricante = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);
	}
	
	$arrViaje = $objViaje->getViajes();
   	$arrViaje = $arrViaje[0];
	
	$datos['id_usuario'] = $idUsuario;
	$datos['id'] = $arrViaje['vi_mo_id'];
	$movil = $objViaje->getMovil($datos);
	
	##Inicio. Definición de parámetros
	$arrViaje['vi_codigo'] = isset($datosCargados[0]['vi_codigo'])?$datosCargados[0]['vi_codigo']:$arrViaje['vi_codigo'];
	$arrViaje['vt_id'] = $datosCargados[0]['vi_vt_id']?$datosCargados[0]['vi_vt_id']:$arrViaje['vi_vt_id'];
	$arrViaje['vi_dador'] = $datosCargados[0]['vi_dador']?$datosCargados[0]['vi_dador']:$arrViaje['vi_dador'];
	$arrViaje['vi_transportista'] = $_POST['transportista']?$_POST['transportista']:$arrViaje['vi_transportista'];
	$arrViaje['mo_id_tipo_movil'] = $_POST['movil_tipo']?$_POST['movil_tipo']:$movil[0]['mo_id_tipo_movil'];
	$arrViaje['vi_mo_id'] = $datosCargados[0]['vi_mo_id']?$datosCargados[0]['vi_mo_id']:$arrViaje['vi_mo_id'];
	$arrViaje['vi_co_id'] = $datosCargados[0]['vi_co_id']?$datosCargados[0]['vi_co_id']:$arrViaje['vi_co_id'];
	$arrViaje['vi_observaciones'] = isset($datosCargados[0]['vi_observaciones'])?$datosCargados[0]['vi_observaciones']:$arrViaje['vi_observaciones'];
	$arrViaje['vi_facturado'] = isset($datosCargados[0]['vi_facturado'])?$datosCargados[0]['vi_facturado']:$arrViaje['vi_facturado'];
	$arrViaje['vi_finalizado'] = isset($datosCargados[0]['vi_finalizado'])?$datosCargados[0]['vi_finalizado']:$arrViaje['vi_finalizado'];
	$arrViaje['vi_stock_Status'] = 0;

	if(tienePerfil(28)){//Si no tiene transportista asignado no visualziar movil.
		$arrViaje['mo_id_tipo_movil'] = empty($arrViaje['vi_transportista']) ? NULL : $arrViaje['mo_id_tipo_movil'];
	}
	##Fin. Definición de parámetros
	
	$referencias = $arrDestinos ? $arrDestinos : $objViaje->getReferencias();
	
	$datos = $objViaje->getDestinos($referencias);	
	$arrRef = $datos['ref'];
	##Inicio. Decodificar utf8
	foreach($arrRef as $k => $item){
		foreach($item as $sk => $subItem){
			$arrRef[$k][$sk] = encode($subItem);
		}
	}
	##Fin. Decodificar utf8
	
	$arrViaje['id_geozonas'] = $datos['id_geozonas']?$datos['id_geozonas']:"";
	$tieneMovilAsignado = $objViaje->validarMovilesAsignados($arrViaje['vi_mo_id']);//validador para ocultar boton guardar
	
	$historial = $objViaje->getHistorial();
	
	###########################################
	### 	RETIROS / ENTREGAS - FORZA 		###
	if($seccion == 'retirosforza' || $seccion == 'entregasforza'){
		if($cbo_origen){
			$arrViaje['origen_viaje'] = $arrRef[0]['vd_re_id'];
			unset($arrRef[0]);
			$arrViaje['id_geozonas'] = str_replace($arrViaje['origen_viaje'].',','',$arrViaje['id_geozonas']);
		}
	}

	if($seccion == 'entregasforza'){
		$POD = $objViaje->getPOD();
	}
	
	if($seccion == 'retirosforza'){
		$query = "EXEC dbo.pa_obtenerVales {$idViaje}";
		$valeselectronicos = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);
	}

	/*if($seccion == 'retirosforza'){
		if(tienePerfil(28)){
			$strSQL = " SELECT vc_valor, vc_observaciones, vi_transportista ";
			$strSQL.= " FROM tbl_viajes_cotizaciones WITH(NOLOCK) ";
			$strSQL.= " INNER JOIN tbl_viajes WITH(NOLOCK) ON vi_id = vc_vi_id ";
			$strSQL.= " WHERE vc_vi_id = {$idViaje} AND vc_transportista = {$_SESSION['idEmpresa']}";
			$cotizacion = $objSQLServer->dbGetRow($objSQLServer->dbQuery($strSQL),0,3);
		}
		else{
			/*$strSQL = " SELECT sl_descripcion, sl_us_nombre, sl_fecha_alta ";
			$strSQL.= " FROM tbl_system_log WITH(NOLOCK) WHERE sl_st_id = 2 AND sl_rel_id = ".$idViaje;
			$strSQL.= " ORDER BY sl_fecha_alta ";
			$cotizacion = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL),3);
			*//*
			$strSQL = " SELECT vc_valor, vc_observaciones, vc_fecha_cotizado, cl_razonSocial, vc_transportista ";
			$strSQL.= " FROM tbl_viajes_cotizaciones WITH(NOLOCK) ";
			$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON vc_transportista = cl_id ";
			$strSQL.= " WHERE cl_id_distribuidor = {$_SESSION['idEmpresa']} AND vc_vi_id = {$idViaje} ";
			$cotizacion = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL),3);
		}
	}*/

	
	###########################################
	###########################################
	
	$extraCSS[] = 'css/estilosAbmViajes.css';
	$extraCSS[] = 'css/abmViajes.css';
	$extraCSS[] = 'css/ui/jquery.ui.datepicker.css';
	
    $extraJS[] = 'js/jquery/jquery-ui-1.8.5.custom.min.js';
	$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$extraJS[] = 'js/jquery/jquery.datepicker.js';
	$extraJS[] = 'js/abmViajes.js';
	$extraJS[] = 'js/abmViajesAM.js';
	
	##Necesarios para el popup de la ruedita de referencias##
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] = 'js/popupHostFunciones.js';
	
	$operacion = "modificar";
	$sinDefaultJS = true;
	
	require("includes/template.php");
}

function bajaViaje($objSQLServer, $seccion = ""){
	global $lang;
	$idViaje = (int)$_POST['hidId']?(int)$_POST['hidId']:(int)$idViaje;
	
	require_once 'clases/clsViajes.php';
    $objViaje = new Viajes($objSQLServer, $idViaje);
	
	if($objViaje->deleteViaje()){
		$mensaje = 	$lang->message->ok->msj_baja_viaje;
	}
	else{
		$mensaje = 	$lang->message->error->msj_baja_viaje;
	}
	
	index($objSQLServer, $seccion, $mensaje);
}

function baja($objSQLServer, $seccion = ""){
	global $lang;
	$idPod = (int)$_POST['hidId']?(int)$_POST['hidId']:NULL;
	$idViaje = (int)$_POST['id_viaje']?(int)$_POST['id_viaje']:NULL;
		
	$objSQLServer->dbQuery("EXEC pa_eliminarVale {$idViaje}, {$idPod}, {$_SESSION['idEmpresa']} ");
	$mensaje = 'El vale se ha elimiando con éxito';
	//index($objSQLServer, $seccion, $mensaje);
	//modificar($objSQLServer, $seccion, $mensaje, $id_viaje, $datosCargados, $ret['arrDestinos']);
	modificar($objSQLServer, $seccion, $mensaje, $idViaje);
}

function volver(){
	header('Location: boot.php?c='.$_REQUEST["hidSeccion"]);
}

function guardarA($objSQLServer, $seccion) {
	global $lang;
	$mensaje = '';
	
	//--Ini. Ajuste pallets
	if($seccion == 'retirosforza'){
		$_POST['tipo_viaje'] = 30;
	}
	elseif($seccion == 'entregasforza'){
		$_POST['tipo_viaje'] = 29;
		global $cbo_origen;
	}
	//--Fin. Ajuste pallets

	$ret = controlarCampos(1);
	
	$mensaje = $ret['mensaje'];
	$method = isset($_GET['method']) ? $_GET['method'] : '';
	
	if (!$mensaje) {
		require_once 'clases/clsViajes.php';
        $objViaje = new Viajes($objSQLServer);
       	$id_viaje = $objViaje->setViajes($ret['campos'],$ret['valorCampos']); 
		$resp = false;
		if($id_viaje){
			$objViaje->id_viaje = $id_viaje;
			$resp = $objViaje->setViajesDestinos($ret['arrDestinos']);		
			if($resp){

				//--Ini. Ejecutar procedimiento al finalizar carga de pallets
				if(tienePerfil(27)){
					$objSQLServer->dbQuery("EXEC db_ws_transaction_sync");
				}
				//--Fin.

				if ($_POST['popup_ready'] == '1') {?>
					<script>window.parent.cerrarPopup();</script>
				 <?php } 
				 else {
					header('Location: boot.php?c='.$_REQUEST["hidSeccion"]);
				exit;
				}
			}
		}
		
		if($resp == false){
			$mensaje = $lang->message->ok->solicitud_enviada;
		}
	}
	
	if(!empty($mensaje)){
		array_walk($ret['valorCampos'], function(&$v) {$v = trim($v, "''");});
        $datosCargados[0] = array_combine($ret['campos'], $ret['valorCampos']);
		$ret = completeDatosDestinos($objSQLServer, $ret);
		
        //redireccionar al alta con los datos cargados.
       	if ($_POST['popup_ready'] != 1) {
            alta($objSQLServer, $seccion, $mensaje, $datosCargados, false, $ret['arrDestinos']);
        } 
		else{
            alta($objSQLServer, $seccion, $mensaje, $datosCargados, true, $ret['arrDestinos']);?>
            <div id="contenedorIngresarComo" style="text-align:center;padding:0 3px 0 3px;border:3px solid #FFFF4A;background-color:#FFFFAA;font-size:11px;line-height:13px;position:absolute;bottom:50px;left:35%;width:30%;">
                <a href="javascript: cerrarMensaje();">
                    <img id="imgCerrarMensaje" src="imagenes/cerrar.png" />
                </a>
                <br/>
                <span style='color:#000000;'><br/><?=$mensaje;?><br/></span><br/>
            </div>
            <?php
       }
	}	
}

function guardarM($objSQLServer, $seccion) { 
	if($seccion == 'entregasforza'){
		global $cbo_origen;
	}

	global $lang;
    $mensaje = '';
    $ret = controlarCampos(0);
	$mensaje = $ret['mensaje'];
	$id_viaje = (int)$_POST['id_viaje'];
	
	if (!$mensaje) {
		require_once 'clases/clsViajes.php';
        $objViaje = new Viajes($objSQLServer, $id_viaje);
       	
		$arrMotivosCambio = $objViaje->getMotivoViajes($_POST['id_motivo_cambio']);
		$objViaje->setLog($lang->system->motivo.': '.decode($arrMotivosCambio[0]['vmc_descripcion']),$_POST['id_motivo_cambio']);
		$resp = $objViaje->updateViajes($ret['campos'],$ret['valorCampos']); 
		if($resp){
			$id_geozona = "";
			$coma = "";
			if($ret['arrDestinos'] && !tienePerfil(28)){
				foreach($ret['arrDestinos'] as $item){
					$id_geozona.= $coma.$item['vd_re_id'];
					$coma = ",";	
				}
				$resp = $objViaje->deleteViajesDestinos($id_geozona);
				
				if($resp){
					$resp = $objViaje->setViajesDestinos($ret['arrDestinos']);		
					if($resp){

						//--Ini. Ejecutar procedimiento al finalizar carga de pallets
						if(tienePerfil(27)){
							$objSQLServer->dbQuery("EXEC db_ws_transaction_sync");
						}
						//--Fin.
						
						header('Location: boot.php?c='.$_REQUEST["hidSeccion"]);
					}
				}
			}
		}
		
		if($resp == false){
			$mensaje = $lang->message->ok->solicitud_enviada;
		}
	}
	
	if(!empty($mensaje)){
		array_walk($ret['valorCampos'], function(&$v) {$v = trim($v, "''");});
        $datosCargados[0] = array_combine($ret['campos'], $ret['valorCampos']);
        $ret = completeDatosDestinos($objSQLServer, $ret);
        //redireccionar al alta con los datos cargados.
       modificar($objSQLServer, $seccion, $mensaje, $id_viaje, $datosCargados, $ret['arrDestinos']);
	}	
	else{
		$mensaje = 'Tu solicitud fue procesada con exito!';
		modificar($objSQLServer, $seccion, $mensaje, $id_viaje);
	}
}

function controlarCampos($esAlta = 0) {
	global $lang;
	global $objSQLServer;
	require_once 'clases/clsReferencias.php';
	$objRef = new Referencia($objSQLServer);
				

    $campos = array();
    $valorCampos = array();
    $mensaje = '';

	if(!tienePerfil(array(28,19))){
		$campos[] = 'vi_us_id';
    	$valorCampos[] = $_SESSION['idUsuario'];
	}
	
	if(isset($_POST['cod_viaje'])){
		$campos[] = 'vi_codigo';
		$msjError = checkString(trim($_POST['cod_viaje']), 0, 50, $lang->system->nro_tarea, 1);
		$valorCampos[] = "'" .trim($_POST['cod_viaje']). "'";
		if ($msjError){
			$mensaje.="* " . $msjError . "<br/> ";}
	}
	
	$campos[] = 'vi_vt_id';
    $msjError = checkCombo((int)$_POST['tipo_viaje'], $lang->system->tipo_tarea, 1, 0);
    $valorCampos[] = (int)$_POST['tipo_viaje'];
	if ($msjError){
        $mensaje.="* " . $msjError . "<br/> ";}
		
	if(!tienePerfil(array(28,19))){
		$campos[] = 'vi_dador';
		$valorCampos[] = $_POST['dador']?$_POST['dador']:NULL;
	}
	elseif(tienePerfil(19) && $_REQUEST['hidSeccion'] == 'retirosforza'){
		$campos[] = 'vi_dador';
		$valorCampos[] = $_POST['dador']?$_POST['dador']:NULL;
	}
	/*global $objSQLServer;
	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	$tipoUsuario = $objUsuario->get_tipoUsuario($_SESSION['idUsuario']);
	if($tipoUsuario == 'dador' || $tipoUsuario == 'empresa'){
		$msjError = checkCombo(trim($_POST['dador']), 'Dador', 1, 0);
    	if ($msjError){
        	$mensaje.="* " . $msjError . "<br/> ";}
	}*/
	
	##valido transportista pero no lo guardo en base
	if(!tienePerfil(28)){
		if(isset($_POST['transportista'])){
			$campos[] = 'vi_transportista';
			$valorCampos[] = $_POST['transportista']?$_POST['transportista']:NULL;
		}
	}
		
	##valido tipo movil pero no lo guardo en base
   	//$msjError = checkCombo((int)$_POST['movil_tipo'], $lang->system->tipo_movil, 1, 0);
    //if ($msjError){
    //    $mensaje.="* " . $msjError . "<br/> ";}
		
	$campos[] = 'vi_mo_id';
    $valorCampos[] = $_POST['movil']?$_POST['movil']:NULL;;
	//$msjError = checkCombo(trim($_POST['movil']), $lang->system->movil, 1, 0);
    //if ($msjError){
    //    $mensaje.="* " . $msjError . "<br/> ";}
	
    $campos[] = 'vi_co_id';
	$valorCampos[] = $_POST['conductor']?$_POST['conductor']:NULL;
	//$msjError = checkCombo(trim($_POST['conductor']), $lang->system->conductor, 0, 0);
	//if ($msjError){
    //    $mensaje.="* " . $msjError . "<br/> ";}

    $campos[] = 'vi_observaciones';
	$valorCampos[] = !empty($_POST['observaciones'])?"'".trim($_POST['observaciones'])."'":NULL;
	
	$campos[] = 'vi_finalizado';
	$valorCampos[] = $_POST['vi_finalizado']?$_POST['vi_finalizado']:0;
	
	$campos[] = 'vi_facturado';
	$valorCampos[] = $_POST['vi_facturado']?$_POST['vi_facturado']:NULL;

	$arrDestinos = array();
    if (!empty($_POST['id_geozonas'])){

		$ref = explode(',',$_POST['id_geozonas']);
		$i = 0;

		if(isset($_POST['origen_viaje'])){			
			if(!empty($_POST['origen_viaje'])){
				$arrDestinos[] = array('vd_orden' => $i, 'vd_re_id' => $_POST['origen_viaje'], 'vd_ini' => date('Y-m-d H:i'), 'vd_fin' => NULL);
			}
			$i = 1;
		}

		foreach($ref as $ref_id){
			$auxDestino = array('vd_orden' => $i, 'vd_re_id' => $ref_id);

			if(isset($_POST['fecha_'.$ref_id])){
				$auxDestino['vd_ini'] = date('Y-m-d H:i',strtotime($_POST['fecha_'.$ref_id].' '.$_POST['hora_'.$ref_id].':'.$_POST['min_'.$ref_id]));
			}
			
			$f_fin = NULL;
			if(isset($_POST['f_egreso_'.$ref_id])){
				$auxDestino['vd_fin'] = date('Y-m-d H:i',strtotime($_POST['f_egreso_'.$ref_id])); 
			}
			
			if(isset($_POST['fecha_vencimiento_'.$ref_id])){
				$auxDestino['vd_vencimiento'] = $_POST['fecha_vencimiento_'.$ref_id] ? date('Y-m-d',strtotime($_POST['fecha_vencimiento_'.$ref_id])) : NULL; 
			}

			if(isset($_POST['estado_'.$ref_id])){
				$auxDestino['vd_checked'] = $_POST['estado_'.$ref_id]; 
			}

			$refdata = $objRef->obtenerRegistros($ref_id);
			$refdata = isset($refdata[0]) ? $refdata[0] : $refdata;
			
			//--Pallets
			if(isset($_POST['pallets_stock_'.$ref_id])){
				if($refdata['re_rg_id'] != '119'){	
					if(empty($_POST['pallets_stock_'.$ref_id])){
						//--$msjError = 'Debe indicar el nro de pallets';
					}
					elseif($_REQUEST['hidSeccion'] == 'entregasforza'){
						if($_POST['pallets_stock_'.$ref_id] < 0 || $_POST['pallets_stock_'.$ref_id] > 501){
							$msjError = 'El número de pallets debe ser mayor a 1 y menor a 500';
						}
					}
				}					
				
				$auxDestino['vd_stock'] = intval($_POST['pallets_stock_'.$ref_id]);	
				$arrDestinos[] = $auxDestino;
			}
			else{
				if($esAlta == 1 && $refdata['re_rg_id'] != '119'){
					//--$msjError = 'Debe indicar el nro de pallets';
				}
				$arrDestinos[] = $auxDestino;	
			}
			
			$i++;
		}

		if ($msjError){
			$mensaje.="* " . $msjError . "<br/> ";
		}
	}
	else{
		$msjError = $lang->message->msj_viajes_ingresar_destinos;
		$mensaje.="* " . $msjError . "<br/> ";	
	}

	return array('mensaje' => $mensaje, 'campos' => $campos, 'valorCampos' => $valorCampos, 'arrDestinos' => $arrDestinos);
}

function  completeDatosDestinos($objSQLServer, $ret){
	require_once 'clases/clsReferencias.php';
    $objRef = new Referencia($objSQLServer);
    for ($i = 0; $i < count($ret['arrDestinos']); $i++) {
        $id_ref = $ret['arrDestinos'][$i]['vd_re_id'];
		$arrRef = $objRef->obtenerReferencias($id_ref);
		
		$ret['arrDestinos'][$i]['re_nombre'] = $arrRef[0]['re_nombre'];
		$ret['arrDestinos'][$i]['re_ubicacion'] = $arrRef[0]['re_ubicacion'];
		$ret['arrDestinos'][$i]['re_identificador'] = $arrRef[0]['re_identificador'];
		$ret['arrDestinos'][$i]['re_numboca'] = $arrRef[0]['re_numboca'];
		$ret['arrDestinos'][$i]['re_contacto'] = $arrRef[0]['re_contacto'];
		$ret['arrDestinos'][$i]['re_whatsapp'] = $arrRef[0]['re_whatsapp'];
		$ret['arrDestinos'][$i]['re_email'] = $arrRef[0]['re_email'];
	}
	
	return $ret;
}

function export_estadias($objSQLServer){
	global $lang;
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	include_once 'clases/clsViajes.php';
	$objViaje = new Viajes($objSQLServer);
	
	$filtros = array();	
	if(!empty($_POST['buscar'])){
		$filtros['buscar'] = $_POST['buscar'];	
		unset($_POST['fdesde']);
		unset($_POST['fhasta']);
	}
	else{
		$_POST['fdesde'] = $_POST['fdesde']?$_POST['fdesde']:($_COOKIE['filtro_desde']?$_COOKIE['filtro_desde']:getFechaServer('d-m-Y'));
		$_POST['fhasta'] = $_POST['fhasta']?$_POST['fhasta']:getFechaServer('d-m-Y');
		
		if(!empty($_POST['fdesde'])){
			$filtros['f_ini'] = date('Y-m-d',strtotime($_POST['fdesde']));	
		}
		if(!empty($_POST['fhasta'])){
			$filtros['f_fin'] = date('Y-m-d',strtotime($_POST['fhasta']));	
		}
	}	
	
	$regis = $objViaje->getListadoViajes($filtros,true);
				
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->botonera->exportar_estadias)
		->setSubject($lang->botonera->exportar_estadias)
		->setDescription($lang->botonera->exportar_estadias)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
		
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $lang->system->codigo_viaje)
		->setCellValue('B1', $lang->system->transportista)
		->setCellValue('C1', $lang->system->origen)
		->setCellValue('D1', $lang->system->ingreso_prog_origen)
		->setCellValue('E1', $lang->system->ingreso_real_origen)
		->setCellValue('F1', $lang->system->egreso_prog_origen)
		->setCellValue('G1', $lang->system->egreso_real_origen)
		->setCellValue('H1', $lang->system->status_origen)
		->setCellValue('I1', $lang->system->estadia_origen)
		->setCellValue('J1', $lang->system->destino)
		->setCellValue('K1', $lang->system->ingreso_prog_destino)
		->setCellValue('L1', $lang->system->ingreso_real_destino)
		->setCellValue('M1', $lang->system->egreso_prog_destino)
		->setCellValue('N1', $lang->system->egreso_real_destino)
		->setCellValue('O1', $lang->system->status_destino)
		->setCellValue('P1', $lang->system->estadia_destino);
						
	$arralCol = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','D','E','F','G','K','L','M','N');
	$objPHPExcel->alignCenter($alingCenterCol);
						
	$i = 2;
	foreach($regis as $row){
		$estado_arribro = '';
		//destino
		if($row['diferenciaIngreso'] > 0){$estado_arribro = $lang->system->arribo_atrasado;}
		elseif($row['diferenciaIngreso'] <= 0 && trim($row['vd_ini_real']) == true){$estado_arribro = $lang->system->arribo_en_tiempo;}
		else{$estado_arribro = '';}
							
		$estado_egreso = '';
		if($row['diferenciaEgreso'] > 0){$estado_egreso = $lang->system->partio_atrasado;}
		elseif($row['diferenciaEgreso']<= 0 && trim($row['vd_fin_real']) == true){$estado_egreso = $lang->system->partio_en_tiempo;}
		else{$estado_egreso = '';}
							
		//origen
		$o_estado_arribro = '';
		if($row['o_diferenciaIngreso'] > 0){$o_estado_arribro = $lang->system->arribo_atrasado;}
		elseif($row['o_diferenciaIngreso'] <= 0 && trim($row['o_ini_real']) == true){$o_estado_arribro = $lang->system->arribo_en_tiempo;}
		else{$o_estado_arribro = '';}
							
		$o_estado_egreso = '';
		if($row['o_diferenciaEgreso'] > 0){$o_estado_egreso = $lang->system->partio_atrasado;}
		elseif($row['o_diferenciaEgreso']<= 0 && trim($row['o_fin_real']) == true){$o_estado_egreso = $lang->system->partio_en_tiempo;}
		else{$o_estado_egreso = '';}
							
		$o_ini = 	  ($row['o_ini'])?	   $row['o_ini']:'-';
		$o_ini_real = ($row['o_ini_real'])?$row['o_ini_real']:'-';
		$o_fin = 	  ($row['o_fin'])?	   $row['o_fin']:'-';
		$o_fin_real = ($row['o_fin_real'])?$row['o_fin_real']:'-';
				
		$vd_ini = 	   ($row['vd_ini'])?     $row['vd_ini']:'-';
		$vd_ini_real = ($row['vd_ini_real'])?$row['vd_ini_real']:'-';
		$vd_fin = 	   ($row['vd_fin'])?     $row['vd_fin']:'-';
		$vd_fin_real = ($row['vd_fin_real'])?$row['vd_fin_real']:'-';
							
		$EstadosOrigenC = ($o_estado_arribro!='' && $o_estado_egreso!='')? $o_estado_arribro.'  '.$o_estado_egreso :'-';
		$EstadosDestinoC =($estado_arribro!='' && $estado_egreso!='')?	  $estado_arribro.'  '.$estado_egreso     :'-';
							
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $row['vi_codigo'])
			->setCellValue('B'.$i, encode($row['transportista']))
			->setCellValue('C'.$i, encode($row['o_origen']))							
			->setCellValue('D'.$i, formatearFecha($o_ini))
			->setCellValue('E'.$i, formatearFecha($o_ini_real))
			->setCellValue('F'.$i, formatearFecha($o_fin))
			->setCellValue('G'.$i, formatearFecha($o_fin_real))
			->setCellValue('H'.$i, $EstadosOrigenC)
			->setCellValue('I'.$i, $row['o_diferencia'])				
			->setCellValue('J'.$i, encode($row['re_nombre']).' '.encode($row['re_descripcion']))
			->setCellValue('K'.$i, formatearFecha($vd_ini))
			->setCellValue('L'.$i, formatearFecha($vd_ini_real))
			->setCellValue('M'.$i, formatearFecha($vd_fin))
			->setCellValue('N'.$i, formatearFecha($vd_fin_real))
			->setCellValue('O'.$i, $EstadosDestinoC)
			->setCellValue('P'.$i, $row['d_diferencia']);
		$i++;
	}
						
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->botonera->exportar_estadias);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="export'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}

function proof_delivery($objSQLServer, $seccion){
	global $lang;
	$extraCSS[] = 'css/estilosAbmPopup.css';
    $extraCSS[] = 'css/popup.css';
    $extraJS[] = 'js/popupFunciones.js?1';
	$extraJS[] = 'js/jquery.blockUI.js';
    $tipoBotonera = 'A';
	$operacion = 'proof_delivery';
    require("includes/frametemplate.php");
}

function vales_asociados($objSQLServer, $seccion, $vi_id, $mensaje = NULL){
	global $lang;
	$extraCSS[] = 'css/estilosAbmPopup.css';
	$extraCSS[] = 'css/abmViajes.css';
    $extraCSS[] = 'css/popup.css';
    $extraJS[] = 'js/popupFunciones.js?1';
	$extraJS[] = 'js/jquery.blockUI.js';

	$query = "EXEC Pallets_administracion_vales_listado {$_SESSION['idUsuario']}, {$vi_id}";
	$registros = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);

	$tipoBotonera = 'A';
	$operacion = 'vales_asociados';
    require("includes/frametemplate.php");
}

function guardarVales($objSQLServer, $seccion){
	if(isset($_POST['chkId'])){
		$ids = implode(',', $_POST['chkId']);
		$vi_id = (int)$_POST['idViaje'];

		$query = "EXEC Pallets_administracion_vales_modificar {$_SESSION['idUsuario']}, {$vi_id}, '{$ids}'";
		$objSQLServer->dbQuery($query);
	}

	vales_asociados($objSQLServer, $seccion, $vi_id, 'Los datos fueron guardados.');
}