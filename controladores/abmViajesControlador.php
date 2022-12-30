<?php
function index($objSQLServer, $seccion, $mensaje="", $filtroCol = false) {
	if($_GET['idViaje']){
		modificar($objSQLServer, $seccion, "", (int)$_GET['idViaje']);
		exit;	
	}
	
	global $lang;
	require_once 'clases/clsViajes.php';
    $objViaje = new Viajes($objSQLServer);
	
	$col['transportista'] = 'colidTransportista';
	$col['movil'] = 'colidMovil';
	$col['referencia'] = 'colidReferencia';
	$col['iniReal'] = 'colIniReal';
	$col['finReal'] = 'colFinReal';
	$col['estado'] = 'colEstado';
	require_once 'clases/clsFiltrosCol.php';
	$objFiltroCol = new FiltrosCol($col);
	
	$filtros = array();	
	if(!empty($_POST['buscar'])){
		$filtros['buscar'] = $_POST['buscar'];	
		unset($_POST['fdesde']);
		unset($_POST['fhasta']);
	}
	else{
		$borroFechaHasta = false;
		if($_POST){
			if($_POST['fdesde']){
				setcookie('filtro_desde', $_POST['fdesde'], time()+3600);
			}
			else{
				setcookie('filtro_desde', "", time()-3600); 
			}
			
			if($_POST['fhasta']){
				setcookie('filtro_hasta', $_POST['fhasta'], time()+3600);
			}
			else{
				setcookie('filtro_hasta', "", time()-3600); 
				$borroFechaHasta = true;
			}
		}
	
		$_POST['fdesde'] = $_POST['fdesde']?$_POST['fdesde']:($_COOKIE['filtro_desde']?$_COOKIE['filtro_desde']:getFechaServer('d-m-Y'));
		$_POST['fhasta'] = $_POST['fhasta']?$_POST['fhasta']:($borroFechaHasta?getFechaServer('d-m-Y'):($_COOKIE['filtro_hasta']?$_COOKIE['filtro_hasta']:getFechaServer('d-m-Y')));
		
		
		if(!empty($_POST['fdesde'])){
			$filtros['f_ini'] = date('Y-m-d',strtotime($_POST['fdesde']));	
		}
		if(!empty($_POST['fhasta'])){
			$filtros['f_fin'] = date('Y-m-d',strtotime($_POST['fhasta']));	
		}
	}
	
	##-- ini.GRAFICOS --##
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
	##-- fin.GRAFICOS --##
	
	##-- ini.ESTADISTICAS --##
	$stats['en_origen'] = array('detectados'=>0, 'total'=>0, 'promedio'=>0);
	$stats['en_destino'] = array('detectados'=>0, 'total'=>0, 'promedio'=>0);
	$stats['moviles'] = array('reportando'=>0, 'total'=>0, 'promedio'=>0);
	$auxViaje = NULL;
	##-- fin.ESTADISTICAS --##
	
	$arrViajes = $objViaje->getListadoViajes($filtros);
	if($arrViajes){
		foreach($arrViajes as $k => $row){
			
			##--ini.FILTROS --##
			$objFiltroCol->value($col['transportista'],$row['transportista'],$row['id_transportista']);
			$objFiltroCol->value($col['movil'],$row['vi_movil'],$row['id_movil']);
			$objFiltroCol->value($col['referencia'],$row['re_nombre'],$row['re_id']);
			##--fin.FILTROS --##
			
			##-- ini.GRAFICOS --##
			if($row['diferenciaIngreso'] > 0){
				$grafico1[$atrasado]['valor'] = (int)$grafico1[$atrasado]['valor'] + 1;
				$grafico2[$row['transportista']]['atrasado'] = (int)$grafico2[$row['transportista']]['atrasado'] + 1;
				$grafico4[$row['co_conductor']]['atrasado'] = (int)$grafico4[$row['co_conductor']]['atrasado'] + 1;
			}
			elseif($row['diferenciaIngreso'] <= 0  && trim($row['vd_ini_real']) == true){
				$grafico1[$en_tiempo]['valor'] = (int)$grafico1[$en_tiempo]['valor'] + 1;
				$grafico2[$row['transportista']]['en_tiempo'] = (int)$grafico2[$row['transportista']]['en_tiempo'] + 1;
				$grafico4[$row['co_conductor']]['en_tiempo'] = (int)$grafico4[$row['co_conductor']]['en_tiempo'] + 1;
			}
			
			if(trim($row['vd_ini_real']) == true && trim($row['vd_fin_real']) == true){
				$hs = (((strtotime($row['vd_fin_real']) - strtotime($row['vd_ini_real']))/60)/60);
				
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
			##-- fin.GRAFICOS --##
			
			##-- ini.ESTADISTICAS --##
			if(!tienePerfil(array(8,12))){
				if($row['vd_orden'] == 0){
					$stats['en_origen']['total']++;	
					if(trim($row['vd_ini_real']) == true){
						$stats['en_origen']['detectados']++;	
					}
				}
				elseif($row['vd_orden'] > 0){
					$stats['en_destino']['total']++;	
					if(trim($row['vd_ini_real']) == true){
						$stats['en_destino']['detectados']++;	
					}
				}
				
				if($auxViaje != $row['vi_id']){
					$stats['moviles']['total']++;	
					$auxViaje = $row['vi_id'];
					if($row['sh_rd_id'] != 75 && $row['sh_rd_id'] != 76){
						$stats['moviles']['reportando']++;	
					}
				}
			}
			##-- fin.ESTADISTICAS --##
		}
		
		##--ini.FILTROS --##
		$objFiltroCol->value($col['iniReal'],$lang->system->ingreso_realizado,1);
		$objFiltroCol->value($col['iniReal'],$lang->system->ingreso_pendiente,2);
		$objFiltroCol->value($col['finReal'],$lang->system->egreso_realizado,1);
		$objFiltroCol->value($col['finReal'],$lang->system->egreso_pendiente,2);
		
		if(!$filtroCol){
			foreach($col as $item){
				unset($_POST[$item]);
			}
		}
		
		if($objFiltroCol->validar()){ 
			$filtros['transportista'] = $_POST[$col['transportista']]?implode(',',$_POST[$col['transportista']]):NULL;
			$filtros['movil'] = $_POST[$col['movil']]?implode(',',$_POST[$col['movil']]):NULL;
			$filtros['referencia'] = $_POST[$col['referencia']]?implode(',',$_POST[$col['referencia']]):NULL;
			$filtros['iniReal'] = $_POST[$col['iniReal']]?implode(',',$_POST[$col['iniReal']]):NULL;
			$filtros['finReal'] = $_POST[$col['finReal']]?implode(',',$_POST[$col['finReal']]):NULL;
			$arrViajes = $objViaje->getListadoViajes($filtros);
		}
		##--fin.FILTROS --##
		
		##-- ini.GRAFICOS --##
		if(count($grafico1) > 0 && ($grafico1[$en_tiempo]['valor'] > 0 || $grafico1[$atrasado]['valor'] > 0)){
			$data1 = "['".$lang->system->estado."', '".$lang->system->valores."']";
			foreach($grafico1 as $k => $item){
				$data1.= ",['".encode($k)."',".(int)$item['valor']."]";
			}
		}
					
		if(count($grafico2) > 0 && (!$filtros['transportista'] && !tienePerfil(array(8,12)))){
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
		
		if(count($grafico4) > 0 && ($filtros['transportista'] || tienePerfil(array(8,12)))){
			$data4 = "['".$lang->system->tiempo."','".$en_tiempo."','".$atrasado."']";
			foreach($grafico4 as $k => $item){
				$data4.= ",['".encode($k)."',".(int)$item['en_tiempo'].",".(int)$item['atrasado']."]";
			}
		}
		##-- fin.GRAFICOS --##
		
		##-- ini.ESTADISTICAS --##
		if(!tienePerfil(array(8,12))){
			$stats['en_origen']['promedio'] = round(($stats['en_origen']['detectados']/$stats['en_origen']['total'])*100);
			$stats['en_destino']['promedio'] = round(($stats['en_destino']['detectados']/$stats['en_destino']['total'])*100);
			$stats['moviles']['promedio'] = round(($stats['moviles']['reportando']/$stats['moviles']['total'])*100);
		}
		##-- fin.ESTADISTICAS --##
	}
	
	$extraJS[] = 'js/abmViajes.js';
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

function alta($objSQLServer, $seccion, $mensaje = "", $datosCargados = array(), $popup = false, $arrDestinos = array()){
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
	
	##Inicio. Definición de parámetros
	$arrViaje['vi_codigo'] = isset($datosCargados[0]['vi_codigo'])?$datosCargados[0]['vi_codigo']:"";
	$arrViaje['vt_id'] = isset($datosCargados[0]['vi_vt_id'])?$datosCargados[0]['vi_vt_id']:"";
	$arrViaje['vi_dador'] = isset($datosCargados[0]['vi_dador'])?$datosCargados[0]['vi_dador']:"";
	$arrViaje['vi_transportista'] = isset($_POST['transportista'])?$_POST['transportista']:"";
	$arrViaje['mo_id_tipo_movil'] = isset($_POST['movil_tipo'])?$_POST['movil_tipo']:"";
	$arrViaje['vi_mo_id'] = isset($datosCargados[0]['vi_mo_id'])?$datosCargados[0]['vi_mo_id']:"";
	$arrViaje['vi_co_id'] = isset($datosCargados[0]['vi_co_id'])?$datosCargados[0]['vi_co_id']:"";
	$arrViaje['vi_observaciones'] = isset($datosCargados[0]['vi_observaciones'])?$datosCargados[0]['vi_observaciones']:"";
	$arrViaje['vi_facturado'] = isset($datosCargados[0]['vi_facturado'])?$datosCargados[0]['vi_facturado']:0;
	$arrViaje['vi_finalizado'] = isset($datosCargados[0]['vi_finalizado'])?$datosCargados[0]['vi_finalizado']:0;
	##Fin. Definición de parámetros
	
	
	##Inicio. En caso de error al guardar, se levanta los destinos que ingreso el usuario.
	if($arrDestinos){
		$referencias = $arrDestinos;
		$datos = $objViaje->getDestinos($referencias);	
		$arrRef = $datos['ref'];
		$arrViaje['id_geozonas'] = $datos['id_geozonas']?$datos['id_geozonas']:"";
	}
	##Fin. En caso de error al guardar, se levanta los destinos que ingreso el usuario.
	
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
	
	$idViaje = (int)$_POST['hidId']?(int)$_POST['hidId']:(int)$idViaje;
	$idUsuario = (int)$_SESSION["idUsuario"];

	global $objSeguridad;
	if(!$objSeguridad->validar($idViaje)){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: boot.php?c=".$seccion); 
		exit;
	}
	
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
	##Fin. Definición de parámetros
   	
	if($arrDestinos){
		$referencias = $arrDestinos;}
	else{	
		$referencias = $objViaje->getReferencias();}
	
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
	
	$extraCSS[] = 'css/estilosAbmViajes.css';
	$extraCSS[] = 'css/abmViajes.css';
	$extraCSS[] = 'css/ui/jquery.ui.datepicker.css';
	
    $extraJS[] = 'js/jquery/jquery-ui-1.8.5.custom.min.js';
	$extraJS[] = 'js/jquery/jquery.placeholder.js';
    $extraJS[] = 'js/jquery/jquery.datepicker.js';
	$extraJS[] = 'js/abmViajes.js';
	$extraJS[] = 'js/abmViajesAM.js';
	$extraJS[] = 'js/abmViajesFunciones.js';
	
	
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
	
	global $objSeguridad;
	if(!$objSeguridad->validar($idViaje)){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: boot.php?c=".$seccion); 
		exit;
	}

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

function volver(){
	header('Location: boot.php?c=abmViajes');
}

function guardarA($objSQLServer, $seccion) {
	global $lang;
    $mensaje = '';
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
				if ($_POST['popup_ready'] == '1') {?>
					<script>window.parent.cerrarPopup();</script>
				 <?php } 
				 else {
					header('Location: boot.php?c=abmViajes');
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
        require_once 'clases/clsReferencias.php';
        $objRef = new Referencia($objSQLServer);
        for ($i = 0; $i < count($ret['arrDestinos']); $i++) {
            $id_ref = $ret['arrDestinos'][$i]['vd_re_id'];
            $arrRef = $objRef->obtenerReferencias($id_ref);
            $ret['arrDestinos'][$i]['re_nombre'] = $arrRef[0]['re_nombre'];
        }
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
	global $lang;
    $mensaje = '';
    $ret = controlarCampos(1);
	$mensaje = $ret['mensaje'];
	$id_viaje = (int)$_POST['id_viaje'];

	global $objSeguridad;
	if(!$objSeguridad->validar($id_viaje)){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: boot.php?c=".$seccion); 
		exit;
	}
	
	if (!$mensaje) {
		require_once 'clases/clsViajes.php';
        $objViaje = new Viajes($objSQLServer, $id_viaje);
       	
		$arrMotivosCambio = $objViaje->getMotivoViajes($_POST['id_motivo_cambio']);
		$objViaje->setLog($lang->system->motivo.': '.encode($arrMotivosCambio[0]['vmc_descripcion']),$_POST['id_motivo_cambio']);
		
		$resp = $objViaje->updateViajes($ret['campos'],$ret['valorCampos']); 
		if($resp){
			$id_geozona = "";
			$coma = "";
			foreach($ret['arrDestinos'] as $item){
				$id_geozona.= $coma.$item['vd_re_id'];
				$coma = ",";	
			}
			$resp = $objViaje->deleteViajesDestinos($id_geozona);
			if($resp){
				$resp = $objViaje->setViajesDestinos($ret['arrDestinos']);		
				if($resp){
					header('Location: boot.php?c=abmViajes');
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
        require_once 'clases/clsReferencias.php';
        $objRef = new Referencia($objSQLServer);
        for ($i = 0; $i < count($ret['arrDestinos']); $i++) {
            $id_ref = $ret['arrDestinos'][$i]['vd_re_id'];
            $arrRef = $objRef->obtenerReferencias($id_ref);
            $ret['arrDestinos'][$i]['re_nombre'] = $arrRef[0]['re_nombre'];
        }
        //redireccionar al alta con los datos cargados.
       modificar($objSQLServer, $seccion, $mensaje, $id_viaje, $datosCargados, $ret['arrDestinos']);
	}	
}

function controlarCampos($esAlta = 0) {
	global $lang;
    $campos = array();
    $valorCampos = array();
    $mensaje = '';
    
	$campos[] = 'vi_us_id';
    $valorCampos[] = $_SESSION['idUsuario'];
	
	if(isset($_POST['cod_viaje'])){
		$campos[] = 'vi_codigo';
		$msjError = checkString(trim($_POST['cod_viaje']), 0, 50, $lang->system->codigo_viaje, 1);
		$valorCampos[] = "'" .trim($_POST['cod_viaje']). "'";
		if ($msjError){
			$mensaje.="* " . $msjError . "<br/> ";}
	}	

	if(isset($_POST['vi_contenedor'])){
		$campos[] = 'vi_contenedor';
		$valorCampos[] = !empty($_POST['vi_contenedor'])?"'".trim($_POST['vi_contenedor'])."'":NULL;
	}
	
	if(isset($_POST['tipo_viaje'])){
		$campos[] = 'vi_vt_id';
		$msjError = checkCombo((int)$_POST['tipo_viaje'], $lang->system->tipo_viaje, 1, 0);
		$valorCampos[] = (int)$_POST['tipo_viaje'];
		if ($msjError){
			$mensaje.="* " . $msjError . "<br/> ";}
	}	
		
	if(isset($_POST['dador'])){	
		$campos[] = 'vi_dador';
		$valorCampos[] = $_POST['dador']?$_POST['dador']:NULL;
		global $objSQLServer;
		require_once 'clases/clsUsuarios.php';
		$objUsuario = new Usuario($objSQLServer);
		$tipoUsuario = $objUsuario->get_tipoUsuario($_SESSION['idUsuario']);
		if($tipoUsuario == 'dador' || $tipoUsuario == 'empresa'){
			$msjError = checkCombo(trim($_POST['dador']), 'Dador', 1, 0);
			if ($msjError){
				$mensaje.="* " . $msjError . "<br/> ";}
		}
	}
	
	##valido transportista pero no lo guardo en base
	if(isset($_POST['transportista'])){
		if(empty($_POST['temp_transportista'])){
			$campos[] = 'vi_transportista';
			$valorCampos[] = $_POST['transportista']?$_POST['transportista']:NULL;
			$msjError = checkCombo((int)$_POST['transportista'], $lang->system->transportista, 1, 0);
			if ($msjError){
				$mensaje.="* " . $msjError . "<br/> ";}	
		}
	}	
		
	##valido tipo movil pero no lo guardo en base
   	//$msjError = checkCombo((int)$_POST['movil_tipo'], $lang->system->tipo_movil, 1, 0);
    //if ($msjError){
    //    $mensaje.="* " . $msjError . "<br/> ";}
		
	if(isset($_POST['movil'])){	
		$campos[] = 'vi_mo_id';
		$valorCampos[] = $_POST['movil']?$_POST['movil']:NULL;;
		//$msjError = checkCombo(trim($_POST['movil']), $lang->system->movil, 1, 0);
		//if ($msjError){
		//    $mensaje.="* " . $msjError . "<br/> ";}
	}

	if($_POST['conductor']){
		$campos[] = 'vi_co_id';
		$valorCampos[] = $_POST['conductor']?$_POST['conductor']:NULL;
		//$msjError = checkCombo(trim($_POST['conductor']), $lang->system->conductor, 0, 0);
		//if ($msjError){
		//    $mensaje.="* " . $msjError . "<br/> ";}
	}

	if(isset($_POST['observaciones'])){
    	$campos[] = 'vi_observaciones';
		$valorCampos[] = !empty($_POST['observaciones'])?"'".trim($_POST['observaciones'])."'":NULL;
	}
	
	if(isset($_POST['vi_finalizado'])){
		$campos[] = 'vi_finalizado';
		$valorCampos[] = $_POST['vi_finalizado']?$_POST['vi_finalizado']:0;
	}
	
	if(isset($_POST['vi_facturado'])){
		$campos[] = 'vi_facturado';
		$valorCampos[] = $_POST['vi_facturado']?$_POST['vi_facturado']:NULL;
	}
	 
	if(isset($_POST['id_geozonas'])){
		$arrDestinos = array();
		if (!empty($_POST['id_geozonas'])){
			$ref = explode(',',$_POST['id_geozonas']);
			$i = 0;
			foreach($ref as $ref_id){
				$arr_ini = explode('-',str_replace('/','-',$_POST['fecha_'.$ref_id]));
				$f_ini = $arr_ini[2].'-'.$arr_ini[1].'-'.$arr_ini[0].' '.$_POST['hora_'.$ref_id].':'.$_POST['min_'.$ref_id].':00'; 
				
				$f_fin = NULL;
				if(isset($_POST['f_egreso_'.$ref_id])){
					$array_fin = explode(' ',$_POST['f_egreso_'.$ref_id]);
					$arr_fin = explode('-',str_replace('/','-',$array_fin[0]));
					$f_fin = $arr_fin[2].'-'.$arr_fin[1].'-'.$arr_fin[0].' '.$array_fin[1].':00';
				}
				$arrDestinos[] = array('vd_orden' => $i, 'vd_re_id' => $ref_id, 'vd_ini' =>$f_ini , 'vd_fin' =>$f_fin);	
				$i++;
			}
		}
		else{
			$msjError = $lang->message->msj_viajes_ingresar_destinos;
			$mensaje.="* " . $msjError . "<br/> ";	
		}
	}

    return array('mensaje' => $mensaje, 'campos' => $campos, 'valorCampos' => $valorCampos, 'arrDestinos' => $arrDestinos);
}

function export_arribos($objSQLServer){
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
		->setTitle($lang->botonera->exportar_arribos)
		->setSubject($lang->botonera->exportar_arribos)
		->setDescription($lang->botonera->exportar_arribos)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
			
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $lang->system->nro_orden)
		->setCellValue('B1', $lang->system->transortista)
		->setCellValue('C1', $lang->system->movil)
		->setCellValue('D1', $lang->system->conductor)
		->setCellValue('E1', $lang->system->procedencia)
		->setCellValue('F1', $lang->system->arribo_a)
		->setCellValue('G1', $lang->system->ingreso_programado)
		->setCellValue('H1', $lang->system->ingreso_real)
		->setCellValue('I1', $lang->system->estado);					
						
	$arralCol = array('A','B','C','D','E','F','G','H','I');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','G','H','I');
	$objPHPExcel->alignCenter($alingCenterCol);
						
	$i = 2;
	foreach($regis as $row){
		$estado_arribro = '';
		if($row['diferenciaIngreso'] > 0){$estado_arribro = $lang->system->arribo_atrasado;}
		elseif($row['diferenciaIngreso'] <= 0 && trim($row['vd_ini_real']) == true){$estado_arribro = $lang->system->arribo_en_tiempo;}
		else{$estado_arribro = '-';}
									
		if(empty($row['procedencia'])){
			$row['procedencia'] = '-';
		}
									
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $row['vi_codigo'])
			->setCellValue('B'.$i, encode($row['transportista']))
			->setCellValue('C'.$i, encode($row['vi_movil']))
			->setCellValue('D'.$i, encode($row['co_conductor']))
			->setCellValue('E'.$i, encode($row['procedencia']))
			->setCellValue('F'.$i, encode($row['re_nombre']).' '.encode($row['re_descripcion']))							
			->setCellValue('G'.$i, formatearFecha($row['vd_ini']))
			->setCellValue('H'.$i, formatearFecha($row['vd_ini_real']))
			->setCellValue('I'.$i, $estado_arribro);
			$i++;
	}
						
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->botonera->exportar_arribos);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="listado-modulo-viajes-arribos-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}

function export_partidas($objSQLServer){
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
		->setTitle($lang->botonera->exportar_partidas)
		->setSubject($lang->botonera->exportar_partidas)
		->setDescription($lang->botonera->exportar_partidas)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $lang->system->nro_orden)
		->setCellValue('B1', $lang->system->transportista)
		->setCellValue('C1', $lang->system->movil)
		->setCellValue('D1', $lang->system->conductor)
		->setCellValue('E1', $lang->system->parte_de)
		->setCellValue('F1', $lang->system->destino)
		->setCellValue('G1', $lang->system->egreso_programado)
		->setCellValue('H1', $lang->system->egreso_real)
		->setCellValue('I1', $lang->system->estado);	
						
	$arralCol = array('A','B','C','D','E','F','G','H','I');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','G','H','I');
	$objPHPExcel->alignCenter($alingCenterCol);
					
	$i = 2;
	foreach($regis as $row){							
		$estado_egreso = '';
		if($row['diferenciaEgreso'] > 0){$estado_egreso = $lang->system->partio_atrasado;}
		elseif($row['diferenciaEgreso']<= 0 && trim($row['vd_fin_real']) == true){$estado_egreso = $lang->system->en_tiempo;}
		else{$estado_egreso = '-';}
							
		if(empty($row['destino'])){
			$row['destino'] = '-';
		}
							
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $row['vi_codigo'])
			->setCellValue('B'.$i, encode( $row['transportista']))					
			->setCellValue('C'.$i, encode($row['vi_movil']))							
			->setCellValue('D'.$i, encode($row['co_conductor']))							
			->setCellValue('E'.$i, encode($row['re_nombre']).' '.encode($row['re_descripcion']))	
			->setCellValue('F'.$i, encode($row['destino']))
			->setCellValue('G'.$i, formatearFecha($row['vd_fin']))
			->setCellValue('H'.$i, formatearFecha($row['vd_fin_real']))
			->setCellValue('I'.$i, $estado_egreso);
		$i++;
	}
						
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->botonera->exportar_partidas);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="listado-modulo-viajes-partidas-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
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
		->setCellValue('A1', $lang->system->nro_orden)
		->setCellValue('B1', $lang->system->transportista)
		->setCellValue('C1', $lang->system->conductor)
		->setCellValue('D1', $lang->system->origen)
		->setCellValue('E1', $lang->system->ingreso_prog_origen)
		->setCellValue('F1', $lang->system->ingreso_real_origen)
		->setCellValue('G1', $lang->system->egreso_prog_origen)
		->setCellValue('H1', $lang->system->egreso_real_origen)
		->setCellValue('I1', $lang->system->status_origen)
		->setCellValue('J1', $lang->system->estadia_origen)
		->setCellValue('K1', $lang->system->destino)
		->setCellValue('L1', $lang->system->ingreso_prog_destino)
		->setCellValue('M1', $lang->system->ingreso_real_destino)
		->setCellValue('N1', $lang->system->egreso_prog_destino)
		->setCellValue('O1', $lang->system->egreso_real_destino)
		->setCellValue('P1', $lang->system->status_destino)
		->setCellValue('Q1', $lang->system->estadia_destino);
						
	$arralCol = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','E','F','G','H','L','M','N','O');
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
			->setCellValue('C'.$i, encode($row['co_conductor']))
			->setCellValue('D'.$i, encode($row['o_origen']))							
			->setCellValue('E'.$i, formatearFecha($o_ini))
			->setCellValue('F'.$i, formatearFecha($o_ini_real))
			->setCellValue('G'.$i, formatearFecha($o_fin))
			->setCellValue('H'.$i, formatearFecha($o_fin_real))
			->setCellValue('I'.$i, $EstadosOrigenC)
			->setCellValue('J'.$i, $row['o_diferencia'])				
			->setCellValue('K'.$i, encode($row['re_nombre']).' '.encode($row['re_descripcion']))
			->setCellValue('L'.$i, formatearFecha($vd_ini))
			->setCellValue('M'.$i, formatearFecha($vd_ini_real))
			->setCellValue('N'.$i, formatearFecha($vd_fin))
			->setCellValue('O'.$i, formatearFecha($vd_fin_real))
			->setCellValue('P'.$i, $EstadosDestinoC)
			->setCellValue('Q'.$i, $row['d_diferencia']);
		$i++;
	}
						
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->botonera->exportar_estadias);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="listado-modulo-viajes-estadias-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}
?>