var seccion = 'abmCruces';
var campoId = 'vi_id',
/*{
		id:'vi_id',
		width:20,
		filtro:1,
		label:'N',
		link_modificar:true
	},*/
columnas = [
	{
		id:'vi_codigo',
		width:35,
		filtro:1,
		label:'Código',
		link_modificar:true
	},
	/*{
		id:'us_nombreUsuario',
		width:120,
		filtro:1,
		label:'Usuario',
		link_modificar:true
	},*/{
		id:'vi_mo_id',
		width:35,
		filtro:1,
		label:'Vehiculo / Conductor',
		link_modificar:false
	},{
		id:'vd_re_id',
		width:120,
		filtro:1,
		label:'Destino',
		link_modificar:true
	},{
		id:'vd_ini',
		width:120,
		filtro:1,
		label:'Ingreso Programado / Real',
		link_modificar:false
	},{
		id:'vd_fin',
		width:120,
		filtro:1,
		label:'Egreso Programado / Real',
		link_modificar:false
	}
];
var diaActual;
function dia(suma) {
	var fecha = $('#fc').text();
	var aux = fecha.split("/")
	var day = aux[0];
	var month = aux[1]-1;
	var year = aux[2];
	var myDate=new Date(year, month, day);
	if (suma == 1) myDate.setDate(myDate.getDate()+1);
	else myDate.setDate(myDate.getDate()-1);
	
	fecha  = (myDate.getDate() < 10 ? '0' + myDate.getDate() : myDate.getDate()) + '/'
	fecha += (myDate.getMonth()+1 < 10 ? '0' + (myDate.getMonth()+1) : (myDate.getMonth()+1)) + '/'
	fecha += myDate.getFullYear()
	$('#fc').text(fecha);
	//alert(myDate)
	//alert(myDate.getDate())
	
	$('#datepicker').val(fecha);
	$('#datepicker').trigger('change');
	//new Date(year, month, day, hours, minutes, seconds, milliseconds)
}

var cale = false;
function mostrarCalendar() {
        if (cale) return false;
        cale = true;
        $('#calendar').fullCalendar({
                header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                },
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio',
                        'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort:['Ene','Feb','Mar','Abr','May','Jun','Jul.','Ago','Sep','Oct','Nov','Dic'],
                dayNames: ['Domingo','Lunes','MArtes','Miercoles','Jueves','Viernes','Sabado'],
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                editable: false,
                buttonText: {
                        today: 'Hoy',
                        day: 'dia',
                        week:'semana',
                        month:'mes'
                    },
                titleFormat: {
                        month: 'MMMM yyyy',
                        week: "d[ MMMM][ yyyy]{ - d MMMM yyyy}",
                        day: 'dddd d MMMM yyyy'
                },
                columnFormat: {
                        month: 'ddd',
                        week: 'ddd d',
                        day: ''
                },
                events: "boot.php?c=abmCruces&action=calendar",
                eventDrop: function(event, delta) {
                },
                loading: function(bool) {
                        if (bool) $('#loading').show();
                        else $('#loading').hide();
                }
		});          
}
$(document).ready(function(){
        $("#intermil-viajes-tabs").tabs();
        $('#datepicker').datepicker();
        $('#datepicker').change(function(){
            var params = '';
            params += 'inicio=' + $('#datepicker').val();
            $('#fc').text( $(this).val() );
            load(params);
        });
        $('#ui-datepicker-div').css('display','none');
        $('#txtDiaEmpieza').show();
        
        $('#txtDiaEmpieza').change(function() {
            
        });
        $('#btnNewViaje').click(function(){
                mostrarPopup('boot.php?c=abmCruces&action=popup');
        });
        armarTabla();
        tableData();

        insertar_boton_exportacion_periodo();

        load('');
});

function insertar_boton_exportacion_periodo()
{
    /* BEGIN Inserto boton de Exportacion de Periodo */

    var $oContainer = $(".DTTT_container.ui-buttonset.ui-buttonset-multi", $("#listadoRegistros_wrapper"));
    var $oButton = $("<button>");

    $oButton.addClass("DTTT_button");
    $oButton.addClass("DTTT_button_collection");
    $oButton.addClass("ui-button");
    $oButton.addClass("ui-state-default");

    $oButton.bind("mouseenter", function()
    {
    	$(this).addClass("DTTT_button_collection_hover");
    	$(this).addClass("ui-state-hover");
    });

    $oButton.bind("mouseleave", function()
    {
    	$(this).removeClass("DTTT_button_collection_hover");
    	$(this).removeClass("ui-state-hover");
    });

    $oButton.html("<span>Cumplimiento de Transportistas (60d)</span>");

    $oButton.bind("click", function(ev)
    {
    	location.href = "javascript:enviar('export_cumplimientoTransportistas');";
    	return false;
    });

    $oContainer.append( $oButton );
    /* END Inserto boton de Exportacion de Periodo */
}

