<?php 
$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

$arrProtocolo = array('SOAP','JSON');
$arrMetodo = array('POST','GET','PUT');

function index($objSQLServer, $seccion, $mensaje = "") {
   
    $action = isset($_GET['action']) ? $_GET['action'] : 'listar';
   	require_once 'clases/clsOctopus.php';
    $objOctopus = new Octopus($objSQLServer);
	
	$filtro['txt'] = $_POST['txtFiltro']?trim($_POST['txtFiltro']):NULL;
    $arrListado = $objOctopus->getWS($filtro);
	
	$operacion = 'listar';
	$tipoBotonera = 'LI';
	require("includes/template.php");
}

function alta($objSQLServer, $seccion, $mensaje = "", $noError = false) {
  	global $arrProtocolo;
	global $arrMetodo;
  
  	require_once("clases/clsClientes.php");
	$objCliente = new Cliente($objSQLServer);
	$arrAgente = $objCliente->obtenerAgentes2();  
	
	$operacion = 'alta';
	$tipoBotonera = 'AM';
	require("includes/template.php");
}

function modificar($objSQLServer, $seccion = "", $mensaje = "", $id = 0) {
  	global $arrProtocolo;
	global $arrMetodo;
	
	require_once("clases/clsClientes.php");
	$objCliente = new Cliente($objSQLServer);
	$arrAgente = $objCliente->obtenerAgentes2();  
	
	$filtro['id'] = $id = (isset($_POST['hidId']))?$_POST['hidId']:(($id)?$id:0);
	
	require_once 'clases/clsOctopus.php';
    $objOctopus = new Octopus($objSQLServer);
	
	$arrRow = $objOctopus->getWS($filtro);
	
	$_POST['txtNombre'] = $arrRow[0]['oc_nombre'];
	$_POST['cmbAgente'] = $arrRow[0]['oc_cl_id'];
    $_POST['cmbTipoProtocolo'] = $arrRow[0]['oc_protocolo'];
    $_POST['cmbMetodo'] = $arrRow[0]['oc_metodo'];
    $_POST['txtURL'] = $arrRow[0]['oc_url'];
	$_POST['txtHeader'] = $arrRow[0]['oc_header'];
    //$_POST['txtDatos'] = str_replace('&quot;','"',str_replace('&lt;','<',str_replace('&gt;','>',$arrRow[0]['oc_datos'])));
	$_POST['txtDatos'] = $arrRow[0]['oc_datos'];
	$_POST['txtCurl'] = $arrRow[0]['oc_curl_setopt'];
	
	$arrParameter = $objOctopus->getWSParameter($id);
	foreach($arrParameter as $parameter){
		$_POST[$parameter['op_parametro']] = $parameter['op_valor'];	
	}
	
	$operacion = 'modificar';
    $tipoBotonera = 'AM';
    require("includes/template.php");
}


function baja($objSQLServer, $seccion) {
	global $lang;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:0;
	
	if($id){
		$params = array('oc_borrado' => 1);
		if($objSQLServer->dbQueryUpdate($params, 'tbl_octopus', 'oc_id = '.(int)$id)){
			$mensaje = $lang->message->ok->msj_baja;	
		}
		else{
			$mensaje = $lang->message->error->msj_baja;
		}
	}
	index($objSQLServer, $seccion, $mensaje);
}

