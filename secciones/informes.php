<script type="text/javascript">
	arrLang['sin_resultados'] = '<?=$lang->message->sin_resultados?>';
	arrLang['tiempo_carga'] = '<?=$lang->message->tiempo_carga?>';
	arrLang['seleccione_movil'] = '<?=$lang->system->seleccione_movil?>';
	arrLang['seleccione_evento'] = '<?=$lang->system->seleccione_evento?>';
	arrLang['procesando_datos'] = '<?=$lang->message->procesando_datos?>';
</script>

<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
    <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
    <input type="hidden" name="solapa" id="solapa" value="<?=$solapa?>" />
    <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
    
<div id="colIzq">
	<?php 
	$fecha = formatearFecha(getFechaServer(),'date');
	switch($solapa){
		case 'historico':
		case 'km_recorridos':
		case 'viajes':
			if($solapa=='historico'){
				if($arrMovilRastreo['id']){?>
                <a href="boot.php?c=rastreo"  target="_self" class="button volver-rastreo">
                    <img src="imagenes/raster/black/volver.png" style="float:left; margin-right:10px;">
                    <span><?=$lang->botonera->volver_rastreo?></span>
                </a>
            <?php }?>
			<input type="text" id="txtBuscar" placeholder="<?=$lang->system->indicar_movil?>" class="buscar" value="<?=encode($arrMovilRastreo['nombre'])?>" onkeyup="javascript:setBuscador(event);">
			<input type="hidden" name="idMovil" id="idMovil" value="<?=$arrMovilRastreo['id']?>"/>
			<?php }?>
            
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
    	<?php 
		break;
		default:
		?>
		<!-- Inicio. Calendario Avanzado -->
        <div id="busqueda-avanzada">
            <div class="esqueleto-calendario">
                <!-- desde -->
                <div class="calendar-desde">
                    <a href="javascript:;" class="calendario prev"><span>&lsaquo;</span></a>
                    <span class="calendario mes-actual"></span>
                    <a href="javascript:;" class="calendario next"><span>&rsaquo;</span></a>
                    <div id="mes-calendario" class="clear"></div>
                </div>
                <!-- hasta -->
                <div class="calendar-hasta mt_20">
                    <a href="javascript:;" class="calendario prev"><span>&lsaquo;</span></a>
                    <span class="calendario mes-actual"></span>
                    <a href="javascript:;" class="calendario next"><span>&rsaquo;</span></a>
                    <div id="mes-calendario" class="clear"></div>
                </div>
            </div>
        </div><!-- fin. #busqueda-avanzada-->
        <input type="hidden" name="fecha_desde" id="fecha_desde" value="<?=$fecha?>"/>
        <input type="hidden" id="mes-desde" value="<?=(int)getFechaServer('m')?>" />
        <input type="hidden" id="anio-desde" value="<?=getFechaServer('Y')?>" />
        <input type="hidden" name="fecha_hasta" id="fecha_hasta" value="<?=$fecha?>"/>
        <input type="hidden" id="mes-hasta" value="<?=(int)getFechaServer('m')?>" />
        <input type="hidden" id="anio-hasta" value="<?=getFechaServer('Y')?>" />
        <?php
		break;
	}?>
</div><!-- fin. #colIzq-->

<div id="mainInformes" class="sinColIzq">
	<div class="solapas gum clear">
    	<a class="izquierda float_l <?=($solapa=='historico')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=historico'?>"><?=$lang->system->historico?></a>
        
		<?php global $objPerfil;?>
		<?php if($objPerfil->validarSeccion('informes_historico_avanzado')){?>
        	<a class="izquierda float_l <?=($solapa=='historico_avanzado')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=historico_avanzado'?>"><?=$lang->system->historico_avanzado?></a>
        <?php }?>
        <?php if($objPerfil->validarSeccion('informes_km_recorridos')){?>
        	<a class="izquierda float_l <?=($solapa=='km_recorridos')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=km_recorridos'?>"><?=$lang->system->km_recorridos?></a>
        <?php }?>
		<?php if($objPerfil->validarSeccion('informes_viajes')){?>
        	<a class="izquierda float_l <?=($solapa=='viajes')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=viajes'?>"><?=$lang->system->viajes?></a>
        <?php }?>
		<?php if($objPerfil->validarSeccion('informes_alertas')){?>
        	<a class="izquierda float_l <?=($solapa=='alertas')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=alertas'?>"><?=$lang->system->alertas?></a>
        <?php }?>
        
        <?php /*
            <a class="izquierda float_l <?=($solapa=='correo_enviado')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=correo_enviado'?>"><?=$lang->system->correos_enviados?></a>
		*/
		
		switch($solapa){
			case 'historico':
				include('informe_historico.php');
			break;
			case 'historico_avanzado':
				include('informe_historico_avanzado.php');
			break;
			case 'km_recorridos':
				include('informe_km_recorridos.php');
			break;
			case 'viajes':
				include('informe_viajes.php');
			break;	
			/*case 'correo_enviado':
				include('informe_mails_enviados.php');
			break;*/
			case 'alertas':
				include('informe_alertas.php');
			break;
		}
		?>
	</div><!-- fin. solapas-->
</form>