var referenciaSelect = [];
var popupZonaInteres;

// Namespace
var newTracer = {
    data: null,
    mapHoverMarker: null
};

// BEGIN newTracer event handlers
newTracer.movil_onClick = function(ev, oMovilInfo){
	//zoomMoviles(); -- Lo saque porq descontrola al hacer click en el panel.
	newTracer.seguirMovil(oMovilInfo.mo_id);
}

newTracer.group_onClick = function(ev, oGroupInfo){
    newTracer.toggleGrupo(oGroupInfo.id);
}

newTracer.groupCheck_onClick = function(ev, oGroupInfo){
    newTracer.cambiarEstadoGrupo( oGroupInfo.id );
    ev.stopPropagation();
}

newTracer.asigGrupo_onClick = function(ev, oGroupInfo){
    asignarGrupo(oGroupInfo.id);
    ev.stopPropagation();
}

newTracer.modGrupo_onClick = function(ev, oGroupInfo){
    modificarGrupo(oGroupInfo.id);
    ev.stopPropagation();
}

newTracer.chkMovil_onClick = function(groupID, movID){
	newTracer.deleteReferenciaSelect();
	
    var $check = $("#chk_" + movID);
    var $fakecheck = $("#fakecheck_" + movID);
    
    $fakecheck.css( { "visibility": ( $check.prop("checked") ? "visible" : "hidden" ) } );
   	newTracer.actualizarTextoCantidadSeleccionados(groupID);
   	newTracer.actualizarCheckboxGrupo(groupID);
   	newTracer.verMovilesSeleccionados();
};
// END newTracer event handlers

// --GENERAR codigo HTML para un grupo de moviles --//
newTracer.htmlcodeGrupo = function( oGroupInfo ){
	var sDisplay, sNameImgToogle, sImgToggle;
   if (oGroupInfo.expandido){
        sDisplay = "block";
        sImgToggle = "raster/black/minus_12x3.png";
        sNameImgToogle = sImgToggle.substr(0, 4);
    } 
    else{
        sDisplay = "none";
        sImgToggle = "raster/black/plus_12x12.png";
        sNameImgToogle = sImgToggle.substr(0, 2);
    }
    
    $group = $("<div>")
        .attr( {
            "class": "group-container",
            "id": "group-container-" + oGroupInfo.id
        });
        
    var bGroupChecked = (oGroupInfo.cantMovsSeleccionados == oGroupInfo.cantMovsTotal);
    //var is_expanded = $.inArray(oGroupInfo.id, newTracer.data.expanded_group_ids) != -1;
	var is_expanded = getIsChecked(oGroupInfo.id, newTracer.data.expanded_group_ids);
	
	$groupHeader_ul = $("<ul>")
        // Boton expandir/colapsar grupo
        .append(
            $("<li>").attr( {
                "id": "expansor_g" + oGroupInfo.id,
                "class": "f-left expansor" } )
                .addClass( oGroupInfo.id == 0 
                    ? ( g_bUngroupedMovsExpanded ? "colapsar" : "expandir" )
                    : ( is_expanded ? "colapsar" : "expandir" )
                )
        )
        // Titulo del grupo
        .append(
            $("<li>")
                .attr( {
                    "class": "f-left group-title",
                    "title": oGroupInfo.nombre
                } )
                .append(
                    $("<a>")
                        .attr( {    "class": "group-title non-breaking",
                                    "href": "javascript: void(0)" } )
                        .html(
                            (
								(typeof(oGroupInfo.nombre) != 'undefined')?
									((oGroupInfo.nombre != null)?((oGroupInfo.nombre.length > NOMBREGRUPO_MAX_LENGTH?oGroupInfo.nombre.substr(0, NOMBREGRUPO_MAX_LENGTH - 3)+"...":oGroupInfo.nombre)):'--')
								:'--'	
							)	
							
                        )
                )
        )
        // Check grupos
        .append( 
            $("<li>")
                .attr( { 
                    "id": "chkGrupo_" + oGroupInfo.id,
                    "class": "f-right group-setcheck triple-check " + (bGroupChecked ? "checkstate-checked" : "checkstate-unchecked")
                })
            	//.val( oGroupInfo.id )
                .bind( "click", function(ev){ newTracer.groupCheck_onClick(ev, oGroupInfo); } )
        );
    
    $groupHeader = $("<div>")
        .bind("click", function(ev){ newTracer.group_onClick(ev, oGroupInfo) } )
        .attr( { "class": 'group-head '+oGroupInfo.id+' '.concat(((oGroupInfo.id < 0) || oGroupInfo.id === '-0' || oGroupInfo.id === '-')?'group-referencias':'group-moviles') } )
        .append( $groupHeader_ul )
        .append( $("<div>").attr( { "class": "clearfix" } ) ).appendTo( $group );
       
	   
    if(g_iOrderingCriteria == ORDERING_CRITERIA.GROUP){
		if(newTracer.data.canModifyGroups && oGroupInfo.id >= 0){ 
            $groupHeader_ul
                .append(
                    $("<li>")
                        .attr( {
                            "class": "boton-panel-grupo _16x16 modificar-grupo f-right",
                            "title": arrLang['modificar_grupo']
                        } )
                        .css( {
                            "position": "absolute",
                            "right": "20px"
                        } )
                        .bind( "click", function(ev){ newTracer.modGrupo_onClick(ev, oGroupInfo) } )
                );
        	}
			
        
        if(newTracer.data.canAssignGroups){
            $groupHeader_ul
                .append(
                    $("<li>")
                        .attr( {
                            "class": "boton-panel-grupo _16x16 asignar-grupo f-right",
                            "title": arrLang['asignar_grupo']
                        } )
                        .css( {
                            "position": "absolute",
                            "right": "42px"
                        } )
                        .bind( "click", function(ev){ newTracer.asigGrupo_onClick(ev, oGroupInfo) } )
                );
        }
    }
    
    $groupHeader_ul
        .append(
            $("<li>").attr({ "class": "clearfix" })
        )
        // Cant seleccionados/total
        .append(
            $("<li>")
                .attr( { 
                    "class": "f-left group-qty",
                    "id": "group-qty-" + oGroupInfo.id
                } )
                .append("(")
                .append( 
					$("<span>")
                        .attr( { 
                            "id": "spanCantMovilesSeleccionados_" + oGroupInfo.id,
                            "class": "movs-selected" } )
                        .html( oGroupInfo.cantMovsSeleccionados ) 
					
				)
                .append("/")
                .append( $("<span>")
                        .attr( { 
                            "id": "spanCantMovilesTotal_" + oGroupInfo.id,
                            "class": "movs-total" } )
                        .html( oGroupInfo.cantMovsTotal ) )
                .append(")")
        )
        .append(
            $("<li>").attr({ "class": "clearfix" })
        );
        
    $groupMoviles = $("<ul>")
        .attr( { 
            "id": "contenidoGrupo_" + oGroupInfo.id,
            "class": "contenido-grupo " + ( oGroupInfo.id == 0 
                ? ( g_bUngroupedMovsExpanded ? "shown" : "hidden" )
                : ( is_expanded ? "shown" : "hidden" )
            )
        } );
        
		
		        
	for(iMIndex in oGroupInfo.movs){
		movil = oGroupInfo.movs[iMIndex];
        oInfotabInfo = {
            "movil": movil,
            "fechaInfo": ""
        };
       
	   	movil_label = movil.movil
		//-- Datos necesarios para form Lateral
		movil.um_id_icono = 1; //Lo defino xq ya no se lo trae x base
		
		var oPackedData = {
        'ID': movil.mo_id,
        'lat': movil.sh_latitud,
        'lng': movil.sh_longitud,
        'iconID': movil.um_id_icono,
        'iconName': movil.iconName,
        'label': movil_label,
        'infoText': newTracer.htmlcodeInfotab(oInfotabInfo),
        'imgFolder': movil.iconFolder,
		'tipoPto': movil.tipopto, //Indica el tipo (truck/car/token/cellphone/box/referencia)
		'coords': movil.coords,//LatLng de pto de un poliogo o recta.
		'tipo': movil.mo_id_tipo_movil,//si es circular, recta, poligono, truck, car, etc.
		'mtrs': movil.radio_circulo,//Radio de las referencias Circulares en metros
		'color': movil.color,
		'id_evento': movil.tr_id_reporte,
		'precision': movil.sh_presicion,
		'um_grupo': movil.um_grupo,//id grupo para referencias
		'markerObj': null, 
        'grouped': 1 // grouped sera > 1 cuando haya moviles agrupados en el mapa, por defecto es = 1, significando solo el punto en si mismo.
    	};
		markerV3data[movil.mo_id] = oPackedData;
		
		//$groupMoviles.append( newTracer.htmlcodeMovil( oGroupInfo.movs[iMIndex], oGroupInfo.id ) );
		$groupMoviles.append( newTracer.htmlcodeMovil(movil, oGroupInfo.id ) );
    }
    
	$groupMoviles.appendTo($group);//HTML contenido x grupo de vehículos
       
    return $group;
}

