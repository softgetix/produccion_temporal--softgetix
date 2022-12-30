<?php require_once 'includes/botoneraABMs.php';?>			
<div id="main" class="sinColIzq">
    <div class="mainBoxLICabecera">
        <form name="frm_<?=$seccion ?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;" enctype="multipart/form-data">
            <div class="esp">
                <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
                <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
                <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion?>" />
                <input type="hidden" name="action" value="<?=$_REQUEST['action']?>" />
            </div>
            <strong><p style="margin:0 0 20px 40px; font-size:15px;">Aprobaciones y Activaciones</p></strong>
			<br >
            <table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros">
				<tr>
					<td align="right" valign="middle" height="20"><?=$lang->system->archivo?>&nbsp;&nbsp;</td>
                    <td><input type="file" name="archivo" id="archivo" /></td>
               	</tr>
            	<tr>
					<td>&nbsp;</td>
                    <td>
                    	<a class="button colorin" style="width:250px;" onclick="javascript:enviar('uploadExcel')" href="javascript:;" id="uploadFile">
							<?=$lang->botonera->cargar?>
                        </a>
                    </td>
               	</tr>
            	<tr>
					<td colspan="2" height="30">&nbsp;</td>
                </tr>
                
                <tr class="titulo">
					<td width="100%" colspan="2"><?=$lang->system->archivos_subidos?></td>
                </tr>	
            	<?php if(count($arrArchivos)){
					for($i=0; $i < count($arrArchivos); $i++) {
						$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
						<tr class="<?=$class?>">
							<td align="center" colspan="2">
                            	<a href="DescargaXls.php?archivoadt=<?=$arrArchivos[$i]?>" target="_blanck"><?=$arrArchivos[$i]?></a>
                            </td>
               	 		</tr>
                   	<?php }?>
               <?php }
			   else{?>
					<tr class="filaPar">
						<td align="center" colspan="2"><i><?=$lang->system->archivos_no_subidos?></i></td>
					</tr>
				<?php }?>   
			</table>
	    </form>
    </div>
</div>