<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");
header('Content-type: application/json');

set_time_limit(300);
error_reporting(0);

$txtArchivo = $_POST["txtArchivo"]?$_POST["txtArchivo"]:NULL;

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

//$grupo = $_GET['g'];

include 'clases/clsComandos.php';

$objComando = new Comando($objSQLServer);
/*
PASOS:
1. Creo el grupo con borrado logico
2. Recorro el archivo y creo que los comandos con borrado logico.
3. Leo los comandos de dicho grupo.
*/
$strDir = 'importacion/';
$return = array();
$return['contenido'] = '';
$grupo = 0;
$strExtension = "";
    $nombreGrupo = "Grupo Automatico ".rand(1,9999);
    $sql = "INSERT INTO tbl_grupo ([gr_nombre],[gr_borrado]) VALUES ('".$nombreGrupo."', 1);";
    $objSQLServer->dbQuery($sql);
    $busqueda[0]["gr_id"] = $objSQLServer->dbLastInsertId();
    $grupo = $busqueda[0]["gr_id"];
    $strLineas = $txtArchivo;
    if (!$strLineas) die();
    //$strLineas=preg_replace('/\n|\r|\n\r/','<br />',$strLineas); 
    //$arrLineas = explode("<br />",$strLineas);
    $arrLineas = explode('|::|',$strLineas);
    for ($i = 0;$i < count($arrLineas) && $arrLineas;$i++) {
        $contenido = $arrLineas[$i];
        $contenido = str_replace("\r\n","",$contenido);
        if (strlen($contenido) > 1) {
			$strnombre = $contenido;
			if(strlen($contenido) > 50){
				$strnombre = substr($contenido,0,47).'...';
			}
            $sql = "INSERT INTO tbl_comando ([co_nombre],[co_codigo],[co_tipo],[co_instrucciones],[co_borrado],[co_respuesta_ok]) ";
			$sql.= "VALUES ('".$strnombre."','".$contenido."',1,'ANDA',1,'');";
            $objSQLServer->dbQuery($sql);
            $busqueda[0]["co_id"] = $objSQLServer->dbLastInsertId();
            //$sql = "SELECT TOP 1 co_id FROM tbl_comando WHERE co_borrado = 1 AND co_codigo = '".$contenido."' ORDER BY co_id DESC;";
            //$busqueda=$objSQLServer->dbGetAllRows($sql);
            $co_id = $busqueda[0]["co_id"];
            
            $sql = "INSERT INTO tbl_grupo_comando ([gc_co_id],[gc_gr_id]) VALUES (".$co_id.",".$grupo.");";
               if ($co_id && $grupo) {
                    $objSQLServer->dbQuery($sql);
               }
        }
    }

if (!$grupo) die();
$salida = array();
$_SESSION["grupo"] = $grupo;
$busqueda=$objComando->obtenerComandoGrupoTodos($grupo);
for ($i = 0;$i < count($busqueda);$i++) {
    $salida[$busqueda[$i]["co_id"]] = $busqueda[$i];
}
//limpiarArray($busqueda);
$return['result'] = $busqueda;
echo json_encode($salida);