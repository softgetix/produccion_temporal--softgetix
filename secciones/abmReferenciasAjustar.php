<div id="main" class="sinColIzq">
    <div class="solapas gum clear">
    <?php include('includes/navbarSolapas.php');?>
    <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<div style="height:100%" class="contenido clear"> 
        <input name="hidId" id="hidId" type="hidden" value="<?=isset($id)?$id:0?>" />
        <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
	<?php
	switch ($operacion){
            case 'listar':
            require_once 'includes/botoneraABMs.php';
	?>
            <table width="100%" height="100%">
            <thead>
                <tr>
                    <td><span class="campo1"><?=$lang->system->referencia_a_revisar?></span></td>
                    <td><span class="campo1"><?=$lang->system->viaje_analizado?></span></td>
                    <td><span class="campo1"><?=$lang->system->vehiculo?></span></td>
                    <td><center><span class="campo1"><?=$lang->system->fecha_procesamiento?></span></center></td>
                    <td><span class="campo1"><?=$lang->system->usuario?></span></td>
                    <td class="td-last">&nbsp;</td>
                </tr>
            </thead>
            <tbody>
            <?php if($ajustar){
            foreach($ajustar as $i => $item){
                $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                <tr class="<?=$class?> <?=((count($ajustar) - 1)==$i)?'tr-last':''?>">
                    <td><?=$item['re_nombre']?></td>
                    <td><?=$item['vi_codigo']?></td>
                    <td><?=$item['mo_matricula']?></td>
                    <td><center><?=date('d-m-Y',strtotime($item['iar_fecha_recomendada']))?></center></td>
                    <td><?=$item['us_nombre'].' '.$item['us_apellido']?></td>        
                    <td class="no_padding td-last">
                    	<center>
                            <?php switch($item['iar_estado']){
                                case '0':?><a href="javascript:;" onclick="enviarModificacion('ajustar',<?=$item['iar_id']?>)"><?=$lang->botonera->ajustar?></a></td><?php break;
                                case '1': echo '<strong style="color:#006633">'.$lang->system->ajustado.'</strong>';break;
                                case '2': echo '<strong style="color:#CC0000">'.$lang->system->ignorado.'</strong>'; break;
                                case '3': echo '<strong style="color:#FF9900">'.$lang->system->pendiente.'</strong>'; break;
                            }?>
                            <input type="hidden" name="chkId[]" id="chk_<?=$item['iar_id']?>" value="<?=$item['iar_id']?>"/>
			</center>
                    </td>
                </tr>
                <?php } 
                     $ajustar = count($ajustar);
                     include('secciones/footer_LI.php');
                }
            else{?>
                <tr class="tr-last">
                    <td class="td-last" colspan="6"><center><?=$lang->message->sin_resultados?></center></td>
                </tr>
            <?php }?>
            </tbody>
            </table>
            
	<?php break;
		case 'ajustar':
			require_once("includes/google.v3.ini");
			?>
            <div id="botonesABM">
            	<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
            </div>
            <span class="clear"></span>
            <div id="mapa25" style="width:100%;"></div>
			
            <div style="z-index:999; position:fixed; left:50%; margin-left:-150px; bottom:35px; width:360px; background:#FFF; border:1px solid #666; padding:8px 10px;">
            	<span>Desea mover la coordenada de <strong style="font-weight:bolder;"><?=$ajustar['re_nombre']?></strong>? (<?=$ajustar['re_numboca']?> - <?=$ajustar['re_ubicacion']?>)</span>
                <center class="clear" style="margin-top:10px;">
                	<a class="button extra-wide colorin" href="javascript:;" onclick="enviarModificacion('ajustarRecomendacion',<?=$ajustar['iar_id']?>);"><?=$lang->botonera->ref_ajustar?></a>
                	<a class="button extra-wide colorin" href="javascript:;" onclick="enviarModificacion('noRecomendar',<?=$ajustar['iar_id']?>);"><?=$lang->botonera->ref_no_ajustar?></a>
                    <a class="button extra-wide colorin" href="javascript:;" onclick="enviarModificacion('ignorarRecomendacion',<?=$ajustar['iar_id']?>);"><?=$lang->botonera->ref_ajustar_masTarde?></a>
                </center>
            </div>
            <script language="javascript">
				var arrLang = [];
				var zoom = 15;
				var refLatLngAjustar = [];
				refLatLngAjustar['lat'] = <?=$ajustar['iar_latitud']?$ajustar['iar_latitud']:0?>;
				refLatLngAjustar['lng'] = <?=$ajustar['iar_longitud']?$ajustar['iar_longitud']:0?>;
				
				var refLatLngOriginal = [];
				refLatLngOriginal['lat'] = '<?=$coord['rc_latitud']?$coord['rc_latitud']:0?>';
				refLatLngOriginal['lng'] = '<?=$coord['rc_longitud']?$coord['rc_longitud']:0?>';
				
				$( document ).ready(function() {
					var y = $(window).height();
					$('#mapa25').css('height',(parseInt(y) - 147));
										
					CrearMapa('mapa25');
					
					//-- referencia Original--//
					setMapObj(mapCircle(refLatLngOriginal['lat'], refLatLngOriginal['lng'], <?=$ajustar['radio']?$ajustar['radio']:0?>, '#4b5de4'));
					
					var srcIcono = '1/referencias/ref-wp.png';
					var arr = [];
					arr['lat'] = refLatLngOriginal['lat'];
					arr['lng'] = refLatLngOriginal['lng'];
					arr['icono'] = 'getImage.php?pathmode=rel&file='+srcIcono+'&caption=Actual';
					var marker = mapMarker(arr);
					setMap(marker,false);
					
					
					
					//-- Ubicación Recomendada --//
					setMapObj(mapCircle(refLatLngAjustar['lat'], refLatLngAjustar['lng'], <?=$ajustar['radio']?$ajustar['radio']:0?>, ''));
					
					var srcIcono = '1/referencias/ref-zonaInteres.png';
					var arr = [];
					arr['lat'] = refLatLngAjustar['lat'];
					arr['lng'] = refLatLngAjustar['lng'];
					arr['icono'] = 'getImage.php?pathmode=rel&file='+srcIcono+'&caption=Sugerida';
					var marker = mapMarker(arr);
					marker.icon.size.w = 58;
					marker.icon.size.h = 78;
					setMap(marker,false);
					
					mapSetZoom(zoom);
					mapSetCenter(refLatLngOriginal['lat'],refLatLngOriginal['lng']);
				});
			</script>	
			<?php
		break;
	}?>
    </div>
    </form>
    </div>
</div>

