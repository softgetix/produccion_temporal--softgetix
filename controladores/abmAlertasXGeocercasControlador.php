<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
$sinDefaultJS=true;

function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:"";
    
	if($action === 'buscar') {
		busqueda($objSQLServer, $seccion);
	}elseif ($action==='popup'){
		alta( $objSQLServer, $seccion, $mensaje, array(), true );
	}else {
		$operacion = 'listar';
		$tipoBotonera='LI';
		$cantidadTotalRegistros=0;
		$cantidadCoincidencias=0;
		
		
		require_once 'clases/clsAlertasXGeocercas.php';
		$alertas=new AlertasXGeocerca($objSQLServer);
		
		$datos['idTipoEmpresa'] = $_SESSION['idTipoEmpresa'];
		$datos['idUsuario'] = $_SESSION['idUsuario'];
		$datos['idPerfil'] = $_SESSION['idPerfil'];
		$datos['filtro'] = $filtro;
		
		if($_GET['viewAll']){
			$datos['filtro'] = 'getAllReg';
			$filtro = '';
		}
		$arrEntidades = obtenerListadoAlertas($alertas, 'list', $datos);
		$cantRegistros = count($arrEntidades);
		
		if($arrEntidades){
			foreach($arrEntidades as $k => $item){
				$arrEntidades[$k]['accion'] = $alertas->validarEdicionAlertas($item['al_id'], $item['al_us_id'], $arrMoviles);	
			}
		}
		
		$extraCSS=array('css/estilosAbmListadoDefault.css');
		require 'includes/template.php';
	}
}

function busqueda($objSQLServer, $seccion) {
	global $lang;

	require_once 'clases/clsAlertasXGeocercas.php';

	$method = isset($_GET['method']) ? $_GET['method'] : 'ajax_json';

	/* FILTROS */

	$idLenguaje			= 1;
	$objProducto = new AlertasXGeocerca($objSQLServer);
	$tiempo=time();

	$IdEmpresa = ($_SESSION["idTipoEmpresa"] <= 2) ? $_SESSION["idEmpresa"] : 0;
	$idTipoEmpresaExcuyente= ($_SESSION["idTipoEmpresa"] <= 3) ? 4 : 0; //todos excluyen localizart
    
	$busqueda= $objSQLServer->dbQuery('select al_id, al_nombre, al_referencia, al_evento, al_confirmacion from tbl_alertas where al_borrado = 0 and al_us_id='.$_SESSION['idUsuario'].' order by al_id');
	$busqueda=$objSQLServer->dbGetAllRows($busqueda);

	$cantidadCoincidencias = $cantidadTotalRegistros = count($busqueda);
	$demoraBusqueda = $tiempo - time();
	$demoraBusqueda = date("i:s",$demoraBusqueda);

	if($busqueda) {

		limpiarArray($busqueda);
		$temp2->result=$busqueda;

		if($method == 'ajax_json') {
			$temp2->msg = 'ok';
			$temp2->status = 1;
			$temp2->cantRegistros=$cantidadTotalRegistros;
			$temp2->cantCoincidencias=$cantidadCoincidencias;
			$temp2->demoraBusqueda=$demoraBusqueda;

			$temp2->config[0] = 50; // paginas por detalle
			$temp2->config[1] = 50; // paginas por resumen

			$json = json_encode($temp2);
			header('Content-Type: application/json');
			echo $json;
		}
	} else {
		if($method == 'ajax_json') {
			$out->msg = $lang->message->sin_resultados;
			$out->status = 2;
			$json = json_encode($out);
			header('Content-Type: application/json');
			echo $json;
		} else {
			echo $lang->message->sin_resultados;
		}
	}
}

