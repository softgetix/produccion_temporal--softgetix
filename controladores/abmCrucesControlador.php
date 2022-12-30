<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
$sinDefaultCSS=$sinDefaultJS=true;

function index($objSQLServer, $seccion, $mensaje=""){
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$filtro = "";
	if($action === 'buscar') {
		busqueda($objSQLServer, $seccion);
	}else if($action === 'calendar') {
		calendar($objSQLServer, $seccion);
	} else if ($action==='popup'){
		alta($objSQLServer,$seccion,$mensaje,array(),true);
	} else {
		$operacion = 'listar';
		$tipoBotonera='LI';
		$cantidadTotalRegistros=0;
		$cantidadCoincidencias=0;
		$demoraBusqueda='00:00';
		$extraCSS=array('css/estilosAbmListadoDefault.css','css/demo_page.css','css/demo_table_jui.css','css/TableTools.css','css/smoothness/jquery-ui-1.8.4.custom.css','css/estilosPopup.css');
		$extraJS=array('js/jquery.autofill.js','js/jquery.dataTables.js','js/jquery.ui.js','js/popupHostFunciones.js');
		
		$extraCSS[] = 'css/fullcalendar.css';
		$extraJS[] = 'js/jquery-ui-1.8.17.custom.min.js';
		$extraJS[] = 'js/fullcalendar.min.js';
		$extraJS[] = 'js/ZeroClipboard.js';
		$extraJS[]='js/abmCrucesListar.js';
		
		require 'includes/template.php';
	}
}


function alta($objSQLServer, $seccion, $mensaje="", $arrEntidades=array(), $popup=false, $arrDestinos = array(), $arrRepeticiones = array()){
	global $lang;

	$operacion = 'alta';
	$tipoBotonera='AM';
	
	$extraJS[]='js/abmCrucesAM.js?3';
	$extraCSS[]='css/estilosWizard.css';
	$extraJS[]='js/jquery.ui.js';
	$extraCSS[]='css/flick/jquery-ui-1.8.14.autocomplete.css';
	$extraJS[]='js/jquery/jquery-ui-1.8.14.autocomplete.min.js';
	$extraJS[]='js/jquery/combobox.js';
	$extraJS[]='js/jqBoxes.js';
	

	$extraCSS[]='css/estilosAbmCruces.css';
	$extraCSS[]='css/jquery.ui.css';
	

	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	if($_SESSION["idTipoEmpresa"] == 2/* OR 1*/) { //CLIENTE
		/*
		$arrEntidades = $objUsuario->obtenerUsuarios(0,"","");
		usuario($arrEntidades,$_SESSION["idUsuario"],0);
		global $arrUsuarios;
		*/
		$arrUsuarios = $objUsuario->obtenerUsuarios(0,'',"");
	}elseif ($_SESSION["idTipoEmpresa"] == 1){ //AGENTE
		$arrUsuarios = $objUsuario->obtenerUsuariosPorEmpresa($_SESSION['idEmpresa'],0);
	} elseif ($_SESSION["idTipoEmpresa"] == 3) {
		$arrUsuarios = $objUsuario->obtenerUsuariosPorEmpresa(0,4);
	} else {
		$arrUsuarios = $objUsuario->obtenerUsuarios(0,'',"");
	}

	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	$id=$_SESSION["idUsuario"];
	$arrMoviles = $objMovil->obtenerMovilesUsuarioCombo($_SESSION["idUsuario"]);
	
	$optionsHora='';
	foreach(range(0,23) as $hora){
		if ($hora<10){
			$hora='0'.$hora;
		}
		$optionsHora.='<option value="'.$hora.'">'.$hora.'</option>';
	}
	$optionsMinutos='';
	foreach(range(0,59) as $min){
		if ($min<10){
			$min='0'.$min;
		}
		$optionsMinutos.='<option value="'.$min.'">'.$min.'</option>';
	}

	$arrAlerta=array(
		array('valor'=>500,'texto'=>'500 mts'),
		array('valor'=>1000,'texto'=>'1000 mts'),
		array('valor'=>2000,'texto'=>'2000 mts'),
		array('valor'=>5000,'texto'=>'5000 mts'),
	);
	require_once 'clases/clsCruces.php';
	$Cond = new Viajes($objSQLServer);
	$Cond_res = $Cond->obtenerCondutores();
	$arrConductores=array();

	for($i=0; $i<count($Cond_res) && $Cond_res;$i++)
	{
		$arrConductores[]=array('co_id'=>$Cond_res[$i]['co_id'],'co_nombre'=>$Cond_res[$i]['co_apellido'].', '.$Cond_res[$i]['co_nombre']);
	}
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	$arrUbicaciones = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION["idEmpresa"],'');
	
	if (!$popup){
		$extraCSS[]='css/estilosPopup.css';
		$extraJS[]='js/popupHostFunciones.js';
		require("includes/template.php");
	}else{
		$extraCSS[]='css/estilosABMDefault.css';
		$extraCSS[]='css/estilosAbmPopup.css';
		$extraCSS[] = 'css/popup.css';
		$extraJS[]='js/popupFunciones.js?1';
		require("includes/frametemplate.php");
		$popup = true;
	}
}

