var seccion = 'abmIntermill';
var campoId = 'vi_id';
var columnas = [
{
    id:'NumeroOrden',
    width:500,
    filtro:1,
    label:'Codigo',
    link_modificar:false
},{
    id:'Vehiculo',
    width:40,
    filtro:1,
    label:'Vehículo /AA Conductor (Transportista)',
    link_modificar:false
},{
    id:'NombreCorto',
    width:100,
    filtro:1,
    label:'Lugar',
    link_modificar:false
}/*,{
    id:'FechaIngresoProgramado',
    width:100,
    filtro:1,
    label:'Ingreso Programado',
    link_modificar:false
},*/,{
    id:'FechaIngreso',
    width:70,
    filtro:1,
    label:'Ingreso Real',
    link_modificar:false
}/*,{
		id:'Ingreso',
		width:60,
		filtro:1,
		label:'Ingreso',
		link_modificar:false
	}*/
,{id:'FechaEgreso',
    width:70,
    filtro:1,
    label:'Egreso Real',
    link_modificar:false
}/*,{
    id:'FechaEgresoProgramado',
    width:100,
    filtro:1,
    label:'Egreso Programado',
    link_modificar:false
}/*,{
		id:'Egreso',
		width:60,
		filtro:1,
		label:'Egreso',
		link_modificar:false
	}*/,{
    id:'TiempoEstadia',
    width:20,
    filtro:1,
    label:'Estadía',
    link_modificar:false
}
/*
        [FechaIngreso] => Feb 13 2012  3:35PM
        [FechaEgreso] =>
        [FechaIngresoProgramado] => Feb 13 2012  3:00PM
        [FechaEgresoProgramado] => Feb 13 2012  7:00PM
        [Ingreso] => Feb 13 2012  3:35PM
        [Egreso] =>
        [TiempoEstadia] => 14
    */
];

var oCache = {
    iCacheLower: -1
};
 
function fnSetKey( aoData, sKey, mValue )
{
    for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
    {
        if ( aoData[i].name == sKey )
        {
            aoData[i].value = mValue;
        }
    }
}
 
function fnGetKey( aoData, sKey )
{
    for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
    {
        if ( aoData[i].name == sKey )
        {
            return aoData[i].value;
        }
    }
    return null;
}
 
