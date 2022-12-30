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

	$('.pv_send_sms').live("click", function(){
		$("#hidMessage").val($(this).attr('attrmessage'));
		$("#hidNumber").val($(this).attr('attrnumber'));
		$("#hidTitle").val($(this).attr('attrtitle'));
		$("#hidPath").val($(this).attr('attrurl'));
		$("#hidIdViaje").val($(this).attr('attridviaje'));
		$("#hidAdicional").val($("#txtadicional_"+$(this).attr('attrkey')).val());
	});
});
