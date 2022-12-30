	<meta name="viewport" content="width=device-width, user-scalable=no">
	<link href="<?=$url?>/css/menu.css" rel="stylesheet" type="text/css"/>
    <link href="<?=$url?>/css/estilosLogin.css" rel="stylesheet" type="text/css" />
	<input type="hidden" name="hidUrl" id="hidUrl" value="<?=$url?>" />
	<?php
	$background = $url.'/imagenes/img-clientes/'.(isset($esMobil)?'back_avanti_mobile.jpg':'back_avanti.jpg');
	?>
	<link href="https://avantiequipo.com/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="https://avantiequipo.com/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="https://avantiequipo.com/assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="https://avantiequipo.com/assets/js/bootstrap.min.js"></script> 
	<nav class="navbar navbar-expand-md bg-white">
		<div class="container">
			<a class="navbar-brand" href="https://www.avantiequipo.com">
				<img src="https://avantiequipo.com/assets/images/Avanti_logo.png"  alt="Logo" class="logo-regular" style="height:50px;width:158px;">
				<img src="https://avantiequipo.com/assets/images/Avanti_logo_blanco_full.png" alt="Logo" class="logo-white" style="height:50px;width:158px;">
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navigation">
				<ul class="navbar-nav ml-auto">
				<li class="nav-item">
					<a class="nav-link" href="https://www.avantiequipo.com">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="https://www.avantiequipo.com/aspectos-tecnicos-legales.html">Aspectos técnicos y Legales</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="https://www.avantiequipo.com/prensa-avanti.html">Prensa</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="https://www.localizar-t.com.ar">Quienes somos</a>
				</li>
				</ul>
			</div> 
		</div>
	</nav>
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

		
		div#contenido{top:80px;	}
	</style>
</head>	
<body>
	<?php 
	if(isset($esMobil)){?>
	<div style="height: 100%; top:0px; max-width:100%">
		<fieldset style="width:90%; position: absolute; top: 50%; left: 50%; -moz-transform: translateX(-50%) translateY(-50%); -webkit-transform: translateX(-50%) translateY(-50%); transform: translateX(-50%) translateY(-50%);">
			<center><label id="mobil">Abre <strong>www.avantiequipo.com</strong> en tu cumputadora para comenzar a usar tu Acceso Web.</label></center>
		</fieldset>
	</div>
	<?php }
	else{?>
	<div id="contenido">	
		<form id="login_form" method="POST"> 
			<br><br>
       		<h2>Log In</h2>
            <br><br>
			<?php if(isset($strError)){?><p class="required_error"><?=$strError;?></p><?php }?>
            <p class="required_error" id="caplock" style=" visibility:hidden"><?=$lang->localizart->bloqueo_mayuscula?></p>
			<fieldset>
            	<?php if($ie){?>						 
				<label><?=$lang->localizart->usuario?></label>
            	<?php }?>
                <input type="text" name="txtUsuario" id="txtUsuario" placeholder="<?=$lang->localizart->usuario?>" onClick="despintarCampos();" onKeyPress="despintarCampos(); capLock(event);" onKeyUp="if(validateEnter(event) == true) { subm(); }">
				<hr>
            </fieldset>
            <fieldset>
            	<?php if($ie){?>						 
				<label><?=$lang->localizart->password?></label>
            	<?php }?>
                <input type="password" name="txtPassword" id="txtPassword" placeholder="<?=$lang->localizart->password?>" autocomplete="off" onClick="despintarCampos();" onKeyPress="despintarCampos(); capLock(event);" onKeyUp="if(validateEnter(event) == true) { subm(); }">
				<hr>
            </fieldset>
            <fieldset>
            	<input class="button" value="<?=$lang->localizart->ingresar?>" type="button" onClick="subm()">
				<a id="recuperar_contrasenna" href="#div_recuperar_contrasenna" class="olvido-contrasena"><?=$lang->localizart->olvido_contrasena?></a>
				<div class="clear"></div>
			</fieldset>
        </form>
	</div>
	<?php }?>
    <div class="clear"></div>
	<div class="footer2">
		<p class="float_l">Powered by <a href="http://www.localizar-t.com.ar">Localizar-T</a></p>
		<div class="clear"></div>
	</div>