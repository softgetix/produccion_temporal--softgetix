<? 
function escapear_array($array){
	if(isset($array)){
		array_walk($array, 
			function(&$v) {
				if(is_string($v)){
						$v = escapear_string($v);
				}
				elseif(is_array($v)){
					escapear_array($v);
				}
			}
		);
		return $array;
	}
	else{
		return false;	
	}
}

function escapear_string($strVar){
	$strVar = trim(decode($strVar));
	
   $strVar = str_replace(">", "&gt;", $strVar); 
   $strVar = str_replace("<", "&lt;", $strVar); 
   $strVar = str_replace("`", "&#039", $strVar); 
   $strVar = str_replace("'", "&#039", $strVar);
   $strVar = str_replace("\"", "&quot;", $strVar);
   $strVar = str_replace("--", "", $strVar);
   
   $strVar = strip_tags($strVar);
   return $strVar;
}

function distancia($lat1, $long1, $lat2, $long2) {
    $degtorad = 0.01745329;
    $radtodeg = 57.29577951;
    $dlong = ($long1 - $long2);
    $dvalue = (sin($lat1 * $degtorad) * sin($lat2 * $degtorad)) + (cos($lat1 * $degtorad) * cos($lat2 * $degtorad) * cos($dlong * $degtorad));
    $dd = acos($dvalue) * $radtodeg;
    $miles = ($dd * 69.16);
    $km = ($dd * 111.302);
    return $km;
}

function getFechaServer($format = NULL){
	global $objSQLServer;
	global $rel;
	$format = $format?$format:'d-m-Y H:i:s';
	/*if(!$objSQLServer){
		require_once $rel.'includes/conn.php'; //descomente porq en allinonerastreo tiene conflicto.
	}*/
	$sql = " SELECT server FROM zonaHoraria(NULL,".(int)$_SESSION['idUsuario'].") ";
	$res = $objSQLServer->dbQuery($sql);
	$rs = $objSQLServer->dbGetRow($res,0,3);
	return date($format,strtotime($rs['server'].' hour',strtotime(date('Y-m-d H:i:s'))));
}

function setLogTXT($txt, $prefijoArch = NULL){
	//-- --//
	$dir = 'log/'.date('mY');
	if(!file_exists($dir)){
		mkdir($dir, 0777, true);
	}
	//-- --//
	$prefijoArch = $prefijoArch?'_'.$prefijoArch:'';
	$arch   = $dir.'/'.date("d").date("m").date("Y").$prefijoArch.'.txt';
	$ip = $_SERVER['REMOTE_ADDR'];
	$fechaLOG=date("H:i:s");
	//$log = file_exists($arch)?fopen($arch,"a"):fopen($arch,"w");
	
	if(file_exists($arch)){
		$log = fopen($arch,"a");
	}
	else{
		$log = fopen($arch,"w");
		 @chown($arch, 'root');	
	}
	fwrite($log,$fechaLOG." (".$ip.") ".$txt."\r\n");
	fclose($log);
}

function encode($arr){
	return utf8_encode($arr);//Mi pc
}

function decode($arr){ // para visualizar de la BD
	 return utf8_decode($arr);//Mi pc
}

function formatearDistancia($distancia, $lang = 'es') {
    $distancia = (float)$distancia;
	
	$returnValue = NULL;
	switch($lang){
		case 'en':			
			$distancia = ($distancia/1.60932);
			//$pref = ' Miles';
			$pref = NULL;
		break;	
		default:
			//$pref = ' Km';
			$pref = NULL;
		break;
	}
	
	$returnValue = (($distancia > 0 && $distancia < 1)?number_format($distancia,2,',',''):round($distancia)).$pref;
	return $returnValue;
}

function formatearFecha($fecha, $lang, $formato = NULL){
	
	//-- $formato = 'date/time/hour/pref_hour';
	
	if(empty($fecha)){
		return NULL;	
	}
	
	$fecha = date('d-m-Y H:i',strtotime($fecha)); //Formato Ingles siempre tiene q venir con barra (/) y el latinoamericano con guion (-)
	
    $returnValue = '--';
	$fecha = str_replace('/','-',$fecha);
	
	switch($lang){
		case 'en':
			$date = 'm/d/Y';
			$time = 'h:i';
			$hour = 'h';
			$pref = ' '.date('A',strtotime($fecha));
		break;	
		default:
			$date = 'd-m-Y';
			$time = 'H:i';
			$hour = 'H';
			$pref = '';
		break;
	}
	
	switch($formato){
		case 'date':
			$returnValue = date($date, strtotime($fecha));
		break;
		case 'time':
			$returnValue = date($time, strtotime($fecha)).$pref;
		break;
		case 'hour':
			$returnValue = date($hour, strtotime($fecha));
		break;
		case 'pref_hour':
			$returnValue = $pref;
		break;
		default:
			$returnValue = date($date.' '.$time, strtotime($fecha)).$pref;
		break;
	}
	
	return $returnValue;
}
?>