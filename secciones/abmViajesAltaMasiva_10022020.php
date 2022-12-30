<script language="javascript">
	var arrLang = [];
	arrLang['cargando_viajes'] = '<?=$lang->message->msj_viaje_cargando?>';
</script>
<div id="main" class="sinColIzq">
    <div class="solapas gum clear">
    <?php include('includes/navbarSolapas.php');?>
    <form name="frm_<?=$seccion ?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;" enctype="multipart/form-data">
	<div style="height:100%" class="contenido clear"> 
    <input name="hidId" id="hidId" type="hidden" value="<?=isset($id)?$id:0?>" />
        <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
        <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
        <input type="hidden" name="action" value="<?=$_REQUEST['action']?>" />
            
        <fieldset>
        <br />
            <table width="100%" height="100%">
                <tr>
                    <td></td>
                    <td colspan="3"><span class="campo1">Selecione el archivo a importar</span></td>
               	</tr>
                <tr><td colspan="3" height="5"></td></tr>
                <tr>
                    <td align="right" valign="middle" height="20"><?=$lang->system->archivo?>&nbsp;&nbsp;</td>
                    <td colspan="3"><input type="file" name="archivo" id="archivo" /></td>
               	</tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3">
                        <a class="button colorin" style="width:250px;" onclick="javascript:enviar('uploadExcel')" href="javascript:;" id="uploadFile"><?=$lang->botonera->cargar?></a>
                    </td>
               	</tr>
		    	<tr>
                    <td colspan="3" height="10">&nbsp;</td>
                </tr>
                </table>
            </fieldset>
            <br/>
            <table width="100%" height="100%">
            <thead>
                <tr>
                    <td colspan="5" class="td-last"><span class="campo1"><center><?=$lang->system->archivos_subidos?></center></span></td>
                </tr>
            </thead>
            <tbody>
            <?php if(count($arrArchivos)){
                $i = 0;
                foreach($arrArchivos as $k => $item){
                    $class = ($i % 2 == 0)?'filaPar':'filaImpar';
                    $i++;?>

                    <tr class="<?=$class?> <?=((count($arrArchivos) - 1)==$k)?'tr-last':''?>">
                        <?php foreach($item as $kf => $row){?>	
                            <td class="<?=((count($item) == ($kf+1))?'td-last':'')?>">
                                <center>
                                    <?php if(strtotime($row)){
                                        echo date('d-m-Y H:i',strtotime($row)).' hs';
                                    }
                                    else{?>   
                                        <a href="DescargaXls.php?file=<?=$row?>" target="_blanck"><?=$row?></a>
                                    <?php } ?>    
                                </center>
                            </td>
                        <?php }?>
                    </tr>
                <?php }
            }
            else{?>
                <tr class="tr-last">
                    <td class="td-last" colspan="6"><center><?=$lang->system->archivos_no_subidos?></center></td>
                </tr>
            <?php }?>
            </tbody>
            </table>
        </div>
    </form>
    </div>
</div>