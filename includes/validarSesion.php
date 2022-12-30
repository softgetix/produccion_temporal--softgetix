<?php
@session_start();
if(!isset($_SESSION['idUsuario'])){
	session_destroy();
	$out['status'] = 3;
	$out['msg'] = 'session expiro';
	$_SESSION['truncate_session'] = true;
	echo json_encode($out);
	exit;
}
?>