g_bDebugMode = true;

var port = "";
if(document.location.port != ''){
	port = ":"+document.location.port;	
}
var arrpath = (document.location.href).split('/');
//var dominio = 'https://' + document.location.hostname + port + '/'+arrpath[1]+'/'; codigo comentado
var dominio = arrpath[0]+'//'+document.location.hostname + port + '/'+arrpath[3]+'/';

//--- Capturar F5 (evitar re-envio de variables por POST)---//
$(function() {
$(window).keydown(function(e){
	var code = (e.keyCode ? e.keyCode : e.which);
	if(code == 116) {
		location.href = location.href;
	}
});
});
//--- ---//

//funciones y eventos que se ejecutan al cargar la pagina
$(document).ready(function(){

	$('.only_number').bind('keypress', function(e) { 
		key=(document.all) ? e.keyCode : e.which;
		if ((key < 48 || key > 57) && key != 0 && key != 8){
			return false;
		}
	});

	$('.only_number_and_char').bind('keypress', function(e) { 
		key=(document.all) ? e.keyCode : e.which;
		if ((key >= 32 && key <=47) || (key >= 58 && key <=64) || (key >= 91 && key <=96) || (key >= 123 && key <=128) || key == 161 || key == 176 || key == 180 || key == 191){
			return false;
		}
	});
	
	$("input[type=checkbox]").change(function(){
		// botonBaja
		flag = false;
		$('input[type=checkbox]').each(function(index) {
			 if (flag==false) {
				 if ($(this).is(':checked')) {
					flag = true
				 } else {
					flag = false;
				 }	
			 }
		});
		
		if (flag==true) {
			$("#botonBaja").css("display","block");
		} else {
			$("#botonBaja").css("display","none");
		}
		
	});
	
	$(document).bind('click', function(e) {
		if( e.which == 2 ) {
		   e.preventDefault();
		}
	});

	$.easing.bouncy = function (x, t, b, c, d) {
		var s = 1.70158;
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	}
	
	// create custom tooltip effect for jQuery Tooltip
	$.tools.tooltip.addEffect("bouncy",
	
		// opening animation
		function(done) {
			this.getTip().animate({top: '+=15'}, 500, 'bouncy', done).show();
		},
	
		// closing animation
		function(done) {
			this.getTip().animate({top: '-=15'}, 500, 'bouncy', function()  {
				$(this).hide();
				done.call();
			});
		}
	);
	
	$('.fbJewel[title]').tooltip({
		offset: [150, 0]//,
	});
	
	
	var res = screen.width;
	//var res = $(window).width();
	$('#hidResolucion').val(res)

	setMetrics();
	
	/*
	$('.opcionMenu').mouseover(function(){
		var idOption = this.id.replace('menu_',''),
			idGroup = 'grupo' + idOption,
			velocidad = (navigator.appName === "Microsoft Internet Explorer")? 30 : 250;
		hideOptionsMenu(idGroup);
		$('#' + idGroup).fadeIn(velocidad);
	 });
	*/ 
	 
	var res = screen.width;
	$('#hidResolucion').val(res)	
	
	
	$('#uploadFile').live("click", function(){
		$(this).addClass('clear disabled').html('<span class="float_l">'+arrLang['msj_cargando']+'&nbsp;</span><img src="imagenes/ajax-loader.gif" class="float_r"></img><span class="clear"></span>').attr('onclick','javascript:;');
	});
	
});

function only_number(e){
	key=(document.all) ? e.keyCode : e.which;
	if ((key < 48 || key > 57) && key != 0  && key != 8){
		e.preventDefault();
		return true;
	}
	return false;
}

function mostrarPopupMultiRastreo(url){
	$('#popupOverlay').hide();
	var ov=$('<div id="popupOverlay0"/>').prependTo('body')
	for (var i = 0;i < 4;i++) {
		var ph=$('<div id="popupHolder'+i+'"/>').insertAfter($('#popupOverlay0'));
		ph.append('<iframe id="popupFrm'+i+'" frameborder="0" src="about:blank" />');
		$('#popupHolder'+i+'').css({'width':'48%','left':(i%2)*50 + '%'});
		$('#popupFrm'+i).attr('src',url);
		$('#popupOverlay,#popupHolder'+i+'').show();
	}
		
	$('#popupOverlay0').click(function() {
		$('#popupOverlay0,#popupHolder0,#popupHolder1').hide();
	})
}