/**
 *@param SqlServer $objSQLServer
 *@param string $seccion
 */
function guardarA($objSQLServer, $seccion){
	//GUARDA LAS ALTAS
	$mensaje='';
	$ret=controlarCampos(1);
	$campos= implode(',',$ret['campos']);
	$valorCampos= implode(',',$ret['valorCampos']);
	$mensaje=$ret['mensaje'];
	$method = isset($_GET['method'])?$_GET['method']:'';

	if(!$mensaje){

		require_once 'clases/clsCruces.php';
		$objViaje=new Viajes($objSQLServer);
		$objViajeDestino=new Viajes($objSQLServer);
		$campoValidador = $ret['campoValidador'];
		$campoValidador	= !empty($campoValidador)?("vi_codigo = '".$campoValidador."'"):$campoValidador;
		if($objViaje->insertarRegistro($campos,$valorCampos,$campoValidador)){
			
			require_once 'clases/clsReferencias.php';
			$objReferencia=new Referencia($objSQLServer);			
			$idViaje=$objSQLServer->dbLastInsertId();
			$camposDestinos = 'vd_vi_id,vd_orden,vd_re_id,vd_ini,vd_fin,vd_estado';
			for ($i = 0;$i < count($ret['arrDestinos']) && $ret['arrDestinos'];$i++) {
				
				$insRetIni = (strlen($ret['arrDestinos'][$i]['vd_ini']) > 6)?$ret['arrDestinos'][$i]['vd_ini']:'0000-00-00 00:00:00';
				$insRetFin = (strlen($ret['arrDestinos'][$i]['vd_fin']) > 6)?$ret['arrDestinos'][$i]['vd_fin']:'0000-00-00 00:00:00';		
				
				$valores = $idViaje;
				$valores .= ",'".$ret['arrDestinos'][$i]['vd_orden'];
				$valores .= "','".$ret['arrDestinos'][$i]['vd_re_id'];
				$valores .= "','".$insRetIni;
				$valores .= "','".$insRetFin;
				$valores .= "',0";
				$objViajeDestino->insertarDestino($camposDestinos,$valores);
				
			}
			
			// Hay repeticiones ?
			if($ret['arrRepeticiones']){
				$salida = 0;
				$Exploini= explode('/',$_POST['txtRepEmpieza']);
				$dia = $Exploini[2].'-'.$Exploini[1].'-'.$Exploini[0];
				$Explofin =  explode('/',$_POST['txtRepTermina']);
				$RepFin =$Explofin[2].'-'.$Explofin[1].'-'.$Explofin[0];
				$campos.=',vi_vi_padre';
				$valorCampos.=','.$idViaje;
					
				for( $dia; $dia <= $RepFin ; $dia=date('Y-m-d',strtotime("$dia +1 day"))){
						$salida++;
						$NumDia = date("N",strtotime($dia));						
						if(in_array($NumDia,$ret['arrRepeticiones'])){
							$campoValidador = $ret['campoValidador'];
							$campoValidador	= !empty($campoValidador)?("vi_codigo = '".$campoValidador."'"):$campoValidador;
							if($objViaje->insertarRegistro($campos,$valorCampos,$campoValidador	)){
								$idViajeRep=$objSQLServer->dbLastInsertId();				
							
								for ($i = 0;$i < count($ret['arrDestinos']) && $ret['arrDestinos'];$i++) {
									
									$arrRetIni =$ret['arrDestinos'][$i]['vd_ini'];
									if( strlen($arrRetIni) > 6){
										$arrRetIni=$dia;
										$horaIni=substr($ret['arrDestinos'][$i]['vd_ini'],10,18);
									}
									else{
										$horaIni='0000-00-00';
										$arrRetIni='00:00:00';
									}
										
									$arrRetFin = $ret['arrDestinos'][$i]['vd_fin'];
									if( strlen($arrRetFin) > 6){
										$arrRetFin = $dia;
										$horaFin=substr($ret['arrDestinos'][$i]['vd_fin'],10,18);
									}
									else{
										$arrRetFin ='0000-00-00';
										$horaFin='00:00:00';
									}
										
									$insRetIni=$arrRetIni.' '.$horaIni;
									$insRetFin=$arrRetFin.' '.$horaFin;
									$valores = $idViajeRep;
									$valores .= ",'".$ret['arrDestinos'][$i]['vd_orden'];
									$valores .= "','".$ret['arrDestinos'][$i]['vd_re_id'];
									$valores .= "','".$insRetIni;
									$valores .= "','".$insRetFin;
									$valores .= "',0";
									$objViajeDestino->insertarDestino($camposDestinos,$valores);
								}
							}
						}
						if ( $salida == 100 ) break;
					}
				}

			$mensaje = 'Viaje cargado con exito';
			if($_POST['popup_ready']=='1')
			{
				?>
				<script>
					window.parent.cerrarPopup();
					window.parent.location.href = "boot.php?c=abmIntermillAP";
				</script>
				<?php
			}else{
				header('Location: boot.php?c=abmCruces');
				exit;
			}
		}else{
			$mensaje = $lang->message->error->msj_alta;
			$ret['valorCampos'] []=$_POST['txtRepEmpieza'];
			$ret['valorCampos'] []=$_POST['txtRepTermina'];
			
			$datosRepeticiones =$ret['valorCampos'];
			$datosCargados=datosCargados($ret['campos'],$datosRepeticiones);
			//redireccionar al alta con los datos cargados.
			if($_POST['popup_ready']!=1){
				alta($objSQLServer, $seccion, $mensaje, $datosCargados);
			}
			else{				
				alta($objSQLServer, $seccion, $mensaje, $datosCargados, true);
			}
		}
	}else{
		//desescapeo valorCampos
		array_walk($ret['valorCampos'],function(&$v){$v=trim($v,"''");});
		$datosCargados[0]=array_combine($ret['campos'],$ret['valorCampos']);
		require_once 'clases/clsReferencias.php';
		$objRef = new Referencia($objSQLServer);
		for($i=0;$i<count($ret['arrDestinos']);$i++){	
		$id_ref=$ret['arrDestinos'][$i]['vd_re_id'];	
		$arrRef = $objRef->obtenerReferencias($id_ref);
		$ret['arrDestinos'][$i]['re_nombre'] = $arrRef[0]['re_nombre'];
		}
		//redireccionar al alta con los datos cargados.
		if($_POST['popup_ready']!=1){
			alta($objSQLServer, $seccion, $mensaje, $datosCargados, false, $ret['arrDestinos'],$ret['arrRepeticiones']);
		}else{
			alta($objSQLServer, $seccion, $mensaje, $datosCargados, true, $ret['arrDestinos'],$ret['arrRepeticiones']);
		?>
		<div id="contenedorIngresarComo" style="text-align:center;padding:0 3px 0 3px;border:3px solid #FFFF4A;background-color:#FFFFAA;font-size:11px;line-height:13px;position:absolute;bottom:50px;left:35%;width:30%;">
			<a href="javascript: cerrarMensaje();">
				<img id="imgCerrarMensaje" src="imagenes/cerrar.png" />
			</a>
		<br/>
			<span style='color:#000000;'><br/><?=$mensaje;?><br/></span><br/>
		</div>
		<?php
			
		}
	}
}

