<?
$dirAndroid = 'https://www.adtfindu.com/adt/mobile/ADT_FindU.apk';
//$dirAndroid = 'https://play.google.com/store/apps/details?id=ar.com.localizart.android.report&hl=es-419';
$dirBlackBerrry = 'http://appworld.blackberry.com/webstore/content/34029889';

LogIP();

require_once "detectorDispositivo.php";
if(esAndroid()){ 	
	header("Location: ".$dirAndroid.""); 
}
elseif(esBlackBerry()){ 	
	header("Location: ".$dirBlackBerrry.""); 
}
else{	
	echo "Dispositivo no Soportado";
	exit; 
}

//--------------------
function LogIP(){
	$file = 'logDescargas.txt';
	$ipadress = $_SERVER['REMOTE_ADDR'];
	$date = date('d/m/Y h:i:s');
	//$webpage = $_SERVER['SCRIPT_NAME'];
	$browser = $_SERVER['HTTP_USER_AGENT'];

	$fp = fopen($file, 'a');
	fwrite($fp, $date.' - ['.$ipadress.'] ['.$browser."]\r\n");
	fclose($fp);
}
//--------------------
?>