var idSeccion;

function distancia(lat1, lat2, long1,long2){
	var degtorad = 0.01745329;
	var radtodeg = 57.29577951;
	var dlong = (long1 - long2);
	var dvalue = (Math.sin(lat1 * degtorad) * Math.sin(lat2 * degtorad))	+ (Math.cos(lat1 * degtorad) * Math.cos(lat2 * degtorad) * Math.cos(dlong * degtorad));   
	var dd = Math.acos(dvalue) * radtodeg;
	var miles = (dd * 69.16);
	var km = (dd * 111.302);
	return km;
}

/*
0 = index de registros
1 = apagado(0)/ascendente(1)/descendente(2)
*/
var orden = new Array(),orden_actual = 0;

function strcmp(str1, str2) {
	return ((str1 === str2) ? 0 : ((str1 > str2) ? 1 : -1));
}

function usort(inputArr, sorter) {
	var valArr = [], k = '', i = 0, strictForIn = false, populateArr = {};

	if (typeof sorter === 'string') {
		sorter = this[sorter];
	} else if (Object.prototype.toString.call(sorter) === '[object Array]') {
		sorter = this[sorter[0]][sorter[1]];
	}

	// BEGIN REDUNDANT
	this.php_js = this.php_js || {};
	this.php_js.ini = this.php_js.ini || {};
	// END REDUNDANT
	strictForIn = this.php_js.ini['phpjs.strictForIn'] && this.php_js.ini['phpjs.strictForIn'].local_value && this.php_js.ini['phpjs.strictForIn'].local_value !== 'off';
	populateArr = strictForIn ? inputArr : populateArr;

	for (k in inputArr) { // Get key and value arrays
		if (inputArr.hasOwnProperty(k)) {
			valArr.push(inputArr[k]);
			if (strictForIn) {
				delete inputArr[k];
			}
		}
	}
	try {
		valArr.sort(sorter);
	} catch (e) {
		return false;
	}
	for (i = 0; i < valArr.length; i++) { // Repopulate the old array
		populateArr[i] = valArr[i];
	}

	return strictForIn || populateArr;
}
function sort(n) {
	if(n !== orden_actual) {
		orden[orden_actual][1] = 0;
		document.getElementById('img_'+(orden_actual + 1)).src = 'imagenes/ordenar_0.png';
		orden_actual = n;
	}
        
	if(orden[n][1] === 0) {
		if(nomenclado) {
			registros_ordenados = usort(registros, function(a, b) { return strcmp(a[orden[n][0]], b[orden[n][0]]); });
		}
		else{
			registros = usort(registros, function(a, b) { return strcmp(a[orden[n][0]], b[orden[n][0]]); });
		}
		orden[n][1] = 1;
		document.getElementById('img_'+(orden_actual + 1)).src = 'imagenes/ordenar_1.png';
	}
	else if(orden[n][1] === 1) {
		if(nomenclado) {
			registros_ordenados = usort(registros, function(a, b) { return strcmp(b[orden[n][0]], a[orden[n][0]]); });
		} else {
			registros = usort(registros, function(a, b) { return strcmp(b[orden[n][0]], a[orden[n][0]]); });
		}
		orden[n][1] = 2;
		document.getElementById('img_'+(orden_actual + 1)).src = 'imagenes/ordenar_2.png';
	} else if(orden[n][1] === 2) {
		if(nomenclado) {
			registros_ordenados = usort(registros, function(a, b) { return strcmp(a[orden[n][0]], b[orden[n][0]]); });
		} else {
			registros = usort(registros, function(a, b) { return strcmp(a[orden[n][0]], b[orden[n][0]]); });
		}
		orden[n][1] = 1;
		document.getElementById('img_'+(orden_actual + 1)).src = 'imagenes/ordenar_1.png';
	}
	actualPage(true);
}

