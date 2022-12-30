<?php 
global $lang;
$arrEntidades = decodeArray($arrEntidades);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html lang="<?=$_SESSION['idioma']?>" xmlns="http://www.w3.org/1999/xhtml"> 
<head>
    <?php if(ES_MOBILE){?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php }?>
	<meta http-equiv="content-language" content="<?=$_SESSION['idioma']?>">
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=TITLE?></title>
    <link rel="shortcut icon" href="imagenes/img-clientes/<?=FAVICON?>" type="image/x-icon" />
    <link type="text/css" rel="stylesheet" href="css/estilosDefault.css"/>
	<link type="text/css" rel="stylesheet" href="css/estilosABMDefault.css"/>
    <link type="text/css" rel="stylesheet" href="css/menu.css"/>
    <link type="text/css" rel="stylesheet" href="css/smoothness/jquery-ui-1.8.4.custom.css"/>
    <!--[if lt IE 8]>
		<link rel="stylesheet" type="text/css" href="css/ie.css" />
    <![endif]-->
	<!--[if IE]>
		<link rel=stylesheet type="text/css" href="css/estilosDefault_ie.css" />
	<![endif]-->
    <?php global $sinDefaultCSS;
    if(!isset($sinDefaultCSS)){
		$urlInclude = 'css/estilos'.ucwords($seccion).'.css';
		if(is_file($urlInclude)){?>
      		<link type="text/css" rel="stylesheet" href="<?=$urlInclude?>"/>
    	<?php }?>
	<?php } ?>
    <?php if(isset($extraCSS) && is_array($extraCSS)) {
    	foreach ($extraCSS as $url){ 
            $aux = explode('?',$url);
            if(is_file($aux[0])){?>
				<link type="text/css" rel="stylesheet" href="<?=$url?>"/>
            <?php }?>
        <?php }
    }
    $styleClient = 'css/estilos_'.SITE.'.css';
    if(is_file($styleClient)){?>
        <link type="text/css" rel="stylesheet" href="<?=$styleClient?>"/>
    <?php }?>
	
   	<script anguage="javascript" type="text/javascript">
		var arrLang = [];
		arrLang['msj_select_baja'] = '<?=$lang->message->msj_select_baja?>';
		arrLang['msj_limite_baja'] = '<?=$lang->message->msj_limite_baja?>';
		arrLang['msj_confirmar_baja'] = '<?=$lang->message->msj_confirmar_baja?>';
		arrLang['msj_alerta_baja_equipo'] = '<?=$lang->message->msj_alerta_baja_equipo?>';
		arrLang['msj_un_update'] = '<?=$lang->message->msj_un_update?>';
		arrLang['msj_select_update'] = '<?=$lang->message->msj_select_update?>';
		arrLang['msj_cargando'] = '<?=$lang->message->msj_cargando?>';
		arrLang['msg_datos_guadados'] = '<?=$lang->message->interfaz_generica->msg_datos_guadados?>';
		
		
		var arrCheck = [];
		arrCheck['msj_completar'] = '<?=$lang->message->interfaz_generica->msj_completar?>';
		arrCheck['msj_campos_invalidos'] = '<?=$lang->message->interfaz_generica->msj_campos_invalidos?>';
		arrCheck['msj_cant_max_caracteres'] = '<?=$lang->message->interfaz_generica->msj_cant_max_caracteres?>';
		arrCheck['msj_cant_min_caracteres'] = '<?=$lang->message->interfaz_generica->msj_cant_min_caracteres?>';
		arrCheck['msj_select_option'] = '<?=$lang->message->interfaz_generica->msj_select_option?>';
		arrCheck['msj_cant_caracteres'] = '<?=$lang->message->interfaz_generica->msj_cant_caracteres?>';
		arrCheck['msj_valor_positivo'] = '<?=$lang->message->interfaz_generica->msj_valor_positivo?>';
		arrCheck['msj_fecha_invalida'] = '<?=$lang->message->interfaz_generica->msj_fecha_invalida?>';
		arrCheck['msj_formato_image'] = '<?=$lang->message->interfaz_generica->msj_formato_image?>';
		
		var $language = '<?=$_SESSION['language']?>';
	</script>
	<script type="text/javascript" src="js/jquery.1.7.1.min.js"></script> 
    <script type="text/javascript" src="js/jquery.tools.js"></script>
	<script type="text/javascript" src="js/jquery.ui.js"></script>
    <script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/funciones.js?3"></script>
    <script type="text/javascript" src="js/constantes.inc.js"></script>
    <script type="text/javascript" src="js/check.js"></script>
	<script language="javascript" type="text/javascript" src="js/navbar.js"></script>
  
  	<?php
	if(isset($extraJS) && is_array($extraJS)){
		foreach ($extraJS as $url){
			if(is_file($url)){?>
        		<script type="text/javascript" src="<?=$url?>"></script>
            <?php }?>    
    	<?php }
	}?>

	<?php if(!isset($sinDefaultJS)){
		$urlInclude = 'js/'.$seccion.'Funciones.js';
		if(is_file($urlInclude)){?>
        	<script type="text/javascript" src="js/<?=$seccion?>Funciones.js?1"></script>
        <?php }?>
    <?php } ?>
