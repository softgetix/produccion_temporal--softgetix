var registros, arrBitMotor, celulares, token;
var total_registros = 0;	
var documentHeight = 0;		
var nomenclar_i = 0;
var nomenclar_iHasta = 0;
var ajaxNomenclarComplete = true;
var primerIngreso = true;

var buscarHabilitado = true;
var buscar_response = false;
var pressSelect = false;

$(document).ready(function(){
	
	$('input').keypress(function(e){
    	if(e.which == 13){
      		return false;
    	}
  	});
		
		
	documentHeight = $(document).height();
	nomenclar_iHasta = parseInt(documentHeight/28);
	
	//-- inicio. Autocomplete MOVILES/GEOZONAS --//
	var ajaxAutocomplete;
	$("#txtBuscar" ).autocomplete({
   		source: function( request, response ) {
			if(typeof(ajaxAutocomplete) != 'undefined'){
				ajaxAutocomplete.abort();
			}	
			
			$(this).removeClass('ui-autocomplete-loading');
		ajaxAutocomplete = $.ajax({
			type: "POST",
			url: "ajax.php",
			dataType: "json",
			data:({
				accion:'get-buscador-movil',
				buscar:request.term
			}),
			success: function(data){
				response( $.map( data.resultados, function(item) {
					return {
						label: item.valor,
						value: item.valor,
						id: item.id,
					}
				}));
					
				if (!data.resultados.length) {
					buscar_response = false;
				}
				else {
					buscar_response = true;
				}   
				$("#txtBuscar").removeClass('ui-autocomplete-loading');    
			}
		});
	},
    minLength: 2,
       select: function( event, ui ) {
       	$(this).end().val(ui.item.value);
			if (buscarHabilitado){
				$("#idMovil").val($.trim(ui.item.id));
				if(typeof(sFilterText) != 'undefined'){
					pressSelect = true;
					filtrar($('#idMovil').val(), $('#fecha').val());
				}
			}
        },
        open: function() {
        	$(this).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
        },
        close: function() {
        	$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
        }
	});
	//-- fin. Autocomplete MOVILES/GEOZONAS --//
	
	
	if(parseInt($('#idMovil').val()) > 0){
		filtrar($('#idMovil').val(), $('#fecha').val());
	}
});

$(document).scroll(function (event) {
    var scroll = $(document).scrollTop();
    var total = parseInt(((documentHeight + scroll)/28));
	if(total > nomenclar_iHasta){
		nomenclar_iHasta = total;	
	}
	
	nomenclarHistorico();
});

setBuscador = function(e){
	if(e.keyCode == 13){
		
		if(/*buscar_response == true &&*/ pressSelect == false){
			if($('#txtBuscar').val() != '' && $('#idMovil').val() != ''){
				filtrar($('#idMovil').val(), $('#fecha').val());
			}
		}
		pressSelect = false;
		
		if($('#txtBuscar').val() == '' || $('#idMovil').val() == ''){
			$('#idMovil').val('');
			$('#txtBuscar').val('')		
		}
	}
}	

