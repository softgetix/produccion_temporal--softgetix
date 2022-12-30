<?php	
	session_start();
	set_time_limit(300);
	include "includes/funciones.php";
	include "includes/config_clientes.php";
	include "includes/conn.php";
	
	if(isset($_GET['file'])){
		$seccion = "abmViajes";	
		$dir = 'viajes/'.$_SESSION['idEmpresa'];
		$archivo = $_GET['file'];
		$type = 'xls';
	}
	elseif(isset($_GET['fileDelivery'])){
		$seccion = 'abmViajesDeliveryAltaMasiva';
		$dir = 'viajes_delivery/'.$_SESSION['idEmpresa'];
		$archivo = $_GET['fileDelivery'];
		$type = 'txt';
	}
	elseif(isset($_GET['tarea'])){
		$seccion = "agendaGPS";	
		$dir = 'AgendaGPS/'.$_SESSION['idEmpresa'];
		$archivo = $_GET['tarea'];
		$type = 'xls';
	}
	elseif(isset($_GET['archivoadt'])){
		$seccion = "adtaltamasiva";	
		$dir = 'adt/adtaltamasiva/'.$_SESSION['idEmpresa'];
		$archivo = $_GET['archivoadt'];
		$type = 'xls';
	}
	elseif(isset($_GET['archivoadt2'])){
		$seccion = "adtaltamasiva2";	
		$dir = 'adt/adtaltamasiva2/'.$_SESSION['idEmpresa'];
		$archivo = $_GET['archivoadt2'];
		$type = 'xls';
	}
	elseif(isset($_GET['archivoadt3'])){
		$seccion = "adtaltamasiva3";	
		$dir = 'adt/adtaltamasiva3/'.$_SESSION['idEmpresa'];
		$archivo = $_GET['archivoadt3'];
		$type = 'xls';
	}
	elseif(isset($_GET['cargafactruaadt'])){
		$seccion = "adtcargafacturas";	
		$dir = 'adt/adtcargafacturas';
		$archivo = $_GET['cargafactruaadt'];
		$type = 'xls';
	}	
	elseif(isset($_GET['pod'])){
		$seccion = "abmViajesDelivery";	
		$dir = 'kcc/'.$_SESSION['idEmpresa'];
		$archivo = $_GET['pod'];
		$type = 'pod';
	}
	else{
		echo "Acceso Denegado.";
		die;
	}
	
	if(isset($_SESSION['DIRCONFIG']) && isset($_SESSION["idUsuario"])){		
		include 'includes/verificarSeccion.php';
	}
	else{
		echo "Acceso Denegado.";
		die;
	}

	//------------------------------------------------------------
	$url = 'Adjuntos/'.$dir.'/'.$archivo;
	if(!file_exists($url)){
		$url = PATH_ATTACH.'/'.$dir.'/'.$archivo;
		if(!file_exists($url)){
			echo "Acceso Denegado";
			exit;
		}
	}

	if($type == 'xls'){
		header("Content-type: application/octet-stream"); 
		header("Content-Type: application/force-download"); 
		header("Content-Disposition: attachment; filename=\"$archivo\"\n"); readfile($url); 
		exit;
	}
	elseif($type == 'txt'){
		$file = basename($url);
		header('Content-Description: File Transfer');
		ob_end_flush();
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename='.$archivo);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($url));
		ob_clean();
		flush();
		readfile($url);
	}
	elseif($type == 'pod'){
		$url = $_SERVER['DOCUMENT_ROOT'].'/dashboard/viajes/images/proof_delivery/'.$archivo;
		if (is_file($url)) {
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=\"$archivo\"");
			readfile($url);
		}	
	}
?>