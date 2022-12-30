<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
$sinDefaultJS=true;
$arr_hora = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','00');
$arr_min = array('00','10','20','30','40','50');

function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	
	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
			
	require_once 'clases/clsAlertasXGeocercas.php';
	$objAlertas = new AlertasXGeocerca($objSQLServer);
	
	if($_GET['viewAll']){
		$datos['filtro'] = 'getAllReg';
		$filtro = '';
	}
		
	$datos['idTipoEmpresa'] = $_SESSION['idTipoEmpresa'];
	$datos['idUsuario'] = $_SESSION['idUsuario'];
	$datos['idPerfil'] = $_SESSION['idPerfil'];
	$datos['filtro'] = $filtro;
		
	$arrEntidades = obtenerListadoAlertas($objAlertas, 'list', $datos);
	if($arrEntidades){
		foreach($arrEntidades as $k => $item){
			$arrEntidades[$k]['accion'] = $objAlertas->validarEdicionAlertas($item['al_id'], $item['al_us_id'], $arrMoviles);	
		}
		$cantRegistros = count($arrEntidades);
	}
	
	require 'includes/template.php';
}

function alta($objSQLServer, $seccion, $mensaje = NULL,$datosCargados = NULL){
	global $lang;
	
	require_once 'clases/clsReferencias.php';
	$objReferencias = new Referencia($objSQLServer);
	$arrReferencias = $objReferencias->obtenerReferenciasEmpresa($_SESSION["idEmpresa"]);
	
	require_once("clases/clsDefinicionReportes.php");
	$objEventos = new DefinicionReporte($objSQLServer);
	$arrEventos = $objEventos->obtenerEventosAsignados($_SESSION['idAgente']);
	foreach($arrEventos as $k => $item){
		if($item['id'] == 14 || $item['id'] == 15){
			unset($arrEventos[$k]);
		}
	}
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	$arrMoviles = $objMovil->obtenerMovilesUsuarioAlerta($_SESSION['idUsuario']);	
	
	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	$arrUsuarios = $objUsuario->obtenerUsuarios(0,NULL,NULL,$_SESSION['idEmpresa']);
	
	$valorActivo['moviles'] = isset($datosCargados['moviles'])?$datosCargados['moviles']:array();
	$valorActivo['referencias'] = isset($datosCargados['referencias'])?$datosCargados['referencias']:array();
	$valorActivo['eventos'] = isset($datosCargados['eventos'])?$datosCargados['eventos']:array();
	$valorActivo['usuarios'] = isset($datosCargados['usuarios'])?((isset($datosCargados['usuarios']) && isset($datosCargados['usuarios_mail']))?array_merge($datosCargados['usuarios'],$datosCargados['usuarios_mail']):$datosCargados['usuarios']):array($_SESSION['idUsuario']);
	$valorActivo['lunes_a_viernes'] = ($datosCargados['dias']['dia_semana']['fin'] == '00:00:00')?0:1;
	$valorActivo['sabados_y_domingos'] = ($datosCargados['dias']['fin_semana']['fin'] == '00:00:00')?0:1;
	$valorActivo['hora'] = array('ini'=>00,'fin'=>00);
	$valorActivo['min']= array('ini'=>00,'fin'=>00);
	
	if(!isset($datosCargados)){
	//-- Config opciones por defecto --//
	if(count($arrMoviles) < 11){
		foreach($arrMoviles as $item){
			array_push($valorActivo['moviles'],$item['mo_id']);
		}		
	}
	
	if(count($arrReferencias) < 11){
		foreach($arrReferencias as $item){
			array_push($valorActivo['referencias'],$item['re_id']);
		}		
	}
	
	if(count($arrEventos) < 11){
		foreach($arrEventos as $item){
			array_push($valorActivo['eventos'],$item['id']);
		}		
	}
	//-- --//
	}
	
	//$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$operacion = 'alta';
	require("includes/template.php");
}