//-- Generar codigo HTML del SIR (Seccion Izquierda Rastreo) --//
newTracer.buildSIR = function(){
	$ultimosReportes = $("#divScrollUltimosReportes").html("");
    $ultimosReportes.hide();
    
	if(newTracer.data.groupIDs.length){
		for(items in newTracer.data.groupIDs){
			groupID = newTracer.data.groupIDs[items];
			
			$ultimosReportes.append(
				newTracer.htmlcodeGrupo(newTracer.data.groups[groupID])
			);
		}
	}
	else{
		$ultimosReportes.append('<div style="text-align:center; line-height:30px;">'+arrLang['rastreo_sin_moviles']+'</div>');
	}
	
	// Panel incrustado a la izquierda
	if(g_bEmbedGPSPanel){
        if (g_bGPSPanelActive){}
        else{
            $ultimosReportes.show();
		}
    }
    else{// Ventana flotante
    	$ultimosReportes.show();
	}
}


//--  GENERAR codigo HTML para un movil --//
newTracer.htmlcodeMovil = function(oMovilInfo, groupID){
	var $checkbox, $fake_checkbox;
    
	var motor_encendido = oMovilInfo.estado_motor;

	var status = '';
	//-- SE DEFINE ESTADO DE GPS/WIFI/SEÑAL/BATERIA --//
	if(oMovilInfo.tipo_data == 1 && oMovilInfo.mo_id_tipo_movil == 1){//-- Es Movil  y de tipo celular (1-Celular/2-auto/3-camion)--// 
		oMovilInfo.entradas = parseInt(oMovilInfo.entradas);
		status+= '<li class="f-right i-status '+(oMovilInfo.entradas?(
			(oMovilInfo.entradas> 0 && oMovilInfo.entradas <= 25)?'bateria_2':
			((oMovilInfo.entradas > 25 && oMovilInfo.entradas <= 50)?'bateria_3':
			((oMovilInfo.entradas > 50 && oMovilInfo.entradas <= 75)?'bateria_4':'bateria_5'
			))
		):'bateria_1')+'" title="'+(oMovilInfo.entradas?(arrLang['bateria']+' '+oMovilInfo.entradas+'%'):arrLang['bateria_baja'])+'"></li>';
		
		var txt_senial = arrLang['sin_cobertura'];
		var class_senial = 'signal_1';
		if(oMovilInfo.sh_senial){
			if(oMovilInfo.sh_senial < 0){
				oMovilInfo.sh_senial = oMovilInfo.sh_senial*-1;
			}
			if(oMovilInfo.sh_senial > 0 && oMovilInfo.sh_senial <= 25){
				txt_senial = arrLang['cobertura_mala'];
				class_senial = 'signal_2';
			}
			else if(oMovilInfo.sh_senial > 25 && oMovilInfo.sh_senial <= 50){
				txt_senial = arrLang['cobertura_regular'];
				class_senial = 'signal_3';
			}
			else if(oMovilInfo.sh_senial > 50 && oMovilInfo.sh_senial <= 75){
				txt_senial = arrLang['cobertura_buena'];
				class_senial = 'signal_4';
			}
			else {
				txt_senial = arrLang['cobertura_muy_buena'];
				class_senial = 'signal_5';
			}
		}
		
		var wifi_status = 'wifi_off';
		if(oMovilInfo.sh_estado_wifi == 1){wifi_status = 'wifi_on';}
		var wifi_title = arrLang['wifi_apagado'];
		if(oMovilInfo.sh_estado_wifi == 1){wifi_title = oMovilInfo.sh_wifi_name;}
		
		var gps_status = 'gps_off';
		if(oMovilInfo.sh_estado_gps == 1){gps_status = 'gps_on';}
		var gps_title = arrLang['gps_apagado'];
		if(oMovilInfo.sh_estado_gps == 1){gps_title = arrLang['gps_encendido']}
				
		status+= '<li class="f-right i-status '+class_senial+'" title="'+txt_senial+'"></li>';
		status+= '<li class="f-right i-status '+wifi_status+'" title="'+wifi_title+'"></li>';
		status+= '<li class="f-right i-status '+gps_status+'" title="'+gps_title+'"></li>';
	}
	else{
		// Velocidad
		status = '<li class="f-right velocidad-movil">'+oMovilInfo.velocidadFormateada+'</li>';
	}
	//-- --//
	
	//-- Tipo de evento --//
	var perfilTxt = $("<li>").attr({"class": "f-left tipo-evento"}).html('&nbsp;');	
    
	if(idPerfil != 16){
		perfilTxt = $("<li>")
                .attr( { 
                    "class": "f-left tipo-evento",
                    "title": oMovilInfo.tr_descripcion
                } )
				.html( 
				oMovilInfo.tr_descripcion.length > TIPOEVENTO_MAX_LENGTH
                    ? oMovilInfo.tr_descripcion.substr(0, TIPOEVENTO_MAX_LENGTH - 3) + "..."
                    : oMovilInfo.tr_descripcion
                )
	}
	
	
	
   	is_checked = getIsChecked(oMovilInfo.mo_id, newTracer.data.checked_mov_ids);
	
    $checkbox = $("<li>");
    $checkbox
        .attr( { "class": "f-right check-mov-container" } )
        .append( $("<input>")
            .attr( {
                "id": "chk_" + oMovilInfo.mo_id,
                "class": "chk-movil",
                "type": "checkbox",
                "data-id": oMovilInfo.mo_id } )
            .prop( "checked", is_checked )
            .val( oMovilInfo.mo_id )
        );
    
    $.each( $(".chk-movil", $checkbox), function(ind, checkbox){
        $(checkbox).bind( "click", function(ev){
            newTracer.chkMovil_onClick(groupID, checkbox.value);
            ev.stopPropagation();
        });
    });
    
    $fake_checkbox = $("<li>")
    $fake_checkbox.attr( {
            "class": "f-right movil-tick",
            "id": "fakecheck_" + oMovilInfo.mo_id,
            "title": is_checked ? "seleccionado" : ""
        } )
        .css( {
            "visibility": is_checked ? "visible" : "hidden"
        } );
            
    if(g_bShowMovChecks){
        $checkbox.show();
        $fake_checkbox.hide();
    }
    else{
        $checkbox.hide();
        $fake_checkbox.show();
    }
    
	if(oMovilInfo.movil != null){
		etiqueta = oMovilInfo.movil;
    	etiqueta_short = (oMovilInfo.movil.length>DATAMOVIL_MAX_LENGTH)?oMovilInfo.movil.substr(0,DATAMOVIL_MAX_LENGTH-3)+"...":oMovilInfo.movil;
	}
	else{
		etiqueta = '<'+arrLang['sin_nombre']+'>';
		etiqueta_short = '<'+arrLang['sin_nombre']+'>';
	}
	oMovilInfo.tr_descripcion = (oMovilInfo.tr_descripcion != null)?oMovilInfo.tr_descripcion:'';
	
	$movil_info = $("<ul>");
    // Motor Encendido/Apagado
	$movil_info.append( $("<li>")
            .attr( { 
                "title":(oMovilInfo.estado_movil == 'movimiento')?oMovilInfo.dg_curso : "",
                "class": "f-left " + (((oMovilInfo.estado_movil == 'gris')?"movil-motor":(
					((oMovilInfo.estado_movil == 'movimiento')? "rumbo-imagen " + "rumbo-"+ oMovilInfo.dg_curso:'movil-motor') 
				)
				+ ((oMovilInfo.tipopto == 'referencia')?' icon-referencia':' icon-movil')
                ))
            } )
            .css( { 
                "background-color": (
					(oMovilInfo.estado_movil == 'movimiento')?'transparent':
						((oMovilInfo.estado_movil == 'verde')?'green':(
							(oMovilInfo.estado_movil == 'rojo')?'red':'gray')
						)
					)
				}
			)
		)
		
		// Matricula del movil
        .append( $("<li>")
            .attr( { 
                "class": "f-left pad-2px movil-matricula non-breaking",
                "title": etiqueta } )
            .html( etiqueta_short )
        )
        
        // Check oculto
        .append( $checkbox )

        // señuelo de moviles chequeados
        .append( $fake_checkbox )

        // Fecha de generacion
        .append( $("<li>")
            .attr( { "class": "f-right pad-2px movil-fechagen" } )
            .html( oMovilInfo.fechaFormateada )
        )

       	// Clearfix para hacer la siguiente fila
		.append( $("<li>").css( { "clear": "both" } ) )
		
		// Tipo de evento
        .append(perfilTxt)
		//-- Estado de WIFI/GPS/Senial o VELOCIDAD--//
	   	.append(status);
		
          
    $movil_info_tick = $("<div>");
    $movil_info_tick.attr( { "class": "movil-tick-right f-right" } );
    
    $movil = $("<li>");
    $movil.attr( { 
            "class": "movil-info",
            "id": "movil-info_" + oMovilInfo.mo_id } )
        .bind( "click", function(ev){ newTracer.movil_onClick(ev, oMovilInfo); } )
        .append(
            $("<div>").attr( { "class": "movil-info-container f-left" } ).append( $movil_info )
        )
        .append(
            $("<div>").attr( { "class": "f-right" } ).append( $movil_info_tick )
        )
        .append(
            $("<div>").attr( { "class": "clearfix" } )
        );
        
    if(oMovilInfo.mo_id == g_iMovEnSeguimiento){
        $movil.addClass("movil-info-highlight");
    }
    
    return $movil;
}


