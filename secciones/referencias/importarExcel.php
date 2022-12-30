<?php
//$operacion = 'Listar';
//require_once 'includes/botoneraABMs.php';
?>
<div style="margin-top:4px;width:100%;text-align:right;"><a href="javascript:window.parent.cerrarPopup();" class="f-right">&laquo;&nbsp;<?=$lang->botonera->volver?></a></div>
<span style="clear:both"></span>
<br>

<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
<input type="hidden" name="hidOperacion" value="importarReferencias" />


<fieldset>
	<legend><?=$lang->system->importar_referencias?></legend>
    <div style="padding:10px 20px;">
        <p>Seleccione el archivo con extensión *.xls o *.xlsx.</p>
        <?php //-- Bloqueo de Template: acebos2015 --//?>
        <p>Haga <a href="templates/<?=($_SESSION['idEmpresa'] == 2272)?'template_referencias_kccPeru.xlsx':'template_referencias.xlsx'?>" target="_blank">click aqui</a> para descargar el template con la estructura que deberá tener el archivo que desea importar.</p>
        <br />
        <input type="file" name="archivo" value="<?=$lang->botonera->examinar?>" />
        
        <?php /*if(!empty($mensaje)){?>
        	<center><p style="color:<?=$msg=='ok'?'#3C8DDE':'#F00'?>; padding:10px 0px;"><?=$mensaje?></p></center>
        <?php }*/?>
        
        <div class="clear"></div>
        <a id="uploadFile" class="button colorin float_r" href="javascript:;" onclick="javascript:enviar('importarReferencias')" style=" margin:10px 0px;"><?=$lang->botonera->cargar?></a>
    </div>
</fieldset>




