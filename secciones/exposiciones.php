<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">google.load('visualization', '1', {packages: ['corechart']});</script>
<script type="text/javascript" src="js/draws.js"></script>

<script type="text/javascript">
var timeout;
google.charts.load('current', { 'packages': ['corechart'] });
$(document).ready(function(){
   timeout = setInterval(function () {
      if (google.visualization != undefined) {
        var $titulo = 'Cantidad de alertas por hora'; 
        var $ide = 'grafico1';
        var $cant = <?=count($data1)?>;
        var $data1 = google.visualization.arrayToDataTable([<?=$data1?>]);
        drawChartBarras($data1,$titulo,$ide,["#2C42B0"]);
        clearInterval(timeout);
      }
   }, 300);
});

$(function() {    
    $("#txtFiltro" ).autocomplete({
    	source: function( request, response ) {
        $.ajax({
        	type: "POST",
			url: 'ajax.php',
			dataType: "json",
			data:({
				accion:'get-buscador-movil',
				buscar:request.term
			}),
			success: function(data){
               response( $.map( data.resultados, function( item ) {
                	return {
                    	label: item.mo_otros + ((typeof(item.mo_matricula) != 'undefined')?' ('+ item.mo_matricula +')':''),
                        value: item.mo_matricula
                    }
                }));
			}
    	});
		},
    	minLength: 2,
        select: function( event, ui ) {
			$(this).end().val(ui.item.value);
        },
        open: function() {
        	$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
        },
        close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
        }
    });
});    
</script>
<?php if(count($data1)){?>
<center>
    <div id="grafico1" style="width: 80%; height:259px; display:inline-block"></div>
</center>
<br>
<?php }?>
<div id="main" class="sinColIzq">
	<div class="solapas gum clear">
    	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
          	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
			<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
            
            <div style="height:100%" class="contenido flaps clear"> 
                <!-- -->
                <div id="buscador" class="no_padding margin_l" style="magin-botom:10px;">
					<div class="buscar_general_abm_tit">
                    	<span><?=$lang->system->filtro_buscador?></span>
                    </div>
                    <!--<input type="text" name="txtFiltro" id="txtFiltro" class="buscar" onkeypress="if(capturarEnter(event)) enviar('index');" style="width: 295px; float:left; margin-right:10px;" value="<? //=$_POST['txtFiltro']?>" />-->
                    <select name="diasFiltro"  class="buscar" style="float:left; margin-right:10px;">
                        <!--<option value="">Seleccione</option>-->
                        <option value="1" <?=($_POST['diasFiltro'] == 1) ? 'selected="selected"':''?> >Hoy</option>
                        <option value="7" <?=($_POST['diasFiltro'] == 7) ? 'selected="selected"':''?> >Última semana</option>
                        <option value="14" <?=($_POST['diasFiltro'] == 14) ? 'selected="selected"':''?>>Últimos 14 días</option>
                    </select>
                    <a class="float_l margin_l button colorin" style="margin-top:1px;" href="javascript:enviar('index');"><?=$lang->botonera->buscar?></a>
                </div>
                <a id="botonGuardar" class="button_xls exp_excel margin_l float_r" onclick="enviar('exportar_xls');" style="margin-bottom:10px;" href="javascript:;">Exportar</a>
                <span class="clear" style="clear:both; margin-bottom:5px"></span>
               
                
                   
                
                <table width="100%" height="100%">
                    <thead>
                        <tr>
                            <td><span class="campo1"><center>Tel&eacute;fono</center></span></td>
                            <td><span class="campo1"><center>Tel&eacute;fono en cercan&iacute;a</center></span></td>
                            <td><span class="campo1"><center>Fecha en la que se rompi&oacute; el distanciamiento social</center></span></td>
                            <td class="td-last"><span class="campo1"><center>Duraci&oacute;n</center></span></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result){
                            foreach($result as $i => $item) {
                                $class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
                                <tr class="<?=$class?> <?=((count($result)-1) == $i)?'tr-last':''?>">
                                    <td><center><?=$item['id']?></center></td>
                                    <td><center><?=$item['phone']?></center></td>
                                    <td><center><?=formatearFecha($item['contact'])?></center></td>
                                    <td class="td-last"><center><?=$item['distance']?></center></td>
                                </tr>
                            <?php }?>
                        <?php }
                        else{?>
                        <tr class="tr-last">
                            <td class="td-last" colspan="4"><center><?=$lang->message->sin_resultados?></center></td>
                        </tr>
                        <?php }?>    
                    </tbody>
                </table>
                <!-- -->
            	<span class="clear"></span>
			</div><!-- fin. contenido--> 
        </form>  
	</div> <!-- fin. solapas-->   
</div>
