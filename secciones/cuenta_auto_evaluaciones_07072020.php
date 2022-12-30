
<?php 
//$tipoBotonera = 'LI-Export';
//$sinBuscador = true;
//include('includes/botoneraABMs.php');
?>
<table width="100%" height="100%">
    <thead>
        <tr>
            <td><span class="campo1">Colaborador</span></td>
            <td><center><span class="campo1">Fecha</span></center></td>
            <td class="td-last"><center><span class="campo1">Resultado de auto evaluación (Verde - todas las respuestas fueron NO / Rojo - alguna respuesta fue SI)</span></center></td>
        </tr>
    </thead>
    <tbody>
    <?php 
    if($arrEntidades){
        foreach($arrEntidades as $i => $item){
            $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
            <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>">
				<td><?=$item['device']?></td>
                <td><center><?=formatearFecha($item['date'])?></center></td>
                <td class="no_padding td-last" style=""><center><div style="width:30%; height:18px; background:<?=($item['status'] == 1) ? '#FF6A6A' : '#008000'?>">&nbsp</div></center></td>
            </tr>
        <?php } 
        }
        else{?>
            <tr class="tr-last">
                <td class="td-last" colspan="3"><center><?=$lang->message->sin_resultados?></center></td>
            </tr>
        <?php }?>
    </tbody>
</table>