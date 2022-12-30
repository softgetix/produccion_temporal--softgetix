var objectMoviles = 'fieldset#alert-moviles';
var objectReferencias = 'fieldset#alert-referencia';
var objectEventos = 'fieldset#alert-evento';
var objectUsuarios = 'fieldset#alert-usuario';

var objectActual = null;


$(document).ready(function(){
	
	if($('#hidOperacion').val() == 'alta' || $('#hidOperacion').val()=='modificar'){
	
	var $browser = null;
	if (navigator.userAgent.search("MSIE") >= 0) {
   		$browser = 'ie';
	}
	else if (navigator.userAgent.search("Chrome") >= 0) {
		$browser = 'chrome';
	}
	else if (navigator.userAgent.search("Firefox") >= 0) {
		$browser = 'firefox';
	}
	else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
		$browser = 'safari';
	}
	else if (navigator.userAgent.search("Opera") >= 0) {
		$browser = 'opera';
	}
	
	
	//-- buscadores --//
	$(objectMoviles+' .content-tags .search_tags input[type=text]').keyup(function() {
		objectActual = objectMoviles;
		searchTags(objectActual);  
	});
	
	$(objectReferencias+' .content-tags .search_tags input[type=text]').keyup(function() {
		objectActual = objectReferencias;
		searchTags(objectActual); 
	});
	
	$(objectEventos+' .content-tags .search_tags input[type=text]').keyup(function() {
		objectActual = objectEventos;
		searchTags(objectActual); 
	});
	
	$(objectUsuarios+' .content-tags .search_tags input[type=text]').keyup(function(e) {
		objectActual = objectUsuarios;
		searchTags(objectActual); 
		
		if(e.keyCode == 13){
			if($(this).val() != ''){
				var ide = 'alert-usuario@@'+$(this).val();
				inputTagElement('alert-usuario',ide, $(this).val());
								
				var valor = $('#val-alert-usuario').val();
				if(valor != ''){
					$('#val-alert-usuario').val(valor+','+$(this).val());	
				}
				else{
					$('#val-alert-usuario').val($(this).val());		
				}
		
				$(this).val('');
				$(this).focus();
				$(objectUsuarios+' .content-tags .search_tags ul li').hide();
			}
		}
	});
	//-- buscadores --//
	
	//-- Seleccion de opcion de busqueda --//
	$('.search_tags ul li a.input-add-tag').click(function(e){
    	var arr = $(this).attr('id').split('@@');
		var valor = $('#val-'+arr[0]).val();
		if(valor != ''){
			$('#val-'+arr[0]).val(valor+','+arr[1]);	
		}
		else{
			$('#val-'+arr[0]).val(arr[1]);		
		}
		
		var ide = $(this).parent().parent().parent().parent().parent().attr('id');
		inputTag(ide,this); 
		ide = 'fieldset#'+ide+' .content-tags';
		$(ide+' .search_tags input[type=text]').val('');
		$(ide+' .search_tags input[type=text]').focus();
	});
	//-- --//
	
	$(document).keyup(function(e) {
		if(e.keyCode == 38 || e.keyCode == 40){	
			if(!$(objectActual+' .search_tags ul li:visible a').is(':focus')){
				$(objectActual+' .search_tags ul li:visible:first a').focus();
			}
			else if($browser == 'ie' || $browser == 'chrome'){
				var obj = $(objectActual+' .search_tags ul li a:focus');
				if(e.keyCode == 40){
					$(obj).parent().nextAll(':visible:first').children('a').focus();
				}
				else{
					$(obj).parent().prevAll(':visible:first').children('a').focus();	
				}
			}
		}                   
	});
	
	//-- Mecanismo para ocultar lista al seleccionar en cualquier otra parte del documento --//
	$(document).click(function(e){
		$('.content-tags .search_tags ul li').hide();
		$('.search_tags input[type=text]').val('');
	});
	//-- --//
	
	//-- Cargar BOX de filtros (para la edición o datos por defecto)--//
	$('fieldset').find('input[type=hidden]').each(function(idx, elem) {
		
		var ide = $(elem).attr('id').replace('val-','');
		var valor = $(elem).val();
		if(valor != '' && ide != 'val_lunes_a_viernes' && ide != 'val_sabados_y_domingos'){
			var ids_actual = valor.split(',');
			$('fieldset#'+ide+' .content-tags .search_tags ul li').find('a').each(function(idx2, elem2) {
				for(var i=0; i<ids_actual.length; i++){
					if($(elem2).attr('id') == ide+'@@'+ids_actual[i]){
						inputTag(ide,elem2); 
					}
				}
			});
			
			//-- excepcion para usuarios alternativos --//
			if(ide == 'alert-usuario'){
				for(var i=0; i<ids_actual.length; i++){
					if(isNaN(ids_actual[i])){
						inputTagElement(ide,ide+'@@'+ids_actual[i], ids_actual[i]);
					}
				}
			}
			//-- --//
			
		}
	});
	//-- --//
	}
});
/*

function emptyCampos(ide){// por IE por cuestiones del placeholder

	var isInputSupported = 'placeholder' in document.createElement('input');
	if($browser = 'ie' && isInputSupported != true){ 
		
		alert(ide);
		//alert($(ide).attr('placeholder'));
		
	}
	else{
		$(ide).val('');		
	}
}*/


