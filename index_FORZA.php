	<link href="<?=$url?>/css/menu.css" rel="stylesheet" type="text/css"/>
    <link href="<?=$url?>/css/estilosLogin.css" rel="stylesheet" type="text/css" />
	<input type="hidden" name="hidUrl" id="hidUrl" value="<?=$url?>" />
	<?php
	//$background = $url.'/imagenes/img-clientes/'.(isset($esMobil)?'back_forza_mobile2.jpg':'back_forza.jpg');
	$background = isset($esMobil) ? NULL : ($url.'/imagenes/img-clientes/back_forza.jpg');
	?>
	<style>
		html{background-color: transparent !important;  height: 100%;}
		h2, input, a, p, span, label{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif !important;}	
		body {background-color: transparent;background-repeat: no-repeat;background-position: center top;background-size:contain;background-attachment: fixed;
				background: url("<?=$background?>") no-repeat top center fixed; 
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
				height: 100%;
		}
		h2{color:#FFF; font-size:32px; font-weight:normal; text-transform:none; border:none; letter-spacing: 0px;}
		label{border:0px; color:#9ED2FF; font-size:16px;}
		label#mobil{border:0px; color:#FFF; font-size:36px;line-height:42px;}
		input[type="text"], input[type="password"]{ background-color: transparent !important; border:0px; color:#9ED2FF; font-size:16px; }
		input[type="text"]:focus, input[type="password"]:focus{border:0px;}	
		hr{color:#9ED2FF;}	
		.button{color:#1780C9; background:#ffffff; border-radius: 30px; font-size:20px; border-color:#ffffff;
			font-weight: normal; text-transform:none; padding: 12px 45px !important; letter-spacing: 0px;}
		.button:hover{background-color:#ffffff !important;}
		#recuperar_contrasenna{color:#ffffff; font-size:14px; font-weight: normal; letter-spacing: 0px; 
			float:right; margin-top:15px;}
		#div_recuperar_contrasenna .button{color:#ffffff; background:#1780C9;}	
		#div_recuperar_contrasenna .button:hover{background-color:#1780C9 !important;}
		#div_recuperar_contrasenna  input[type="text"]{color:#1780C9 !important;}

		.footer2{width:100%;position:fixed;bottom:0px;padding:10px 20px; background:transparent !important; font-size: 14px !important;}
		.footer2 span{color:#9ED2FF; float:left;line-height:14px; margin:0 10px;}
		.footer2 p,.footer2 a{line-height:30px;  text-decoration:none; margin:0px; color:#9ED2FF; }

		#navbar{background:none;  border:0px; box-shadow:none;}

		/*Ini. Mobile*/
		html {background-color:#FFF !important;  height: 100%;}
		body.mobile div#contenido h2{color:#355a9d !important; font-size:40px; font-weight: 600; text-transform:none; border:none; letter-spacing: 0px;}
		body.mobile div#contenido label{border:0px; color:#96a9bd; font-size:16px; font-weight: 600;}
		body.mobile div#contenido label#mobil{border:0px; color:#FFF; font-size:36px;line-height:42px; font-weight: 600;}
		body.mobile div#contenido input[type="text"], input[type="password"]{ background-color: transparent !important; border:0px; color:#96a9bd; font-size:16px;font-weight: 600;}
		body.mobile div#contenido input[type="text"]:focus, input[type="password"]:focus{border:0px;}	
		body.mobile div#contenido hr{color:#e6eff6 !important; border-radius: 1px; background-color: #e6eff6;}	
		body.mobile div#contenido .button{color:#f5f5f5; background:#355a9d; border-radius: 30px; font-size:20px;
			font-weight: 600; text-transform:none; padding: 12px 45px !important; letter-spacing: 0px;}
		body.mobile div#contenido .button:hover{background-color:#355a9d !important;}
		
		body.mobile .footer2{width:100%;position:fixed;bottom:0px;padding:10px 20px; background:transparent !important; font-size: 14px !important; font-weight: 400;}
		body.mobile .footer2 span{color:#96a9bd; float:left;line-height:14px; margin:0 10px;}
		body.mobile .footer2 p,.footer2 a{line-height:30px;  text-decoration:none; margin:0px; color:#96a9bd; }

		body.mobile div#contenido{width:300px;}		
		/*Fin. Mobile*/	

	</style><!--"Trebuchet MS", Helvetica, sans-serif-->

	<?php if($esMobil == true){?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="shortcut icon" href="<?=$url?>/imagenes/img-clientes/<?=FAVICON?>" type="image/x-icon" />
		<script type="text/javascript" src="<?=$url?>/js/jquery/jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/check.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/ajax.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/funciones.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/login.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/jquery.tools.js"></script>
	<?php }?>
</head>	
<body <?=isset($esMobil) ? 'class="mobile"' : ''?> >
	<?php /*if(isset($esMobil)){?>
	<div style="height: 100%; top:0px; max-width:100%">
		<fieldset style="width:90%; position: absolute; top: 50%; left: 50%; -moz-transform: translateX(-50%) translateY(-50%); -webkit-transform: translateX(-50%) translateY(-50%); transform: translateX(-50%) translateY(-50%);">
			<center><label id="mobil">Abre <strong>www.forzagps.com</strong> en tu cumputadora para comenzar a usar tu Acceso Web.</label></center>
		</fieldset>
	</div>
	<?php }
	else{*/?>
	<div id="contenido">
		<?php if(isset($esMobil)){?>
		<img src="<?=$url.'/imagenes/img-clientes/forza_logo.jpg'?>" />
		<br><br>
		<?php }?>

		<form id="login_form" method="POST"> 
			<br><br>
       		<h2>Iniciar Sesión</h2>
            <br><br>
			<?php if(isset($strError)){?><p class="required_error"><?=$strError;?></p><?php }?>
            <p class="required_error" id="caplock" style=" visibility:hidden"><?=$lang->localizart->bloqueo_mayuscula?></p>
			<fieldset>
            	<?php if($ie){?>						 
				<label><?=$lang->localizart->usuario?></label>
            	<?php }?>
                <input type="text" name="txtUsuario" id="txtUsuario" placeholder="<?=$lang->localizart->usuario?>" onClick="despintarCampos();" onKeyPress="despintarCampos(); capLock(event);" <?=!$esMobil ? 'onKeyUp="if(validateEnter(event) == true) { subm(); }"':''?> >
				<hr>
            </fieldset>
            <fieldset>
            	<?php if($ie){?>						 
				<label><?=$lang->localizart->password?></label>
            	<?php }?>
				<input type="password" name="txtPassword" id="txtPassword" placeholder="<?=$lang->localizart->password?>" autocomplete="off" onClick="despintarCampos();" onKeyPress="despintarCampos(); capLock(event);" <?=!$esMobil ? 'onKeyUp="if(validateEnter(event) == true) { subm(); }"':''?> >
				<hr>
            </fieldset>
            <fieldset>
            	<input class="button" value="<?=$lang->localizart->ingresar?>" type="button" onClick="javascript:subm();">
				<?php if(!$esMobil){?>
				<a id="recuperar_contrasenna" href="#div_recuperar_contrasenna" class="olvido-contrasena"><?=$lang->localizart->olvido_contrasena?></a>
				<?php }?>
				<div class="clear"></div>
			</fieldset>
        </form>
	</div>
	<?php //}?>
    <div class="clear"></div>
	<!--<div class="footer2">
		<p class="float_l">Powered by <a href="http://www.localizar-t.com.ar">Localizar-T</a></p>
		<div class="clear"></div>
	</div>
	-->