function modificar($objSQLServer, $seccion, $mensaje = NULL, $datosCargados = NULL){
	global $lang;
	$id = isset($_POST['hidId'])?(int)$_POST['hidId']:0;
	
	require_once 'clases/clsReferencias.php';
	$objReferencias = new Referencia($objSQLServer);
	$arrReferencias = $objReferencias->obtenerReferenciasEmpresa($_SESSION["idEmpresa"]);
	
	require_once("clases/clsDefinicionReportes.php");
	$objEventos = new DefinicionReporte($objSQLServer);
    $arrEventos = $objEventos->obtenerEventosAsignados($_SESSION['idAgente']);
	foreach($arrEventos as $k => $item){
		if($item['id'] == 14 || $item['id'] == 15){
			unset($arrEventos[$k]);
		}
	}
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	$arrMoviles = $objMovil->obtenerMovilesUsuarioAlerta($_SESSION['idUsuario']);	
	
	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	$arrUsuarios = $objUsuario->obtenerUsuarios(0,NULL,NULL,$_SESSION['idEmpresa']);
	
	require_once 'clases/clsAlertasXGeocercas.php';
	$objAlerta = new AlertasXGeocerca($objSQLServer);
	$arrEntidades = $objAlerta->obtenerRegistros($id);
	$arrEntidades = $arrEntidades[0];
	
	$valorActivo['moviles'] = isset($datosCargados['moviles'])?$datosCargados['moviles']:array();
	$valorActivo['referencias'] = isset($datosCargados['referencias'])?$datosCargados['referencias']:array();
	$valorActivo['eventos'] = isset($datosCargados['eventos'])?$datosCargados['eventos']:array();
	$valorActivo['usuarios'] = isset($datosCargados['usuarios'])?((isset($datosCargados['usuarios']) && isset($datosCargados['usuarios_mail']))?array_merge($datosCargados['usuarios'],$datosCargados['usuarios_mail']):$datosCargados['usuarios']):array();
	$valorActivo['lunes_a_viernes'] = isset($datosCargados['dias']['dia_semana']['fin'])?(($datosCargados['dias']['dia_semana']['fin'] == '00:00:00')?0:1):0;
	$valorActivo['sabados_y_domingos'] = isset($datosCargados['dias']['fin_semana']['fin'])?(($datosCargados['dias']['fin_semana']['fin'] == '00:00:00')?0:1):0;
	$valorActivo['hora'] = array('ini'=>00,'fin'=>00);
	$valorActivo['min']= array('ini'=>00,'fin'=>00);
	
	if(!isset($datosCargados)){
	//-- Definir Parámetros seleccionados--//
	if($arrEntidades['al_duracion'] == 1 ){
		$valorActivo['lunes_a_viernes'] = 1;
		$valorActivo['sabados_y_domingos'] = 1;	
	}
	else{
		$arrAlertasDiaSemana = $objAlerta->obtenerAlertasPorDias($id);
		$arrAlertasDiaSemana = filtrarDiasDistintos($arrAlertasDiaSemana);
		if(isset($arrAlertasDiaSemana[1]['ds_hora_inicio'])){
        	if($arrAlertasDiaSemana[1]['ds_hora_inicio'] != '00:00:00' || $arrAlertasDiaSemana[1]['ds_hora_fin'] != '00:00:00'){
				$valorActivo['lunes_a_viernes'] = 1;
				
				$aux_ini = explode(':',$arrAlertasDiaSemana[1]['ds_hora_inicio']);
				$aux_fin = explode(':',$arrAlertasDiaSemana[1]['ds_hora_fin']);
				$valorActivo['hora'] = array('ini'=>$aux_ini[0],'fin'=>$aux_fin[0]);
				$valorActivo['min']= array('ini'=>$aux_ini[1],'fin'=>$aux_fin[1]);
			}
		}
		
        if(isset($arrAlertasDiaSemana[6]['ds_hora_inicio'])){
			if($arrAlertasDiaSemana[6]['ds_hora_inicio'] != '00:00:00' || $arrAlertasDiaSemana[6]['ds_hora_fin'] != '00:00:00'){
				$valorActivo['sabados_y_domingos'] = 1;	
				
				$aux_ini = explode(':',$arrAlertasDiaSemana[6]['ds_hora_inicio']);
				$aux_fin = explode(':',$arrAlertasDiaSemana[6]['ds_hora_fin']);
				$valorActivo['hora'] = array('ini'=>$aux_ini[0],'fin'=>$aux_fin[0]);
				$valorActivo['min']= array('ini'=>$aux_ini[1],'fin'=>$aux_fin[1]);
			}
		}
	}
	//---
	$arrReferenciasUsadas = $objAlerta->obtenerGeocercasAlerta($id);
	if(is_array($arrReferenciasUsadas)) {
		foreach ($arrReferenciasUsadas as $tmp){
			array_push($valorActivo['referencias'],$tmp['id']);
		}
	}
	//---
	$arrEventosUsados = $objAlerta->obtenerEventosAlerta($id);
	if (is_array($arrEventosUsados)) {
		foreach ($arrEventosUsados as $tmp) {
			if($tmp['id'] != 14 && $tmp['id'] != 15){
				array_push($valorActivo['eventos'],$tmp['id']);
			}
		}
	}
	//---
	$arrUsuariosUsados = $objAlerta->obtenerUsuariosAlerta($id);
	if (is_array($arrUsuariosUsados)) {
		foreach ($arrUsuariosUsados as $tmp) {
			array_push($valorActivo['usuarios'],$tmp['id']);
		}
	}
	if(!empty($arrEntidades['al_otros_email'])){
		if(strpos($arrEntidades['al_otros_email'],',') !== false){
			$arr = explode(',',$arrEntidades['al_otros_email']);
			foreach($arr as $temp){
				array_push($valorActivo['usuarios'],trim($temp));	
			}
		}
		elseif(strpos($arrEntidades['al_otros_email'],';') !== false){
			$arr = explode(';',$arrEntidades['al_otros_email']);
			foreach($arr as $temp){
				array_push($valorActivo['usuarios'],trim($temp));	
			}
		}
		else{
			array_push($valorActivo['usuarios'],trim($arrEntidades['al_otros_email']));		
		}	
	}
	//---
	$arrMovilesUsados = $objAlerta->obtenerMovilesAlerta($id);
	if(is_array($arrMovilesUsados)) {
		foreach ($arrMovilesUsados as $tmp) {
			array_push($valorActivo['moviles'],$tmp['id']);
		}
	}
	//-- --//
	}
		
	//-- Fin. Definir Parámetros Seleccionados --//
	//$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$operacion = 'modificar';
	require("includes/template.php");
}