function sqlDateToUnixTimestamp(sqlDate) {
	//DEVUELVE LA CANTIDAD DE SEGUNDOS DE LA FECHA QUE SE LE PASA
	//PARAMETROS: FECHA EN FORMATO DIA/MES/A�O HORA:MINUTO
	var day		= sqlDate.substr(0, 2),
		month	= sqlDate.substr(3, 2) - 1,
		year	= sqlDate.substr(6, 4),
		hours	= sqlDate.substr(11, 2),
		minutes	= sqlDate.substr(14, 2),
		date = new Date(year, month, day, hours, minutes);
	return parseInt(date.getTime() / 1000,10);
}

function setMetrics(){
	return; /* NHK */
	var widthScreen = screen.width;
	$('#wrapper').css('minWidth',(widthScreen - 100) + 'px');
}


function fechaFormateadaAFechaJs(fecha, timestamp) {
	var fechaJS = fecha.substr(6,4) + '/' + fecha.substr(3,2) + '/' + fecha.substr(0,2);
	if(timestamp) {
		return new Date(fechaJS).getTime();
	} else {
		return fechaJS;
	}
}


function capturarEnter(e){
	var intTecla;
	if(document.all){
		intTecla=event.keyCode;
	}else{
		intTecla=e.which;
	}
	if(intTecla===13){return true;}
	else{return false;}
}

function cerrarMensaje() {
   $('#messageDefaultLocalizart').hide();
}

var closeMSG;
function viewMessage(resp, mensaje){//style="display:none"
	if(typeof(closeMSG) != 'undefined'){
		clearInterval(closeMSG);
	}
	
	var classe = '';
	var secsMSG = 10;
	
	if(resp == true){
		if(mensaje == null){
			mensaje = arrLang['procesar_datos_ok'];	
		}
		classe = 'msj_ok';
	}
	else{
		if(mensaje == null){
			mensaje = arrLang['procesar_datos_error'];	
		}
		classe = 'msj_error';
	}
	
	$('#content').append('<div class="'+classe+'"><a href="javascript:closeMessage();"><img id="imgCerrarMensaje" src="imagenes/cerrar.png" /></a><span>'+mensaje+'</span></div>');	
	
	var msg = $('div'+classe);
	if (!msg.hasClass('no_cerrar')){
		closeMSG = setInterval(function(){
			if (!secsMSG){
				closeMessage()
			}
			else{
				secsMSG = --secsMSG;
			}
		},1000);
	}
}

function closeMessage() {
	$('.msj_ok, .msj_error').fadeOut(1000, function(){
		$('.msj_ok, .msj_error').remove();
	});	
	
}

function viewReload(mensaje){
	if(mensaje == null){
		mensaje = arrLang['procesando_datos'];	
	}
		
	$('#content').append('<div class="msj_reload"><span>'+mensaje+'</span></div>');	
}

function closeReload(){
	 $('.msj_reload').remove();
}


/*
function resizePage(){
	var bodyWidth = $('body').width(),
		colIzqWidth = $('#colIzq').width(),
		nuevoWidthDiv = parseInt((bodyWidth - colIzqWidth) - 40,10),
		minWidthWrapper = (parseInt(nuevoWidthDiv,10) + parseInt(colIzqWidth,10)) + 75,
		minHeightWrapper = screen.height - 230,
		widthWrapper;

	//solo redimensiona si es mayor al ancho de la pantalla - 50, ese es el maximo que se puede achicar
	if(minWidthWrapper > (screen.width - 10)){
		//cambio el ancho minimo en el wrapper segun la resolusion
		$('#wrapper').css('minWidth', minWidthWrapper + 'px');

		//cambio los anchos del main y la botonera
		$('#botonera').width(nuevoWidthDiv);
	}
	//si la pantalla es mas chica que eso, entonces seteo un ancho fijo para los divs
	//equivalente al 73% del ancho del Wrapper
	else{
		widthWrapper = $('#wrapper').width();
		nuevoWidthDiv = Math.floor((widthWrapper * 0.78)) + 50;

		//cambio los anchos del main y la botonera
		$('#botonera').width(nuevoWidthDiv);
	}

	//el alto del wrapper lo seteo siempre
	$('#wrapper').css('minHeight', minHeightWrapper + 'px');
}
*/

