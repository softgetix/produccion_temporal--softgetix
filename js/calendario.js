var requiredCombo = false;

$(document).ready(function(){
	<!-- BUSQUEDA simple -->
		<!-- click calendar preview -->
		$("#busqueda-simple .calendario.prev").click(function(){
			calendarPrev('#mes-desde', '#anio-desde', '#busqueda-simple',$('#fecha').val());
		});
		
		<!-- click calendar next -->
		$("#busqueda-simple .calendario.next").click(function(){
			calendarNext('#mes-desde', '#anio-desde', '#busqueda-simple',$('#fecha').val());
		});
	<!-- fIN BUSQUEDA simple -->
	
	<!-- BUSQUEDA avanzada -->
		<!-- click calendar desde preview -->
		$("#busqueda-avanzada .calendar-desde .calendario.prev").click(function(){
			calendarPrev('#mes-desde', '#anio-desde', '#busqueda-avanzada .calendar-desde',$('#fecha_desde').val());
		});
		
		<!-- click calendar desde next -->
		$("#busqueda-avanzada .calendar-desde .calendario.next").click(function(){
			calendarNext('#mes-desde', '#anio-desde', '#busqueda-avanzada .calendar-desde',$('#fecha_desde').val());
		});
		
		<!-- click calendar hasta preview -->
		$("#busqueda-avanzada .calendar-hasta .calendario.prev").click(function(){
			calendarPrev('#mes-hasta', '#anio-hasta', '#busqueda-avanzada .calendar-hasta', $('#fecha_hasta').val());
		});
		
		<!-- click calendar hasta next -->
		$("#busqueda-avanzada .calendar-hasta .calendario.next").click(function(){
			calendarNext('#mes-hasta', '#anio-hasta', '#busqueda-avanzada .calendar-hasta',$('#fecha_hasta').val());
		});
	<!-- fIN BUSQUEDA avanzada -->
});

function calendarPrev(idMes, idAnio, idCalendar,activo){
	var mes = $(idMes).val();
	var anio = $(idAnio).val();
	mes = parseInt(mes) - 1;
		
	if(mes < 1){
		mes = 12;
		anio = parseInt(anio) - 1;
	}
		
	$(idMes).val(mes);
	$(idAnio).val(anio);
	
	getCalendario(idCalendar, mes, anio,activo);	
}

function calendarNext(idMes, idAnio, idCalendar,activo){
	var mes = $(idMes).val();
	var anio = $(idAnio).val();
	mes = parseInt(mes) + 1;
	
	if(mes > 12){
		mes = 1;
		anio = parseInt(anio) + 1;
	}
		
	$(idMes).val(mes);
	$(idAnio).val(anio);
	
	getCalendario(idCalendar, mes, anio,activo);	
}

var ajaxCalendar;
var idCalendar;
function getCalendario(id, mes, anio,activo){
	var accion = 'get-calendario';
	if(typeof(actionCalendar) != 'undefined'){
		accion = actionCalendar;	
	}
	
	$(id+' #mes-calendario table.calendar').addClass('loader');
	$(id+' #mes-calendario table.calendar a').remove();
	
	if(typeof(ajaxCalendar) != 'undefined'){
		if(idCalendar == id){
			ajaxCalendar.abort();
		}
	}
	idCalendar = id;
	ajaxCalendar = $.ajax({
		type: "POST",
		url: "ajax.php",
		data:({
			  	accion:accion,
			  	mes:mes,
				anio:anio,
				activo:activo,
				ide:String(id).replace('#','')
			}),
		success: function(msg){
			$calendario = jQuery.parseJSON(msg)
			
			$(id+' #mes-calendario').html($calendario.calendar);
			$(id+' #mes-calendario table.calendar').removeClass('loader');
			
			if(id == '#busqueda-avanzada .calendar-desde' && requiredCombo == true){
				var anioDesde = $('#anio-desde').val()?$('#anio-desde').val():anio;
				var contMeses = $calendario.meses+'<select name="anio_desde" class="float_l anio" onChange="javascript:changeYear(this.value,\'desde\')">'+getOptionYear(anioDesde)+'</select>'; 	
			}
			else if(id == '#busqueda-avanzada .calendar-hasta' && requiredCombo == true){
				var anioHasta = $('#anio-hasta').val()?$('#anio-hasta').val():anio;
				var contMeses = $calendario.meses+'<select name="anio_hasta" class="float_l anio" onChange="javascript:changeYear(this.value,\'hasta\')">'+getOptionYear(anioHasta)+'</option></select>'; 
			}
			else{
				var contMeses = $calendario.meses+' '+anio; 
			}
			$(id+' .calendario.mes-actual').html(contMeses);
		}	
	});
}

