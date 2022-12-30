/*global $,buscar*/
var campoId = 'co_id',
columnas = [
{
id:'co_nombre',
width:130,
filtro:1,
orden:1,
link_modificar:1
},{
id:'co_tipo',
width:100,
orden:1,
opciones:{
	0:'<img src="imagenes/espera.png" width="14" height="14" alt="Espera evento" title="Espera evento"/>',
	1:'<img src="imagenes/envia_espera.png" width="14" height="14" alt="Env\u00EDa y espera respuesta" title="Env\u00EDa y espera respuesta"/>'
}
},{
id:'co_codigo',
width:300,
filtro:1,
orden:1
},{
id:'co_instrucciones',
width:'auto',
filtro:1
}];
$(document).ready(function(){
	buscar('ajax_json','abmComandos');
});