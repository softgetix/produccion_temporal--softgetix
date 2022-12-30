<?php
//$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje=""){

	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);

	$strSQL = "SELECT oea_id, vt_id, vt_nombre, origen.re_id, origen.re_nombre as origen, destino.re_id, destino.re_nombre  as destino"
		. " FROM tbl_oea WITH(NOLOCK) "
		. " INNER JOIN tbl_referencias as origen WITH(NOLOCK) ON origen.re_id = oea_origen_re_id "
		. " INNER JOIN tbl_referencias as destino WITH(NOLOCK) ON destino.re_id = oea_destino_re_id "
		. " INNER JOIN tbl_viajes_tipo WITH(NOLOCK) ON vt_id = oea_vt_id "
		. " WHERE oea_borrado = 0 ";

	$strSQL.= !empty($filtro) ? " AND (vt_nombre LIKE '%{$filtro}%' OR origen.re_nombre LIKE '%{$filtro}%' OR destino.re_nombre LIKE '%{$filtro}%') " : "";
	$arrListado = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL),3);

	$operacion = 'listar';
   	$tipoBotonera='LI';
   	require("includes/template.php");
}

function getValoresMostrar($objSQLServer){
	$data = array();

	$strSQL = "EXEC Robot_OEA_tipo_viajes_disponibles";
	$data['arrTipoViaje'] = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL),3);
		
	$strSQL = "EXEC Robot_OEA_origen_disponibles";
	$data['arrOrigen'] = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL),3);
		
	$strSQL = "EXEC Robot_OEA_fronteras_disponibles";
	$data['arrDestino'] = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL),3);
		
	$strSQL = "EXEC Robot_OEA_rutas_disponibles";
	$data['arrRutasDisp'] = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL),3);
	
	//--Completar forma correcta combo de rutas
	if($_POST['cmbRutasAsociadas']){
		$data['arrRutasAsoc'] = array();
		foreach($data['arrRutasDisp'] as $k => $item){
			if(in_array($item['re_id'],$_POST['cmbRutasAsociadas'])){
				array_push($data['arrRutasAsoc'], $data['arrRutasDisp'][$k]);
				unset($data['arrRutasDisp'][$k]);
			}
		}	
	}
	//--

	return $data;
} 

function alta($objSQLServer, $seccion, $mensaje=""){ 
	$data = getValoresMostrar($objSQLServer);

	$saved['tipoViaje'] = isset($_POST['tipoViaje']) ? (int)$_POST['tipoViaje'] : NULL;
	$saved['origen'] = isset($_POST['origen']) ? (int)$_POST['origen'] : NULL;
	$saved['destino'] = isset($_POST['destino']) ? (int)$_POST['destino'] : NULL;

	$extraJS[]='js/boxes.js';
    
	$operacion = 'alta';
	$tipoBotonera='AM';
	
	require("includes/template.php");
}

function guardarA($objSQLServer, $seccion){
	global $lang;
	
	$mensaje = validarCampos();

	if(empty($mensaje)){

		$params = array(
			'oea_vt_id' => (int)$_POST['tipoViaje']
			,'oea_origen_re_id' => (int)$_POST['origen']
			,'oea_destino_re_id' => (int)$_POST['destino']
		);
		$id_oea = $objSQLServer->dbQueryInsert($params, 'tbl_oea');
		if($id_oea){
			foreach($_POST['cmbRutasAsociadas'] as $item){
				$params = array(
					'oear_oea_id' => $id_oea
					,'oear_ruta_re_id' => $item
				);
				$objSQLServer->dbQueryInsert($params, 'tbl_oea_rutas');
			}

			$mensaje = $lang->message->ok->msj_alta;
			index($objSQLServer, $seccion, $mensaje);
		}
		else{
			$mensaje = $lang->message->error->msj_alta;
			alta($objSQLServer, $seccion, $mensaje);
		}		
	}
	else{
		alta($objSQLServer, $seccion, $mensaje);
	}
}

function modificar($objSQLServer, $seccion, $mensaje="", $id=0){
	$id = isset($_POST["hidId"]) ? $_POST["hidId"]: ( $id  ? $id : 0);

	$strSQL = "SELECT oea_vt_id, oea_origen_re_id, oea_destino_re_id FROM tbl_oea WITH(NOLOCK) WHERE oea_id = {$id}";
	$oea = $objSQLServer->dbGetRow($objSQLServer->dbQuery($strSQL),0,3);	

	$strSQL = "SELECT oear_ruta_re_id FROM tbl_oea_rutas WITH(NOLOCK) WHERE oear_oea_id = {$id}";
	$oea_rutas = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL),3);

	$saved['tipoViaje'] = $oea['oea_vt_id'];
	$saved['origen'] = $oea['oea_origen_re_id'];
	$saved['destino'] = $oea['oea_destino_re_id'];

	if($oea_rutas){
		foreach($oea_rutas as $item){
			$_POST['cmbRutasAsociadas'][] = $item['oear_ruta_re_id'];
		}
	}
	
	$data = getValoresMostrar($objSQLServer);

	$extraJS[]='js/boxes.js';
    
	$operacion = 'modificar';
   	$tipoBotonera='AM';
	
	   require("includes/template.php");
}

function guardarM($objSQLServer, $seccion){
	global $lang;
	
	$id = isset($_POST['hidId']) ? $_POST['hidId'] : NULL;
	
	$mensaje = validarCampos();

	if(empty($mensaje)){

		$params = array(
			'oea_vt_id' => (int)$_POST['tipoViaje']
			,'oea_origen_re_id' => (int)$_POST['origen']
			,'oea_destino_re_id' => (int)$_POST['destino']
		);
		if($objSQLServer->dbQueryUpdate($params, 'tbl_oea', 'oea_id = '.$id)){
			
			$strSQL = "DELETE FROM tbl_oea_rutas WHERE oear_oea_id = ".$id;
			$objSQLServer->dbQuery($strSQL);

			foreach($_POST['cmbRutasAsociadas'] as $item){
				$params = array(
					'oear_oea_id' => $id
					,'oear_ruta_re_id' => $item
				);
				$objSQLServer->dbQueryInsert($params, 'tbl_oea_rutas');
			}

			$mensaje = $lang->message->ok->msj_modificar;
			index($objSQLServer, $seccion, $mensaje);
		}
		else{
			$mensaje = $lang->message->error->msj_modificar;
			modificar($objSQLServer, $seccion, $mensaje, $id);
		}		
	}
	else{
		modificar($objSQLServer, $seccion, $mensaje, $id);
	}
}

function validarCampos(){
	$mensaje = NULL;

	if(empty($_POST['tipoViaje'])){
		$mensaje.="* Tipo de Viaje es requerido <br/> ";
	}

	if(empty($_POST['origen'])){
		$mensaje.="* Origen es requerido <br/> ";
	}

	if(empty($_POST['destino'])){
		$mensaje.="* Frontera Destino es requerido <br/> ";
	}

	if(empty($_POST['cmbRutasAsociadas'])){
		$mensaje.="* Debe asocial una o más rutas<br/> ";
	}

	return $mensaje;
}

function baja($objSQLServer, $seccion){
	global $lang;
	
	$id = isset($_POST['hidId']) ? $_POST['hidId'] : NULL;

	$mensaje = $lang->message->error->msj_baja;
	if($id){
		if($objSQLServer->dbQueryUpdate(array('oea_borrado' => 1), 'tbl_oea', 'oea_id = '.$id)){
			$mensaje = $lang->message->ok->msj_baja;
		}
	}

   	index($objSQLServer, $seccion, $mensaje);
}
?>