function alta($objSQLServer, $seccion, $mensaje="", $arrEntidades=array(), $popup=false){
	global $lang;
	
	require_once 'clases/clsAlertasXGeocercas.php';
	$objAlertas = new AlertasXGeocerca($objSQLServer);
	
	require_once 'clases/clsReferencias.php';
	$objReferencias = new Referencia($objSQLServer);
	$arrGeocercas= $objReferencias->obtenerReferenciasEmpresa($_SESSION["idEmpresa"]);
	foreach($arrGeocercas as $k => $item){
		$arrGeocercas[$k]['re_nombre'] = htmlentities(encode($item['re_nombre'])); //xq muestra la info por javascript;
	}
    
	require_once("clases/clsDefinicionReportes.php");
	$objEventos2 = new DefinicionReporte($objSQLServer);
    $arrEventos2 = $objEventos2->obtenerEventosCombo2($_SESSION['idUsuario']);
	foreach($arrEventos2 as $k => $item){
		if(tienePerfil(16)){
			if($item['id'] == 14){
				$item['dato'] = $lang->system->egreso;
			}
			if($item['id'] == 15){
				$item['dato'] = $lang->system->ingreso;
			}
		}

		$arrEventos2[$k]['dato'] = htmlentities(encode($item['dato'])); //xq muestra la info por javascript;
	}

	require_once 'clases/clsMoviles.php';
	$objMovil=new Movil($objSQLServer);
	$arrTemp = $objMovil->obtenerMovilesUsuarioAlerta($_SESSION['idUsuario']);	
	
	$arrMoviles=array();
	foreach($arrTemp as $movil){
		$arrMoviles[$movil['mo_id']]=$movil;
		$arrMoviles[$movil['mo_id']]['mo_matricula']= htmlentities(encode($movil['mo_matricula'])); //xq muestra la info por javascript;
		$arrMoviles[$movil['mo_id']]['grupos']=array();
	}
	
	$arrMovilGrupo = $objMovil->obtenerMovilesConGrupo($_SESSION['idUsuario']);
	if ($arrMovilGrupo){
		foreach($arrMovilGrupo as $movil){
			if($arrMoviles[$movil['mo_id']]){
			$arrMoviles[$movil['mo_id']]['grupos'][]='grp_'.$movil['um_grupo'];
			}
		}
	}
	unset($arrTemp,$arrMovilGrupo);
	
	$arrGruposMoviles=$objMovil->obtenerGruposMovilesUsuario(0,'',$_SESSION['idUsuario'],1);	
	
	$arrUsuarios = $objAlertas->obtenerUsuarios($_SESSION['idUsuario']);
		
	$arrDesplegable = $objSQLServer->dbQuery('select al_id as id, al_nombre as dato from tbl_alertas where al_borrado=0 and al_us_id='.$_SESSION['idUsuario'].' order by al_nombre');
	$arrDesplegable = $objSQLServer->dbGetAllRows($arrDesplegable);
	
	$arrGruposGeocercas = $objReferencias->getReferenciasGrupos();
	foreach($arrGruposGeocercas as $k => $item){
		$arrGruposGeocercas[$k]['rg_nombre'] = htmlentities(encode($item['rg_nombre'])); //xq muestra la info por javascript;
	}
	
	$idEmpresa = ($_SESSION['idEmpresa'] == 74) ? 0 : $_SESSION['idEmpresa'];
	
	if ($idEmpresa == 0) {
		$tipos[1] = " - Agente";
		$tipos[2] = " - Cliente";
		$tipos[3] = " - Tesma";
	}
	else {
		$tipos[1] = "";
		$tipos[2] = "";
		$tipos[3] = "";
	}
			
	if (!empty($_POST['hid_lstGeocercasElegidas'])){
		$arrGeocercasUsadas=explode(',',$_POST['hid_lstGeocercasElegidas']);
		array_walk($arrGeocercasUsadas,function(&$v){
			$v=array('id'=>$v);
		});
	}
	if (!empty($_POST['hid_lstAlertasElegidas'])){
		$arrEventosUsados=explode(',',$_POST['hid_lstAlertasElegidas']);
		array_walk($arrEventosUsados,function(&$v){
			$v=array('id'=>$v);
		});
	}
		
	if (!empty($_POST['hid_lstMovilesElegidos'])){
		$arrMovilesUsados=explode(',',$_POST['hid_lstMovilesElegidos']);
		array_walk($arrMovilesUsados,function(&$v){
			$v=array('id'=>$v);
		});
	}
	if (!empty($_POST['hid_lstUsuariosElegidos'])){
		$arrUsuariosUsados=explode(',',$_POST['hid_lstUsuariosElegidos']);
		array_walk($arrUsuariosUsados,function(&$v){
			$v=array('id'=>$v);
		});
	}
	
	$extraCSS=array('css/estilosABMDefault.css', 'css/estilosWizard.css');
	$extraJS[]='js/jqBoxes.js';
	$extraJS[]='js/abmAlertasXGeocercasAM.js';
	$operacion = 'alta';
	$tipoBotonera='AM';
  	if (!$popup){
		require("includes/template.php");}
    else{
		$extraCSS[]='css/estilosAbmPopup.css';
		$extraJS[]='js/popupFunciones.js?1';
		require("includes/frametemplate.php");
	}
}

