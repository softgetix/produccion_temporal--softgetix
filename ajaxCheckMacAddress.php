<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
error_reporting(0);

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";
include "clases/clsNomenclador.php";

$mins = 3;
require_once 'clases/clsEquipos.php';
$objEquipo = new Equipo($objSQLServer);
$macs = explode(";", $_POST['mac']);

foreach ($macs as $mac) {
    if (!isset($_SESSION['mac'][$mac])) {
        $_SESSION['mac'][$mac] = array();
    }
    $res = $objEquipo->validarUnidadUsuario($mac);
    
    if ($res !== false) {
        $msg .= "| encontro: {$mac}";
       
        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = base64_encode(rand(1, 99999999));
        }

        $objNomenclador = new Nomenclador($objSQLServer);
       
        $_SESSION['hashKey'] = md5($mac . date("Y-m-d") . $_SESSION['token']);
        $obj = array();
        $obj['mac'] = $mac;
        $obj['token'] = $_SESSION['token'];
        $obj['nombre'] = $res['mo_matricula'];
        $obj['lat'] = $res['sh_latitud'];
        $obj['lng'] = $res['sh_longitud'];
        $obj['fecha_recepcion'] = $res['sh_Fecharecepcion'];
        $obj['fecha_recepcion_ts'] = strtotime($res['sh_Fecharecepcion']);
        $obj['diferencia'] = time() - $obj['fecha_recepcion_ts'];
        //$obj['diferencia'] = 500;
        $obj['direccion'] = $objNomenclador->obtenerNomenclados($res['sh_latitud'], $res['sh_longitud']);
        
        // No estaba conectado o es la primera vez que manda por ajax
        if (!isset($_SESSION['conectado'][$mac]) || $_SESSION['conectado'][$mac] === false) {
            $_SESSION['conectado'][$mac] = true;
            $_SESSION['data'][$mac] = $res;
            $objEquipo->insertarEvento(INDOOR_EVT_CONEXION, $res['sh_latitud'], $res['sh_longitud'], $res['un_id'], INDOOR_ESTADO_CONECTADO);
        }

        if (!isset($_SESSION['last'][$mac])) {
            $_SESSION['last'][$mac] = time();
        } else {
            $diff = time() - $_SESSION['last'][$mac];
            if ($diff > 60) {
                if ($obj['diferencia'] > $mins * 60) {
                    $objEquipo->insertarEvento(INDOOR_EVT_EN_LINEA_CON_TOKEN_NO_LOCALIZADO, $res['sh_latitud'], $res['sh_longitud'], $res['un_id'], INDOOR_ESTADO_CONECTADO);
                } else {
                    $objEquipo->insertarEvento(INDOOR_EVT_EN_LINEA_CON_TOKEN, $res['sh_latitud'], $res['sh_longitud'], $res['un_id'], INDOOR_ESTADO_CONECTADO);
                }
                $_SESSION['last'][$mac] = time();
            }
        }

        echo json_encode($obj);
        exit;
    } else {
        $msg .= " | NO encontro: {$mac}";
        if (isset($_SESSION['conectado'][$mac]) && $_SESSION['conectado'][$mac] === true) {
            $res = $_SESSION['data'][$mac];
            $objEquipo->insertarEvento(INDOOR_EVT_DESCONEXION, $res['sh_latitud'], $res['sh_longitud'], $res['un_id'], INDOOR_ESTADO_NO_CONECTADO);
        }
        $_SESSION['conectado'][$mac] = false;
    }
}

foreach ($_SESSION['mac'] as $_mac => $tmp) {
    if (!in_array($_mac, $macs)) {
        // Si el elemento del array no se encuentra en el array de 
        // elementos posteados es que se removio el token.
        if (isset($_SESSION['conectado'][$_mac]) && $_SESSION['conectado'][$_mac] === true) {
            $res = $_SESSION['data'][$_mac];
            $objEquipo->insertarEvento(INDOOR_EVT_DESCONEXION, $res['sh_latitud'], $res['sh_longitud'], $res['un_id'], INDOOR_ESTADO_NO_CONECTADO);
        }
        unset($_SESSION['data'][$_mac]); //['conectado'] = false;
        unset($_SESSION['conectado'][$_mac]);
        unset($_SESSION['last'][$_mac]);
    }
}

unset($_SESSION['hashKey']);
unset($_SESSION['token']);
echo 0;
