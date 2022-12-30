<?php 
switch($operacion){
	case 'listar':
		if($seccion=="abmGrupoMoviles"){
			$acc = "altaAsignacion";}
		else{
			$acc = "alta";}
		
		$verAlta = false;
		$verAlta = ($seccion!='abmMoviles' && $seccion!='abmEquipos' && $seccion!='abmClientes' && $seccion!='abmConductores' && $seccion!='abmViajes')?true:$verAlta;
		$verAlta = ($seccion == 'abmClientes' && tienePerfil(array(5,9,13,19)))?true:$verAlta;
		$verAlta = ($seccion == 'abmConductores' && !tienePerfil(array(7,11)))?true:$verAlta;
		$verAlta = ($seccion == 'abmViajes' && !tienePerfil(array(8,12)))?true:$verAlta;
		if($verAlta){ ?>
        <div style="width:210px;">
			<a href="javascript:;" style="margin-bottom:10px; width:225px;" id="botonGuardar" onclick="enviar('<?=$acc?>');" class="button extra-wide colorin">
            	<?=$lang->botonera->agregar?>
            </a><br>
		</div>
        <?php }?>
        
        <?php if(function_exists('export_xls') && !tieneperfil(16)){?>
			<div style="width:210px;">
            	<a href="javascript:;" onclick="javascript:enviar('export_xls')" id="botonGuardar" style="margin-bottom:10px; width:225px;" class="button_xls exp_excel">
                	<?=$lang->botonera->exportar_excel?>
   				</a><br>
        	</div>	
		<?php }
		
		## Excepcion de reportes ##
		if($seccion == 'abmEquipos'){?>
			<div style="width:210px;">
            	<a href="javascript:;" onclick="javascript:enviar('export_equipos_status_xls')" id="botonGuardar" style="margin-bottom:10px; width:225px;" class="button_xls exp_excel">
                	<?=$lang->botonera->exportar_status_equipos?>
   				</a><br>
        	</div>
		<?php }
		## -- ##
		
		## Excepcion de importación ##
		if($seccion == 'abmReferencias' && tieneperfil(array(19,5,9))){?>
			<div style="width:210px;">
            	<a href="javascript:;" onclick="javascript:mostrarPopup('boot.php?c=<?=$seccion?>&action=importarExcel',480,250);" id="botonGuardar" style="margin-bottom:10px; width:225px;" class="button_xls exp_excel">
                	<?=$lang->botonera->importar_excel?>
   				</a><br>
        	</div>
		<?php }
		## -- ##
	break;
	case 'alta':
	?>
	<div style="width:210px;">
		<a href="javascript:;" style="margin-bottom:10px; width:225px;" id="botonGuardar" onclick="enviar('guardarA');" class="button extra-wide colorin">
        	<?=$lang->botonera->guardar?>
        </a>
        <br>
	</div>
    <?php 
	break;
	case 'modificar':
	?>
    	<div style="width:210px;">
			<a href="javascript:;" style="margin-bottom:10px; width:225px;" id="botonGuardar" onclick="enviar('guardarM');" class="button extra-wide colorin">
            	<?=$lang->botonera->guardar?>
            </a><br>
		</div>
    <?php 
	break;
	case 'modificarAsignacion':
	?>
    	<div style="width:210px;">
			<a href="javascript:;" style="margin-bottom:10px; width:225px;" id="botonGuardar" onclick="enviar('guardarAsignacion');" class="button extra-wide colorin">
            	<?=$lang->botonera->guardar?>
            </a><br>
		</div>
    <?php 
	break;
	case 'altaAsignacion':
	?>
    	<div style="width:210px;">
			<a href="javascript:;" style="margin-bottom:10px; width:225px;" id="botonGuardar" onclick="enviar('guardarAltaAsignacion');" class="button extra-wide colorin">
            	<?=$lang->botonera->guardar?>
            </a><br>
		</div>
    <?php 
	break;
}
 	
##-- BLOQUE Ayuda --##
require_once('ayuda.php'); 
if(!empty($resp_ayuda)){
	echo '<div id="ayudante">'.$resp_ayuda.'</div>';
	echo '<br/><br/>';
}
##-- --##
/*
if(isset($mensaje) && strlen($mensaje)>5){?>
	<div style="padding:0 3px 0 3px;border-top:3px solid #FFFF4A;background-color:#FFFFAA;font-size:11px;line-height:13px; text-align:center;">
	<?php if($tipoBotonera=='AM' && !(isset($noError) && $noError)){?>
                <span style='color:#ff0000;'><b><?=idiomaHTML($lang->message->info_error)?></b></span><br/><br/><?=$mensaje;?><br/>
	<?php }
    else{?>
    	<span style='color:#000000;'><br/><?=$mensaje;?><br/></span><br/>
	<?php }?>
	</div>
<?php } */?>