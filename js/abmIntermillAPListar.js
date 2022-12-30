var seccion = 'abmIntermillAP';
var campoId = 'vi_id',
columnas = [
{
		id:'Vehiculo',
		width:200,
		filtro:1,
		label:'Veh\u00edculo / Conductor (Transportista) (C\u00f3digo)',
		link_modificar:false
	},{
		id:'NombreCorto',
		width:200,
		filtro:1,
		label:'Lugar',
		link_modificar:false
	},{
		id:'FechaIngreso',
		width:150,
		filtro:1,
		label:'Ingreso Real',
		link_modificar:false
	},{
		id:'FechaProgramada',
		width:150,
		filtro:1,
		label:'Ingreso estimado (Turno Asignado)',
		link_modificar:false
	},{
		id:'Estadia',
		width:100,
		filtro:1,
		label:'Estad\u00eda',
		link_modificar:false
	}
];

/*
1)	Vehiculo.
2)	Lugar.
3)	Fecha de ingreso Real.
4)	Fecha de ingreso Programado. 
5)	Fecha de ingreso Estimado.
*/

var columnasP = [
{
		id:'Vehiculo',
		width:200,
		filtro:1,
		label:'Veh\u00edculo / Conductor (Transportista)',
		link_modificar:false
	},{
		id:'NombreCorto',
		width:200,
		filtro:1,
		label:'Lugar',
		link_modificar:false
	},{
		id:'FechaIngreso',
		width:150,
		filtro:1,
		label:'Ingreso Real',
		link_modificar:false
	},{
		id:'FechaEgreso',
		width:150,
		filtro:1,
		label:'Egreso Real',
		link_modificar:false
	},{
		id:'Estadia',
		width:100,
		filtro:1,
		label:'Estad\u00eda',
		link_modificar:false
	}
];

function intermilap_onload() {
	$("#intermil-arribos-partidas").tabs({
		select: function(event, ui) {
			//var isValid = ... // form validation returning true or false
			//alert(event);alert(ui);
			//alert(ui.tab);     // anchor element of the selected (clicked) tab
			//alert(ui.panel);   // element, that contains the selected/clicked tab contents
			//alert(ui.index);   // zero-based index of the selected (clicked) tab
			//var $tabs = $("#tabs").tabs();
			//var selected = $tabs.tabs('option', 'selected'); // => 0
			//alert(selected);
			if (ui.index == 3) {
				return false;
			}
			return true;
		}
	});
	
	$('#datepicker').datepicker();
	$('#datepicker').change(function(){
		var params = '';
		params += 'inicio=' + $('#datepicker').val();
		$('#fc').text($(this).val())
		load(params);
	});
	
	$('#ui-datepicker-div').css('display','none');
	$('#txtDiaEmpieza').show();
	
	armarTabla(columnas,'A');
	tableData(columnas,'A');
	
	armarTabla(columnasP,'');
	tableData(columnasP,'');
	
	load('');
	timer();
}

$(document).ready(function(){
	intermilap_onload();
});

var total = 600;
function timer(){
	total--;
	var minutes = parseInt(total/60);
	var seconds = parseInt(total % 60);
	
	if (total == 0) {
		load('');
		total = 600;
	}
	
	if (seconds < 10) {
		seconds = "0"+seconds;
	}
	
	document.getElementById("intermil-timer").innerHTML= minutes + ":" + seconds;
	setTimeout("timer()",1000);
}

function mostrar(tipo) {
	if(tipo == 'grafico'){
		getGrafico();
	}
	
	$('.dataTables_wrapper, .dataTables_scroll,.dataTables_scrollHeadInner, .display').css('width','100%');
}


