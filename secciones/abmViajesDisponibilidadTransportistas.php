<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
    <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
    <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
    <h1>Grilla de Disponibilidad</h1>
    <div id="colIzq">
       <div id="busqueda-simple" class="clear">
        	<div class="esqueleto-calendario">
            	<a href="javascript:;" class="calendario prev"><span>&lsaquo;</span></a>
                <span class="calendario mes-actual"></span>
                <a href="javascript:;" class="calendario next"><span>&rsaquo;</span></a>
                <div id="mes-calendario" class="clear"></div>
			</div>
		</div>
        <input type="hidden" name="fecha" id="fecha" value="<?=$fecha?>"/>
        <input type="hidden" id="mes-desde" value="<?=(int)getFechaServer('m')?>" />
        <input type="hidden" id="anio-desde" value="<?=getFechaServer('Y')?>" />	
    </div><!-- fin. #colIzq-->   
    
    
    <div id="mainInformes" class="sinColIzq">
    	<p>En este apartado deberá indicar la disponibilidad de su flota para la fecha seleccionada.</p>
       	<p>La disponibilidad para el <strong id="fecha-seleccionada"><?=$fecha?></strong> es la siguiente:</p>
        
        <fieldset id="listado-moviles">
        <ul class="float_l">
        	<li>
            	<label for="group-all-movil">
                	Todos los Vehículos 
                	<input id="group-all-movil" onchange="javascript:checkGroup('all-movil')" class="float_r" type="checkbox" <?=$checkAllSelect?'checked="checked"':''?> <?=$checkDisabled?'disabled="disabled"':''?> >
                </label>
            </li>
		</ul>
        <?php if($checkDisabled){?>
        	<span class="nota">* NOTA: No puede editar la información de disponibilidad para días anteriores a la fecha.</span>
        <?php }?>
        <span class="clear"></span>
        <ul class="grupoMoviles">
		<?php foreach($arrMovilesDisponibilidad as $movil){?>
        	<li class="float_l">
            	<label for="movil-<?=$movil['idMovil']?>">
					<input id="movil-<?=$movil['idMovil']?>" name="movil[]" value="<?=$movil['idMovil']?>" type="checkbox" <?=$movil['disponibilidad']?'checked="checked"':''?> <?=$checkDisabled?'disabled="disabled"':''?> >
					<?=$movil['movil']?>
                </label>
			</li>
        <?php }?>
        <span class="clear"></span>
		</ul>
        </fieldset>
      	<center>
        	<a href="javascript:;" <?=!$checkDisabled?'onclick="javascript:enviar(\'guardarDisponibilidad\')"':''?>  style="width:200px;" class="button colorin <?=$checkDisabled?'disabled':''?>">
                Guardar Cambios
            </a>
		</center>
</form>
<script type="text/javascript">
$(document).ready(function(){
	<!-- BUSQUEDA simple -->
		getCalendario('#busqueda-simple', $('#mes-desde').val(), $('#anio-desde').val(),$('#fecha').val());
	<!-- -->	
});

function clickDate(id){
	var ide = String(id).explode('#');
	var ide_1 = ide[0];
	//var fecha = ide[1];
	ide_1 = String(ide_1).replace('.','');
	ide_1 = String(ide_1).replace(' ','-');
	
	var ide_2 = String(ide[1]).explode('-');
	
	var id = ide_2[0]+ide_2[1]+ide_2[2]; 
	
	
	var fecha = ((ide_2[0] < 10)?'0':'')+ide_2[0];
	fecha+= '-'+((ide_2[1] < 10)?'0':'')+ide_2[1];
	fecha+= '-'+ide_2[2];
	
	$('#fecha').val(fecha);
	enviar('index');
}

function checkGroup(ide){
	if(ide == 'all-movil' || ide == 'all-event'){
		
		var idpadre = $('#group-'+ide).parent().parent().parent().parent().attr('id');
		if($('#group-'+ide).is(':checked')){
			$('#'+idpadre+' input:checkbox').attr('checked',true);
		}
		else{
			$('#'+idpadre+' input:checkbox').attr('checked',false);
		}
	}
	else{
		if($('#group-'+ide).is(':checked')){
			$('.check-grupo-'+ide).attr('checked',true);
		}
		else{
			$('.check-grupo-'+ide).attr('checked',false);
		}
	}
}
</script>	