function getOptionYear(anio){
	var fecha = new Date();
	
	var option = '';
	for(i=fecha.getFullYear(); i>=(2012); i-- ){
		if(anio == i){
			option+= '<option value="'+i+'" selected="selected">'+i+'</option>';
			}
		else{
			option+= '<option value="'+i+'">'+i+'</option>';
		}	
	}
	return option;
}

function changeYear(year, nroCalendar){
	if(nroCalendar == 'desde'){
		getCalendario('#busqueda-avanzada .calendar-desde', $('#mes-desde').val(), year, $('#fecha_desde').val());
		$('#anio-desde').val(year);
	}
	if(nroCalendar == 'hasta'){
		getCalendario('#busqueda-avanzada .calendar-hasta', $('#mes-hasta').val(), year, $('#fecha_desde').val());
		$('#anio-hasta').val(year);
	}
}

function resetDate(){
	var fecha = new Date();
	$('#mes-desde').val(parseInt(fecha.getMonth())+1);
	$('#anio-desde').val(fecha.getFullYear());
	$('#mes-hasta').val(parseInt(fecha.getMonth())+1);
	$('#anio-hasta').val(fecha.getFullYear());
}

/* Variables de Entorno 
var Tablebgcolor = "#AAAAAA";
var bgcolor = "#FFFFFF";
var Tdbgcolor = "#CCCCCC";
var DayNamebgcolor = "#EEEEEE";
var strMode;

var isIE = (document.all ? true : false);
var isDOM = (document.getElementById ? true : false);

var title;
var days;
var months;
var daysInMonth = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
var displayMonth;
var displayYear;
var displayDivName;
var displayElement;

function setMode(strModeToSet) {
	strMode = strModeToSet;
	if (strMode == 'ENG') {
		title = 'Calendar';
		days = new Array ("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
		months = new Array ("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
	}
	else {
		title = 'Calendario';
		days = new Array ("Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa");
		months = new Array ("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
	}

	return;
}

function getDays(month, year) {
	if (1 == month) {
		return ((0 == year % 4) && (0 != (year % 100))) ||
			(0 == year % 400) ? 29 : 28;
	}
	else {
		return daysInMonth[month];
	}
}

function getToday(varFecha) {
	if (varFecha != ""){
		
		// Saco el dia, mes y año
		var FechaDia = varFecha.split("/");
		
		if (FechaDia.length < 3) {
			varFecha = "";
		}
		else {
			if (FechaDia[2].length == 2) {
				if (FechaDia[2] > 05) {
					FechaDia[2] = "19" + FechaDia[2];
				}
				else {
					FechaDia[2] = "20" + FechaDia[2];
				}
			}
			
			varFechaReal = new Date(FechaDia[1] + "/" + FechaDia[0] + "/" + FechaDia[2]);
			var CampoDia = varFechaReal.getDate();
			var CampoMes = varFechaReal.getMonth();
			var CampoAnio = varFechaReal.getFullYear();
			
			if ((FechaDia[0] == CampoDia) 
				&& (FechaDia[1] == (CampoMes + 1))
				&& (FechaDia[2] == CampoAnio)) {
					this.year = CampoAnio;
					this.month = CampoMes;
					this.day = CampoDia;
			}
			else {
				varFecha = "";
			}
		}
	}
	
	if (varFecha == "") {
		// Generate today's date.
		this.now = new Date();
		this.year = this.now.getFullYear();
		this.month = this.now.getMonth();
		this.day = this.now.getDate();
	}
}

function newCalendar(eltName,attachedElement) {
	if (attachedElement) {
		if (displayDivName && displayDivName != eltName) {
			hideElement(displayDivName);
		}
		displayElement = attachedElement;
		
		// Start with a calendar for today.
		today = new getToday(displayElement.value);
		displayMonth = today.month;
		displayYear = today.year;
	}

	displayDivName = eltName;
	
	// Start with a calendar for today.
	today = new getToday(displayElement.value);
	var parseYear = parseInt(displayYear + '');
	var newCal = new Date(parseYear,displayMonth,1);
	var day = -1;
	var startDayOfWeek = newCal.getDay();
	if ((today.year == newCal.getFullYear()) &&
		(today.month == newCal.getMonth())) {
            day = today.day;
         }
	var intDaysInMonth =
		getDays(newCal.getMonth(), newCal.getFullYear());
	var daysGrid = makeDaysGrid(startDayOfWeek,day,intDaysInMonth,newCal,eltName);
	
  	if (isIE) {
		var elt = document.all[eltName];
		elt.innerHTML = daysGrid;
    }
	else {
		if (isDOM) {
     		var elt = document.getElementById(eltName);
			elt.innerHTML = daysGrid;
  		}
		else {
     		var elt = document.layers[eltName].document;
     		elt.open();
     		elt.write(daysGrid);
     		elt.close();
  		}
	}
}

function incMonth(delta,eltName) {
	displayMonth += delta;
	if (displayMonth >= 12) {
    	displayMonth = 0;
    	incYear(1,eltName);
  	}
  	else {
		if (displayMonth <= -1) {
    		displayMonth = 11;
    		incYear(-1,eltName);
  		}
		else {
			newCalendar(eltName);
  		}
	}
}

function incYear(delta,eltName) {
	displayYear = parseInt(displayYear + '') + delta;
	newCalendar(eltName);
}

function makeDaysGrid(startDay,day,intDaysInMonth,newCal,eltName) {
    var daysGrid;
    var month = newCal.getMonth();
    var year = newCal.getFullYear();
    var isThisYear = (year == new Date().getFullYear());
    var isThisMonth = (day > -1);

    daysGrid = '<table border="0" cellspacing="0" cellpadding="0" bgcolor="' + Tablebgcolor + '" width="180">';
    daysGrid += '<tr><td class="title" nowrap align="center" width="170">';
    daysGrid += '<img src="images/trans.gif" width="5" height="1" border="0"><span class="title">' + title + '</span></td><td class="title" align="right">';
    daysGrid += '<a href="javascript:hideElement(\'' + eltName + '\')"><img src="images/close.gif" width="10" height="9" alt="Cerrar" border="0"></a>';
    daysGrid += '</td></tr></table>';
	daysGrid += '<table border="0" cellspacing="1" cellpadding="0" bgcolor="' + Tablebgcolor + '" width="180">';
    daysGrid += '<tr><td bgcolor="' + Tdbgcolor + '" nowrap colspan="4" align="center">';
    daysGrid += '<a href="javascript:incMonth(-1,\'' + eltName + '\')">';
    daysGrid += '<img src="images/cal_menos.gif" width="9" height="9" border="0"></a>';

	daysGrid += '<span class="campos"';
    if (isThisMonth) {
		daysGrid += ' style="color: red;"';
	}
	
	daysGrid += '>&nbsp;' + months[month] + '&nbsp;</span>';
    daysGrid += '<a href="javascript:incMonth(1,\'' + eltName + '\')"><img src="images/cal_mas.gif" width="9" height="9" border="0"></a>';
    daysGrid += '</td>';
    daysGrid += '<td bgcolor="' + Tdbgcolor + '" nowrap colspan="3" align="center">';
    daysGrid += '<a href="javascript:incYear(-1,\'' + eltName + '\')"><img src="images/cal_menos.gif" width="9" height="9" border="0"></a>';

    if (isThisYear) {
		daysGrid += '<span class="campos" style="color: red;">&nbsp;' + year + '&nbsp;</span>';
	}
    else {
		daysGrid += '<span class="campos">&nbsp;' + year + '&nbsp;</span>';
	}

    daysGrid += '<a href="javascript:incYear(1,\'' + eltName + '\')"><img src="images/cal_mas.gif" width="9" height="9" border="0"></a>';
    daysGrid += '</td></tr><tr>';
    daysGrid += '<td align="center" bgcolor="' + DayNamebgcolor + '"><span class="campos">' + days[0] + '</span></td>';
    daysGrid += '<td align="center" bgcolor="' + DayNamebgcolor + '"><span class="campos">' + days[1] + '</span></td>';
    daysGrid += '<td align="center" bgcolor="' + DayNamebgcolor + '"><span class="campos">' + days[2] + '</span></td>';
    daysGrid += '<td align="center" bgcolor="' + DayNamebgcolor + '"><span class="campos">' + days[3] + '</span></td>';
    daysGrid += '<td align="center" bgcolor="' + DayNamebgcolor + '"><span class="campos">' + days[4] + '</span></td>';
    daysGrid += '<td align="center" bgcolor="' + DayNamebgcolor + '"><span class="campos">' + days[5] + '</span></td>';
    daysGrid += '<td align="center" bgcolor="' + DayNamebgcolor + '"><span class="campos">' + days[6] + '</span></td>';
    daysGrid += '</tr><tr>';

    var dayOfMonthOfFirstSunday = (7 - startDay + 1);

	for (var intWeek = 0; intWeek < 6; intWeek++) {
		var dayOfMonth;
		for (var intDay = 0; intDay < 7; intDay++) {
			dayOfMonth = (intWeek * 7) + intDay + dayOfMonthOfFirstSunday - 7;
			if (dayOfMonth <= 0) {
				daysGrid += '<td bgcolor="#FFFFFF"><span class="campos">&nbsp;</span></td>';
			}
			else {
				if (dayOfMonth <= intDaysInMonth) {
					var color = bgcolor;
					if (day > 0 && day == dayOfMonth) {
						color = "#CCCCCC";
					}
					daysGrid += '<td align="center" bgcolor="' + color + '"><a href="javascript:setDay(';
					daysGrid += dayOfMonth + ',\'' + eltName + '\')"><span class="campos">';
					var dayString = dayOfMonth + "</span></a></td> ";
					if (dayString.length == 6) {
						dayString = '0' + dayString;
					}
					daysGrid += dayString;
				}
				else {
					if (dayOfMonth > intDaysInMonth) {
						daysGrid += '<td bgcolor="#FFFFFF"><span class="campos">&nbsp;</span></td>';
					}
				}
			}
		}
		if (dayOfMonth < intDaysInMonth) {
			daysGrid += '</tr><tr>';
		}
		else {
			intWeek = 6;
		}
	}
	return daysGrid + "</tr></table>";
}

function setDay(day,eltName) {
	switch (strMode) {
		case 'ENG':
			displayElement.value = (displayMonth + 1) + "/" + day + "/" + displayYear;
			break;
		case 'ESP':
		default:
			displayElement.value = day + "/" + (displayMonth + 1) + "/" + displayYear;
			break;
	}
	hideElement(eltName);
}

function toggleDatePicker(eltName,formElt, strModeToSet) {
	var x = formElt.indexOf('.');
	var formName = formElt.substring(0,x);
	var formEltName = formElt.substring(x+1);

	setMode(strModeToSet);
	newCalendar(eltName,document.forms[formName].elements[formEltName]);
	toggleVisible(eltName);
}

function fixPosition(divname) {
	divstyle = getDivStyle(divname);
	positionerImgName = divname + 'Pos';

	isPlacedUnder = false;
	if (isPlacedUnder) {
		setPosition(divstyle,positionerImgName,true);
	}
	else {
		setPosition(divstyle,positionerImgName);
	}
}

function fixPositions(){
	fixPosition('daysOfMonth');
}

function Cancel() {
	hideElement("daysOfMonth");
}

// get the true offset of anything on NS4, IE4/5 & NS6, even if it's in a table!
function getAbsX(elt) {
	return (elt.x) ? elt.x : getAbsPos(elt,"Left");
}

function getAbsY(elt) {
	return (elt.y) ? elt.y : getAbsPos(elt,"Top");
}

function getAbsPos(elt,which) {
	iPos = 0;
	while (elt != null) {
		iPos += elt["offset" + which];
		elt = elt.offsetParent;
	}
	return iPos;
}

function getDivStyle(divname) {
	var style;
	if (isDOM) {
		style = document.getElementById(divname).style;
	}
	else {
		style = isIE ? document.all[divname].style : document.layers[divname]; // NS4
	}
 return style;
}

function hideElement(divname) {
	getDivStyle(divname).visibility = 'hidden';
}

// annoying detail: IE and NS6 store elt.top and elt.left as strings.
function moveBy(elt,deltaX,deltaY) {
	elt.left = parseInt(elt.left) + deltaX;
	elt.top = parseInt(elt.top) + deltaY;
}

function toggleVisible(divname) {
	divstyle = getDivStyle(divname);
	if (divstyle.visibility == 'visible' || divstyle.visibility == 'show') {
		divstyle.visibility = 'hidden';
	}
	else {
		fixPosition(divname);
		divstyle.visibility = 'visible';
	}
}

function setPosition(elt,positionername,isPlacedUnder) {
	var positioner;
	
	if (isIE) {
		positioner = document.all[positionername];
	}
	else {
		if (isDOM) {
			positioner = document.getElementById(positionername);
		}
		else {
		    // not IE, not DOM (probably NS4)
		    // if the positioner is inside a netscape4 layer this will *not* find it.
		    // I should write a finder function which will recurse through all layers
		    // until it finds the named image...
		    positioner = document.images[positionername];
		 }
	}
	
	elt.left = getAbsX(positioner);
	elt.top = getAbsY(positioner) + (isPlacedUnder ? positioner.height : 0);
}
*/