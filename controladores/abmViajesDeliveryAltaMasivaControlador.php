<?php
set_time_limit(1800);
function index($objSQLServer, $seccion, $mensaje="", $errorLog=""){
	
	
	if($_REQUEST['action'] == 'procesar_viajes'){
		procesarViajes($objSQLServer, $seccion);
		exit;
	}
	
	//traer todos los archivos de la carpeta adjuntos/viajes:
	$arrArchivos = array();	
	$adj = PATH_ATTACH.'/viajes_delivery/'.$_SESSION['idEmpresa'];
	if(file_exists($adj)){
		$handle = opendir($adj);
		while($file = readdir($handle)){
 			if(!is_dir($file)){
				//array_push($arrArchivos, $file);
				//-- Mecanismo de ordenamiento por fecha de modificación --//
				$data[] = array($file, date("Y-m-d H:i:s",filemtime($adj.'/'.$file)));
        		$dates[] = date("Y-m-d H:i:s",filemtime($adj.'/'.$file));
				//-- --//
			}
		}

		closedir($handle);
		//rsort($arrArchivos); //lo ordena de mayor a menor por valor
		//-- Mecanismo de ordenamiento por fecha de modificación --//
		array_multisort($dates, SORT_DESC, $data);
		foreach($data as $arc){
			array_push($arrArchivos, $arc[0]);
		}
		//-- --//
		
		$arrFiles = array();
		foreach($arrArchivos as $item){
			$arr = explode('.',$item);
			$arr = explode('-',$arr[0]);
			$idx = ($arr[2] == 'error_log')?9:((int)$arr[2]< 9?(int)$arr[2]:0);
			$arrFiles[$arr[0].'-'.$arr[1]][$idx] = $item;
			//$arrFiles[$arr[0].'-'.$arr[1]][] = $item;
			ksort($arrFiles[$arr[0].'-'.$arr[1]]);
		}
		$arrArchivos = $arrFiles;
	}
	
	
	
	$extraJS[] = 'js/abmViajesDeliveryFunciones.js';
	require("includes/template.php");
}

function uploadArvhivosTXT_SAP($objSQLServer, $seccion){
	global $lang;
	
	$msj = "";
	$error = false;
	
	if(isset($_FILES['archivo'])){
		foreach($_FILES['archivo']['error'] as $k=>$error){
			switch((int)$error){
				case UPLOAD_ERR_OK:
					$extens = extension_archivo($_FILES['archivo']['name'][$k]);
					if($extens != "txt") {
						$msj = $lang->message->interfaz_generica->msj_extension_txt.' [File: '.$_FILES['archivo']['name'][$k].']';
						$error = true;
					}
				break;
				case UPLOAD_ERR_INI_SIZE:	//Superó directiva "upload_max_filesize" del php.ini.
				case UPLOAD_ERR_FORM_SIZE:	//Superó MAX_FILE_SIZE (HTML).
					$msj = $lang->message->interfaz_generica->msj_tamanio_max_archivo.' [File: '.$_FILES['archivo']['name'][$k].']';
					$error = true;
				break;
				case UPLOAD_ERR_PARTIAL:	//Archivo truncado.
				case UPLOAD_ERR_NO_TMP_DIR:	//Pérdida del tmp_dir.
				case UPLOAD_ERR_CANT_WRITE:	//No se pudo escribir en disco.
					$msj = $lang->message->error->upload_archivo;
					$error = true;
				break;
				case UPLOAD_ERR_EXTENSION:	//La extensión es incorrecta.
					$msj = $lang->message->interfaz_generica->msj_extension_txt.' [File: '.$_FILES['archivo']['name'][$k].']';
					$error = true;
				break;
				case UPLOAD_ERR_NO_FILE:	//Archivo no subido.
					$msj = $lang->message->interfaz_generica->msj_seleccione_archivo;
				break;
			}
			
			if($error){
				break;	
			}
		}
		
		if(!$error){
			include_once 'clases/clsViajes.php';
			include_once 'clases/clsViajesDelivery.php';
			$objViaje = new ViajesDelivery($objSQLServer);
	
			$resultado = $objViaje->importarTxt_KCCPERU($_FILES['archivo']);
			switch ($resultado){
				case ERR_SIN_PERMISOS:
					$msj = $lang->message->interfaz_generica->msj_permiso_update_archivo;
				break;
				default:
					$msj = $resultado['msg'];
					$errorLog = $resultado['errorLog'];
				break;
			}
		}
		
	}

	index($objSQLServer, $seccion, $msj, $errorLog);
}