function load(params) {
	var table;
	var tbody;
	var tr;
	var td;
	var a;
	var url = "boot.php?c="+seccion+"&action=buscar&method=ajax_json&" + params;
	$.getJSON(
		url,
			function(data){
				if (data.msg==='ok'){
					armarTabla(columnas,'A');
					$.each(data.arribos, function(i,x){
						render(i,x, columnas,'A')
					})
					tableData(columnas,'A');
                    
                    armarTabla(columnasP,'');
					$.each(data.partidas, function(i,x){
						render(i,x, columnasP,'')
					})
					tableData(columnasP,'');
                    
				} else {
					armarTabla(columnas,'A');
					tableData(columnas,'A');
                    
                    armarTabla(columnasP,'P');
                    tableData(columnasP,'P');
				}
			}
		);
}
function armarTabla(cols,ext) {
        
	var table = $('<table>');
	table.attr('id','listadoRegistros' + ext);
	table.attr('cellspacing','0');
	table.attr('cellpadding','0');
	table.attr('border','0');
	table.addClass('display');
	
	var thead = $('<thead>');
	var tbody = $('<tbody>');
	tbody.attr('id','resultTable' + ext);
	var tfoot = $('<tfoot>');
	
	$('#mainBoxLI' + ext).text('');
	var trHead = $('<tr>');
	trHead.addClass('tituloListado');
	var trBody = $('<tr>');
	var trFoot = $('<tr>');
	
	$.each(cols,function(c,col){
		
		var tdH = $('<td>');
		tdH.text(col.label);
		tdH.appendTo(trHead);

		var tdB = $('<td>');
		tdB.appendTo(trBody);

		var tdF = $('<td>');
		var input = $('<input type="text">');
		input.addClass('search_init');
		input.css('width','60px');
		input.attr('id','search' + c)
		input.appendTo(tdF)
		tdF.appendTo(trFoot);
		
	});
	
	trHead.appendTo(thead);
	trBody.appendTo(tbody);
	trFoot.appendTo(tfoot);
	
	thead.appendTo(table);
	tbody.appendTo(table);
	tfoot.appendTo(table);
	
	table.appendTo($('#mainBoxLI' + ext))
	$('#resultTable' + ext).children( 'tr:first' ).remove();
	
}
var strBusqueda;
function tableData(columnas, ext) {
	var asInitVals = new Array();
	var oTable = $('#listadoRegistros' + ext).dataTable( {
		"sScrollY": 300,
		"sScrollCollapse": true,
		"bStateSave": true,
		 "oLanguage": {
            "sLengthMenu": "Mostrar _MENU_ registros por pagina",
            "sZeroRecords": "No se encontraron registros",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
			"sSearch": "",
            "sInfoFiltered": "",
			"oPaginate": {
                "sFirst": "<<",
				"sLast": ">>",
				"sNext": ">",
				"sPrevious": "<" 
            }
        },
		"aaSorting": [[ 2, "desc" ]],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	} );
	new AutoFill( oTable );
	$("tfoot input").keyup( function () {
		oTable.fnFilter( this.value, $("tfoot input").index(this) );
	} );
	 
	$("tfoot input").each( function (i) {
		asInitVals[i] = this.value;
	} );
	 
	$("tfoot input").focus( function () {
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	} );
	 
	$("tfoot input").blur( function (i) {
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = asInitVals[$("tfoot input").index(this)];
		}
	} );
	$("tfoot").css('display','none');
	
	$('.search_init').css('margin-left','5px')
	var allInputs = $("input[type=text]");
	
	//return false;
	var allLabels = $(".DataTables_sort_wrapper");
	strBusqueda = '';
	$.each(allLabels,function(i,x) {
		var aux = $(this).html().split("<");
			strBusqueda += (strBusqueda) ? ', ' + aux[0] : aux[0];
	});
	
	$.each(allInputs,function(i,x) {
		if ($(this).attr('name') === undefined && $(this).attr('id') === undefined) {
			$(this).attr('id', 'txtFiltro'+ext);
			$(this).attr('size','60');
		}
	});
	strBusqueda = 'Buscar por ' + strBusqueda;
	
	procesarTipsFiltro(['txtFiltro'+ext],strBusqueda);
}

