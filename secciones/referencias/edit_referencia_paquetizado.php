<script language="javascript">	
	var perfilADT = false; //permite drag
	arrLang['mostrar_datos'] = '<?=$lang->system->mostrar_datos?>';
	arrLang['ocultar_datos'] = '<?=$lang->system->ocultar_datos?>';
	arrLang['dir_no_encontrada'] = '<?=$lang->system->direccion_no_encontrada?>';
</script>
<style>
	#mainBoxAM{width:auto !important; margin-bottom:0 !important;}
</style>
<?
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$posicion = strrpos($user_agent, "MSIE");
	if($posicion === false) { $ie = false;} else {$ie = true;}
?>
<input type="hidden" name="hidUsuario" value="<?=$_SESSION["idUsuario"]?>">  
<input type="hidden" name="cmbTipoReferencia" id="cmbTipoReferencia" value="1" />

<a href="javascript:deleteMarker();" style="position:absolute; z-index:1; margin: 70px 0 0 8px;"><img src="imagenes/map_delete.png" border="0" /></a>
<div id="infoPtos" class="claseInfoPunto">
    <div id="botonesABM">
    	<a id="botonVolver" href="<?=($popup)?'javascript:;':('boot.php?c='.$seccion)?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
	</div>
   	<div id="mainBoxAM"></div> 
    <fieldset>
        <span class="float_l">
        	<?php if($ie){ ?>        				
				<input type="text" name="txtDireccion" id="txtDireccion_2" value="<?=encode($arrEntidades[0]['re_ubicacion'])?>" size="30" style="width:400px; margin-top: 5px;" class="buscar-ref" onkeypress="javascript:setEnter(event)">
				<a href="javascript:centrarDireccion();" class="button colorin" style="margin-top: 5px; padding: 4px 6px;"><?=$lang->system->centrar?></a>			
				<br><span style="color:gray;font-style:italic;"><?=$lang->system->ejemplo_direccion?></span>
			<?php }else{ ?>		
				<input type="text" name="txtDireccion" id="txtDireccion_2" value="<?=$arrEntidades[0]['re_ubicacion']?>" size="30" style="width:400px; margin-top: 5px;" placeholder = "<?=$lang->system->ejemplo_direccion?>" class="buscar-ref" onkeypress="javascript:setEnter(event)">
				<a href="javascript:centrarDireccion();" class="button colorin" style="margin-top: 5px; padding: 4px 6px;"><?=$lang->system->centrar?></a>		
			<?php } ?>	
        </span>
    </fieldset>
    
    <div id="DatosDeRefencia" class="clear" style="display:<?=($operacion=='alta')?'none':'block'?>">
    <fieldset>
        <label for="txtNombre"><?=$lang->system->nombre_referencia?></label>
		<input type="text" name="txtNombre" class="" id="txtNombre" value="<?=isset($_POST['txtNombre'])?$_POST['txtNombre']:$arrEntidades[0]['re_nombre']?>"  style="width:200px;"  size=50>
        <span> * </span>
	</fieldset>
    
    <fieldset>	
        <label for="cmbGrupo"><?=$lang->system->categoria?></label>
		<?php $arrEntidades[0]['re_rg_id'] = !isset($arrEntidades[0]['re_rg_id'])?0:$arrEntidades[0]['re_rg_id'];?>
		<select name="cmbGrupo" id="cmbGrupo" style="width:204px;">
			<option value="0"><?=$lang->system->seleccione?></option>
			<?php for($i = 0;$i < count($arrGrupos) && $arrGrupos;$i++) { ?>
			<option value="<?=$arrGrupos[$i]['rg_id']?>"  <?=($arrEntidades[0]['re_rg_id']==$arrGrupos[$i]['rg_id'] )?"selected":"";?>><?=encode($arrGrupos[$i]['rg_nombre'])?></option>
			<?php } ?>
		</select><span> * </span>
    </fieldset>    
   
    <fieldset>	
        <label for="txtBoca"><?=$lang->system->num_boca?></label>
        <input type="text" name="txtBoca" id="txtBoca" value="<?=isset($_POST['txtBoca'])?$_POST['txtBoca']:$arrEntidades[0]['re_numboca']?>"  style="width:200px;"  size=50>
    	<span>&nbsp;&nbsp;</span>
    </fieldset>
    
    <fieldset>
    	<label><?=$lang->system->provincia?></label>
        <select name="cmbProvincia" id="cmbProvincia"  style="width:204px;" onchange="javascript:getLocalidad('cmbLocalidad', this.value, 0);"> 
        	<option value=""><?=$lang->system->seleccione?></option> 
            <?php foreach($arrProvincias as $item){?>
            	<option value="<?=$item['pr_id']?>"  <?=($arrEntidades[0]['re_provincia'] == $item['pr_id'])?  "selected":"";?>><?=encode($item['pr_nombre'])?></option>
            <?php } ?>
		</select>
        <span>&nbsp;&nbsp;</span>
	</fieldset> 
       
    <fieldset>
        <label><?=$lang->system->localidad?></label>
        <select name="cmbLocalidad" id="cmbLocalidad"  style="width:204px;"> 
        	<option value=""><?=$lang->system->seleccione?></option> 
		</select>
        <span>&nbsp;&nbsp;</span>
    </fieldset>
    
    <fieldset>
        <label for="txtEmail"><?=$lang->system->email?></label>
        <input type="text" name="txtEmail" id="txtEmail" value="<?=isset($_POST['txtEmail'])?$_POST['txtEmail']:$arrEntidades[0]['re_email']?>"  style="width:200px;" size=100>
        <span>&nbsp;&nbsp;</span>
	</fieldset>   

	<fieldset>	
        <label for="txtStock">Stock</label>
        <input type="text" name="txtStock" id="txtStock" value="<?=$arrEntidades[0]['re_stock_actual']?>"  style="width:120px;" disabled=disabled>
    	<span>&nbsp;&nbsp;</span>
    </fieldset>  
	
	<fieldset>	
        <label for="txtRecolecta">Recoleta pallets en otra ubicación</label>
		<div id="Up-recolecta" <?=!empty($arrEntidades[0]['re_recoleccion_re_id'])?'style="display:none"':''?>>
			<input type="text" name="txtRecolecta" id="txtRecolecta" class="buscar-ref" value=""  style="width:240px;">
		</div>	
    	<div id="Down-recolecta" <?=empty($arrEntidades[0]['re_recoleccion_re_id'])?'style="display:none"':''?>>
			<p class="float_l"><?=$txtRecoleccion?></p>
			<a href="javascript:;" id="RecolectaDown" class="float_l" style="margin:2px 0px 0px 10px"><img src="imagenes/delete_circle.png" /></a>
			<input type="hidden" name="idrecolecta" id="idrecolecta" value="<?=$arrEntidades[0]['re_recoleccion_re_id']?>" />
		</div>
		<span class ="clear">&nbsp;&nbsp;</span>
    </fieldset>  
    
	<!-- -->
	<fieldset style="display:none">
    	<select name="cmbRadioIngreso" id="cmbRadioIngreso" >
		    <option value="1000" selected="selected"><?=$lang->system->mediana?></option>
        </select>
        
	</fieldset>  
	<!-- -->


    <fieldset>
    	<a id="botonGuardar" href="javascript:;" onclick="javascript: enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px; margin-top:18px;"><?=$lang->botonera->guardar?></a>
    </fieldset>
    </div>
    <div class="clear"></div>
    <fieldset class="clear">
    	<a href="javascript:;" onclick="javascript:expandwidget();" class="link-grey"><span class="widget-expand-button-icon widget-<?=($operacion=='alta')?'down':'up'?> float_l"></span>&nbsp;&nbsp;<?=($operacion=='alta')?$lang->system->mostrar_datos:$lang->system->ocultar_datos?></a>
    </fieldset>
    
    
    