function fnDataTablesPipeline ( sSource, aoData, fnCallback ) {
    var iPipe = 5; /* Ajust the pipe size */
     
    var bNeedServer = false;
    var sEcho = fnGetKey(aoData, "sEcho");
    var iRequestStart = fnGetKey(aoData, "iDisplayStart");
    var iRequestLength = fnGetKey(aoData, "iDisplayLength");
    var iRequestEnd = iRequestStart + iRequestLength;
    oCache.iDisplayStart = iRequestStart;
     
    /* outside pipeline? */
    if ( oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper )
    {
        bNeedServer = true;
    }
     
    /* sorting etc changed? */
    if ( oCache.lastRequest && !bNeedServer )
    {
        for( var i=0, iLen=aoData.length ; i<iLen ; i++ )
        {
            if ( aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho" )
            {
                if (oCache.lastRequest.length == 0) {
                        bNeedServer = true;
                        break;
                }
                if ( aoData[i].value != oCache.lastRequest[i].value )
                {
                    bNeedServer = true;
                    break;
                }
            }
        }
    }
     
    /* Store the request for checking next time around */
    oCache.lastRequest = aoData.slice();
     
    if ( bNeedServer )
    {
        if ( iRequestStart < oCache.iCacheLower )
        {
            iRequestStart = iRequestStart - (iRequestLength*(iPipe-1));
            if ( iRequestStart < 0 )
            {
                iRequestStart = 0;
            }
        }
         
        oCache.iCacheLower = iRequestStart;
        oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
        oCache.iDisplayLength = fnGetKey( aoData, "iDisplayLength" );
        fnSetKey( aoData, "iDisplayStart", iRequestStart );
        fnSetKey( aoData, "iDisplayLength", iRequestLength*iPipe );
         
        $.getJSON( sSource, aoData, function (json) { 
            /* Callback processing */
            oCache.lastJson = jQuery.extend(true, {}, json);
             
            if ( oCache.iCacheLower != oCache.iDisplayStart )
            {
                json.aaData.splice( 0, oCache.iDisplayStart-oCache.iCacheLower );
            }
            json.aaData.splice( oCache.iDisplayLength, json.aaData.length );
             
            fnCallback(json)
        } );
    }
    else
    {
        json = jQuery.extend(true, {}, oCache.lastJson);
        json.sEcho = sEcho; /* Update the echo for each response */
        json.aaData.splice( 0, iRequestStart-oCache.iCacheLower );
        json.aaData.splice( iRequestLength, json.aaData.length );
        fnCallback(json);
        return;
    }
}

$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw )
{
	//alert("fnReloadAjax");
    if ( typeof sNewSource != 'undefined' && sNewSource != null )
    {
        oSettings.sAjaxSource = sNewSource;
    }
    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;
    var iStart = oSettings._iDisplayStart;
    var aData = [];
 
    this.oApi._fnServerParams( oSettings, aData );
     
    oSettings.fnServerData( oSettings.sAjaxSource, aData, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );
         
        /* Got the data - add it to the table */
        var aData =  (oSettings.sAjaxDataProp !== "") ?
            that.oApi._fnGetObjectDataFn( oSettings.sAjaxDataProp )( json ) : json;
         
        for ( var i=0 ; i<aData.length ; i++ )
        {
            that.oApi._fnAddData( oSettings, aData[i] );
        }
         
        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
        that.fnDraw();
         
        if ( typeof bStandingRedraw != 'undefined' && bStandingRedraw === true )
        {
            oSettings._iDisplayStart = iStart;
            that.fnDraw( false );
        }
         
        that.oApi._fnProcessingDisplay( oSettings, false );
         
        /* Callback user function - for event handlers etc */
        if ( typeof fnCallback == 'function' && fnCallback != null )
        {
            fnCallback( oSettings );
        }
    }, oSettings );
}

function procesarInputSearch() {
	// Toma todos los titulos de columna
	var allLabels = $(".DataTables_sort_wrapper");
	strBusqueda = '';
	$.each(allLabels,function(i,x) {
		var aux = $(this).html().split("<");
		strBusqueda += (strBusqueda) ? ', ' + aux[0] : aux[0];
	});

	// Busca el input a agrandar y acoplar el tip
	var allInputs = $("input[type=text]");
	$.each(allInputs,function(i,x) {
		if ($(this).attr('name') === undefined && $(this).attr('id') === undefined) {
			$(this).attr('id', 'txtFiltro');
			$(this).attr('size','80');
		}
	});
	strBusqueda = 'Buscar por ' + strBusqueda;
	procesarTipsFiltro(['txtFiltro'],strBusqueda);
}

