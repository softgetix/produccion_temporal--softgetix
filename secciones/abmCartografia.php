<script type='text/javascript' src='js/boxes.js'></script>
<script type="text/javascript">
	arrLang['dir_no_encontrada'] = '<?=$lang->system->direccion_no_encontrada?>';
</script>
<!--
<script type='text/javascript' src='js/ajax.js'></script>
-->
<?php //require_once("includes/google.v3.ini");?>
<script type='text/javascript' src='js/defaultMap_Google.js'></script>
<?php if (!isset($popup)) { ?>
	<div id="colIzq">
		<?php require_once('includes/datosColIzqAbm.php')?>
	</div>
<?php } 
else{
	if($operacion=="alta"){
		if(!$popup>0){?>
			<div id="colIzq">
				<?php require_once('includes/datosColIzqAbm.php')?>
			</div>	
		<?php }
	}
} 
$botonera_old = "";
?>
<style>
select{height: 23px !important;}
</style>
<div id="main" <?php if (isset($popup)) { if ($popup>0) { $botonera_old = "_old"; ?> class="sinColIzq" <?php } } ?>>
	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
    <div class="mainBoxLICabezera">
		<h1>Administraci&oacute;n de Cartograf&iacute;a</h1>
		<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
		<input name="hidId" id="hidId" type="hidden" value="<?=isset($id)?$id:''?>" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<?php 
		switch($operacion){
			case 'listar':
			require_once 'includes/botoneraABMs.php';
		?>
	</div><!-- fin. mainBoxLICabezera -->        
    	<div id="mainBoxLI">
        	<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
            	<tr class="titulo">
                	<td width="4%"></td>
                    <td width="32%" align="center">Ubicaci&oacute;n</td>
                    <td width="32%" align="center">Provincia</td>
                    <td width="32%" align="center">Pais</td>
                </tr>
                <?php $i = 0;
                if($arrCartografia){
                    foreach($arrCartografia as $item){
                        $i++;
                        $class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
                        <tr class="<?=$class?>">
                            <td>
                            	<input type="checkbox" name="chkId[]" id="chk_<?=$item['ar_id']?>" value="<?=$item['ar_id']?>"/>
                            </td>
                            <td align="center">
                            	<a href="javascript: enviarModificacion('modificar',<?=$item['ar_id']?>)"><?=$item['Partido']?></a>
                            </td>
                            <td align="center"><?=$item['Provincia']?></td>
                            <td align="center"><?=$item['pa_nombre']?></td>
                        </tr>
					<?php }
                }
                else{?>
                    <tr class="filaPar">
                        <td colspan="6" align="center"><i><?=$lang->message->sin_resultados?></i></td>
                    </tr>
                <?php }?>
			</table>
		</div><!-- fin. #mainBoxLI -->
        <?php 
			break;
			case 'alta':
			case 'modificar':
				require_once 'includes/botoneraABMs.php';
		?>	
    </div><!-- fin. mainBoxLICabezera -->
    <style>body{overflow:hidden;}</style>  		
    	<div id="mainBoxAM">
        	<input type="hidden" name="hidPuntos" id="hidPuntos" value="<?=isset($datos["hidPuntos"])?$datos["hidPuntos"]:''?>" />
            <input type="hidden" name="HidPopUp" id="HidPopUp" value="<?php if(isset($_GET['action']))echo "popup";?>" />
            <div id="popup-content">
				<table class="widefat" width="100%"> 
					<tr>
                    	<td class="label">Ubicaci&oacute;n</td>
                        <td>
                            <input type="text" name="txtUbicacion" id="txtUbicacion" value="<?=$datos['txtUbicacion']?>"  style="width:200px;"  size=50>
                        </td>
                        <td class="label">Provincia</td>
                        <td>
                            <input type="text" name="txtProvincia" id="txtProvincia" value="<?=$datos['txtProvincia']?>"  style="width:200px;"  size=50>
                        </td>
    					<td class="label">Pais</td>
                        <td>
                            <select name="cmbPais" id="cmbPais"  style="width:204px;"> 
                            <option value=""><?=$lang->system->seleccione?></option> 
                            <?php foreach($arrPais as $item){?>
                                <option value="<?=$item['pa_id']?>"  <?=($datos['cmbPais']== $item['pa_id'])?  "selected":"";?>>
									<?=$item['pa_nombre']?>
                                </option>
                            <?php }?>
                        </select>
                        </td>
				    </tr>
				</table>
				<div id="mapa25" style="display:block;height:380px;width:100%;position:relative; margin-top:5px;"></div>
				</div>
                <script language="javascript">
					$(document).ready(function(e) {
						var y = $(window).height();
						$('#mapa25').css('height',(parseInt(y) - 180));
					});
				</script>
			<?php
			break;	
		} ?>
	</form>
</div>
