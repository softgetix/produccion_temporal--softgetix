function filtrarUsuarios(flagBtn){
	var filtro = document.getElementById("txtFiltroUsuario").value;
	simple_ajax("ajaxObtenerEquiposUsuariosFiltro.php?filtro=" + filtro +"&p=0");
}

function obtenerEquiposAsignados(){
	equiposAsignados = document.getElementById("cmbEquiposAsignados");
	var equipos="";
	for(i=0;i < equiposAsignados.options.length;i++){
		if(equipos==""){
			equipos = equiposAsignados.options[i].value;
		}else{
			equipos += "," + equiposAsignados.options[i].value;
		}
	}	
	return equipos;
}

function obtenerComandosAsignados(){
	comandosAsignados = document.getElementById("cmbComandos");
	var comandos="";
	for(i=0;i < comandosAsignados.options.length;i++){
		if(comandos==""){
			comandos = comandosAsignados.options[i].value;
		}else{
			comandos += "," + comandosAsignados.options[i].value;
		}
	}	
	return comandos;
}

function agregarComando(errorSinComando){
	var txtComando = 	document.getElementById("txtComando");
	if(txtComando.value){
		var listaComandos = 	document.getElementById("cmbComandos");
		try {
		 listaComandos.add(new Option(txtComando.value, txtComando.value),null); // standards compliant; doesn't work in IE
		}
		catch(ex) {
		 listaComandos.add(new Option(txtComando.value, txtComando.value)); // IE only
		}
		//POSICIONO EL SCROLL AL FINAL
		if(listaComandos.scrollTop != listaComandos.scrollHeight) listaComandos.scrollTop = listaComandos.scrollHeight;
		//----------------------------
		txtComando.value="";
		txtComando.focus();
	}else{
		alert(errorSinComando);	
	}
}

function quitarComando(errorSinComandoSeleccionado){
	var listaComandos = 	document.getElementById("cmbComandos");
	if(listaComandos){
		var indexSeleccionado = listaComandos.selectedIndex;
		listaComandos.options[listaComandos.selectedIndex]=null;
		var indexNuevo = indexSeleccionado-1
		if(indexNuevo > 0) listaComandos.selectedIndex = indexNuevo;
		if(indexNuevo <= 0) listaComandos.selectedIndex = 0;
	}else{
		alert(errorSinComandoSeleccionado);	
	}
}

function enviarComandos(mensajeAlerta){
	clearTimeout(myTime);
	var equipos = obtenerEquiposAsignados();
	var comandos = obtenerComandosAsignados();
	if(equipos && comandos){
		simple_ajax("ajaxAgregarComandos.php?equipos=" + equipos + "&comandos=" + comandos + "&p=0");
	}else{
		alert(mensajeAlerta);	
	}
}

var myTime;
function actualizarTablaEnvios(){
	clearTimeout(myTime);
	simple_ajax("ajaxActualizarTablaEnvioComandos.php?p=0");
}

setTimeout('actualizarTablaEnvios();',2000);

function scrollear(){
	var tablaEstado = document.getElementById('divTablaEstado');
	if(tablaEstado) tablaEstado.scrollTop = parseInt(tablaEstado.scrollHeight);
}

	//R-> procesarTips(['txtFiltro','txtComando','txtFiltroUsuario']);