/**
 *funcion unificada que controla la validez de todos los campos del alta/modificacion
 *@return array [mensaje,campos,valorCampos,campoValidador]
 */
function controlarCampos($esAlta = 0){
	
	$campos=array();
	$valorCampos=array();
	$mensaje='';
	$campoValidador='';

	$campos[]='vi_codigo';
	$msjError= checkString(trim($_POST['txtCodigo']), 0, 50,'Codigo',1);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]="''".trim($_POST['txtCodigo'])."''";

	$campoValidador='-1';

	$campos[]='vi_us_id';
	if (isset($_POST['cmbUsuario'])){
		$msjError= checkCombo(trim($_POST['cmbUsuario']),'Usuario',1, 0);
		if ($msjError) $mensaje.="* ".$msjError."<br/> ";
		$valorCampos[]=trim($_POST['cmbUsuario']);
	}else{
		$valorCampos[]=$_SESSION['idUsuario'];
	}
	$us = (int) $valorCampos[1];

	$campos[]='vi_mo_id';
	if ( isset( $_POST['cmbMovil'] ) )
	{
		if ( intval(trim($_POST['cmbMovil'])) != '0' )
		{
			$msjError = checkCombo(trim($_POST['cmbMovil']),'Unidad',1, 0);
			if ($msjError) $mensaje.="* ".$msjError."<br/> ";
		}
		$valorCampos[] = trim($_POST['cmbMovil']);
	}
	else if ( $esAlta )
	{
		require_once 'clases/clsMoviles.php';
		
		global $objSQLServer;
		$objMovil = new Movil($objSQLServer);
		$arrMovil= $objMovil->obtenerAsignacionMovilUsuario($us);
		$valorCampos[]=$arrMovil[0]['um_mo_id'];
	} 
	else
	{
		$valorCampos[] = 0;
	}
	
	$campos[]='vi_co_id';
	$msjError= checkCombo(trim($_POST['cmbConductor']),'Conductor',0, 0);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]=trim($_POST['cmbConductor']);

	$campos[]='vi_observaciones';
	$msjError= checkString(trim($_POST['txtObservaciones']), 0, 1000,'Observaciones',0);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]="''".trim($_POST['txtObservaciones'])."''";

	$campos[]='vi_alerta';
	//$msjError= checkCombo(trim($_POST['cmbAlerta']),'Alerta',0, 0);
	//if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]=0;

	$arrRepeticiones=array();

	$campos[]='vi_rep_lunes';
	if (isset($_POST['chkRepLunes'])){
		$valorCampos[]='1';
		$arrRepeticiones[]=1;
	}else{
		$valorCampos[]='0';
	}
	$campos[]='vi_rep_martes';
	if (isset($_POST['chkRepMartes'])){
		$valorCampos[]='1';
		$arrRepeticiones[]=2;
	}else{
		$valorCampos[]='0';
	}
	$campos[]='vi_rep_miercoles';
	if (isset($_POST['chkRepMiercoles'])){
		$valorCampos[]='1';
		$arrRepeticiones[]=3;
	}else{
		$valorCampos[]='0';
	}
	$campos[]='vi_rep_jueves';
	if (isset($_POST['chkRepJueves'])){
		$valorCampos[]='1';
		$arrRepeticiones[]=4;
	}else{
		$valorCampos[]='0';
	}
	$campos[]='vi_rep_viernes';
	if (isset($_POST['chkRepViernes'])){
		$valorCampos[]='1';
		$arrRepeticiones[]=5;
	}else{
		$valorCampos[]='0';
	}
	$campos[]='vi_rep_sabado';
	if (isset($_POST['chkRepSabado'])){
		$valorCampos[]='1';
		$arrRepeticiones[]=6;
	}else{
		$valorCampos[]='0';
	}
	$campos[]='vi_rep_domingo';
	if (isset($_POST['chkRepDomingo'])){
		$valorCampos[]='1';
		$arrRepeticiones[]=7;
	}else{
		$valorCampos[]='0';
	}
	if ($arrRepeticiones && 0){
		$campos[]='vi_rep_ini';
		$msjError= checkString(trim($_POST['txtRepEmpieza']), 10, 10,'Fecha Empieza',1);
		if (!$msjError){$a=explode('/',trim($_POST['txtRepEmpieza'])); if (!checkdate($a[1],$a[0],$a[2])){$msjError='La fecha de inicio de la repeticion no es v&aacute;lida';}}
		if ($msjError) $mensaje.="* ".$msjError."<br/> ";
		$valorCampos[]="''".datetoDataBase(trim($_POST['txtRepEmpieza']))."''";

		$campos[]='vi_rep_fin';
		$msjError= checkString(trim($_POST['txtRepTermina']), 10, 10,'Fecha Termina',1);
		if (!$msjError){$a=explode('/',trim($_POST['txtRepTermina'])); if (!checkdate($a[1],$a[0],$a[2])){$msjError='La fecha de fin de la repeticion no es v&aacute;lida';}}
		if ($msjError) $mensaje.="* ".$msjError."<br/> ";
		$valorCampos[]="''".datetoDataBase(trim($_POST['txtRepTermina']))."''";
	}
	$arrDestinos=array();
	if (isset($_POST['ref_id']))
	foreach ($_POST['ref_id'] as $i=>$ref_id){
		$arrDestinos[]=array('vd_orden'=>$i,'vd_re_id'=>$ref_id, 'vd_ini'=>($_POST['ini'][$i]!= ' --- ')? dateJqueryPhp($_POST['ini'][$i]):'---','vd_fin'=>($_POST['fin'][$i]!= ' --- ')? dateJqueryPhp($_POST['fin'][$i]):'---');
	}
	if ($arrRepeticiones){
		$arrayFechasDestinos = array();
		for($i=0;$i<count($arrDestinos);$i++)
		{
			$arrDesIni =$arrDestinos[$i]['vd_ini'];
			if( strlen($arrDesIni) < 9)
				{
					$arrDesIni ='00/00/0000 00:00';
				}
				
			$fechIni = substr($arrDesIni,0,10);
			
			$arrDesFin =$arrDestinos[$i]['vd_fin'];
				if( strlen($arrDesFin) < 9)
				{
					$arrDesFin ='00/00/0000 00:00';
				}
			
			$fechFin =  substr($arrDesFin,0,10);
			if(!in_array($fechIni,$arrayFechasDestinos))
			{
					$arrayFechasDestinos []=$fechIni;
			}
			if(!in_array($fechFin,$arrayFechasDestinos))
			{
					$arrayFechasDestinos []=$fechFin;
			}
		}
		if(count($arrayFechasDestinos) != 1)
		{
				$msjError='La fecha de inicio y fin en todos los destinos no son v&aacute;lidas, para repeticiones';
		}
	}
	if(count($arrDestinos) == 0)
	{
			$msjError='No ingreso ningun Destino';
	}
	
		if ($msjError) $mensaje.="* ".$msjError."<br/> ";

	return array('mensaje'=>$mensaje,'campos'=>$campos,'valorCampos'=>$valorCampos, 'campoValidador'=>$campoValidador, 'arrRepeticiones'=>$arrRepeticiones,'arrDestinos'=>$arrDestinos);
}
function volver($objSQLServer, $seccion){
	index($objSQLServer, $seccion);
}


