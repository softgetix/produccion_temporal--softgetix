
<?php 
//$tipoBotonera = 'LI-Export';
//$sinBuscador = true;
//include('includes/botoneraABMs.php');
?>
<div id="buscador" class="no_padding margin_l" style="magin-botom:10px;">
	<div class="buscar_general_abm_tit">
    	<span><?=$lang->system->filtro_buscador?></span>
    </div>
    <!--<input type="text" name="txtFiltro" id="txtFiltro" class="buscar" onkeypress="if(capturarEnter(event)) enviar('index');" style="width: 295px; float:left; margin-right:10px;" value="<? //=$_POST['txtFiltro']?>" />-->
    <select name="colorFiltro"  class="buscar" style="float:left; margin-right:10px;">
        <option value="">Seleccione</option>
        <option value="1" <?=($_POST['colorFiltro'] === '1') ? 'selected="selected"':''?> >Sospechosos</option>
        <option value="0" <?=($_POST['colorFiltro'] === '0') ? 'selected="selected"':''?> >Todos</option>
    </select>
    <a class="float_l margin_l button colorin" style="margin-top:1px;" href="javascript:enviar('index');"><?=$lang->botonera->buscar?></a>
</div>
<span class="clear" style="clear:both; margin-bottom:5px"></span>

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