/**** Filtros Column Table ****/
var $filterTable = null;
$(document).ready(function(){
    /*$('div.filterTable').mouseover(function(e){
	$(this).find('dl').show();
    }).mouseleave(function(){
	$(this).find('dl').hide();
    });*/
	
    /*.mouseout(function(){
	//$(this).find('dl').hide();
    });*/
    
    $('div.filterTable').click(function(e){
	//$(this).find('dl').show();
        if($filterTable != 'none'){
            $(this).find('div.showFilter').css('display','block');
            
            var $dl = $(this).children('div.showFilter').children('div.contenedorFilter').children('dl');
            if($dl.height() > 230){
                $dl.css('overflow-y','scroll');	
                //var ancho = $(this).children('dl').width();
                $dl.css('width','auto');
                $dl.css('height','240px');	
            }
        }
        $filterTable = 'show';
    });
    
    $('a.button.cancel').click(function(e){
        $(this).parent().parent().parent().css('display','none');
        $filterTable = 'none';
    });
    
});

/*
$(window).load(function(){
    $('div.filterTable').each(function(index, value){
        if($(this).children('dl').height() > 230){
            $(this).children('dl').css('overflow-y','scroll');	
            //var ancho = $(this).children('dl').width();
            $(this).children('dl').css('width','auto');
            $(this).children('dl').css('height','240px');	
	}
    });
});
*/
/*
function serializeFilter($ide){
    $('dl#div_filterTablePopup-'+$ide+' dt').each(function() {
       if($(this).children('input[type="checkbox"]').attr('name') != $ide+'CheckAll'){ 
            if($(this).children('input[type="checkbox"]').is(':checked')){
                $('form').append('<input type="hidden" name="'+$ide+'[]" value="'+$(this).children('input[type="checkbox"]').val()+'" />');
            }
        }
    });
    enviar('filtrarCol');
}
*/

function checkFilterAll(thisObject){
    $(thisObject).parent().parent().parent().children('a').children('img').attr('src','imagenes/filtroListen.jpg');
    if($(thisObject).parent().index() == 0){
        var checked = false;
        $($(thisObject).parent().parent().children()).each(function(index, value){
		if(index == 0){
			checked = $(this).children('input[type="checkbox"]').is(':checked');
		}
		else{
			if(checked == true){
				$(this).children('input[type="checkbox"]').attr('checked',true);
			}
			else{
				$(this).children('input[type="checkbox"]').attr('checked',false);
			}		
		}
        });
    }
}
	
function checkFilterItem(thisObject){
	var checked = $(thisObject).is(':checked');
	var idePadre = $(thisObject).parent().parent().children(':eq(0)').children('input[type="checkbox"]').attr('id');
	if(checked == false){
		if($('#'+idePadre).is(':checked') == true){
			$('#'+idePadre).attr('checked',false);
		}
			
		//-- icon filter --//
		$(thisObject).parent().parent().parent().children('a').children('img').attr('src','imagenes/filtroListen_on.jpg');
		$($(thisObject).parent().parent().children()).each(function(index, value){
			if(index > 0){
				if(typeof($(this).children('input[type="checkbox"]').attr('value')) != 'undefined'){
					if($(this).children('input[type="checkbox"]').is(':checked') == true){
						checked = true;
					}
				}	
			}	
		});
		
		if(checked == false){
			$(thisObject).parent().parent().parent().children('a').children('img').attr('src','imagenes/filtroListen.jpg');
		}
		//-- --//
		
	}
	else{//-- verifico si todas las opciones se encuentran seleccionadas para chequear o no la opcion: (Seleccionar todo)
		var checked = true;
		$($(thisObject).parent().parent().children()).each(function(index, value){
			if(index > 0){
				if(typeof($(this).children('input[type="checkbox"]').attr('value')) != 'undefined'){
					if($(this).children('input[type="checkbox"]').is(':checked') == false){
						checked = false;
					}
				}	
			}	
		});
		$('#'+idePadre).attr('checked',checked);
	
		//-- icon filter --//
		if(checked == false){
			$(thisObject).parent().parent().parent().children('a').children('img').attr('src','imagenes/filtroListen_on.jpg');
		}
		else{
			$(thisObject).parent().parent().parent().children('a').children('img').attr('src','imagenes/filtroListen.jpg');	
		}
		//-- --//
	}
}
/*** ***/