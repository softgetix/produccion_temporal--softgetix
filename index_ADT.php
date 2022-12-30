<link rel="stylesheet" href="<?=$url?>/css/inmental.css" type="text/css" charset="utf-8" />
</head>
<body>    
	<div class="line">
        <div class="web">
            <div class="header">
                <img src="<?=$url.'/imagenes/img-clientes/logoa-dt-dos.png'?>" />
            </div>
            <div class="body">
                <div class="login">
                    <form id="login_form" class="form-inline" action="" method="POST" style="margin:0px; padding:0px;">                         
						 <?php if($ie){?>						 
						 <table><tr><td width="245px"><?=$lang->localizart->usuario?></td><td width="245px"><?=$lang->localizart->password?></td></tr></table>
						 <?php }?>
						 <input type="text"     class="input-small" id="txtUsuario"   name="txtUsuario" placeholder="<?=$lang->localizart->usuario?>" onClick="despintarCampos();" onKeyPress="despintarCampos(); capLock(event);" onKeyUp="if(validateEnter(event) == true) { subm(); }">
                         							 
						 <input type="password" class="input-small" name="txtPassword" id="txtPassword" placeholder="<?=$lang->localizart->password?>" onClick="despintarCampos();" onKeyPress="despintarCampos(); capLock(event);" onKeyUp="if(validateEnter(event) == true) { subm(); }" >
                         <input type="button" class="btn btn-primary" onClick="subm()"  value="<?=$lang->localizart->ingresar?>" />
                         <div style=" clear:both"></div>
                        <?php if(isset($strError)){ ?>	
                        	<label style="display:block; margin-top:5px;" for="error"><?=$strError;?></label>
						<?php } ?>
					</form>
                    <a id="recuperar_contrasenna" href="#div_recuperar_contrasenna" style="display:inline-block; font-size:11px; float:right; margin-right:90px;"><?=$lang->localizart->olvido_contrasena?></a>
					<span id="caplock" class="error" style="visibility:hidden"><?=$lang->localizart->bloqueo_mayuscula?></span>
                </div>
                <div>
                    <div class="row-fluid">
                        <div class="span7">
                            <img src="<?=$url?>/imagenes/img-clientes/bk.png" />
                        </div>
                        <div class="span5">
                            <div>
                                <h4 class="titulo"><?=$lang->adt->donde_esta_title?></h4>
                                <a class="subtitulo"><?=$lang->adt->donde_esta_txt?></a>
                            </div>
                            <div>
                                <h4 class="titulo"><?=$lang->adt->donde_estuvo_title?></h4>
                                <a class="subtitulo"><?=$lang->adt->donde_estuvo_txt?></a>
                            </div>
                            <div>
                                <h4 class="titulo"><?=$lang->adt->mis_lugares_title?></h4>
                                <a class="subtitulo"><?=$lang->adt->mis_lugares_txt?></a>
                            </div>
                            <div>
                                <h4 class="titulo"><?=$lang->adt->mis_alertas_title?></h4>
                                <a class="subtitulo"><?=$lang->adt->mis_alertas_txt?></a>
                            </div>
                        </div>
                      </div>
                </div>
            </div>
            <div class="footer">
				<!--
                <p>ADT Security Services S.A. &ndash; 0810-555-1008 &ndash; <a href="http://www.adt.com.ar" style="color:#959595" >www.adt.com.ar</a> | <a href="mailto:ar.clientesadt@tycoint.com" style="color:#959595" >ar.clientesadt@tycoint.com</a> - Powered by <a href="http://www.localizar-t.com.ar" style="color:#959595" >Localizar-T</a></p>
                -->
                <p style="line-height:12px;"><img src="<?=$url?>/imagenes/banderines/flag_ar.png">ADT Argentina &ndash; 0810-555-1008 &ndash; <a href="http://www.adt.com.ar" style="color:#959595" >www.adt.com.ar</a> | <a href="mailto:ar.clientesadt@tycoint.com" style="color:#959595" >ar.clientesadt@tycoint.com</a></p>
                <p style="line-height:12px;"><img src="<?=$url?>/imagenes/banderines/flag_cl.png">ADT Chile &ndash; 600-600-0238 &ndash; <a href="http://www.adt.cl" style="color:#959595" >www.adt.cl</a> | <a href="mailto:cl.postventaadt@tycoint.com" style="color:#959595" >cl.postventaadt@tycoint.com</a></p>
            	<p style="line-height:12px;"><img src="<?=$url?>/imagenes/banderines/flag_ur.png">ADT Uruguay &ndash; 0800-8238 &ndash; <a href="http://www.adt.com.uy" style="color:#959595" >www.adt.com.uy</a> | <a href="mailto:uy.ateadt@tycoint.com" style="color:#959595" >uy.ateadt@tycoint.com</a></p>
                <center>
                	Powered by <a href="http://www.localizar-t.com.ar" style="color:#959595" >Localizar-T</a>
                </center>
            </div>
            
        </div>
	</div>
    
    <!-- ejecutar sonida para los casos en q se rompe session cuando estas en rastreo -->
   	<?php if($_SESSION['truncate_session']){?>
	<script type="text/javascript" src="<?=$url?>/js/sm2/soundmanager2-nodebug-jsmin.js"></script>
	<script language="javascript">
			if($.browser.msie){
				$('body').append('<embed src="<?=$url?>/sounds/alertas/ALARMA 2.wav" autostart="true" loop="true" style="display:none;"></embed>');
			}
			else{
				soundManager.setup( {
					"url": "<?=$url?>/swf/sm2/",
					"allowScriptAccess": "always",
					"useHTML5Audio": true,
					"preferFlash": false,
					"onready": function(){
						g_oSoundObject = soundManager.createSound({
							"id" : "mySound2",
							"url": "<?=$url?>/sounds/alertas/ALARMA 2.wav",
							"volume": 100,
							"autoLoad": true,
							"autoPlay": true,
							"loops": 9999
						});
					}
				});
			}
	</script>
    <?php $_SESSION['truncate_session'] = false;?>
    <?php }?>
    <!-- -->