var ajaxHistorico;
function filtrar(idMovil, fecha){
	OcultarBtnVideo();
	
	if(typeof(ajaxNomenclar) != 'undefined'){
		ajaxNomenclar.abort();
		ajaxNomenclarAbort = true;
	}	
	
	idMovil = idMovil;	
	if(typeof(ajaxHistorico) != 'undefined'){
		ajaxHistorico.abort();
	}
	
	var colspan = 11;
	$('tbody#resultado').html('<tr><td colspan="'+colspan+'"><img src="imagenes/ajax-loader.gif"/></td></tr>'); 
	$('#infoPtos').hide().empty();
	$('#mapa-historico').html('<img src="imagenes/ajax-loader.gif" class="float_l"/>');  	
	ajaxHistorico = $.ajax({
		type: "POST",
		url: "ajaxHistorico.php",
		dataType: "json",
		data:({
			accion:'get-datos',
			fecha:fecha,
			idMovil:idMovil
		}),
		success: function(data){
			 if(data.msg === 'Error 408'){
				$('tbody#resultado').html('<tr><td colspan="'+colspan+'">'+arrLang['tiempo_carga']+'</td></tr>');  	
			}
			else if (data.msg === 'ok' && data.result != false){
                registros = data.result;
				arrBitMotor = data.bit;
                celulares = data.celulares;
				token = data.token;
				total_registros = registros.length
				crearMarcadores();
				
				if(primerIngreso == true){
					historicoView();
					Cargar();
					vistaHistorico();
					primerIngreso = false;
				}
				else if($('#detalle-historico').css('display') == 'none'){
					Cargar();
					vistaHistorico();	
					VerBtnVideo();
				}
				else{
					vistaHistorico();	
					Cargar();
				}
			}
			else{
              	$('tbody#resultado').html('<tr><td colspan="'+colspan+'" style="text-align:center" >'+arrLang['sin_resultados']+'</td></tr>'); 
				$('#mapa-historico, #btnExportar, #btnDetalleMapa, #btnPlayVideo').hide();
				$('#detalle-historico').show();
				primerIngreso = true;
            }
		}
	});
}

var ajaxNomenclar;
var ajaxNomenclarAbort = false;
function vistaHistorico(){
	ajaxNomenclarAbort = false;
	$.ajax({
		type: "POST",
		url: "ajaxHistorico.php",
		//datatype:"json",
		data:({
			accion:'vista-historico'
			,data:JSON.stringify({arrBitMotor:arrBitMotor,esCelular:celulares,arr_datos:registros})
		}),
		success: function(table){
			nomenclar_i = 0;
			if(nomenclar_iHasta < 20){
				nomenclar_iHasta = 20;	
			}
			
			$('tbody#resultado').html(table); 
			nomenclarHistorico();
		} 
	});
}

function nomenclarHistorico(){
	if(ajaxNomenclarComplete == true){
		
		if(nomenclar_iHasta > total_registros){
			nomenclar_iHasta = total_registros;
		}
		
		if(nomenclar_iHasta > nomenclar_i){
			ajaxNomenclarComplete = false;
			$('#nomenclado_'+nomenclar_i).html('<img src="imagenes/ajax-loader.gif"/>'); 
			ajaxNomenclar = $.ajax({
				type: "POST",
				url: "ajaxHistorico.php?i="+nomenclar_i,
				data:({
					accion:'nomenclar-historico'
					,i:nomenclar_i
					,idMovil:[0]['idMovil']
					,lat:registros[nomenclar_i]['lat']
					,lng:registros[nomenclar_i]['lon']
					,id_referencia:registros[nomenclar_i]['idHe']
					,evento:registros[nomenclar_i]['evento_txt']
					,fecha:registros[nomenclar_i]['fechaGenerado'] //fechaOrdenado
				}),
				success: function(data){
					$('#nomenclado_'+nomenclar_i).html(data); 
				}
				,beforeSend: function(){
					ajaxNomenclarComplete = false;
				}
				,complete: function(){
					ajaxNomenclarComplete = true;
					if(ajaxNomenclarAbort == false){
						nomenclar_i++;
						nomenclarHistorico();
					}
				}
			});
		}
	}
}

function historicoView(){
	$('#btnExportar').show();
	var a = $('#detalle-historico');
	var m = $('#mapa-historico');
	var btn = $('#btnDetalleMapa');
	if (a.css('display') == 'none'){
        m.css('display', 'none');
		$('#footer_space').css('display', 'block');
		a.css('display', 'block');
        btn.html(arrLang['mapa']);
		$('#infoPtos').hide();
		OcultarBtnVideo();		
	} 
	else {
        a.css('display', 'none');
        m.css('display', 'block');
		$('#footer_space').css('display', 'none');
		btn.html('&lt;&lt; '+arrLang['detalle']);
		btn.show();
		VerBtnVideo();
	}	
}