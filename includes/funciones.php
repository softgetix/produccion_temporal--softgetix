<?php

function rompioSession($get, $error){
	$cliente = $_SESSION['DIRCONFIG'];
	session_destroy();
	$method = isset($get['method']) ? $get['method'] : false;

	if($method == 'ajax_json') {
		$out['status'] = 3;
		$out['msg'] = 'session expiro';
		$_SESSION['truncate_session'] = true;
		echo json_encode($out);
		die();
	}
	?>
	<html>
		<body>
			<script type="text/javascript">
				var form = document.createElement('form');
				form.method = 'post';
				form.action = '/<?=$cliente?>'

				var input;
				input = document.createElement('input');
				input.setAttribute('name', 'referencia_error');
				input.setAttribute('value', '<?=$error?>');
				input.setAttribute('type', 'hidden');
			    form.appendChild(input);

				document.body.appendChild(form);
				form.submit();
	   		</script>
		</body>
   	</html>
	<?php
	die();	
}

//LIBRERIA DE FUNCIONES
function calcularRumbo($curso){
    $retorno = '';
    if ($curso > 337.5 || $curso <= 22.5){
        $retorno = 'N';
    }
	elseif($curso > 22.5 && $curso <= 67.5){
        $retorno = 'NE';
    }
	elseif($curso > 67.5 && $curso <= 112.5){
        $retorno = 'E';
    }
	elseif($curso > 112.5 && $curso <= 157.5){
        $retorno = 'SE';
    }
	elseif($curso > 157.5 && $curso <= 202.5){
        $retorno = 'S';
    }
	elseif($curso > 202.5 && $curso <= 247.5){
		$retorno = 'SO';
		//$retorno = 'SW';
	}
	elseif($curso > 247.5 && $curso <= 292.5){
    	$retorno = 'O';
        //$retorno = 'W';
	}
	elseif($curso > 292.5 && $curso <= 337.5){
    	$retorno = 'NO';
		//$retorno = 'NW';
    }
    return $retorno;	
}

function calcularRumbo2(&$arr){
    if (!is_array($arr)) {
        return;
    }
    $actual = $anterior = null;
    foreach ($arr as $id => $arr2) {
        if (isset($arr[$id]['eliminado']) && $arr[$id]['eliminado'] == true) {
            // No hace nada.
        }
		else{
            $anterior = $actual;
            $actual = $id;
            if ($actual !== null && $anterior !== null) {
                $virtual['lat'] = $arr[$anterior]['lat'];
                $virtual['lon'] = $arr[$actual]['lon'];

                $angles = calcularAngulos($arr[$anterior], $arr[$actual], $virtual);
                if (is_nan($angles['alfa'])){
                    $angles['alfa'] = 0;
                    $angles['beta'] = 0;
                    $angles['gamma'] = 0;
                }
                $arr[$anterior]['rumbo_norte'] = $angles['alfa'];
                $abajo = $izquieda = null;

                if ($arr[$actual]['lat'] > $arr[$anterior]['lat']) {
                    $abajo = false;
                }
				else {
                    $abajo = true;
                }

                if ($arr[$actual]['lon'] < $arr[$anterior]['lon']) {
                    $izquierda = true;
                }
				else {
                    $izquierda = false;
                }

                if ($abajo == true) {
                    if ($izquierda == true) {
                        $arr[$anterior]['rumbo'] = 270 - $arr[$anterior]['rumbo_norte'];
                    } else if ($izquieda == false) {
                        $arr[$anterior]['rumbo'] = 90 + $arr[$anterior]['rumbo_norte'];
                    } else {
                    }
                } else if ($abajo === false) {
                    if ($izquierda == true) {
                        $arr[$anterior]['rumbo'] = 270 + $arr[$anterior]['rumbo_norte'];
                    } else if ($izquieda == false) {
                        $arr[$anterior]['rumbo'] = 90 - $arr[$anterior]['rumbo_norte'];
                    } else {
                    }
                } 
				
                if ($arr[$anterior]['rumbo']) {
                    $rumbo = calcularRumbo($arr[$anterior]['rumbo'], 1);
                    $arr[$anterior]['flecha'] = $rumbo;
                    $arr[$anterior]['curso'] = $arr[$anterior]['rumbo'];
                }
            }
        }
    }
    //return $arr;
}