function modificar($objSQLServer, $seccion="", $mensaje="", $idCliente=0){
	global $lang;
	require_once 'clases/clsAlertasXGeocercas.php';
	$objAlerta = new AlertasXGeocerca($objSQLServer);
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idCliente)? $idCliente: 0);
	
	////////////////////////////////////////////////Protección contra inyección JS//////////////////////////////////////////
	$datos['idTipoEmpresa'] = $_SESSION['idTipoEmpresa'];
	$datos['idUsuario'] = $_SESSION['idUsuario'];
	$datos['idPerfil'] = $_SESSION['idPerfil'];
	$datos['idAlerta'] = $id;
	$arrEntidades = obtenerListadoAlertas($objAlerta, 'update', $datos);
	$hablitado = validarModificar($arrEntidades,$objSQLServer);
	////////////////////////////////////////////////Protección contra inyección JS//////////////////////////////////////////
	
	$arrEntidades = $objAlerta->obtenerRegistros($id);
	if($arrEntidades[0]['al_duracion'] == 1 ){
        $arrEntidades[0]['DiaDeSemanaCustom'] = false;
        $arrEntidades[0]['DiaDeSemana_hora_inicio'] = '00:00:00';
        $arrEntidades[0]['DiaDeSemana_hora_fin'] = '23:59:59';
        
        $arrEntidades[0]['FinDeSemanaCustom'] = false;
        $arrEntidades[0]['FinDeSemana_hora_inicio'] = '00:00:00';
        $arrEntidades[0]['FinDeSemana_hora_fin'] = '23:59:59';
    }
    // ---  Personalizada  ---
    elseif ( $arrEntidades[0]['al_duracion'] == 2 ){
        $arrAlertasDiaSemana = $objAlerta->obtenerAlertasPorDias($id);
		$arrAlertasDiaSemana = filtrarDiasDistintos($arrAlertasDiaSemana);
		
        $arrDiaLunes  = $arrAlertasDiaSemana[1];
        $arrDiaSabado = $arrAlertasDiaSemana[6];
        
	    $arrEntidades[0]['DiaDeSemanaCustom'] = false;
		if(isset($arrDiaLunes['ds_hora_inicio'])){
        	if($arrDiaLunes['ds_hora_inicio'] != '00:00:00' || $arrDiaLunes['ds_hora_fin'] != '00:00:00'){
				$arrEntidades[0]['DiaDeSemanaCustom'] = true;
				$arrEntidades[0]['DiaDeSemana_hora_inicio'] = millitia_time($arrDiaLunes['ds_hora_inicio']);
				$arrEntidades[0]['DiaDeSemana_hora_fin'] = millitia_time($arrDiaLunes['ds_hora_fin']);
			}
		}
		
        $arrEntidades[0]['FinDeSemanaCustom'] = false;
       	if(isset($arrDiaSabado['ds_hora_inicio'])){
			if($arrDiaSabado['ds_hora_inicio'] != '00:00:00' || $arrDiaSabado['ds_hora_fin'] != '00:00:00'){
				$arrEntidades[0]['FinDeSemanaCustom'] = true;
				$arrEntidades[0]['FinDeSemana_hora_inicio'] = millitia_time($arrDiaSabado['ds_hora_inicio']);
				$arrEntidades[0]['FinDeSemana_hora_fin'] = millitia_time($arrDiaSabado['ds_hora_fin']);
			}
		}
    }
	
	require_once 'clases/clsReferencias.php';
	$objReferencias = new Referencia($objSQLServer);
	$arrGeocercas = $objReferencias->obtenerReferenciasEmpresa($_SESSION["idEmpresa"]);
    foreach($arrGeocercas as $k => $item){
		$arrGeocercas[$k]['re_nombre'] = htmlentities(encode($item['re_nombre'])); //xq muestra la info por javascript;
	}
	
	require_once("clases/clsDefinicionReportes.php");
	$objEventos2 = new DefinicionReporte($objSQLServer);
    $arrEventos2 = $objEventos2->obtenerEventosCombo2($_SESSION['idUsuario']);
    foreach($arrEventos2 as $k => $item){
		if(tienePerfil(16)){
			if($item['id'] == 14){
				$item['dato'] = $lang->system->egreso;
			}
			if($item['id'] == 15){
				$item['dato'] = $lang->system->ingreso;
			}
		}
		$arrEventos2[$k]['dato'] = htmlentities(encode($item['dato'])); //xq muestra la info por javascript;
	}

	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	$arrTemp = $objMovil->obtenerMovilesUsuarioAlerta($_SESSION['idUsuario']);
	$arrMoviles=array();
	foreach ($arrTemp as $movil){
		$arrMoviles[$movil['mo_id']] = $movil;
		$arrMoviles[$movil['mo_id']]['mo_matricula']= htmlentities(encode($movil['mo_matricula'])); //xq muestra la info por javascript;
		$arrMoviles[$movil['mo_id']]['grupos']=array();
	}
	
	$arrMovilGrupo = $objMovil->obtenerMovilesConGrupo($_SESSION['idUsuario']);	
	if ($arrMovilGrupo){
		foreach($arrMovilGrupo as $movil){
			if($arrMoviles[$movil['mo_id']]){
				$arrMoviles[$movil['mo_id']]['grupos'][]='grp_'.$movil['um_grupo'];
			}
		}
	}
	unset($arrTemp,$arrMovilGrupo);
	
	$arrGruposMoviles = $objMovil->obtenerGruposMovilesUsuario(0,'',$_SESSION['idUsuario'],1);
	
	$arrGruposGeocercas = $objReferencias->getReferenciasGrupos();
	foreach($arrGruposGeocercas as $k => $item){
		$arrGruposGeocercas[$k]['rg_nombre'] = htmlentities(encode($item['rg_nombre'])); //xq muestra la info por javascript;
	}
		
	$arrUsuarios = $objAlerta->obtenerUsuarios($_SESSION['idUsuario']);
	$idEmpresa = ($_SESSION['idEmpresa'] == 74) ? 0 : $_SESSION['idEmpresa'];
	
	if ($idEmpresa == 0) {
		$tipos[1] = " - Agente";
		$tipos[2] = " - Cliente";
		$tipos[3] = " - Tesma";
	} else {
		$tipos[1] = "";
		$tipos[2] = "";
		$tipos[3] = "";
	}
	
	if ($arrEntidades[0]['al_referencia']){
		$arrGeocercasUsadas = $objAlerta->obtenerGeocercasAlerta($id);
		if (is_array($arrGeocercasUsadas)) {
			foreach ($arrGeocercasUsadas as $tmp) {
				$arrGeocercasUsadas2[] = $tmp['id'];
			}
		}
	}
        
	if ($arrEntidades[0]['al_evento']){
		$arrEventosUsados = $objAlerta->obtenerEventosAlerta($id);
		if (is_array($arrEventosUsados)) {
			foreach ($arrEventosUsados as $tmp) {
				$arrEventosUsados2[] = $tmp['id'];
			}
		}
	}
		
	if ($arrEntidades[0]['al_vel_max'] == 999){
		$arrEntidades[0]['al_vel_max'] = 0;
	}
	
	$arrMovilesUsados = $objAlerta->obtenerMovilesAlerta($id);
	
	$arrUsuariosUsados = $objAlerta->obtenerUsuariosAlerta($id);
	if (is_array($arrUsuariosUsados)) {
		foreach ($arrUsuariosUsados as $tmp) {
			$arrUsuariosUsados2[] = $tmp['id'];
		}
	}
	
	$extraCSS=array('css/estilosWizard.css');
	$extraJS[]='js/jqBoxes.js';
	$extraJS[]='js/abmAlertasXGeocercasAM.js';
	$operacion = 'modificar';
	$tipoBotonera='AM';
	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsAlertasXGeocercas.php';
	$objAlerta = new AlertasXGeocerca($objSQLServer);
    $idAlertas = ($_POST["chkId"]) ? $_POST["chkId"] : array();

	////////////////////////////////////////////////////Protejo contra inyeccion JS////////////////////////////////////////
	$datos['idTipoEmpresa'] = $_SESSION['idTipoEmpresa'];
	$datos['idUsuario'] = $_SESSION['idUsuario'];
	$datos['idPerfil'] = $_SESSION['idPerfil'];
	
	foreach($idAlertas as $ids){
		 $datos['idAlerta'] = $ids;
		 $arrEntidades = obtenerListadoAlertas($objAlerta, 'delete', $datos);
		 $hablitado = validarModificar($arrEntidades,$objSQLServer);
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	if(count($idAlertas) > 0){
        $bOverallSuccess = true;
        
        foreach ( $idAlertas as $alertaID ){
            if(!$objAlerta->eliminarRegistro($alertaID)){
                $bOverallSuccess = $bOverallSuccess && false;
            }    
        }
        
        if($bOverallSuccess){
            $mensaje = $lang->message->ok->msj_baja;
        }
        else{
            $mensaje = $lang->message->error->msj_baja;
        }
	}
	index($objSQLServer, $seccion, $mensaje);
}

