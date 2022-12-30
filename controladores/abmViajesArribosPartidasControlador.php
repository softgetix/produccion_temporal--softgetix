<?php
function index($objSQLServer, $seccion, $mensaje="", $filtroCol = false) {
	global $lang;
	$idUsuario = (int)$_SESSION['idUsuario'];
	$solapa = ($_REQUEST['solapa'] == 'partidas')?$_REQUEST['solapa']:'arribos';
		
	require_once 'clases/clsViajes.php';
    $objViaje = new Viajes($objSQLServer);
	
	$col['transportista'] = 'colidTransportista';
	$col['movil'] = 'colidMovil';
	$col['referencia'] = 'colidReferencia';
	$col['arribo'] = 'colArribos';
	$col['partida'] = 'colPartidas';
	$col['facturado'] = 'colFacturado';
	require_once 'clases/clsFiltrosCol.php';
	$objFiltroCol = new FiltrosCol($col);
	
	$arrViajes = $objViaje->getArribosPartidas($solapa);
	if($arrViajes){
		foreach($arrViajes as $k => $row){
			$objFiltroCol->value($col['transportista'],$row['transportista'],$row['id_transportista']);
			$objFiltroCol->value($col['movil'],$row['vi_movil'],$row['id_movil']);
			$objFiltroCol->value($col['referencia'],$row['re_nombre'],$row['re_id']);
		}
		if($solapa == 'partidas'){
			$objFiltroCol->value($col['partida'],$lang->system->egreso_realizado.' - '.$lang->system->atrasado,1);
			$objFiltroCol->value($col['partida'],$lang->system->egreso_realizado.' - '.$lang->system->en_tiempo,2);
			$objFiltroCol->value($col['partida'],$lang->system->egreso_pendiente.' - '.$lang->system->atrasado,3);
			$objFiltroCol->value($col['partida'],$lang->system->egreso_pendiente.' - '.$lang->system->en_tiempo,4);
		}
		else{
			$objFiltroCol->value($col['arribo'],$lang->system->ingreso_realizado.' - '.$lang->system->atrasado,1);
			$objFiltroCol->value($col['arribo'],$lang->system->ingreso_realizado.' - '.$lang->system->en_tiempo,2);
			$objFiltroCol->value($col['arribo'],$lang->system->ingreso_pendiente.' - '.$lang->system->atrasado,3);
			$objFiltroCol->value($col['arribo'],$lang->system->ingreso_pendiente.' - '.$lang->system->en_tiempo,4);
		}
		
		$objFiltroCol->value($col['facturado'],$lang->system->si,1);
		$objFiltroCol->value($col['facturado'],$lang->system->no,2);
		
		if(!$filtroCol){
			foreach($col as $item){
				unset($_POST[$item]);
			}
		}
		
		if($objFiltroCol->validar()){ 
			$filtros['transportista'] = $_POST[$col['transportista']]?implode(',',$_POST[$col['transportista']]):NULL;
			$filtros['movil'] = $_POST[$col['movil']]?implode(',',$_POST[$col['movil']]):NULL;
			$filtros['referencia'] = $_POST[$col['referencia']]?implode(',',$_POST[$col['referencia']]):NULL;
			$filtros['facturado'] = $_POST[$col['facturado']]?implode(',',$_POST[$col['facturado']]):NULL;
			if($solapa == 'partidas'){
				$filtros['partida'] = $_POST[$col['partida']]?implode(',',$_POST[$col['partida']]):NULL;
			}
			else{
				$filtros['arribo'] = $_POST[$col['arribo']]?implode(',',$_POST[$col['arribo']]):NULL;
			}
			
			$arrViajes = $objViaje->getArribosPartidas($solapa, $filtros);
			
			##-- Ajuste para poder filtrar Pendientes de Ingreso --##
			$arrFiltroArribos = explode(',',$filtros['arribo']);
				
			if(
				(in_array('1',$arrFiltroArribos) && in_array('2',$arrFiltroArribos))
				 || (in_array('3',$arrFiltroArribos) || in_array('4',$arrFiltroArribos))
			){
				foreach($arrViajes as $k => $row){
					$arribo = $objViaje->getTrayectoEstimado($row);
					$borrarPosition = true;
						
					if(in_array('4',$arrFiltroArribos)){				
						if(strtotime($row['vd_ini']) >= strtotime($arribo['fecha']) && !$row['vd_ini_real']){
							$borrarPosition = false;
						}
					}
					if(in_array('3',$arrFiltroArribos) && $borrarPosition){
						if(strtotime($row['vd_ini']) < strtotime($arribo['fecha']) && !$row['vd_ini_real']){
							$borrarPosition = false;
						}
					}
					if(in_array('2',$arrFiltroArribos) && $borrarPosition && $row['vd_ini_real']){
						if($row['diferenciaIngreso'] <= 0){
							$borrarPosition = false;
						}
					}
					if(in_array('1',$arrFiltroArribos) && $borrarPosition && $row['vd_ini_real']){
						if($row['diferenciaIngreso'] > 0){
							$borrarPosition = false;
						}
					}
					if($borrarPosition){
						unset($arrViajes[$k]);	
					}
				}
			}
			##-- --##
		}
	}
	
	$extraCSS[]='css/abmViajes.css';
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] ='js/popupHostFunciones.js';
	$extraJS[] = 'js/filtrosCol.js';
	$extraJS[] = 'js/abmViajesArribosPartidas.js';
	
	include_once('includes/template.php');
	$objFiltroCol->aplicar();
}

function filtrarCol($objSQLServer, $seccion){
	unset($_POST['hidId']);
	index($objSQLServer, $seccion, '', true);
}

function modificarFacturado($objSQLServer, $seccion){
	$idViaje = (int)$_POST['hidId'];
	$solapa = ($_REQUEST['solapa'] == 'partidas')?$_REQUEST['solapa']:'arribos';
	
	if($idViaje){
		require_once 'clases/clsViajes.php';
		$objViaje = new Viajes($objSQLServer, $idViaje);
		
		$arrViajes = $objViaje->getArribosPartidas($solapa);
		foreach($arrViajes as $viajes){
			if($viajes['vi_id'] == $idViaje){
				$campos = array();
				$valorCampos = array();
				array_push($campos,'vi_facturado');
				array_push($valorCampos,1);
				$objViaje->updateViajes($campos, $valorCampos);
				break;	
			}	
		}
		unset($_POST['hidId']);
	}
	filtrarCol($objSQLServer, $seccion);
	//header('Location:'.$_SERVER['HTTP_REFERER']);
}
?>