<?php
function index($objSQLServer, $seccion, $mensaje="", $filtroCol = false) {
	global $filtro;
	global $lang;
	
	$idUsuario = (int)$_SESSION['idUsuario'];
	$solapa = ($_REQUEST['solapa'] == 'partidas')?$_REQUEST['solapa']:'arribos';
    $arribos_y_partidas = 'planta';

    $col['transportista'] = 'colidTransportista';
	$col['movil'] = 'colidMovil';
	$col['referencia'] = 'colidReferencia';
	$col['arribo'] = 'colArribos';
	$col['partida'] = 'colPartidas';
	require_once 'clases/clsFiltrosCol.php';
    $objFiltroCol = new FiltrosCol($col);

    require_once 'clases/clsViajes.php';
	require_once 'clases/clsViajesDelivery.php';
	$objViaje = new ViajesDelivery($objSQLServer);
    
    $filtros['partidas'] = ($solapa == 'partidas')?1:0; //--Si estamos en "Partidas", el valor es 1, si estamos en "Arribos" el valor es 0
    $filtros['destinos'] = 0; //--Valor 0 para visualización "a Planta"
    $filtros['operacion'] = $_SESSION['idAgente'];
    $filtros['transportista'] = 'NULL';
	$filtros['movil'] = 'NULL';
    $filtros['referencia'] = 'NULL';
    $filtros['pendiente'] = -1; //Si quiero ver los casos en que hubo un ingreso o egreso el valor es 1. -1 no aplica filtro.
    $filtros['estado'] = -1;//Para ver lo que esta "En tiempo" el valor es 0, para ver lo que esta "Demorado" el valor es 1. -1 no aplica filtro.
    $arrViajes = $objViaje->getProcedureArribesPartidas($filtros);
    if($arrViajes){
		foreach($arrViajes as $k => $row){
			$objFiltroCol->value($col['transportista'],$row['transportista'],$row['id_transportista']);
			$objFiltroCol->value($col['movil'],$row['vi_movil'],$row['id_movil']);
			$objFiltroCol->value($col['referencia'],$row['re_nombre'],$row['re_id']);
		}
		if($solapa == 'partidas'){
            $objFiltroCol->value($col['partida'],'(Ver todos)',-1,true);
            /*
            $objFiltroCol->value($col['partida'],$lang->system->egreso_realizado.' - '.$lang->system->atrasado,1,true);
			$objFiltroCol->value($col['partida'],$lang->system->egreso_realizado.' - '.$lang->system->en_tiempo,2,true);
			$objFiltroCol->value($col['partida'],$lang->system->egreso_pendiente.' - '.$lang->system->atrasado,3,true);
            $objFiltroCol->value($col['partida'],$lang->system->egreso_pendiente.' - '.$lang->system->en_tiempo,4,true);
            */
            $objFiltroCol->value($col['partida'],$lang->system->egreso_realizado,5,true);
			$objFiltroCol->value($col['partida'],$lang->system->egreso_pendiente,6,true);
		}
		else{
            $objFiltroCol->value($col['arribo'],'(Ver todos)',-1,true);
            /*
            $objFiltroCol->value($col['arribo'],$lang->system->ingreso_realizado.' - '.$lang->system->atrasado,1,true);
			$objFiltroCol->value($col['arribo'],$lang->system->ingreso_realizado.' - '.$lang->system->en_tiempo,2,true);
			$objFiltroCol->value($col['arribo'],$lang->system->ingreso_pendiente.' - '.$lang->system->atrasado,3,true);
            $objFiltroCol->value($col['arribo'],$lang->system->ingreso_pendiente.' - '.$lang->system->en_tiempo,4,true);
            */
            $objFiltroCol->value($col['arribo'],$lang->system->ingreso_realizado,5,true);
			$objFiltroCol->value($col['arribo'],$lang->system->ingreso_pendiente,6,true);
		}
		
		if(!$filtroCol){
			foreach($col as $item){
				unset($_POST[$item]);
			}
		}
		
		if($objFiltroCol->validar()){ 
			$filtros['transportista'] = $_POST[$col['transportista']]?"'".implode(',',$_POST[$col['transportista']])."'":'NULL';
			$filtros['movil'] = $_POST[$col['movil']]?"'".implode(',',$_POST[$col['movil']])."'":'NULL';
			$filtros['referencia'] = $_POST[$col['referencia']]?"'".implode(',',$_POST[$col['referencia']])."'":'NULL';
            
            $auxFilter = NULL;
            if($solapa == 'partidas'){
                //$filtros['partida'] = $_POST[$col['partida']]?implode(',',$_POST[$col['partida']]):NULL;
                if(count($_POST[$col['partida']]) == 1){
                    $auxFilter = $_POST[$col['partida']][0];
                }
			}
			else{
                //$filtros['arribo'] = $_POST[$col['arribo']]?implode(',',$_POST[$col['arribo']]):NULL;
                if(count($_POST[$col['arribo']]) == 1){
                    $auxFilter = $_POST[$col['arribo']][0];
                }
            }
            switch($auxFilter){
               /*
                case 1: //Realizado - Demorado
                    $filtros['pendiente'] = 0;
                    $filtros['estado'] = 1;
                break;
                case 2: //Realizado - En Tiempo
                    $filtros['pendiente'] = 0;
                    $filtros['estado'] = 0;
                break;
                case 3: //Pendiente - Demorado
                    $filtros['pendiente'] = 1;
                    $filtros['estado'] = 1;
                break;
                case 4: //Pendiente - En Tiempo
                    $filtros['pendiente'] = 1;
                    $filtros['estado'] = 0;
                break;
                */
                case 5: //Realizado
                    $filtros['pendiente'] = 0;
                break;
                case 6: //Pendiente
                    $filtros['pendiente'] = 1;
                break;
            }
            $arrViajes = $objViaje->getProcedureArribesPartidas($filtros);;
        }
    }
    
    $deliveryView=false;
    $seccion = 'abmViajesDeliveryArribosPartidas';
    
	$extraCSS[]='css/abmViajes.css';
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] ='js/popupHostFunciones.js';
	$extraJS[] = 'js/filtrosCol.js';
    $extraJS[] = 'js/abmViajesDeliveryFunciones.js';
    $extraJS[] = 'js/abmViajesArribosPartidas.js';
	
	include_once('includes/template.php');
	$objFiltroCol->aplicar();
}

function filtrarCol($objSQLServer, $seccion){
	index($objSQLServer, $seccion, '', true);
}
?>
<?php
/*$seccion = 'abmViajesDeliveryArribosPartidas';
$filtro = 'planta';
require('abmViajesDeliveryArribosPartidasControlador.php');
*/
?>