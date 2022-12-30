<link href="<?=$url?>/css/menu.css" rel="stylesheet" type="text/css"/>
    <link href="<?=$url?>/css/estilosLogin.css" rel="stylesheet" type="text/css" />
	<input type="hidden" name="hidUrl" id="hidUrl" value="<?=$url?>" />

	<?php
	switch(SITE){
		case 'AGENTEOFICIALADT':?>
		<style>
		html{background-color: transparent !important;}	
		body {background-color: transparent;background-repeat: no-repeat;background-position: center top;background-size:contain;background-attachment: fixed;
				background-image: url("<?=$url?>/imagenes/img-clientes/back_agenteoficialadt6.png");}
		</style>
		<?php
		break;
	}
	?>
</head>	
<body>
	<div id="navbar">
    	<img id="logoSite" src="<?=$url.'/imagenes/img-clientes/'.LOGO?>" />
        <div class="clear"></div>
	</div>
    <div id="contenido">
        <form id="login_form" method="POST"> 
       		<h2><?=$lang->localizart->label_iniciar_session?></h2>
            <?php if(isset($strError)){?><p class="required_error"><?=$strError;?></p><?php }?>
            <p class="required_error" id="caplock" style=" visibility:hidden"><?=$lang->localizart->bloqueo_mayuscula?></p>
			<fieldset>
            	<?php if($ie){?>						 
				<label><?=$lang->localizart->usuario?></label>
            	<?php }?>
                <input type="text" name="txtUsuario" id="txtUsuario" placeholder="<?=$lang->localizart->usuario?>" onClick="despintarCampos();" onKeyPress="despintarCampos(); capLock(event);" onKeyUp="if(validateEnter(event) == true) { subm(); }">
            </fieldset>
            <fieldset>
            	<?php if($ie){?>						 
				<label><?=$lang->localizart->password?></label>
            	<?php }?>
                <input type="password" name="txtPassword" id="txtPassword" placeholder="<?=$lang->localizart->password?>" autocomplete="off" onClick="despintarCampos();" onKeyPress="despintarCampos(); capLock(event);" onKeyUp="if(validateEnter(event) == true) { subm(); }">
            </fieldset>
            <fieldset>
            	<center>
                	<input class="button" value="<?=$lang->localizart->ingresar?>" type="button" onClick="subm()">
                </center>
			</fieldset>
            <center><a id="recuperar_contrasenna" href="#div_recuperar_contrasenna" class="olvido-contrasena"><?=$lang->localizart->olvido_contrasena?></a></center>
        </form>
    </div>
    