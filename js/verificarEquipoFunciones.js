var MAX_INTENTOS=3,
	TIEMPO_CONSULTA=5000,
	verificacionId=0,
	t_base=0,
	t_nomenclado=0,
    g_BogusGroup = 0;

var ejecutarComandos = "";
var idComandoEjecute = "";
var t = "";

$(document).ready(function() {
	$('#btnArchivo').click(function(){
        
		importar();
	})
});

function importar() {
	var coma = "";
	verificacionId=0;
    $.ajax( {
        "url": "ajaxComandosImportacion.php",
        "dataType": "json",
        "type": "post",
        "data": {
            "txtArchivo": $('#txtArchivo').val().replace(/\n/g, "|::|")
        },
        "success": function(data) {
			var $tabla=$('#tbl_pruebas'),
                $tr=$('<tr/>'),
                $td=$('<td/>')
            var $btn=$td.clone().append($('<input type="button" value="Enviar"/>').addClass('btnPrueba'))
            var $span=$('<span/>'),	
				$img=$td.clone().append($('<img />').attr({src:"imagenes/cruz.png",width:"14",height:"14"}).addClass('resImg')),
                $espera=$td.clone().append($('<img />').attr({src:"imagenes/espera.png",width:"14",height:"14"})),
                $envia=$td.clone().append($('<img />').attr({src:"imagenes/envia_espera.png",width:"14",height:"14"})),
                $txt=$td.clone().append($('<span/>').addClass('instr'),$('<input type="text" />').addClass('resTxt')),
                temp=null;
                
			$tabla.empty();
            
            var flagGrupo = false;
            
            $.each(data,function(i,v){
                if (!flagGrupo) {
                    g_BogusGroup = v.gc_gr_id;
                    flagGrupo = true;
                }
				
				ejecutarComandos+=coma.concat(v.co_id);
				coma = ",";
				$('#ejecutarAllComandos').show();
					
                //console.log(i);
                $tr.clone().attr('id','cmd'+v.co_id)
                           .data({comando:v.co_id,tipo:v.co_tipo})
                           .append(
                                $td.clone().text(v.co_nombre),
                                (v.co_tipo)?$envia.clone():$espera.clone(),
                                $btn.clone(),
                                $img.clone(),
                                $txt.clone().children('span').text(v.co_instrucciones).parent()
                            )
                .appendTo($tabla);
            });
		}
    } );
	return false;
}

function estadoGuardar(){
	//if (verificacionId && ($('img.resImg[src*="ok"]','#tbl_pruebas').length === $('img.resImg','#tbl_pruebas').length)){
	if (verificacionId && ($('input.resTxt.resOK').length === $('input.resTxt').length)){
		/*$('#botonesABM span').click(function(){
			enviar('guardarM');
		}).css({cursor:'pointer',color:'black'});
		$('#botonesABM span img').attr('src','imagenes/botonGuardar.png');
		*/
	}else{
		/*
		if ($('#botonesABM span').css('cursor')==='pointer'){
			$('#botonesABM span').unbind('click').css({cursor:'default',color:'grey'});
			$('#botonesABM span img').attr('src','imagenes/botonGuardarGrayed.png');
		}
		*/
	}
}

function setEstado(comando,estado){
	var img=$('img.resImg','#cmd'+comando),
		btn=$('.btnPrueba');
	switch(estado){
		case 'ok':
			img.attr('src','imagenes/ok.png');
			btn.removeAttr('disabled','disabled');
			break;
		case 'lo':
			//img.attr('src','imagenes/cargando.gif');//no arrancaba
			img[0].src='imagenes/cargando.gif';
			btn.attr('disabled','disabled');
			break;
		case 'no':
			img.attr('src','imagenes/cruz.png');
			btn.removeAttr('disabled','disabled');
			break;
	}
	estadoGuardar();
}

function resetear(){
	if (verificacionId){
		clearTimeout(t_base);
		t_base=0;
		$.get(
			  'ajaxVerificarEquipo.php',
			  {a:'stp',vId:verificacionId});
		$('#txtNroSerie').next('img').attr('src','imagenes/cruz.png');

		$('#tbl_pruebas tr').each(function(){
			var data=$(this).data();
			setEstado(data.comando,'no');
			clearTimeout(data.t_id);
			$(this).data('t_id',0);
			$.get('ajaxVerificarEquipo.php',
				  {a:'stp',vId:verificacionId,c:data.comando});
		});
		verificacionId=0;
	}

	$('#nom_evento').text('');
	$('#nom_fecha').text('');
	$('#nom_fecha_r').text('');
	$('#nom_dni').text('');
	$('#nom_nomenclado').text('');
	$('#nom_latlon').text('');
	$('#nom_rumbo').text('');
	$('#nom_velgps').text('');
	$('#nom_odometro').text('');
	$('#nom_velocidad').text('');
	$('#nom_gasoil').text('');
	var entradas = '00000000'.split('');
	$.each(entradas, function(i,v){
		$('#nom_entrada'+i).toggleClass('on',Boolean(parseInt(v,10)));
	});

	estadoGuardar();
}