function guardarA($objSQLServer, $seccion){
	global $lang;
	$result = controlarCampos();
	
	$campos = implode(',',$result['campos']);
	$valorCampos = implode(',',$result['valorCampos']);
	$options = $result['arr'];
	$mensaje = $result['mensaje'];

	if(!$mensaje){
		require_once 'clases/clsAlertasXGeocercas.php';
        
		$objAlerta = new AlertasXGeocerca($objSQLServer);
		if($objAlerta->insertarRegistro($campos,$valorCampos)){
			$idAlerta = $objSQLServer->dbLastInsertId();

			foreach($objAlerta->diasSemana as $k => $dia){
				if($k >= 1 && $k <= 5){
					if(isset($options['dias']['dia_semana'])){
						$objAlerta->setAlertasPorDias($idAlerta, $k, $options['dias']['dia_semana']['inicio'], $options['dias']['dia_semana']['fin']);
					}
				}
				elseif(isset($options['dias']['fin_semana'])){
					$objAlerta->setAlertasPorDias($idAlerta, $k, $options['dias']['fin_semana']['inicio'], $options['dias']['fin_semana']['fin']);
				}
			}
			
			if($options['referencias']){
				$objAlerta->modificarGeocercasAlerta($idAlerta,$options['referencias']);
			}
            
			if($options['eventos']){
				$objAlerta->modificarEventosAlerta($idAlerta,$options['eventos']);
			}
			
            $objAlerta->modificarMovilesAlerta($idAlerta,$options['moviles']);
			if($options['usuarios']){
				$objAlerta->modificarUsuariosAlerta($idAlerta,$options['usuarios']);
			}
			
			
			//-- Generar Log --//	
			$arrReferenciasUsadas = $objAlerta->obtenerGeocercasAlerta($idAlerta);
			$auxReferencias = $coma = '';			
			if($arrReferenciasUsadas){
				foreach($arrReferenciasUsadas as $item){
					$auxReferencias.= $coma.$item['dato'];
					$coma = '/';
				}	
			}
			
			$arrEventosUsados = $objAlerta->obtenerEventosAlerta($idAlerta);
			$auxEventos = $coma = '';			
			if($arrEventosUsados){
				foreach($arrEventosUsados as $item){
					$auxEventos.= $coma.$item['dato'];
					$coma = '/';
				}	
			}
			
			$arrUsuariosUsados = $objAlerta->obtenerUsuariosAlerta($idAlerta);
			$auxUsuarios = $coma = '';			
			if($arrUsuariosUsados){
				foreach($arrUsuariosUsados as $item){
					$auxUsuarios.= $coma.$item['dato'];
					$coma = ',';
				}	
			}
			if(!empty($_POST['add_tags'])){
				$auxUsuarios.= $coma.$_POST['add_tags'];
			}
			
			$arrMovilesUsados = $objAlerta->obtenerMovilesAlerta($idAlerta);
			$auxMoviles = $coma = '';			
			if($arrMovilesUsados){
				foreach($arrMovilesUsados as $item){
					$auxMoviles.= $coma.$item['dato'];
					$coma = ',';
				}	
			}
			
			$auxTxtLog = 
			str_replace('[REFERENCIA_ALERT]',(empty($arrEntidades)?'-'.ucwords($lang->system->todos).'-':$arrEntidades),
			str_replace('[USER_SEND]',(empty($auxUsuarios)?'-'.ucwords($lang->system->nadie).'-':$auxUsuarios),
			str_replace('[DAYS_ALERT]',((($_POST['lunes_a_viernes'] == 1)?$lang->system->alertas_option_4:'').(($_POST['lunes_a_viernes'] && $_POST['sabados_y_domingos'])?'/':'').(($_POST['sabados_y_domingos'] == 1)?$lang->system->alertas_option_5:'')),
				str_replace('[EVENT_ALERT]',(empty($auxEventos)?'Sin Definir':$auxEventos),
					str_replace('[TRUCK_ALERT]',$auxMoviles,
						str_replace('[NAME_ALERT]',$_POST['txtNombre'],$lang->system->alta_alerta))
			))));
			
			$objAlerta->generarLog(5,$idAlerta,decode($auxTxtLog));
			//-- fin. Generar Log --//	
			
			$mensaje = $lang->message->ok->msj_alta;
			index($objSQLServer, $seccion, $mensaje);
		}
        else{
			$mensaje = $lang->message->error->msj_alta;
			//$datosCargados = datosCargados($options['campos'],$options['valorCampos']);
			alta($objSQLServer, $seccion, $mensaje, $datosCargados);
		}
	}
	else{
		//$datosCargados = datosCargados($options['campos'],$options['valorCampos']);
		alta($objSQLServer, $seccion, $mensaje, $options);
	}
}

