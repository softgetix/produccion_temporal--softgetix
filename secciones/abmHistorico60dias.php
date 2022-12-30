<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<input type="hidden" name="fecha_result" id="fecha_result" value="<?=str_replace('/','-',$_POST['fecha'])?>" />
    <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
    <input type="hidden" name="hidSeccion" id="hidSeccion" value="<?=$seccion?>" />
    <input type="hidden" name="hidId" id="hidId" />
    
    
    <div class="mainBoxLICabezera">
		<h1>Historico 60 d&iacute;as</h1>
	</div><!-- fin. mainBoxLICabezera -->
	<div id="mainBoxLI" style="overflow:hidden">
        <br />
        <table cellpadding="0" cellspacing="0" border="0" class="widefat">
            <tr>
                <td valign="middle" height="20" align="right">M&oacute;vil&nbsp;/&nbsp;Equipo&nbsp;&nbsp;</td>
                <td><input type="text" name="movil" style="width:250px;" value="<?=$_POST['movil']?>" /></td>
                <td valign="middle" height="20" align="right">Fecha&nbsp;&nbsp;</td>
                <td><input type="text" name="fecha" style="width:75px;" class="date" value="<?=isset($_POST['fecha'])?$_POST['fecha']:$fecha?>" /></td>
                <td><a class="button extra-wide colorin" style="margin:0 0 10px 135px; width:90px;" href="javascript:;" onclick="javascript:enviar('index')">Buscar</a></td>
                <td>&nbsp;</td>
            </tr>
            <tr><td colspan="7">&nbsp;</td></tr>
        </table>
	</div>
    <div id="mainInformes">
		<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros">
			<tr class="titulo">
				<td width="20"></td>
				<td width="150">M&oacute;vil</td>
                <td width="150">Matricula</td>
                <td width="150">Equipo</td>
                <td width="20"></td>
                <td width="20"></td>
			</tr>	
                <?php 
				if($arr_moviles){
					$i = 0;
					foreach($arr_moviles as $item){
						$i++;
						$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
					   	<tr class="<?=$class?>">
					        <td>&nbsp;</td>
					        <td align="left"><?=$item['mo_identificador']?></td>
                            <td align="left"><?=$item['mo_matricula']?></td>
                            <td align="left"><?=$item['un_mostrarComo']?></td>
                            <td style="text-align:center">
                            	<a href="javascript:;" onclick="enviar('export_xls','<?=$item['un_id']?>');" title="Exportar Excel">
                                	<img src="imagenes/excel.png" border="0" width="20" />
                                </a>
                            </td>
                            <td style="text-align:center">
                            	<a href="javascript:;" onclick="javascript:enviar('export_kml','<?=$item['un_id']?>');" title="Exportar Google Eart">
                                	<img src="imagenes/googleEarth.png" border="0" width="20" />
                                </a>
                            </td>
						</tr>
					<?php }?>
				<?php }
				else{?>
					<tr class="filaPar">
						<td width="100%" colspan="10" style="text-align:center">
							<?=(($_POST)?$lang->message->sin_resultados:'Ingrese el m&oacute;vil a filtrar')?>
                        </td>
					</tr>
				<?php }?>
			</table>
		<div>
</form>
<script language="javascript">
$(function() {	
	//$(".date").datepicker({});
	$(".date").live("focusin", function() { 
       $(this).datepicker({
            /*onSelect: function(objDatepicker){
				var fecha = $(this).val().replace('/','-');
                var fecha = fecha.replace('/','-');
				$(this).val(fecha);
            }*/
        });
    });
}); 
</script>