newTracer.unfilterSIR = function(){
    $(".group-container").show();
    $(".group-qty").show();
    $(".movil-info").show();
    
    var aClassNames = [ "expandedOnDemand", "shownOnDemand" ];
    
    for(var iClassIdx in aClassNames){
        var sClassName = aClassNames[iClassIdx];
        $("." + sClassName).removeClass(sClassName);
    }
	
	
}


newTracer.filterSIR = function(groupid, movid){
    $group_container = $("#group-container-" + groupid);
    $(".group-container").hide();
    $group_container.show();
    
    $("#group-qty-" + groupid).hide();
    
    $expansor = $("#expansor_g" + groupid);
    if(!$expansor.hasClass("expandedOnDemand")){
        $expansor.addClass("expandedOnDemand");
    };
    
    $info_container = $("#contenidoGrupo_" + groupid);
    if(!$info_container.hasClass("shownOnDemand")){
        $info_container.addClass("shownOnDemand");
    };
    
    $movil_container = $("#movil-info_" + movid);
    $(".movil-info", $group_container).hide();
    $movil_container.show();
}


newTracer.filtrarMovil = function(sTextToSearch){
    var iMovToShow, iGroupToShow, bMovilFound;
    
    var arrSearchParts = sTextToSearch.split(" / ");
    var oSearch = {
        "matricula": arrSearchParts[0],
        "interno": arrSearchParts[1],
        "otros": arrSearchParts[2]
    };
    
    // Busqueda
    bMovilFound = false;
    
    for(var iGrpIdx = 0; iGrpIdx < newTracer.data.groupIDs.length && !bMovilFound; iGrpIdx++){
        var iGroupID = newTracer.data.groupIDs[iGrpIdx];
        var oGroup   = newTracer.data.groups[iGroupID];
        
        for ( var iMovIdx = 0; iMovIdx < oGroup.movs.length && !bMovilFound; iMovIdx++ ){
            var oMovil = newTracer.data.groups[iGroupID].movs[iMovIdx];
            
            //if ( $.trim(oMovil.mo_matricula) == $.trim(oSearch.matricula) )
            if ( $.trim(oMovil.movil) == $.trim(oSearch.matricula)){
                iMovToShow = oMovil.mo_id;
                iGroupToShow = iGroupID;
                bMovilFound = true;
            }
        }
    }
    
    // Solo muestro el movil corresp. y el grupo al que pertenece
    newTracer.filterSIR(iGroupToShow, iMovToShow);
    
    return iMovToShow;
}


