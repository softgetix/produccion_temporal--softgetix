<?
/*
* proxy.php
*
* Copyright 2013 Miguel Rafael Esteban Martín (www.logicaalternativa.com) <miguel.esteban@logicaalternativa.com>
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
* MA 02110-1301, USA.
*/
####
####
# Script que realiza la funciones de Cross-Domain Proxy para OpenLayers
####
####


// Se muestran todos los errores
ini_set('display_errors', '1');

// Se incluye la librería de Snoopy. Cliente HTTP
include("lib/Snoopy.class.php");

// Se obtiene la URL Del parámetro 'url' que se pasa por get
$url = $_GET['url'];

// Se crea una instancia del cliente HTTP
$snoopy = new Snoopy;

// Como navegador se utiliza el mismo que se ha hecho la petición
$snoopy->agent = $_SERVER['HTTP_USER_AGENT'];

// Si se ha enviado parámetros por POST se hace un submit con esos datos
// Si no se hace un fetch (método GET)
$res = count($_POST) != 0
? $snoopy->submit($url,$_POST)
: $snoopy->fetch( $url );

// Se comprueba el error
if ( ! $res ) {

echo "Error ${$snoopy->error}";

exit;	
}

// Se escriben los resultados que devuelve la petición HTTP
echo $snoopy->results;


?>