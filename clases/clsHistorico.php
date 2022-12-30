<?php
class Historico {
    var $objSQLServer;

    function Historico($objSQLServer) {
        $this->objSQL = $objSQLServer;
        return TRUE;
    }

   	function llenarTablaTemporal($desde, $hasta, $strMoviles, $idUsuario = 0, $strEventos = "", $strAlertas = "") {
        $desde = dateToDataBase($desde);
        $hasta = dateToDataBase($hasta);
        $strSQL = "EXEC dbo.informeHistorico '$desde','$hasta','$strMoviles',$idUsuario,'$strEventos','{$strAlertas}'";
        
        $objHistorico = $this->objSQL->dbQuery($strSQL);
		$arrHistorico = $this->objSQL->dbGetAllRows($objHistorico, 3);
		if($arrHistorico){
			$intRows = $this->objSQL->dbNumRows($objHistorico);
			for ($i = 0; $i < $intRows; $i++) {
                $arrHistorico[$i]['fechaGenerado'] = formatearFecha($arrHistorico[$i]['fechaGenerado']);
				$arrHistorico[$i]['movil'] = encode($arrHistorico[$i]['movil']);
				$arrHistorico[$i]['evento'] = encode($arrHistorico[$i]['evento']);
            }
		
			if($arrHistorico[0]['Resultado'] != 'Error 408'){//- Expiro tiempo de respuesta --//
				return $arrHistorico;		
			}
			else{
				return $arrHistorico[0]['Resultado'];
			}
		}
		return false;
    }
	
   ##-------- --------##
	function agruparHistorico($arrHistorico, $desde = NULL, $hasta = NULL) {
		$unsolodia = false;
		if (!is_array($arrHistorico)) {
			return $arrHistorico;
		}
	
		if (date('d-m-Y',strtotime($desde)) == date('d-m-Y',strtotime($hasta))) {
			$unsolodia = true;
		}
		
		$hoy = getFechaServer('d-m-Y');
		$eventoAnterior = $lat = $lng = NULL;
		$i = 0;
		
		require_once("clases/clsDefinicionReportes.php");
		$objEventos = new DefinicionReporte($this->objSQL);
	
		foreach ($arrHistorico as $id => $item) {
			$arrHistorico[$id]['hs_desde'] = isset($arrHistorico[$id]['hs_desde'])?$arrHistorico[$id]['hs_desde']:NULL; // Hora desde	[20]
			$arrHistorico[$id]['hs_hasta'] = isset($arrHistorico[$id]['hs_hasta'])?$arrHistorico[$id]['hs_hasta']:NULL; // Hora hasta	[36]
			$arrHistorico[$id]['fecha_txt'] = isset($arrHistorico[$id]['fecha_txt'])?$arrHistorico[$id]['fecha_txt']:NULL; 			//	[5]
			
			$borrado = false;
			$i++;
			
			$arrHistorico[$id]['orden'] = $i;
			$arrHistorico[$id]['fecha'] = $item['fechaGenerado'];
			$arrHistorico[$id]['evento_txt'] = $objEventos->traducirEvento($item['idEvento']);
			
			if(substr($item['fechaGenerado'], 0, 10) == $hoy || $unsolodia) {
				$arrHistorico[$id]['hs_desde'] = isset($arrHistorico[$id]['hs_desde'])?$arrHistorico[$id]['hs_desde']:substr($item['fechaGenerado'], 11);
				$arrHistorico[$id]['fecha_txt'] = substr($item['fechaGenerado'], 11); 
			}
			else{
				//$arrHistorico[$id]['fecha_txt'] = substr($item['fechaGenerado'],0,5)." ".substr($item['fechaGenerado'], 11);
				//$arrHistorico[$id]['fecha_txt'] = substr($item['fechaGenerado'],5,5)." ".substr($item['fechaGenerado'], 11);
				$arrHistorico[$id]['fecha_txt'] = $item['fechaGenerado'];
			}
			
			$dist = distancia($lat, $lng, $item['lat'], $item['lon']);
			if (is_nan($dist)) {
				$dist = 0;
			}
			
			if (($eventoAnterior == $item['idEvento'] && $this->tipoMovil == 'vehiculo') || ($this->tipoMovil != 'vehiculo')) {// Si no es de tipo Vehículo agrupa todos los eventos cercanos independientemente del tipo del evento. Si es tipo Vehiculo agupa solo los eventos del mismo tipo.	
				if(($dist * 1000) < 150) {
					if(isset($arrHistorico[$id]['hs_desde'])) {
						$arrHistorico[$idAnterior]['hs_hasta'] = $arrHistorico[$id]['hs_desde'];
					}
					else{
						$arrHistorico[$idAnterior]['hs_hasta'] = $arrHistorico[$id]['fecha_txt'];
					}
					
					
					if(isset($arrHistorico[$idAnterior][37])) {
						$arrHistorico[$idAnterior][37] = $arrHistorico[$idAnterior][37] + 1;
					}
					else{
						$arrHistorico[$idAnterior][37] = 2;
					}
					
					$i--;
					unset($arrHistorico[$id]);
					$borrado = true;
				}
				else{
					$idAnterior = $id;
				}
			}
			else{
				$idAnterior = $id;
			}
	
			if($borrado == false) {
				$eventoAnterior = $item['idEvento'];
				$lat = $item['lat'];
				$lng = $item['lon'];
			}
		}
	
		$arrHistorico = array_values($arrHistorico);
		
		##-- Ferificar Saltos y Picos eliminados --##
		foreach($arrHistorico as $k =>$item){
			if(isset($item['fecha_delete'])){
				if(substr($item['fecha_delete'], 0, 10) == $hoy || $unsolodia){
					## Formato: H:i
					$fd = substr($item['fecha_delete'], 11);
				}
				else{
					## Formato: d/m H:i
					$fd = substr($item['fecha_delete'],0,5)." ".substr($item['fecha_delete'], 11);
				}
				
				if($item['hs_hasta'] != ""){
					if(strtotime($fd) > strtotime($item['hs_hasta'])){
						$arrHistorico[$k]['hs_hasta'] = $fd;	
					}
				}
				else{
					$arrHistorico[$k]['hs_hasta'] = $fd;		
				}
			}
		}
		##-- --##
		
		return $arrHistorico;
	}
	
