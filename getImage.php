<?php
error_reporting(0);
header_remove('X-Powered-By');
include('includes/funciones.php');

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT"); 
header ("Cache-Control: no-cache, must-revalidate");  
header ("Pragma: no-cache");  

$sFile = isset($_GET['file']) ? $_GET['file'] : die();
$sCaption = isset($_GET['caption'])?decode($_GET['caption']):die();

$arr_txt = array('�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','&');
$arr_rep = array('a','a','a','a','a','A','A','A','A','e','e','e','e','E','E','E','E','i','i','i','i','I','I','I','I','o','o','o','o','O','O','O','O','u','u','u','u','U','U','U','U','n','N','c','C','y');
$sCaption = str_replace($arr_txt,$arr_rep,$sCaption);

//$sPathMode = isset($_GET['pathmode'])?trim($_GET['pathmode']) : 'rel';
$arrImage = isset($_GET['arrImage']) ? trim($_GET['arrImage']) : '';

//-- validar URL imagen --//
$arrFile = explode('/',$sFile);
$pathAbsolute = false;
foreach($arrFile as $item){
	if($item == $_SERVER['HTTP_HOST']){
		$pathAbsolute = true;
		break;		
	}	
}

if($pathAbsolute == true){
	$sPathMode = 'absolute';	
}
else{
	$sPathMode = 'rel';	
	$sPath = 'imagenes/iconos/markersRastreo/';	
}
//-- --//

##-- Se define estilo de la etiqueta debajo de cada icono --##
$sFontFamily = 'font/arial.ttf';
$iFontSize = 10;
$arrTextDimensions = imagettfbbox($iFontSize, 0, $sFontFamily, @$sCaption);
$iTextWidth = abs( $arrTextDimensions[2] - $arrTextDimensions[0] );
$iTextHeight = abs( $arrTextDimensions[5] - $arrTextDimensions[3] );
$iPadding = 5;
$iTextMarginTop = 1;

$iImageHeight = 59;
$iFinalWidth = 58;
$iFinalHeight = 78;
##-- --##

if($sPathMode == 'rel'){
    $sFileName = $sPath.$sFile;
} 
else {
    $sFileName = $sFile;
}

$imagen = array();
//array_push($imagen,$sPath.'misc/base_group.png'); // Imagen Base 
		
