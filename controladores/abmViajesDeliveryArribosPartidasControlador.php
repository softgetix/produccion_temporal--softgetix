<?php
function index($objSQLServer, $seccion, $mensaje="", $filtroCol = false) {
	global $filtro;
	global $lang;
	
	$idUsuario = (int)$_SESSION['idUsuario'];
	$solapa = ($_REQUEST['solapa'] == 'partidas')?$_REQUEST['solapa']:'arribos';
	
	require_once 'clases/clsViajes.php';
	require_once 'clases/clsViajesDelivery.php';
	$objViaje = new ViajesDelivery($objSQLServer);
	
	$col['transportista'] = 'colidTransportista';
	$col['movil'] = 'colidMovil';
	$col['referencia'] = 'colidReferencia';
	$col['arribo'] = 'colArribos';
	$col['partida'] = 'colPartidas';
	require_once 'clases/clsFiltrosCol.php';
	$objFiltroCol = new FiltrosCol($col);
	
	$filtros['tipo_referencia'] = $filtro;
	$arrViajes = $objViaje->getArribosPartidas($solapa,$filtros);
	
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
		
		if(!$filtroCol){
			foreach($col as $item){
				unset($_POST[$item]);
			}
		}
		
                
		
		if($objFiltroCol->validar()){ 
			$filtros['transportista'] = $_POST[$col['transportista']]?implode(',',$_POST[$col['transportista']]):NULL;
			$filtros['movil'] = $_POST[$col['movil']]?implode(',',$_POST[$col['movil']]):NULL;
			$filtros['referencia'] = $_POST[$col['referencia']]?implode(',',$_POST[$col['referencia']]):NULL;
			if($solapa == 'partidas'){
				$filtros['partida'] = $_POST[$col['partida']]?implode(',',$_POST[$col['partida']]):NULL;
			}
			else{
				$filtros['arribo'] = $_POST[$col['arribo']]?implode(',',$_POST[$col['arribo']]):NULL;
			}
			$arrViajes = $objViaje->getArribosPartidas($solapa, $filtros);
                        $arrViajes = onlyDestination($arrViajes);
			
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
						if(strtotime($row['fecha_ini']) >= strtotime($arribo['fecha']) && !$row['fecha_ini_real']){
							$borrarPosition = false;
						}
					}
					if(in_array('3',$arrFiltroArribos) && $borrarPosition){
						if(strtotime($row['fecha_ini']) < strtotime($arribo['fecha']) && !$row['fecha_ini_real']){
							$borrarPosition = false;
						}
					}
					if(in_array('2',$arrFiltroArribos) && $borrarPosition && $row['fecha_ini_real']){
						if(strtotime($row['fecha_ini_real']) <= strtotime($row['fecha_ini'])){
							$borrarPosition = false;
						}
					}
					if(in_array('1',$arrFiltroArribos) && $borrarPosition && $row['fecha_ini_real']){
						if(strtotime($row['fecha_ini_real']) > strtotime($row['fecha_ini'])){
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
                else{
                    $arrViajes = onlyDestination($arrViajes);
                }
	}
	
	$deliveryView=true;

	$extraCSS[]='css/abmViajes.css';
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] ='js/popupHostFunciones.js';
	$extraJS[] = 'js/filtrosCol.js';
	$extraJS[] = 'js/abmViajesArribosPartidas.js';
	
	include_once('includes/template.php');
	$objFiltroCol->aplicar();
}

function filtrarCol($objSQLServer, $seccion){
	index($objSQLServer, $seccion, '', true);
}

function onlyDestination($arrViajes){
    //--Ajuste para visualizar solamente un unico destido al que arriba o debe partir --//
    $auxViajes = array();
    foreach($arrViajes as $k => $row){
        if(in_array($row['vi_id'],$auxViajes)){
            if($re_ant != $row['re_id'] || $vi_ant != $row['vi_id']){
                unset($arrViajes[$k]);
            }
        }
        else{
            array_push($auxViajes,$row['vi_id']);
            $re_ant = $row['re_id'];
            $vi_ant = $row['vi_id'];
        }     	
    }
    return $arrViajes;
}
?>