</div>
<div id="mapa25" style="display:block; width:100%;position:relative; margin-top:5px;"></div>
  
<script language="javascript">
	var latCentro = '<?=$_SESSION['lat']?>';
	var lngCentro = '<?=$_SESSION['lng']?>';

	function resizeRef(){
		if($('#HidPopUp').val() == 'popup'){
			$('#mapa25').css('height',parseInt($(window).height()) - 23);	
		}
		else{
			$('#footer_space').hide();
			$('#mapa25').css('height',parseInt($(window).height()) - (parseInt($("#navbar").height()) + 68));
		}
		
		$('#infoPtos').css('width',parseInt($(window).width()) - 100);
	}
	
	function deleteMarker(){
		cambioReferencia($("#cmbTipoReferencia").val());
	}
	
	$(document).ready(function(){
		resizeRef();
		<?php if($arrEntidades[0]['re_provincia']){?>
			getLocalidad('cmbLocalidad', <?=(int)$arrEntidades[0]['re_provincia']?>, <?=(int)$arrEntidades[0]['re_localidad']?>);
		<?php }?>
		
		$(window).resize(function() {
        	resizeRef();
		});
		
		//-- inicio. Autocomplete --//
		var ajaxAutocomplete;
		$( "#txtRecolecta" ).autocomplete({
			source: function( request, response ) {
				if(typeof(ajaxAutocomplete) != 'undefined'){
					ajaxAutocomplete.abort();
					$(this).removeClass('ui-autocomplete-loading');
				}
				ajaxAutocomplete = $.ajax({
					type: "POST",
					url: "ajax.php",
					dataType: "json",
					data:({
						accion:'get-buscador-referencias-palled',
						buscar:request.term
					}),
					success: function(data){
						response( $.map( data.resultados, function(item) {
							return {
								label: item.valor,
								value: item.valor,
								id: item.id
							}
						}));
						
						if (!data.resultados.length) {
							buscar_response = false;
						}
						else {
							buscar_response = true;
						}   
						$( "#txtRecolecta" ).removeClass('ui-autocomplete-loading');    
					}
				});
				
			},
			minLength: 2,
			select: function( event, ui ) {
				//$(this).end().val(ui.item.label);
				//alert(ui.item.id);
				$('#idrecolecta').val(ui.item.id);
				$('#Down-recolecta p').html(ui.item.value);
				$('#Up-recolecta').hide();
				$('#Down-recolecta').show();
			},
			open: function() {
				$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
			},
			close: function() {
				$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			}
		});

		$('#RecolectaDown').bind("click", function(ev){
			$('#idrecolecta').val('');
			$('#Down-recolecta p').html('');
			$('#Down-recolecta').hide();
			$('#Up-recolecta input').val('');
			$('#Up-recolecta').show();
    	});
		//-- fin. Autocomplete --//
	});
</script> 