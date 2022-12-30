/*global $*/
var sortitems=1, filtroBoxes={};
//@param {string} box
function sortD(box){
//de adentro hacia afuera: saco los option del list, los ordeno con sort y los vuelvo a pegar
	$('#'+box).append($('#'+box+' option').detach().get().sort(function(a,b){return a.value>b.value;}));
}
/**
 *@param {string} fbox
 *@param {string} tbox
 */
function move(fbox,tbox){
	$('#'+tbox).append($('#'+fbox+' option:selected'));
	if (sortitems){sortD(tbox);}
}
//@param {string} name
function actualizarHidden(name){
	var serializado='';
	$('#'+name+' option').each(function(){
		serializado += $(this).val()+',';
	});
	$('#hid_'+name).val(serializado);
}
$(document).ready(function(){
	$('.lstDer option[value="dummy"],.lstDer option[value="0"]').remove();
	$('.btnIzqT').click(function(){
		var tbox=$('.lstIzq',$(this).parent().parent()).attr('id'),
			fbox=$('.lstDer',$(this).parent().parent()).attr('id');
		$('#'+fbox+' option').attr('selected','selected');
		move(fbox,tbox);
		$('#hid_'+fbox).val('');
	});
	$('.btnIzq').click(function(){
		var tbox=$('.lstIzq',$(this).parent().parent()).attr('id'),
			fbox=$('.lstDer',$(this).parent().parent()).attr('id');
		move(fbox,tbox);
		actualizarHidden(fbox);
	});
	$('.btnDer').click(function(){
		var fbox=$('.lstIzq',$(this).parent().parent()).attr('id'),
			tbox=$('.lstDer',$(this).parent().parent()).attr('id');
		move(fbox,tbox);
		actualizarHidden(tbox);
	});
	$('.btnDerT').click(function(){
		var fbox=$('.lstIzq',$(this).parent().parent()).attr('id'),
			tbox=$('.lstDer',$(this).parent().parent()).attr('id');
		$('#'+fbox+' option').attr('selected','selected');
		move(fbox,tbox);
		actualizarHidden(tbox);
	});
	$('.lstIzq').dblclick(function(){
		var tbox=$('.lstDer',$(this).parent().parent()).attr('id');
		move(this.id,tbox);
		actualizarHidden(tbox);
	});
	$('.lstDer').dblclick(function(){
		var tbox=$('.lstIzq',$(this).parent().parent()).attr('id'),
			fbox=this.id;
		move(fbox,tbox);
		actualizarHidden(fbox);
	});

	$('.txtFiltroTransfer').keyup(function(){
		debug.log("filtrando...");
		function sortOrder(a,b){return parseInt(a.value,10)-parseInt(b.value,10);};


		var comp;

		if ( $(this).val() == $(this).data("tip") )
		{
			comp = "";
		}
		else
		{
			comp=$(this).val().toLowerCase();
		}
		var tipo=this.id.replace('txtFiltro',''),
		sel='#lst'+tipo+' option';

		if (typeof filtroBoxes[tipo] === 'undefined'){
			filtroBoxes[tipo]=[];
		}

		$.merge(filtroBoxes[tipo],$(sel).detach().get());
		filtroBoxes[tipo].sort(sortOrder);
		$('#lst'+tipo).append(filtroBoxes[tipo]);
		filtroBoxes[tipo]=$(sel).filter(function(){return (this.text.toLowerCase().indexOf(comp) === -1)?true:false;}).detach();
		$(this).trigger('boxes.filterEnd');//le doy el pie para que se ejecuten filtros adicionales
	});
});


	//R-> procesarTips(['txtFiltroUsuarios','txtFiltroMoviles']);