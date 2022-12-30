<?php 
switch($operacion){
	case 'alta':
    case 'modificar':
        require_once 'abmGrupoMoviles/alta_mod.php';
        ?>
        <br />
        <center>
        	<a href="javascript:;" onclick="javascript:$('#cmbMovilesAsignados option').attr('selected', 'selected'); enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px;"><?=$lang->botonera->guardar?></a>
        </center>
        <br />
        <?php
    break;
	default:
    $tipoBotonera='LI-NewItem';
    //$sinBuscador = true;
    include('includes/botoneraABMs.php');
    ?>
    <table width="100%" height="100%">
        <thead>
            <tr>
                <td><span class="campo1"><?=$lang->system->grupos?></span></td>
                <td class="td-last" width="50"><center><span class="campo1"></span></center></td>
            </tr>
        </thead>
        <tbody>
        <?php 
        if($arrEntidades){
            foreach($arrEntidades as $i => $item){
                $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>">
                    <td>
                        <input type="hidden" name="chkId[]" id="chk_<?=$item['gm_id']?>" value="<?=$item['gm_id']?>"/>
                        <a href="javascript:enviarModificacion('modificar',<?=$item['gm_id']?>)"><?=$item['gm_nombre']?></a>
                    </td>
                    <td class="td-last"><center></center></td>
                </tr>
            <?php } 
            }
            else{?>
                <tr class="tr-last">
                    <td class="td-last" colspan="2"><center><?=$lang->message->sin_resultados?></center></td>
                </tr>
            <?php }?>
        </tbody>
    </table>
    <?php break;
}?>