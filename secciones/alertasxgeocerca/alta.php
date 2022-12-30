<div id="botoneraABM">
	<div id="botonesABM">
		<span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /><?=$lang->botonera->volver?> </span>
	</div>
	<div class="clear"></div>
</div>
	
<div id="mainBoxAM" >
    <?php if (isset($mensaje)) echo $mensaje; ?>
	<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=@$mensaje;?>" />
	<input name="hidId" id="hidId" type="hidden" value="<?=@$id;?>" />
	<input name="ag" type="hidden" value="<?=@$_GET['ag']?>"/>
	<!--
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	--><script>
		
		
	
	//============================================================
	// Add ECMA262-5 Array methods if not supported natively
	//if(browser == IE Style Browser) {
		if (!Array.prototype.indexOf) {
			Array.prototype.indexOf = function(obj, start) {
				 for (var i = (start || 0), j = this.length; i < j; i++) {
					 if (this[i] === obj) { return i; }
				 }
				 return -1;
			}	
		}
	//}
	//============================================================
	function strip_tags(input, allowed) {
		allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
		var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
		commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
		return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
		return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
		});
	}	
	//============================================================
	var plantilla = null;
    var inicial = 0;
    var actualStep = 1;
    
	function names(id, que)
	{
		//console.warn("function names('" + id + "', '" + que + "');");
		var select = document.getElementById(id);
		var q = select.options.length;
		var str = "";
		var mensaje = "[<?=$lang->system->alertas_txt28?>]";
		
		var txtOtrosEmail = strip_tags($("#txtOtrosEmail").val());
		
		if (q == 0) {
			if (que == 'usuarios') {				
				if (txtOtrosEmail != "") {
					return '<a class="link" href="javascript:;" id="click-'+que+'" onclick="wizardOpen(\''+ que+'\')">'+ txtOtrosEmail +'</a>';
				} else {
					return '<a class="link" href="javascript:;" id="click-'+que+'" onclick="wizardOpen(\''+ que+'\')"><?=$lang->system->nadie?></a>';
				}
			} else {
				return '<a class="link" href="javascript:;" id="click-'+que+'" onclick="wizardOpen(\''+ que+'\')">'+mensaje+'</a>';
			}
		}
		
		for (var pos=0; pos < q; pos++){
			text = select.options[pos].text;
			text = '<a class="link" href="javascript:;" id="click-'+que+'" onclick="wizardOpen(\''+ que+'\')">'+text+'</a>';
			if (pos == 0) {
				str = text;
			} else {
				if (que == 'usuarios') {
					str = str + " <?=$lang->system->y?> " + text;
				} else {
					str = str + " <?=$lang->system->o?> " + text;
				}
			}
		}
		
		if (que == 'usuarios' && txtOtrosEmail != "") {
			str = str + " <?=$lang->system->y?> " + '<a class="link" href="javascript:;" id="click-'+que+'" onclick="wizardOpen(\''+ que+'\')">' + txtOtrosEmail + '</a>';
		}

		
		return str;
	}
	
	function wizardParse() {
		$("#wizard-moviles-"+plantilla).html(names('lstMovilesElegidos', 'moviles'));
		$("#wizard-eventos-"+plantilla).html(names('lstAlertasElegidas', 'eventos'));
		
		if (plantilla == 'c')
		{
			var lugar = "";
			if ($("#radDentroFuera1:checked").val() == 1) {
				lugar = '<?=$lang->system->dentro?>';
			} else {
				lugar = '<?=$lang->system->afuera?>';
			}
			var dentrofuera = '<a class="link" href="javascript:;" id="click-dentrofuera" onclick="wizardOpen(\'dentrofuera\')">'+lugar+'</a>';
			$("#wizard-dentrofuera-"+plantilla).html(dentrofuera);
		}
		
		$("#wizard-geocercas-"+plantilla).html(names('lstGeocercasElegidas', 'geocercas'));
		$("#wizard-usuarios-"+plantilla).html(names('lstUsuariosElegidos', 'usuarios'));
		
		if (getQ('lstUsuariosElegidos') == 0 && strip_tags($("#txtOtrosEmail").val()) == "") {
			$("#chkNoNotificar").attr("checked", "checked");
		} else {
			$("#chkNoNotificar").removeAttr("checked");
		}
	}
	
	function wizardOpen(que) { 
		$("#"+que).dialog({
			minWidth: 800
			, beforeClose: wizardParse 							
			, buttons:{
				'<?=$lang->botonera->guardar?>': function(){
					$(this).dialog("close");
				}
			}
			,open: function(event, ui){$(".ui-dialog-titlebar-close", ui.dialog).hide();}
			,resizable: false
						  });
		viewCountElement(que);
	}

	function addOption(selectid, value, text){
		var elOptNew = document.createElement('option');
	  	elOptNew.text = text;
	  	elOptNew.value = value;
	  	var elSel = document.getElementById(selectid);
	  
	  	if(valueOptionExists(selectid, value) == false){
		  	try {
				elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
		  	} 
		  	catch(ex) {
				elSel.add(elOptNew); // IE only
		  	}
	  	}
	}

	function valueOptionExists(selectid, id){
		var elSel = document.getElementById(selectid);
		var i;
		for (i = elSel.length - 1; i>=0; i--) {
			if (elSel.options[i].value == id) {
				return true;
			}
		}
		return false;
	}

	function removeAllExcept(selectid, ids){
		var id = ids.split(",");
		var origen = document.getElementById(selectid);
		var destino = document.getElementById(selectid + "Removido");
		
		var i, j;
		var coincide;
		for (i = origen.length - 1; i >= 0; i--){
			coincide = false;
			for (var j = 0; j < id.length; j++){
				if (id[j] == origen.options[i].value) {
					coincide = true;
				}
			}
			
			if (coincide == true) {}
			else {
				var val = origen.options[i].value;
				addOption(selectid+"Removido", val, origen.options[i].text);
				removeOption(selectid, val);
			}
		}
	}
	
	function restoreOptions(origen, destino){
		var removido = document.getElementById(origen);
		var original = document.getElementById(destino);
						
		if (removido != null) {
			var i;
			for (i = removido.length - 1; i >= 0; i--){				
				var val = removido.options[i].value;
				addOption(destino, val, removido.options[i].text);
				removeOption(origen, val);					
			}
		}
	}

	function removeOption(selectid, id){
		var elSel = document.getElementById(selectid);
		var i;
		for (i = elSel.length - 1; i>=0; i--) {
			if (elSel.options[i].value == id) {
				elSel.remove(i);
			}
		}
	}

	function setPlantilla(cual, onchange) {
		if (inicial == 0) {
			inicial = cual;
		}
		plantilla = cual;
        $("#hidTipoElegido").val(cual);
        $("#plantilla-"+cual).attr("checked", "checked");
		$("#step-1-button-next").removeAttr("disabled");		
	}

	function showWizard() { 
		restoreOptions('lstAlertasRemovido', 'lstAlertas');
		restoreOptions('lstMovilesElegidos', 'lstMoviles');
		restoreOptions('lstGeocercasElegidas', 'lstGeocercas');
		restoreOptions('lstAlertasElegidas', 'lstAlertas');

		if($('#tipoAlerta').val() == plantilla){
			//## Cargar al combo DERECHO y Quitar del IZQ los items guardados en el alta (Solo UPDATE)##//
			/*var arr_moviles = ($('#h_moviles').val()).split(',');
			for(i=0; i < arr_moviles.length; i++){
				if(parseInt(arr_moviles[i]) > 0){ 
					var elSel = document.getElementById('lstMoviles');
					for(e=0; e<$('#lstMoviles  option').length; e++){
						if (elSel.options[e].value == arr_moviles[i]){
							addOption('lstMovilesElegidos', elSel.options[e].value, elSel.options[e].text);
							removeOption('lstMoviles', elSel.options[e].value);
						}
					}
				}	
			}
			
			var arr_zonas = ($('#h_zonas').val()).split(',');
			for(i=0; i < arr_zonas.length; i++){
				if(parseInt(arr_zonas[i]) > 0){ 
					var elSel = document.getElementById('lstGeocercas');
					for(e=0; e<$('#lstGeocercas  option').length; e++){
						if (elSel.options[e].value == arr_zonas[i]){
							addOption('lstGeocercasElegidas', elSel.options[e].value, elSel.options[e].text);
							removeOption('lstGeocercas', elSel.options[e].value);
						}
					}
				}	
			}
			
			var arr_eventos = ($('#h_eventos').val()).split(',');
			for(i=0; i < arr_eventos.length; i++){
				if(parseInt(arr_eventos[i]) > 0){ 
					var elSel = document.getElementById('lstAlertas');
					for(e=0; e<$('#lstAlertas  option').length; e++){
						if (elSel.options[e].value == arr_eventos[i]){
							addOption('lstAlertasElegidas', elSel.options[e].value, elSel.options[e].text);
							removeOption('lstAlertas', elSel.options[e].value);
						}
					}
				}	
			}
			/**/
			var arr_moviles = ($('#h_moviles').val()).split(',');
			var arr_remove = new Array();
			var i = 0;			
			if(arr_moviles.length > 0){
				var elSel = document.getElementById('lstMoviles');				
				var tamanio = $('#lstMoviles  option').length;
				for(e=0; e < tamanio; e++){	
					if(arr_moviles.indexOf(elSel.options[e].value) >= 0){						
						addOption('lstMovilesElegidos', elSel.options[e].value, elSel.options[e].text);						
						arr_remove[i] = elSel.options[e].value;						
						i++;					
					}	
				}				
				for(e=0; e<arr_remove.length; e++){					
					removeOption('lstMoviles', arr_remove[e]);
				}				
			}		
			
		//------------	
			var arr_zonas = ($('#h_zonas').val()).split(',');
			var arr_remove = new Array();
			var i = 0;
			if(arr_zonas.length > 0){			
				var elSel = document.getElementById('lstGeocercas');
				var tamanio = $('#lstGeocercas  option').length;						
				for(e=0; e < tamanio; e++){
					if(arr_zonas.indexOf(elSel.options[e].value) >= 0){
						addOption('lstGeocercasElegidas', elSel.options[e].value, elSel.options[e].text);
						arr_remove[i] = elSel.options[e].value;
						i++;
					}
				}
				for(e=0; e<arr_remove.length; e++){
					removeOption('lstGeocercas', arr_remove[e]);
				}
			}
		//------------	
			
			var arr_eventos = ($('#h_eventos').val()).split(',');
			var arr_remove = new Array();
			var i = 0;
			if(arr_eventos.length > 0){
				var elSel = document.getElementById('lstAlertas');
				var tamanio = $('#lstAlertas  option').length;
				for(e=0; e < tamanio; e++){
					if(arr_eventos.indexOf(elSel.options[e].value) >= 0){
						addOption('lstAlertasElegidas', elSel.options[e].value, elSel.options[e].text);
						arr_remove[i] = elSel.options[e].value;
						i++;
					}
				}
				for(e=0; e<arr_remove.length; e++){
					removeOption('lstAlertas', arr_remove[e]);
				}
			}
			
			
			
		}
//--------------------------------------------		
		cual = plantilla;
		if (cual == 'a') {
			removeAllExcept('lstAlertas', '14,15');

			/*if (inicial != 'a') {
			// Egreso e ingreso, fijos.
				//restoreOptions('lstAlertasRemovido', 'lstAlertas');
				//restoreOptions('lstAlertasElegidas', 'lstAlertas');
				removeOption('lstAlertas', 14);
				addOption('lstAlertasElegidas', 14, 'Egreso de');
				removeOption('lstAlertas', 15);
				addOption('lstAlertasElegidas', 15, 'Ingreso a');
			}*/
		} /*else if (cual == 'b' && inicial != 'b') {
			restoreOptions('lstAlertasRemovido', 'lstAlertas');
			restoreOptions('lstAlertasElegidas', 'lstAlertas');
		}*/

		$("#hid_tipoAlerta").val(cual);
		$("#step-1").css('display', 'none');
		$("#step-2").css('display', 'block');
		$("#step-3").css('display', 'none');
		$("#wizard-a").css('display', 'none');
		$("#wizard-b").css('display', 'none');
		$("#wizard-c").css('display', 'none');
		$("#wizard-d").css('display', 'none');
		$("#wizard-"+cual).css('display', 'block');

		wizardParse();		
	}
	
	function getQ(que) {
		return document.getElementById(que).length;
	}
	
	function Step(step) { 
		var error = false;
		if (step > actualStep) {
			var msg = '<?=$lang->system->seleccionar_items?>'+': ';
			var coma = "";
			if (step == 3) {
				/* SE LIBERA EL ALTA DE ALERTAS SIN VEHICULOS
				if (getQ('lstMovilesElegidos') == 0) {
					msg = msg.concat('M\u00f3viles');
					coma = ", ";
					error = true;
				}*/
				if (getQ('lstAlertasElegidas') == 0) {
					msg = msg.concat(coma.concat('<?=$lang->system->eventos?>'));
					coma = ", ";
					error = true;
				}
				if (getQ('lstGeocercasElegidas') == 0 && plantilla != 'b') {
					msg = msg.concat(coma.concat('<?=$lang->system->geocercas?>'));
					error = true;
				}
				
				if(error == true){
					alert(msg);	
				}

				$("#hid_radDentroFuera").val( $("input[name=radDentroFuera]:checked").val() );
				
			}
		}
		else if (step <= actualStep) {
			var valores = false;
			if (getQ('lstMovilesElegidos') > 0){
				valores = true;}
			if (getQ('lstAlertasElegidas') > 0){
				valores = true;}
			if (getQ('lstGeocercasElegidas') > 0 && plantilla != 'b'){
				valores = true;}
				
			if(valores == true){
				var resp = confirm('<?=$lang->system->paso_anterior?>');
				if(resp == false){
					error = true;
				}
			}
		}
		
		
		if (error == false)
		{			
			$("#step-1").css('display', 'none');
			$("#step-2").css('display', 'none');
			$("#step-3").css('display', 'none');
			$("#step-" + step).css('display', 'block');			
			$("#paso-1").css('color', 'black');
			$("#paso-2").css('color', 'black');
			$("#paso-3").css('color', 'black');
			$("#paso-" + step).css('color', '#FE6500');
		}
		
	}
	
	function actualizarListadoReferencias(tipo){
				
		$.ajax({
			type: "GET",
			url: "ajaxLlenarListadoReferenciasAlertas.php",
			data: "tipoAlerta=" + tipo,
			dataType: "text",
			contentType: "application/text; charset=utf-8",
			success: function (msg){
				$('#lstGeocercas').html(msg);
				$('#cmbFiltroGeocercas').val(0);
				if(tipo == 'c'){ $('#spmNota').html(' (<?=$lang->system->solo_circulares?>)'); } else{ $('#spmNota').html(''); }
				
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(textStatus);
            }
		});
				
	}	
	//Si tiene el permiso, salta el paso uno, seleccionando el tipo A
	<? if(tienePerfil(array(9, 10, 11, 12, 16))){?>
		$(document).ready(function(){
			setPlantilla('a', 1);
			Step(2); showWizard();		
		});
	<? }?>	
	
	</script>

	<? if (isset($arrEntidades[0])){?><h1><?=$lang->system->editar_alerta.': '.$arrEntidades[0]['al_nombre']?></h1><? }
	else{?><h1><?=$lang->system->crear_alerta?></h1><? }?>
	
	<div id="main" class="sinColIzq">
		<div id="step-list">
			<ul>
				<? if(!tienePerfil(16)){?><li><a class="paso" id="paso-1" style="color:#FE6500;" href="javascript:;"><?=$lang->system->paso?> 1: <?=$lang->system->alertas_step1?></a></li><? }?>
				<li><a class="paso" id="paso-2"><? if(!tienePerfil(16)){?><?=$lang->system->paso?> 2: <? }?><?=$lang->system->alertas_step2?></a></li>
				<li><a class="paso" id="paso-3"><? if(!tienePerfil(16)){?><?=$lang->system->paso?> 3: <? }?><?=$lang->system->alertas_step3?></a></li>
			</ul>
		</div>
		
		<div id="step-1" <?=tienePerfil(16)?'style="display:none;"':''?> >
			<div class="step-buttons">
				<input type="hidden" id="hidTipoElegido" name="hidTipoElegido" value="0" />
				<button id="step-1-button-next" class="button colorin" type="button" onclick="Step(2);showWizard();actualizarListadoReferencias($('#hidTipoElegido').val());" disabled="disabled" value=""><?=$lang->botonera->siguiente?> >></button>
				
			</div>
			<fieldset>
				<legend><?=$lang->system->alertas_step1?></legend>
				<ul id="step-1-items">
					<li>
						<input onchange="setPlantilla('a', 1);" id="plantilla-a" type="radio" name="plantilla" value="a" />
						<label for="plantilla-a"><?=$lang->system->alertas_type1?></label>
					</li><li>
						<input onchange="setPlantilla('b', 1);" id="plantilla-b" type="radio" name="plantilla" value="b" />
						<label for="plantilla-b"><?=$lang->system->alertas_type2?></label>
					</li><li>
						<input onchange="setPlantilla('c', 1);" id="plantilla-c" type="radio" name="plantilla" value="c" />
						<label for="plantilla-c"><?=$lang->system->alertas_type3?></label>
					</li>
					<!--<li>
						<input onchange="setPlantilla('d');" id="plantilla-d" type="radio" name="plantilla" value="d" />
						<label for="plantilla-d">Generar un alerta frente a la detenci&oacute;n / movimiento de un m&oacute;vil dentro o fuera de una zona.</label>
					</li>-->
				</ul>
			</fieldset>
		</div>

		<div id="step-2" <?=!tienePerfil(16)?'style="display:none;"':''?>>
			<div class="step-buttons">
				<? if(!tienePerfil(16)){?><button type="button" class="button colorin" onclick="Step(1)" value="">&#60;&#60; <?=$lang->botonera->anterior?></button><? }?>
				<button type="button" class="button colorin" onclick="Step(3)" value=""><?=$lang->botonera->siguiente?> &#62;&#62;</button>
			</div>

			<fieldset>
				<legend><?=$lang->system->alertas_step2?></legend>
				<div>
					<div id="wizard-a" style="display:none;">
						<dl>
							<dt><?=$lang->system->alertas_txt38?></dt>
							<dd><span id="wizard-eventos-a"><a class="link" href="javascript:;" id="click-eventos" onclick="wizardOpen('eventos')">[<?=$lang->system->seleccione?> <?=$lang->system->eventos?>]</a></span></dd>
							<dt><?=$lang->system->alertas_txt39?></dt>
							<dd><span id="wizard-moviles-a"><a class="link" href="javascript:;" id="click-moviles" onclick="wizardOpen('moviles')">[<?=$lang->system->seleccione?> <?=$lang->system->moviles?>]</a></span></dd>
							<dt><?=$lang->system->alertas_txt40?></dt> 
							<dd><span id="wizard-geocercas-a"><a class="link" href="javascript:;" id="click-geocercas" onclick="wizardOpen('geocercas')">[<?=$lang->system->seleccione?> <?=$lang->system->zonas?>]</a></span></dd>
							<dt><?=$lang->system->alertas_txt41?></dt>
							<dd><span id="wizard-usuarios-a"><a class="link" href="javascript:;" id="click-usuarios" onclick="wizardOpen('usuarios')">[<?=$lang->system->seleccione?> <?=$lang->system->destinatarios?>]</a></span></dd>
						</dl>
					</div>
					
					<div id="wizard-b" style="display:none;">
						<dl>
						<dt><?=$lang->system->alertas_txt42?></dt>
						<dd><span id="wizard-eventos-b"><a class="link" href="javascript:;" id="click-eventos" onclick="wizardOpen('eventos')">[<?=$lang->system->seleccione?> <?=$lang->system->eventos?>]</a></span></dd>
						<dt><?=$lang->system->alertas_txt39?></dt>
						<dd><span id="wizard-moviles-b"><a class="link" href="javascript:;" id="click-moviles" onclick="wizardOpen('moviles')">[<?=$lang->system->seleccione?> <?=$lang->system->moviles?>]</a></span></dd>
						<!--<dt>a</dt>
						<dd><span id="wizard-geocercas-b"><a class="link" href="javascript:;" id="click-geocercas" onclick="wizardOpen('geocercas')"></a></span></dd>
	     -->
						<dt><?=$lang->system->alertas_txt43?></dt>
						<dd><span id="wizard-usuarios-b"><a class="link" href="javascript:;" id="click-usuarios" onclick="wizardOpen('usuarios')"></a></span></dd>
						</dl>
					</div>
					
					<div id="wizard-c" style="display:none;">
						<dl>
							<dt><?=$lang->system->alertas_txt42?></dt>
							<dd><span id="wizard-eventos-c"><a class="link" href="javascript:;" id="click-eventos" onclick="wizardOpen('eventos')">[<?=$lang->system->seleccione?> <?=$lang->system->eventos?>]</a></span></dd>
							<dt><?=$lang->system->alertas_txt39?></dt>
							<dd><span id="wizard-moviles-c"><a class="link" href="javascript:;" id="click-moviles" onclick="wizardOpen('moviles')">[<?=$lang->system->seleccione?> <?=$lang->system->moviles?>]</a></span></dd>
							<dt><?=$lang->system->alertas_txt44?></dt>
							<dd><span id="wizard-dentrofuera-c"><a class="link" href="javascript:;" id="click-dentrofuera" onclick="wizardOpen('dentrofuera')">[<?=$lang->system->dentro?> / <?=$lang->system->afuera?>]</a></span></dd>
							<dt><?=$lang->system->alertas_txt45?></dt>
							<dd><span id="wizard-geocercas-c"><a class="link" href="javascript:;" id="click-geocercas" onclick="wizardOpen('geocercas')">[<?=$lang->system->seleccione?> <?=$lang->system->zonas?>]</a></span></dd>
							<dt><?=$lang->system->alertas_txt43?></dt>
							<dd><span id="wizard-usuarios-c"><a class="link" href="javascript:;" id="click-usuarios" onclick="wizardOpen('usuarios')">[<?=$lang->system->seleccione?> <?=$lang->system->destinatarios?>]</a></span></dd>
						</dl>
					</div>


					<div id="wizard-d" style="display:none;">
						<dl>
						<dt><?=$lang->system->alertas_txt9?></dt>
						<dd><?=$lang->system->alertas_txt10?></dd>
						<dt><?=$lang->system->alertas_txt40?></dt>
						<dd>70 km/h </dd>
						<dt><?=$lang->system->alertas_txt39?></dt>
						<dd><span id="wizard-moviles-d"><a class="link" href="javascript:;" id="click-moviles" onclick="wizardOpen('moviles')">[<?=$lang->system->seleccione?> <?=$lang->system->moviles?>]</a></span></dd>
						<dt><?=$lang->system->alertas_txt11?></dt>
						<dd><span id="wizard-geocercas-d"><a class="link" href="javascript:;" id="click-geocercas" onclick="wizardOpen('geocercas')">[<?=$lang->system->seleccione?> <?=$lang->system->zonas?>]</a></span></dd>
						<dt><?=$lang->system->alertas_txt43?></dt>
						<dd><span id="wizard-usuarios-d"><a class="link" href="javascript:;" id="click-usuarios" onclick="wizardOpen('usuarios')">[<?=$lang->system->seleccione?> <?=$lang->system->destinatarios?>]</a></span></dd>
						</dl>
					</div>
				</div>
			</fieldset>
		</div>
			
		<div id="step-3" style="display:none;">
			<div class="step-buttons">
				<button type="button" class="button colorin" onclick="Step(2)" value="">&#60;&#60; <?=$lang->botonera->siguiente?></button>
				<button type="button" class="btn_fin button colorin"><?=$lang->botonera->finalizar?></button>
			</div>
			<fieldset>
				<legend><?=$lang->system->alertas_step3?></legend>
				<table class="widefat">
				<tr>
					<td class="td_label"><label for="txtNombre"><?=$lang->system->nombre?></label></td>
					<td class="td_campo"><input type="text" id="txtNombre" name="txtNombre" placeholder="<?=$lang->system->alertas_txt12?>" value="<?=@$arrEntidades[0]['al_nombre'];?>" maxlength="50"/> *</td>
				</tr>
				<tr><td colspan=2>&nbsp;</td></tr>
				<tr>
					<td class="td_label"><label for="chkActiva"><?=$lang->system->estado?></label></td>					
					<td class="td_campo">						
						<label><?=$lang->system->activada?> <input type="radio" id="chkActiva1" name="chkActiva" class="radHid" <? if (@$arrEntidades[0]['al_activa'] == 1 || !isset($arrEntidades[0]['al_activa'])){echo 'checked="checked"';}?> value="1"/></label>
						<label><?=$lang->system->desactivada?> <input type="radio" id="chkActiva2" name="chkActiva" class="radHid" <? if (@$arrEntidades[0]['al_activa'] == 0 &&  isset($arrEntidades[0]['al_activa'])){echo 'checked="checked"';}?> value="0"/></label>
					</td>
				</tr>
				<tr><td colspan=2>&nbsp;</td></tr>
				<tr>
					<td class="td_label"><label for="chkDuracion"><?=$lang->system->alertas_txt13?></label></td>
					<td>
						<label>
							<?=$lang->system->alertas_txt14?>
							<?php if ( $operacion == 'alta' ): ?>
								<input type="radio" id="radDurationAllDays" name="radDuration" class="radHid" checked="checked" value="1"/>
							<?php elseif ( $operacion == 'modificar' ): ?>
								<input type="radio" id="radDurationAllDays" name="radDuration" class="radHid" <? if (@$arrEntidades[0]['al_duracion'] == 1){echo 'checked="checked"';}?> value="1"/>
							<?php endif ?>
						</label>
						<label>
							<?=$lang->system->alertas_txt15?>
							<?php if ( $operacion == 'alta' ): ?>
								<input type="radio" id="radDurationCustom" name="radDuration" class="radHid" value="2"/>
							<?php elseif ( $operacion == 'modificar' ): ?>
								<input type="radio" id="radDurationCustom" name="radDuration" class="radHid" <? if (@$arrEntidades[0]['al_duracion'] == 2){echo 'checked="checked"';}?> value="2"/>
							<?php endif ?>
						</label>
					</td>
				</tr>
				<!-- Depende de la duracion que se haya estipulado (personalizada) -->
				<tr id="trCustomDuration">
					<td class="td_label"></td>
					<td>
						<fieldset id="fldCustomDuration" style="border: none">
							<?php if ( $operacion == 'alta' ): ?>
								<div id="divCustomDuration" style="display: none;">
							<?php elseif ( $operacion == 'modificar' ): ?>
								<div id="divCustomDuration" style="display: <?= @$arrEntidades[0]['al_duracion'] == 2 ? 'block' : 'none' ?>;">
							<?php endif ?>
								<table>
									<tr id="trDurationLuVi">
										<td>
											<?php if ( $operacion == 'alta' ): ?>
												<fieldset id="fsetDurationLuVi">
											<?php elseif ( $operacion == 'modificar' ): ?>
												<fieldset id="fsetDurationLuVi" >
											<?php endif ?>
												<legend style="white-space: nowrap;">
													<label>
														<?=$lang->system->alertas_txt16?>
														<?php if ( $operacion == 'alta' ): ?>
															<input type="checkbox" id="chkLuVi" name="chkLuVi" />
														<?php elseif ( $operacion == 'modificar' ): ?>
															<?php if ( @$arrEntidades[0]['DiaDeSemanaCustom'] ): ?>
																<input type="checkbox" id="chkLuVi" name="chkLuVi" checked="true" />
															<?php else: ?>
																<input type="checkbox" id="chkLuVi" name="chkLuVi" />
															<?php endif ?>
														<?php endif ?>
													</label>
												</legend>
												<table>
													<tr>
														<td class="td_label" style="white-space: nowrap;">
															<label>
																<?=$lang->system->alertas_txt17?>
															</label>
														</td>
														<td>
															<?php if ( @$arrEntidades[0]['DiaDeSemanaCustom'] ): ?>
																<?= selectHorarios('cboDurationLuVi_desde', 'desde', 'cboDurationLuVi_desde', @$arrEntidades[0]['DiaDeSemana_hora_inicio'], false ); ?>
															<?php else: ?>
																<?= selectHorarios('cboDurationLuVi_desde', 'desde', 'cboDurationLuVi_desde', '0900', true ); ?>
															<?php endif ?>
														</td>
													</tr>
													<tr>
														<td class="td_label" style="white-space: nowrap;">
															<label>
																<?=$lang->system->alertas_txt18?>
															</label>
														</td>
														<td>
															<?php if ( @$arrEntidades[0]['DiaDeSemanaCustom'] ): ?>
																<?= selectHorarios('cboDurationLuVi_hasta', 'hasta', 'cboDurationLuVi_hasta', @$arrEntidades[0]['DiaDeSemana_hora_fin'], false  ); ?>
															<?php else: ?>
																<?= selectHorarios('cboDurationLuVi_hasta', 'hasta', 'cboDurationLuVi_hasta', '1800', true ); ?>
															<?php endif ?>
														</td>
													</tr>
												</table>
											</fieldset>
										</td>
									
									</tr>
									<tr id="trDurationSabDo">
									
										<td>
											<?php if ( $operacion == 'alta' ): ?>
												<fieldset id="fsetDurationSabDo">
											<?php elseif ( $operacion == 'modificar' ): ?>
												<fieldset id="fsetDurationSabDo">
											<?php endif ?>
											
												<legend style="white-space: nowrap;">
													<label>
														<?=$lang->system->alertas_txt19?>
														<?php if ( $operacion == 'alta' ): ?>
															<input type="checkbox" id="chkSabDo" name="chkSabDo" />
														<?php elseif ( $operacion == 'modificar' ): ?>
															<?php if ( @$arrEntidades[0]['FinDeSemanaCustom'] ): ?>
																<input type="checkbox" id="chkSabDo" name="chkSabDo" checked="true" />
															<?php else: ?>
																<input type="checkbox" id="chkSabDo" name="chkSabDo" />
															<?php endif ?>
														<?php endif ?>
													</label>
												</legend>
												<table>
													<tr>
														<td class="td_label" style="white-space: nowrap;">
															<label>
																<?=$lang->system->alertas_txt17?>
															</label>
														</td>
														<td>
															<?php if ( @$arrEntidades[0]['FinDeSemanaCustom'] ): ?>
																<?= selectHorarios('cboDurationSabDo_desde', 'desde', 'cboDurationSabDo_desde', @$arrEntidades[0]['FinDeSemana_hora_inicio'], false); ?>
															<?php else: ?>
																<?= selectHorarios('cboDurationSabDo_desde', 'desde', 'cboDurationSabDo_desde', '0900', true ); ?>
															<?php endif ?>
														</td>
													</tr>
													<tr>
														<td class="td_label" style="white-space: nowrap;">
															<label>
																<?=$lang->system->alertas_txt18?>
															</label>
														</td>
														<td>
															<?php if ( @$arrEntidades[0]['FinDeSemanaCustom'] ): ?>
																<?= selectHorarios('cboDurationSabDo_hasta', 'hasta', 'cboDurationSabDo_hasta', @$arrEntidades[0]['FinDeSemana_hora_fin'], false); ?>
															<?php else: ?>
																<?= selectHorarios('cboDurationSabDo_hasta', 'hasta', 'cboDurationSabDo_hasta', '1200', true ); ?>
															<?php endif ?>
														</td>
													</tr>
												</table>
											</fieldset>
										</td>
									</tr>
								</table>
							</div>
						</fieldset>
					</td>
				</tr>
			<? if(!tienePerfil(16)){?>	
				<tr><td colspan=2>&nbsp;</td></tr>
				<tr>
					<td class="td_label"><label><?=$lang->system->alertas_txt20?></label> *</td>
					<td class="td_campo">
						<label><?=$lang->system->si?> <input type="radio" id="radAlConfirmacion1" name="radAlConfirmacion" class="radHid" <? if (@$arrEntidades[0]['al_confirmacion'] == 1){echo 'checked="checked"';}?> value="1"/></label>
						<label><?=$lang->system->no?> <input type="radio" id="radAlConfirmacion2" name="radAlConfirmacion" class="radHid" <? if (@$arrEntidades[0]['al_confirmacion'] == 0){echo 'checked="checked"';}?> value="0"/></label>
					</td>
				</tr>
			<? }
			  else{
			?>
						<label style="visibility:hidden;"><?=$lang->system->no?> <input style="visibility:hidden;" type="radio" id="radAlConfirmacion2" name="radAlConfirmacion" class="radHid" checked="checked" value="0"/></label>					
			<? }?>  
			</table>
			</fieldset>				
		</div>
	</div>
</div>
	
	<fieldset class="hidden">
		<legend><?=$lang->system->general?></legend>
		<div class="fld_wrapper hidden" id="general">
			<div class="buttonera hidden">
				<button type="button" disabled="disabled"><?=$lang->botonera->siguiente?></button>
				<button type="button" class="btn_sig"><?=$lang->botonera->siguiente?></button>
			</div>
		</div>
	</fieldset>
	
	<fieldset  class="hidden" id="fldGeocerca">
		<legend><?=$lang->system->geocercas?></legend>
		<div class="hidden" id="geocercas" title="<?=$lang->system->geocercas?>" >
		<table class="widefat">
			<tbody>
				
				<? if(!tienePerfil(16)){?>	
				<tr>
					<td class="td_label"><label for="txtFiltroGeocercas"><?=$lang->system->filtro?></label></td>
					<td class="td_campo"><input type="text" id="txtFiltroGeocercas" class="txtFiltroTransfer" maxlength="50"/></td>		
				</tr>	
				<? }else{?>	
				<tr>
					<td class="td_campo" colspan=2><input style="visibility:hidden;" type="text" id="txtFiltroGeocercas" class="txtFiltroTransfer" maxlength="50"/></td>					
				</tr>	
				<? }?>				
				
				<tr>
					<td class="td_label"><label for="cmbFiltroGeocercas"><?=$lang->system->grupo_geocercas?></label></td>
					<td class="td_campo">
						<select name="cmbFiltroGeocercas" id="cmbFiltroGeocercas">
							<option value="0"><?=$lang->system->seleccione?></option>
<?php
						if(isset($arrGruposGeocercas) && $arrGruposGeocercas){
							foreach($arrGruposGeocercas as $arrFila){?>
							<option value="<?=$arrFila['rg_id']?>"><?=decode($arrFila['rg_nombre'])?></option>
<?php
							}
						}?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="td_transfer_box">
						<table>
						<tbody>							
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td><label for="lstGeocercas"><?=$lang->system->geocercas_disponibles?></label><span id="spmNota"></span></td>
								<td></td>
								<td><label for="lstGeocercasElegidas"><?=$lang->system->geocercas_seleccionados?></label></td>
							</tr>	
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td class="td_campo">
									<select id="lstGeocercas" multiple="multiple" size="10" class="ref_lstIzq">
<?												$tempGeocercas='';
									foreach ($arrGeocercas as $fila){
										if (isset($arrGeocercasUsadas2) && in_array($fila['re_id'], $arrGeocercasUsadas2)){
											$arrGeocercasElegidas[]=$fila;
											$tempGeocercas.=$fila['re_id'].',';
											continue;
										}
										$class="";
										if (isset($fila['re_rg_id']) && $fila['re_rg_id']){
											$class=' class="rg_'.$fila['re_rg_id'].'"';
										}
?>
										<option value="<?=$fila['re_id']?>"<?=$class;?>><?=decode($fila['re_nombre'])?></option>
<?									}?>
									</select>
								</td>
								<td class="td_pasaje">
									<button type="button" class="ref_btnDerT">&gt;&gt;</button>
									<button type="button" class="ref_btnDer">&gt;</button>
									<button type="button" class="ref_btnIzq">&lt;</button>
									<button type="button" class="ref_btnIzqT">&lt;&lt;</button>
								</td>
								<td class="td_campo">
									<select id="lstGeocercasElegidas" name="lstGeocercasElegidas[]" multiple="multiple" size="10" class="ref_lstDer">
<?												if (isset($arrGeocercasElegidas)){
									foreach ($arrGeocercasElegidas as $fila){
										$class="";
										if (isset($fila['re_gr_id'])){
											$class=' class="rg_'.$fila['re_gr_id'].'"';
										}
										?>
										<option value="<?=$fila['re_id']?>"<?=$class;?>><?=decode($fila['re_nombre'])?></option>
<?									}}?>
									</select>
                                </td>
							</tr>
                            <tr>
                            	<td></td>
                                <td></td>
                                
								<? if(!tienePerfil(16)){?>															
								<td>
                                	<span id="cantZonasElegidos" ></span>
                                    <span id="cantZonas"  style="float:right"></span>
                                </td>
								<? }?>
                            </tr>
						</tbody>
						</table>
					</td>
				</tr>
			<? if(!tienePerfil(16)){?>						
				<table class="widefat">
				<tbody>
				<tr>
					<td class="td_label" style="width:330px"><label for="chkVelMax"><?=$lang->system->alertas_txt21?></label></td>
					<td class="td_campo">
						<? if ( @$arrEntidades[0]['al_vel_max'] == 0 ): ?>
							<input type="checkbox" name="chkVelMax" id="chkVelMax" class="chkOptional" value="1" />
							<input type="text" id="txtVelMax" name="txtVelMax" style="width:80px" value="" maxlength="50" disabled="true" />
						<? else: ?>
							<input type="checkbox" name="chkVelMax" id="chkVelMax" class="chkOptional" value="1" checked="true" />
							<input type="text" id="txtVelMax" name="txtVelMax" style="width:80px" value="<?= @$arrEntidades[0]['al_vel_max']; ?>" maxlength="50"/>
						<? endif ?>
					</td>
				</tr>
				<tr>
					<td class="td_label">
						<label for="chkVelMin"><?=$lang->system->alertas_txt22?></label>
					</td>
					<td class="td_campo">
						<? if ( @$arrEntidades[0]['al_vel_min'] == 0 ): ?>
							<input type="checkbox" name="chkVelMin" id="chkVelMin" class="chkOptional" value="1" />
							<input type="text" id="txtVelMin" name="txtVelMin" style="width:80px" value="" maxlength="50" disabled="true" />
						<? else: ?>
							<input type="checkbox" name="chkVelMin" id="chkVelMin" class="chkOptional" value="1" checked="true" />
							<input type="text" id="txtVelMin" name="txtVelMin" style="width:80px" value="<?= @$arrEntidades[0]['al_vel_min']; ?>" maxlength="50"/>
						<? endif ?>
					</td>
				</tr>
				</tbody>
				</table>
			<?}?>	
			</tbody>
		</table>
		<div class="buttonera hidden">
			<button type="button" class="btn_ant"><?=$lang->botonera->siguiente?></button><button type="button" class="btn_sig"><?=$lang->botonera->siguiente?></button>
		</div>
		</div>
	</fieldset>
	<fieldset  class="hidden" <? if (@$arrEntidades[0]['al_evento'] != 1){echo 'class="hidder"';}?> id="fldAlEventos">
		<legend><?=$lang->system->eventos?></legend>
		<div class="fld_wrapper hidden" id="eventos" title="<?=$lang->system->eventos?>">
		<table class="widefat">
			<tbody>
				<? if(!tienePerfil(16)){?>
				<tr>
					<td class="td_label"><label for="txtFiltroAlertas"><?=$lang->system->filtro?></label></td>
					<td class="td_campo"><input type="text" id="txtFiltroAlertas" class="txtFiltroTransfer" maxlength="50"/></td>
				</tr>
				<? }else{?>
				<tr>					
					<td class="td_campo" colspan=2><input style="visibility:hidden;" type="text" id="txtFiltroAlertas" class="txtFiltroTransfer" maxlength="50"/></td>
				</tr>				
				<? }?>
				<tr>
					<td colspan="2" class="td_transfer_box">
							<table class="widefat">
							<tbody>								
								<tr>
									<td><label for="lstAlertas"><?=$lang->system->eventos_disponible?></label></td>
									<td></td>
									<td><label for="lstAlertasElegidas"><?=$lang->system->eventos_seleccionados?></label></td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td class="td_campo">
                                    	<select id="lstAlertasRemovido" multiple="multiple" size="10" class="hidden">
										</select>
										<select id="lstAlertas" multiple="multiple" size="10" class="ref_lstIzq">
	<?									$tempAlertas='';
										foreach ($arrEventos2 as $fila){
											if (isset($arrEventosUsados2) && in_array($fila['id'], $arrEventosUsados2)){
												$arrEventosElegidos[]=$fila;
												$tempAlertas.=$fila['id'].',';
												continue;
											}?>
											<option value="<?=$fila['id']?>"><?=decode($fila['dato'])?></option>
										<? } ?>
										</select>
									</td>
									<td class="td_pasaje">
										<button type="button" class="ref_btnDerT">&gt;&gt;</button>
										<button type="button" class="ref_btnDer">&gt;</button>
										<button type="button" class="ref_btnIzq">&lt;</button>
										<button type="button" class="ref_btnIzqT">&lt;&lt;</button>
									</td>
									<td class="td_campo">
										<select id="lstAlertasElegidas" name="lstAlertasElegidas[]" multiple="multiple" size="10" class="ref_lstDer">
										<?
                                        if (isset($arrEventosElegidos)){
											foreach ($arrEventosElegidos as $fila){ ?>
												<option value="<?=$fila['id']?>"><?=decode($fila['dato'])?></option>
											<? }
										}?>
										</select>
									</td>
								</tr>
								<tr>
                            	<td></td>
                                <td></td>
                                
								<? if(!tienePerfil(16)){?>														
								<td>
                                	<span id="cantEventosElegidos"></span>
                                    <span id="cantEventos" style="float:right"></span>
                                </td>
								<? }?>
                            </tr>
							</tbody>
							</table>
					
					</td>
				</tr>
			</tbody>
		</table>
		<div class="buttonera hidden">
			<button type="button" class="btn_ant"><?=$lang->botonera->siguiente?></button><button type="button" class="btn_sig"><?=$lang->botonera->siguiente?></button>
		</div>
		</div>
	</fieldset>
	
	<fieldset class="hidden">
		<div class="fld_wrapper hidden" id="dentrofuera">
		<table class="widefat">
			<tr>
				<td style="text-align:center">
					<label><?=$lang->system->alertas_txt23?> 
						<input type="radio" id="radDentroFuera1" name="radDentroFuera" class="radDentroFuera" <? if (!isset($arrEntidades[0]['al_dentro_fuera']) || $arrEntidades[0]['al_dentro_fuera'] == 1){echo 'checked="checked"';}?> value="1"/>
					</label>
					<label style="margin-left:15px;"><?=$lang->system->alertas_txt24?>
						<input type="radio" id="radDentroFuera2" name="radDentroFuera" class="radDentroFuera" <? if (@$arrEntidades[0]['al_dentro_fuera'] === 0){echo 'checked="checked"';}?> value="0"/>
					</label>
				</td>
			</tr>
		</table>
		</div>
	</fieldset>

	<fieldset class="hidden">
		<legend><?=$lang->system->moviles?></legend>
		<div class="fld_wrapper hidden" id="moviles" title="<?=$lang->system->moviles?>">
		<table class="widefat">
			<tbody>
				<? if(!tienePerfil(16)){?>
				<tr>
					<td class="td_label"><label for="txtFiltroMoviles"><?=$lang->system->filtro?></label></td>
					<td class="td_campo"><input type="text" id="txtFiltroMoviles" class="txtFiltroTransfer" maxlength="50"/></td>
				</tr>
				<? }else{?>
				<tr>					
					<td class="td_campo" colspan=2><input style="visibility:hidden;" type="text" id="txtFiltroMoviles" class="txtFiltroTransfer" maxlength="50"/></td>
				</tr>
				<? }?>
				<tr>
					<td class="td_label"><label for="cmbGrupoMoviles"><?=$lang->system->grupos?></label></td>
					<td class="td_campo">
						<select id="cmbGrupoMoviles">
							<option value="0"><?=$lang->system->seleccione?></option>
							<?php
							if(isset($arrGruposMoviles) && $arrGruposMoviles){
								foreach($arrGruposMoviles as $arrFila){?>
								<option value="<?=$arrFila['gm_id']?>"><?=decode(trim($arrFila['gm_nombre']))?></option>
								<? }
							}?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="td_transfer_box">
						<table>
						<tbody>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td><label for="lstMoviles"><?=$lang->system->moviles_disponibles?></label></td>
								<td></td>
								<td><label for="lstMovilesElegidos"><?=$lang->system->moviles_seleccionados?></label></td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td class="td_campo">
									<select id="lstMoviles" multiple="multiple" size="10" class="ref_lstIzq">
<?									$tempMoviles='';
									foreach ($arrMoviles as $fila){
										if (isset($arrMovilesUsados) && in_array(array('id'=>$fila['mo_id']),$arrMovilesUsados)){
											$arrMovilesElegidos[]=$fila;
											$tempMoviles.=$fila['mo_id'].',';
											continue;
										}
										$class='';
										if (isset($fila['grupos'])){
											$class=implode(' ',$fila['grupos']);
										}
										?>
										<option value="<?=$fila['mo_id']?>" <? if($class){echo "class=\"{$class}\"";}?>>
											<? if(tienePerfil(16)){ ?>
												<?=decode($fila['mo_matricula'])?>											
											<? } else{?>
												<?=decode($fila['mo_matricula'].' / '.$fila['mo_identificador'].' ('.$fila['mod_equipo'].')')?>
											<? } ?>
                                        </option>
<?									}?>
									</select>
								</td>
								<td class="td_pasaje">
									<button type="button" class="ref_btnDerT">&gt;&gt;</button>
									<button type="button" class="ref_btnDer">&gt;</button>
									<button type="button" class="ref_btnIzq">&lt;</button>
									<button type="button" class="ref_btnIzqT">&lt;&lt;</button>
								</td>
								<td class="td_campo">
									<select id="lstMovilesElegidos" name="lstMovilesElegidos[]" multiple="multiple" size="10" class="ref_lstDer">
<?												if (isset($arrMovilesElegidos)){
									foreach ($arrMovilesElegidos as $fila){
										$class='';
										if (isset($fila['grupos'])){
											$class=implode(' ',$fila['grupos']);
										}
					?>
										<option value="<? echo $fila['mo_id']?>" <? if ($class){echo "class=\"{$class}\"";}?>>
                                        	<? if(tienePerfil(16)){ ?>
												<?=decode($fila['mo_matricula'])?>											
											<? } else{?>
												<?=decode($fila['mo_matricula'].' / '.$fila['mo_identificador'].' ('.$fila['mod_equipo'].')')?>
											<? } ?>
                                        </option>
<?																		}}?>
										<!--<option value="dummy"></option>-->
									</select>
								</td>
							</tr>
                            <tr>
                            	<td></td>
                                <td></td>
                                <? if(!tienePerfil(16)){?>								
								<td>
                                	<span id="cantMovilesElegidos" ></span>
                                    <span id="cantMoviles" style="float:right"></span>
                                </td>
								<? }?>
                            </tr>
						</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="buttonera hidden">
			<button type="button" class="btn_ant"><?=$lang->botonera->anterior?></button><button type="button" class="btn_sig"><?=$lang->botonera->siguiente?></button>
		</div>
		</div>
	</fieldset>
	<fieldset class="hidden">
		<legend><?=$lang->system->usuarios?></legend>
		<div class="fld_wrapper hidden" id="usuarios" title="<?=$lang->system->alertas_txt30?>">
		<table class="widefat">
			<tbody>				
				<? if(!tienePerfil(16)){
					if(count($arrUsuarios)>1){?>
						<tr>
							<td class="td_label"><label for="txtFiltroUsuarios"><?=$lang->system->filtro?></label></td>
							<td class="td_campo"><input type="text" id="txtFiltroUsuarios" class="txtFiltroTransfer" maxlength="50"/></td>
						</tr>
				<? }
				 }?>				
				<input type="hidden" id="cmbCCosto" name="cmbCCosto" value="0"/>			
				<tr>
					<td colspan="2" class="td_transfer_box">						
							
						<table>
						<tbody>							
							<tr>
								<? if(count($arrUsuarios)>1){?>
								<td><label for="lstUsuarios"><?=$lang->system->usuarios_disponibles?></label></td>
								<td></td>
								<td><label for="lstUsuariosElegidos"><?=$lang->system->usuarios_seleccionados?></label></td>
								<? }?>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td class="td_campo">
									<? if(count($arrUsuarios)>1){?>
									<select id="lstUsuarios" multiple="multiple" size="10" class="ref_lstIzq">
<?									$tempUsuarios='';
									foreach ($arrUsuarios as $fila){
										if(trim($fila['us_mailAlertas']) != ""){
											if (isset($arrUsuariosUsados2) && in_array($fila['id'],$arrUsuariosUsados2)){
												$arrUsuariosElegidos[]=$fila;
												$tempUsuarios .= $fila['id'].',';
												continue;
											}
											$class='';
											if (isset($fila['us_pe_id'])){
												$class='pe_id_'.$fila['us_pe_id'];
											}
											$valor="{$fila['us_nombre']} {$fila['us_apellido']} / {$fila['dato']} / {$fila['us_telefono']}";
											?>
                                            <option <? if ($class){echo "class=\"{$class}\"";}?> value="<?=$fila['id']?>" title="<?=$valor?>">
												<?=$valor?>
                                            </option>
										<? }?>
									<? }?>
									</select>
									<? }?>
								</td>
								<td class="td_pasaje">
									<? if(count($arrUsuarios)>1){?>
									<button type="button" class="ref_btnDerT">&gt;&gt;</button>
									<button type="button" class="ref_btnDer">&gt;</button>
									<button type="button" class="ref_btnIzq">&lt;</button>
									<button type="button" class="ref_btnIzqT">&lt;&lt;</button>
									<? }?>
								</td>
								<td class="td_campo">
								<? if(count($arrUsuarios)>1){?>
									<select name="lstUsuariosElegidos[]" id="lstUsuariosElegidos" multiple="multiple" size="10" class="ref_lstDer">
									<? if ( isset($arrUsuariosElegidos) ){
										foreach ($arrUsuariosElegidos as $fila){
										$valor = "{$fila['us_nombre']} {$fila['us_apellido']} / {$fila['dato']} / {$fila['us_telefono']}";										
										?>
										<option value="<?=$fila['id']?>" title="<?=$valor;?>"><?=$valor;?></option>
									  <? }
									 }?>
									</select>
								<? } else { ?> 
									<select style="visibility:hidden" id="lstUsuariosElegidos"></select> 
								<? }?>
								</td>
							</tr>							
						</tbody>
						</table>						
					</td>
				</tr>				
			
				<tr>
					<? if(count($arrUsuarios)>1){?>
						<td class="td_label"><label for="txtOtrosEmail"><?=$lang->system->otros?></label></td>
					<? }else{?>
						<td class="td_label" style="width:150px; text-align:right;"><label for="txtOtrosEmail"><?=$lang->system->alertas_txt25?></label>&nbsp;&nbsp;</td>
					<? }?>
					<td class="td_campo"><input type="text" id="txtOtrosEmail" name="txtOtrosEmail" placeholder="ej: info@localizar-t.com.ar" value="<?=trim(@$arrEntidades[0]['al_otros_email'])?>" style="width:350px;"/></td>
				</tr>
                <tr>
					<td class="td_label" colspan="2" style="text-align:center"><br /><label style="font-size:10px;">**<?=$lang->system->alertas_txt26?>.</label></td>
				</tr>
			</tbody>
		</table>
		
		
		<fieldset style="display:none">
			<legend><?=$lang->system->probar?></legend>
			<p style="text-align:center"><?=$lang->system->alertas_txt27?></p>
			<p style="text-align:center"><img src="imagenes/cargando.gif" id="imgCargando" style="display:none"/><button type="button" id="probar"><?=$lang->botonera->enviar?></button></p>
		</fieldset>
		<fieldset style="display:none">
			<legend><?=$lang->system->resumen_alerta?></legend>
			<p style="text-align:center" id="msjDescripcion">
				<?php
				$dentro 	= isset($arrEntidades[0]['al_dentro_fuera']) 	? $arrEntidades[0]['al_dentro_fuera'] 	: 0;
				$max 		= isset($arrEntidades[0]['al_vel_max']) 		? $arrEntidades[0]['al_vel_max'] 		: 0;
				$min 		= isset($arrEntidades[0]['al_vel_min']) 		? $arrEntidades[0]['al_vel_min'] 		: 0;
				$evento 	= isset($arrEntidades[0]['al_evento']) 			? $arrEntidades[0]['al_evento'] 		: 0;
				$geocerca 	= isset($arrEntidades[0]['al_referencia']) 		? $arrEntidades[0]['al_referencia'] 	: 0;
				$mensaje = "";
				if ($geocerca && $evento) {
					
					$mensaje .= $dentro?$lang->system->alertas_txt_msg1:$lang->system->alertas_txt_msg1;
					if ($max && $min) {
						$mensaje .= ' '.$lang->system->alertas_txt_msg4;
					} else if ($max) {
						$mensaje .= ' '.$lang->system->alertas_txt_msg5;
					} else if ($min) {
						$mensaje .= ' '.$lang->system->alertas_txt_msg6;
					}
					$mensaje .= $lang->system->alertas_txt_msg3;
				} else if ($geocerca) {
					$mensaje .= ' '.$lang->system->alertas_txt_msg7;
					if ($max && $min) {
						$mensaje .= ' '.$lang->system->alertas_txt_msg4;
					} else if ($max) {
						$mensaje .= ' '.$lang->system->alertas_txt_msg5;
					} else if ($min) {
						$mensaje .= ' '.$lang->system->alertas_txt_msg6;
					}
					$mensaje .= ' '.$lang->system->alertas_txt_msg3;
				} else if ($evento) {
					$mensaje .= ' '.$lang->system->alertas_txt_msg8;
					$mensaje .= ' '.$lang->system->alertas_txt_msg3;
				}
				
				echo $mensaje;
				$mensaje = "";
				?>
				
			</p>
		</fieldset>
		<div class="buttonera hidden">
			<button type="button" class="btn_ant"><?=$lang->botonera->siguiente?></button>
			<button type="button" disabled="disabled"><?=$lang->botonera->siguiente?></button>
			<button type="button" class="btn_fin"><?=$lang->botonera->finalizar?></button>
		</div>
		</div>
	</fieldset>
	<input type="hidden" id="hid_lstGeocercasElegidas" name="hid_lstGeocercasElegidas" value="<?=$tempGeocercas;?>"/>
	<input type="hidden" id="hid_lstUsuariosElegidos" name="hid_lstUsuariosElegidos" value="<?=$tempUsuarios;?>"/>
	<input type="hidden" id="hid_lstMovilesElegidos" name="hid_lstMovilesElegidos" value="<?=$tempMoviles;?>"/>
	<input type="hidden" id="hid_lstAlertasElegidas" name="hid_lstAlertasElegidas" value="<?=$tempAlertas;?>"/>
	
    <!-- datos temporal para edicion -->
    <input type="hidden" id="tipoAlerta" value="<?=@$arrEntidades[0]['al_tipo']?>"/>
    <input type="hidden" id="h_zonas" value="<?=$tempGeocercas;?>"/>
	<input type="hidden" id="h_moviles" value="<?=$tempMoviles;?>"/>
	<input type="hidden" id="h_eventos" value="<?=$tempAlertas;?>"/>
    <!-- -->
    
    
    <input type="hidden" id="hid_txtOtrosEmail" name="hid_txtOtrosEmail" value=""/>
	<input type="hidden" id="hid_radDentroFuera" name="hid_radDentroFuera" value=""/>
	<input type="hidden" id="hid_txtVelMin" name="hid_txtVelMin" value=""/>
	<input type="hidden" id="hid_txtVelMax" name="hid_txtVelMax" value=""/>
	<input type="hidden" id="hid_tipoAlerta" name="hid_tipoAlerta" value=""/>
	
	<script type="text/javascript">
	<?php if ( isset($arrEntidades[0]) ): ?>
		<?php if (strlen($arrEntidades[0]['al_tipo']) == 1): ?>
			setPlantilla("<?php echo $arrEntidades[0]['al_tipo']; ?>", 0);
		<?php endif; ?>
	<?php endif ?>
	</script>
</div>