	function historicoVista($arrHistorico) {
		global $lang;
		if (!is_array($arrHistorico)) {
			return $arrHistorico;
		}
		
		foreach ($arrHistorico as $id => $arr){
			if(empty($arr['hs_desde']) && empty($arr['hs_hasta'])){
				$arrHistorico[$id][37] = $arr[37] = 0;
			}
			
			if (isset($arr[37]) && $arr[37] > 0) {
				if(!tienePerfil(16)){
					$arrHistorico[$id]['evento_txt'].= ' ('.$arrHistorico[$id][37].')';
				}
				
				if($arrHistorico[$id]['hs_desde'] != $arrHistorico[$id]['hs_hasta'] 
					&& $arrHistorico[$id]['fecha_txt'] != $arrHistorico[$id]['hs_hasta']){
					$arrHistorico[$id]['fecha_txt'].= ' '.$lang->system->a.' '.$arrHistorico[$id]['hs_hasta'];
					$arrHistorico[$id]['fecha'].= ' '.$lang->system->a.' '.$arrHistorico[$id]['hs_hasta'];
				}
			}
		}
		return $arrHistorico;
	}
	
	function limpiarSaltosPtos($arrHistorico){
		$eliminados = 0;
		
		for($i = 2; $i< count($arrHistorico); $i ++) {
			$ival = $i - 2;
			for($val = $ival; $val >= 0; $val --){
				if(isset($arrHistorico[$val]['orden'])){
					$ival = $val;	
					$val = -1;
				}	
			}
			
			##-- Verifico si el pto 1 y 3 se encuentran a menos de 150mts --##
			$distPtos = distancia($arrHistorico[$ival]['lat'], $arrHistorico[$ival]['lon'], $arrHistorico[$i]['lat'], $arrHistorico[$i]['lon']);
			if(($distPtos * 1000) < 150){
				##-- Verifico si el pto 1 y 2 se encuentran a menos de 150mts --##
				$distPtos_2 = distancia($arrHistorico[$i-1]['lat'], $arrHistorico[$i-1]['lon'], $arrHistorico[$i]['lat'], $arrHistorico[$i]['lon']);	
				if(($distPtos_2 * 1000) > 150){//-- Si es mayor entonces es un salto --//
					$df = true;
					if(isset($arrHistorico[$ival]['fecha_delete'])){
						if(strtotime($arrHistorico[$ival]['fecha_delete']) > strtotime($arrHistorico[$i-1]['fechaGenerado'])){
							$df = false;
						}
					}
					if($df == true){
						$arrHistorico[$ival]['fecha_delete'] = $arrHistorico[$i-1]['fechaGenerado'];
						$arrHistorico[$ival][37] = (isset($arrHistorico[$ival][37]))?($arrHistorico[$ival][37]+1):2;
					}
					unset($arrHistorico[$i-1]);	
					$eliminados ++;
				}
			}
			##-- --##
		}
		
		$arrHistoricoTemp = $arrHistorico;
		unset($arrHistorico);
		$arrHistorico = array();
		foreach($arrHistoricoTemp as $item){
			array_push($arrHistorico, $item);	
		}
		
		$resp = array('eliminados' => $eliminados, 'arrHistorico' =>$arrHistorico);
		return $resp;
	}
	