function enviar(operacion, id){
	var seccion = $('#hidSeccion').val(),
		f = $('#frm_'+ seccion),
		oCheck = new check(''),
		checks = document.getElementsByName('chkId[]'),
		val = "",i,moviles='',eventos='',equipo,evento,usuarios='',usuariosAsignados,hidUsuariosSerializados,max,
		movilesAsignados,hidMovilesSerializados,equipos,equiposAsignados,hidEquiposSerializados;
		
	$('#hidOperacion').val(operacion);
	if(typeof(id) != 'undefined'){
		document.getElementById('hidId').value = id;	
	}
		
	switch(operacion){
		case 'copiar':
			break;
		case 'guardarP':
			break;
		case 'alta':
		break;
		case 'guardarReferencia':
			break;
		case 'baja':
			val = oCheck.checkCheckbox(checks,1,15);
			if(val === 'menor'){
				alert(arrLang['msj_select_baja']);
				return false;
			}
			else if(val === 'mayor'){
				alert(arrLang['msj_limite_baja']);
				return false;
			}
			else if(confirm(arrLang['msj_confirmar_baja']) === false){
				return false;
			}
		break;
		case 'bajaAllInOne':
			if(confirm("\u00BFDesea dar de baja al Cliente?") === false){
				return false;
			}
		break;	
		case 'bajaEquipo':
			//valida si se selecciono un solo item
			val = oCheck.checkCheckbox(checks,1,15);
			if(val === 'menor'){
				alert(arrLang['msj_select_baja']);
				return false;
			}else if(val === 'mayor'){
				alert(arrLang['msj_limite_baja']);
				return false;
			}else if(confirm(arrLang['msj_confirmar_baja']+" "+arrLang['msj_alerta_baja_equipo']) === false){
				return false;
			}
			break;
		case 'modificar':
			//valida si se selecciono algun item
			val = oCheck.checkCheckbox(checks,1,1);
			if(val === 'mayor'){
				alert(arrLang['msj_un_update']);
				return false;
			}
			else if(val === 'menor'){
				alert(arrLang['msj_select_update']);
				return false;
			}
			break;
		case 'modificarAsignacion':
			document.getElementById('hidId').value = id;
			break;
		case 'guardarAltaAsignacion':
			switch (seccion) {
				case "abmGrupoMoviles":
					movilesAsignados = document.getElementById("cmbMovilesAsignados");
					hidMovilesSerializados = document.getElementById("hidMovilesSerializados");
					max=movilesAsignados.options.length;
					for(i=0;i < max;i++){
						if(moviles===""){
							moviles = movilesAsignados.options[i].value;
						}else{
							moviles += "," + movilesAsignados.options[i].value;
						}
					}
					hidMovilesSerializados.value = moviles;
				break;
			}
			break;		
		case 'guardarAsignacion':
			switch(seccion){
				case "abmUsuarios":
					movilesAsignados = document.getElementById("cmbMovilesAsignados");
					hidMovilesSerializados = document.getElementById("hidMovilesSerializados");
					max=movilesAsignados.options.length;
					for(i=0;i < max;i++){
						if(moviles===""){
							moviles = movilesAsignados.options[i].value;
						}else{
							moviles += "," + movilesAsignados.options[i].value;
						}
					}
					hidMovilesSerializados.value = moviles;
					break;
				case "abmEquiposMoviles":
					equiposAsignados = document.getElementById("cmbEquiposAsignados");
					hidEquiposSerializados = document.getElementById("hidEquiposSerializados");
					equipos="";
					for(i=0;i < equiposAsignados.options.length;i++){
						if(equipos===""){
							equipos = equiposAsignados.options[i].value;
						}else{
							equipos += "," + equiposAsignados.options[i].value;
						}
					}
					hidEquiposSerializados.value = equipos;
					break;
				case "abmGrupoMoviles":
					movilesAsignados = document.getElementById("cmbMovilesAsignados");
					hidMovilesSerializados = document.getElementById("hidMovilesSerializados");
					moviles=""; max=movilesAsignados.options.length;
					for(i=0;i < max;i++){
						if(moviles===""){
							moviles = movilesAsignados.options[i].value;
						}else{
							moviles += "," + movilesAsignados.options[i].value;
						}
					}
				break;
			}
			break;
		case 'volver':
		break;
		case 'cerrarPopup':
			$('#popupOverlay').hide();
			$('#popupHolder').hide();
			//document.getElementById("popupOverlay").style.display = "none";
			//document.getElementById("popupHolder").style.display = "none";
		case 'index':
			$('#hidFiltro').val($('#txtFiltro').val());
			break;
	}
	
	f.submit();
	return true;
}

