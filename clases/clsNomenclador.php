<?php

class Nomenclador {
	var $objSQLServer;

	function Nomenclador($objSQLServer) {
		$this->objSQL = $objSQLServer;
		return TRUE;
	}

	function obtenerNomenclados($lat, $lon, $idMovil = 0) {
		$m = isset($_GET['idMovil']) ? $_GET['idMovil'] : 0;
		$strCalle = '';
		
		if (empty($strCalle)) {
			$conNivel = tienePerfil(16)?1:0;
			$query = "SELECT dbo.geoCodificarUnico($lat, $lon, $conNivel)";
			$res = $this->objSQL->dbQuery($query);
			$result	= $this->objSQL->dbGetRow($res, 0, 2);
			if($result){
				//$strpos = strpos(trim($result[0]),'Brasil');
				//|| (trim($result[0]) != 'En , ,' && $strpos)
				//|| (strpos(trim($result[0]),'Cerca') && $strpos)
				
				##-- Si no encuentro la referencia seleccionada, busco x Geo-Google-Api --##
				if(trim($result[0]) == 'En , ,'	|| empty($result[0])){
					/*
					$google = json_decode($this->nomenclarGoogle($lat, $lon, true));	
					if($google->status == 'OK'){
						$result = $this->objSQL->dbQuery($query);
						$result	= $this->objSQL->dbGetRow($result, 0, 2);
					}
					*/
					
					$osm = json_decode($this->nomenclarOpenStreetMaps_LatLng($lat, $lon, true));	
					if($osm->status == 'OK'){
						$result = $this->objSQL->dbQuery($query);
						$result	= $this->objSQL->dbGetRow($result, 0, 2);
						$this->statusNomenclado('nomenclar_openstreetmap');
					}
					/*else{
						$this->statusNomenclado('nomenclar_google');	
					}*/
				}
				##-- --##
				$retorno 	= ucwords(strtolower(($result[0])));
				$retorno 	= $strCalle . $retorno;
				return encode($retorno);
			}
		} else {
			return ' '.encode($strCalle);
		}
		return false;
	}
	
	function obtenerNomencladosGeocercas($idReferencia) {
		if($idReferencia > 0) {
			$strSQL = " SELECT re_nombre FROM tbl_referencias WITH(NOLOCK) ";
			$strSQL.= " WHERE re_id = ".(int)$idReferencia;
			$res = $this->objSQL->dbQuery($strSQL);
			$result = $this->objSQL->dbGetRow($res, 0, 2);
			return $result;
		}
		return false;
	}
	
	function nomenclarGoogle($lat, $lng, $alta = false) {
		error_reporting(0);
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&sensor=false&language=ES';
		$file = file_get_contents($url);
		$json = json_decode($file);
		$arrDir = array();
		$arrPoints = array();
		$arrAux = array();
		if($json->status == 'OK'){
				
				foreach($json->results[0]->address_components as $item){
					if($item->types[0] == 'route'){
						$arrDir['arcs_name'] = $item->long_name;	
					}
					elseif($item->types[0] == 'locality'){
						$arrDir['arcs_locality'] = $item->long_name;	
					}
					elseif($item->types[0] == 'administrative_area_level_1'){
						$arrDir['arcs_province'] = $item->long_name;	
					}
					elseif($item->types[0] == 'administrative_area_level_2'){
						$arrDir['arcs_zone'] = $item->long_name;	
					}
					elseif($item->types[0] == 'country'){
						$arrDir['arcs_country'] = strtoupper($item->long_name);	
					}
					##-- VALORES AUX --##
					elseif($item->types[0] == 'street_number'){
						$arrAux['altura'] = $item->long_name;	
					}
					elseif($item->types[0] == 'neighborhood'){
						$arrAux['barrio'] = $item->long_name;	
					}
				}
			
			if(!empty($arrDir['arcs_zone'])){
				$arrDir['arcs_zone'].= (!empty($arrAux['barrio']))?', '.$arrAux['barrio']:"";}
			else{
				$arrDir['arcs_zone'] = $arrAux['barrio'];}	
			
			$arrDir['arcs_type_id'] = 1;
			$arrDir['arcs_number_id'] = 1;
			$arrDir['arcs_userId'] = $_SESSION['idUsuario'];
			$arrDir['arcs_direction'] = $json->results[0]->formatted_address;
			
			if(!empty($arrAux['altura'])){
				$altura = explode('-',$arrAux['altura']);}
			if((int)$altura[0] && (int)$altura[1]){
				$arrDir['arcs_aid'] = $altura[0];
				$arrDir['arcs_afd'] = $altura[1];
				$arrDir['arcs_aii'] = $altura[0] + 1;
				$arrDir['arcs_afi'] = $altura[1] + 1;	
			}
			else{
				$arrDir['arcs_aid'] = $arrDir['arcs_afd'] = $arrDir['arcs_aii'] = $arrDir['arcs_afi'] = (int)$arrAux['altura'];	
			}
			
			$cant = 0;
			if($json->results[0]->geometry->bounds){
				foreach($json->results[0]->geometry->bounds as $point){
					array_push($arrPoints, array('point_x' => $point->lng,'point_y' => $point->lat, 'point_ordinal' => $cant));
					$cant++;
				}
			}
			else{
				array_push($arrPoints, array('point_x' => $json->results[0]->geometry->location->lng,'point_y' => $json->results[0]->geometry->location->lat, 'point_ordinal' => $cant));
				$cant++;
			}
			
			$arrDir['arcs_amountPoints'] = $cant + 1;
			$result['arrDir'] = $arrDir;
			$result['arrPoint'] = $arrPoints;
			
			if($alta == true){
				$this->setARCS($arrDir, $arrPoints, $lat, $lng);
			}
		
		}//-- Fin Ok --//
		
		
		$result['status'] = $json->status;
		return json_encode($result);
	}
	