//-- GENERAR codigo HTML para el infotab de un movil --//
newTracer.htmlcodeInfotab = function(oInfotabInfo){    
    var sHTML = '';
    var mov = oInfotabInfo.movil;
    sHTML += '<div style="width: 300px; height: 150px;">';
    
    sHTML += "<i>" + arrLang['datos_gps'] + "</i>";
    sHTML += "<hr width=\"300\"/>";
    sHTML += arrLang['movil']+ ": <b>" + mov.movil + "</b>";
    sHTML += "<br/>";

    sHTML += arrLang['matricula']+ ": <b>" + mov.mo_matricula + "</b>";
    sHTML += "<br/>" + "<br/>";
    
    sHTML += "<b>" + mov.fechaFormateada + "</b>";
    sHTML += "<br/>" + "<br/>";
    
    bgColorV = "";
    if(mov["um_velocidadMaxima"] > 0 && mov["dg_velocidad"] > 0){
        if (mov["dg_velocidad"] < mov["um_velocidadMaxima"]){
            // VERDE
            fontColor = "#008000";
        } 
        else if(( mov["dg_velocidad"] >= mov["um_velocidadMaxima"]) && ( mov["dg_velocidad"] <= ( mov["um_velocidadMaxima"] * 1.1))){
            // AMARILLO
            fontColor = "#E8E800";
            bgColorV = "#808080";
        }
        else{
            // ROJO
            fontColor = "#FF6A6A";
        }
    }
    else{
        fontColor = "#000000";
    }
    
    sHTML += arrLang['velocidad']+ ": " 
        + "<span class=\"textoGrande\" style=\"color:" + fontColor + ";background-color:" + bgColorV + "\">"
            + mov.velocidadFormateada 
        + "</span>"
        + "<br/>";
    
    sHTML += '</div>';
    
    return sHTML;    
}


newTracer.toggleGrupo = function(idGrupo){
    flagGuardarPreferencias = true;
    var estado = 0;
    
    if ( $("#expansor_g" + idGrupo).hasClass("expandir") ){
        $("#expansor_g" + idGrupo).removeClass("expandir").addClass("colapsar");
        estado = 1; // GRUPO CERRADO
        if ( idGrupo == 0 ){
            g_bUngroupedMovsExpanded = true;
        }
    }
    else{
        $("#expansor_g" + idGrupo).removeClass("colapsar").addClass("expandir");
        estado = 0; // GRUPO ABIERTO
        if(idGrupo == 0){
            g_bUngroupedMovsExpanded = false;
        }
    }
    
    $("#contenidoGrupo_" + idGrupo).toggle();

    url = "ajaxActualizarEstadoGrupo.php";
    $.ajax({
        "url": dominio+url,
        "data": {
            "idGrupo": idGrupo,
            "estado": estado,
            "p": 0
        },
        "type": "post",
        "success": function(data, status, jqxhr){}
		/*,"error": function(jqxhr, status, error){
            debug.warn("Error en el AJAX al llamar a '" + url + "'.");
        }*/
    });
}


newTracer.reiniciarTabla = function(){
    var movilesListados = 0;
    
    ultimoCentrado = 0;
    idMovilSeleccionado = 0;
    idUltimoResaltado = 0;
    idUltimoMovilSeleccionado = 0;
    
    movilesListados = obtenerMovilesListados();
	mostrarMarcadoresAgrupados(movilesListados);
    seleccionarChecks();
    
    var datos = newTracer.htmlcodeInfoGPS();
    if (g_bEmbedGPSPanel){
        $("#divDatosInfoGps").html(datos);
    }
    else{
        $("#infogps").html(datos);
    }
}