function enviarModificacion(operacion, id){
	$("[id*=chk_]").attr('checked', false);
	$('#chk_'+id).attr('checked', 'checked');
	enviar(operacion, id);
}

function disablePredefinidos() {
	var divUltimosReportes = document.getElementById("tdPredefinidos"),
		listaInputs = divUltimosReportes.getElementsByTagName("input"),
		max=listaInputs.length,i,input;
	for(i =0; i < max; i++) {
		input = listaInputs[i];
		if(input.type === "radio") {
			input.checked = false;
		}
	}
}
function onClickDisablePredefinidos() {
	var cmbHoraDesde	= document.getElementById('cmbHoraDesde'),
		cmbMinutoDesde	= document.getElementById('cmbMinutoDesde'),
		cmbHoraHasta	= document.getElementById('cmbHoraHasta'),
		cmbMinutoHasta	= document.getElementById('cmbMinutoHasta');
/*
	cmbHoraDesde.onchange	= new Function('disablePredefinidos()');
	cmbMinutoDesde.onchange = new Function('disablePredefinidos()');
	cmbHoraHasta.onchange	= new Function('disablePredefinidos()');
	cmbMinutoHasta.onchange = new Function('disablePredefinidos()');
*/
	cmbHoraDesde.onchange	= disablePredefinidos;
	cmbMinutoDesde.onchange = disablePredefinidos;
	cmbHoraHasta.onchange	= disablePredefinidos;
	cmbMinutoHasta.onchange = disablePredefinidos;
}

function compararFechas(fecha1 , fecha2){
	//COMPARA DOS FECHAS. SI LA PRIMERA ES MENOR A LA SEGUNDA DEVUELVE TRUE SINO DEVUELVE FALSE.
	var fechaInicio = sqlDateToUnixTimestamp(fecha1),
		fechaFin = sqlDateToUnixTimestamp(fecha2);
	if(fechaInicio < fechaFin){
		return true;
	}else{
		return false;
	}
}
 function compareFechasJquery(fecha, fecha2)
      {
        var xMonth=fecha.substring(3, 5);
        var xDay=fecha.substring(0, 2);
        var xYear=fecha.substring(6,10);
        var yMonth=fecha2.substring(3, 5);
        var yDay=fecha2.substring(0, 2);
        var yYear=fecha2.substring(6,10);
        if (xYear> yYear)
        {
            return(true)
        }
        else
        {
          if (xYear == yYear)
          { 
            if (xMonth> yMonth)
            {
                return(true)
            }
            else
            { 
              if (xMonth == yMonth)
              {
                if (xDay> yDay)
                  return(true);
                else
                  return(false);
              }
              else
                return(false);
            }
          }
          else
            return(false);
        }
    }