/**
 * pide actualizacion sobre el estado de un comando en prueba. si se excede el tiempo de espera, lo toma como rechazado
 * @param {number} iter
 * @param {number} comando
 */
function controlarComando(iter,comando){
	if (verificacionId){
		if (iter <= MAX_INTENTOS){
			$.getJSON(
				'ajaxVerificarEquipo.php' /*+ '?XDEBUG_SESSION_START=netbeans-xdebug'*/,
				{a:'ctr',vId:verificacionId,c:comando},
				function(d){
					var t;
					if (d.ok){
						$('#cmd'+comando).data('t_id',0)
						.find('span.instr').hide().end()
						.find('input.resTxt').val(d.res)
							.toggleClass('resBAD',d.bad)
							.toggleClass('resOK',!d.bad)
							.show();
						setEstado(comando,'ok');
					}else{
						t=setTimeout('controlarComando('+(iter+1)+','+comando+')',TIEMPO_CONSULTA);
						$('#cmd'+comando).data('t_id',t);
					}
				}
			);
		}else{
			$.get('ajaxVerificarEquipo.php',
				  {a:'stp',vId:verificacionId,c:comando});
			$('#cmd'+comando+' input.resTxt').val('')
			.toggleClass('resBAD',true)
			.toggleClass('resOK',false);
			setEstado(comando,'no');
		}
	}
}

/**
 * creo el registro
 * @param {object} data
 */
function iniciarPrueba(data){
	setEstado(data.comando,'lo');
	$.getJSON(
		'ajaxVerificarEquipo.php' /*+ '?XDEBUG_SESSION_START=netbeans-xdebug'*/,
		{a:'prb',vId:verificacionId,c:data.comando,t:data.tipo},
		function(d){
			if(d.ok){
				controlarComando(0,data.comando);
			}else{
				setEstado(data.comando,'no');
			}
		}
	);
}

iniciarPruebaNroSerie=$.noop;

var actualizo = 0
function actualizarNomenclado(id){
	if (id>0 && actualizo == 0){
		actualizo = 1;
		$.getJSON(
			'ajaxObtenerDatosCombo.php',
			't=DatosUnidadSysHeart%20'+id,
			function(d){
				d=d[0];
				$('#nom_evento').text(d.Evento);
				$('#nom_fecha').text(d.FechaGeneracion);
				$('#nom_fecha_r').text(d.FechaRecepcion);
				$('#nom_dni').text(d.DniConductor);
				$('#nom_nomenclado').text(d.NomencladoEquipo);
				//$('#nom_latlon').text((d.Latitud).substr(0,7)+','+(d.Longitud).substr(0,7));
				$('#nom_latlon').text((d.Latitud).toFixed(4)+','+(d.Longitud).toFixed(4));
				$('#nom_rumbo').text(d.Rumbo);
				$('#nom_velgps').text(d.VelocidadGPS);
				$('#nom_odometro').text(d.Odometro);
				$('#nom_velocidad').text(d.Velocidad);
				$('#nom_gasoil').text(d.GasOilConsumido);
				var entradas = d.Entradas.split('');
				$.each(entradas, function(i,v){
					$('#nom_entrada'+i).toggleClass('on',Boolean(parseInt(v,10)));
				});
			}
		);
		setTimeout("actualizaNomenclado("+id+")",20000);
	}
}

function actualizaNomenclado(id) {
	 actualizo = 0;
	 actualizarNomenclado($('#txtEquipo').val());
}

