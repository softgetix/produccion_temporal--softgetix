sortitems=1;
function Move(fbox,tbox){
	$('#'+tbox).append($('#'+fbox+' option:selected'));
	if (sortitems) SortD(tbox);
}
function SortD(box){
//de adentro hacia afuera: saco los option del list, los ordeno con unique y los vuelvo a pegar
	$('#'+box).append($.unique($('#'+box+' option').detach().get()));
}

function actualizarHidden(){
	var serializado='';
	$('#lstAsignados option').each(function(){
		serializado += $(this).val()+',';
	});
	$('#hidSerializado').val(serializado);
}
$(document).ready(function(){
	$('#btnIzqT').click(function(){
		$('#lstAsignados option').attr('selected','selected');
		Move('lstAsignados','lstDisponibles');
		$('#hidSerializado').val('');
	});
	$('#btnIzq').click(function(){
		Move('lstAsignados','lstDisponibles');
		actualizarHidden();
	});
	$('#btnDer').click(function(){
		Move('lstDisponibles','lstAsignados');
		actualizarHidden();
	});
	$('#btnDerT').click(function(){
		$('#lstDisponibles option').attr('selected','selected');
		Move('lstDisponibles','lstAsignados');
		actualizarHidden();
	});
	$('#lstAsignados').dblclick(function(){
		Move('lstAsignados','lstDisponibles');
		actualizarHidden();
	});
	$('#lstDisponibles').dblclick(function(){
		Move('lstDisponibles','lstAsignados');
		actualizarHidden();
	});
});
//R-> procesarTips(['txtFiltro','txtNombre']);