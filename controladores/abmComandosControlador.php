<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje=""){
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:"";

	require_once 'clases/clsComandos.php';
	$objComando = new Comando($objSQLServer);
	$arrEntidades = $objComando->obtenerRegistros(0, $filtro);
		
	$extraCSS=array('css/demo_page.css','css/demo_table_jui.css','css/TableTools.css','css/smoothness/jquery-ui-1.8.4.custom.css');
	$extraJS=array('js/jquery.dataTables.js','js/jquery.ui.js','js/tableConvert.js');
	$extraJS[]='js/jquery/jquery-ui-1.8.14.autocomplete.min.js';
	$extraJS[]='js/jquery/combobox.js';
	$operacion = 'listar';
	$tipoBotonera='LI';
	require 'includes/template.php';
}


function alta($objSQLServer, $seccion, $mensaje="", $arrEntidades=array()){
	require_once 'clases/clsComandos.php';
	$comando = new Comando($objSQLServer);
	$cmbModeloEquipo = $comando->getModeloEquipos();

	$operacion = 'alta';
	$tipoBotonera='AM';
	require("includes/template.php");
}

function modificar($objSQLServer, $seccion="", $mensaje="", $idComando=0){
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idComando)? $idComando: 0);

	require_once 'clases/clsComandos.php';
	$comando = new Comando($objSQLServer);
	$arrEntidades = $comando->obtenerRegistros($id);
	$cmbModeloEquipo = $comando->getModeloEquipos();

	$operacion = 'modificar';
	$tipoBotonera='AM';
	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsComandos.php';
	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0;
	$objComando = new Comando($objSQLServer);
	$idComandos="";
	for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idComandos.=$arrCheks[$i];
		else $idComandos.=$arrCheks[$i].",";
	}
	if($idComandos){
		if($objComando->eliminarRegistro($idComandos)){
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

	$ret=controlarCampos();

	$campos= implode(',',$ret['campos']);
	$valorCampos= implode(',',$ret['valorCampos']);
	$mensaje=&$ret['mensaje'];
	$campoValidador=&$ret['campoValidador'];

	if(!$mensaje){
		require_once 'clases/clsComandos.php';
		$objComando = new Comando($objSQLServer);
		$campoValidador	= !empty($campoValidador)?("co_nombre = '".$campoValidador."'"):$campoValidador;
		if($objComando->insertarRegistro($campos,$valorCampos,$campoValidador)){
			$mensaje = $lang->message->ok->msj_alta;
			index($objSQLServer, $seccion, $mensaje);
		}else{
			$mensaje = $lang->message->error->msj_alta;
			$datosCargados=datosCargados($ret['campos'],$ret['valorCampos']);
			alta($objSQLServer, $seccion, $mensaje,$datosCargados);
		}
	}else{
		$datosCargados=datosCargados($ret['campos'],$ret['valorCampos']);
		alta($objSQLServer, $seccion, $mensaje,$datosCargados);
	}
}

function guardarM($objSQLServer, $seccion){
	global $lang;
   	$idComando = (isset($_POST["hidId"]))? $_POST["hidId"]:"";

	$set="";
	$mensaje="";

	$ret=controlarCampos();
	
	$mensaje=$ret['mensaje'];
	$campoValidador = $ret['campoValidador'];

	if(!$mensaje){
		$max=count($ret['campos']);
		for($i=0;$i<$max;$i++){
			$set[]=$ret['campos'][$i].'='.$ret['valorCampos'][$i];
		}
		$set=implode(',',$set);

		require_once 'clases/clsComandos.php';
		$objComando = new Comando($objSQLServer);
		$campoValidador	= !empty($campoValidador)?("co_nombre = '".$campoValidador."'"):$campoValidador;
		$cod = $objComando->modificarRegistro($set,$idComando,$campoValidador);
		switch($cod){
			case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				modificar($objSQLServer, $seccion, $mensaje,$idComando);
				break;
			case 1:
				$mensaje = $lang->message->ok->msj_modificar;
				index($objSQLServer, $seccion, $mensaje);
				break;
			case 2:
				$mensaje = $lang->message->error->msj_modificar;
				modificar($objSQLServer, $seccion, $mensaje,$idComando);
				break;
		}
	}else{
		//redireccionar al alta con los datos cargados.
		modificar($objSQLServer, $seccion, $mensaje,$idComando);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}

function controlarCampos(){
	global $lang;
	$campos=array();
	$valorCampos=array();
	$mensaje='';

	$campos[]='co_nombre';
	$msjError= checkString(trim($_POST['txtNombre']), 3, 50,$lang->system->nombre,1);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]="''".trim($_POST['txtNombre'])."''";

	$campos[]='co_codigo';
	$msjError= checkString(trim($_POST['txtCodigo']), 1, 250,$lang->system->comando,1);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]="''".trim($_POST['txtCodigo'])."''";
	$campoValidador=trim($_POST['txtCodigo']);

	$campos[]='co_instrucciones';
	$msjError= checkString(trim($_POST['txtInstrucciones']), 0, 255,$lang->system->instrucciones,0);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]="''".trim($_POST['txtInstrucciones'])."''";

	$campos[]='co_respuesta_ok';
	$msjError= checkString(trim($_POST['txtResOK']), 0, 999999,$lang->system->respuesta_correcta,0);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]="''".trim($_POST['txtResOK'])."''";

	$campos[]='co_tipo';
	$msjError= checkNumber(trim($_POST['radTipo']), 0, 1,$lang->system->tipo_comando,1);
	if ($msjError) $mensaje.="* ".$msjError."<br/> ";
	$valorCampos[]=(float)($_POST['radTipo']);
	
	$campos[] = 'co_favorito';
    $msjError = checkCombo((int)$_POST['cmbFavorito'], $lang->system->favorito, 0, 0);
    $valorCampos[] = (int)$_POST['cmbFavorito'];
	if ($msjError){
        $mensaje.="* " . $msjError . "<br/> ";}
		
	$campos[] = 'co_mo_id';
    $msjError = checkCombo((int)$_POST['cmbModeloEquipo'], $lang->system->modelo_de_equipo,1, 0);
    $valorCampos[] = (int)$_POST['cmbModeloEquipo'];
	if ($msjError){
        $mensaje.="* " . $msjError . "<br/> ";}	

	return array('mensaje'=>$mensaje,'campos'=>$campos,'valorCampos'=>$valorCampos, 'campoValidador'=>0); /*$campoValidador*/
}

function export_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST["hidFiltro"]))?$_POST["hidFiltro"] : '');
	
	require_once 'clases/clsComandos.php';
	$objComando = new Comando($objSQLServer);
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$arrEntidades = $objComando->obtenerRegistros(0, $txtFiltro);
	
	
	
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
		->setCellValue('B1',$lang->system->comando)
		->setCellValue('C1',$lang->system->instrucciones);
	
	$arralCol = array('A','B','C');
	$objPHPExcel->setFormatoRows($arralCol);
	//$alingCenterCol = array('');
	//$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','B','C');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['co_nombre']))
			->setCellValue('B'.$i, encode($row['co_codigo']))
			->setCellValue('C'.$i, encode($row['co_instrucciones']));
			
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