newTracer.conFiltroBusq = function(){
	mostrarMarcadoresAgrupados(newTracer.data.movilesFiltrados );
    centrarMovilesSeleccionados(newTracer.data.movilesFiltrados, g_iZoomSpreadThreshold);
    flagChekeados = 1;
}


newTracer.sinFiltroBusq = function(){
	mostrarMarcadoresAgrupados(obtenerMovilesCheckeados());
    flagChekeados = 1;
}


newTracer.sinFiltroBusqRadSelec = function(){
	mostrarMarcadoresAgrupados( strMoviles );
    flagChekeados = 1;
}


//-- jQuery callback para la renovada seccion izquierda de rastreo --//
newTracer.callback_success = function(data, textStatus, jqXHR){
    
	var oPackedData = data.packed;
    timeLastReq = timeCurrReq;
    timeCurrReq = new Date();

    if (timeLastReq != null){
        var dd, mm, yyyy, h, m, s;

        dd = timeCurrReq.getDate();
        mm = parseInt(timeCurrReq.getMonth()) + 1;
        yyyy = timeCurrReq.getFullYear();

        h = timeCurrReq.getHours();
        if (h < 10){h = "0" + h;}
        
        m = timeCurrReq.getMinutes();
        if(m < 10){m = "0" + m;}
        
        s = timeCurrReq.getSeconds();
        if (s < 10){s = "0" + s;}

        var sTimeStamp = 
            dd + "/" + mm + "/" + yyyy + " " +
            h  + ":" + m  + ":" + s;

        var iDiffTime = timeCurrReq.getTime() - timeLastReq.getTime();
        arrReqTimes[sTimeStamp] = iDiffTime;
    }
    // -- refresh debug --

    if (g_bIsDataUpdate){
        newTracer.data.checked_mov_ids = oPackedData.checked_mov_ids;
        newTracer.data.expanded_group_ids = oPackedData.expanded_group_ids;
        
        if(typeof oPackedData.updated_mov_ids == 'undefined'){}
        else{
            if(oPackedData.updated_mov_ids.length > 0){
                newTracer.updateInformationSIR(oPackedData);
            }
        }
    }
    else{
        newTracer.data = oPackedData;
    }
	
	g_iMovEnSeguimiento = oPackedData.enSeguimiento;
	buscarHabilitado = true;
    
    orden = oPackedData.orden;
    limpiarMoviles();
    markerV = [];
	newTracer.buildSIR();// Carga la info en el panel Izq()
    
	if (g_bSearchIsActive){
        filtro = $("#txtBuscar").val();
        if(filtro != ""){
			idMovil = newTracer.filtrarMovil(filtro);
        	newTracer.seguirMovilFiltrado(idMovil);
		}
		else{
			g_bSearchIsActive = false;
			newTracer.unfilterSIR();	
		}
	}

    $("#divScrollUltimosReportes").prop("scrollTop", g_iScrollReportes);
    if ( g_bGPSPanelActive && g_iMovEnSeguimiento != SIN_SEGUIR_MOVIL ){
		crearTooltip(g_iMovEnSeguimiento);
    }
	
	if(typeof(oPackedData.paso1) != 'undefined'){
    	eval(oPackedData.paso1)(); //-- Ayuda a q no se eliminen los voliles seleccionados al refrescar --//
	}
	else{
		newTracer.sinFiltroBusq();
	}
	// Las proximas requests seran updates
	g_bIsDataUpdate = true;
	
    if (g_bIsFirstLoad){
		if($('#seleccionar-todos-los-grupos').val() == 1){//-- Seleccionar todos los Grupos por defecto --//
			//$('.group-setcheck.triple-check').css('visibility','hidden');	
			newTracer.cambiarEstadoGrupos(true,false);
		}
		else{
			centrarMovilesSeleccionados(obtenerMovilesCheckeados());
		}
    }
	g_bIsFirstLoad = false;
}


newTracer.updateInformationSIR = function(oUpdateData){
    var arrIDsToUpdate = oUpdateData.updated_mov_ids;

    // Recorro los updates a traves de los grupos
    for ( var i = 0; i < oUpdateData.groupIDs.length; i++ ){
        var iGroupID = oUpdateData.groupIDs[i];
        
        if (typeof(oUpdateData.groups[iGroupID]) != 'undefined' && typeof(newTracer.data.groups[iGroupID]) != 'undefined' ){
            newTracer.data.groups[iGroupID].cantMovsTotal = oUpdateData.groups[iGroupID].cantMovsTotal;
            newTracer.data.groups[iGroupID].cantMovsSeleccionados = oUpdateData.groups[iGroupID].cantMovsSeleccionados;
            
            var arrMovs = newTracer.data.groups[iGroupID].movs;
            for ( var ma = 0; ma < arrMovs.length; ma++ ){
                var arrMovsToUpdate = oUpdateData.groups[iGroupID].movs;
                
                for ( var mu = 0; mu < arrMovsToUpdate.length; mu++ ){
                    if ( arrMovs[ma].mo_id == arrMovsToUpdate[mu].mo_id ){
                        arrMovs[ma] = arrMovsToUpdate[mu];
                    }
                }
            }
        }
        /*else{
            debug.log("NO EXISTE el grupo '" + iGroupID + "'.");
        }*/
    }
    
    //Aclaracion:  newTracer.data quedo actualizado porque JavaScript maneja los arrays por referencia en memoria. //
}

/*
newTracer.toggleContextMenu = function(){
    $("#tracerCtxMenuContainer").toggle();
}
*/


newTracer.toggleInfoGPSContextMenu = function(){
    $("#infogpsCtxMenuContainer").toggle();
}


newTracer.htmlcodeInfoGPS = function(){
    var sHTML = '';
    
    datos += '<b>'+arrLang['movil']+': </b><br/>';
    datos += '<b>'+arrLang['fecha']+': </b><br/>';
    datos += '<b>'+arrLang['matricula']+': </b><br/>';
    datos += '<b>'+arrLang['velocidad']+': </b><br/>';
    datos += '<b>'+arrLang['evento']+': </b><br/>';
    datos += '<b>'+arrLang['ubicacion']+': </b><br/>';
    datos += '<b>'+arrLang['sentido']+': </b><br/>';
    
    return sHTML;
}