function guardarM($objSQLServer, $seccion){
	global $lang;
	
	$idAlerta = (isset($_POST['hidId']))?$_POST['hidId']:0;
	$mensaje = "";
	$set = array();
    
	
	$result = controlarCampos();
	$options = $result['arr'];
	$mensaje = $result['mensaje'];
	
    if(!$mensaje){
		$max = count($result['campos']);
		for($i=0; $i<$max; $i++){            
			if ($result['campos'][$i] != 'al_us_id' ){
                $set[]=$result['campos'][$i].'='.$result['valorCampos'][$i];				
            }
		}
		$set = implode(',',$set);

        require_once 'clases/clsAlertasXGeocercas.php';
		$objAlerta = new AlertasXGeocerca($objSQLServer);
       	
		//-- Generar Log (Info Actual) --//
		$arrEntidades = $objAlerta->obtenerRegistros($idAlerta);
		$arrEntidades = $arrEntidades[0];
		$auxInfoActual = array();
		$auxInfoActual['al_nombre'] = $arrEntidades['al_nombre'];
		$auxInfoActual['al_otros_mail'] = $arrEntidades['al_otros_email'];
		
		$arrAlertasDiaSemana = $objAlerta->obtenerAlertasPorDias($idAlerta);
		$arrAlertasDiaSemana = filtrarDiasDistintos($arrAlertasDiaSemana);
		if(isset($arrAlertasDiaSemana[1]['ds_hora_inicio'])){
        	if($arrAlertasDiaSemana[1]['ds_hora_inicio'] != '00:00:00' || $arrAlertasDiaSemana[1]['ds_hora_fin'] != '00:00:00'){
				$auxInfoActual['lunes_a_viernes'] = 1;
			}
		}
        if(isset($arrAlertasDiaSemana[6]['ds_hora_inicio'])){
			if($arrAlertasDiaSemana[6]['ds_hora_inicio'] != '00:00:00' || $arrAlertasDiaSemana[6]['ds_hora_fin'] != '00:00:00'){
				$auxInfoActual['sabados_y_domingos'] = 1;	
			}
		}
		
		$auxInfoActual['referencias'] = $objAlerta->obtenerGeocercasAlerta($idAlerta);
		$auxInfoActual['eventos'] = $objAlerta->obtenerEventosAlerta($idAlerta);
		$auxInfoActual['usuarios']  = $objAlerta->obtenerUsuariosAlerta($idAlerta);
		$auxInfoActual['moviles'] = $objAlerta->obtenerMovilesAlerta($idAlerta);
		//-- fin. Generar Log (Info Actual) --//
			
	    $cod = $objAlerta->modificarRegistro($set,$idAlerta);
        
		switch($cod){
            case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				modificar($objSQLServer, $seccion, $mensaje,$idAlerta);
				break;
            case 1:
              	foreach($objAlerta->diasSemana as $k => $dia){
					if($k >= 1 && $k <= 5){
						$objAlerta->setAlertasPorDias($idAlerta, $k, $options['dias']['dia_semana']['inicio'], $options['dias']['dia_semana']['fin']);
					}
					else{
						$objAlerta->setAlertasPorDias($idAlerta, $k, $options['dias']['fin_semana']['inicio'], $options['dias']['fin_semana']['fin']);
					}
				}
				
				$objAlerta->modificarGeocercasAlerta($idAlerta,$options['referencias']);
				$objAlerta->modificarEventosAlerta($idAlerta,$options['eventos']);
				$objAlerta->modificarMovilesAlerta($idAlerta,$options['moviles']);
				$objAlerta->modificarUsuariosAlerta($idAlerta,$options['usuarios']);
				
				//-- Generar Log --//	
				$arrEntidades = $objAlerta->obtenerRegistros($idAlerta);
				$arrEntidades = $arrEntidades[0];
		
				$auxInfoUpdate = array();
				$auxInfoUpdate['al_nombre'] = $arrEntidades['al_nombre'];
				$auxInfoUpdate['al_otros_mail'] = $arrEntidades['al_otros_email'];
				
				$arrAlertasDiaSemana = $objAlerta->obtenerAlertasPorDias($idAlerta);
				$arrAlertasDiaSemana = filtrarDiasDistintos($arrAlertasDiaSemana);
				if(isset($arrAlertasDiaSemana[1]['ds_hora_inicio'])){
					if($arrAlertasDiaSemana[1]['ds_hora_inicio'] != '00:00:00' || $arrAlertasDiaSemana[1]['ds_hora_fin'] != '00:00:00'){
						$auxInfoUpdate['lunes_a_viernes'] = 1;
					}
				}
				if(isset($arrAlertasDiaSemana[6]['ds_hora_inicio'])){
					if($arrAlertasDiaSemana[6]['ds_hora_inicio'] != '00:00:00' || $arrAlertasDiaSemana[6]['ds_hora_fin'] != '00:00:00'){
						$auxInfoUpdate['sabados_y_domingos'] = 1;	
					}
				}
				
				$auxInfoUpdate['referencias'] = $objAlerta->obtenerGeocercasAlerta($idAlerta);
				$auxInfoUpdate['eventos'] = $objAlerta->obtenerEventosAlerta($idAlerta);
				$auxInfoUpdate['usuarios']  = $objAlerta->obtenerUsuariosAlerta($idAlerta);
				$auxInfoUpdate['moviles'] = $objAlerta->obtenerMovilesAlerta($idAlerta);
				
				
				$txtActual = $txtUpdate = $coma = NULL;
				if($auxInfoActual['al_nombre'] != $auxInfoUpdate['al_nombre']){
					$txtActual.= $coma.$lang->system->nombre.'['.$auxInfoActual['al_nombre'].']';	
					$txtUpdate.= $coma.$lang->system->nombre.'['.$auxInfoUpdate['al_nombre'].']';
					$coma = ', ';	
				}
				
				if($auxInfoActual['lunes_a_viernes'] != $auxInfoUpdate['lunes_a_viernes'] || $auxInfoActual['sabados_y_domingos'] != $auxInfoUpdate['sabados_y_domingos']){
					$txtActual.= $coma.'Dias['.((($auxInfoActual['lunes_a_viernes'] == 1)?$lang->system->alertas_option_4:'').(($auxInfoActual['lunes_a_viernes'] && $auxInfoActual['sabados_y_domingos'])?'/':'').(($auxInfoActual['sabados_y_domingos'] == 1)?$lang->system->alertas_option_5:'')).']';	
					$txtUpdate.= $coma.'Dias['.((($auxInfoUpdate['lunes_a_viernes'] == 1)?$lang->system->alertas_option_4:'').(($auxInfoUpdate['lunes_a_viernes'] && $auxInfoUpdate['sabados_y_domingos'])?'/':'').(($auxInfoUpdate['sabados_y_domingos'] == 1)?$lang->system->alertas_option_5:'')).']';
					$coma = ', ';	
				}
				//--
				$auxActual = $auxUpdate = array('id'=>array(),'dato'=>array());
				foreach($auxInfoActual['referencias'] as $item){
					array_push($auxActual['id'], $item['id']);
					array_push($auxActual['dato'], $item['dato']);
				};
				foreach($auxInfoUpdate['referencias'] as $item){
					array_push($auxUpdate['id'], $item['id']);
					array_push($auxUpdate['dato'], $item['dato']);
				};
	
				$auxDiff1 = array_diff($auxActual['id'],$auxUpdate['id']);
				$auxDiff2 = array_diff($auxUpdate['id'],$auxActual['id']);
				if(count($auxDiff1) || count($auxDiff2)){
					$txtActual.= $coma.'Referencias['.implode(',',$auxActual['dato']).']';	
					$txtUpdate.= $coma.'Referencias['.implode(',',$auxUpdate['dato']).']';
					$coma = ', ';
				}
				//--
				$auxActual = $auxUpdate = array('id'=>array(),'dato'=>array());
				foreach($auxInfoActual['eventos'] as $item){
					array_push($auxActual['id'], $item['id']);
					array_push($auxActual['dato'], $item['dato']);
				};
				foreach($auxInfoUpdate['eventos'] as $item){
					array_push($auxUpdate['id'], $item['id']);
					array_push($auxUpdate['dato'], $item['dato']);
				};
	
				$auxDiff1 = array_diff($auxActual['id'],$auxUpdate['id']);
				$auxDiff2 = array_diff($auxUpdate['id'],$auxActual['id']);
				if(count($auxDiff1) || count($auxDiff2)){
					$txtActual.= $coma.'Eventos['.implode(',',$auxActual['dato']).']';	
					$txtUpdate.= $coma.'Eventos['.implode(',',$auxUpdate['dato']).']';
					$coma = ', ';
				}
				//--
				$auxActual = $auxUpdate = array('id'=>array(),'dato'=>array());
				foreach($auxInfoActual['moviles'] as $item){
					array_push($auxActual['id'], $item['id']);
					array_push($auxActual['dato'], $item['dato']);
				};
				foreach($auxInfoUpdate['moviles'] as $item){
					array_push($auxUpdate['id'], $item['id']);
					array_push($auxUpdate['dato'], $item['dato']);
				};
	
				$auxDiff1 = array_diff($auxActual['id'],$auxUpdate['id']);
				$auxDiff2 = array_diff($auxUpdate['id'],$auxActual['id']);
				if(count($auxDiff1) || count($auxDiff2)){
					$txtActual.= $coma.'Moviles['.implode(',',$auxActual['dato']).']';	
					$txtUpdate.= $coma.'Moviles['.implode(',',$auxUpdate['dato']).']';
					$coma = ', ';
				}
				//--
				$auxActual = $auxUpdate = array('id'=>array(),'dato'=>array());
				foreach($auxInfoActual['usuarios'] as $item){
					array_push($auxActual['id'], $item['id']);
					array_push($auxActual['dato'], $item['dato']);
				};
				foreach($auxInfoUpdate['usuarios'] as $item){
					array_push($auxUpdate['id'], $item['id']);
					array_push($auxUpdate['dato'], $item['dato']);
				};
	
				$auxDiff1 = array_diff($auxActual['id'],$auxUpdate['id']);
				$auxDiff2 = array_diff($auxUpdate['id'],$auxActual['id']);
				if(count($auxDiff1) || count($auxDiff2) || $auxInfoActual['al_otros_mail'] != $auxInfoUpdate['al_otros_mail']){
					$txtActual.= $coma.'Usuarios['.implode(',',$auxActual['dato']).','.$auxInfoActual['al_otros_mail'].']';	
					$txtUpdate.= $coma.'Usuarios['.implode(',',$auxUpdate['dato']).','.$auxInfoUpdate['al_otros_mail'].']';
					$coma = ', ';
				}
				
				if(!empty($txtUpdate)){
					$objAlerta->generarLog(5,$idAlerta,decode(str_replace('[DATOS_EDITADOS]',$txtUpdate,str_replace('[DATOS_ACTUALES]',$txtActual,$lang->system->edicion_alerta))));
				}
				//-- fin. Generar Log --//	
				
				$mensaje = $lang->message->ok->msj_modificar;
                index($objSQLServer, $seccion, $mensaje);
            break;
           	case 2:
            	$mensaje = $lang->message->error->msj_modificar;
				modificar($objSQLServer, $seccion, $mensaje, $options);
            break;
		}
	}else{
		modificar($objSQLServer, $seccion, $mensaje, $options);
	}
}