function calcularRumbo(curso, idioma) {
	idioma = '1';
	var retorno='';
	if(curso > 337.5 || curso <= 22.5) {
		retorno= 'N';
	} else if(curso > 22.5 && curso <= 67.5) {
		retorno= 'NE';
	} else if(curso > 67.5 && curso <= 112.5) {
		retorno= 'E';
	} else if(curso > 112.5 && curso <= 157.5) {
		retorno= 'SE';
	} else if(curso > 157.5 && curso <= 202.5) {
		retorno= 'S';
	} else if(curso > 202.5 && curso <= 247.5) {
		if(idioma == '1') {
			retorno= 'SO';
		} else if(idioma == '2') {
			retorno= 'SW';
		} else if(idioma == '3') {
			retorno= 'SO';
		}
	} else if(curso > 247.5 && curso <= 292.5) {
		if(idioma == '1') {
			retorno= 'O';
		} else if(idioma == '2') {
			retorno= 'W';
		} else if(idioma == '3') {
			retorno= 'O';
		}
	} else if(curso > 292.5 && curso <= 337.5) {
		if(idioma == '1') {
			retorno= 'NO';
		} else if(idioma == '2') {
			retorno= 'NW';
		} else if(idioma == '3') {
			retorno= 'NO';
		}
	}
	return retorno;
}

function procesarTipsFiltro(arrInputs,texto){
	
	var $input;
	$.each(arrInputs,function(i,e){
		$input=$('#'+e);
		$input.addClass('conTip').data('tip',texto);
		$input.attr('title','<br />' + texto)
		if ($input.val()===''){
			$input.addClass('showTip').val(texto);
		}
	});

	$('input.showTip').live('focus',function(){
		$(this).val('').removeClass('showTip');
	});
	$('input.conTip').blur(function(){
		var $this = $(this);
		if ($this.val()===''){
			$this.val($this.data('tip')).addClass('showTip');
		}
	});

	(function(){
		var _enviar = window.enviar;
		window.enviar = function(operacion, id){
			$('input.showTip').val('').removeClass('showTip');
			_enviar(operacion,id);
		};
	}());
}

function addTimeToDate(time,unit,objDate,dateReference){
    var dateTemp=(dateReference)?objDate:new Date(objDate);
    switch(unit){
        case 'y': dateTemp.setFullYear(objDate.getFullYear()+time); break;
        case 'M': dateTemp.setMonth(objDate.getMonth()+time); break;
        case 'w': dateTemp.setTime(dateTemp.getTime()+(time*7*24*60*60*1000)); break;
        case 'd': dateTemp.setTime(dateTemp.getTime()+(time*24*60*60*1000)); break;
        case 'h': dateTemp.setTime(dateTemp.getTime()+(time*60*60*1000)); break;
        case 'm': dateTemp.setTime(dateTemp.getTime()+(time*60*1000)); break;
        case 's': dateTemp.setTime(dateTemp.getTime()+(time*1000)); break;
        default : dateTemp.setTime(dateTemp.getTime()+time); break;
    }
    return dateTemp;
}
function ceroIzquierda(v){
	if(v<=9)
	{
		v = "0"+String(v);
	}	
	return v;
	
}




function dista(lat1, lat2, long1, long2)
{
    var degtorad = 0.01745329;
    var radtodeg = 57.29577951;
    var dlong = (long1 - long2);
    var dvalue = (Math.sin(lat1 * degtorad) * Math.sin(lat2 * degtorad)) + (Math.cos(lat1 * degtorad) * Math.cos(lat2 * degtorad) * Math.cos(dlong * degtorad));   
    var dd = Math.acos(dvalue) * radtodeg;
    var miles = (dd * 69.16);
    var km = (dd * 111.302);
    return km;
}

function getEscala(zoom)
{
    var metros = 0;
	
    switch (zoom)
    {
        case 17: {            
            metros = 100;
            break;
        }
        case 16: {            
            metros = 200;
            break;
        }
        case 15: {            
            metros = 200;
            break;
        }
        case 14: {            
            metros = 500;
            break;
        }
        case 13: {            
            metros = 1000;
            break;
        }
        case 12: {            
            metros = 2000;
            break;
        }
        case 11: {            
            metros = 5000;
            break;
        }
        case 10: {            
            metros = 10000;
            break;
        }
        case 9: {            
            metros = 20000;
            break;
        }
        case 8: {            
            metros = 50000;
            break;
        }
        case 7: {            
            metros = 100000;
            break;
        }
        case 6: {            
            metros = 200000;
            break;
        }
        case 5: {            
            metros = 350000;
            break;
        }
        case 4: {            
            metros = 1000000;
            break;
        }
        case 3: {            
            metros = 2000000;
            break;
        }
        case 2: {            
            metros = 5000000;
            break;
        }
        case 1: {            
            metros = 10000000;
            break;
        }
    }
    //alert(zoom + ":" + metros);
    return metros;

}

