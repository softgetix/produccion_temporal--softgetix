<?php
	//Borro las sessiones y redirecciono al index.php
	@session_start();
	header_remove('X-Powered-By');
	$dir = $_SERVER['REQUEST_URI']; //$_SESSION['DIRCONFIG'];
	$final = explode("/",$dir);	
	$location='index';
	if(isset($_SESSION['hkey'])){
		$location='indexKey';
	}
	
	@session_unset();
	@session_destroy();
	
	header('Location: /'.$final[1]);
?>