$(document).ready(function() {
    //R-> procesarTips(['txtFiltro','txtNombre','txtApellidos','txtUsuario','txtPreguntaSecreta','txtRespuestaSecreta','txtMailAlerta','txtMailContacto','txtTelefono','txtHardKey']);
	
	
	/*$("#txtExpiracion").datepicker({
		showOn: "button",
		buttonImage: "imagenes/calendario/bul_cal.gif",
		buttonImageOnly: true,
		dateFormat: 'yy/mm/dd'
	});*/
    $('.date').datepicker({
    	minDate: '-1'
    });
});

function filtro(id){
    if (document.getElementById("filtro"+id)) {
        if (document.getElementById("filtro"+id).style.display === "none") {
            document.getElementById("filtro"+id).style.display = "block";
        } else {
            document.getElementById("filtro"+id).style.display = "none";
        }
    }
}

function filtrar(id,ext,total,chk,chk2,ext2) {
    var disp = "",
    iCont = 0,
    color = "",
    i;

    for (i = 0;i < total;i++) {
        if (document.getElementById("tr_"+i)) {
            if (document.getElementById(ext+"_"+i)) {
                if (document.getElementById(ext+"_"+i).innerHTML === id) {
                    if (document.getElementById(chk+id).checked && document.getElementById(chk2 + document.getElementById(ext2+"_"+i).innerHTML).checked) {
                        disp = "block";
                    } else {
                        disp = "none";
                    }
                    document.getElementById("tr_"+i).style.display = disp;
                }

                if (document.getElementById("tr_"+i).style.display === "block") {
                    if (!iCont%2) {
                        color = "#eaeaea";
                    } else {
                        color = "#ffffff";
                    }
                    iCont++;
                    document.getElementById("tr_"+i).style.backgroundColor = color;
                }
            }
        }
    }
}

function cumpleCriterioBusqueda(i){
    var validaBusqueda = true;
    var filtro;
    var valor;
    for (var c = 0;validaBusqueda && c < columnas.length;c++){
        if (columnas[c]['filtro'] == 1) {
            filtro = document.getElementById("txtFiltro_" + columnas[c]['id']);
            if (filtro != false && filtro.value.length>0) {
                valor=registros[i][columnas[c]['id']]+'';
                if(columnas[c]['opciones']){
                    valor=columnas[c]['opciones'][valor]+'';
                }
                if (strpos(valor.toLowerCase(),filtro.value.toLowerCase()) === -1) {
                    validaBusqueda = false;
                }
            }
        }
    }
    return validaBusqueda;
}

function strpos (haystack, needle) {
    var i = (haystack + '').indexOf(needle,0);
    return i === -1 ? -1 : i;
}

function listar(id){
    var filtro = $('#txtFiltro_co_nombre').val()
    var validaBusqueda;
    $('#mainTable tr').each(function() {
        validaBusqueda = true;
        $.each(this.cells, function(){
            if (this.cellIndex == 2) {
                if (strpos($(this).val().toLowerCase(),filtro.toLowerCase()) === -1) {
                    validaBusqueda = false;
                }
            }
			
        });
        if (validaBusqueda) {
            $(this).css({
                'display':'none'
            })
        } else {
            $(this).css({
                'display':'block'
            })
        }
    //si no aplica, oculto
    });
}
var passAnterior="";
function limpiarPass(){
    var txtPass = document.getElementById("txtPass"),
    txtPassRepetido = document.getElementById("txtRepetirPass"),
    chkPass = document.getElementById("chkCambiarPass");

    if(chkPass.checked){
        if(!passAnterior){
            passAnterior = txtPass.value;
        }
        txtPass.disabled = false;
        txtPassRepetido.disabled = false;
        txtPass.value = "";
        txtPassRepetido.value = "";
        txtPass.focus();
    }else{
        if(passAnterior){
            txtPass.value = passAnterior;
        }
        txtPass.disabled = true;
        txtPassRepetido.disabled = true;
        txtPassRepetido.value = "";
    }
}

var usuarioLogComo = 0;
function abrirIngresarComo(idUsuario){
    document.getElementById("fondoNegro").style.display = "block";
    document.getElementById("contenedorIngresarComo").style.display = "block";
    document.getElementById("txtPass").text = "";
    document.getElementById("txtPass").focus();
    usuarioLogComo = idUsuario;
}

function cerrarIngresarComo(){
    document.getElementById("fondoNegro").style.display = "none";
    document.getElementById("contenedorIngresarComo").style.display = "none";
    usuarioLogComo = 0;
}

function ingresarComo() {
    var form,input,
    pass = document.getElementById("txtPass").value;

    pass = jQuery.trim(pass);
    if(!pass){
        cerrarIngresarComo();
        return alert(arrLang['password_vacio']);
    }
    form = document.createElement('form');
    form.method = 'post';
    var arrpath = (document.location.pathname).split('/');
	form.action = '/'+arrpath[1]+'/ingresar_como.php';

    input = document.createElement('input');
    input.setAttribute('name', 'ingresar_como');
    input.setAttribute('value', usuarioLogComo);
    input.setAttribute('type', 'hidden');
    form.appendChild(input);

    input = document.createElement('input');
    input.setAttribute('name', 'hidPass');
    input.setAttribute('value', pass);
    input.setAttribute('type', 'hidden');
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

function pressEnter() {
    if(event.keyCode == 13){
        ingresarComo();
    }
}

function auto_ingresar(usr,pass){
	 var form,input,
    form = document.createElement('form');
    form.method = 'post';
    var arrpath = (document.location.pathname).split('/');
	form.action = '/'+arrpath[1]+'/ingresar_como.php';

    input = document.createElement('input');
    input.setAttribute('name', 'ingresar_como');
    input.setAttribute('value', usr);
    input.setAttribute('type', 'hidden');
    form.appendChild(input);

    input = document.createElement('input');
    input.setAttribute('name', 'hidPassDirect');
    input.setAttribute('value', pass);
    input.setAttribute('type', 'hidden');
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}