var oTable = null;
function crearTabla(params, destroy) {
	oTable = $('#example').dataTable( {
        "bProcessing": true,
        "sAjaxSource": "boot.php?c="+seccion+"&action=buscar&method=ajax_json&"+params,
        "sScrollY": 300,
        "bScrollCollapse": true,
        "bStateSave": true,
		"bDestroy": destroy,
        "oLanguage": {
            "sLengthMenu": "Mostrar _MENU_ registros por pagina",
            "sZeroRecords": "No se encontraron registros",
            "sProcessing": "Cargando...",
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
        "bJQueryUI": true,
        "sDom": '<"H"Tfr>t<"F"ip>',
        /*"oTableTools": {
            "aButtons": [
                {
					"sExtends":    "copy_to_div",
                    "sButtonText": "Exportar",
                    "sDiv":        "copy",
                    "sUrl": "/localizart/kcc2xls.php"
                }
            ]
        },/**/
        "sPaginationType": "full_numbers",
        "fnRowCallback": function(nRow, aData, iDisplayIndex) {
			if (aData[0] == 0) {
				$('td:eq(0)', nRow).html("-");
			} 
			/**/
			else {
				$('td:eq(0)', nRow).html("<a target='_blank' style='color:blue' href='http://www.localizar-t.com/trafico/admin/popOrden.php?num="+aData[0]+"'>"+aData[0]+"</a>");
			}
			
			if (aData[6] == 1 && aData[7] == 1) {
				if (aData[2].substr(0,2) != "KC") {
					var n = $('td:eq(2)', nRow).html();
					$('td:eq(2)', nRow).html("<a target='_blank' style='color:blue' href='http://www.localizar-t.com/trafico/admin/abmReferencias.php?hidTipoListado=AM&id="+aData[8]+"'>"+n+"</a>");
				}
			}
			/**/
			if (aData[6] == 1) {
				$('td:eq(3)', nRow).css('color', 'red');
			}
			
			if (aData[7] == 1) {
				$('td:eq(4)', nRow).css('color', 'red');
			}
			
			if (aData[9]) {
				$('td:eq(1)', nRow).html(aData[10] + " <br/> (" + aData[9] + ")");
			}
        }
    });
	
	
	//-- agregar link export --//
	
	var $oContainer = $(".DTTT_container.ui-buttonset.ui-buttonset-multi");
    var $oButton = $("<button>");

	$oButton.addClass("DTTT_button");
    $oButton.addClass("DTTT_button_text");
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

    $oButton.html("<span>Exportar</span>");

    $oButton.bind("click", function(ev)
    {
    	location.href = "javascript:enviar('export_xls');";
    	return false;
    });

    $oContainer.html( $oButton );
    /* END Inserto boton de Exportacion de Periodo */
}

function onload() {
    
    $("#intermil").tabs();
    
    $(document).ajaxStop($.unblockUI); 
    
    $.blockUI({ message: '<p>Cargando datos, espere por favor...</p>' });
    var params = "";
	
	crearTabla(params, false);

	// El cuadro de busqueda con los nombres de columna
	procesarInputSearch();
    
    $('#datepicker').datepicker();
    $('#datepicker2').datepicker();

    $('#datepicker').change(function(){
		// Distinto funcionamiento si el segundo calendario esta desplegado
		var params = '';
		if ($("#buscador-avanzado").css("display") == 'none') {
			params += 'inicio=' + $('#datepicker').val();
			$('#fc').text($(this).val())
			//load(params);
			cargarGrilla(params);
			$('#date-selected').html($('#datepicker').val());
		} else {
			var f1 = sqlDateToUnixTimestamp($('#datepicker').val());
			var f2 = sqlDateToUnixTimestamp($('#datepicker2').val());
			if (f1 > f2) {
				$("#btnBuscarAvanzado").prop("disabled", "disabled");
			} else {
				$("#btnBuscarAvanzado").prop("disabled", null);
			}
		}
    });

    $('#datepicker2').change(function(){
		$('#date-selected').html("desde " + $('#datepicker').val() + " hasta " + $('#datepicker2').val());
		var f1 = sqlDateToUnixTimestamp($('#datepicker').val());
		var f2 = sqlDateToUnixTimestamp($('#datepicker2').val());
		if (f1 > f2) {
			$("#btnBuscarAvanzado").prop("disabled", "disabled");
		} else {
			$("#btnBuscarAvanzado").prop("disabled", null);
		}
    });

    $('#ui-datepicker-div').css('display','none');
    $('#txtDiaEmpieza').show();

    $('#txtDiaEmpieza').change(function() {

    });
}

$(document).ready(function(){
	onload();
});

function mostrar() {
    $('.dataTables_scrollHeadInner, .display').css('width','100%');
}

function cargarGrilla(params){
	$('#fechaDesde').val($('#datepicker').val());
	$('#fechaHasta').val($('#datepicker').val());
	load(params);	
}
function load(params) {
       
   	$.blockUI({ message: '<p>Cargando datos, espere por favor...</p><p><img src="imagenes/loading.gif" /></p>' }); 

	crearTabla(params, true);

	// El cuadro de busqueda con los nombres de columna
	procesarInputSearch();
}

/*function armarTabla() {
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
**/

var strBusqueda;
function tableData() {
    var asInitVals = new Array();
    //TableTools.DEFAULTS.aButtons = [ "copy", "csv", "xls" ];
    var oTable = $('#listadoRegistros').dataTable( {
		//"sDom": 'T<"clear">lfrtip',
		//"sDom": '<"H"Tfr>t<"F"ip>',
		//"iDisplayLength": 50,
		"oTableTools": {
            //"sSwfPath": "http://datatables.net/release-datatables/extras/TableTools/media/swf/copy_cvs_xls_pdf.swf",
			"aButtons": [
				{
					"sExtends": "copy",
					"sButtonText": "Copy to clipboard"
				},
				{
					"sExtends": "csv",
					"sButtonText": "Save to CSV"
				},
				{
					"sExtends": "xls",
					"sButtonText": "Save for Excel"
				}
			]
			/*"aButtons": [
				{
                "sExtends":    "collection",
                "sButtonText": "Exportar",
                "aButtons": [

				{
					"sExtends": "copy",
					"sButtonText": "Copiar"
				},
				{
					"sExtends": "xls",
					"sButtonText": "CVS"
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
                ]*/

		},
        //"sScrollY": 250,
        //"sScrollCollapse": true,
        "bStateSave": true,
        "oLanguage": {
            "sLengthMenu": "Mostrar _MENU_ registros por pagina",
            "sZeroRecords": "No se encontraron registros",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
            "sSearch": "",
            "sInfoFiltered": "", //"(filtrados de _MAX_ registros totales)",
            "oPaginate": {
                "sFirst": "<<",
                "sLast": ">>",
                "sNext": ">",
                "sPrevious": "<"
            }
        },
        //"aaSorting": [[ 1, "asc" ],[ 3, "asc" ]],
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
	//$('.DTTT_container').css('margin-right','85px').css('margin-bottom','0px')
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
    procesarTipsFiltro(['txtFiltro'],strBusqueda);
}

function render(i,data) {
	//alert("render");
    var tb = $('#resultTable');
    tr = $('<tr>');
    var cId = data[columnas[0].id]

    chk='<input name="chkId[]" type="checkbox"/>';
    if ($.browser.msie){
        chk=document.createElement(chk);
    }
    chk=$(chk).attr('checked',false);

    input=chk.clone().attr({
        id:'chk_'+cId,
        value:cId
    });
    td = $('<td>');
    var span = $('<span>');
    span.text(data[columnas[0].id]);
    td.attr('width','15px').append(input).append(span).appendTo(tr);

    $.each(columnas,function(c,col){
        if (c > 0) {
            td = $('<td>');//3
            td.css('width',columnas[c].width + 'px')
            /*
            if (col.link_modificar){
                var a = $('<a href="javascript:enviarModificacion(\'modificar\','+cId+')"/>');
                if (columnas[c].id == 'vi_codigo') {
                    a.text(data[columnas[c].id] + ' (' + data['vd_orden'] + ')');
                } else {
                    a.text(data[columnas[c].id]);
                }
                td.append(a);
            } else if(columnas[c].id == 'estado_ini'){
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
                div1.css('font-size','10pt')
                div1.css('font-weight','bold')
                div1.text(data['vd_ini'].substr(11,5))
                td.append(div1);

                var div1 = $('<div>');
                div1.text(data['vd_ini_real'])
                td.append(div1);

                if (data['diferenciaIngreso']) {
                    if (data['diferenciaIngreso'] <= 0) {
                        data['estadoIngreso'] = 'Arribo en Tiempo';
                    } else {
                        data['estadoIngreso'] = 'Arribo Atrasado';
                    }
                    var div1 = $('<div>');
                    div1.text('(' + data['estadoIngreso'] + ')')
                    td.append(div1);
                }


            } else if(columnas[c].id == 'vd_fin') {
                var div1 = $('<div>');
                //div1.text(data['vd_fin'])
                div1.css('font-size','10pt')
                div1.css('font-weight','bold')
                div1.text(data['vd_fin'].substr(11,5))
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
			else if (data['TiempoEstadia'] < 0 && columnas[c].id == 'TiempoEstadia') {
				td.text("-");
				td.css('color','red');
            } else {
                td.text(data[columnas[c].id]);
                if (data['noPoseeIngreso'] == 1 && columnas[c].id == 'FechaIngreso') {
                    td.css('color','red');
                }
                if (data['noPoseeEgreso'] == 1 && columnas[c].id == 'FechaEgreso') {
                    td.css('color','red');
                }
            }
            */
            td.text(data[columnas[c].id]);
            td.appendTo(tr);
        }
    });
    tr.appendTo(tb);
}

/**
 * Despliega el div con el segundo calendario
 */
function busquedaAvanzada()
{
	if ($("#buscador-avanzado").css("display") == 'none') {
		// Muestra busqueda avanzada
		$("#buscador-avanzado").css("display", "block");
		$("#lblDesde").css("display", "block");
		$("#lblHasta").css("display", "block");
		$("#btnAvanzado").html("B&uacute;squeda Simple");
		$("#btnBuscarAvanzado").css("display", "block");
	} else {
		$("#lblDesde").css("display", "none");
		$("#buscador-avanzado").css("display", "none");
		$("#btnAvanzado").html("B&uacute;squeda Avanzada");
		$("#btnBuscarAvanzado").css("display", "none");
		var params = 'inicio=' + $('#datepicker').val();
		$('#date-selected').html($('#datepicker').val());
		
		//load(params);
		cargarGrilla(params);
		/*if ($("#datepicker2").val() != $("#datepicker").val())
		{
			load(params);
			$('#date-selected').html($('#datepicker').val());
			$("#datepicker2").val($("#datepicker").val());
		}*/
	}
}

/**
 * Una vez que los dos calendarios tengan fechas validas, el 
 * boton Buscar se habilita y llama a esta funcion.
 */
function buscar() {
	var params = 'inicio=' + $('#datepicker').val() + '&fin=' + $('#datepicker2').val();
	//load(params);
	cargarGrilla(params);
}

/*
TableTools.BUTTONS.copy_to_div = {
    "sAction": "text",
    "sFieldBoundary": "",
    "sFieldSeperator": "\t",
    "sNewLine": "<br>",
    "sToolTip": "",
    "sButtonClass": "DTTT_button_text",
    "sButtonClassHover": "DTTT_button_text_hover",
    "sButtonText": "Copy to element",
    "mColumns": "all",
    "bHeader": true,
    "bFooter": true,
    "sDiv": "",
    "fnMouseover": null,
    "fnMouseout": null,
    "fnClick": function( nButton, oConfig ) {
        //document.getElementById(oConfig.sDiv).innerHTML = this.fnGetTableData(oConfig);
        //$("#"+oConfig.sDiv).val(this.fnGetTableData(oConfig));
        
        nIFrame = document.createElement('iframe');
        nIFrame.setAttribute( 'id', 'RemotingIFrame' );
        nIFrame.style.border='0px';
        nIFrame.style.width='0px';
        nIFrame.style.height='0px';
             
        document.body.appendChild( nIFrame );
        var nContentWindow = nIFrame.contentWindow;
        nContentWindow.document.open();
        nContentWindow.document.close();
         
        var nForm = nContentWindow.document.createElement( 'form' );
        nForm.setAttribute( 'method', 'post' );
         
        //for ( var i=0 ; i<aoPost.length ; i++ ) {
            nInput = nContentWindow.document.createElement( 'input' );
            nInput.setAttribute( 'name', 'xlsdata'); //aoPost[i].name );
            nInput.setAttribute( 'type', 'text' );
            //nInput.value = aoPost[i].value;   
            nInput.value = this.fnGetTableData(oConfig);
            nForm.appendChild( nInput );
        //}
         
        nForm.setAttribute( 'action', oConfig.sUrl );
         
        nContentWindow.document.body.appendChild( nForm );
         
        nForm.submit();

    },
    "fnSelect": null,
    "fnComplete": null,
    "fnInit": null
};


TableTools.BUTTONS.download = {
    "sAction": "text",
    "sFieldBoundary": "",
    "sFieldSeperator": "\t",
    "sNewLine": "<br>",
    "sToolTip": "",
    "sButtonClass": "DTTT_button_text",
    "sButtonClassHover": "DTTT_button_text_hover",
    "sButtonText": "Download",
    "mColumns": "all",
    "bHeader": true,
    "bFooter": true,
    "sDiv": "",
    "fnMouseover": null,
    "fnMouseout": null,
    "fnClick": function( nButton, oConfig ) {
		var oParams = this.s.dt.oApi._fnAjaxParameters( this.s.dt );
		var iframe = document.createElement('iframe');
		iframe.style.height = "0px";
		iframe.style.width = "0px";
		iframe.src = oConfig.sUrl+"?"+$.param(oParams);
		document.body.appendChild(iframe);
		//alert(iframe.src);
    },
    "fnSelect": null,
    "fnComplete": null,
    "fnInit": null
};

TableTools.BUTTONS.download = {
    "sAction": "text",
    "sFieldBoundary": "",
    "sFieldSeperator": "\t",
    "sNewLine": "<br>",
    "sToolTip": "",
    "sButtonClass": "DTTT_button_text",
    "sButtonClassHover": "DTTT_button_text_hover",
    "sButtonText": "Download",
    "mColumns": "all",
    "bHeader": true,
    "bFooter": true,
    "sDiv": "",
    "fnMouseover": null,
    "fnMouseout": null,
    "fnClick": function( nButton, oConfig ) {
        var oParams = this.s.dt.oApi._fnAjaxParameters( this.s.dt );
        var aoPost = [
            { "name": "hello", "value": "world" }
        ];
        var aoGet = [];
 
        nIFrame = document.createElement('iframe');
        nIFrame.setAttribute( 'id', 'RemotingIFrame' );
        nIFrame.style.border='0px';
        nIFrame.style.width='0px';
        nIFrame.style.height='0px';
             
        document.body.appendChild( nIFrame );
        var nContentWindow = nIFrame.contentWindow;
        nContentWindow.document.open();
        nContentWindow.document.close();
         
        var nForm = nContentWindow.document.createElement( 'form' );
        nForm.setAttribute( 'method', 'post' );
         
        for ( var i=0 ; i<aoPost.length ; i++ )
        {
            nInput = nContentWindow.document.createElement( 'input' );
            nInput.setAttribute( 'name', aoPost[i].name );
            nInput.setAttribute( 'type', 'text' );
            nInput.value = aoPost[i].value;
             
            nForm.appendChild( nInput );
        }
         
        var sUrlAddition = '';
        for ( var i=0 ; i<aoGet.length ; i++ )
        {
            sUrlAddition += aoGet[i].name+'='+aoGet[i].value+'&';
        }
         
        nForm.setAttribute( 'action', oConfig.sUrl );
         
        nContentWindow.document.body.appendChild( nForm );
         
        nForm.submit();
    },
    "fnSelect": null,
    "fnComplete": null,
    "fnInit": null
};
*/