function filtrarMoviles(){
	var grupo = document.getElementById("cmbGrupos").value;
	var filtro = document.getElementById("txtFiltroMovil").value;
	simple_ajax("ajaxObtenerMovilesGrupos.php?idGrupo=" + grupo+ "&filtro=" + filtro +"&p=0");
}

function imprimir(){
	var filtro = document.getElementById('txtFiltro').value;
	var url = 'boot.php?c=abmGrupoMoviles&method=export_prt&filtro='+filtro;
	window.open(url, '', 'width=800, height=500');
}


//R->	procesarTips(['txtFiltro','txtFiltroMovil','txtGrupo']);