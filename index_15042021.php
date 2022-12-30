<?php
session_start();
error_reporting(E_ALL);
set_time_limit(300);
header_remove('X-Powered-By');

include('includes/config_clientes.php');
include "includes/funciones.php";

include ('clases/clsIdiomas.php');
$objIdioma = new Idioma();
$lang = $objIdioma->getHome(getIdiomaBrowser());
//--------------------------------------------------------------------
$seccion='index';

$url = str_replace('//','/',$_SERVER['REQUEST_URI']);
$arrUrl = explode('/',$url);
$url = '/'.$arrUrl[1];

require_once "mobile/detectorDispositivo.php";
if(SITE != 'MOBILE'){
	if(esDispositivo()){
		$esMobil = true;
		
		if(strtolower($_SERVER['REQUEST_URI']) == '/shootup'){
			header('Location: /shootup/dashboard');
			exit;
		}
		elseif(strtolower($_SERVER['REQUEST_URI']) == '/forza'){
			include('index_FORZA.php');
			exit;
	    }
		elseif(strpos($_SERVER['REQUEST_URI'], '/agenteoficialadt') !== false){
			if(!$_POST){
				include('index_AGENTEOFICIALADT.php');
				exit;
			}
	    } 
		else{
			header('Location: /mobile');
			exit;
		}
	}
}
else{
	if(!esDispositivo()){
		header("Location: ".$url."/mobile/Redireccion2.html"); 
		exit;
	} 
}

require_once 'mobile/Mobile_Detect.php';
	$detect = new Mobile_Detect;
	if($detect->is('Blackberry')){
		$version = $detect->version('BlackBerry','float');
		if((int)$version < 7){ header("Location: ".$url."/mobile/Redireccion3.html"); exit;}
	}


//--------------------------------------------------------------------
//		L O G U E O
//--------------------------------------------------------------------
if(isset($_POST["txtUsuario"])){
	$mensaje = "";
	require_once('includes/check.php');
	$mensaje.= checkString(trim($_POST["txtUsuario"]), 3, 50,$lang->localizart->usuario,1);
	if($mensaje)$mensaje.="<br/>";
	$mensaje.= checkString(trim($_POST["txtPassword"]), 3, 50,$lang->localizart->password,1);

	if(!$mensaje){
		$arrDatos["usuario"] = escapear_string(trim(@$_POST["txtUsuario"]));
		$arrDatos["pass"] = escapear_string(trim(@$_POST["txtPassword"]));
	}else{
		$strError = $lang->localizart->msg_datos_incorrectos;
	}
}

//--------------------------------------------------------------------
//		R E D I R E C C I O N A M I E N T O
//--------------------------------------------------------------------

if (isset($_SESSION["nombreUsuario"]) && !isset($_POST["txtUsuario"])) {
	if (isset($_SESSION["seccion"]) && isset($_SESSION["accion"])) {
		$pagina = $_SESSION["seccion"];
	} else {
        $pagina = $_SESSION['paginaDefecto'];
	}
	redireccionarPagina('boot.php?c='.$pagina);
}
elseif(isset($arrDatos["usuario"]) && isset($arrDatos["pass"]) && isset($_POST["txtUsuario"])){
	include('controladores/loginControlador.php');
	$login = validarUsuario($arrDatos);
	require_once 'clases/clsPerfiles.php';
	$objPerfil = new Perfil($objSQLServer);

    switch($login){
        case 1: 			
            if($_SESSION["ultimoAcceso"]==NULL && tienePerfil(16) && $objPerfil->validarSeccion('wizard')){
                redireccionarPagina("boot.php?c=wizards&wizard=default");
            }
            else{
	 	redireccionarPagina("boot.php?c=".$_SESSION["paginaDefecto"]); 
            }
	break;
	case 4: redireccionarPagina('indexKey.php',true);
	break;
	case 5: redireccionarPagina('logoff.php');
	break;
	default:
            $strError = $lang->localizart->msg_datos_incorrectos;
	break;
	}
	$objSQLServer->dbDisconnect();
}?>

<?php 
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$posicion = strrpos($user_agent, "MSIE");
if ($posicion === false) { $ie = false;} else {$ie = true;}

//------------------------------------------------------------------------------------------------------------------------------------------------------------------
//								                 	           C A B E C E R A   W E B
//------------------------------------------------------------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<title><?=$lang->localizart->label_iniciar_session?> | <?=TITLE?></title>    
	
	<!-- Protecci�n contra ClickJacking (evita q el sistema sea incrutado en un iframe en un dominio externo.)-->
	<script language="javascript" language="javascript"> 
	if(top.location != location){
		top.location = self.location;
	}
	</script>
	<!-- -->
	
	<link rel="shortcut icon" href="<?=$url?>/imagenes/img-clientes/<?=FAVICON?>" type="image/x-icon" />
	<script type="text/javascript" src="<?=$url?>/js/jquery/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="<?=$url?>/js/check.js"></script>
	<script type="text/javascript" src="<?=$url?>/js/ajax.js"></script>
	<script type="text/javascript" src="<?=$url?>/js/funciones.js"></script>
	<script type="text/javascript" src="<?=$url?>/js/login.js"></script>
	<script type="text/javascript" src="<?=$url?>/js/jquery.tools.js"></script>
    
	<script type="text/javascript" src="<?=$url?>/js/jquery/colorbox/jquery.colorbox-min.js"></script>
	<link rel=stylesheet type="text/css" href="<?=$url?>/js/jquery/colorbox/colorbox.css" />
	<style>
		.campoFaltante{background-color: #ffebe8 !important; border: 1px solid #c00 !important;}
	</style>
	<script language="javascript">
		$(document).ready(function() {
			$("#recuperar_contrasenna,#cambiar_clave").colorbox({
				inline: true,
				width: "480px",
				height: "275px",
				overlayClose: true,
				escKey: true,
				closeButton: true
			});
		});
		
		function validateEnter(e) {
			var key=e.keyCode || e.which;
			if (key==13){ return true; } else { return false; }
		}
		
		var arrLang = [];
		arrLang['ingrese_mail'] = '<?=$lang->localizart->recupero_contrasena->msg_ingrese_mail?>';
	</script>

<?php
//------------------------------------------------------------------------------------------------------------------------------------------------------------------
if(file_exists('index_'.SITE.'.php')){
	include('index_'.SITE.'.php');
}
else{
	include('index_default.php');
	include('footer.php');
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
?>

<div style="display: none;">
	<div id='div_recuperar_contrasenna'>
		<div id="datos-mail">
        <p class="required_error" id="error-mail" style=" display:none;"></p>
        <fieldset>
			<?php if($ie){?>						 
            <label><?=$lang->localizart->recupero_contrasena->ingresar_mail?></label>
            <?php }?>
            <input type="text" name="txtMail" id="txtMail" placeholder="<?=$lang->localizart->recupero_contrasena->ingresar_mail?>" autocomplete="off">
		</fieldset>
        <fieldset>
        	<center>
            	<input class="button" value="<?=$lang->localizart->recupero_contrasena->btn_enviar?>" type="button" onClick="javascript:setResetPassword('<?=strrev($_GET['config'])?>');">
            </center>
		</fieldset>
        </div>
        <div id="respuesta-ok" style="display:none;">
        	<p><strong><?=$lang->localizart->recupero_contrasena->msg_ok?></strong></p>
        	<p><?=$lang->localizart->recupero_contrasena->msg_spam?></p>
        </div>
		
	</div>
</div>

	</body>
</html>