	function limpiarPicosPtos($arrHistorico){
		for($i = 2; $i< count($arrHistorico); $i ++) {
			$ival = $i - 2;
			for($val = $ival; $val >= 0; $val --){
				if(isset($arrHistorico[$val]['orden'])){
					$ival = $val;	
					$val = -1;
				}	
			}
			$lat1 = $arrHistorico[$i]['lat'];
			$lng1 = $arrHistorico[$i]['lon'];
			
			$lat2 = $arrHistorico[$i-1]['lat'];
			$lng2 = $arrHistorico[$i-1]['lon'];
			
			$lat3 = $arrHistorico[$ival]['lat'];
			$lng3 = $arrHistorico[$ival]['lon'];
			
				
			##-- Verifico si existe pico entre el pto 1 y 3 --##
			$resp = calcularAngulos($arrHistorico[$i], $arrHistorico[$ival], $arrHistorico[$i-1]);
			$angulo = isset($resp['gamma'])?$resp['gamma']:999;
			
			if($angulo < 30){//-- Si el angulo es menor a 30, elimino el pico --//
				
				$distPtos[0] = distancia($lat2, $lng2, $lat3, $lng3);
				$distPtos[1] = distancia($lat1, $lng1, $lat2, $lng2);
				if($distPtos[0] < $distPtos[1]){
					$arrHistorico[$ival]['fecha_delete'] = $arrHistorico[$i-1]['fechaGenerado'];
					$arrHistorico[$ival][37] = (isset($arrHistorico[$ival][37]))?($arrHistorico[$ival][37]+1):2;	
				}
				else{
					$arrHistorico[$i]['fecha_delete'] = $arrHistorico[$i]['fechaGenerado'];
					$arrHistorico[$i]['fechaGenerado'] = $arrHistorico[$i-1]['fechaGenerado'];
					$arrHistorico[$i][37] = (isset($arrHistorico[$i][37]))?($arrHistorico[$i][37]+1):2;	
				}
				
				unset($arrHistorico[$i-1]);	
			}
			##-- --##
		}

		$arrHistoricoTemp = $arrHistorico;
		unset($arrHistorico);
		$arrHistorico = array();
		foreach($arrHistoricoTemp as $item){
			array_push($arrHistorico, $item);	
		}
		
		return $arrHistorico;
	}
	
