<div id="main" class="sinColIzq">
	<div class="solapas gum clear">
    	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>&solapa=<?=$solapa?>" method="post">
          	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
			<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		    <input type="hidden" name="solapa" id="solapa" value="<?=$solapa?>" />                
            <input name="hidId" id="hidId" type="hidden" value="<?=(int)$id?>" />    
            <?php global $objPerfil;?>
                            
            <a class="izquierda float_l <?=($solapa=='cambiar-password')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=cambiar-password'?>"><?=$lang->system->mi_cuenta?></a>
    		<?php if($objPerfil->validarSeccion('cuenta_accesos_cuenta')){?>
            <a class="izquierda float_l <?=($solapa=='accesos_cuenta')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=accesos_cuenta'?>"><?=$lang->menu->log?></a>
            <?php }?>
            <?php if($objPerfil->validarSeccion('cuenta_usuarios')){?>
            <a class="izquierda float_l <?=($solapa=='usuarios')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=usuarios'?>"><?=$lang->menu->abmUsuarios?></a>
            <?php }?>
            <?php if($objPerfil->validarSeccion('cuenta_clientes')){?>
            <a class="izquierda float_l <?=($solapa=='clientes')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=clientes'?>"><?=$lang->menu->abmClientes?></a>
            <?php }?>
            <?php if($objPerfil->validarSeccion('cuenta_moviles')){?>
            <a class="izquierda float_l <?=($solapa=='moviles')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=moviles'?>"><?=$lang->menu->abmMoviles?></a>
            <?php }?>
            <?php if($objPerfil->validarSeccion('cuenta_conductores')){?>
            <a class="izquierda float_l <?=($solapa=='conductores')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=conductores'?>"><?=$lang->menu->abmConductores?></a>
            <?php }?>
            <?php if($objPerfil->validarSeccion('cuenta_api')){?>
            <a class="izquierda float_l <?=($solapa=='api')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=api'?>">API</a>
			<?php }?>
            <?php if($objPerfil->validarSeccion('cuenta_connect_team')){?>
            <a class="izquierda float_l <?=($solapa=='connect_team')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=connect_team'?>"><?=$lang->menu->ConnectYourTime?></a>
			<?php }?>
            <?php if($objPerfil->validarSeccion('cuenta_payment')){?>
            <a class="izquierda float_l <?=($solapa=='payment')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=payment'?>">Payment</a>
			<?php }?>
            <?php if($objPerfil->validarSeccion('cuenta_recoleccion_datos')){?>
            <a class="izquierda float_l <?=($solapa=='recoleccion_datos')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c=cuenta&solapa=recoleccion_datos'?>">Recolección de datos</a>
			<?php }?>
            
            <div style="height:100%" class="contenido flaps clear"> 
				<?php 
				if(tienePerfil(array(16,17,18))){$solapa = 'cambiar-password';}
				
				switch($solapa){
                    case 'cambiar-password':
                        include('cuenta_cambiar_password.php');
                    break;
                    case 'accesos_cuenta':
						include('cuenta_log_accesos.php');
					break;
					default:
						include('cuenta_'.$solapa.'.php');
					break;
                }
                ?>
            	<span class="clear"></span>
			</div><!-- fin. contenido--> 
        </form>  
	</div> <!-- fin. solapas-->   
</div>
