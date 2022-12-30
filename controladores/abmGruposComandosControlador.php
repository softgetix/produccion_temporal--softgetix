<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje=""){
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$filtro = "";

	if($action == 'buscar') {
		busqueda($objSQLServer, $seccion);
	}
	else {
		require_once 'clases/clsGruposComandos.php';
		$objGrupoComandos = new GrupoComandos($objSQLServer);
		$busqueda2 = $objGrupoComandos->obtenerRegistros(0);
	
		$comandos = $objGrupoComandos->obtenerGrupoComandos();
		$busqueda=array();
		foreach($busqueda2 as $grupo){
			$busqueda[$grupo['gr_id']]=$grupo;
		}
	
		foreach($comandos as $comando){
			$busqueda[$comando['gc_gr_id']]['comandos'][]=$comando['co_codigo'];
		}
	
		foreach($busqueda as &$arr){
			$arr['comandos']=@implode(',',$arr['comandos']);
		}
		sort($busqueda);
		$arrEntidades = $busqueda;
		
		$operacion = 'listar';
		$tipoBotonera='LI';
		$cantidadTotalRegistros=0;
		$cantidadCoincidencias=0;
		$demoraBusqueda='00:00';
		require 'includes/template.php';
	}
}

function busqueda($objSQLServer, $seccion) {
	global $lang;

	require_once 'clases/clsGruposComandos.php';

	$method 			= isset($_GET['method']) ? $_GET['method'] : 'ajax_json';
	$idLenguaje			= 1;

	$objGrupoComandos = new GrupoComandos($objSQLServer);
	$tiempo=time();
	$busqueda2 = $objGrupoComandos->obtenerRegistros(0);

	$comandos = $objGrupoComandos->obtenerGrupoComandos();
	$busqueda=array();
	foreach($busqueda2 as $grupo){
		$busqueda[$grupo['gr_id']]=$grupo;
	}

	foreach($comandos as $comando){
		$busqueda[$comando['gc_gr_id']]['comandos'][]=$comando['co_codigo'];
	}

	foreach($busqueda as &$arr){
		$arr['comandos']=@implode(',',$arr['comandos']);
	}
	sort($busqueda);

	unset($busqueda2,$comandos);

	$cantidadTotalRegistros = $objGrupoComandos->obtenerTotalRegistros();
	
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
			echo $json;

		} 
		
	}
	else if($method == 'ajax_json') {
		$out->msg = $lang->message->sin_resultados;
		$out->status = 2;
		$json = json_encode($out);
		echo $json;
	}
	else{
		echo $lang->message->sin_resultados;
	}
}

function alta($objSQLServer, $seccion, $mensaje=""){
	$operacion = 'alta';
	$tipoBotonera='AM';
	require_once 'clases/clsComandos.php';
	$objComandos=new Comando($objSQLServer);
	$arrBox1=$objComandos->obtenerRegistros();
	require("includes/template.php");
}

function modificar($objSQLServer, $seccion="", $mensaje="", $idGrupoComandos=0){
	require_once 'clases/clsGruposComandos.php';

	$operacion = 'modificar';
	$tipoBotonera='AM';
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idGrupoComandos)? $idGrupoComandos: 0);
	$objGrupoComandos = new GrupoComandos($objSQLServer);
	$arrEntidades = $objGrupoComandos->obtenerRegistros($id);
	$arrBox1=$objGrupoComandos->obtenerComandosDisponibles();
	$arrBox2=$objGrupoComandos->obtenerComandosAsignados();

	$serializado='';
	foreach($arrBox2 as $v){
		$serializado.=$v['co_id'].',';
	}
	$serializado=trim($serializado,',');
	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsGruposComandos.php';
	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0;
	$objGrupoComandos = new GrupoComandos($objSQLServer);
	$idGruposComandos="";
	for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idGruposComandos.=$arrCheks[$i];
		else $idGruposComandos.=$arrCheks[$i].",";
		$objGrupoComandos->eliminarComandosGrupo($arrCheks[$i]);
	}
	if($idGruposComandos){
		if($objGrupoComandos->eliminarRegistro($idGruposComandos)){
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
	$campos="";
	$valorCampos="";
	$mensaje="";

	$campos.='gr_nombre';
	$msjError= checkString(trim($_POST['txtNombre']), 3, 50,$lang->system->nombre,1);
	if (!$msjError) $valorCampos.="''".trim($_POST['txtNombre'])."''";
	else $mensaje.="* ".$msjError."<br/> ";
	$campoValidador=trim($_POST['txtNombre']);

	if(!$mensaje){
		require_once 'clases/clsGruposComandos.php';
		$objGrupoComandos = new GrupoComandos($objSQLServer);
		$campoValidador	= !empty($campoValidador)?("gr_nombre = '".$campoValidador."'"):$campoValidador;
		if($objGrupoComandos->insertarRegistro($campos,$valorCampos,$campoValidador)){
			$registros=$objSQLServer->dbQuery("SELECT @@identity AS id");
			$registro=$objSQLServer->dbGetRow($registros,0,3);
			$id=$registro['id'];
			$objGrupoComandos->insertarComandosGrupo($id,$_POST['hidSerializado']);

			$mensaje = $lang->message->ok->msj_alta;
			index($objSQLServer, $seccion, $mensaje);
		}else{
			$mensaje = $lang->message->error->msj_alta;
			alta($objSQLServer, $seccion, $mensaje);
		}
	}else{
		alta($objSQLServer, $seccion, $mensaje);
	}
}

function guardarM($objSQLServer, $seccion){
	global $lang;
   	$idGrupoComandos = (isset($_POST["hidId"]))? $_POST["hidId"]:"";

	$set="";
	$mensaje="";

	$msjError= checkString(trim($_POST['txtNombre']), 3, 50,$lang->system->nombre,1);
	if (!$msjError) $set.="gr_nombre=''".trim($_POST['txtNombre'])."''";
	else $mensaje.="* ".$msjError."<br/> ";
	$campoValidador=trim($_POST['txtNombre']);


	if(!$mensaje){
		require_once 'clases/clsGruposComandos.php';
	 	$objGrupoComandos = new GrupoComandos($objSQLServer);
		$campoValidador	= !empty($campoValidador)?("gr_nombre = '".$campoValidador."'"):$campoValidador;
	 	$cod = $objGrupoComandos->modificarRegistro($set,$idGrupoComandos,$campoValidador);
		 switch($cod){
			 case 0:
				 $mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				 modificar($objSQLServer, $seccion, $mensaje,$idGrupoComandos);
				 break;
			 case 1:
				$objGrupoComandos->eliminarComandosGrupo($idGrupoComandos);
				$objGrupoComandos->insertarComandosGrupo($idGrupoComandos,$_POST['hidSerializado']);
				 $mensaje = $lang->message->ok->msj_modificar;
				 index($objSQLServer, $seccion, $mensaje);
				 break;
			 case 2:
				 $mensaje = $lang->message->error->msj_modificar;
				 modificar($objSQLServer, $seccion, $mensaje,$idGrupoComandos);
				 break;
		 }
	}
	else{
		modificar($objSQLServer, $seccion, $mensaje,$idGrupoComandos);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
?>