newTracer.callback_error = function(jqXHR, textStatus, errorThrown){
    debug.warn('Error del servidor:');
    debug.warn(jqXHR);
    debug.warn(textStatus);
    debug.warn(errorThrown);
}


newTracer.seguirMovil = function(idMovil){	
	$("#divDatosInfoGps").html("");
	$("#chk_" + idMovil).prop( "checked", true );
    $("#fakecheck_" + idMovil).css( { "visibility": "visible" } );
    
    flagGuardarPreferencias = true;
    flagChekeados = 1;
    flagRad=1;
   	newTracer.despintarFila(g_iMovEnSeguimiento);
    newTracer.pintarFila(idMovil);
    
    g_iMovEnSeguimiento = idMovil;
    centrarMovilesSeleccionados(idMovil, g_iZoomSpreadThreshold);
    
	g_bGPSPanelActive = true;
    g_bIsDataUpdate = false;

    if (g_bGPSPanelActive && g_iMovEnSeguimiento != SIN_SEGUIR_MOVIL){
        crearTooltip(g_iMovEnSeguimiento);
    }
    flagChekeados = 1;
	mostrarMarcadoresAgrupados(obtenerMovilesCheckeados());
}


newTracer.seguirMovilFiltrado = function(idMovil){
    newTracer.despintarFila(g_iMovEnSeguimiento);
    newTracer.pintarFila(idMovil);
    
    if ( g_iSearchType == SEARCH_TYPES.FILTER_AND_CHASE ){
        g_iMovEnSeguimiento = idMovil;
        centrarMovilesSeleccionados(idMovil, g_iZoomSpreadThreshold);
    }
}


$(document).bind( "ready", function(){
    $g_oOverlayLatLng = $("#divHoverLatLng");
    var menuMasOpciones = new jqxmenu("tracerCtxMenu", g_o_menucfg_MasOpciones);
    
    $("#divScrollUltimosReportes").bind( "scroll", function(ev){
        g_iScrollReportes = $(this).prop("scrollTop");
    });
});

$(document).bind("keydown", function(ev){
    newTracer.onKeyDown(ev);
});

$(document).bind("keyup", function(ev){
    newTracer.onKeyUp(ev);
});


newTracer.onKeyUp = function(ev){
    if (ev.keyCode == KEYS.CTRL){
        g_bShowLatLng = false;
        //map.setOptions({ draggableCursor: "url(http://maps.google.com/mapfiles/openhand.cur), move" });
        $g_oOverlayLatLng.hide();
    }
}


newTracer.onKeyDown = function(ev){
   switch( ev.keyCode ){
	   case KEYS.CTRL:
            g_bShowLatLng = true;
            if(g_bHoveringMap){
                $g_oOverlayLatLng.show();
                //map.setOptions({ draggableCursor: "crosshair" });
            }
            break;
        default: break;
    }
}


newTracer.pintarFila = function(idMovil){
    $movilinfo = $("#movil-info_" + idMovil);
    if(!$movilinfo.hasClass("movil-info-highlight")){
        $movilinfo.addClass("movil-info-highlight");
    }
}


newTracer.despintarFila = function(idMovil){
    $movilinfo = $("#movil-info_" + idMovil);
    if ($movilinfo.hasClass("movil-info-highlight")){
        $movilinfo.removeClass("movil-info-highlight");
    }
}


newTracer.verMovilesSeleccionados = function(){
    flagGuardarPreferencias = true;
    strMoviles = obtenerMovilesCheckeados();
    strMovilesCentrar = strMoviles;
    
    if ( $.trim(strMoviles) == "" ){
        strMoviles = "none";
    }
	mostrarMarcadoresAgrupados(strMoviles);
    centrarMovilesSeleccionados(strMovilesCentrar);
}


newTracer.cambiarEstadoGrupos = function(state, esMovil){
    flagCambiarEstado = true;
    
    $reportes = $("#divScrollUltimosReportes");
    $groups = $(".group-setcheck", $reportes);
    $checkboxes = $(".chk-movil", $reportes);
    
    $.each( $groups, function(ind, group){// Check Nombre del Grupo
		$selectedMovs = $("#spanCantMovilesSeleccionados_" + $(group).val() );
        $totalMovs = $("#spanCantMovilesTotal_" + $(group).val());
        
		if(state){ // Check
        	if(esMovil){
				if($(group).val() >= 0 && $(group).val() != '-0'){
					$selectedMovs.html( $totalMovs.html() );
					$(group).removeClass("checkstate-unchecked");
					$(group).addClass("checkstate-checked");
					flagChekeados = 0;	
				}	
			}
			else{
				$selectedMovs.html( $totalMovs.html() );
				$(group).removeClass("checkstate-unchecked");
				$(group).addClass("checkstate-checked");
				flagChekeados = 0;
			}
		}
        else{ // Uncheck
            $selectedMovs.html( 0 );
            $(group).removeClass("checkstate-checked");
            $(group).addClass("checkstate-unchecked");
            flagChekeados = 1;
        }
    });
    
    $.each( $checkboxes, function(ind, checkbox){ //Check x vehículo
        var id = checkbox.value;
        if(state){ // Chequear
            if(esMovil){
				if(id >= 0 && id != '-0'){
					checkbox.checked = true;
            		$("#fakecheck_" + id).css( { "visibility": "visible" } );
				}
			}
			else{
				checkbox.checked = true;
            	$("#fakecheck_" + id).css( { "visibility": "visible" } );
			}	
        }
        else{// Deschequear
           checkbox.checked = false;
           $("#fakecheck_" + id).css( { "visibility": "hidden" } );	
        }
    });
    
    guardarPreferencias();
    if(flagChekeados == 0){
        flagCheckeados = 1;
    }
    else{
        flagChekeados = 0;
    }
    
    newTracer.verMovilesSeleccionados();
}


