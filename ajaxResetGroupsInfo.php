<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";
include "includes/validarSesion.php";

$nameVar = "rastreo_" . $_SESSION["idUsuario"];
$nameVarConf = $nameVar.'_conf';

unset( $_SESSION[$nameVarConf]['groups'] );
unset( $_SESSION[$nameVarConf]['groups_ready'] );