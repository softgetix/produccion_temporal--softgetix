$(document).ready(function() {
			
	$('.pv_send_message').live("click", function(){
		var idviaje = $(this).attr('attrIdViaje');
		mostrarPopup('boot.php?c=abmViajesDeliveryPlayaVirtual&action=sendmessage&idviaje='+idviaje,740,350);
	});

	$('.pv_view_history').live("click", function(){
		var idviaje = $(this).attr('attrIdViaje');
		mostrarPopup('boot.php?c=abmViajesDeliveryPlayaVirtual&action=viewhistory&idviaje='+idviaje,740,350);
	});

	$('.pv_view_info_aditional').live("click", function(){
		var idviaje = $(this).attr('attrIdViaje');
		mostrarPopup('boot.php?c=abmViajesDeliveryPlayaVirtual&action=viewinfoaditional&idviaje='+idviaje,740,350);
	});

	$('.pv_attach_file').live("click", function(){
		var idviaje = $(this).attr('attrIdViaje');
		var iddestino = $(this).attr('attrIdDestino');
		mostrarPopup('boot.php?c=abmViajesDeliveryPlayaVirtual&action=attachfile&idviaje='+idviaje+'&iddestino='+iddestino,740,150);
	});

	$('.pv_authorize_entry').live("click", function(){
		var idviaje = $(this).attr('attrIdViaje');
		var iddestino = $(this).attr('attrIdDestino');
		mostrarPopup('boot.php?c=abmViajesDeliveryPlayaVirtual&action=authorize_entry&idviaje='+idviaje+'&iddestino='+iddestino,440,170);
	});

	$('.pv_send_sms').live("click", function(){
		$("#hidMessage").val($(this).attr('attrmessage'));
		$("#hidNumber").val($(this).attr('attrnumber'));
		$("#hidTitle").val($(this).attr('attrtitle'));
		$("#hidPath").val($(this).attr('attrurl'));
		$("#hidIdViaje").val($(this).attr('attridviaje'));
		$("#hidAdicional").val($("#txtadicional_"+$(this).attr('attrkey')).val());
	});

	$('.pv_facturado').live("click", function(){
		if(typeof($(this).attr('checked')) != 'undefined'){
			var $fact = 1;
		}
		else{
			var $fact = 0;
		}

		$.ajax({
			type: "POST",
			url: "ajaxViajes.php",
			dataType: "json",
			data:({
				accion:'change-facturado',
				idviaje:$(this).attr('attrIdViaje'),
				facturado:$fact
			}),
			success: function(data){}
		});
	});
});
