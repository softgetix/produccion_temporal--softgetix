<?php
ini_set('memory_limit', '-1'); 
set_time_limit(10800);
//error_reporting(E_ALL);
 
function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	$_GET['action'] = isset($_REQUEST['action'])?$_REQUEST['action'] : $popup;
	
	//traer todos los archivos de la carpeta adjuntos/viajes:
	$arrArchivos = array();	
	$adj = PATH_ATTACH.'/adt/adtaltamasiva3/'.$_SESSION['idEmpresa'];
	if(file_exists($adj)){
		$handle = opendir($adj);
		sort($handle);
		while (false !== ($file = readdir($handle))){
		   if ($file != "." && $file != "..") {
				///array_push($arrArchivos, $file);
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
	}
	
	require("includes/template.php");
}

function uploadExcel($objSQLServer, $seccion){
	global $lang;

	$msj = "";
	if (isset($_FILES["archivo"])) {
		switch ((int)$_FILES["archivo"]["error"]) {
			case UPLOAD_ERR_OK:
				$extens = extension_archivo($_FILES["archivo"]["name"]);
				if ($extens != 'xls' && $extens != 'xlsx'){
					$msj = $lang->message->interfaz_generica->msj_extension_xls;
				}
				else{
					require_once 'clases/ADT.php';
    				$objADT = new ADT($objSQLServer);
					
					$resultado = $objADT->Importar_Excel3($_FILES['archivo']);
					switch ($resultado) {
						case ERR_SIN_PERMISOS:
							$msj = $lang->message->interfaz_generica->msj_permiso_update_archivo;
						break;
						default:
							$msj = $resultado;
						break;
					}
				}
			break;
			case UPLOAD_ERR_INI_SIZE:	//Superó directiva "upload_max_filesize" del php.ini.
			case UPLOAD_ERR_FORM_SIZE:	//Superó MAX_FILE_SIZE (HTML).
				$msj = $lang->message->interfaz_generica->msj_tamanio_max_archivo;
			break;
			case UPLOAD_ERR_PARTIAL:	//Archivo truncado.
			case UPLOAD_ERR_NO_TMP_DIR:	//Pérdida del tmp_dir.
			case UPLOAD_ERR_CANT_WRITE:	//No se pudo escribir en disco.
				$msj = $lang->message->error->upload_archivo;
			break;
			case UPLOAD_ERR_EXTENSION:	//La extensión es incorrecta.
				$msj = $lang->message->interfaz_generica->msj_extension_xls;
			break;
			case UPLOAD_ERR_NO_FILE:	//Archivo no subido.
				$msj = $lang->message->interfaz_generica->msj_seleccione_archivo;
			break;
		}
	}

	index($objSQLServer, $seccion, $msj);
}
?>