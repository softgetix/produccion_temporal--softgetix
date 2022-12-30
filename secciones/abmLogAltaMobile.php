<style>
#mainInformes {margin-left:0px !important;}
#mainBoxLI{ overflow:hidden !important; height:auto !important;}
</style>
<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>">
	<div class="mainBoxLICabezera">
		<h1>Log Alta Mobile</h1>
	</div><!-- fin. mainBoxLICabezera -->
	<div id="mainBoxLI" >
	<br />
    <table cellpadding="0" cellspacing="0" border="0" class="widefat">
		<tr>
			<td valign="middle" height="20" align="right">E-Mail&nbsp;&nbsp;</td>
            <td><input type="text" name="email" id="email" style="width:250px;" onkeypress="return buscarLog(event)"/></td>
        </tr>
        <tr>
			<td valign="middle" height="20">&nbsp;</td>
            <td><a class="button extra-wide colorin" style="margin:0 0 10px 135px; width:90px;" href="javascript:getLogAltaMobile()">Buscar</a></td>
        </tr>    
	</table>
    <div id="mainInformes">
    <table width="100%" cellpadding="0" cellspacing="0" id="listadoRegistros" class="widefat" border="0" >
    	<thead>
        	<tr class="titulo">
            	<td width="20">&nbsp;</td>
                <td width="90" style="text-align:center !important">Hora</td>
                <td width="150">E-Mail</td>
                <td width="80" style="text-align:center !important">IMEI</td>
                <td width="40" style="text-align:center !important">Contrase&ntilde;a</td>
                <td width="200">Nombre Disp.</td>
                <td width="150">Nro Cel.</td>
                <td width="80" style="text-align:center !important">Code</td>
            </tr>
        </thead>
        <tbody id="ResultadoLog">
        	<tr class="filaPar">
				<td width="100%" colspan="8" style="text-align:center"><?=($_POST)?$lang->message->sin_resultados:'Ingrese el e-mail o parte del e-mail a filtrar'?></td>
			</tr>
        </tbody>
    </table>
    </div>
 	</div>
</form>