function cloneObject(obj) {
    var newObj = {};
    for ( prop in obj ){
        newObj[prop] = obj[prop];
    }
    
    return newObj;
}

Math.average = function( arrNumbers ) {
    
    var fAvg = 0, fSum = 0;
    
    if ( typeof arrNumbers == 'object' ) {
        for ( var n in arrNumbers ) {
            fSum += arrNumbers[n];
        }
        fAvg = fSum / arrNumbers.length;
    }
    
    return fAvg;
}
// Para evitar que Internet Explorer tire errores innecesarios
if ( typeof console == "undefined" )
{
    console = {
        "error": function(){ ; },
        "info" : function(){ ; },
        "log"  : function(){ ; },
        "warn" : function(){ ; }
    };
}

debug = {
    "info": function(mixed)
        {
            if ( g_bDebugMode )
            {
                console.info(mixed);
            }
        },
    "log": function(mixed)
        {
            if ( g_bDebugMode )
            {
                console.log(mixed);
            }
        },

    "warn": function(mixed)
        {
            if ( g_bDebugMode )
            {
                console.warn(mixed);
            }
        },

    "error": function(mixed)
        {
            if ( g_bDebugMode )
            {
                console.error(mixed);
            }
        }
};

function getDateTime()
{
	var dt = new Date();
	var str = "";

	dd   = dt.getDate();
	dd   = ( dd < 10 ? "0" : "" ) + dd;

	mm   = dt.getMonth() + 1;
	mm   = ( mm < 10 ? "0" : "" ) + mm;

	yyyy = dt.getFullYear();

	h = dt.getHours();
	h   = ( h < 10 ? "0" : "" ) + h;

	m = dt.getMinutes();
	m   = ( m < 10 ? "0" : "" ) + m;

	s = dt.getSeconds();
	s   = ( s < 10 ? "0" : "" ) + s;

	str = dd + "/" + mm + "/" + yyyy + " " + h + ":" + m + ":" + s;

	return str;
}

function validateSession(resp){
	/* VALIDAR EXPIRE DE SESSION  include/validarSession.php*/
	if(resp != null){
		if(resp.status == 3){
			document.location.href = dominio;
		}
	}
	/* */
}

function setSolapas(solapa){
	$('.solapas a').removeClass('active');
	$('.solapas .contenido-solapa').hide();
	
	$('#listado-'+solapa).show();
	$('.solapas a#solapa-'+solapa).addClass('active');
}

//--- AJAX ---//
function getFecha(){
	$.ajax({
		type: "POST",
		url: dominio+"ajax.php",
		data:({
			accion:'get-fechaHora-server'
		}),
		success: function(msg){ 
			if(msg.indexOf('status') > 0){
				//-- expiro session;	
			}
			else{
				$('.footer #dia-semana').html(msg);
			}
		}	
	});	
}

function getProvincia(ide, id_pais, id_provincia){
	$('#'+ide).find('option:not(:first)').remove();
	
	$.ajax({
		type:"POST"
		,url:"ajax.php"
		,dataType:"json"
		,data:({accion:"get-provincia",id_pais:id_pais})
		,success:function(c){
			if(c != null){
				for(i=0; i<c.length; i++){
					var selected = '';
					if(id_provincia == c[i].pr_id){
						selected = 'selected = "selected"';	
					}
					$('#'+ide).append('<option value="'+c[i].pr_id+'" '+selected+'>'+c[i].pr_nombre+'</option>');
				}
			}
		}
	});
}