if(!empty($arrImage) && $arrImage != 'undefined'){##-- PARA LOS CASOS EN Q SE AGRUPAN PTOS SE Solapan imagenes --##
	$arr_grupo = explode(',',$arrImage);
	
	$only = ''; 
	if(count($arr_grupo) == 1){
		$only = '_only';	
	}
	//-- Defino el orden para la creacion de las imagenes --//
	if(in_array('box',$arr_grupo)){
		array_push($imagen,$sPath.'misc/box'.$only.'.png'); 
	}
	if(in_array('token',$arr_grupo)){
		array_push($imagen,$sPath.'misc/token'.$only.'.png'); 
	}
	if(in_array('truck',$arr_grupo)){
		array_push($imagen,$sPath.'misc/truck'.$only.'.png'); 
	}
	if(in_array('car',$arr_grupo)){
		array_push($imagen,$sPath.'misc/car'.$only.'.png');
	}
	if(in_array('cellphone',$arr_grupo)){
		array_push($imagen,$sPath.'misc/cellphone'.$only.'.png');
	}
	if(in_array('semi',$arr_grupo)){
		array_push($imagen,$sPath.'misc/semi'.$only.'.png');
	}
	if(in_array('referencia',$arr_grupo)){
		array_push($imagen,$sPath.'misc/referencia.png');
	}
	$x = 0;
	$y = 0;
}
elseif($_GET['historico']){##-- en caso q sea historico --##
	array_push($imagen,$sFileName); 
	$sCaption = trim(str_replace('+',' ',$sCaption));
	$srtlen = strlen($sCaption);
	if($srtlen <= 5){
		$x = 20;
		$iFinalWidth = 58;
		$iTextWidth = 26;
	}
	elseif($srtlen <= 11){
		$x = 26;
		$iFinalWidth = 74;
		$iTextWidth = 58;
	}
	elseif($srtlen <= 13){
		$x = 32;
		$iFinalWidth = 80;
		$iTextWidth = 60;
	}
	else{
		$x = 58;
		$iFinalWidth = 136;
		$iTextWidth = 128;
	}
	
	$sCaption = str_pad($sCaption,$iTextWidth,' ');	
	$y = 0;
	$iFinalHeight = 40;
	$iImageHeight = 20;	
	$iPadding = 10;
	$iTextMarginTop = 1;
	$iFontSize = 8;
}
else{##-- PARA LOS DEMAS CASOS --##
	array_push($imagen,$sFileName); 
	$y = strpos($sFileName,'ref-adt.png')?'27':(strpos($sFileName,'ref-')?20:11);
	$x = 13;
	$iFontSize = 8;
	$iTextWidth = 54;
	$iPadding = 10;
	$iTextMarginTop = 1;
	
	if(!empty($sCaption )){
		$sCaption = (strlen(@$sCaption) > 10)?substr(@$sCaption,0,8).'...':@$sCaption;
		$sCaption = str_pad($sCaption,9,' ',STR_PAD_LEFT);
	}
	else{
		$y = 32;	
	}
}

	/**/
	$img[0] = imagecreatetruecolor($iFinalWidth, $iFinalHeight);
    imagesavealpha($img[0], true); // Conservo la transparencia del marker
	$trans_colour = imagecolorallocatealpha($img[0], 0, 0, 0, 127);
    imagefill($img[0], 0, 0, $trans_colour);
	/**/
	
	for($i = 0; $i < count($imagen); $i++){
		$img[$i+1] = imagecreatefrompng($imagen[$i]);
		imagesavealpha($img[$i+1], true);
	}
	
	// Copiamos una de las im�genes sobre la otra.
	// imagecopyresampled( "img_origen", "imagen_que_nueva", pos x imagen_que_nueva, pos y imagen_que_nueva, pos_x_img_origen, pos_y_img_origen, largo_para_imagen_nueva, ancho_para_imagen_nueva, largo_para_imagen_origen, largo_para_imagen_origen);
	for($i = 1; $i < count($img); $i++){
		imagecopyresampled($img[0],$img[$i],$x,$y,0,0,imagesx($img[$i]),imagesy($img[$i]),imagesx($img[$i]),imagesy($img[$i]));
	}
	
	if(!empty($sCaption )){
		// Creo el texto que voy a incrustar en la imagen dinamica + el rectangulo circundante
		$colorSilver = imagecolorallocate($img[0], 210, 210, 210);
		$colorGray = imagecolorallocate($img[0], 128, 128, 128);
		$x1 = 0;
		$y1 = $iImageHeight + $iTextMarginTop;
		$x2 = $iFinalWidth - 1;
		$y2 = $iFinalHeight - 1;
		imagefilledrectangle($img[0], $x1, $y1, $x2, $y2, $colorSilver);
		imagerectangle($img[0], $x1, $y1, $x2, $y2, $colorGray);
		// Escribo el label sobre la imagen
	   
		$iX = floor($iFinalWidth/2) - floor($iTextWidth/2); 
		$iY = $iImageHeight + $iFontSize + floor($iPadding / 2) + $iTextMarginTop;
		$iAngle = 0;
		imagettftext($img[0], $iFontSize, $iAngle, $iX, $iY, $colorBlack, $sFontFamily, @$sCaption);	
	}
	
	// Damos salida a la imagen final a un archivo
	header('Content-Type: text/html; charset=utf-8');
	header('Content-type: image/png');
	ob_start();
	//imagepng($tapa_caratula,  $sPath."misc/salida.png");
	imagepng($img[0]);
	$output = ob_get_clean();
	
	// Destruimos las im�genes
	for($i = 0; $i < count($img); $i++){
		imagedestroy($img[$i]);
	}
	echo $output;	
	exit;