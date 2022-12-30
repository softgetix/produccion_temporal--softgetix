<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
$sinDefaultCSS=$sinDefaultJS=true;

function index($objSQLServer, $seccion, $mensaje=""){
	global $objPerfil;
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$filtro = "";
	if($action === 'buscar') {
		busqueda($objSQLServer, $seccion);
	}
	elseif($action === 'grafico') {
		getGrafico($objSQLServer, $seccion);
	}
	else {
		$operacion 		= 'listar';
		$tipoBotonera 	= 'LI';
		$extraCSS		= array('css/estilosAbmListadoDefault.css','css/demo_page.css','css/demo_table_jui.css','css/TableTools.css','css/smoothness/jquery-ui-1.8.4.custom.css','css/estilosPopup.css');
		$extraJS		= array('js/jquery.autofill.js','js/jquery.dataTables.js','js/jquery.ui.js','js/popupHostFunciones.js');
		
		$extraCSS[] 	= 'css/fullcalendar.css';
		$extraCSS[] 	= 'css/estilosAbmIntermillAP.css?v='.rand(0,9999);
		$extraJS[] 		= 'js/jquery-ui-1.8.17.custom.min.js';
		$extraJS[] 		= 'js/fullcalendar.min.js';
		$extraJS[]		= 'js/abmIntermillAPListar.js';
		
		require 'includes/template.php';
	}
}

function busqueda($objSQLServer, $seccion) {
	global $lang;

	require_once 'clases/clsIntermill.php';
	$objIntermill 	= new Intermill($objSQLServer);
	
	require_once 'clases/clsCruces.php';
	$objCruce 	= new Viajes($objSQLServer);
	
	$inicio = date("Y-m-d") . " 00:00:00";
    $fin    = date("Y-m-d") . " 23:59:59";
	
	$arribos 		= $objIntermill->obtenerTableroIntermill($inicio,$fin,'arribos',1);
	$partidas 	= $objIntermill->obtenerTableroIntermill($inicio,$fin,'partidas',0);
	$cruces 		= $objCruce->obtenerArribos($inicio,$fin);
	
	if (is_array($cruces)) {
		// Tengo que agregar los cruces que aun no hayan sido detectados.
		$arribos2 = $arribos;
		foreach ($cruces as $cruce){
			$encontrado = false;
			foreach ($arribos2 as $arriboid => $arribo){
				if ($arribo['Vehiculo'] == $cruce['Vehiculo']){
					$encontrado = true;
					break;
				}
			}
			
			if ($encontrado === false){
				if ($cruce["FechaProgramada"]) {
					$cruce["FechaProgramada"] = date("d/m H:i", $cruce["FechaProgramada"] + 3 * 3600);// .' (E:'.date("H:i",$row["FechaEgresoTs"]).')';
				}

				$distancia = distancia(
					$cruce['sh_latitud'], 
					$cruce['sh_longitud'], 
					$cruce['rc_latitud'], 
					$cruce['rc_longitud']);
				$horas = ($distancia - 50) / 50;
				/*
				// Solo para el perfil localizar-t
				if ($_SESSION["idPerfil"] == 99999) {
					$cruce['Link'] = "http://maps.google.com/maps?q=".$cruce['sh_latitud'].",".$cruce['sh_longitud'];
				}
				*/
				$cruce['FechaEstimada'] = date("d/m H:i", time() + ($horas+1)*3600);
				$cruce['FechaIngreso'] = '';
				$cruce['ev_fecha'] = '';
				if ($cruce['sh_latitud'] != 0) {
					$cruce['Distancia'] = "(a " . number_format($distancia, 0) . " kms)";
				} else {
					$cruce['Distancia'] = -1;
				}

				$cruce['EsCruce'] = 1;
				$arribos[] = $cruce;
			} else {
				$arribos[$arriboid]['Observaciones'] = $cruce['Observaciones'];
				$arribos[$arriboid]['Nombre'] = $cruce['Nombre'];
				$arribos[$arriboid]['EsCruce'] = 2;
			}
		}
	}
	if($arribos || $partidas) {
		if ($arribos) limpiarArray($arribos);
		if ($partidas) limpiarArray($partidas);
		
		foreach ($arribos as $id => $arribo) {
			if (isset($arribos[$id]['UltimoReporte'])) {
				if (time() - strtotime($arribos[$id]['UltimoReporte']) > 30 * 60) {
					$arribos[$id]['NoReportando'] = 1;
				} else {
					$arribos[$id]['NoReportando'] = 0;
				}
				
				$arribos[$id]['TiempoUltimoReporte'] = time() - strtotime($arribos[$id]['UltimoReporte']); 
			}
			/*
			if (isset($arribo['lat']) && isset($arribo['lng']) && $_SESSION["idPerfil"] == 9999) {
				$arribos[$id]['Link'] = "http://maps.google.com/maps?q=".$arribo['lat'].",".$arribo['lng'];
			}*/
			if (!empty($arribo['FechaIngresoTs'])) {
				$arribos[$id]['Estadia'] = calcular_estadia($arribo['FechaIngresoTs']);
			}
			
			if (!empty($arribo['FechaEgresoTs'])) {
				$arribos[$id]['FechaEgresoFormato'] = date("d/m H:i", $arribo['FechaEgresoTs']);
			}
		}
		
		foreach ($partidas as $id => $partida) {
			if (!empty($partida['FechaIngresoTs'])) {
				// Si tiene egreso calculamos hasta esa fecha, 
				// sino suponemos que sigue en el lugar.
				$hasta = time();
				if (!empty($partida['FechaEgresoTs'])) {
					$hasta = $partida['FechaEgresoTs'];
				}
				$partidas[$id]['Estadia'] = calcular_estadia($partida['FechaIngresoTs'], $hasta);
			}
		}
		
		$temp2->arribos = ($arribos) ? $arribos: array();
		$temp2->partidas = ($partidas) ? $partidas: array();
		$temp2->msg = 'ok';
		$temp2->status = 1;
		$json = json_encode($temp2);
	} else {
		$out->msg = $lang->message->sin_resultados;
		$out->status = 2;
		$json = json_encode($out);
	}
	
	if($seccion == 'grafico'){
		return $json;
	}
	else{
		header('Content-Type: application/json');
		echo $json;
	}
}

