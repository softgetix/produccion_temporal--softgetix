var g_iCantFilasAlertas = 0;
var g_bSoundPresent = false;
$(document).ready(function(){
	soundManager.setup( {
		"url": "swf/sm2/",
		"allowScriptAccess": "always",
		"useHTML5Audio": true,
		"preferFlash": false,
		
		"onready": function(){
			g_oSoundObject = soundManager.createSound({
				"id" : "mySound2",
				"url": "sounds/alertas/Sirena.wav",
				"volume": 50,
				"autoLoad": true,
				"autoPlay": false,
				"onload": function() {
					g_bSoundPresent = true;
				}
			});
		}
	});
	
	getAlertas();
	alertasIntervalHandler = setInterval(getAlertas, ALERTAS_REFRESH_INTERVAL);
});

$(document).bind("ready", function(){
    g_dlgConfirmarAlerta = $("#dlgConfirmarAlerta");
    
    g_dlgConfirmarAlerta.dialog({
        "autoOpen": false,
        "modal": true,
        "minWidth": 500,
        "close": function(){},
        "buttons":[
			{text: arrLang['confirmar'],
			click: function (){
				var id = $("#alerta_id").val(), ids = $("#alerta_ids").val(), motivo_conf = $("#cmbMotivoConfirmacion").val(), motivo_obs = $("#txtObs").val();
                confirmarAlerta( id, ids, motivo_conf, motivo_obs);
                $(this).dialog("close");
			}},
			{text: arrLang['cancelar'],
        	click: function () {
            	$(this).dialog("close");
        	}}
		]
    });
});

function getAlertas(){
	$.ajax({
		type: "POST",
		url: "boot.php?c=grillaAlertas",
		dataType:"json",
		data:({
			  	ajax:true,
				action:'actualizar-grilla',
				idevento: $('#idevento').val()
			}),
		success: function(msg){
			$('table#contenidoGrilla tbody').empty();
			var hasta = msg.length;
			var contenido = '';
			var classe = 'filaPar';
			
			if($('#idempresa').val() != 4835 && $('#idempresa').val() != 74){
				if(hasta > 0){
					if (g_bSoundPresent){
						g_oSoundObject.play();
					}
					else if($.browser.msie){
						$('#play-alarma-ie').html('<embed src="sounds/alertas/Sirena.wav" autostart="true" style="display:none;"></embed>');
					}	
				}
			}
			
			for(var i=0; i<hasta; i++){
				contenido = '';
				contenido+= '<tr class="'+classe+'" id="alerta_'+msg[i].id+'">';
				
				if($('#idempresa').val() == 4835 || $('#idempresa').val() == 74){
					contenido+= '<td align="center">'+msg[i].movil+'<br>'+msg[i].nombreEmpresa+'</td>';
					contenido+= '<td align="center">'+msg[i].generado+'</td>';
					contenido+= '<td align="center">'+((msg[i].alerta!=null)?msg[i].alerta:'')+' ('+msg[i].evento+')'+'</td>';
					contenido+= '<td align="center"><a target="_self" href=javascript:mostrarPopup("secciones/grillaAlertas.php?googleMaps=true&lat='+msg[i].lat+'&lon='+msg[i].lng+'",600,450)>'+msg[i].nomenclado+'</a></td>';
					//contenido+= '<td align="left">'+msg[i].nomenclado+'</td>';
					contenido+= '<td align="center">'+msg[i].tel+'</td>';
				}
				else{
					contenido+= '<td align="center"><a target="_self" href=javascript:mostrarPopup("secciones/abmAllInOneRastreo.php?idMovil='+msg[i].id_movil+'",600,450)>'+msg[i].movil+'</a><br>'+msg[i].nombreEmpresa+'</td>';
					contenido+= '<td align="center">'+calcularRumbo(msg[i].sentido, 1)+'</td>';
					contenido+= '<td align="center">'+msg[i].velocidad+'</td>';
					contenido+= '<td align="center">'+msg[i].recibido+'</td>';
					contenido+= '<td align="center">'+msg[i].generado+'</td>';
					contenido+= '<td align="center">'+((msg[i].alerta!=null)?msg[i].alerta:'')+' ('+msg[i].evento+')'+'</td>';
					contenido+= '<td align="center">'+msg[i].ocurrencias+'</td>';
					contenido+= '<td align="left">'+msg[i].nomenclado+'</td>';
					contenido+= '<td align="center">'+msg[i].tel+'</td>';
				}
				
				contenido+= '<td align="center"><a href="javascript:;" id="alert_'+i+'">'+arrLang['confirmar']+'</a></td>';
				contenido+= '</tr>';
				
				if(classe == 'filaImpar'){classe = 'filaPar';}else{classe = 'filaImpar';}
				
				$('table#contenidoGrilla tbody').append(contenido);
				$('a#alert_'+i)
					.attr({"data-rowid":i})
					.bind("click", function(ev){
						var indice = $(this).attr("data-rowid");
						confirmarAlerta_onClick(msg[indice].id, msg[indice].arr_ids);
       				});
			}
			g_iCantFilasAlertas = i;
			$("#cantidad_de_alertas").html(g_iCantFilasAlertas);
		}	
	});	
}

function confirmarAlerta_onClick(id, ids){
    $("#alerta_id").val(id);
    $("#alerta_ids").val(ids);
    g_dlgConfirmarAlerta.dialog("open");
}

function confirmarAlerta(filaid, ids, mot_conf, motivo_obs) {
    var url = "ajaxConfirmarAlerta.php";
    $.ajax({
        "url": dominio+url,
        "type": "post",
        "dataType":"json",
        "data":{
            "ids":ids,
            "motivo": mot_conf,
			"observacion": motivo_obs
        },
        "success": function(data){
            if (data.msg == true){
                $("#alerta_" + filaid).css('display', 'none');
                g_iCantFilasAlertas--;
                $("#cantidad_de_alertas").html( g_iCantFilasAlertas );
        	}
        }
	});
}



