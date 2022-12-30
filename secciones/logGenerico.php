<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<div class="mainBoxLICabezera">
		<h1>Log Generico</h1>
	</div><!-- fin. mainBoxLICabezera -->
	<div id="mainBoxLI">
	<br />
    <table cellpadding="0" cellspacing="0" border="0" class="widefat">
		<tr>
			<td valign="middle" height="20" align="right">Buscar&nbsp;&nbsp;</td>
            <td><input type="text" name="buscar" id="buscar" style="width:250px;"/></td>
        </tr>
        <tr>
			<td valign="middle" height="20" align="right">Fuente&nbsp;&nbsp;</td>
            <td>
            	<select name="fuente" id="fuente" style="width:250px;" />
					<option value="SAP">SAP</option>
					<option value="Satelital">Satelital</option>
					<option value="Sistema">Sistema</option>
                </select>
            </td>
        </tr>
        <tr>
			<td valign="middle" height="20" align="right">Fecha&nbsp;&nbsp;</td>
            <td><input type="text" name="fecha" id="fecha" class="date" value="<?=date('d-m-Y')?>" style="width:90px;" /></td>
        </tr>	
        <tr>
			<td valign="middle" height="20">&nbsp;</td>
            <td><a class="button extra-wide colorin" style="margin:0 0 10px 135px; width:90px;" href="javascript:buscar()">Buscar</a></td>
        </tr>    
	</table>
    <!-- Inicio. solapas -->
    <div class="solapas gum clear">
		<div class="float_l" style=" line-height:30px; margin-left:20px;" id="datos-movil"></div> 
        <div class="contenido clear" style="height:100%">
            <div id="listado-log" class="contenido-solapa">
            	<!-- -->
                <div id="ResultadoLog"><span class="filaPar" style="text-align:center">Iniciar búsqueda</span></div>    
                <!-- -->
            </div>
        </div>
	</div>
    <!-- Fin. solapas -->    
	</div>
</form>
<script type="text/javascript">
$(function() {	
	$(".date").live("focusin", function() { 
       $(this).datepicker({
            /*onSelect: function(objDatepicker){
				var fecha = $(this).val().replace('/','-');
                var fecha = fecha.replace('/','-');
				$(this).val(fecha);
            }*/
        });
    });
});	

function buscar(){
	var buscar = $('#buscar').val();
	var fecha = $('#fecha').val();
	var fuente = $('#fuente').val();
	
	if(buscar == ''){
		alert('Ingrese el texto a buscar');
	}
	else if(fecha == ''){
		alert('Ingrese la fecha deseada');	
	}
	else{
		$('#ResultadoLog').html('<img src="imagenes/ajax-loader.gif" >');
		
		$.ajax({
			async:false,
			cache:false,
			type: "POST",
			url: "controladores/logGenericoControlador.php",
			data:({
				accion:'getInfo',
				buscar:buscar,
				fecha:fecha,
				fuente:fuente
			}),
			success: function(msg){
				var arr = jQuery.parseJSON(msg);
				$('#ResultadoLog').html(arr.resp);
			},	
			beforeSend:function(){},
			error:function(objXMLHttpRequest){}	
		});
	}
}
</script>