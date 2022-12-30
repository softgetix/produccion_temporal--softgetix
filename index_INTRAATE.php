	<link href="<?=$url?>/css/menu.css" rel="stylesheet" type="text/css"/>
    <link href="<?=$url?>/css/estilosLogin.css" rel="stylesheet" type="text/css" />
	<input type="hidden" name="hidUrl" id="hidUrl" value="<?=$url?>" />
	<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>
	<style>
		html{background-color:#FFF !important;  height: 100%;}
		h2, input, a, p, span, label{font-family:"Source Sans Pro","Helvetica Neue",Helvetica,Arial,sans-serif !important;}	
		body{height: 100%; padding:0px;}
		h2{color:#355a9d; font-size:25px; font-weight: 600; text-transform:none; border:none; letter-spacing: 0px;}
		label{border:0px; color:#96a9bd; font-size:16px; font-weight: 600;}
		label#mobil{border:0px; color:#FFF; font-size:36px;line-height:42px; font-weight: 600;}
		input[type="text"], input[type="password"]{ background-color: transparent !important; border:0px; color:#96a9bd; font-size:16px;font-weight: 600;}
		input[type="text"]:focus, input[type="password"]:focus{border:0px;}	
		hr{color:#e6eff6 !important; border-radius: 1px; background-color: #e6eff6;}	
		.button{color:#f5f5f5; background:#355a9d; border-radius: 30px; font-size:20px;
			font-weight: 600; text-transform:none; padding: 12px 45px !important; letter-spacing: 0px;}
		.button:hover{background-color:#355a9d !important;}
		#recuperar_contrasenna{color:#355a9d; font-size:14px; font-weight: 300; letter-spacing: 0px; 
			float:right; margin-top:15px;}
		#div_recuperar_contrasenna .button{color:#ffffff; background:#1780C9;}	
		#div_recuperar_contrasenna .button:hover{background-color:#1780C9 !important;}
		#div_recuperar_contrasenna  input[type="text"]{color:#1780C9 !important;}

		.footer2{width:100%;position:fixed;bottom:0px;padding:10px 20px; background:transparent !important; font-size: 14px !important; font-weight: 400;}
		.footer2 span{color:#96a9bd; float:left;line-height:14px; margin:0 10px;}
		.footer2 p,.footer2 a{line-height:30px;  text-decoration:none; margin:0px; color:#96a9bd; }

		div#body{width:100%; height: 100%;}
		@media screen and (min-width:300px) {
			#contenido{width:300px;}
			
		}
		@media screen and (max-width:1090px) {
			#block_left{clear:both; width:100%; height: 100%; padding:0px; margin:0px;}
			#block_right{clear:both; width:0px; height: 0px; padding:0px; margin:0px; background:transparent;}
		}
		@media screen and (max-width:1500px) and (min-width:1090px) {
			#block_left{float:left; width:50%; height: 100%; padding:0px; margin:0px;}
			#block_right{float:right; width:50%; height: 100%; padding:0px; margin:0px; 
				background: url("<?=$url.'/imagenes/img-clientes/home_agente_oficial_adt.jpg'?>") no-repeat top right fixed;
				background-size: auto 570px;
			;}
		}
		@media screen and (min-width:1500px) {
			#block_left{float:left; width:60%; height: 100%; padding:0px; margin:0px;}
			#block_right{float:right; width:40%; height: 100%; padding:0px; margin:0px; 
				background: url("<?=$url.'/imagenes/img-clientes/home_agente_oficial_adt.jpg'?>") no-repeat top right;
				background-size: auto 570px;
			;}
			div#body{width:1500px; height: 100%; text-align: center; margin:auto;}
		}
		
	</style>
	<?php if($esMobil == true){?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="shortcut icon" href="<?=$url?>/imagenes/img-clientes/<?=FAVICON?>" type="image/x-icon" />
		<script type="text/javascript" src="<?=$url?>/js/jquery/jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/check.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/ajax.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/funciones.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/login.js"></script>
		<script type="text/javascript" src="<?=$url?>/js/jquery.tools.js"></script>
		
		<script type="text/javascript" src="<?=$url?>/js/jquery/colorbox/jquery.colorbox-min.js"></script>
		<link rel=stylesheet type="text/css" href="<?=$url?>/js/jquery/colorbox/colorbox.css" />
	<?php }?>
</head>	
<body>
	<div id="body">
	<fieldset id="block_left">
		<div id="contenido">
			<img src="<?=$url.'/imagenes/img-clientes/logo_ate.png'?>" />
			<br><br>

			<form id="login_form" method="POST"> 
				<br><br>
				<h2>Iniciar sesión | Intra ATE</h2>
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
	</fieldset>
	<fieldset id="block_right"></fieldset>
	<div class="clear"></div>
	</div>
	<?php if(!$esMobil){?>
	<div class="clear"></div>
	<div class="footer2">
		<p class="float_l">Powered by <a href="http://www.localizar-t.com.ar">Localizar-T</a></p>
		<div class="clear"></div>
	</div>
	<?php }?>