<?php $sinColIzq=true;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
		<meta http-equiv="Pragma" content="no-cache"/>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?=TITLE?></title>
		
		<link type="text/css" rel="stylesheet" href="css/estilosDefault.css"/>
        <link type="text/css" rel="stylesheet" href="css/estilosABMDefault.css"/>
        
		<?php global $sinDefaultCSS; 
		if(!isset($sinDefaultCSS)){
			$urlInclude = 'css/estilos'.ucwords($seccion).'.css';
			if(is_file($urlInclude)){?>
				<link type="text/css" rel="stylesheet" href="<?=$urlInclude?>"/>
			<?php }?>
		<?php } 
		
if(isset($extraCSS) && is_array($extraCSS)){
	foreach($extraCSS as $url){ ?>
		<link type="text/css" rel="stylesheet" href="<?=$url;?>"/>
<?php	}
}
	if(isset($extraStyle)){ ?>
		<style type="text/css">
		<?=$extraStyle;?>
		</style>
<?php 	} ?>

	<script anguage="javascript" type="text/javascript">
		var arrLang = [];
		arrLang['msj_select_baja'] = '<?=$lang->message->msj_select_baja?>';
		arrLang['msj_limite_baja'] = '<?=$lang->message->msj_limite_baja?>';
		arrLang['msj_confirmar_baja'] = '<?=$lang->message->msj_confirmar_baja?>';
		arrLang['msj_alerta_baja_equipo'] = '<?=$lang->message->msj_alerta_baja_equipo?>';
		arrLang['msj_un_update'] = '<?=$lang->message->msj_un_update?>';
		arrLang['msj_select_update'] = '<?=$lang->message->msj_select_update?>';
		arrLang['msj_cargando'] = '<?=$lang->message->msj_cargando?>';
	</script>
	<script type="text/javascript" src="js/jquery.1.7.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.tools.js"></script>
	<script type="text/javascript" src="js/jquery.ui.js"></script>
	<link type="text/css" rel="stylesheet" href="css/smoothness/jquery-ui-1.8.4.custom.css"/>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/funciones.js?3"></script>
	<script type="text/javascript" src="js/check.js"></script>
	<?php global $sinDefaultJS; 
	$sinDefaultJS = is_file('js/'.$seccion.'Funciones.js')?$sinDefaultJS:'';
	if(!isset($sinDefaultJS)){ ?>
    	<script type="text/javascript" src="js/<?=$seccion?>Funciones.js?1"></script>
    <?php }?>
<?php 	if(isset($extraJS) && is_array($extraJS)){
		foreach($extraJS as $url){ ?>
		<script type="text/javascript" src="<?=$url;?>"></script>
<?php		}
	}?>
    
    <script>
		<?php if (isset($recargarAlCerrar) && $recargarAlCerrar === true): ?>
		var recargar = true;
		<?php else: ?>
		var recargar = false;
		<?php endif; ?>
	</script>
        
	</head>
	<input type="hidden" name="hidUrl" id="hidUrl" value="<?php echo $_SESSION["DIRCONFIG"]?>" />
	<input type="hidden" name="hidLatDefecto" id="hidLatDefecto" value="<?php echo $_SESSION["lat"]?>" />
	<input type="hidden" name="hidLngDefecto" id="hidLngDefecto" value="<?php echo $_SESSION["lng"]?>" />
	<input type="hidden" name="hidZoomDefecto" id="hidZoomDefecto" value="<?php echo $_SESSION["zoom"]?>" />
	<body id="cuerpo" style="background:none;">
		
        <?php if (isset($mensaje) && strlen($mensaje) > 5): ?>
        <div id="messageDefaultLocalizart" style="left:20%; width:60%;">
            <a href="javascript: cerrarMensaje();"><img id="imgCerrarMensaje" src="imagenes/cerrar.png" /></a>

            <?php if ($tipoBotonera == 'AM' && !(isset($noError) && $noError)): ?>
                <span style='color:#ff0000; display: block;  width:90% !important; '><b><?=$lang->message->info_error?></b></span><br/><br/><?= $mensaje; ?><br/>
            <?php else: ?>
                <span style='color:#000000;'><br/><?= $mensaje; ?><br/></span><br/>
    		<?php endif; ?>
		</div><!-- #messageDefaultLocalizart -->
        <?php endif; ?>
        
        <div id="wrapper" <?=$scroll ? 'style="overflow-y:auto"':''?> >
        	<div id="content">
        		<?php if($seccion == 'verificarEquipo'){ ?>
        	    <div class="mainBoxLICabezera">
        	        <h1>Verificaci&oacute;n de Equipos</h1>
        	        <form enctype="multipart/form-data" name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
        	            <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=($_GET['action'] == "popup") ? "guardarM" : ""?>" />
        	            <input name="hidId" id="hidId" type="hidden" value="<?=isset($id) ? $id : ""?>" />
        	            <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
        	            <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
        	            <input type="hidden" name="HidPopUp" id="HidPopUp" value="<?=$popup?'popup':(isset($_GET['action'])?'popup':NULL)?>" />
						<? require_once 'includes/botoneraABMs.php';?>
        	        </form>
        	    </div>
				<? }?>
	
        	    <?php if($seccion!="accesDenied"){ require_once("secciones/" . $seccion . ".php");} ?>
				<div class="clear" ></div>
			</div>
		</div><!-- wrapper -->
		<div id="divAlerta" class="divAlerta"></div>
	</body>
</html>