	function nomenclarOpenStreetMaps_LatLng($lat, $lng, $alta = false) {
		error_reporting(0);
		$url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat='.$lat.'&lon='.$lng;
		
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,$url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Localizar-T');
		curl_setopt ($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
	
		$json = json_decode(curl_exec($curl_handle));
		curl_close($curl_handle);
		
		$arrDir = array();
		$arrPoints = array();
		$arrAux = array();
		$status = 'ERROR';
		if(!empty($json->address->road)){
			$status = 'OK';		
			//postcode	
			$arrDir['arcs_name'] = $json->address->road;	
			$arrDir['arcs_locality'] = $json->address->city;	
			$arrDir['arcs_province'] = $json->address->state;
			$arrDir['arcs_country'] = strtoupper($json->address->country);	
			$arrDir['arcs_zone'] = $json->address->state_district;
				
			##-- VALORES AUX --##
			$arrAux['barrio'] = $json->address->suburb;
			$arrAux['altura'] = $json->address->house_number;
				
			if(!empty($arrDir['arcs_zone'])){
				$arrDir['arcs_zone'].= (!empty($arrAux['barrio']))?', '.$arrAux['barrio']:"";}
			else{
				$arrDir['arcs_zone'] = $arrAux['barrio'];}	
			
			$arrDir['arcs_type_id'] = 1;
			$arrDir['arcs_number_id'] = 1;
			$arrDir['arcs_userId'] = $_SESSION['idUsuario'];
			$arrDir['arcs_direction'] = $json->display_name;
			
			if(!empty($arrAux['altura'])){
				$altura = explode('-',$arrAux['altura']);
			}
			if((int)$altura[0] && (int)$altura[1]){
				$arrDir['arcs_aid'] = $altura[0];
				$arrDir['arcs_afd'] = $altura[1];
				$arrDir['arcs_aii'] = $altura[0] + 1;
				$arrDir['arcs_afi'] = $altura[1] + 1;	
			}
			else{
				$arrDir['arcs_aid'] = $arrDir['arcs_afd'] = $arrDir['arcs_aii'] = $arrDir['arcs_afi'] = (int)$arrAux['altura'];	
			}
			
			$cant = 0;
			array_push($arrPoints, array('point_x' => $json->lon,'point_y' => $json->lat, 'point_ordinal' => $cant));
			$cant++;
		
			$arrDir['arcs_amountPoints'] = $cant + 1;
			$result['arrDir'] = $arrDir;
			$result['arrPoint'] = $arrPoints;
			
			if($alta == true){
				$this->setARCS($arrDir, $arrPoints, $lat, $lng);
			}
		
		}//-- Fin Ok --//
		
		$result['status'] = $status;
		return json_encode($result);
	}
	
	function nomenclarOpenStreetMaps_Street($street){
		$arrPoints = array();
		$arrPoints['status'] = 'Error';
		
		$street = str_replace(',','',str_replace(' ','+',$street));
		$url = 'https://nominatim.openstreetmap.org/search.php?format=json&q='.$street;
				
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,$url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Localizar-T');
		curl_setopt ($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
			
		$json = json_decode(curl_exec($curl_handle));
		curl_close($curl_handle);
		if($json[0]){
			$arrPoints['status'] = 'Ok';
			$arrPoints['lat'] = $json[0]->lat;
			$arrPoints['lon'] = $json[0]->lon;
			$arrPoints['street'] = $json[0]->display_name;
		}
		
		return json_encode($arrPoints);
	}
	
	function setARCS($arrDir, $arrPoints, $lat, $lng){
		##-- VALIDO SI NO EXISTE LA REFERENCIA EN ARCS ANTES DE GENERARLA --//
		$sql = 'SELECT poin_arcs_id ';
		$count = 0;
		$objArcs = array();
		foreach($arrPoints as $item){
			if($count > 0){
				$sql.= " AND poin_arcs_id = ( SELECT TOP 1 poin_arcs_id";	
			}
			$sql.= " FROM Arcos.dbo.POINTS WITH(NOLOCK) WHERE poin_x = '".$item['point_x']."' AND poin_y = '".$item['point_y']."' ";
			if($count > 0){
				$sql.= " ) ";	
			}
			$count++;	
		}
		if($count){
			$result = $this->objSQL->dbQuery($sql);
			$objArcs = $this->objSQL->dbGetRow($result,0, 3);
		}
		##-- --##
		if($objArcs['poin_arcs_id']){
			$point_arcs_id = (int)$objArcs['poin_arcs_id'];
			$sql = " SELECT COUNT(*) AS cant FROM Arcos.dbo.POINTS WITH(NOLOCK) WHERE poin_arcs_id = ".$point_arcs_id;
			$result = $this->objSQL->dbQuery($sql);
			$objRow = $this->objSQL->dbGetRow($result,0, 3);
			
			##-- Inicio. ALTA EN POINT --##}
				$sql = " INSERT INTO Arcos.dbo.POINTS(poin_arcs_id, poin_x, poin_y, poin_ordinal) ";
				$sql.= " VALUES (".$point_arcs_id.", '".str_replace(',','.',$lng)."','".str_replace(',','.',$lat)."','".$objRow['cant']."') ";
				$this->objSQL->dbQuery($sql);
			##-- fin. ALTA EN POINT --##
		}
		else{
			##-- Inicio. ALTA EN ARCS --##
			$sql = " INSERT INTO Arcos.dbo.ARCS(arcs_amountPoints, arcs_userId, arcs_country, arcs_province, arcs_zone, arcs_locality, arcs_name, arcs_type_id, arcs_aid, arcs_afd, arcs_aii, arcs_afi, arcs_number_id) ";
			$sql.= "VALUES (".(int)$arrDir['arcs_amountPoints'].",".(int)$arrDir['arcs_userId'].",'".utf8_decode($arrDir['arcs_country'])."','".utf8_decode($arrDir['arcs_province'])."','".utf8_decode($arrDir['arcs_zone'])."','".utf8_decode($arrDir['arcs_locality'])."','".utf8_decode($arrDir['arcs_name'])."',".(int)$arrDir['arcs_type_id']." ,".(int)$arrDir['arcs_aid'].",".(int)$arrDir['arcs_afd']." ,".(int)$arrDir['arcs_aii']." ,".(int)$arrDir['arcs_afi']." ,".(int)$arrDir['arcs_number_id'].")";
			if($this->objSQL->dbQuery($sql)){
				$count = -1;
				$lastID = $this->objSQL->dbLastInsertId();
				foreach($arrPoints as $item){
					$sql = " INSERT INTO Arcos.dbo.POINTS(poin_arcs_id, poin_x, poin_y, poin_ordinal) ";
					$sql.= " VALUES (".$lastID.", '".$item['point_x']."','".$item['point_y']."','".$item['point_ordinal']."') ";
					$this->objSQL->dbQuery($sql);
					$count = $item['point_ordinal'];
				}
					
				$count = $count + 1;
				$sql = " INSERT INTO Arcos.dbo.POINTS(poin_arcs_id, poin_x, poin_y, poin_ordinal) ";
				$sql.= " VALUES (".$lastID.", '".str_replace(',','.',$lng)."','".str_replace(',','.',$lat)."','".$count."') ";
				$this->objSQL->dbQuery($sql);
			}
			##-- Fin. ALTA EN ARCS --##	
		}
	}
	
	function statusNomenclado($ssClave){
		$sql = " SELECT ss_fecha, ss_cantidad FROM tbl_status_sistema WITH(NOLOCK) WHERE ss_clave = '".$ssClave."' ";
		$result = $this->objSQL->dbQuery($sql);
		$objRow = $this->objSQL->dbGetRow($result,0, 3);
		
		$cant = 1;
		if(date('Y-m-d') == date('Y-m-d',strtotime($objRow['ss_fecha']))){
			$cant = $objRow['ss_cantidad'] + 1;
		}
			
		$sql = " UPDATE tbl_status_sistema SET ss_fecha = CURRENT_TIMESTAMP, ss_cantidad = ".(int)$cant." WHERE ss_clave = '".$ssClave."'";
		$this->objSQL->dbQuery($sql);
	}

}
?>
