<div id="botoneraABM">
	<?php if (isset($desplegablePlantilla)): ?>
		<select id="cmbPlantilla">
			<option value="0">Ninguna</option>
			<?php foreach($arrDesplegable as $opcion): ?>
			<option value="<?php echo $opcion['id'];?>"><?php echo $opcion['dato'];?></option>
			<?php endforeach; ?>
		</select>
	<?php endif; ?>

	<?php 
	switch($tipoBotonera){
		##-- $tipoBotonera --##
		case 'AM':
			switch($operacion){
				case 'alta': ?>
					<div id="botonesABM">
					<?php if (isset($esReferencia)) { ?>
						<span id="botonVolver" onclick="window.location.href='boot.php?c=abmReferencias'"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
					<?php } else { ?>
						<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
					<?php } ?>
					<?php if (isset($popup)){ 
							if ($popup>0){?>
								<span id="botonGuardar" onclick="enviar('guardarA');"> 
                                	<img src="imagenes/botonGuardar.png" alt="" />
									<?=$lang->botonera->guardar?>
                                </span>
						<?php }
					}?>
					<!--document.getElementById('idAlerta').value=1;enviar('guardarA');-->
					</div>
				<?php break;
				case 'modificar':?>
					<div id="botonesABM">
						<?php if (isset($popup) && $popup>0) {?>
						<span id="botonGuardar" onclick="enviar('guardarM');"> <img src="imagenes/botonGuardar.png" alt="" /> <?=$lang->botonera->guardar?> </span>
						<span id="botonVolver" onclick="window.parent.cerrarPopup();"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->cerrar?> </span>
                        <?php }
						else{ ?>
						<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
						<?php }?>
					</div>
				<?php break;
				case 'Listar':?>
					<div id="botonesABM">
						<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>												
					</div>
				<?php break;
				case 'modificarAsignacion':?>
					<div id="botonesABM">
						<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
						<?php if(isset($popup) && $popup>0){?>
						<span id="botonGuardar" onclick="enviar('guardarAsignacion');"> <img src="imagenes/botonGuardar.png" alt="" /> <?=$lang->botonera->guardar?> </span>
						<?php }?>
					</div>
				<?php break;
				case 'altaAsignacion':?>
					<div id="botonesABM">
						<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
						<?php if(isset($popup) && $popup>0){?>
						<span id="botonGuardar" onclick="enviar('guardarAltaAsignacion');"> <img src="imagenes/botonGuardar.png" alt="" /> <?=$lang->botonera->guardar?> </span>
						<?php }?>
					</div>
				<?php break;
				case 'modificarHorarios':?>
					<div id="botonesABM">
						<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
					</div>
				<?php break;
				case 'ErrorEquipoAsociado':?>
					<div id="botonesABM">
						<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
					</div>
				<?php break;
				case 'index':?>
					<div id="botonesABM">
						<span id="botonGuardar" onclick="enviar('guardar');"> <img src="imagenes/botonGuardar.png" alt="" /> <?=$lang->botonera->guardar?> </span>
					</div>
				<?php break;
			}
		break;
		##-- $tipoBotonera --##
		case 'A':
			switch($operacion){
				case 'alta':	
					?>
					<div id="botonesABM">
					<?php if (isset($esReferencia)) { ?>
						<span id="botonVolver" onclick="window.location.href='boot.php?c=abmReferencias'"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
					<?php } else { ?>
						<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
					<?php } ?>
					<span id="botonGuardar" onclick="enviar('guardarA');"> <img src="imagenes/botonGuardar.png" alt="" /> <?=$lang->botonera->guardar?> </span>
					</div>
				<?php break;
			}	
		break;
		##-- $tipoBotonera --##
		case 'LI':
		case 'LI-Export':
			if (!isset($sinBuscador)) { ?>
				<div id="buscador" class="no_padding margin_l">
					<div class="buscar_general_abm_tit">
                    	<span><?=(isset($tituloFiltroBuscador))? $tituloFiltroBuscador:$lang->system->filtro_buscador?></span>
                    </div>
                    <input type="text" name="txtFiltro" id="txtFiltro" class="buscar" onkeypress="if(capturarEnter(event)) enviar('index');" style="width: 295px;" value="<?=!empty($filtro)?$filtro:trim($_POST['txtFiltro'])?>" />
				</div>
			<?php } ?>
			<?php if($tipoBotonera == 'LI-Export'){?>
				<a id="botonGuardar" class="button_xls exp_excel margin_l float_r" onclick="enviar('exportar_xls');" style="margin-bottom:10px;" href="javascript:;"><?=$lang->botonera->exportar?></a>	
			<?php }?>

			<?php if(!empty($btn_action) && !empty($btn_name)){?>
				<a id="botonGuardar" class="button margin_l float_r extra-wide colorin" onclick="<?=$btn_action?>" style="margin-bottom:10px;" href="javascript:;"><?=$btn_name?></a>
			<?php }?>
			
			<div id="botonesABM">
            <?php if(tienePerfil(17) && $seccion == 'abmClientes'){ ?>			
                <a href="http://www.localizar-t.com.ar/findupedia/index.php/P%C3%A1gina_principal" target="_blank" style="margin-top:4px;" class="button">Findupedia</a>
                <!--<a href="javascript:mostrarPopup('boot.php?c=abmAllInOne&action=popup',600,360)" target="_self" id="botonAllInOne" style="margin-top:4px; width:120px;" class="button colorin">Alta nuevo cliente</a>-->
            <?php }
            else{?>		
                <span id="botonBaja" onclick="enviar('baja');" style="display:none;"><img src="imagenes/botonEliminar.png" alt="" /></span>			
            <?php } ?>
			</div>
		<?php
		break;
		##-- $tipoBotonera --##
		case 'LIasignacion2':
			if (!isset($sinBuscador)){?>
            <div id="buscador">
                <div class="buscar_general_abm_tit">
                    <?=(isset($tituloFiltroBuscador))? $tituloFiltroBuscador:""?>
                </div>
                <input type="text" name="txtFiltro" id="txtFiltro" class="buscar" onkeypress="if(capturarEnter(event)) enviar('index');" style="width: 295px;" value="<?=$filtro?>" />
                <!--<img src="imagenes/lupaChica.png" alt="Buscar" width="20" onclick="enviar('index');"/>-->
            </div>
            <?php } ?>
            <div id="botonesABM">
                <!--<span onclick="enviar('altaAsignacion');"> <img src="imagenes/botonAgregar.png" alt="" width="32" /> </span> -->
                <span onclick="enviar('baja');" style="display:none;" id="botonBaja"> <img src="imagenes/botonEliminar.png" alt="" /> </span>
    			<?php /* span onclick="enviar('modificarAsignacion');"> <img src="imagenes/botonModificar.png" alt="" width="20" /> </span> */?>
            </div>
		<?php break;
		##-- $tipoBotonera --##
		case 'visualizacion':
			switch($operacion){
                case 'alta': ?>
                    <div id="botonesABM">
                        <span id="botonVolver"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
                    </div>
				<?php break;
				case 'modificar':?>
					<div id="botonesABM">
						<span onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
					</div>
				<?php break;
			}
		break;
		case 'LI-NewItem':
		case 'LI-NewItem-Export':
			?>
			 <div id="buscador" class="no_padding margin_l">
                <div class="buscar_general_abm_tit">
                    <span><?=(isset($tituloFiltroBuscador))? $tituloFiltroBuscador:$lang->system->filtro_buscador?></span>
                </div>
                <input type="text" name="txtFiltro" id="txtFiltro" class="buscar" onkeypress="if(capturarEnter(event)) enviar('index');" style="width: 295px;" value="<?=trim($_POST['txtFiltro'])?>" />
            </div>
            <?php if($tipoBotonera == 'LI-NewItem-Export'){?>
				<a id="botonGuardar" class="button_xls margin_l float_r" onclick="enviar('exportar_xls');" style="margin-bottom:10px;" href="javascript:;"><?=$lang->botonera->exportar?></a>	
			<?php }?>
            <a id="botonGuardar" class="button extra-wide colorin float_r" onclick="enviar('alta');" style="margin-bottom:10px;" href="javascript:;"><?=$lang->botonera->agregar_nuevo?></a>
			<?php 
		break;
	}
?>
</div>