	function historicoDistancia($arrHistorico) {
		$anterior = NULL;
		$odometro = 0;
		if(is_array($arrHistorico)){
			foreach ($arrHistorico as $id => $actual) {
				$arrHistorico[$id]['km_acumulado'] = 0;
				if((int)$arrHistorico[$id]['odometro'] > 0){}//En caso q tenga valores en el Campo ODOMETRO
				else{
					if ($anterior !== NULL) {
		
						$km = distancia($actual['lat'], $actual['lon'], $arrHistorico[$anterior]['lat'], $arrHistorico[$anterior]['lon']);
										if (is_nan($km)) {
											$km = 0;
										}
						$arrHistorico[$id]['km'] = $km;
						$arrHistorico[$id]['km_acumulado'] = $arrHistorico[$anterior]['km_acumulado'] + $km;
						
						//$arrHistorico[$id]['odometro'] = $arrHistorico[$anterior]['odometro'] + $km;
						$odometro = $odometro + $km;
						$arrHistorico[$id]['odometro'] = number_format($odometro,2,',','.');
					} else {
						$arrHistorico[$id]['km'] = 0;
						$arrHistorico[$id]['km_acumulado'] = 0;
						$arrHistorico[$id]['odometro'] = 0;
					}
				}
				$anterior = $id;
			}
		}
		return $arrHistorico;
	}
	
	function getObtenerHistorico($idMovil, $fechaDesde, $fechaHasta, $tipo = false){
		global $lang;
		##-- validar movil --##
		include "includes/caja_negra.php";
		caja_negra($idMovil,'moviles',1,$this->objSQL);
		##-- --##
			
		$desde = date('Y-m-d H:i:s',strtotime($fechaDesde.' 00:00:00'));
		$hasta = date('Y-m-d H:i:s',strtotime($fechaHasta.' 23:59:59'));
			
		switch ($tipo){
			case 'celular':
				$this->tipoMovil = 'celular';
			break;
			case 'token':
				$this->tipoMovil = 'token';
			break;
			/*case 'satelital':
				$objHistorico->tipoMovil = 'satelite';
			break;*/
			case 'vehiculo'://--camiones--//
			case 'auto':
			case 'caja':
			case 'semi':
				$this->tipoMovil = 'vehiculo';
			break;
		}
				
		$arrHistorico = $this->llenarTablaTemporal($desde, $hasta, $idMovil, $_SESSION["idUsuario"]);
		if(!is_array($arrHistorico)){//-- Error 408: Supero tiempo de espera --//
			return $arrHistorico;
		}
		
		## Remplazo el nombre del evento para permiso "Historico - Reducido" ##
		if(tienePerfil(16)){
			foreach($arrHistorico as $k => $item){
				if($item['idEvento'] != 14){//-- Si es distinto a "Egreso De" lo piso x "Estuvo en"
					$arrHistorico[$k]['evento'] = $lang->system->estuvo_en.': ';
				}
			}
		}
		##-- --##
			
		if($this->tipoMovil == 'celular'){//-- Si es celular limpio picos y saltos --//
			$resp['eliminados'] = 9999;
			$resp['arrHistorico'] = $arrHistorico;
			while($resp['eliminados'] > 0){
				$resp = $this->limpiarSaltosPtos($resp['arrHistorico']);
			}
			$arrHistorico = $resp['arrHistorico'];
			$arrHistorico = $this->limpiarPicosPtos($arrHistorico);
		}
		
		$arrHistorico = $this->agruparHistorico($arrHistorico, $desde, $hasta);
		$arrHistorico = $this->historicoVista($arrHistorico);
			
		// Si no hay rumbo lo calculamos segun los puntos
		if (is_array($arrHistorico)){
			$todosRumboCero = true;
				/*if($tipo != 'celular'){
					foreach ($arrHistorico as $arr){
						if ($arr[11] > 0) {
							$todosRumboCero = false;
							break;
						}
					}
				}*/
			if($todosRumboCero === true){
				calcularRumbo2($arrHistorico);
			}
		}
		
		$arrHistorico = $this->historicoDistancia($arrHistorico);
		return $arrHistorico;		
	}
}