jQuery(document).ready(function($){

	$('#cmbGrupos').change(function(){
		var coma = "";
		resetear();
        g_BogusGroup = 0;
		jQuery.getJSON(
			'ajaxComandosAsignados.php',
			{g:$(this).val()},
			function(data){
				var $tabla=$('#tbl_pruebas'),
					$tr=$('<tr/>'),
					$td=$('<td/>')
					var $btn=$td.clone().append($('<input type="button" value="Enviar"/>').addClass('btnPrueba'))
					var $span=$('<span/>'),
					$img=$td.clone().append($('<img />').attr({src:"imagenes/cruz.png",width:"14",height:"14"}).addClass('resImg')),
					$espera=$td.clone().append($('<img />').attr({src:"imagenes/espera.png",width:"14",height:"14"})),
					$envia=$td.clone().append($('<img />').attr({src:"imagenes/envia_espera.png",width:"14",height:"14"})),
					$txt=$td.clone().append($('<span/>').addClass('instr'),$('<input type="text" />').addClass('resTxt')),
					temp=null;
				$tabla.empty();
				$.each(data,function(i,v){
					
					ejecutarComandos+=coma.concat(v.co_id);
					coma = ",";
					$('#ejecutarAllComandos').show();
					
					$tr.clone().attr('id','cmd'+v.co_id)
								.data({comando:v.co_id,tipo:v.co_tipo})
								.append(
									   $td.clone().text(v.co_nombre),
									   (v.co_tipo)?$envia.clone():$espera.clone(),
									   $btn.clone(),
									   $img.clone(),
									   $txt.clone().children('span').text(v.co_instrucciones).parent()
									   )
					.appendTo($tabla);
				});
			}
		);
		
	});
	
	$('#txtEquipo option').eq(0).text('');
	$('#txtEquipo')
		//.combobox()
		.focus(function(){
			$(this).css('backgroundColor','');
		})
		.change(function(){
			resetear();
			clearTimeout(t_nomenclado);
			t_nomenclado=0;
			actualizarNomenclado(this.value);
		});
	
    // Boton PROBAR comando
	$('#tbl_pruebas').delegate('input.btnPrueba','click',function(){
		
		var cmbEquipo=$('#txtEquipo'),
			data;
			
		if (cmbEquipo.val()==='0'){
			cmbEquipo.css('backgroundColor','red');
			return false;
		}

		//obtengo data. si no se inicio una verificacion, la inicio aca, sino paso directo a grabar el registro
		data=$(this).parentsUntil('table','tr').data();
		if (verificacionId!==0){
			iniciarPrueba(data);
		}else{
            var grupoID;
            if ( g_BogusGroup != 0) {
                grupoID = g_BogusGroup;
            } else {
                grupoID = $('#cmbGrupos').val();
            }
			$.getJSON(
				'ajaxVerificarEquipo.php',
				{a:'ini',e:cmbEquipo.val(),g:grupoID},
				function(d){
					if (d.ok){
						verificacionId=d.vId;
						//iniciarPruebaNroSerie();
						iniciarPrueba(data);
					}
				}
			);
		}
		return false;
	});
});
	//R-> procesarTips(['txtEquipo','txtNroSerie']);
	
function ejecutarAllComandos(){
	arr_comandos = ejecutarComandos.split(',');
		if(idComandoEjecute === ""){
			idComandoEjecute = 0;
			$('#tbl_pruebas tr#cmd'+arr_comandos[0]+' .btnPrueba').trigger('click');
			$('#tbl_pruebas tr#cmd'+arr_comandos[0]+' img.resImg').attr('src','imagenes/cargando.gif');
			setTimeout('ejecutarAllComandos()',1000);
			$("a#ejecutarAllComandos").attr('href','javascript:cancelarAllComandos()');
			$("a#ejecutarAllComandos").html('Cancelar Comandos');
		}
		else{
			var en_ejecucion = false;
			for(i = 0; i < arr_comandos.length; i++ ){
				if($('#tbl_pruebas tr#cmd'+arr_comandos[i]+' img.resImg').attr('src') == 'imagenes/cargando.gif'){
					en_ejecucion = true;	
				}
			}
			
			if(en_ejecucion == false){
				idComandoEjecute = parseInt(idComandoEjecute) + 1;
				if(idComandoEjecute < arr_comandos.length){
					$('#tbl_pruebas tr#cmd'+arr_comandos[idComandoEjecute]+' .btnPrueba').trigger('click');
					$('#tbl_pruebas tr#cmd'+arr_comandos[idComandoEjecute]+' img.resImg').attr('src','imagenes/cargando.gif');
					setTimeout('ejecutarAllComandos()',1000);
				}
				else{
						idComandoEjecute = "";
						$("a#ejecutarAllComandos").attr('href','javascript:ejecutarAllComandos()');
						$("a#ejecutarAllComandos").html('Ejecutar Comandos');
					}
			}
			else{
				setTimeout('ejecutarAllComandos()',1000);
				}
		}
}	

function cancelarAllComandos(){
	clearTimeout(t);
	clearTimeout(t_base);
	t_base = 0;
	arr_comandos = ejecutarComandos.split(',')
	idComandoEjecute = arr_comandos.length;
	$("a#ejecutarAllComandos").attr('href','javascript:;');
	$("a#ejecutarAllComandos").html('Cancelando Ejecuci&oacute;n...');
}