function redireccionarPagina($strURL, $blnDie = false, $request_uri = true) {
    $url = '';
	if($request_uri == true){
		$url = $_SERVER['REQUEST_URI'];
	}
	?>
    <script type="text/javascript">
        window.location = "<?=$url.'/'.$strURL?>";
    </script>
    <?php
	exit;
}

//Le pasas una fecha sacada de un formulario en el formato DD/MM/AAAA
//y te la devuelve en el formato YYYY-MM-DD para la base de datos.
function dateToDataBase($datFecha) {
    if (preg_match("|([0-9]{2})\/([0-9]{2})\/([0-9]{4})|iUsm", $datFecha)) {
        $datFecha = split_fecha($datFecha, 0);
        $datFecha = format_date($datFecha, "YYYYMMDD", "/", "-");
    }
    if (preg_match("|([0-9]{4})\/([0-9]{2})\/([0-9]{2}) ([0-9]{2}):([0-9]{2})|iUsm", $datFecha, $arr)) {
        $datFecha = str_replace("/", "-", $datFecha);
    }
    return $datFecha;
}

//Le pasas una fecha de la base (en el formato que sea) DDMMYYY TIME y te devuelve
//s�lo la fecha o s�lo la hora, seg�n el par�metro intReturn
function split_fecha($strValue, $intReturn) {
    if ($strValue) {
        $arrFecha = explode(" ", $strValue);
        if (is_array($arrFecha)) {
            if (($intReturn != 0) || ($intReturn != 1)) {
                $intReturn = 0;
            }
            return $arrFecha[$intReturn];
        }
    }
	return "";
}

//Le pasas una fecha en (DD/MM/AAAA) y un formato y te devuelve la fecha formateada
function format_date($strValue, $strFormat, $strSeparator, $strSeparatorReturn) {

    if (!($strValue && $strFormat && $strSeparator && $strSeparatorReturn)) {
        return "";
    }

    //Paso el valor que me pasan a un array para evaluarlo con checkDate
    $arrFecha = explode($strSeparator, $strValue);

    if (!is_array($arrFecha)) {
        return "";
    }
	
    switch ($strFormat) {
        case "YYYYMMDD":
            return $arrFecha[2] . $strSeparatorReturn . $arrFecha[1] . $strSeparatorReturn . $arrFecha[0];
            break;
        case "DDMMYYYY":
            return $arrFecha[0] . $strSeparatorReturn . $arrFecha[1] . $strSeparatorReturn . $arrFecha[2];
            break;
    }
	return false;
}

//Remplaza el formato dd/mm/YYYY HH:ii
// por formato YYYY-mm-dd HH:ii:ss
//solucion abmViajesControlador almacenamiento en controlador de variables.
function dateJqueryPhp($datFechaHora) {
    $datFecha = explode('/', $datFechaHora);
    $datSepar = explode(' ', $datFecha[2]);
    $salida = $datSepar[0] . '-' . $datFecha[1] . '-' . $datFecha[0] . ' ' . $datSepar[1] . ':00';
    return $salida;
}


