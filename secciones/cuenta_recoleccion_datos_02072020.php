
<?php 
$tipoBotonera = 'LI-Export';
$sinBuscador = true;
include('includes/botoneraABMs.php');
?>
<table width="100%" height="100%">
    <thead>
        <tr>
            <td><span class="campo1"><?=$lang->system->movil?></span></td>
            <td><center><span class="campo1">Fecha de subida</span></center></td>
            <td class="td-last"><center><span class="campo1">Distanciamientos registrados</span></center></td>
        </tr>
    </thead>
    <tbody>
    <?php if($arrEntidades){
        foreach($arrEntidades as $i => $item){
            $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
            <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>">
				<td><?=$item['device']?></td>
                <td><center><?=formatearFecha($item['date'])?></center></td>
                <td class="no_padding td-last"><center><?=$item['quantity']?></center></td>
            </tr>
        <?php } 
        }
        else{?>
            <tr class="tr-last">
                <td class="td-last" colspan="6"><center><?=$lang->message->sin_resultados?></center></td>
            </tr>
        <?php }?>
    </tbody>
</table>