function baja($objSQLServer, $seccion){
	global $lang;
	
	require_once 'clases/clsAlertasXGeocercas.php';
	$objAlerta = new AlertasXGeocerca($objSQLServer);
    
	$id = $_POST['hidId']?$_POST['hidId']:0;
	if($id){
		////////////////////////////////////////////////////Protejo contra inyeccion JS////////////////////////////////////////
		$datos['idTipoEmpresa'] = $_SESSION['idTipoEmpresa'];
		$datos['idUsuario'] = $_SESSION['idUsuario'];
		
		$datos['idAlerta'] = $id;
		$arrEntidades = obtenerListadoAlertas($objAlerta, 'delete', $datos);
		$hablitado = validarModificar($arrEntidades,$objSQLServer);
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$arrEntidades = $objAlerta->obtenerRegistros($id);
		$auxInfoBaja['al_nombre'] = $arrEntidades[0]['al_nombre'];
		if($objAlerta->eliminarRegistro($id)){
			$objAlerta->generarLog(5,$id,decode($lang->system->baja_alerta.': '.trim($auxInfoBaja['al_nombre'])));
			$mensaje = $lang->message->ok->msj_baja;
		}    
		else{
			$mensaje = $lang->message->error->msj_baja;
		}
	}
	index($objSQLServer, $seccion, $mensaje);
}


