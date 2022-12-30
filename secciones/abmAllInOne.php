<style>
.valor_cod {display:block; text-align:center;}
.valor_cod span{font-weight:bold; font-size:16px; color:#000; padding:10px 30px; border:1px solid #333; display:inline-block;}
</style>
<?php if((int)$_POST['idCliente'] && empty($mensaje)){?>
<script>
	window.parent.location.href="boot.php?c=abmClientes&hidFiltro=<?=$_POST['email']?>";
</script>
<?php exit; }?>
<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?><?=($action=='popup')?'&action=popup':''?>" method="post">
	<input type="hidden" name="idCliente" value="<?=(int)$_REQUEST['idCliente']?>"/>
    <input type="hidden" name="editMail" value="<?=$_POST['editMail']?>"/>
    <input type="hidden" name="sms_enviados" value="<?=(int)$_POST['sms_enviados']?>"/>
    
    <div class="mainBoxLICabezera">
		<h1><?=($_REQUEST['idCliente'])?$lang->system->editar_cliente:$lang->system->nuevo_cliente?></h1>
		<?php if($action=='popup'){ ?>				
               <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />                               
			   <?php require_once 'includes/botoneraABMs.php';?>     
			<?php if($mensaje){ ?>
			   <div style="background:#F7F8E0;color:red;width:100%;height:15px;padding:5px;"><?=$mensaje?></div>	
			<?php } ?>
		<?php } ?>		
	</div><!-- fin. mainBoxLICabezera -->
	<div id="mainBoxLI">
	<br />
    	<table cellpadding="0" cellspacing="0" border="0" class="widefat">
		<tr>
			<td valign="middle" height="20" align="right"><?=$lang->system->email_cuenta?>&nbsp;&nbsp;</td>
            <td>
				<?php if(($_REQUEST['idCliente'] && !isset($_POST['editMail'])) || ($_REQUEST['idCliente'] && empty($_POST['editMail']))){?>
            	<input type="text" name="email" value="<?=$_POST['email']?>" style="width:210px;" disabled="disabled"/>
                <input type="hidden" name="email" value="<?=$_POST['email']?>"/>
            	<?php }
				else{?>   
                <input type="text" name="email" value="<?=$_POST['email']?>" style="width:210px;"/>
            	<?php }?>
            </td>
        </tr>
        <tr>
			<td valign="middle" height="20" align="right"><?=$lang->system->nro_entidad?>&nbsp;&nbsp;</td>
            <td><input type="text" name="codigo_usuario" value="<?=$_POST['codigo_usuario']?>" style="width:210px;"  autocomplete="off"/></td>
        </tr>
        <tr>
			<td valign="middle" height="20" align="right"><?=$lang->system->cant_licencias?>&nbsp;&nbsp;</td>
            <td><input type="text" name="cant_licencias" value="<?=$_POST['cant_licencias']?>" style="width:20px;" maxlength="2" autocomplete="off"/></td>
        </tr>	
        <tr><td colspan="2">&nbsp;</td></tr>	
        <tr>
			<td valign="middle" align="center" height="20" colspan="2">
            	<div style="text-align:left; width:415px; margin-bottom:5px;">
                	<?=$lang->system->nro_celular_sms?>
                	<span style="font-style:italic;">
                    	<?=$lang->system->msg_envio_sms?>
                    </span>
                </div>
            </td>
        </tr>	
        <tr>
			<td valign="middle" align="center" height="20"colspan="2">
            	<textarea style="width:415px; max-width:415px; min-width:415px; height:31px; max-height:31px; min-height:31px;" name="nro_cel"><?=$_POST['nro_cel']?></textarea>
            	<div style="text-align:left; width:415px; margin-bottom:5px;">
                	<span style="font-style:italic;">
                    	<?=str_replace('[SMS-MAX]',$_POST['sms_max'],str_replace('[SMS-ENVIADOS]',(int)$_POST['sms_enviados'],$lang->system->msg_envio_sms_usuario))?>
                    </span>
                </div>
            </td>
        </tr>	
        <tr>
			<td align="center" colspan="2">
            	<br />
            	<input type="submit" name="generar" value="<?=($_REQUEST['idCliente'])?$lang->botonera->actualizar_datos:$lang->system->generar_codigo?>" class="button colorin" /> 
            </td>
        </tr>
        <?php if(!empty($codigoValidacion) && empty($mensaje)){?>
        <tr><td colspan="2" height="40"></td></tr>
        <tr>
        	<td colspan="2" align="center">
        		<div class="valor_cod"><span><?=$codigoValidacion?></span></div>
        	</td>
        </tr>
        <?php }?>
        </table>
    </div>
</form>