function guardarA($objSQLServer, $seccion) {
	global $lang;
	
	$resp = validarCampos();
	$mensaje = $resp['mensaje'];
	$params = $resp['campos'];
	
	if(isset($_POST['cmbAgente'])){
		$params['oc_cl_id'] = trim($_POST['cmbAgente']);
		$msjError = checkCombo(strlen($params['oc_cl_id']), 'Agente', 1, 1);
		if($msjError){ 
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	if(!$mensaje){
		if ($id = $objSQLServer->dbQueryInsert($params, 'tbl_octopus')){
			//-- Procesar parametros --//
			require_once 'clases/clsOctopus.php';
    		$objOctopus = new Octopus($objSQLServer);
	
			$resp = validarCamposAdicional();
			$objOctopus->setWSParameter($id, $resp);
			
			$mensaje = $lang->message->ok->msj_alta;
		}
		else{
			$mensaje = $lang->message->error->msj_alta;
		}
		index($objSQLServer, $seccion, $mensaje);
	}
	else{
		alta($objSQLServer, $seccion, $mensaje);
	}
}

function guardarM($objSQLServer, $seccion) { 
   	global $lang;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:0;
	 
	$resp = validarCampos();
	$mensaje = $resp['mensaje'];
	$params = $resp['campos'];
	
	if(isset($_POST['cmbAgente'])){
		$params['oc_cl_id'] = trim($_POST['cmbAgente']);
		$msjError = checkCombo(strlen($params['oc_cl_id']), 'Agente', 1, 1);
		if($msjError){ 
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	if(!$mensaje){
		if ($objSQLServer->dbQueryUpdate($params, 'tbl_octopus', 'oc_id = '.(int)$id)){
			//-- Procesar parametros --//
			require_once 'clases/clsOctopus.php';
    		$objOctopus = new Octopus($objSQLServer);
			
			$resp = validarCamposAdicional();
			$objOctopus->setWSParameter($id, $resp);
			
			$mensaje = $lang->message->ok->msj_modificar;
		}
		else{
			$mensaje = $lang->message->error->msj_modificar;
		}
		index($objSQLServer, $seccion, $mensaje);
	}
	else{
		 modificar($objSQLServer, $seccion, $mensaje, $id);
	}
}

function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}

function probarServicio($objSQLServer, $seccion){
	global $arrProtocolo;
	global $arrMetodo;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:0;
	
	require_once("clases/clsClientes.php");
	$objCliente = new Cliente($objSQLServer);
	$arrAgente = $objCliente->obtenerAgentes2();  
	
	
	$resp = validarCampos();
	if(!empty($resp['mensaje'])){
		$noError = true;
		alta($objSQLServer, $_POST['hidSeccion'], $resp['mensaje'], $noError);
	}
	else{
		$campos = $resp['campos'];
		$valorCampos =  $resp['valorCampos'];
		
		$mensaje = '';
		switch($_POST['cmbTipoProtocolo']){
			case 'SOAP':
				//-- Header
				$header = array('Content-Type:text/xml');
				
				//-- Datos
				$datos = trim($_POST['txtDatos']);
				$datos = html_entity_decode($datos);
			break;
			case 'JSON':
				//-- Header
				$header = array('Content-Type:application/json');
				
				//-- Datos
				$datos = trim($_POST['txtDatos']);
				if(!empty($datos)){	
					$aux = explode(',',$datos);
					$datos = array();
					foreach($aux as $item){
						$aux_item = explode(':',$item);					
						if(isset($datos[trim($aux_item[0])])){
							if(is_array($datos[trim($aux_item[0])])){
								$datos[trim($aux_item[0])][] = trim($aux_item[1]); 
							}
							else{
								$datos[trim($aux_item[0])] = array($datos[trim($aux_item[0])], trim($aux_item[1]));
							}
						}
						else{
							$datos[trim($aux_item[0])] = trim($aux_item[1]); 
						}	
					}
					unset($aux);
					unset($aux_item);
					$datos = json_encode($datos);
				}
			break;	
		}
		
		//-- Header
		
		if(!empty($_POST['txtHeader'])){
			$aux = explode(',',$_POST['txtHeader']);
			foreach($aux as $item){
				array_push($header, trim($item));	
			}
		}
		
		//-- Curl_Opt
		if(!empty($_POST['txtCurl'])){
			$aux = explode(',',$_POST['txtCurl']);
			$curl_opt = array();
			foreach($aux as $item){
				$aux_item = explode('=',$item);
				$curl_opt[trim($aux_item[0])] = trim($aux_item[1]);
				unset($aux_item);
			}
		}
		
		if(empty($mensaje)){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, trim($_POST['txtURL']));
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_POST['cmbMetodo']);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			// este seteo me sirve para q no le de bola a la alerta de que no estoy en un SSL
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			if(isset($curl_opt)){
				foreach($curl_opt as $curl_item => $curl_value){
					curl_setopt($ch, $curl_item, $curl_value);	
				}	
			}
			
			// --
			if(!$ws_return = curl_exec($ch)){
				$ws_return = curl_error($ch);
			}
			curl_close($ch);
			
			switch($_POST['cmbTipoProtocolo']){
				case 'SOAP':
					$doc = new DOMDocument();
					libxml_use_internal_errors(true);
					$doc->loadHTML($ws_return);
					libxml_clear_errors();
					$xml = $doc->saveXML($doc->documentElement);
					$xml = simplexml_load_string($xml);
					$ws_response = $xml->body->envelope->body;
					$ws_response_msg = $ws_return;
				break;
				case 'JSON':
					$ws_response = json_decode($ws_return);
					$ws_response_msg = $ws_return;
				break;	
			}
			unset($ws_return);
			
			if(!empty($_POST['valRuta']) || is_array($ws_response)){ 
				if(!is_array($ws_response)){
					$aux_ruta = getObjectVar($_POST['valRuta']);
					foreach($aux_ruta as $ruta){
						$ws_response = $ws_response->$ruta;				
					}
				}

				if($ws_response){
					$var['matricula'] = getObjectVar($_POST['valMatricula']);
					$var['evento'] = getObjectVar($_POST['valEvento']);
					$var['fechaGPS'] = getObjectVar($_POST['valFechaGPS']);
					$var['latitud'] = getObjectVar($_POST['valLatitud']);
					$var['longitud'] = getObjectVar($_POST['valLongitud']);
					$var['velocidad'] = getObjectVar($_POST['valVelocidad']);
					$var['estadoMotor'] = getObjectVar($_POST['valEstadoMotor']);
					$var['odometro'] = getObjectVar($_POST['valOdometro']);
					
					$opt['evento'][1] = getOptionVar($_POST['valOptEvento01']);
					$opt['evento'][75] = getOptionVar($_POST['valOptEvento75']);
					$opt['evento'][76] = getOptionVar($_POST['valOptEvento76']);
					$opt['evento'][6] = getOptionVar($_POST['valOptEvento06']);
					$opt['evento'][9] = getOptionVar($_POST['valOptEvento09']);
					$opt['evento'][8] = getOptionVar($_POST['valOptEvento08']);
					$opt['evento'][13] = getOptionVar($_POST['valOptEvento13']);
					$opt['evento'][36] = getOptionVar($_POST['valOptEvento36']);
					$opt['evento'][67] = getOptionVar($_POST['valOptEvento67']);
					$opt['evento'][54] = getOptionVar($_POST['valOptEvento54']);
					$opt['evento'][55] = getOptionVar($_POST['valOptEvento55']);
					
					$opt['estadoMotor'][0] = getOptionVar($_POST['valOptEstadoMotorApagado']);
					$opt['estadoMotor'][1] = getOptionVar($_POST['valOptEstadoMotorEncendido']);
						
					$ws_data = array();										
					foreach($ws_response as $item){
						//--Matricula
						if($var['matricula']){
							$ws['matricula'] = $item;
							foreach($var['matricula'] as $item_var){
								$ws['matricula'] = $ws['matricula']->$item_var;
							}
						}
						
						if($var['evento']){
							$ws['evento'] = $item;
							foreach($var['evento'] as $item_var){
								$ws['evento'] = $ws['evento']->$item_var;
							}
							
							if(!empty($ws['evento'])){
								foreach($opt['evento'] as $k => $var_opt){
									if(in_array((strtolower((string)$ws['evento'])),$var_opt)){
										$ws['evento'] = $k;	
									}	
								}	
							}
						}
						
						if($var['fechaGPS']){
							$ws['fechaGPS'] = $item;
							foreach($var['fechaGPS'] as $item_var){
								$ws['fechaGPS'] = $ws['fechaGPS']->$item_var;
							}
							$ws['fechaGPS'] = str_replace('/','-',$ws['fechaGPS']);

							$ws['fechaGPS'] = date('Y-m-d H:i:s',strtotime($ws['fechaGPS']));
						}
						
						if($var['latitud']){
							$ws['latitud'] = $item;
							foreach($var['latitud'] as $item_var){
								$ws['latitud'] = $ws['latitud']->$item_var;
							}
						}
						
						if($var['longitud']){
							$ws['longitud'] = $item;
							foreach($var['longitud'] as $item_var){
								$ws['longitud'] = $ws['longitud']->$item_var;
							}
						}
						
						if($var['velocidad']){
							$ws['velocidad'] = $item;
							foreach($var['velocidad'] as $item_var){
								$ws['velocidad'] = $ws['velocidad']->$item_var;
							}
						}
						
						if($var['estadoMotor']){
							$ws['estadoMotor'] = $item;
							foreach($var['estadoMotor'] as $item_var){
								$ws['estadoMotor'] = $ws['estadoMotor']->$item_var;
							}
							
							if(!empty($ws['estadoMotor'])){
								foreach($opt['estadoMotor'] as $k => $var_opt){
									if(in_array((strtolower((string)$ws['estadoMotor'])),$var_opt)){
										$ws['estadoMotor'] = $k;	
									}	
								}	
							}
						}
						
						if($var['odometro']){
							$ws['odometro'] = $item;
							foreach($var['odometro'] as $item_var){
								$ws['odometro'] = $ws['odometro']->$item_var;
							}
						}
						
						array_push($ws_data,array('matricula'=>(string)$ws['matricula']
								,'evento'=>(string)$ws['evento'],'fechaGPS'=>(string)$ws['fechaGPS']
								,'latitud'=>(float)$ws['latitud'],'longitud'=>(float)$ws['longitud']
								,'velocidad'=>(string)$ws['velocidad'],'estadoMotor'=>(string)$ws['estadoMotor']
								,'odometro'=>(string)$ws['odometro']));	
					}
				}
			}
		
			$operacion = ($id)?'modificar':'alta';
			$tipoBotonera = 'AM';
			require("includes/template.php");
		}
		
	}
}

function getObjectVar($parameter){
	$response = NULL;
	if(!empty($parameter)){
		$parameter =  ($_POST['cmbTipoProtocolo'] == 'SOAP')?trim(strtolower($parameter)):trim($parameter);
		$response = explode(htmlentities('->'),$parameter);
	}
	return $response;	
}

function getOptionVar($parameter){
	$response = array();
	if(!empty($parameter)){
		$aux = explode('/',$parameter);
		foreach($aux as $item){
			array_push($response,trim(strtolower($item)));
		}
	}
	return $response;	
}

function validarCampos(){
	global $lang;
	$mensaje = '';
	$arr = array();
	
	$arr['oc_nombre'] = trim($_POST['txtNombre']);	
	$msjError = checkString($arr['oc_nombre'], 0, 50 ,'Nombre',1);
	if($msjError){ 
		$mensaje.="* ".$msjError."<br/> ";
	}
	
	if(isset($_POST['cmbTipoProtocolo'])){
		$arr['oc_protocolo'] = trim($_POST['cmbTipoProtocolo']);
		$msjError = checkCombo(strlen($arr['oc_protocolo']), 'Protocolo', 1, 1);
		if($msjError){ 
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	if(isset($_POST['cmbMetodo'])){
		$arr['oc_metodo'] = trim($_POST['cmbMetodo']);
		$msjError = checkCombo(strlen($arr['oc_metodo']), 'Método', 1, 1);
		if($msjError){ 
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	if(isset($_POST['txtURL'])){
		$arr['oc_url'] =  trim($_POST['txtURL']);	
		$msjError = checkString($arr['oc_url'], 0, 500 ,'URL',1);
		if($msjError){ 
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	$arr['oc_header'] = trim($_POST['txtHeader']);
	$arr['oc_datos'] = trim($_POST['txtDatos']);
	$arr['oc_curl_setopt'] = trim($_POST['txtCurl']);
	
	$resp = array('mensaje' => $mensaje, 'campos' => $arr);
	return $resp;
}

function validarCamposAdicional(){
	$arr = array();
	
	$arr['valRuta'] = !empty($_POST['valRuta'])?trim($_POST['valRuta']):NULL;
    $arr['valMatricula'] = !empty($_POST['valMatricula'])?trim($_POST['valMatricula']):NULL;
    $arr['valEvento'] = !empty($_POST['valEvento'])?trim($_POST['valEvento']):NULL;
	$arr['valOptEvento01'] = !empty($_POST['valOptEvento01'])?trim($_POST['valOptEvento01']):NULL;
	$arr['valOptEvento75'] = !empty($_POST['valOptEvento75'])?trim($_POST['valOptEvento75']):NULL;
	$arr['valOptEvento76'] = !empty($_POST['valOptEvento76'])?trim($_POST['valOptEvento76']):NULL;
	$arr['valOptEvento06'] = !empty($_POST['valOptEvento06'])?trim($_POST['valOptEvento06']):NULL;
	$arr['valOptEvento09'] = !empty($_POST['valOptEvento09'])?trim($_POST['valOptEvento09']):NULL;
    $arr['valOptEvento08'] = !empty($_POST['valOptEvento08'])?trim($_POST['valOptEvento08']):NULL;
	$arr['valOptEvento13'] = !empty($_POST['valOptEvento13'])?trim($_POST['valOptEvento13']):NULL;
	$arr['valOptEvento36'] = !empty($_POST['valOptEvento36'])?trim($_POST['valOptEvento36']):NULL;
	$arr['valOptEvento67'] = !empty($_POST['valOptEvento67'])?trim($_POST['valOptEvento67']):NULL;
	$arr['valOptEvento54'] = !empty($_POST['valOptEvento54'])?trim($_POST['valOptEvento54']):NULL;
	$arr['valOptEvento55'] = !empty($_POST['valOptEvento55'])?trim($_POST['valOptEvento55']):NULL;
	$arr['valFechaGPS'] = !empty($_POST['valFechaGPS'])?trim($_POST['valFechaGPS']):NULL;
    $arr['valLatitud'] = !empty($_POST['valLatitud'])?trim($_POST['valLatitud']):NULL;
    $arr['valLongitud'] = !empty($_POST['valLongitud'])?trim($_POST['valLongitud']):NULL;
    $arr['valVelocidad'] = !empty($_POST['valVelocidad'])?trim($_POST['valVelocidad']):NULL;
    $arr['valEstadoMotor'] = !empty($_POST['valEstadoMotor'])?trim($_POST['valEstadoMotor']):NULL;
	$arr['valOptEstadoMotorEncendido'] = !empty($_POST['valOptEstadoMotorEncendido'])?trim($_POST['valOptEstadoMotorEncendido']):NULL;
	$arr['valOptEstadoMotorApagado'] = !empty($_POST['valOptEstadoMotorApagado'])?trim($_POST['valOptEstadoMotorApagado']):NULL;
    $arr['valOdometro'] = !empty($_POST['valOdometro'])?trim($_POST['valOdometro']):NULL;
	return $arr;
}