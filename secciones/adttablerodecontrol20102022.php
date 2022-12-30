 <div id="main" class="sinColIzq">
    <?php  if  ($_SESSION['idAgente'] == 11273 || $_SESSION['idEmpresa']== 11273 ){ if(!ES_MOBILE){require_once 'banner/banner.php';}}?>	
    <div class="solapas clear">
    <div style="height:100%" class="contenido clear"> 
	
    <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
        <?php if(!ES_MOBILE){?>
            <div class="box_buscador padding_b">
            <?php if(tienePerfil(array(32,36))){?>
            <input type="text" class="date fdesde float_l" name="fdesde" placeholder="Fecha Inicio" autocomplete="off" value=<?=$_POST['fdesde']?> >
            <input type="text" class="date fhasta float_l" name="fhasta" placeholder="Fecha Fin" autocomplete="off" value=<?=$_POST['fhasta']?>>
            <a class="float_l margin_l button colorin" style="margin-top:6px;" href="javascript:enviar('index');">Buscar</a>
            <?php } ?>
            <a id="botonGuardar" class="button_xls margin_l float_r" onclick="javascript:enviar('exportar_xls');" style="margin-top:6px;" href="javascript:;">Exportar Contenido</a>
            <span class="clear"></span>
        </div>

        <?php }?>	
    </form>

    <?php if($tables){
        foreach($tables as $table){?>
    
            <table width="100%" height="100%">
                <thead>
                <tr>
                    <?php 
                    $total_colum = count($table[0]);
                    $colum = 1;
                    foreach($table[0] as $key => $item){?>
                        <td <?php if($colum == $total_colum){?>class="td-last"<?php }?>><span class="campo1"><center><?=encode($key)?></center></span></td>
                    <?php $colum++; }?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($table as $i => $row){
                    $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                    <tr class="<?=$class?> <?=((count($table) - 1)==$i)?'tr-last':''?>">
                    <?php foreach($row as $item){?>
                        <td><?=encode($item)?></td>
                    <?php }?>    
                    </tr>
                <?php }?>
                <tbody>
            </table>
            <br /><br /> 
        <?php }?>       
    <?php }     
    else{?>
        <table width="100%" height="100%">
        <tbody>
        <tr class="tr-last">
           <td class="td-last"><center><?=$lang->message->sin_resultados?></center></td>
        </tr>
        <tbody>
        </table> 
    <?php }?>
     
    </div>
    </div>
</div>


<script language="javascript">
$(function() {	
	$(".date").live("focusin", function() {
       $(this).datepicker({});
    });
});
</script>