function guardarM($objSQLServer, $seccion){
	global $lang;
	$ret=controlarCampos(0);
	$campos= implode(',',$ret['campos']);
	$valorCampos= implode(',',$ret['valorCampos']);
	
	$mensaje=$ret['mensaje'];
	$method = isset($_GET['method'])?$_GET['method']:'';	
	$vi_id = (int)$_POST['vi_id'];
	
	$idAlerta = $vi_id;
	if(count($ret['arrDestinos']) == 0)	{
			$msjError='No ingreso ningun Destino';
	}
	if(!$mensaje){
		//Armo matriz destinos//
		foreach ($_POST['ref_id'] as $i=>$ref_id){
			$arrDestinos[]=array('vd_old'=>$_POST['es_antiguo'][$i],'vd_orden'=>$i,'vd_re_id'=>$ref_id, 'vd_ini'=>($_POST['ini'][$i]!= ' --- ')? dateJqueryPhp($_POST['ini'][$i]):'---','vd_fin'=>($_POST['fin'][$i]!= ' --- ')? dateJqueryPhp($_POST['fin'][$i]):'---');
		}

		//actualizo Viaje
		
		$sqlQuery = "UPDATE tbl_viajes SET ";
		for($i=0;$i<=5;$i++){
			if( $i<5){
				$sqlQuery .=	$ret['campos'][$i]."=".str_replace("''",'"',$ret['valorCampos'][$i]).", ";
			}else{
				$sqlQuery .=	$ret['campos'][$i]."=".str_replace('"','"',$ret['valorCampos'][$i])." ";
			}
		
		}
		$sqlQuery .=" WHERE vi_id={$vi_id}";
		require_once 'clases/clsCruces.php';
		$query = new Viajes($objSQLServer);
		$resQ=$query->modificarViaje($vi_id,$sqlQuery);
		
		//actualizo Destinos!
		/*1º Borro todos los destinos, que no tengan  fecha inicio o fin SET.
		 */
		 $resQ=$query->borrarDestinos($vi_id);
		 /*2 y 3º Verifico Destino Viejo.
		  * vd_old = 1 // no agregar
		  * vd_old = 0 // agregar
		  * */
		$camposDestinos = 'vd_vi_id,vd_orden,vd_re_id,vd_ini,vd_fin,vd_estado';
		$objViajeDestino=new Viajes($objSQLServer);
	
		 for($i=0;$i<count($arrDestinos);$i++)
		 {
			if($arrDestinos[$i]['vd_old'] != 3)
			{
				$arrRetIni =$arrDestinos[$i]['vd_ini'];
				if( strlen($arrRetIni) > 6)
				{
					$arrRetIni=substr($arrDestinos[$i]['vd_ini'],0,10);
					$horaIni=substr($arrDestinos[$i]['vd_ini'],10,18);
				}else{
					$arrRetIni='0000-00-00';
					$horaIni='00:00:00';
				}
				$arrRetFin = $arrDestinos[$i]['vd_fin'];
			
			if( strlen($arrRetFin) > 6)
				{
					$arrRetFin = substr($arrDestinos[$i]['vd_fin'],0,10);
					$horaFin=substr($arrDestinos[$i]['vd_fin'],10,18);
				}else{
					$arrRetFin ='0000-00-00';
					$horaFin='00:00:00';
				}
			$insRetIni=$arrRetIni.' '.$horaIni;
			$insRetFin=$arrRetFin.' '.$horaFin;
			$insRetIni=$arrRetIni.' '.substr($arrDestinos[$i]['vd_ini'],10,18);
			$insRetFin=$arrRetFin.' '.substr($arrDestinos[$i]['vd_fin'],10,18);
			$valores = $vi_id;
			$valores .= ",'".$arrDestinos[$i]['vd_orden'];
			$valores .= "','".$arrDestinos[$i]['vd_re_id'];
			$valores .= "','".$insRetIni;
			$valores .= "','".$insRetFin;
			$valores .= "',0";
			$objViajeDestino->insertarDestino($camposDestinos,$valores);
			}
		 }
		 
	index($objSQLServer, $seccion, $mensaje="Registro Actualizado");
 		
	}else{
		//redireccionar a modificar con los datos cargados.
		modificar($objSQLServer, $seccion, $mensaje,$idAlerta);
	}
}