newTracer.cambiarEstadoGrupo = function(idGrupo, estadoForzado){
    flagCambiarEstado = true;
    $groupCheck = $("#chkGrupo_" + idGrupo);
    
    if ( typeof estadoForzado == "undefined" ){
        // Si esta chequeado
        if ( $groupCheck.hasClass("checkstate-checked") ){
            $groupCheck.removeClass("checkstate-checked");
            $groupCheck.addClass("checkstate-unchecked");
            flagChekeados = 1;
        }
        else{ // Si no esta chequeado
            $groupCheck.removeClass("checkstate-unchecked");
            $groupCheck.addClass("checkstate-checked");
            flagChekeados = 0;
        }
    }
    else{
        // Si esta chequeado
        if (estadoForzado){
            $groupCheck.removeClass("checkstate-unchecked");
            $groupCheck.addClass("checkstate-checked");
            flagChekeados = 0;
        }
        else{ // Si no esta chequeado
            $groupCheck.removeClass("checkstate-checked");
            $groupCheck.addClass("checkstate-unchecked");
            flagChekeados = 1;
        }
    }
    
    seleccionarChecksGrupo(idGrupo);
	flagGuardarPreferencias = true;
    guardarPreferencias();
    newTracer.verMovilesSeleccionados();
}

newTracer.embedGPSPanel = function(){
    if ( !g_bEmbedGPSPanel && g_bGPSPanelActive ){
        $('#infoListado-upper').css('display', 'none');
        $("#divScrollUltimosReportes").css('display', 'none');
        $("#info").css('display', 'block');

        // Cierro el panel de datos GPS
        $("#main").removeClass("info-gps-activo");
        $("#alertas").removeClass("info-gps-activo");
        $("#infogps").hide();
        $("#divDatosInfoGps").html( $("#infogps-contenido").html() );
    }
    
    g_bEmbedGPSPanel = true;
    g_bGPSPanelActive = true;
}


newTracer.moveGPSPanel = function(){
    if ( g_bEmbedGPSPanel ){
        $("#infogps-contenido").html( $("#divDatosInfoGps").html() );
        $('#infoListado-upper').show();
        $("#divScrollUltimosReportes").show();
		$("#info").hide();
        g_bGPSPanelActive = false;
    }
    
    g_bEmbedGPSPanel = false;
}


newTracer.updateMenuGUI = function(mainMenuID){
    $(mainMenuID + " *").css( { "font-weight": "inherit" } );
    $(mainMenuID + " li[active='true']").css( { "font-weight": "bold" } );
    $(mainMenuID + " li[active='false']").css( { "font-weight": "normal" } );
}


newTracer.actualizarTextoCantidadSeleccionados = function(idGrupo){
    var $groupContent = $("#contenidoGrupo_" + idGrupo);
    var $checkboxes = $checkboxes = $(".chk-movil", $groupContent);
    var cantSeleccionados = 0;
    
    $.each( $checkboxes, function(ind, checkbox){
        if ( checkbox.checked ){
            cantSeleccionados++;
        }
    });
    
    $("#spanCantMovilesSeleccionados_" + idGrupo).html( cantSeleccionados );
}


newTracer.showMovChecks = function(){
    $reportes = $("#divScrollUltimosReportes");
    $li_checkboxes = $(".check-mov-container", $reportes);
    $fake_checkboxes = $(".movil-tick", $reportes);
    
    $li_checkboxes.show();
    $fake_checkboxes.hide();
    
    g_bShowMovChecks = true;
}


newTracer.hideMovChecks = function(){
    $reportes = $("#divScrollUltimosReportes");
    $li_checkboxes = $(".check-mov-container", $reportes);
    $fake_checkboxes = $(".movil-tick", $reportes);
    
    $li_checkboxes.hide();
    $fake_checkboxes.show();
    
    g_bShowMovChecks = false;
}


newTracer.actualizarCheckboxGrupo = function(groupID){
    var $groupcheckbox = $("#chkGrupo_" + groupID);
    var $movcheckboxes = $("#contenidoGrupo_" + groupID + " .chk-movil");
    
    var cantMovsSeleccionados = 0;
    var cantMovsTotal = newTracer.data.groups[groupID].cantMovsTotal;
    $.each( $movcheckboxes, function(ind, checkbox){
        if(checkbox.checked){
            cantMovsSeleccionados++;
        }
    });
    
    if(cantMovsSeleccionados == cantMovsTotal){
        $groupcheckbox.addClass("checkstate-checked");
        $groupcheckbox.removeClass("checkstate-unchecked");
    }
    else{
        $groupcheckbox.addClass("checkstate-unchecked");
        $groupcheckbox.removeClass("checkstate-checked");
    }
}

newTracer.setBuscador = function(e){
    if(e.keyCode == 13){
		if(buscar_response == false &&  $("#txtBuscar").val() != ""){
			buscarDireccion();
		}
		else if($("#txtBuscar").val() == ""){
			g_bSearchIsActive = false;
			newTracer.unfilterSIR();
		}
		else{
			$('#divMSG').hide();
			$('#divScrollUltimosReportes').show();
		}
	}
}


newTracer.mostrarVisorAlertas = function(){
    $("a#btnMostrarAlertas").hide();
    $("a#btnOcultarAlertas").show();
    $("div#alertas-contenido").show();
}

newTracer.ocultarVisorAlertas = function(){
    $("a#btnMostrarAlertas").show();
    $("a#btnOcultarAlertas").hide();
    $("div#alertas-contenido").hide();
}


newTracer.cerrarPanelGPS = function(){
    g_bGPSPanelActive = false;
    $("#main").removeClass("info-gps-activo");
    $("#alertas").removeClass("info-gps-activo");
    $("#infogps").hide();
}

newTracer.getInfoByMovID = function(idMovil){
    if ( typeof newTracer.data != "undefined"){
        var arrGroupIDs = newTracer.data.groupIDs;
        var bFound = false;
        var oMovInfo = null;

        // Recorro cada grupo...
        for ( var g=0; g<arrGroupIDs.length && !bFound; g++ ){
            var iGroupID = arrGroupIDs[g];

            var arrMoviles = newTracer.data.groups[iGroupID].movs;
            // Recorro cada movil de dicho grupo...
            for ( var m=0; m<arrMoviles.length && !bFound; m++ ){
                var oMovilInfo = newTracer.data.groups[iGroupID].movs[m];

                if ( oMovilInfo["mo_id"] == idMovil ){
                    oMovInfo = oMovilInfo;
                    bFound = true;
                }
            }
        }

        return oMovInfo;
    }
    else{
        return false;
    }
}