function ObtenerNavegador($user_agent){
 
    $navegadores = array(        
		'Mobile Login - Android' => '((Android)|(android))',
		'Mobile Login - BlackBerry' => '(BlackBerry)',
		'Mobile Login - iPhone' => '(iPhone)',
		'Mobile Login - iPad' => '(iPad)',
		'Mobile Login - iPod' => '(iPod)',
		'Mobile Login - Unknown' => '((Mobile)|(mobile))',
		'Web browser - Mozilla Firefox'=> '((Firebird)|(Firefox))',
		'Web browser - Opera' => '(OPR)',
		'Web browser - Google Chrome' => '(Chrome)',
        'Web browser - Safari' => '(Safari)',		
		'Web browser - Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
		'Web browser - Internet Explorer 7' => '(MSIE 7\.[0-9]+)',
		'Web browser - Internet Explorer 8' => '(MSIE 8\.[0-9]+)',
		'Web browser - Internet Explorer 9' => '(MSIE 9\.[0-9]+)',
		'Web browser - Internet Explorer 10' => '(MSIE 10\.[0-9]+)',
		'Web browser - Internet Explorer 11' => '(MSIE 11\.[0-9]+)',
		'Web browser - Internet Explorer 12' => '(MSIE 12\.[0-9]+)',
		'Web browser - Internet Explorer 13' => '(MSIE 13\.[0-9]+)',
		'Web browser - Internet Explorer 14' => '(MSIE 14\.[0-9]+)',
		'Web browser - Internet Explorer 15' => '(MSIE 15\.[0-9]+)',
		'Web browser - Internet Explorer 16' => '(MSIE 16\.[0-9]+)',
		'Web browser - Internet Explorer 17' => '(MSIE 17\.[0-9]+)',
		'Web browser - Internet Explorer 18' => '(MSIE 18\.[0-9]+)',
		 //'Internet Explorer' => '(MSIE)',        
        'Web browser - Netscape' => '((Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79))',
        'Web browser - Konqueror'=>'(Konqueror)',
		'Web browser - Lynx' => '(Lynx)',
		'Web browser - Galeon' => '(Galeon)',
		'Web browser - MyIE'=>'(MyIE)',		
         //'Mozilla'=>'(Gecko)',
	   
   );
   
    foreach($navegadores as $navegador=>$pattern){
        if(preg_match($pattern, $user_agent))
        return $navegador;
    }
	
   return "Desconocido";	
}


function obtenerDatosCombo($store, $tipo = 1, $isConsulta = false) {
    global $objSQLServer;
    require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$result = $objInterfazGenerica->obtenerDatosCombo($store, $tipo, $isConsulta);
	foreach($result as $k => $arrItem){
		foreach($arrItem as $a => $item){
			$result[$k][$a] = encode($item); // con encode muestra bien para el combo de Atente en abmEquipos
		}
	}
	return $result;
}

function formatearVelocidad($velocidad){
    $velocidad = (float)$velocidad;
	$velocidad  = ($velocidad > 0)?$velocidad:0;
   
   
   	$returnValue = NULL;
	switch($_SESSION['language']){
		case 'en':			
			$velocidad = ($velocidad/1.60932);
			$pref = ' Mph';
		break;	
		default:
			$pref = ' Km/h';
		break;
	}
	
	$returnValue = (($velocidad > 0 && $velocidad < 1)?number_format($velocidad,2,',',''):round($velocidad)).$pref;
		
	return $returnValue;
}

function formatearDistancia($distancia) {
    $distancia = (float)$distancia;
	
	$returnValue = NULL;
	switch($_SESSION['language']){
		case 'en':			
			$distancia = ($distancia/1.60932);
			$pref = ' Miles';
		break;	
		default:
			$pref = ' Km';
		break;
	}
	
	//$returnValue = (($distancia > 0 && $distancia < 1)?number_format($distancia,2,',',''):round($distancia)).$pref;
	$returnValue = (($distancia > 0 && $distancia < 1)?number_format($distancia,2,',',''):number_format($distancia,1,',','')).$pref;
	return $returnValue;
}