function baja($objSQLServer, $seccion){
	$ids = $_POST['chkId'];
	require_once 'clases/clsCruces.php';
	$query = new Viajes($objSQLServer);
	for($i = 0; $i<count($ids);$i++)
	{
		$query->borrarViajes($ids[$i]);
		$query->borrarDestinos($ids[$i]);
	}
	index($objSQLServer, $seccion, $mensaje="Cruces Eliminados");
}

function modificar($objSQLServer, $seccion="", $mensaje="", $idCliente=0){
	require_once 'clases/clsCruces.php';
	$operacion = 'modificar';
	$tipoBotonera='AM';

	//pr($_POST);
	$idViaje = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idCliente)? $idCliente: 0);
	$objViaje = new Viajes($objSQLServer);

	$extraCSS[]='css/estilosWizard.css';
	$extraJS[]='js/wizardFunciones.js';
	$extraJS[]='js/jquery.ui.js';
	$extraCSS[]='css/flick/jquery-ui-1.8.14.autocomplete.css';
	$extraJS[]='js/jquery/jquery-ui-1.8.14.autocomplete.min.js';
	$extraJS[] ='js/jquery.ui.js';
	$extraJS[]='js/jquery/combobox.js';
	

	$extraCSS[]='css/estilosAbmCruces.css';
	$extraCSS[]='css/jquery.ui.css';
	$extraJS[]='js/abmCrucesAM.js';

	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	if($_SESSION["idTipoEmpresa"] == 2){ //CLIENTE
		$arrEntidades = $objUsuario->obtenerUsuarios(0,"","");
		usuario($arrEntidades,$_SESSION["idUsuario"],0);
		global $arrUsuarios;
		//$arrEntidades = $arrUsuarios;
	}elseif ($_SESSION["idTipoEmpresa"] == 1){ //AGENTE
		$arrUsuarios = $objUsuario->obtenerUsuariosPorEmpresa($_SESSION['idEmpresa'],0);
	} elseif ($_SESSION["idTipoEmpresa"] == 3) {
		$arrUsuarios = $objUsuario->obtenerUsuariosPorEmpresa(0,4);
	} else {
		$arrUsuarios = $objUsuario->obtenerUsuarios(0,'',"");
	}

	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	$id=$_SESSION["idUsuario"];
	$arrMoviles = $objMovil->obtenerMovilesUsuarioCombo($_SESSION["idUsuario"]);
	
	$optionsHora='';
	foreach(range(0,23) as $hora){
		if ($hora<10){
			$hora='0'.$hora;
		}
		$optionsHora.='<option value="'.$hora.'">'.$hora.'</option>';
	}
	$optionsMinutos='';
	foreach(range(0,59) as $min){
		if ($min<10){
			$min='0'.$min;
		}
		$optionsMinutos.='<option value="'.$min.'">'.$min.'</option>';
	}

	$arrAlerta=array(
		array('valor'=>500,'texto'=>'500 mts'),
		array('valor'=>1000,'texto'=>'1000 mts'),
		array('valor'=>2000,'texto'=>'2000 mts'),
		array('valor'=>5000,'texto'=>'5000 mts'),
	);
	require_once 'clases/clsCruces.php';
	$Cond = new Viajes($objSQLServer);
	$Cond_res = $Cond->obtenerCondutores();
	$arrConductores=array();

	for($i=0; $i<count($Cond_res)  && $Cond_res;$i++)
	{
		$arrConductores[]=array('co_id'=>$Cond_res[$i]['co_id'],'co_nombre'=>$Cond_res[$i]['co_apellido'].', '.$Cond_res[$i]['co_nombre']);
	}
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	//pr($_SESSION);
	//echo $_SESSION["idEmpresa"];
	$arrUbicaciones = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION["idEmpresa"],'');
	
	$arrEntidades = $objViaje->obtenerRegistros($idViaje);
	//pr($arrEntidades);
	$arrDestinos = $objViaje ->obtenerDestinos($idViaje);
	
	$objRef = new Referencia($objSQLServer);
	//var_dump($arrDestinos);
	if (is_array($arrDestinos)) {
		for($i=0;$i<count($arrDestinos);$i++){	
			$id_ref=$arrDestinos[$i]['vd_re_id'];
			$arrRef = $objRef-> obtenerReferencias($id_ref);
			$arrDestinos[$i]['re_nombre'] = $arrRef[0]['re_nombre'];
		}
	}
	require("includes/template.php");
}

