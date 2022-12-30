<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");
set_time_limit(300);
error_reporting(0);

$idMovilSeguimiento = ($_POST['idMovilSeguimiento'])? $_POST['idMovilSeguimiento']:"";
$strMoviles= ($_POST['strMoviles'])? $_POST['strMoviles']:"";

$arrData = array();

$nameVar="rastreo_".$_SESSION["idUsuario"];
$arrMovilesUsuario = $_SESSION[$nameVar];

if($idMovilSeguimiento){
	for($i=0; $i < count($arrMovilesUsuario); $i++){
		if ($arrMovilesUsuario[$i]["mo_id"] ==	$idMovilSeguimiento){
			$arrMovilesUsuario[$i]["um_estado"] = 2;
		}else{
			$arrMovilesUsuario[$i]["um_estado"] = 0;
		}
	}
	$_SESSION[$nameVar] = $arrMovilesUsuario;
	setItemsSelect(array($idMovilSeguimiento));
}
elseif($strMoviles){
	$arrMoviles=explode(",",$strMoviles);
	
	for($i=0; $i < count($arrMovilesUsuario); $i++){
		if(in_array($arrMovilesUsuario[$i]["mo_id"],$arrMoviles)){
			$arrMovilesUsuario[$i]["um_estado"] = 1;
		}else{
			$arrMovilesUsuario[$i]["um_estado"] = 0;
		}
	}
	$_SESSION[$nameVar] = $arrMovilesUsuario;
	setItemsSelect($arrMoviles);
}
else{
	bajaItemsSelect();
}

$arrData['refreshInterval'] = 30000;

header('Content-type: application/json');
echo json_encode($arrData);
die;
	
	

function setItemsSelect($arrMoviles){
	$nameVar = "rastreo_".$_SESSION["idUsuario"];
	$nameVarConf = $nameVar.'_conf';
	bajaItemsSelect();
	$_SESSION[$nameVarConf]['checked_mov_ids'] = $arrMoviles;
}

function bajaItemsSelect(){
	$nameVar = "rastreo_".$_SESSION["idUsuario"];
	$nameVarConf = $nameVar.'_conf';
	$_SESSION[$nameVarConf]['checked_mov_ids'] = "";
}	