function formatearFecha($fecha, $formato = NULL){
	//-- $formato = 'short/date/time/hour/pref_hour/seconds';
	
	if(empty($fecha)){
		return NULL;	
	}
	elseif(!strtotime($fecha)){
		return NULL;
	}
	
	$fecha = date('d-m-Y H:i:s',strtotime($fecha)); //Formato Ingles siempre tiene q venir con barra (/) y el latinoamericano con guion (-)
	
    global $lang;
	$returnValue = '--';
	$fecha = str_replace('/','-',$fecha);
	
	switch($_SESSION['language']){
		case 'en':
			$date = 'm/d/Y';
			$second = 'h:i:s';
			$time = 'h:i';
			$hour = 'h';
			$pref = ' '.date('A',strtotime($fecha));
		break;	
		default:
			$date = 'd-m-Y';
			$second = 'H:i:s';
			$time = 'H:i';
			$hour = 'H';
			$pref = '';
		break;
	}
	
	switch($formato){
		case 'short':
			$hoy = getFechaServer();	
			$ayer = date('Y-m-d', strtotime('-1 day',strtotime($hoy)));
			$hoy = date('Y-m-d', strtotime($hoy));
					
			if(date('Y-m-d',strtotime($fecha)) == $hoy){
				$returnValue = $lang->system->hoy.', '.date($time,strtotime($fecha)).$pref;
			} 
			elseif(date('Y-m-d',strtotime($fecha)) == $ayer){
				$returnValue = $lang->system->ayer.', '.date($time,strtotime($fecha)).$pref;
			}
			else{
				$returnValue = date($date.' '.$time, strtotime($fecha)).$pref;
			}	
		break;
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
		case 'seconds':
			$returnValue = date($date.' '.$second, strtotime($fecha)).$pref;
		break;
		default:
			$returnValue = date($date.' '.$time, strtotime($fecha)).$pref;
		break;
	}
	
	return $returnValue;
}

function getRealIP() {
   $realip = false;
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		if(validarIP($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];	
		}
    }
	
	if(!$realip){
		if(isset($_SERVER['HTTP_VIA'])){
			if(validarIP($_SERVER['HTTP_VIA'])){
				$realip = $_SERVER['HTTP_VIA'];	
			}
		}
	}
	
	if(!$realip){
		if(isset($_SERVER['HTTP_CLIENT_IP'])){
			if(validarIP($_SERVER['HTTP_CLIENT_IP'])){
				$realip = $_SERVER['HTTP_CLIENT_IP'];	
			}
		}
	}
	
	if(!$realip){
        $realip = $_SERVER["REMOTE_ADDR"];
    }
	
    return $realip;
}

function validarIP($ip){
	$arrIp = explode('.',$ip);
	if(is_numeric($arrIp[0]) && is_numeric($arrIp[1]) && is_numeric($arrIp[2]) && is_numeric($arrIp[3])){
		return true;
	}
	return false;
}


function sqlDateToUnixTimestamp($sqlDate){
    //DEVUELVE LA CANTIDAD DE SEGUNDOS DE LA FECHA QUE SE LE PASA
    //PARAMETROS: FECHA EN FORMATO DIA/MES/A�? HORA:MINUTO
    $day = substr($sqlDate, 0, 2);
    $month = substr($sqlDate, 3, 2);
    $year = substr($sqlDate, 6, 4);
    $hours = substr($sqlDate, 11, 2);
    $minutes = substr($sqlDate, 14, 2);

    $date = mktime($hours, $minutes, 0, $month, $day, $year);
    return $date;
}

if (!function_exists('json_encode')) {
    function json_encode($data, $options = '') {
        include_once('includes/json.php');
        $temp = new Services_JSON();
        return $temp->encode($data);
    }
}

function limpiarArray(&$arr) {
 	array_walk($arr, function(&$v) {
    	if (is_string($v))
        	$v = encode($v);
        elseif (is_array($v))
        	limpiarArray($v);
	});
}

function encode($arr){
	return utf8_encode($arr);//Mi pc
}

function decode($arr){ // para visualizar de la BD
	 return utf8_decode($arr);//Mi pc
}