function mostrar() {
	$('.dataTables_scrollHeadInner, .display').css('width','100%');
}

var fechaSeleccionada = null;

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
					armarTabla();
					fechaSeleccionada = data.fecha;
					$.each(data.result, function(i,x){
						render(i,x)
					})
					tableData();
				} else {
					armarTabla();
					tableData();
				}
				insertar_boton_exportacion_periodo();
			}
		);
}

function armarTabla() {
	var table = $('<table>');
	table.attr('id','listadoRegistros');
	table.attr('cellspacing','0');
	table.attr('cellpadding','0');
	table.attr('border','0');
	table.addClass('display');
	
	var thead = $('<thead>');
	var tbody = $('<tbody>');
	tbody.attr('id','resultTable');
	var tfoot = $('<tfoot>');
	
	$('#mainBoxLI').text('');
	//$('#mainBoxLI').remove();
	//THEAD
	var trHead = $('<tr>');
		trHead.addClass('tituloListado');
	var trBody = $('<tr>');
	var trFoot = $('<tr>');
		//trFoot.attr('id','tituloListado');
	
	$.each(columnas,function(c,col){
		
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
	
	table.appendTo($('#mainBoxLI'))
	$('#resultTable').children( 'tr:first' ).remove();
	
}
var strBusqueda;

function tableData() {
	var asInitVals = new Array();
	var oTable = $('#listadoRegistros').dataTable( {
		"sScrollY": 250,
		"sScrollCollapse": false,
		"bStateSave": true,
		"oTableTools": {
		"sSwfPath": "http://datatables.net/release-datatables/extras/TableTools/media/swf/copy_cvs_xls_pdf.swf",
		"aButtons": [
				{
					"sExtends":    "collection",
					"sButtonText": "Exportar",
					"aButtons": [
						{
							"sExtends": "xls",
							"sButtonText": "CSV"
						},
						{
							"sExtends": "pdf",
							"sButtonText": "PDF"
						},
						{
							"sExtends": "print",
							"sButtonText": "Imprimir"
						}
					]
				}
			]
		},
			

		"sDom": '<"H"Tfr>t<"F"ip>', //'<"H"Tfr>t<"F"ip>',
		"oLanguage": {
            "sLengthMenu": "Mostrar _MENU_ registros por pagina",
            "sZeroRecords": "No se encontraron registros",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
			"sSearch": "",

            "sInfoFiltered": "(filtrados de _MAX_ registros totales)",
			"oPaginate": {
                "sFirst": "<<",
				"sLast": ">>",
				"sNext": ">",
				"sPrevious": "<" 
            }
        },
		"aaSorting": [[ 5, "asc" ]],
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
	
	//$('#listadoRegistros').css('height', '250px');
	//$('#listadoRegistros').css('width', '200px');
	
	var allLabels = $(".DataTables_sort_wrapper");
	//var strBusqueda = '';
	strBusqueda = '';
	$.each(allLabels,function(i,x) {
		var aux = $(this).html().split("<");
		//if ($('#search' + i).attr('width') == 60)
			strBusqueda += (strBusqueda) ? ', ' + aux[0] : aux[0];
	});
	
	$.each(allInputs,function(i,x) {
		if ($(this).attr('name') === undefined && $(this).attr('id') === undefined) {
			$(this).attr('id', 'txtFiltro');
			$(this).attr('size','60');
		}
	});
	//$('#txtFiltro').attr('value',strBusqueda);
	strBusqueda = 'Buscar por ' + strBusqueda;
	//R-> procesarTipsFiltro(['txtFiltro'],strBusqueda);
	
	
	$('#ToolTables_listadoRegistros_0').hide();
}

function render(i,data) {
	
	var tb = $('#resultTable');
	tr = $('<tr>');
	//var cId = data[columnas[0].id]
	var cId = data['vi_id'];	
	
	chk='<input name="chkId[]" type="checkbox"/>';
	if ($.browser.msie){
		chk=document.createElement(chk);
	}
	chk=$(chk).attr('checked',false);
		
	input=chk.clone().attr({id:'chk_'+cId,value:cId});
	td = $('<td>');
	//span.text(data[columnas[0].id]);
	var span = $('<span>');
	if (data['editable'] == 1) {
		var a = $('<a style="color:blue" href="javascript:enviarModificacion(\'modificar\','+cId+')"/>');
		a.text(data[columnas[0].id]);
		span.append(a);
	} else {
		span.text(data[columnas[0].id]);
	}

	td.attr('width','15px').append(input).append(span).appendTo(tr);
	
	$.each(columnas,function(c,col){
		if (c > 0) {
			td = $('<td>');//3
			td.css('width',columnas[c].width + 'px')
			if (col.link_modificar && data['editable'] == 1){
				var a = $('<a href="javascript:enviarModificacion(\'modificar\','+cId+')"/>');
				if (columnas[c].id == 'vi_codigo') {
					a.text(data[columnas[c].id] + ' (' + data['vd_orden'] + ')');
				} else {
					a.text(data[columnas[c].id]);	
				}
				td.append(a);
			}
			else if(columnas[c].id == 'estado_ini')
			{
					if (data['diferenciaIngreso']) {
						if (data['diferenciaIngreso'] <= 0) {
							data['estadoIngreso'] = 'Arribo en Tiempo';
						} else {
							data['estadoIngreso'] = 'Arribo Atrasado';
						}
						td.text(data['estadoIngreso']);
					} else {
						td.text(' ');
					}
			} else if(columnas[c].id == 'estado_fin'){
					if (data['diferenciaEgreso']) {
						if (data['diferenciaEgreso'] <= 0) {
							data['estadoEgreso'] = 'Partio en Tiempo';
						} else {
							data['estadoEgreso'] = 'Partio Atrasado';
						}
						td.text(data['estadoEgreso']);
					} else {
						td.text(' ');
					}
			} else if(columnas[c].id == 'vd_ini') {
					var div1 = $('<div>');
					//div1.text(data['vd_ini'])
					div1.css('font-size','10pt');
					div1.css('font-weight','bold');
					
					var fecha = data['vd_ini'];
					if (fecha.substr(0,10) == fechaSeleccionada) {
						fecha = fecha.substr(11,5);
					}
					div1.text(fecha);
					td.append(div1);
					
					var div1 = $('<div>');
					div1.text(data['vd_ini_real']);
					td.append(div1);
					
					if (data['diferenciaIngreso']) {
						if (data['diferenciaIngreso'] <= 0) {
							data['estadoIngreso'] = 'Arribo en Tiempo';
						} else {
							data['estadoIngreso'] = 'Arribo Atrasado';
						}
						var div1 = $('<div>');
						div1.text('(' + data['estadoIngreso'] + ')');
						td.append(div1);
					} 		
			}
			else if(columnas[c].id == 'vd_fin') {
					var div1 = $('<div>');
					//div1.text(data['vd_fin'])
					div1.css('font-size','10pt')
					div1.css('font-weight','bold')
					
					var fechaFin = data['vd_fin'];
					if (fechaFin.substr(0,10) == fechaSeleccionada) {
						fechaFin = fechaFin.substr(11,5);
					} else {
						fechaFin = fechaFin.substr(0,5) + " " + fechaFin.substr(11,5);
					}
					div1.text(fechaFin);
					td.append(div1);
					
					var div1 = $('<div>');
					div1.text(data['vd_fin_real'])
					td.append(div1);
					
					if (data['diferenciaEgreso']) {
						if (data['diferenciaEgreso'] <= 0) {
							data['estadoEgreso'] = 'Partio en Tiempo';
						} else {
							data['estadoEgreso'] = 'Partio Atrasado';
						}
						var div1 = $('<div>');
						div1.text('(' + data['estadoEgreso'] + ')')
						td.append(div1);
					} 
					
			}
			else if(columnas[c].id == 'vi_mo_id') 
			{
				if (data['mo_otros'] != undefined && data['mo_otros'] != "") {
					td.text(data[columnas[c].id] + " / " + data['mo_otros']);
				} else {
					td.text(data[columnas[c].id]);
				}
			}
			else
			{
				td.text(data[columnas[c].id]);
			}
			/*
			var estado = 0;
	
	
			
			*/
			td.appendTo(tr);
		}
	});
	tr.appendTo(tb);
	
}