/**
 *@param SqlServer $objSQLServer
 *@param string $seccion
 */
function busqueda($objSQLServer, $seccion) {
	require_once 'clases/clsCruces.php';

	$method 			= isset($_GET['method']) ? $_GET['method'] : 'ajax_json';
	/*FILTROS*/
	$fin     		= isset($_GET['fin']) ? $_GET['fin'] : '';
	$inicio     	= isset($_GET['inicio']) ? $_GET['inicio'] : '';
	
	 if ($inicio == '') {
        $fin    = date("Y-m-d");
        $inicio = date("Y-m-d",time() - 7200);
    } else {
		$inicio = dateToDataBase($inicio);
		$fin 	= ($inicio);
	}
	$idLenguaje			= 1;
	$objViaje = new Viajes($objSQLServer);
	$tiempo=time();
	
	$busqueda 	= $objViaje->obtenerDatos(0,$inicio,$inicio,0);
	$arribos = array();
	$partidas = array();
	
	$cantidadCoincidencias = $cantidadTotalRegistros = count($busqueda);
	$demoraBusqueda = $tiempo - time();
	$demoraBusqueda = date("i:s",$demoraBusqueda);
	
	if($busqueda) {

		limpiarArray($busqueda);
		$temp2->result=$busqueda;
		$temp2->arribos=$arribos;
		$temp2->partidas=$partidas;

		if($method == 'ajax_json') {
			$temp2->msg = 'ok';
			$temp2->status = 1;
			$temp2->cantRegistros=$cantidadTotalRegistros;
			$temp2->cantCoincidencias=$cantidadCoincidencias;
			$temp2->demoraBusqueda=$demoraBusqueda;
			$temp2->fechaSeleccionada=$inicio;
			$temp2->fecha = substr($inicio,8,2) . "/" . substr($inicio,5,2) . "/" . substr($inicio,0,4); //04\/05\/2012

			$temp2->config[0] = 50; // paginas por detalle
			$temp2->config[1] = 50; // paginas por resumen

			$json = json_encode($temp2);
			header('Content-Type: application/json');
			echo $json;

		} else if($method == 'export_xls'){}
		else if($method == 'export_prt'){}
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


function calendar($objSQLServer, $seccion) {
	require_once 'clases/clsCruces.php';

	$fin     		= isset($_GET['end']) ? $_GET['end'] : '';
	$inicio     	= isset($_GET['start']) ? $_GET['start'] : '';
	
    $fin    = date("Y-m-d",$fin);
    $inicio = date("Y-m-d",$inicio);
	$objViaje = new Viajes($objSQLServer);
	
	$busqueda 	= $objViaje->obtenerDatos(0,$inicio,$fin,0);
	$temp = array();
	//print_r($busqueda);
	for ($i = 0; $i < count($busqueda) && $busqueda;$i++) {
		$aux['id'] 		= $busqueda[$i]['vi_id'];
		$aux['title'] 	= $busqueda[$i]['vd_re_id'];
		$aux['start'] 	= date("Y-m-d H:i:s",$busqueda[$i]['ingresoTS']);//'2010-03-09 12:30:00'
		$aux['end'] 	= date("Y-m-d H:i:s",$busqueda[$i]['egresoTS']);//'2010-03-09 12:30:00'
		$aux['allDay'] 	= false;
		$aux['url'] 	= '';
		//if ($i % 5 == 0) $aux['className'] = 'fc-event-skin_a';
		//else  $aux['className'] = '';
		$temp[] = $aux;
	}
	
	if($temp) {
		limpiarArray($temp);
		$json = json_encode($temp);
		header('Content-Type: application/json');
		echo $json;

	}
}

function export_cumplimientoTransportistas($objSQLServer){
	global $lang;
	
	include('clases/clsIntermill.php');
	$objIntermil = new Intermill($objSQLServer);
	$objIntermil->iplan();
	
	$sSQL = "
  		SELECT ae.numero,u.str_nombre as conductor,uu.str_nombre as usuario,us.str_nombre as transportista, v.ve_label as patente,h.accion,(CONVERT(varchar,h.fecha_creado,103) + ' ' + CONVERT(varchar,h.fecha_creado,108)) as fechacreado FROM historico_ae h 
		INNER JOIN ae ae ON (ae.id_ae = h.id_ae) 
		LEFT JOIN seguridad_usuarios u ON (u.id_usuario = h.id_conductor) 
		
		LEFT JOIN identificadores i ON (i.identificador = h.id_transportista)
		LEFT JOIN seguridad_usuarios us ON (us.id_usuario = i.id_transportista)
		 
		INNER JOIN seguridad_usuarios uu ON (uu.id_usuario = h.id_usuario) 
		LEFT JOIN tbl_vehicles v ON (v.ve_id = h.id_vehiculo)
		where fecha_Creado > current_timestamp - 60
		ORDER BY numero, fechacreado desc  ";

	$objRes = $objIntermil->kccSQL->dbQuery($sSQL);
	$arrRows = $objIntermil->kccSQL->dbGetAllRows($objRes,3);
	$arrData = array();
	foreach($arrRows as $arrRow){
		$arrData[] = $arrRow;
	}
	$objIntermil->kccSQL->dbDisconnect();
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle('Cumplimiento de Transportistas')
		->setSubject('Cumplimiento de Transportistas')
		->setDescription('Cumplimiento de Transportistas')
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
		
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $lang->system->nombre)
		->setCellValue('B1', $lang->system->conductor)
		->setCellValue('C1', $lang->system->usuario)
		->setCellValue('D1', $lang->system->transportista)
		->setCellValue('E1', $lang->system->matricula)
		->setCellValue('F1', 'Accion')
		->setCellValue('G1', $lang->system->fecha);
						
	$arralCol = array('A','B','C','D','E','F','G');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','G');
	$objPHPExcel->alignCenter($alingCenterCol);
						
	$i = 2;
	foreach($arrData as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['numero']))
			->setCellValue('B'.$i, encode($row['conductor']))
			->setCellValue('C'.$i, encode($row['usuario']))
			->setCellValue('D'.$i, encode($row['transportista']))							
			->setCellValue('E'.$i, encode($row['patente']))
			->setCellValue('F'.$i, encode($row['accion']))							
			->setCellValue('G'.$i, date('d-m-Y H:i',strtotime(str_replace('/','-',$row['fechacreado']))));
		$i++;
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(' Cumplimiento de Transportistas');
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="cumplimiento-transportistas-'.date('d').date('m').date('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}