function uploadArvhivoExcel($objSQLServer, $seccion){
	global $lang;
	
	$msj = "";
	$error = false;
	
	if(isset($_FILES['archivo'])){
		$error_file = $_FILES['archivo']['error'];
		$name = $_FILES['archivo']['name'];
		switch((int)$error_file){
			case UPLOAD_ERR_OK:
				$extens = extension_archivo($name);
				if($extens != "xls" && $extens != 'xlsx') {
					$msj = $lang->message->interfaz_generica->msj_extension_xls.' [File: '.$name.']';
					$error = true;
				}
			break;
			case UPLOAD_ERR_INI_SIZE:	//Superó directiva "upload_max_filesize" del php.ini.
			case UPLOAD_ERR_FORM_SIZE:	//Superó MAX_FILE_SIZE (HTML).
				$msj = $lang->message->interfaz_generica->msj_tamanio_max_archivo.' [File: '.$name.']';
				$error = true;
			break;
			case UPLOAD_ERR_PARTIAL:	//Archivo truncado.
			case UPLOAD_ERR_NO_TMP_DIR:	//Pérdida del tmp_dir.
			case UPLOAD_ERR_CANT_WRITE:	//No se pudo escribir en disco.
				$msj = $lang->message->error->upload_archivo;
				$error = true;
			break;
			case UPLOAD_ERR_EXTENSION:	//La extensión es incorrecta.
				$msj = $lang->message->interfaz_generica->msj_extension_xls.' [File: '.$name.']';
				$error = true;
			break;
			case UPLOAD_ERR_NO_FILE:	//Archivo no subido.
				$msj = $lang->message->interfaz_generica->msj_seleccione_archivo;
				$error = true;
			break;
		}

		if(!$error){
			include_once 'clases/clsViajes.php';
			include_once 'clases/clsViajesDelivery.php';
			$objViaje = new ViajesDelivery($objSQLServer);
	
			switch($_SESSION['idEmpresa']){
				case 4875://Tasa
                                    $resultado = $objViaje->importarExcel_TASA($_FILES['archivo']);
				break;
				case 4835://Arauco
                                case 10827://KCC-Arg-Bis    
                                    $resultado = $objViaje->importarExcel_ARAUCO($_FILES['archivo']);
				break;
			}
			
			switch ($resultado){
				case ERR_SIN_PERMISOS:
					$msj = $lang->message->interfaz_generica->msj_permiso_update_archivo;
				break;
				default:
					$msj = $resultado['msg'];
					$errorLog = $resultado['errorLog'];
				break;
			}
		}
		
	}

	index($objSQLServer, $seccion, $msj, $errorLog);
}

function export_errorLog($objSQLServer, $seccion){
	header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($_POST['errorLog']));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($_POST['errorLog']));
    readfile($_POST['errorLog']);
    exit;
}

function procesarViajes($objSQLServer, $seccion){
	global $lang;
	
	if($_POST){
		if(empty($_POST['procesar'])){
			$mensaje = 'Debe seleccionar una de las opciones.';	
		}
		else{
			
			include_once 'clases/clsViajes.php';
			include_once 'clases/clsViajesDelivery.php';
			$objViaje = new ViajesDelivery($objSQLServer);
			
			if($objViaje->setRetroactivoViajes($_POST['desde_'.$_POST['procesar']], $_POST['hasta_'.$_POST['procesar']], 1)){
				?><script language="javascript">top.window.location='boot.php?c=abmViajesDelivery';</script><?php 	
				exit;
			}
			else{
				$mensaje = 'NO se ha activado la programaci&oacute;n, vuelva a intentarlo.';	
			}
		}
	}
	
	$ayer = date('Y-m-d',strtotime('-1 day',strtotime(getFechaServer('Y-m-d'))));
	$arrAux[] = array('option' => 'Los &uacute;ltimos 7 d&iacute;as', 'desde'=>date('Y-m-d',strtotime('-7 day',strtotime($ayer))), 'hasta'=>$ayer, 'value'=>'ultima_semana');
	$arrAux[] = array('option' => 'Los &uacute;ltimos 15 d&iacute;as', 'desde'=>date('Y-m-d',strtotime('-15 day',strtotime($ayer))), 'hasta'=>$ayer, 'value'=>'ultima_quincena');
	$arrAux[] = array('option' => 'Los &uacute;ltimos 30  d&iacute;as', 'desde'=>date('Y-m-d',strtotime('-30 day',strtotime($ayer))), 'hasta'=>$ayer, 'value'=>'ultimo_mes');
	
	
	$extraCSS[] = 'css/estilosAbmPopup.css';
    $extraCSS[] = 'css/popup.css';
	$extraCSS[] = 'css/estilosABMDefault.css';
    $extraJS[] = 'js/popupFunciones.js?1';
	$operacion = 'Listar';
	$tipoBotonera = 'AM';
    
	$seccion_job = $seccion;
	$seccion = 'abmViajesProcesar';
	require("includes/frametemplate.php");	
}
?>