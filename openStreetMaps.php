<?php
error_reporting(0);
header_remove('X-Powered-By');
header('Content-Type: text/html; charset=utf-8');
header('Content-type: image/png');

$zoom = (int)$_GET['zoom'];
$coord['x'] = (int)$_GET['x'];
$coord['y'] = (int)$_GET['y'];

/*
$zoom = 10;
$coord['x'] = 5;
$coord['y'] = 9;
*/
$fichero = 'http://tile.openstreetmap.org/'.$zoom.'/'.$coord['x'].'/'.$coord['y'].'.png';
$imagen = file_get_contents($fichero);
echo $imagen;
exit;
?>