</head>
<div style="position:absolute; top:0px; left:0px; width:100%; height:100%; z-index:99999; background:#000000; display:none; text-align:center;" id="fondo_preloader">
	<img src="imagenes/ajax-loader.gif" stlye="position:absolute; top: 200px;" />
</div>
	<input type="hidden" name="hidUrl" id="hidUrl" value="<?=$_SESSION['DIRCONFIG']?>" />
    <input type="hidden" name="hidLatDefecto" id="hidLatDefecto" value="<?=$_SESSION["lat"] ?>" />
    <input type="hidden" name="hidLngDefecto" id="hidLngDefecto" value="<?=$_SESSION["lng"] ?>" />
    <input type="hidden" name="hidZoomDefecto" id="hidZoomDefecto" value="<?=$_SESSION["zoom"] ?>" />
<body onLoad="setInterval('getFecha()',30000);">
	<div id="cuerpo">    
        <div id="wrapper">
            <div id="navbar">
            	<?php if(!MENU_REDUCIDO){?>
                <img id="logoSite" src="<?='imagenes/img-clientes/'.LOGO?>" <?=ES_MOBILE ? 'style="max-width: 130px;"' : ''?> />
                <?php }?>
                <fieldset>
                <?php if(!MENU_REDUCIDO){?>
                	<div class="userLogueado">
                        <span><?=$lang->system->bienvenido?>:</span>
                        <?php
                        $user = $_SESSION['us_nombre'].' '.$_SESSION['us_apellido'];
                        $user = ES_MOBILE ? (strlen($user) > 15 ? substr($user, 0,12) : $user).'...' : $user;
                        ?>

                        <span class="nameUser"><?=encode($user)?></span>
                        <span>|</span>
                        <span class="logout"><a href="logoff.php" target="_self"><?=$lang->menu->salir?></a></span>
                	</div>
                    <span class="clear"></span>
                <?php }?>
                <?php  include("includes/navbar.php"); ?>
					<span class="clear"></span>
                </fieldset>
                <div class="clear"></div>
            </div>
            
			<div id="content" style=" <?=MENU_REDUCIDO?'top:54px':'top:75px;'?>" >
				<?php if ($seccion != "accesDenied") {
					require_once("secciones/".$seccion.".php");
                    }
                ?>
              	<div id="footer_space" class="clear" style=" height:25px;"></div><!-- ayuda a q no se oculte la info x debajo del footer -->
            </div>
        </div><!-- wrapper -->

        <div id="divAlerta" class="divAlerta {10px}"></div>
        <div id="divAlertaRastreo" class="divAlertaRastreo"></div>

        <?php if (isset($mensaje) && strlen($mensaje) > 5): ?>
            <div id="messageDefaultLocalizart" style="left:35%; width:30%; ">
                <a href="javascript: cerrarMensaje();">
                    <img id="imgCerrarMensaje" src="imagenes/cerrar.png" />
                </a>

            	<?php if ($tipoBotonera == 'AM' && !(isset($noError) && $noError)): ?>
                    <span style='color:#ff0000;'><b><?=$lang->message->info_error?></b></span><br/><br/><?= $mensaje; ?><br/>
            	<?php else: ?>
                    <span style='color:#000000;'><br/><?= $mensaje; ?><br/></span><br/>
    			<?php endif; ?>
			</div><!-- #messageDefaultLocalizart -->
        <?php endif; ?>

        <?php if ($_SESSION["faltaPago"]): ?>
            <div id="divFaltaPago">
                <marquee width = 50% scrolldelay = 100 >
    				Esta es una alerta por falta de pago. por favor regularice su situaci&oacute;n dentro de las pr&oacute;ximas 72 hs para evitar interrupci&oacute;n temporal del servicio. Disculpe las molestias ocasionadas.
                </marquee>
            </div>
		<?php endif; ?>

		<?php if(isset($_SESSION['hkey'])) { ?>
            <applet name="Applet" id="Applet" code="HardKeyApplet.ElApplet" archive="AppletMio.jar"
                    style="width: 0px; height: 0px; visibility: hidden;" width="0" height="0"></applet>
		<?php } ?>
        <script type="text/javascript">
		<?php
		if (isset($_SESSION['hkey'])) {
			if ($_SESSION['hkey'] == 0) {
				session_destroy();
			}
			$_SESSION['hkey_check'] = $_SESSION['hkey'];
			$_SESSION['hkey'] = 0;
			require_once 'clases/clsHardKey.php';
			$objHK = new HardKey();
			$StrToSendHex = $objHK->enviarBuscarLlave();
			?>
                                function checkKey(str,cb){
                                    var ap = document.getElementById('Applet');
                                    if (ap && ap.Estado()==='OK'){
                                        var ret=ap.EnviarComando(str);
                                        $.post(
                                        'ajaxHardKey.php',
                                        {m:'checkConnected',s:ret},
                                        function(d){
                                            if (typeof d ==='string') d=$.parseJSON(d);
                                            if(d.r=='OK'){
                                                if(cb)
                                                    cb();
                                            }else if (d.r==='KICK'){
                                                var form = document.createElement('form');
                                                form.method = 'post';
                                                form.action = '/localizart/indexKey.php';
                                                var input;
                                                input = document.createElement('input');
                                                input.setAttribute('name', 'referencia_error');
                                                input.setAttribute('value', 'key fallo');
                                                input.setAttribute('type', 'hidden');
                                                form.appendChild(input);

                                                document.body.appendChild(form);
                                                form.submit();
                                            }
                                        },'text json'
                                    );
                                    }
                                }
                                checkKey("<?php echo $StrToSendHex; ?>")
<?php } ?>


                    function resize() {
                        var sidebar = 0;

                        if ($("#colIzq").css('display') == 'block') {
                            sidebar = 265;}
						else{
                            sidebar = 10;}
		
                        var y = $(window).height();
                        var x = $(window).width();

                        var heightHeader = parseInt($("#navbar").height());
						if(heightHeader < 30){
							var altoHeader = 25;
						} 
						else{
							var altoHeader = 46;
						}
						
						var hm = y - altoHeader - 65;
                        var hs = y - altoHeader - 5;
		
                        var mapa = document.getElementById("mapa");
                        var rastreo_mapa = document.getElementById("rastreo_mapa");
                        var mainInformes = document.getElementById("mainInformes");
                        var intermil = document.getElementById("intermil");
                        var intermilviajes = document.getElementById("intermil-viajes");
                      	
						
						
                        var extraIE = 0;
						if (rastreo_mapa) {
							width = parseInt(x - sidebar - 10);
                            height = parseInt(hm - 6 - extraIE) + 10;
							
							$("#mapa").css('height', parseInt(height) - 3);
							$("#mapa").css('width', width); 
							$("#rastreo_colIzqTabs").css('height', parseInt(height) - 3);
							
							$("#infogps").css('height', parseInt(height));
							$("#infogps").css('left', parseInt(x) - 252);
                        }
						else {
                            width = x - sidebar;
                            height = hm - extraIE;
							$("#mapa").css('height', height - 45);
                            $("#mapa").css('width', width - 20);
                        }
						
						
						if(mainInformes){
                            $("#listado").css('height', parseInt(height) - 64);
                        }
						
                        if (intermilviajes){
                            $("#intermil-viajes-listar").css('width', width - 235);
                        }
						
                        $("#wrapper").css('height', y);
                    }

                    $(document).ready(function(){
                        resize();
		
                        $(window).resize(function() {
                            resize();
                        });
                    });
        </script>
        <?php  include('footer.php')?>
	</div>
</body>
</html>