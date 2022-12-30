<?php
//Libreria de funciones para validar
/*Ejemplos
	echo checkValidDate("/05/2010",1,"DDMMYYYY","/");
	echo checkString("hola como andas¿?",1,10);
*/
//Chequea una fécha válida

function checkValidDate ($strValue, $blnOptional, $strFormat, $strSeparator, $nombrecampo) {
	global $lang;
	if (!$strValue) {
		if (!$blnOptional){
			$strError = $lang->message->interfaz_generica->msj_campo_vacio;
			$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
			return $strError;
		}
	}
	if (!$strSeparator)$strSeparator = "/";

	//Paso el valor que me pasan a un array para evaluarlo con checkDate
	$arrFecha = explode($strSeparator, $strValue);
	if (!is_array($arrFecha)){
		$strError = $lang->message->interfaz_generica->msj_campos_invalidos;
		$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
		return $strError;
	} 
	if (count($arrFecha)!=3){
		$strError = $lang->message->interfaz_generica->msj_formato_fecha;
		$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
		$strError = str_replace('[VALOR]',$strFormat,$strError);
		return $strError;
	}
	switch ($strFormat) {
		case "YYYYMMDD":
			$blnValidacion = @checkdate($arrFecha[1], $arrFecha[2], $arrFecha[0]);
			break;
		case "MMDDYYYY":
			$blnValidacion = @checkdate($arrFecha[0], $arrFecha[1], $arrFecha[2]);
			break;
		case "DDMMYYYY":
		default:
			$blnValidacion = @checkdate($arrFecha[1], $arrFecha[0], $arrFecha[2]);
			break;
	}
	if(!$blnValidacion){
		$strError = $lang->message->interfaz_generica->msj_fecha_invalida;
		$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
		return $strError;
	}
	return true;
}

//Chequea un string
function checkString($strValue, $intMin, $intMax, $nombrecampo, $requerido) {
	global $lang;
	$strError = "";
	$validar = 0;

	$strValue = trim($strValue);
	if($requerido){
		if(esVacio($strValue)){
			$strError = $lang->message->interfaz_generica->msj_completar;
			$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
			return $strError;	
		}
		else $validar = 1;
	}elseif(!esVacio($strValue)){
		$validar = 1;
	}

	if($validar == 1){

		if (strlen($strValue) < $intMin){
			$strError = $lang->message->interfaz_generica->msj_cant_min_caracteres;
			$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
			$strError = str_replace('[VALOR]',$intMin,$strError);
		}else {
			if ((!$strError) && (strlen($strValue) > $intMax)){
				$strError = $lang->message->interfaz_generica->msj_cant_max_caracteres;
				$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
				$strError = str_replace('[VALOR]',$intMax,$strError);
			}else{
				if (strstr($strValue, "'")){
					$strError = $lang->message->interfaz_generica->msj_campos_invalidos;
					$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
				}
			}
		}
		return $strError;
	}
}

/**
 *controla si un numero esta dentro de un rango. los limites son inclusivos [intMin-intMax]
 *@param int|double $intValue
 *@param int|double $intMin
 *@param int|double $intMax
 *@param string $nombrecampo
 *@param boolean $requerido
 *@return string
 */
function checkNumber($intValue, $intMin, $intMax, $nombrecampo, $requerido) {
	global $lang;
	$strError = "";
	$validar = 0;
	if($requerido){
		if(esVacio($intValue)){
			$strError = $lang->message->interfaz_generica->msj_completar;
			$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
		}else{
			$validar = 1;
		}
	}elseif(!esVacio($intValue)){
		$validar = 1;
	}

	if($validar == 1){
		if (!is_numeric($intValue)){
			$strError = $lang->message->interfaz_generica->msj_valor_numerico;
			$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
		}else{
			if($intValue < $intMin){
				$strError = $lang->message->interfaz_generica->msj_valor_min;
				$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
				$strError = str_replace('[VALOR]',$intMin,$strError);
			}elseif ($intValue > $intMax){
				$strError = $lang->message->interfaz_generica->msj_valor_max;
				$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
				$strError = str_replace('[VALOR]',$intMin,$strError);
			}
		}
	}
	return $strError;
}

function checkCombo($intValue, $nombrecampo, $requerido, $valorMenor=0) {
	global $lang;
	$strError = "";
	if($requerido){
		if($intValue <= $valorMenor){
			$strError = $lang->message->interfaz_generica->msj_select_option;
			$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
		}
	}
	return $strError;
}


/*//Chequea un matricula formato : LLLNNN(Letra - Numero)
function checkMatricula($strValue, $nombrecampo, $requerido) {
	global $lang;
	$strError = "";
	$strCadena = "abcdefghijklmnopqrstuvwxyz";
	$validar = 0;
	if($requerido){
		if(esVacio($strValue))return $lang->message->interfaz_generica->msj_completar.' ('.$nombrecampo.')';
		else $validar = 1;
	}elseif(!esVacio($strValue)){
		$validar = 1;
	}

	if($validar == 1){

		if (strlen($strValue) != 6){
			$strError = "El campo ".$nombrecampo." debe contener 6 caracteres";
		}else{
			$intError = 0;
			if (!is_numeric(substr($strValue,3,3))){ $intError = 1;}
			if (strpos($strCadena,substr($strValue,0,1)) === ""){ $intError = 1;}
			if (strpos($strCadena,substr($strValue,1,1)) === ""){ $intError = 1;}
			if (strpos($strCadena,substr($strValue,2,1)) === ""){ $intError = 1;}
				if ($intError)	$strError = "El campo ".$nombrecampo." no respeta el formato";
		}
		return $strError;
	}
}*/