function decodeArray($array){
	foreach($array as $k1 => $arrItemA){
		if(is_array($arrItemA)){
			//--//
			foreach($arrItemA as $k2 => $arrItemB){
				if(is_array($arrItemB)){}
				else{
					$array[$k1][$k2] = encode($arrItemB);	
				}
			}	
			//--//
		}
		else{
			$array[$k1] = encode($arrItemA);	
		}	
	}
	return $array;
}

function sanear_string($string, $sinEspacios = true){
	$string = trim($string);
 
    $string = str_replace(
        array('á', 'ä', 'à', 'Á', 'Ä', 'À', 'â', 'Â'),
        'a',
		$string
    );
 
    $string = str_replace(
        array('é', 'ë', 'è', 'É', 'Ë', 'È', 'ê', 'Ê'),
        'e',
		$string
    );
 
    $string = str_replace(
        array('í', 'ï', 'ì', 'Í', 'Ï', 'Ì', 'î', 'Î'),
        'i',
		$string
    );
 
    $string = str_replace(
        array('ó', 'ö', 'ò', 'Ó', 'Ö', 'Ò', 'õ', 'Õ'),
        'o',
		$string
    );
 
    $string = str_replace(
        array('ú', 'ü', 'ù', 'Ú', 'Ü', 'Ù', 'û', 'Û'),
        'u',
		$string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
 
    //Esta parte se encarga de eliminar cualquier caracter extra�o
   $string = str_replace(
        array("¿", "¡", "-", "~",'#', "@", "|", "!", '"', "$", "%", "&", "/","(",')','?',"'",'[', '^', '<code>', ']', '+', '}', '{', '>', '< ', ';', ',', ':','.'),
        '',
        $string
    );
	
	$string = $sinEspacios ? str_replace(' ','_',$string) : $string;
	
    return $string;
}

function datosCargados(&$arrCampos, &$arrValores) {
  	array_walk($arrValores, function(&$v){ 
   		$v = trim($v, "''");
    });
	$datosCargados[0] = array_combine($arrCampos, $arrValores);
    
	return $datosCargados;
}

function pr($var) {
    echo "<pre>";
    print_r($var);
    echo "</pre>";
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

function tiempo_minutos($minutos) {
    $segundos = $minutos * 60;
    $horas = floor($minutos / 60);
    $dias = floor($horas / 24);
    $minutos2 = $minutos % 60;
    $horas2 = $horas % 24;
    $segundos_2 = $segundos % 60 % 60 % 60;

    if ($minutos2 < 10)
        $minutos2 = '0' . $minutos2;
    if ($segundos_2 < 10)
        $segundos_2 = '0' . $segundos_2;

    if ($segundos < 60) { 
        //$resultado= round($segundos).' Segundos';
        $resultado = "-";
    } elseif ($segundos > 60 && $segundos < 3600) {
        $resultado = $minutos2 . 'm';
    } else if ($horas < 24) {
        $resultado = $horas . 'h ' . $minutos2 . 'm';
    } else {
        $resultado = $dias . 'd ' . $horas2 . 'h ' . $minutos2 . 'm';
    }
    return $resultado;
}

function calcular_estadia($desde, $hasta = null) {
    if ($hasta == null) {
        $hasta = time();
    }

    if (is_string($desde)) {
        $_desde = strtotime($desde); // Fecha formateada
    }

    if (is_numeric($desde)) {
        $_desde = $desde; // Timestamp
    }

    $mins = (int) ($hasta - $_desde) / 60;

    return tiempo_minutos($mins);
}

function calcularAngulos($p1, $p2, $p3) {
    $a = distancia($p1[9], $p1[10], $p2[9], $p2[10]);
    $b = distancia($p1[9], $p1[10], $p3[9], $p3[10]);
    $c = distancia($p2[9], $p2[10], $p3[9], $p3[10]);

    if ($a > 0 && $b > 0 && $c > 0) {
        $alfa = acos((pow($c, 2) - pow($b, 2) - pow($a, 2)) / (-2 * $b * $a));
        $beta = acos((pow($b, 2) - pow($c, 2) - pow($a, 2)) / (-2 * $c * $a));

        $alfa_r = rad2deg($alfa);
        $beta_r = rad2deg($beta);

        $angle = 180 - $alfa_r - $beta_r;
        $arr['alfa'] = $alfa_r;
        $arr['beta'] = $beta_r;
        $arr['gamma'] = $angle;
        return $arr;
    } else {
        return false;
    }
}

function selectHorarios($name, $tipo, $id = null, $default = false, $desactivado = false) {
    if ($id == null) {
        $id = $name;
    }
    
    if ($default !== false) {
        $mins_default = substr($default, -2);
        $hora_default = substr($default, 0, -2);
        if (strlen($hora_default) == 1) {
            $hora_default = "0".$hora_default;
        }
    }
    
	if($desactivado == true){
		$desactivado = ' disabled="true" ';
	}
	else{
		$desactivado = '';
	}
	
		
    $html = "<select id='{$id}' name='{$name}' ".$desactivado.">";
    for ($i = 0; $i < 24; $i++)
    {
        if ($i < 10) $i = "0" . $i; 
        if ($tipo == 'desde' && $i == 24) {
            
        } else {
            $html .= "<option value='{$i}:00:00'";
            if ($hora_default == $i && $mins_default == "00") {
                $html .= " selected";
            }
            $html .= ">{$i}:00</option>";
        }
        
        $html .= "<option value='{$i}:30:00'";
        if ($hora_default == $i && $mins_default == "30") {
            $html .= " selected";
        }                
        $html .= ">{$i}:30</option>";
        

        if ($i == 23 && $tipo == 'hasta') {
            $html .= "<option value='{$i}:59:59'";
            if ($hora_default == 23 && $mins_default == "59") {
                $html .= " selected";
            }
            $html .= ">{$i}:59</option>";
        }        
    }
    $html .= "</select>";
    return $html;
}

function millitia_time($hhmmss){
    $arrHora = explode(':', $hhmmss);
    
    $hh = $arrHora[0];
    $mm = $arrHora[1];
    
    return $hh.$mm;
}

function extension_archivo($archivo) {
	$partes = explode("/", $archivo);
	if (count($partes) == 1) $partes = explode("\\", $partes[0]);
	$archivo = $partes[count($partes) - 1];
	$pos_punto = strrpos($archivo, ".");
	if ($pos_punto === false) {
		return "";
	}
	else {
		return strtolower(trim(substr($archivo, $pos_punto + 1)));
	}
}

function getEstadoMotor($arr){
	$estadoMotor = substr($arr['byteEncendido'], $arr['mo_bit_motor'], 1);
	if ($estadoMotor == $arr['mo_motor_encendido']){ 
		return true;
	}
	else {
		return false;
	}	
}

function getBinary($exac){
	$bit1 = (string)decbin(hexdec(substr($exac,0,1)));
	$len_bit = strlen($bit1);
	if($len_bit < 4){
		for($b=1; $b<=(4 - $len_bit); $b++){
			$bit1 = '0'.$bit1;	
		}
	}
	
	$bit2 = (string)decbin(hexdec(substr($exac,1,1)));
	$len_bit = strlen($bit2);
	if($len_bit < 4){
		for($b=1; $b<=(4 - $len_bit); $b++){
			$bit2 = '0'.$bit2;	
		}
	}	
	
	$binary = $bit1.$bit2;
	return $binary; 
}

function setLog($arch, $txt){
	$fechaLOG=date("d-m-Y H:i:s");
	$log = file_exists($arch) ? fopen($arch,"a+") : fopen($arch,"w");
	fwrite($log,$fechaLOG." - ".$txt."\r\n");
	fclose($log);	
}

//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////FUNCIONES DE DETECCION DE ATAQUES JS////////////////////////////////
function validarModificar($mPermitido,$objSQLServer){
	require_once 'clases/clsSeguridad.php';
	$objSeguridad = new Seguridad($objSQLServer);
	if(is_array($mPermitido) || $mPermitido == 1){
		 //Es un array, quiere decir que tiene permisos. No hago nada y dejo que continue con la ejecuci�n del script;
	}
	else{
		//Me est�n atacando!!!!
		$msjError="<div style='padding:15px;  border:1px solid gray; width:400px; background-color:black; color:gray; font-family:verdana;'><img src='imagenes/logol2.png'border=0><br><br>Error: Acceso denegado</div>";
		$dia=date('d-m-Y');
		$hora=date('H:i:s');
		$ip=getRealIP();
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$includes=get_included_files();
		$impludes=implode("-",$includes);
		
		$errorTxt = " USUARIO: ".$_SESSION['nombreUsuario'];
		$errorTxt.= " - PERFIL: ".$_SESSION['idPerfil'];
		$errorTxt.= " - IP: ".$ip;
		$errorTxt.= " - APARTADO:".$_GET['c']."\r\n";
		$archLog='security_log_'.$dia.'.txt';	
		$campoExpira='us_expira';	
		$idUsuario=$_SESSION['idUsuario'];
		$nombre_usuario=$_SESSION['nombreUsuario'];  
		require_once('clases/clsLog.php');//Seteo en el log de accesos una entrada invalida
				  
		$log = new Log($objSQLServer);
		if($_POST["chkId"]){
			$id = $coma = ''; 
			foreach($_POST["chkId"] as $item){
				$id.= $coma.$item;
				$coma = ',';
			}	
		}
		$log->insertLog($ip, 'POSIBLE INTENTO DE ATAQUE. Apartado: '.$_GET['c'].($id?' ID en cuesti�n: '.$id:''), $nombre_usuario, '', 0);
		setLog(PATH_LOG_SECURE.$archLog,$errorTxt);//Seteo en el log de seguridad datos adicionales del ataque
		$sec = $objSeguridad->expireUser($idUsuario);
		session_destroy();
		//header("Location:".$_SERVER['REQUEST_URI']);
		echo '<script>location.href="'.$_SERVER['REQUEST_URI'].'"</script>';
		exit();
	}
}


function validarEmail($email){
   	$Sintaxis='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
   	if(preg_match($Sintaxis,$email)){
    	return true;
	}
   	else{
     	return false;
	}
}

function generarCodigoValidacion($email, $imei = NULL){
	##-- Generaci�n de codigo verificador --##
	//-- Digito 1: cant de caracteres antes del "@" en la direccion de email. Si la cantidad de digitos es mayor a 9, se tienen en cuenta digito uno y dos y se los suma.
	//-- Digito 2: cant de caracteres despues del "@", el c�lculo siguiente es igual al digito 1.
	//-- Digito 3: Corresponden a los 2 �ltimos digitos obtenidos de la suma del Digito 1 y 2 M�lt por 6.
	
	$arr_mail = explode('@', $email);
	$digito_1 = (int)substr(strlen($arr_mail[0]),0,1) + (int)substr(strlen($arr_mail[0]),1,1);
	$digito_2 = (int)substr(strlen($arr_mail[1]),0,1) + (int)substr(strlen($arr_mail[1]),1,1);
	
	$aux = (string)(($digito_1+$digito_2)*6);
	$digito_1 = substr((string)$digito_1,0,1);
	$digito_2 = substr((string)$digito_2,0,1);
	$digito_3 = substr($aux,strlen($aux) - 2,2);
	
	return $digito_1.$digito_2.$digito_3;
}

function validarNuevaContrasenna($passNuevo) {
	if (strlen($passNuevo) < 10 || !preg_match('`[0-9]`',$passNuevo) || !preg_match('`[a-z]`',$passNuevo)) { 
		return false;
	}
	return true;
}

function setPost($url,$datos,$header){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$datos);
	
	// este seteo me sirve para q no le de bola a la alerta de que no estoy en un SSL
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	// --
	$json=curl_exec($ch);
	curl_close($ch);
	
	$json = json_decode($json);
	return $json;
}

function objectToArray($d){
    if (is_object($d)) {
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        return array_map(__FUNCTION__, $d);
    } else {
        return $d;
    }
}

function getFechaServer($format = NULL){
	@session_start();
	global $objSQLServer;
	global $rel;
	$format = $format?$format:'d-m-Y H:i:s';
	if(empty($_SESSION['zonaHoraria'])){
		if(!$objSQLServer){
			require_once $rel.'includes/conn.php'; //descomente porq en allinonerastreo tiene conflicto.
		}
		$sql = " SELECT server FROM zonaHoraria(NULL,".(int)$_SESSION['idUsuario'].") ";
		$res = $objSQLServer->dbQuery($sql);
		$rs = $objSQLServer->dbGetRow($res,0,3);
		$_SESSION['zonaHoraria'] = $rs['server'];
	}
	return date($format,strtotime($_SESSION['zonaHoraria'].' hour',strtotime(date('Y-m-d H:i:s'))));
}

function tienePerfil($arrPerfil){
	if(is_array($arrPerfil)){
		if(in_array($_SESSION['idPerfil'],$arrPerfil)){
			return true;	
		}
	}
	else{
		if($_SESSION['idPerfil'] == $arrPerfil){
			return true;	
		}
	}
	return false;	
}

function getIdiomas(){
	$arrIdiomas = $arrDir = array();			
	$urlDir = 'language';
	
	if(file_exists($urlDir)){
		$directorio = opendir($urlDir.'/');	
		while($archivo = readdir($directorio)){
			if(!is_dir($archivo)){
				array_push($arrDir,$archivo); 
			}
		}
	}
	
	foreach($arrDir as $dir){
		$urlFile = $urlDir.'/'.$dir;
		if(file_exists($urlFile)){
			$directorio = opendir($urlFile.'/');	
			while($archivo = readdir($directorio)){
				if(!is_dir($archivo)){
					if(strpos($archivo,$dir) === 0){
						$arc = explode('.',$archivo);
						array_push($arrIdiomas,$arc[0]); 
					}
				}
			}
		}
	}
	return $arrIdiomas;
}

function idiomaHTML($txt){
	$txt = str_replace('[','<',$txt);
	$txt = str_replace(']','>',$txt);	
	return $txt;
}

function getIdiomaBrowser(){ 
	$idioma = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
	return $idioma; 
} 

function codificarURL($id){
	$reset_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
	$url = 'id='.$id.'&reset_code='.$reset_code;
	$palabra_clave = substr(md5($reset_code),0,5);//-- se genera palabra clave de 5 caracteres con md5 x seguridad. --//
	$url_encode = base64_encode($url).$palabra_clave;
	$result['url_encode'] = $url_encode;
	$result['reset_code'] = $reset_code;
	return $result;
}

function decodificarURL($url_encode){
	$palabra_clave = substr($url_encode,(strlen($url_encode) - 5),strlen($url_encode));
	$url_encode = substr($url_encode,0,(strlen($url_encode) - 5));
	$url_decode = base64_decode($url_encode);
	$datos = explode('&',$url_decode);
	$aux = explode('=',$datos[0]);
	$id = $aux[1];
	$aux = explode('=',$datos[1]);
	$reset_code = $aux[1];
	
	if($palabra_clave != substr(md5($reset_code),0,5)){
		return false;
	}
	
	$result['id'] = $id;
	$result['reset_code'] = $reset_code;
	return $result;
}

function normaliza($cadena){
    $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $cadena = utf8_decode($cadena);
    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
    $cadena = strtolower($cadena);
    return utf8_encode($cadena);
}

function getBrowser(){
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)){
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)){
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)){
        $platform = 'windows';
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent)){
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent)){
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent)){
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent)){
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent)){
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)){
        // we have no matching number just continue
    }
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1){
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
   
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
   
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
} 
?>