function guardarA($objSQLServer, $seccion){
	global $lang;
	$ret = controlarCampos();
	
	$campos= implode(',',$ret['campos']);
	$valorCampos= implode(',',$ret['valorCampos']);
	$mensaje=$ret['mensaje'];

	$method = isset($_GET['method'])?$_GET['method']:'';

	if(!$mensaje){
		require_once 'clases/clsAlertasXGeocercas.php';
        
		$objAlerta = new AlertasXGeocerca($objSQLServer);
		if($objAlerta->insertarRegistro($campos,$valorCampos)){//,$ret['campoValidador']
			$idAlerta = $objSQLServer->dbLastInsertId();
            
			foreach($objAlerta->diasSemana as $k => $dia){
				if($k >= 1 && $k <= 5){
					$objAlerta->setAlertasPorDias($idAlerta, $k, $ret['dias']['dia_semana']['inicio'], $ret['dias']['dia_semana']['fin']);
				}
				else{
					$objAlerta->setAlertasPorDias($idAlerta, $k, $ret['dias']['fin_semana']['inicio'], $ret['dias']['fin_semana']['fin']);
				}
			}
			
			if ($ret['geocercas']){
				$objAlerta->modificarGeocercasAlerta($idAlerta,$ret['geocercas']);
			}
            
			if ($ret['eventos']){
				$objAlerta->modificarEventosAlerta($idAlerta,$ret['eventos']);
			}
			
            $objAlerta->modificarMovilesAlerta($idAlerta,$ret['moviles']);
			$objAlerta->modificarUsuariosAlerta($idAlerta,$ret['usuarios']);
            
			$mensaje = $lang->message->ok->msj_alta;
            index($objSQLServer, $seccion, $mensaje);
		}
        else{
			$mensaje = $lang->message->error->msj_alta;
			$datosCargados=datosCargados($ret['campos'],$ret['valorCampos']);
			alta($objSQLServer, $seccion, $mensaje, $datosCargados);
		}
	}else{
		array_walk($ret['valorCampos'],function(&$v){$v=trim($v,"''");});
		$datosCargados[0]=array_combine($ret['campos'],$ret['valorCampos']);
		alta($objSQLServer, $seccion, $mensaje, $datosCargados);
	}
}

