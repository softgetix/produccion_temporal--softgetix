var campoId = 'gr_id',
columnas = [{
id:'gr_nombre',
width:130,
filtro:1,
orden:1,
link_modificar:1
},{
id:'comandos',
width:'auto',
filtro:1
}
];
$(document).ready(function(){
	buscar('ajax_json','abmGruposComandos');
});