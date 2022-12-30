<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
error_reporting(0);
ini_set('display_errors', 'On');

$cmd = $_POST['cmd']?$_POST['cmd']:NULL;

require_once("includes/validarSesion.php");
require_once("includes/funciones.php");
require_once("includes/conn.php");
require_once("includes/validarUsuario.php");

require_once("clases/clsComandosFavoritos.php");


$retval = array(
    'errcode' => 0,
    'errmesg' => 'OK',
    'data' => null
);


if ( isset( $_POST['op'] ) )
{
    $sOperation = $_POST['op'];

    switch ($sOperation) {

        case 'send':
        {
            $sCommand = ( isset($cmd) ? $cmd: die("No se especifico el comando a enviar.") );
            $objComando = new ComandoFavorito($objSQLServer);

            $idEquipo = @$_POST['id_equipo'];
            //echo "ID EQ (".$idEquipo.")";
            $bSuccess = $objComando->insertar( $sCommand, $idEquipo );

            if ( $bSuccess )
            {
                $iPort = $objComando->enviarUDP( $idEquipo );
                $iTicket = $objComando->getTicket();
                $retval['data'] = array(
                    'ticket' => $iTicket
                );
            }
            else
            {
                $retval['errcode'] = -1;
                $retval['errmesg'] = 'No se pudo insertar comando en la DB';
            }

            break;
        }
        
        case 'check':
        {
            $iTicket = ( isset( $_POST['ticket'] ) ? $_POST['ticket'] : die("No se especifico el ticket del comando enviado.") );

            $objComando = new ComandoFavorito($objSQLServer);
            $arrResultado = $objComando->chequearEstadoTicket( $iTicket );

            $retval['data']['response'] = $arrResultado;
            //$retval['data']['response'] = false; // Prueba

            break;
        }

        default:
        {
            comandoNoValido();
            break;
        }
    }
}
else
{
    $retval = array(
        'errcode' => -1,
        'errmesg' => 'No se especifico la operacion a realizar sobre el comando'
    );
}

header('Content-type: application/json');
$JSONData = json_encode($retval);
echo $JSONData;