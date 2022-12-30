
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
            <td><center><span class="campo1">Distanciamientos registrados</span></center></td>
            <td><center><span class="campo1">Bluetooth (al recibir los datos)</span></center></td>
            <td><center><span class="campo1">Estado de la App (al recibir los datos)</span></center></td>
            <td><center><span class="campo1">Ubicación (al recibir los datos)</span></center></td>
            <td class="td-last"><center><span class="campo1">Tecnología Low Energy</span></center></td>
        </tr>
    </thead>
    <tbody>
    <?php 
    $auxarr = array(0 => 'No tiene', 1 => 'Tiene', 2 => 'No disponible');
    if($arrEntidades){
        foreach($arrEntidades as $i => $item){
            $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
            <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>">
				<td><?=$item['device']?></td>
                <td><center><?=formatearFecha($item['date'])?></center></td>
                <td><center><?=$item['quantity']?></center></td>
                <td><center><?=isset($auxarr[$item['bluetooth']]) ? $auxarr[$item['bluetooth']] : $auxarr[2]?></center></td>
                <td><center><?=isset($auxarr[$item['app_status']]) ? $auxarr[$item['app_status']] : $auxarr[2]?></center></td>
                <td><center><?=isset($auxarr[$item['ubicacion']]) ? $auxarr[$item['ubicacion']] : $auxarr[2]?></center></td>
                <td class="td-last"><center><?=isset($auxarr[$item['LowEnergy']]) ? $auxarr[$item['LowEnergy']] : $auxarr[2]?></center></td>
            </tr>
        <?php } 
        }
        else{?>
            <tr class="tr-last">
                <td class="td-last" colspan="7"><center><?=$lang->message->sin_resultados?></center></td>
            </tr>
        <?php }?>
    </tbody>
</table>