function searchTags(ide){
	//$('.content-tags .search_tags ul li').hide();
	$(ide+' .search_tags ul li').hide();
	var txtBuscar = $(ide+' .search_tags input[type=text]').val();
	
	if(txtBuscar != ''){
		$(ide+' .search_tags ul').find('li').each(function(idx, elem) {
			$elem = $(elem);
			
			if ($elem.text().toLowerCase().lastIndexOf(txtBuscar.toLowerCase()) != -1){
				$elem.show();
			}
		});
	}
}

function inputTag(id,elem){
	inputTagElement(id,$(elem).attr('id'), $(elem).html());
	var ide = 'fieldset#'+id+' .content-tags';
	$(ide+' .search_tags ul li').hide();
	$(elem).parent().addClass('active');	
}

function inputTagElement(id,elemID, elemValue){
	var ide = 'fieldset#'+id+' .content-tags';
	$(ide+' .tags-input').append('<span class="tag" id="tag-'+elemID+'"><span>'+elemValue+'</span><a href="javascript:;" onclick="javascript:deleteTag(\''+elemID+'\')"></a></span>');
}

function deleteTag(ide){
	var arr = ide.split('@@');
	
	var ids_actual = $('#val-'+arr[0]).val().split(',');
	var coma = '';
	var valor = '';
	for(var i=0; i<ids_actual.length; i++){
		if(ids_actual[i] != arr[1]){
			valor = valor+coma+ids_actual[i];
			coma = ',';
		}
	}
	$('#val-'+arr[0]).val(valor);
	
	$('fieldset#'+arr[0]+' .content-tags .search_tags ul li').find('a').each(function(idx, elem){
		$elem = $(elem);
		if($elem.attr('id') == ide){
			$elem.parent().removeClass('active');
		}
	});
	
	$('fieldset#'+arr[0]+' .content-tags .tags-input').find('span').each(function(idx, elem){
		$elem = $(elem);
		if($elem.attr('id') == 'tag-'+ide){
			$elem.remove();
		}
	});
}

function check_day($this){
	
	var $class = $($this).children('span.check').attr('class');
	if($class.indexOf('check_on') > 0){
		$($this).children('span.check').attr('class',$($this).children('span.check').attr('class').replace('check_on','check_off'));
		$('#val_'+$($this).attr('id')).val(0);	
	}
	else{
		$($this).children('span.check').attr('class',$($this).children('span.check').attr('class').replace('check_off','check_on'));
		$('#val_'+$($this).attr('id')).val(1);		
	}
}