function guardarM($objSQLServer, $seccion){
	global $lang;
	$idAlerta = (isset($_POST["hidId"])) ? $_POST["hidId"] : "";
	$mensaje = "";
	$set = array();
    
	$ret = controlarCampos();
	$mensaje=$ret['mensaje'];

    if(!$mensaje){
		$max=count($ret['campos']);
		for($i=0;$i<$max;$i++){            
			if ( $ret['campos'][$i] != 'al_us_id' ){
                $set[]=$ret['campos'][$i].'='.$ret['valorCampos'][$i];				
            }
		}

		$set = implode(',',$set);
        require_once 'clases/clsAlertasXGeocercas.php';
		$objAlerta = new AlertasXGeocerca($objSQLServer);
       
        $cod = $objAlerta->modificarRegistro($set,$idAlerta);
        
		switch($cod){
            case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				modificar($objSQLServer, $seccion, $mensaje,$idAlerta);
				break;
            case 1:
               
			   	foreach($objAlerta->diasSemana as $k => $dia){
					if($k >= 1 && $k <= 5){
						$objAlerta->setAlertasPorDias($idAlerta, $k, $ret['dias']['dia_semana']['inicio'], $ret['dias']['dia_semana']['fin']);
					}
					else{
						$objAlerta->setAlertasPorDias($idAlerta, $k, $ret['dias']['fin_semana']['inicio'], $ret['dias']['fin_semana']['fin']);
					}
				}
			   
                if($ret['geocercas']){
                    $objAlerta->modificarGeocercasAlerta( $idAlerta, $ret['geocercas'] );
                }
                
                if($ret['eventos']){
                    $objAlerta->modificarEventosAlerta( $idAlerta, $ret['eventos'] );
                }
                
                $objAlerta->modificarMovilesAlerta($idAlerta,$ret['moviles']);
                $objAlerta->modificarUsuariosAlerta($idAlerta,$ret['usuarios']);

				$mensaje = $lang->message->ok->msj_modificar;
                index($objSQLServer, $seccion, $mensaje);
            break;
           	case 2:
            	$mensaje = $lang->message->error->msj_modificar;
				modificar($objSQLServer, $seccion, $mensaje,$idAlerta);
            break;
		}
	}else{
		modificar($objSQLServer, $seccion, $mensaje,$idAlerta);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}

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
	$campos=array();
	$valorCampos=array();
	$mensaje='';
	$campoValidador='-1';
	$geocercas=array();
	$eventos=array();
	$usuario=array();
	$moviles=array();
    
	$campos[]='al_nombre';
	$msjError= checkString(trim($_POST['txtNombre']), 0, 50,$lang->system->nombre,1);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]="''".trim($_POST['txtNombre'])."''";

	$campos[]='al_referencia';
	$valorCampos[] = 1;
	
	$campos[]='al_dentro_fuera';

	if ($_POST['hid_tipoAlerta'] != 'b') {
				
		if (isset($_POST['hid_radDentroFuera'])) {
			$msjError= checkNumber(trim($_POST['hid_radDentroFuera']), 0, 1,$lang->system->alertas_txt29,1);
			if ($msjError) {
				$mensaje.="* ".$lang->system->alertas_txt29."<br/> ";
			}
			if($_POST['hid_tipoAlerta'] == 'a'){
				$valorCampos[] = 1;	
			}
			else{
				$valorCampos[] = (float)($_POST['hid_radDentroFuera']);		
			}
		}
		else {			
			$valorCampos[] = 0;		
		}
								
		$campos[]='al_vel_min';
		if(isset($_POST['hid_txtVelMin']) && $_POST['hid_txtVelMin'] != '' ){
			$msjError= checkNumber(trim($_POST['hid_txtVelMin']), 1, 999,$lang->system->alertas_txt31,1);
			if($msjError){
                $mensaje .= "* ".$msjError."<br/>";
            }
			$valorCampos[]=(float)($_POST['hid_txtVelMin']);
		}
        else{
			$valorCampos[]=0;
		}

		$campos[]='al_vel_max';
		if (isset($_POST['hid_txtVelMax']) && $_POST['hid_txtVelMax'] != ''){ 
			$msjError= checkNumber(trim($_POST['hid_txtVelMax']), 1, 999,$lang->system->alertas_txt32,1); 
			if ($msjError) $mensaje.="* ".$msjError."<br/> ";			
			$valorCampos[]=(float)($_POST['hid_txtVelMax']);			
		}
		else{
			$valorCampos[]=999;
		}
	}else{
		$valorCampos[]=-1;
	}
	
	$campos[]='al_evento';
	$valorCampos[]= 1;

	$campos[]='al_confirmacion';
	$msjError= checkNumber(trim($_POST['radAlConfirmacion']), 0, 1,$lang->system->alertas_txt33,1);
	if ($msjError){
		$mensaje.="* ".$msjError."<br/> ";
    }
	$valorCampos[]=(float)($_POST['radAlConfirmacion']);
    
	if(isset( $_POST['chkActiva'])){
        $campos[]='al_activa';
		$valorCampos[] = (int) $_POST['chkActiva'];
    }
    
	$campos[]='al_tipo';
	$valorCampos[]="''".trim($_POST['hid_tipoAlerta'])."''";

	$campos[]='al_otros_email';
	$msjError= checkString(trim($_POST['hid_txtOtrosEmail']), 0, 2000,$lang->system->alertas_txt34,0);
	if ($msjError) {
		$mensaje.="* ".$msjError."<br/> ";
	}
	$valorCampos[]="''".trim($_POST['hid_txtOtrosEmail'])."''";

	$msjError= checkString(trim($_POST['hid_lstUsuariosElegidos'],', '), 1, 99999,$lang->system->usuarios, empty($_POST['txtOtrosEmail']));
	$usuarios=explode(',',trim($_POST['hid_lstUsuariosElegidos'],', '));
	
	$moviles=explode(',',trim($_POST['hid_lstMovilesElegidos'],', '));
	
	$msjError= checkString(trim($_POST['hid_lstAlertasElegidas'],', '), 1, 99999,$lang->system->eventos,1);
	if ($msjError) {
            $mensaje.="* ".$lang->system->alertas_txt35."<br/> ";
    }
	$eventos=explode(',',trim($_POST['hid_lstAlertasElegidas'],', '));
	
    if($_POST['hid_tipoAlerta'] != 'b'){
		$msjError= checkString(trim($_POST['hid_lstGeocercasElegidas'],', '), 1, 99999,$lang->system->eventos,1);
        if($msjError){
			$mensaje.="* ".$lang->system->alertas_txt36."<br/> ";
		}
        $geocercas=explode(',',trim($_POST['hid_lstGeocercasElegidas'],', '));
	}
        
	$campos[]='al_us_id';
	$valorCampos[]=$_SESSION['idUsuario'];

	$campos[]='al_cl_id';
	$valorCampos[]=$_SESSION['idEmpresa'];
	
	// CONFIG DE ALERTA (Todos los dias - Personalizada)
    $tipo_duracion = $_POST['radDuration'];
    $dias = array();
    switch($tipo_duracion ){
        case 1: //Todos los dias
            $dias['dia_semana']['inicio'] = '00:00:00';
            $dias['dia_semana']['fin']    = '23:59:59';
            
            $dias['fin_semana']['inicio'] = '00:00:00';
            $dias['fin_semana']['fin']    = '23:59:59';
        break;
        case 2: //Personalizar
            if(isset($_POST['chkLuVi']) || isset($_POST['chkSabDo'])){
				if(isset($_POST['chkLuVi'])){//lunes a viernes
					$dias['dia_semana']['inicio'] = $_POST['cboDurationLuVi_desde'];
					$dias['dia_semana']['fin']    = $_POST['cboDurationLuVi_hasta'];
				}
				
				if(isset($_POST['chkSabDo'])){//sábados y domingos
					$dias['fin_semana']['inicio'] = $_POST['cboDurationSabDo_desde'];
					$dias['fin_semana']['fin']    = $_POST['cboDurationSabDo_hasta'];
				}
			}
			else{//-- configuro para todos los dias ya que el usuario no configuro la personalizacion
				
				$tipo_duracion = 1;
				$dias['dia_semana']['inicio'] = '00:00:00';
				$dias['dia_semana']['fin']    = '23:59:59';
				
				$dias['fin_semana']['inicio'] = '00:00:00';
				$dias['fin_semana']['fin']    = '23:59:59';
			}
			/*if (
                ( !@$_POST['chkLuVi'] && !@$_POST['chkSabDo'] ) ||
                ( @$_POST['chkLuVi']  == 'on' 
                &&   @$_POST['chkSabDo'] == 'on'
                && $_POST['cboDurationLuVi_desde'] == '00:00:00'
                && $_POST['cboDurationLuVi_hasta'] == '23:59:59'
                && $_POST['cboDurationSabDo_desde'] == '00:00:00'
                && $_POST['cboDurationSabDo_hasta'] == '23:59:59'
                )
               )
            {
                $tipo_duracion = 1;
                
                $dias['dia_semana']['inicio'] = '00:00:00';
                $dias['dia_semana']['fin']    = '23:59:59';

                $dias['fin_semana']['inicio'] = '00:00:00';
                $dias['fin_semana']['fin']    = '23:59:59';
            }
            else
            {
                // Rango dia de semana
                if ( @$_POST['chkLuVi'] == 'on' )
                {
                    $dias['dia_semana']['inicio'] = $_POST['cboDurationLuVi_desde'];
                    $dias['dia_semana']['fin']    = $_POST['cboDurationLuVi_hasta'];
                }
                else
                {
                    $dias['dia_semana']['inicio'] = '00:00:00';
                    $dias['dia_semana']['fin']    = '23:59:59';
                }    

                // Rango dia de semana
                if ( @$_POST['chkSabDo'] == 'on' )
                {
                    $dias['fin_semana']['inicio'] = $_POST['cboDurationSabDo_desde'];
                    $dias['fin_semana']['fin']    = $_POST['cboDurationSabDo_hasta'];
                }
                else
                {
                    $dias['fin_semana']['inicio'] = '00:00:00';
                    $dias['fin_semana']['fin']    = '23:59:59';
                }
				*/    
		break;
       // }
    }
    
    $campos[]='al_duracion';
    $valorCampos[] = (int) $tipo_duracion;
	
	return array('mensaje'=>$mensaje,'campos'=>$campos,'valorCampos'=>$valorCampos, 'campoValidador'=>$campoValidador,'geocercas'=>$geocercas,'eventos'=>$eventos,'usuarios'=>$usuarios,'moviles'=>$moviles,'dias'=>$dias);
}

