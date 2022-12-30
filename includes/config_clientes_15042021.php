<?php
error_reporting(E_ERROR);
validarCliente($_GET);

//--Ini. Definición de path
if($_SERVER['HTTP_HOST'] == 'localhost'){
	define('PATH_LOG_SECURE','/xampp/htdocs/localizart/Web_system/log/secure/web');
	define('PATH_LOG_SYSTEM','/xampp/htdocs/localizart/Web_system/log/system/web');
	define('PATH_ATTACH','/xampp/htdocs/localizart/Web_system/archivos');
}
else{
	define('PATH_LOG_SECURE','/var/www/log/secure/web');
	define('PATH_LOG_SYSTEM','/var/www/log/system/web');
	define('PATH_ATTACH','/var/www/archivos');
}   
//Fin.


if(defined('FAVICON')){
	if(!is_file('imagenes/img-clientes/'.FAVICON)){
		define('FAVICON','favicon.ico');
	}
}
else{
	define('FAVICON','favicon.ico');	
}

if(defined('LOGO')){
	if(!is_file('imagenes/img-clientes/'.LOGO)){
		define('LOGO','localizart_logo.jpg');
	}
}
else{
	define('LOGO','localizart_logo.jpg');
}

define('MENU_REDUCIDO',false);

function validarCliente($get){
	if(
		(isset($get['config']) && !empty($get['config'])) 
		&& (isset($_SESSION['DIRCONFIG']) && !empty($_SESSION['DIRCONFIG']))
		&& ($get['config'] != strrev($_SESSION['DIRCONFIG']))
	){
		session_destroy();
		echo '<script>document.location.href="/'.strrev($get['config']).'"</script>';
		exit;	
	}
	elseif(isset($get['config']) || isset($_SESSION['DIRCONFIG'])){
		
		$config = isset($get['config'])?$get['config']:strrev($_SESSION['DIRCONFIG']);
		switch($config){
			case 'trazilacol':
				$_SESSION['DIRCONFIG'] = 'localizart';
				define('SITE', 'LOC');
				define('LOGO','localizart_logo.jpg');
				define('TITLE', 'Soluciones Localizar-T');
				define('FOOTER', true);
				define('MENU_REDUCIDO',false);
			break;	
			case 'retnicck':
				$_SESSION['DIRCONFIG'] = 'kccinter';
				define('SITE', 'KCC');
				define('LOGO', 'kcc_logo.jpg');
				//define('FAVICON', '');
				define('TITLE', 'Kimberly Clark');
				define('FOOTER', true);
				define('MENU_REDUCIDO',false);
			break;
			case 'sauga':
				$_SESSION['DIRCONFIG'] = 'aguas';
				define('SITE', 'LOC');
				define('LOGO', 'aguas_logo.jpg');
				define('TITLE', 'Aguas de Corrientes');
				define('FOOTER', true);
				define('MENU_REDUCIDO',false);
			break;
			case 'tda':
				$_SESSION['DIRCONFIG'] = 'adt';
				define('SITE', 'ADT');
				define('LOGO', 'adt_logo.jpg');
				define('FAVICON', 'faviconADT.ico');
				define('TITLE', 'ADT FindU');
				define('FOOTER', false);
			break;	
			case 'oalcck':
				$_SESSION['DIRCONFIG'] = 'kcclao';
				define('SITE', 'KCC-LAO');
				define('LOGO', 'kcc_logo.jpg');
				//define('FAVICON', '');
				define('TITLE', 'Kimberly Clark');
				define('FOOTER', true);
				define('MENU_REDUCIDO',false);
			break;
			case 'anitnegracck':
				$_SESSION['DIRCONFIG'] = 'kccargentina';
				define('SITE', 'KCC-ARGENTINA');
				define('LOGO', 'kcc_logo.jpg');
				//define('FAVICON', '');
				define('TITLE', 'Kimberly Clark');
				define('FOOTER', true);
				define('MENU_REDUCIDO',false);
			break;				
			case 'procrebif':
				$_SESSION['DIRCONFIG'] = 'fibercorp';
				define('SITE', 'FiberCorp');
				define('TITLE', 'FiberCorp Cloud Tracking');
				define('FOOTER', true);
				define('MENU_REDUCIDO',true);
			break;	
			case 'putoohs':
				$_SESSION['DIRCONFIG'] = 'shootup';
				define('SITE', 'ShootUp');
				define('LOGO', 'shootup_logo.jpg');
				define('TITLE', 'ShootUp');
				define('FOOTER', true);
				define('FAVICON', 'faviconShooUp.ico');
				define('MENU_REDUCIDO',false);
			break;
			case 'tdasetnega':
				$_SESSION['DIRCONFIG'] = 'agentesadt';
				define('SITE', 'LOC');
				define('LOGO', 'adt_logo.jpg');
				define('FAVICON', 'faviconADT.ico');
				define('TITLE', 'Agentes ADT');
				define('FOOTER', true);
			break;
			case 'tdalaicifoetnega':
				$_SESSION['DIRCONFIG'] = 'agenteoficialadt';
				define('SITE', 'AGENTEOFICIALADT');
				define('LOGO', 'logo_agentes_adt.jpg');
				define('FAVICON', 'faviconADT.ico');
				define('TITLE', 'Agente Oficial ADT');
				define('FOOTER', true);
			break;
			case 'tdaeta':
				$_SESSION['DIRCONFIG'] = 'ateadt';
				define('SITE', 'LOC');
				define('LOGO', 'adt_logo.jpg');
				define('FAVICON', 'faviconADT.ico');
				define('TITLE', 'ADT');
				define('FOOTER', true);
			break;	
			case 'ametsis':
				$_SESSION['DIRCONFIG'] = 'sistema';
				define('SITE', 'LOC');
				define('LOGO','localizart_logo.jpg');
				define('TITLE', 'Soluciones Localizar-T');
				define('FOOTER', true);
				define('MENU_REDUCIDO',false);
			break;
			
			case 'itnava':
				$_SESSION['DIRCONFIG'] = 'avanti';
				define('SITE', 'AVANTI');
				define('LOGO','logo_avanti.png');
				define('TITLE', 'Avanti Equipo');
				define('FOOTER', false);
				define('MENU_REDUCIDO',false);
				define('FAVICON', 'avanti.ico');
			break;
			
			case 'azrof':
				$_SESSION['DIRCONFIG'] = 'forza';
				define('SITE', 'FORZA');
				define('LOGO','forza_logo.jpg');
				define('TITLE', 'FORZA');
				define('FOOTER', false);
				define('MENU_REDUCIDO',false);
				define('FAVICON', 'forza_favicon.ico');


			break;			
			//--------------------MOBILES------------------------
			case 'elibom':
				$_SESSION['DIRCONFIG'] = 'mobile';
				define('SITE', 'MOBILE');	
				$title = 'Localizar-T';
				if(strpos($_SERVER['SERVER_NAME'],'findu')){
					$title = 'ADT FindU';
					define('FAVICON', 'faviconADT.ico');
				}
				define('TITLE',$title);
			break;
			default:
				$_SESSION['DIRCONFIG'] = strrev($config);
				define('SITE', 'LOC');
				define('TITLE', 'Localizar-T');
				define('FOOTER', true);
				define('MENU_REDUCIDO',false);
			break;
		}
	}
	else{
		
		$url = explode('/',$_SERVER['REQUEST_URI']);
		session_destroy();
		if(!empty($url[1])){
			echo '<script>document.location.href="/'.$url[1].'"</script>';
		}
		else{
			echo '<script>document.location.href="/localizart"</script>';	
		}
		exit;	
	}
}

?>