/*
function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
*/
function filtrarDiasDistintos($arrDiasSemana){
    $arrNewDiasSemana = array();
    for ( $i = 0; $i < count($arrDiasSemana); $i++ ){
        $dia = $arrDiasSemana[$i];
        $arrNewDiasSemana[ $dia['ds_dia'] ] = $dia;
    }
    
    return $arrNewDiasSemana;
}

function controlarCampos(){
	global $lang;    
	$campos = array();
	$valorCampos = array();
	$mensaje = '';
	
	$arr['moviles'] = array();
	$arr['referencias'] = array();
	$arr['eventos'] = array();
	$arr['usuarios'] = array();
	$arr['dias'] = array();
	
	$campos[] = 'al_nombre';
	$msjError = checkString(trim($_POST['txtNombre']), 0, 50,$lang->system->nombre,1);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]="''".trim($_POST['txtNombre'])."''";
	
	$campos[] = 'al_us_id';
	$valorCampos[] = $_SESSION['idUsuario'];

	$campos[] = 'al_cl_id';
	$valorCampos[] = $_SESSION['idEmpresa'];
	
	if(!empty($_POST['alert-moviles'])){
		$arr['moviles'] = explode(',',trim($_POST['alert-moviles']));
	}
	else{
		$mensaje.="* ".$lang->system->alertas_txt2."<br/> ";
	}
	
	if(empty($_POST['alert-referencia']) && empty($_POST['alert-evento'])){
		$mensaje.="* ".$lang->system->alertas_txt3."<br/> ";
	}
	else{
		
		if(!empty($_POST['alert-evento'])){
			$arr['eventos'] = explode(',',trim($_POST['alert-evento']));
		}
				
		if(!empty($_POST['alert-referencia'])){
			$arr['referencias'] = explode(',',trim($_POST['alert-referencia']));
			array_push($arr['eventos'],14);
			array_push($arr['eventos'],15);
		}			
	}
	
	if(!empty($_POST['alert-usuario'])){
		$usuarios = explode(',',trim($_POST['alert-usuario']));
		$arr['usuarios_mail'] = array();
		foreach($usuarios as $item){
			if(is_numeric($item)){
				array_push($arr['usuarios'], $item);	
			}
			else{
				array_push($arr['usuarios_mail'], $item);
			}
		}
		
		if(count($arr['usuarios_mail'])){
			$campos[] = 'al_otros_email';
			$valorCampos[] = "''".implode(',',$arr['usuarios_mail'])."''";
		}
	}
	
	$al_duracion = '0';
	$arr['dias']['dia_semana'] = array('inicio'=>'00:00:00', 'fin'=>($_POST['lunes_a_viernes']?'23:59:59':'00:00:00'));
	$arr['dias']['fin_semana'] = array('inicio'=>'00:00:00', 'fin'=>($_POST['sabados_y_domingos']?'23:59:59':'00:00:00'));
	
	if($_POST['hora_ini']){
		$al_duracion = '1';
	}
	
	if(isset($_POST['hora_ini']) && $_POST['lunes_a_viernes']){
		$arr['dias']['dia_semana'] = array('inicio'=>($_POST['hora_ini'].':'.$_POST['min_ini'].':00'), 'fin'=>($_POST['hora_fin'].':'.$_POST['min_fin'].':00'));
		$al_duracion = '0';
	}
	
	if(isset($_POST['hora_ini']) && $_POST['sabados_y_domingos']){
		$arr['dias']['fin_semana'] = array('inicio'=>($_POST['hora_ini'].':'.$_POST['min_ini'].':00'), 'fin'=>($_POST['hora_fin'].':'.$_POST['min_fin'].':00'));
		$al_duracion = '0';
	}
	
	$campos[] = 'al_duracion';
	$valorCampos[] = "''".$al_duracion."''";
		
	return array('mensaje'=>$mensaje,'campos'=>$campos, 'valorCampos'=>$valorCampos, 'arr'=>$arr);
}

function obtenerListadoAlertas($objAlerta, $tipo, $datos){
	if($tipo == 'update' || $tipo == 'delete'){
		$datos['filtro'] = 'getAllReg';
	}	
	
	$arrEntidades = $objAlerta->getAlertas($datos);
	
	return $arrEntidades;	
}