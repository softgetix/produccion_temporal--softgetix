<?php

function esDispositivo(){
	$useragent=strtolower($_SERVER['HTTP_USER_AGENT']);	
	if(preg_match('/android|bb|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|iphone|ipad|ipod|iris|kindle|palm|psp|symbian/',$useragent)){
		return true;
	}
	else{		
		return false;
	}
}

function esAndroid(){	
	$useragent=strtolower($_SERVER['HTTP_USER_AGENT']);	
	if(preg_match('/android/',$useragent)){
		return true;
	}
	else{		
		return false;
	}
}

function esBlackBerry(){	
	$useragent=strtolower($_SERVER['HTTP_USER_AGENT']);	
	if(preg_match('/bb|blackberry/',$useragent)){
		return true;
	}
	else{		
		return false;
	}
}

?>