function checkMail($email, $nombrecampo, $requerido=0){
	global $lang;
	$validar = 0;
	if($requerido){
		if(esVacio($email)){
			$strError = $lang->message->interfaz_generica->msj_completar;
			$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
			return $strError;
		}
		else $validar = 1;
	}elseif(!esVacio($email)){
		$validar = 1;
	}

	if($validar == 1){
		$mail_correcto=1;

		if (function_exists('filter_var')){
			if (!filter_var($email,FILTER_VALIDATE_EMAIL))
				$mail_correcto=0;
		}else{
			// Primero, checamos que solo haya un símbolo @, y que los largos sean correctos
			if (!@ereg("^[^@]{1,64}@[^@]{1,255}$", $email)){
				// correo inválido por número incorrecto de caracteres en una parte, o número incorrecto de símbolos @
				$mail_correcto=0;
			}

			// se divide en partes para hacerlo más sencillo
			$email_array = explode("@", $email);
			$local_array = explode(".", $email_array[0]);
			
			for ($i = 0; $i < sizeof($local_array); $i++){
			if (!@ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])){
				$mail_correcto=0;
				}
			}
		}
	  	if (!$mail_correcto){
			$strError = $lang->message->interfaz_generica->msj_mail_incorrecto;
			$strError = str_replace('[NOMBRE_CAMPO]',$nombrecampo,$strError);
			return $strError;
		}
	  	else return "";
	}
}

function esVacio($campo=""){
	$campo = trim($campo);
	if(strlen($campo) == 0) return true;
	return false;
}

function checkAll($item, $post){
	global $lang;
	//--//
	$msjError = "";
	$idCampo= $item["ig_idCampo"];
	$campo_error = $lang->system->$item["ig_nombre"]?$lang->system->$item["ig_nombre"]:'**'.$item["ig_nombre"];
	switch ($item["ig_tipoDato"]){
		case 1://TEXTO
			$msjError = checkString($post[$idCampo], $item["ig_min"], $item["ig_max"],$campo_error,$item["ig_requerido"]);
		break;
		case 2://NUMERO
			if($item["ig_ti_id"]==2 && isset($post[$idCampo])){
				//SI ES UN COMBO
				$msjError = checkCombo($post[$idCampo], $campo_error, $item["ig_requerido"]);
			}
			else if (isset($post[$idCampo])){
				//SI ES UN TEXTO NUMERICO
				$msjError = checkNumber($post[$idCampo], $item["ig_min"], $item["ig_max"],$campo_error,$item["ig_requerido"]);
			}
		break;
		case 3:
			//MAIL
			if (isset($post[$idCampo])){
				$msjError = checkMail($post[$idCampo],$campo_error, $item["ig_requerido"]);
			}
		break;
		case 4://TEXTO SIN espacios
			$msjError = checkString(str_replace(" ","",$post[$idCampo]), $item["ig_min"], $item["ig_max"],$campo_error,$item["ig_requerido"]);
		break;
		default:
			$msjError = 'Tipo de datos: '.$item["ig_tipoDato"].'#'.$item['ig_nombre'].', -desconocido-';
		break;
	}
	return $msjError;
	//--//	
}

function escapear_array($array){
	global $_GET;
	if(isset($array)){
		array_walk($array, 
			function(&$v) {
				if(is_string($v)){
						$v = escapear_string($v);
						/*if(isset($_GET['method']) &&  $_GET['method'] == 'ajax'){//-- Por problemas con caracteres unicamente en POPUP se implemento esto. ej: referencias //
							$v = iconv("UTF-8", "ISO-8859-1", escapear_string($v));
						}
						else{
							$v = escapear_string($v);
						}*/
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
	global $esSQL;
	
	$strVar = str_replace(">", "&gt;", $strVar); 
   	$strVar = str_replace("<", "&lt;", $strVar); 
   	$strVar = str_replace("`", "&#039", $strVar); 
   	$strVar = str_replace("'", "&#039", $strVar);
   	$strVar = str_replace("\"", "&quot;", $strVar);
   	$strVar = str_replace("*", "", $strVar);
	$strVar = str_replace("--", "", $strVar);
	if(!$esSQL){
		$strVar = str_ireplace("CHAR(", "", $strVar);
		$strVar = strip_tags($strVar);
	}
	
   	return $strVar;
}


//--- ---//

function escapear_db_array($array){
	if(isset($array)){
		array_walk($array, 
			function(&$v) {
				if(is_string($v)){
					$v = escapar_db($v);
					
				}
				elseif(is_array($v)){
					escapear_db_array($v);
				}
			}
		);
		return $array;
	}
	else{
		return false;	
	}
}

function escapar_db($strVar){
   	$strVar = trim($strVar);
   	$strVar = str_ireplace('insert', '', $strVar); 
   	$strVar = str_ireplace('update', '', $strVar);
	$strVar = str_ireplace('delete', '', $strVar);
	$strVar = str_ireplace('drop', '', $strVar);
	$strVar = str_ireplace('truncate', '', $strVar);
	$strVar = str_ireplace("CHAR(", '', $strVar);
	
	return $strVar;
}