function obtenerListadoAlertas($objAlerta, $tipo, $datos){
	if($tipo == 'update' || $tipo == 'delete'){
		$datos['filtro'] = 'getAllReg';
	}	
	
	$arrEntidades = $objAlerta->getAlertas($datos);
	
	return $arrEntidades;	
}

function export_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST["hidFiltro"]))?$_POST["hidFiltro"] : '');
	
	require_once 'clases/clsAlertasXGeocercas.php';
	$objAlertas = new AlertasXGeocerca($objSQLServer);
   
   	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$datos['idTipoEmpresa'] = $_SESSION['idTipoEmpresa'];
	$datos['idUsuario'] = $_SESSION['idUsuario'];
	$datos['idPerfil'] = $_SESSION['idPerfil'];
	$datos['filtro'] = $txtFiltro;
	if(empty($txtFiltro)){
		$datos['filtro'] = 'getAllReg';
	}
	$arrEntidades = obtenerListadoAlertas($objAlertas, 'list', $datos);
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->$seccion)
		->setSubject($lang->menu->$seccion)
		->setDescription($lang->menu->$seccion)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->nombre)
		->setCellValue('B1',$lang->system->descripcion);
	
	if(!tienePerfil(16)){	
		$objPHPExcel->setActiveSheetIndex(0)	
			->setCellValue('C1',$lang->system->usuario_creador)
			->setCellValue('D1',$lang->system->alerta_por_geocercas)
			->setCellValue('E1',$lang->system->alerta_por_eventos)
			->setCellValue('F1',$lang->system->requiere_confirmacion);
	}
		
	$arralCol = !tienePerfil(16)?array('A','B','C','D','E','F'):array('A','B');
	$objPHPExcel->setFormatoRows($arralCol);
	/*$alingCenterCol = array('B','C','D','E');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','F','G');
	$objPHPExcel->alignLeft($alingLeftCol);
	*/
	
	$arrSiNo[0] = $lang->system->no;
	$arrSiNo[1] = $lang->system->si;
					
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['al_nombre']))
			->setCellValue('B'.$i, encode(strip_tags($row['descripcion'])));
		
		if(!tienePerfil(16)){	
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('C'.$i, encode($row['usuario']))
				->setCellValue('D'.$i, $arrSiNo[$row['al_referencia']])
				->setCellValue('E'.$i, $arrSiNo[$row['al_evento']])
				->setCellValue('F'.$i, $arrSiNo[$row['al_confirmacion']]);
		}
		$i++;	
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->$seccion);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ','-',$lang->menu->$seccion)).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}