function getLocalidad(ide, id_provincia, id_localidad){
	$('#'+ide).find('option:not(:first)').remove();
	
	$.ajax({
		type:"POST"
		,url:"ajax.php"
		,dataType:"json"
		,data:({accion:"get-localidad",id_provincia:id_provincia})
		,success:function(c){
			if(c != null){
				for(i=0; i<c.length; i++){
					var selected = '';
					if(id_localidad == c[i].lo_id){
						selected = 'selected = "selected"';	
					}
					$('#'+ide).append('<option value="'+c[i].lo_id+'" '+selected+'>'+c[i].lo_nombre+'</option>');
				}
			}
		}
	});
}

function getClientesPorTipoEmpresa(ide, value, cl_id){
	$('#'+ide).find('option:not(:first)').remove();
	
	$.ajax({
		type:"POST"
		,url:"ajax.php"
		,dataType:"json"
		,data:({accion:"get-clientes-por-tipoEmpresa",cl_tipo:value})
		,success:function(c){
			if(c != null){
				for(i=0; i<c.length; i++){
					var selected = '';
					if(cl_id == c[i].cl_id){
						selected = 'selected = "selected"';	
					}
					$('#'+ide).append('<option value="'+c[i].cl_id+'" '+selected+'>'+c[i].cl_razonSocial+'</option>');
				}
			}
		}
	});
}

function getPerfiles(ide, value, id_perfil_select){
	$('#'+ide).find('option:not(:first)').remove();
	
	$.ajax({
		type:"POST"
		,url:"ajax.php"
		,dataType:"json"
		,data:({accion:"get-perfiles",id_perfil:value})
		,success:function(c){
			if(c != null){
				for(i=0; i<c.length; i++){
					var selected = '';
					if(id_perfil_select == c[i].pe_id){
						selected = 'selected = "selected"';	
					}
					$('#'+ide).append('<option value="'+c[i].pe_id+'" '+selected+'>'+c[i].pe_nombre+'</option>');
				}
			}
		}
	});
}

function getMoviles(ide, idEmpresa){
	$('#'+ide).find('option:not(:first)').remove();
		
	if(typeof(ajaxMoviles) != 'undefined'){
		ajaxMoviles.abort();
	}	
			
	ajaxMoviles = $.ajax({
		type: "POST",
		url: "ajax.php",
		dataType: "json",
		data:({
			accion:'get-moviles',
			idDistribuidor:idEmpresa
		}),
		success: function(data){
			if(data != null){
				for(i=0; i<data.length; i++){
					$('#'+ide).append('<option value="'+data[i].mo_id+'">'+data[i].movil+'</option>');
				}
			}
		}
	});
}
//--- ---//

function SendMailResetPass(mail, cliente, ide, wait = false){
	$('#'+ide).hide();
	$('<img src="imagenes/ajax-loader.gif" id="enviando-'+ide+'" />').insertAfter('a#'+ide);
	$.ajax({
		url: dominio+'ajaxOlvidoContrasena.php',
		dataType: "json",
		type: "POST",
		async: true,
		cache: false,
		data: {
			"action": 'enviar_mail',
			"mail": mail,
			'config':cliente,
			'ignorar_reset_previo':true,
			'wait':wait 
		},
		success: function(msg) {
			if(msg.ok){
				alert('Se ha enviado un correo a: '+mail+' con la informaci\u00f3n para el cambio de clave.');
			}
			else{
				alert(msg.error);
			}
			
			$('#enviando-'+ide).remove();
			$('#'+ide).show();
		}
	});
}

function CreateAccount(idcliente, ide){
	$('#'+ide).hide();
	$('<img src="imagenes/ajax-loader.gif" id="enviando-'+ide+'" />').insertAfter('a#'+ide);
	$.ajax({
		url: dominio+'ajaxOlvidoContrasena.php',
		dataType: "json",
		type: "POST",
		async: true,
		cache: false,
		data: {
			"action": 'create_account',
			"idcliente": idcliente
		},
		success: function(iduser) {
			$('#enviando-'+ide).remove();
			//$('#'+ide).show();
		}
	});
}