function render(i,data,cols,ext) {
	var timestamp = parseInt(new Date().getTime()/1000);
	//alert(timestamp);

	var tb = $('#resultTable' + ext);
	tr = $('<tr>');
    var div = $('<div>');
	$.each(cols,function(c,col){
        td = $('<td>');
        td.css('width',cols[c].width + 'px')
        
        if (cols[c].id == 'FechaProgramada' && data[cols[c].id]) {
                if (data['FechaEstimada']) {
					div = $('<div>');
					div.css('font-size','10pt')
					div.css('font-weight','bold')
					div.text(data['FechaEstimada']);
					if (data['NoReportando'] == 1) {
						div.css('color', 'red');
					}
					td.append(div);
					div = $('<div>');
					div.css('font-style','italic')
					div.text('('+data[cols[c].id]+')');
					td.append(div);
                } else {
					div = $('<div>');
					div.css('font-size','10pt')
					div.css('font-weight','bold')
					div.text(data[cols[c].id]);
					td.append(div);
				}
        } else if (cols[c].id == 'NombreCorto' && data['EsCruce'] == 1) {
                div = $('<div>');
                div.css('font-size','10pt')
				div.css('font-weight','bold')
				var nombrecorto = data[cols[c].id];
				if (data['Distancia'] != -1) {
					nombrecorto = nombrecorto + " " + data['Distancia'];
				}
                div.text(nombrecorto);
                td.append(div);
        } else if (cols[c].id == 'NombreCorto' && data['Procedencia']) {
                div = $('<div>');
                div.css('font-size','10pt')
				div.css('font-weight','bold')
                div.text(data[cols[c].id]);
                td.append(div);
                div = $('<div>');
                div.css('font-style','italic')
                div.text('Procedencia: '+data['Procedencia']+'');
                td.append(div);  
                if (data['FechaEgresoFormato']) {
					div = $('<div>');
					div.css('font-style','italic')
					div.text('('+data['FechaEgresoFormato']+')');
					td.append(div);  
				}
        } else if (cols[c].id == 'Estadia' && data['Estadia']) {
                div = $('<div>');
                div.css('font-size','10pt')
				//div.css('font-weight','bold')
                div.text(data[cols[c].id]);
                td.append(div);
		} else if (cols[c].id == 'Vehiculo' && data['Link']) {
				var a = $('<a>');
				a.attr('href', data['Link'])
				a.attr('target', '_blank')
				a.text(data[cols[c].id]);
				td.append(a);
        } else if (ext == 'A') {
                if (cols[c].id == 'FechaIngreso') {
                        div = $('<div>');
                        div.css('font-size','10pt')
                        div.css('font-weight','bold')
                        div.text(data[cols[c].id]);
                        td.append(div);
                } else {
					if (cols[c].id == 'Vehiculo') {
						if (data['Conductor'] != undefined && data['Conductor'].length > 1 && data['Conductor'] != data[cols[c].id]) {
							td.text(data[cols[c].id] + " / " + data['Conductor']);
						} else {
							td.text(data[cols[c].id] + " / Sin conductor");
						}
					} else {
						td.text(data[cols[c].id]);
					}
                }
                
                if (cols[c].id == 'Vehiculo')
                {
					if (data['RazonSocial'] != undefined) {
						div = $('<div>');
						div.css('font-style','italic');
						div.text("("+data['RazonSocial']+")");
						td.append(div);
					}
					
					if (data['EsCruce'] == 1 || data['EsCruce'] == 2)
					{
						div = $('<div>');
						div.css('font-style','italic')
						if (data['EsCruce'] == 2) {
							div.text("(Cod. "+data['Nombre']+")");
						} else {
							div.text("(Cod. "+data['Nombre']+")");
						}
						td.append(div);
					}
                }
        } else if (ext == '') {
                if (cols[c].id == 'FechaEgreso' || (cols[c].id == 'FechaIngreso' && !data['FechaEgreso'])) {
                        div = $('<div>');
                        div.css('font-size','10pt');
                        div.css('font-weight','bold');
                        div.text(data[cols[c].id]);
                        td.append(div);
                } else {
                        //td.text(data[cols[c].id]);   
					if (cols[c].id == 'Vehiculo') {
						if (data['Conductor'] != undefined && data['Conductor'].length > 1 && data['Conductor'] != data[cols[c].id]) {
							td.text(data[cols[c].id] + " / " + data['Conductor']);
						} else {
							td.text(data[cols[c].id] + " / Sin conductor");
						}
					} else {
						td.text(data[cols[c].id]);
					}                    
                }
                if (cols[c].id == 'Vehiculo')
                {
					if (data['RazonSocial'] != undefined) {
						div = $('<div>');
						div.css('font-style','italic');
						div.text("("+data['RazonSocial']+")");
						td.append(div);
					}
				}
        } else {
                td.text(data[cols[c].id]);   
        }
        
        if (cols[c].id == 'NombreCorto' && ext == 'A') {
			if (data['Observaciones']) {
				br = $('<br>');
				div = $('<abbr>');
				div.css('font-style','italic')
				var maxT = 50;
				if (data['Observaciones'].length > maxT) {
					div.text(data['Observaciones'].substr(0, maxT) + ' ...');
				} else {
					div.text(data['Observaciones'].substr(0, maxT));
				}
				div.attr('title', data['Observaciones']);
				td.append(br);  
				td.append(div);  
			}
        }
        
        td.appendTo(tr);
	});
	tr.appendTo(tb);
	
}
function generarViaje() {
	var idMovil = $('#hidIdMovilConf').val();
	mostrarPopup('boot.php?c=abmCruces&action=popup&idMovil=' + idMovil + '&popup=true');
}

function getGrafico(){
	var url = "boot.php?c="+seccion+"&action=grafico&method=ajax_json&"; //+ params;
	$.getJSON(
		url,
			function(data){
				$('div#intermil-graficos div#mainBoxLI').html(data.grafico);
				
				//-- Lineas para definir el alto del div contenido la imagen en base a la resolucion --//
				height = $(window).height();
				$("#cuerpoGrafico").css('height', parseInt(height) - 185);
			}
		);
}