function getGrafico($objSQLServer, $seccion){
	
	## 1- Obtengo info de todos los viajes existentes ##
	$arr_viajes = json_decode(busqueda($objSQLServer, 'grafico'));
	
	$viajes = array();
	foreach($arr_viajes->arribos as $item){
			$viajes[$item->IdReferencia][$item->Vehiculo][] = array('ingreso' => getFecha($item->Ingreso), 'egreso' => getFecha($item->Egreso), 'referencia' => $item->NombreCorto);
	}
	
	foreach($arr_viajes->partidas as $item){
		## actualizo fecha egreso en el array en caso q tenga partidas ##
		$actualiza = false;
		if($viajes[$item->IdReferencia][$item->Vehiculo]){
			foreach($viajes[$item->IdReferencia][$item->Vehiculo] as $k => $movil){
				if($movil['ingreso'] == getFecha($item->Ingreso)){
					$actualiza = $k;	
				}	
			}
		}
		
		if($actualiza === false){
			$viajes[$item->IdReferencia][$item->Vehiculo][] = array('ingreso' => getFecha($item->Ingreso), 'egreso' => getFecha($item->Egreso), 'referencia' => $item->NombreCorto);
		}
		else{
			$viajes[$item->IdReferencia][$item->Vehiculo][$actualiza] = array('ingreso' => getFecha($item->Ingreso), 'egreso' => getFecha($item->Egreso), 'referencia' => $item->NombreCorto);	
		}
	}
	
	## 1- fin ##
	
	
	## 2- Obtengo referencias, y calculo cant de vehiculos en cada referencia ##
	require_once 'clases/clsIntermill.php';
	$intermil = new Intermill($objSQLServer);
	$referencias = $intermil->getReferencias();
	$arr_ref = array();
	foreach($referencias as $item){
		
		##--##
		$cant_ingreso = 0;
		$cant_egreso = 0;
		$mat_ingreso = "";
		$mat_egreso = "";
		$coma_ingreso = "";
		$coma_egreso = "";
		if($viajes[$item['re_id']]){
			foreach($viajes[$item['re_id']] as $k => $item_viaje){
				if(!empty($item_viaje[0]['ingreso']) && empty($item_viaje[0]['egreso'])){
					$cant_ingreso++;	
					$mat_ingreso.= $coma_ingreso.$k;
					$coma_ingreso = ",";
				}
				elseif(!empty($item_viaje[0]['ingreso']) && !empty($item_viaje[0]['egreso'])){
					if(in_array($item['re_id'],$intermil->referencias['proveedor'])){##-- contar cant e proveedores están yendo hacia "San Luis 2" --##
						$cant_egreso++;	
						$mat_egreso.= $coma_egreso.$k;
						$coma_egreso = ",";
					}
				}
			}
		}
		##--##
		
		##-- Obtener viajes (ingresos/egresos) para proveedores --##
		if(in_array($item['re_id'],$intermil->referencias['proveedor'])){
			$datos['re_id'] = $item['re_id'];
			
			$pendiente = $intermil->getViajesPendienteIngreso($datos);
			$ingreso = array(
				'pendiente' => $pendiente['cant']
				,'mat-pendiente' => $pendiente['matriculas']?'M&oacute;viles:'.$pendiente['matriculas']:''
			);
			$egreso = array(
				'a_planta' => $cant_egreso
				,'mat-planta' => $mat_egreso?'M&oacute;viles:'.$mat_egreso:''
			);
		}
		##-- --##
		
		##-- Obtener viajes (ingresos/egresos) de Planta (SAN LUIS 2) --##
		if($item['re_id'] == $intermil->referencias['planta'][0]){
			
			$kcclap = $intermil->getViajesEnTransito($intermil->referencias['deposito'][0], $item['re_id']);
			$ingreso = array(
				'kc-clap' => $kcclap['cant']
				,'mat-kc-clap' => $kcclap['matriculas']?'M&oacute;viles:'.$kcclap['matriculas']:'' 	
			);
			
			$kcclap = $intermil->getViajesEnTransito($item['re_id'], $intermil->referencias['deposito'][0]);
			$kcclass = $intermil->getViajesEnTransito($item['re_id'], $intermil->referencias['deposito'][1]);
			$kcpilar = $intermil->getViajesEnTransito($item['re_id'], $intermil->referencias['planta'][1]);
			$cliente = $intermil->getViajesEnTransito($item['re_id'], $intermil->referencias['cliente'][0]);
			$egreso = array(
				'kc-clap' => $kcclap['cant']
				,'mat-kc-clap' => $kcclap['matriculas']?'M&oacute;viles:'.$kcclap['matriculas']:''
				,'kc-class' => $kcclass['cant']
				,'mat-kc-class' => $kcclass['matriculas']?'M&oacute;viles:'.$kcclass['matriculas']:''
				,'kc-pilar' => $kcpilar['cant']
				,'mat-kc-pilar' => $kcpilar['matriculas']?'M&oacute;viles:'.$kcpilar['matriculas']:''
				,'cliente' => $cliente['cant']
				,'mat-cliente' => $cliente['matriculas']?'M&oacute;viles:'.$cliente['matriculas']:'' 
			);
		}
		##-- --##
		
		##-- Obtener viajes (ingresos/egresos) de Planta (KC PILAR) --##
		if($item['re_id'] == $intermil->referencias['planta'][1]){
			$kcclass = $intermil->getViajesEnTransito($item['re_id'], $intermil->referencias['deposito'][1]);
			$cliente = $intermil->getViajesEnTransito($item['re_id'], $intermil->referencias['cliente'][0]);
			$egreso = array(
				'kc-class' => $kcclass['cant'] 
				,'mat-kc-class' => $kcclass['matriculas']?'M&oacute;viles:'.$kcclass['matriculas']:''
				,'cliente' => $cliente['cant']
				,'mat-cliente' => $cliente['matriculas']?'M&oacute;viles:'.$cliente['matriculas']:''
			);
		}
		##-- --##
		
		##-- Obtener viajes (egresos) para Deposito (KC CLAP) --##
		if($item['re_id'] == $intermil->referencias['deposito'][0]){
			$cliente = $intermil->getViajesEnTransito($item['re_id'], $intermil->referencias['cliente'][0]);
			$egreso = array(
				'cliente' => $cliente['cant']
				,'mat-cliente' => $cliente['matriculas']?'M&oacute;viles:'.$cliente['matriculas']:''
			);
		}
		##-- --##
		
		##-- Obtener viajes (ingresos/egresos) para Deposito (KC CLASS) --##
		if($item['re_id'] == $intermil->referencias['deposito'][1]){
			$kcclap = $intermil->getViajesEnTransito($intermil->referencias['deposito'][0],$item['re_id']);
			$ingreso = array(
				'kc-clap' => $kcclap['cant']
				,'mat-kc-clap' => $kcclap['matriculas']?'M&oacute;viles:'.$kcclap['matriculas']:''
			);
			
			$cliente = $intermil->getViajesEnTransito($item['re_id'], $intermil->referencias['cliente'][0]);
			$egreso = array(
				'cliente' => $cliente['cant']
				,'mat-cliente' => $cliente['matriculas']?'M&oacute;viles:'.$cliente['matriculas']:''
			);
		}
		##-- --##
		
		
		$arr_ref[$item['tipo']][] = array(
			're_id' => $item['re_id']
			, 're_nombre' => $item['re_nombre']
			, 'en_terminal' => $cant_ingreso
			, 'mat_en_terminal' => $mat_ingreso?'M&oacute;viles:'.$mat_ingreso:''
			, 'ingreso' => $ingreso
			, 'egreso' => $egreso
		);
	}
	## 2- fin ##
	
	$graf->grafico = '<div id="cuerpoGrafico" style="width: 100%;  overflow:auto ">'; //overflow: auto; height: 430px;
	$graf->grafico.= '<div class="cuerpo-grafico" style="width: 100%; height: 1150px; overflow:hidden">'; // 
	
	## Proveedores ##
	$graf->grafico.= '<fieldset class="t-proveedor float_l">';
	foreach($arr_ref['Proveedor'] as $item){
		$graf->grafico.= '
			<div id="ref-'.$item['re_id'].'" class="caja-proveedor '.($item['en_terminal']?'on':'').'">
				<span class="nro" title="'.$item['mat_en_terminal'].'">#'.$item['en_terminal'].'</span>
				<span class="ingreso '.($item['ingreso']['pendiente']?'on':'').'" title="'.$item['ingreso']['mat-pendiente'].'">'.$item['ingreso']['pendiente'].'</span>
				<span class="egreso '.($item['egreso']['a_planta']?'on':'').'" title="'.$item['egreso']['mat-planta'].'">'.$item['egreso']['a_planta'].'</span>
				<span class="label">'.$item['re_nombre'].'</span>
			</div>
		';
	}
	$graf->grafico.= '</fieldset>';
    ##--##
	
	## Planta ##
	$graf->grafico.= '<fieldset class="t-planta float_l">';
	$item = $arr_ref['Planta'][0]; // KC PILAR	
	$graf->grafico.= '
		<div id="ref-'.$item['re_id'].'" class="caja-planta '.($item['en_terminal']?'on':'').'">
			<span class="nro" title="'.$item['mat_en_terminal'].'">#'.$item['en_terminal'].'</span>
			<span class="label planta">'.$item['re_nombre'].'</span>
			<span class="egreso kc-class '.($item['egreso']['kc-class']?'on':'').'" title="'.$item['egreso']['mat-kc-class'].'">'.$item['egreso']['kc-class'].'</span>
			<span class="egreso cliente '.($item['egreso']['cliente']?'on':'').'" title="'.$item['egreso']['mat-cliente'].'">'.$item['egreso']['cliente'].'</span>
		</div>';
	$item = $arr_ref['Planta'][1]; //SAN LUIS 2	
	$graf->grafico.= '
		<div id="ref-'.$item['re_id'].'" class="caja-planta '.($item['en_terminal']?'on':'').'">
			<span class="nro" title="'.$item['mat_en_terminal'].'">#'.$item['en_terminal'].'</span>
			<span class="label planta">'.$item['re_nombre'].'</span>
			<span class="ingreso '.($item['ingreso']['kc-clap']?'on':'').'" title="'.$item['ingreso']['mat-kc-clap'].'">'.$item['ingreso']['kc-clap'].'</span>
			<span class="egreso kc-clap '.($item['egreso']['kc-clap']?'on':'').'" title="'.$item['egreso']['mat-kc-clap'].'">'.$item['egreso']['kc-clap'].'</span>
			<span class="egreso kc-class '.($item['egreso']['kc-class']?'on':'').'" title="'.$item['egreso']['mat-kc-class'].'">'.$item['egreso']['kc-class'].'</span>
			<span class="egreso cliente '.($item['egreso']['cliente']?'on':'').'" title="'.$item['egreso']['mat-cliente'].'">'.$item['egreso']['cliente'].'</span>
		</div>';
	$graf->grafico.= '</fieldset>';
    ##--##
	
	## Deposito ##
	$graf->grafico.= '<fieldset class="t-deposito float_l">';
	$item = $arr_ref['Deposito'][0];//KC CLAP	
	$graf->grafico.= '
		<div id="ref-'.$item['re_id'].'" class="caja-depositos '.($item['en_terminal']?'on':'').'">
			<span class="nro" title="'.$item['mat_en_terminal'].'">#'.$item['en_terminal'].'</span>
			<span class="label depositos">'.$item['re_nombre'].'</span>
			<span class="egreso cliente '.($item['egreso']['cliente']?'on':'').'" title="'.$item['egreso']['mat-cliente'].'">'.$item['egreso']['cliente'].'</span>
		</div>';
	$item = $arr_ref['Deposito'][1];//KC CLASS	
	$graf->grafico.= '
		<div id="ref-'.$item['re_id'].'" class="caja-depositos '.($item['en_terminal']?'on':'').'">
			<span class="nro"title="'.$item['mat_en_terminal'].'">#'.$item['en_terminal'].'</span>
			<span class="label depositos">'.$item['re_nombre'].'</span>
			<span class="ingreso '.($item['ingreso']['kc-clap']?'on':'').'" title="'.$item['ingreso']['mat-kc-clap'].'">'.$item['ingreso']['kc-clap'].'</span>
			<span class="egreso cliente '.($item['egreso']['cliente']?'on':'').'" title="'.$item['egreso']['mat-cliente'].'">'.$item['egreso']['cliente'].'</span>
		</div>';	
	$graf->grafico.= '</fieldset>';
    ##--##
	
	## Cliente ##
	$graf->grafico.= '<fieldset class="t-cliente float_l">';
	$item = $arr_ref['Cliente'][0];//CLIENTE	
	$graf->grafico.= '
		<div id="ref-'.$item['re_id'].'" class="caja-cliente '.($item['en_terminal']?'on':'').'">
			<!--<span class="nro" title="'.$item['mat_en_terminal'].'">#'.$item['en_terminal'].'</span>-->
			<span class="label cliente">CLIENTES'./*$item['re_nombre'].*/'</span>
		</div>';
	$graf->grafico.= '</fieldset>';
    ##--##
	
	$graf->grafico.= '</div>';
	$graf->grafico.= '<img src="imagenes/kcc_fondo_flechas.png" border="0" id="img-lineas">';
	$graf->grafico.= '</div>';
	
	
	
	$graf->msg = 'ok';
	$graf->status = 1;
	header('Content-Type: application/json');
	$json = json_encode($graf);
	echo $json;
}

function getFecha($fecha){
	if(!empty($fecha)){
		$f = date('d-m-Y H:i',strtotime($fecha));}
	else{
		$f = NULL;}
	return $f;		
}