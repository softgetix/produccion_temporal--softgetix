<?php
session_start();
header_remove('X-Powered-By');

$arr_uri = explode('/',$_SERVER['REQUEST_URI']);
$_GET['config'] = strrev($arr_uri[1]);

include('includes/config_clientes.php');
include 'includes/funciones.php';

include ('clases/clsIdiomas.php');
$objIdioma = new Idioma();
$lang = $objIdioma->getHome(getIdiomaBrowser());


$url = '/'.$arr_uri[1];  			

$msgErr = '';
$sinForm = false;
$irAlLogin = false;

//-- DECODIFICAR URL --//
if(!$_POST){

	$arrValid = decodificarURL($_GET['ref']);
	$idUsuario = $arrValid['id'];
	$reset_code = $arrValid['reset_code'];

	if(!$arrValid){
		$msgErr =$lang->localizart->recupero_contrasena->msg_caduco_link;
		$sinForm = true;	
	}
}
//-- --//

$idUsuario = $_POST['idUsuario']?$_POST['idUsuario']:(int)$idUsuario;
$reset_code = $_POST['reset_code']?$_POST['reset_code']:$reset_code;

include "includes/conn.php";
include "clases/clsUsuarios.php";
$objUsuario = new Usuario($objSQLServer);


if($_POST){

	if(isset($_POST['txtPassword']) && isset($_POST['txtPassword_repeat'])){
		if($_POST['txtPassword'] == $_POST['txtPassword_repeat']){
		
			if(validarNuevaContrasenna($_POST['txtPassword'])){
				if($objUsuario->validarCambioPassword($_POST)){
                    //$hash = md5($_POST['txtPassword']);
                    $hash = hash('sha256',$_POST['txtPassword']);
                    if(!$objUsuario->actualizarPassword((int)$_POST['idUsuario'], $hash)){
						$msgErr =$lang->localizart->recupero_contrasena->msg_error_actualizacion;
                    }	
				}
				else{
					$msgErr =$lang->localizart->recupero_contrasena->msg_caduco_link;
					$sinForm = true;
					
				}
				
				if(empty($msgErr)){
					$msgErr =$lang->localizart->recupero_contrasena->msg_ok_actualizacion;
					$sinForm = true;	
					$irAlLogin = true;
				}
			}
			else{
				$msgErr =$lang->localizart->recupero_contrasena->msg_error_formato_password;
			}
		}
		else{
			$msgErr =$lang->localizart->recupero_contrasena->msg_error_coinciden_password;	
		}
	}
}
else{

	if(!$objUsuario->validarCambioPassword(array('idUsuario' => $idUsuario, 'reset_code' => $reset_code ))){
		

		$msgErr =$lang->localizart->recupero_contrasena->msg_caduco_link;
		$sinForm = true;	
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?=TITLE?></title>    
	<!-- Protección contra ClickJacking (evita q el sistema sea incrutado en un iframe en un dominio externo.)-->
	<script language="javascript">
	if(top.location != location){
		top.location = self.location;
	}
	</script>
	<!-- -->
    
	<link rel="shortcut icon" href="<?=$url?>/imagenes/img-clientes/<?=FAVICON?>" type="image/x-icon" />  
	<link href="<?=$url?>/css/menu.css" rel="stylesheet" type="text/css"/>	
	<link href="<?=$url?>/css/estilosLogin.css" rel="stylesheet" type="text/css" />
</head>	
<body style="overflow:auto;">
	<div id="navbar">
    	<img id="logoSite" src="<?=$url.'/imagenes/img-clientes/'.LOGO?>" />
        <div class="clear"></div>
	</div>
    <div id="contenido">
        <form name="myform" method="post" action="<?=$_SERVER['REQUEST_URI']?>"> 
       		<h2><?=$lang->localizart->recupero_contrasena->title_recupero?></h2>
            
			<?php if($msgErr){?><p class="required_error"><?=$msgErr?></p><?php }?>
            
			<?php if(!$sinForm && $_GET['ref']){?>
            <input type="hidden" name="idUsuario" value="<?=$idUsuario?>" />
            <input type="hidden" name="reset_code" value="<?=$reset_code?>" />
            <fieldset>
                <label><?=$lang->localizart->recupero_contrasena->nueva_contrasena?></label>
                <input type="password" name="txtPassword" autocomplete="off"  />
            </fieldset>
            <fieldset>    
                <label><?=$lang->localizart->recupero_contrasena->repita_contrasena?></label>
                <input type="password" name="txtPassword_repeat" autocomplete="off" />
            </fieldset>
            <fieldset>
            	<center>    
                	<input type="submit" name="enviar" value="<?=$lang->localizart->recupero_contrasena->btn_enviar?>" class="button" />
                </center>
            </fieldset>
            <?php }?>
            <?php if($irAlLogin){?>
            <center>
            	<a class="button" href="/<?=strrev($_GET['config'])?>" style=" text-decoration:none"><?=$lang->localizart->recupero_contrasena->btn_continuar?></a>
            </center>
            <?php }?>
		</form>
    </div>
    <?php include('footer.php') ?>
</body>
</html>