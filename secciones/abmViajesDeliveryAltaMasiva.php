<script language="javascript">
	var arrLang = [];
	arrLang['cargando_viajes'] = '<?=$lang->message->msj_viaje_cargando?>';
</script>
<div id="main" class="sinColIzq">
    <div class="solapas gum clear">
    <?php include('includes/navbarSolapas.php');?>
    <form name="frm_<?=$seccion ?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;" enctype="multipart/form-data">
	<div style="height:100%" class="contenido clear"> 
            <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
            <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
            <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion?>" />
            <input type="hidden" name="errorLog" value="<?=$errorLog?>" />
            
            <fieldset>
            <br />
            <table width="100%" height="100%">
                <tr>
                    <td></td>
                    <td colspan="3"><span class="campo1">Selecione los archivos a importar</span></td>
               	</tr>
                <tr><td colspan="3" height="5"></td></tr>
                <?php switch($_SESSION['idEmpresa']){
		case 4875://Tasa
		case 4835://Arauco
                case 10827://KCC-Arg-Bis
                ?>
		<tr>
                    <td align="right" valign="middle" height="20">Archivo&nbsp;&nbsp;</td>
                    <td colspan="3"><input type="file" name="archivo" /></td>
               	</tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3">
                    	<a class="button colorin" style="width:200px;" onclick="javascript:enviar('uploadArvhivoExcel')" href="javascript:;" id="uploadFile">
							<?=$lang->botonera->cargar?>
                        </a>
                    </td>
               	</tr>
		<?php break;
		default:?>
                <tr>
                    <td align="right" valign="middle" height="20">VT12&nbsp;&nbsp;</td>
                    <td colspan="3"><input type="file" name="archivo[]" /></td>
               	</tr>
                <tr>
                    <td align="right" valign="middle" height="20">ZTMS&nbsp;&nbsp;</td>
                    <td colspan="3"><input type="file" name="archivo[]" /></td>
               	</tr>
                <tr>
                    <td align="right" valign="middle" height="20">ZSDLO05&nbsp;&nbsp;</td>
                    <td colspan="3"><input type="file" name="archivo[]" /></td>
               	</tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3">
                    	<a class="button colorin" style="width:200px;" onclick="javascript:enviar('uploadArvhivosTXT_SAP')" href="javascript:;" id="uploadFile"><?=$lang->botonera->cargar?></a>
                    </td>
               	</tr>
                <?php break;
		}?>
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
                    $i++;
                    $f = explode('-',$k); $f = $f[0];?>
                    <tr class="<?=$class?> <?=((count($arrArchivos) - 1)==$k)?'tr-last':''?>">
                        <td><center><?=substr($f,0,2).'-'.substr($f,2,2).'-'.substr($f,4,4).' '.substr($f,8,2).':'.substr($f,10,2).'hs'?></center></td>
                        <?php foreach($item as $kf => $file){?>	
                        <td class="<?=((count($item) == ($kf+1))?'td-last':'')?>"><center><a href="DescargaXls.php?fileDelivery=<?=$file?>" target="_blanck"><?=$file?></center></a></td>
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