var ajaxResetGroupsInfo;
newTracer.resetGroupsInfo = function( fnc_callback ){
	$('#divScrollUltimosReportes').empty();
	$('#divScrollUltimosReportes').html('<img src="imagenes/ajax-loader.gif" style="margin:10px;" border="0">');
                
	if(typeof(ajaxResetGroupsInfo) != 'undefined'){
		ajaxResetGroupsInfo.abort();
	}
	url = "ajaxResetGroupsInfo.php";
    ajaxResetGroupsInfo = $.ajax({
        "url": dominio+url,
        "success": function(data, status, jqxhr){
            if ( typeof fnc_callback == 'function' ){
                fnc_callback();
            }
        }
    });
}


newTracer.listGroups = function(){
    var str = getDateTime() + "\n";

    for ( var i=0; i < newTracer.data.groupIDs.length; i++ ){
        var groupID = newTracer.data.groupIDs[i];
        //var expanded = $.inArray( groupID, newTracer.data.expanded_group_ids ) != -1;
		var expanded = getIsChecked(groupID, newTracer.data.expanded_group_ids);
    }
}

function getIsChecked(movilId, arrMovilesId){
	var resp = false;
	for(items in arrMovilesId){
		if(movilId == arrMovilesId[items]){
			resp = true;
		}
	}
	
	return resp;	
}


newTracer.deleteReferenciaSelect = function(){
	deleteMap_3(referenciaSelect['objet']); // Elimino el circulo, poligono y recta de las referencias seleccionadas
	referenciaSelect['objet'] = null;
	referenciaSelect['id'] = null;
	deleteMap(popupZonaInteres); // Elimino popup donde se recomienda punto de interes detectado en inteligencia
	popupZonaInteres = null;
}

newTracer.click_referencia = function (arrItem){
	newTracer.deleteReferenciaSelect();
	
	if(arrItem['tipo'] == 1){ //-- Es un movil --//
		if(typeof(arrItem['precision']) != 'undefined' && arrItem['precision'] > 0){//-- marcamos cobertura --//
			var mtrs = arrItem['precision']; //-- Valor de presición reportado por el equipo --//
		}
		else if(typeof(arrItem['mtrs']) == 'undefined'){//-- marcamos cobertura --//
			if(arrItem['id_evento'] == 1){//-- x GPS --//
				var mtrs = 200;}
			else{
				var mtrs = 500;} //-- x Antena --//
		}
		arrItem['color'] = '#2529AC';
	}
	
	var idMovil = arrItem['id'];
	var lat = arrItem['lat'];
	var lng = arrItem['lng'];
	var tipo = arrItem['tipo'];
	var mtrs = arrItem['mtrs']?arrItem['mtrs']:mtrs; 
	var coords = arrItem['coords'];
	var color = arrItem['color']?arrItem['color']:'#4b5de4';
	
	if(tipo == 1){// Es Circular (WP)
		referenciaSelect['objet'] = mapCircle(lat, lng, mtrs, arrItem['color']);
		referenciaSelect['id'] = idMovil;
		setMapObj(referenciaSelect['objet']);
		referenciaSelect['objet'].setZIndex(900); 		
	}
	else if(tipo == 2){// Es Poligono (Zona)
		var polyPoints = [];
		for(items in coords){
			var arr = [];
			arr['lat'] = coords[items].lat;
			arr['lon'] = coords[items].lng;
			polyPoints.push(arr);
		}
		referenciaSelect['objet'] = mapPolygon(polyPoints);
		referenciaSelect['id'] = idMovil;
		setMapObj(polygonLayer);
	}
	else if(tipo == 3){// Es Recta (Ruta)
		var polyPoints = [];
		for(items in coords){
			var arr = [];
			arr['lat'] = coords[items].lat;
			arr['lon'] = coords[items].lng;
			polyPoints.push(arr);
		}
		referenciaSelect['objet'] = mapPolyline(polyPoints);
		referenciaSelect['id'] = idMovil;
		setMapObj(lineLayer);
	}
	else if(tipo == 4){// Es Recta (Trafico)
		var polyPoints = [];
		for(items in coords){
			var arr = [];
			arr['lat'] = coords[items].lat;
			arr['lon'] = coords[items].lng;
			polyPoints.push(arr);
		}
		referenciaSelect['objet'] = mapPolyline(polyPoints);
		referenciaSelect['id'] = idMovil;
		setMapObj(lineLayer);
	}
}

newTracer.wpInteligentePopup = function (arrItem, obj){
	cerrarPopup();
	arrItem['id'] = (arrItem['id'] * -1);
	
	var btn = '<div id="contenedor">';
	btn+= '<span>'+arrLang['sugerencia_inteligencia']+'</span>';
	btn+= '<a href="javascript:newTracer.wpInteligenteAdd('+arrItem['id']+');" class="button extra-wide colorin">'+arrLang['si']+'</a>';
	btn+= '<a href="javascript:newTracer.wpInteligenteCancel('+arrItem['id']+');" class="button extra-wide colorin b-large">'+arrLang['no_mostrar']+'</a>';
	btn+= '</div>';
	
	abrirPopup(arrItem['lat'], arrItem['lng'], btn);
} 

newTracer.wpInteligenteAdd = function (id_referencia){
	newTracer.deleteReferenciaSelect();
	mostrarPopup('boot.php?c=abmReferencias&action=popupModWpInteligente&id_referencia='+id_referencia,505,410);
}

newTracer.wpInteligenteCancel = function (id_referencia){
	newTracer.deleteReferenciaSelect();
	$.ajax({
		async:false,
		cache:false,
		type: "POST",
		url: dominio+"ajaxReferenciaInteligente.php",
		data:({
			accion:'get-baja-referencia-recomendada',
			id_referencia:id_referencia
		}),
		success: function(msg){
			window.location.reload();
		}	
	});	
}

