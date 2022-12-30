<?php if($_GET['googleMaps']){
    $rel = '../';?>
    <script type="text/javascript" src="<?=$rel?>js/jquery.tools.js"></script>
    <script type='text/javascript' src='<?=$rel?>js/funciones.js'></script>
    <script type='text/javascript' src='<?=$rel?>js/jquery.1.7.1.min.js'></script>
    <script type='text/javascript' src='<?=$rel?>js/openLayers/OpenLayers.js'></script>
    <script type='text/javascript' src='<?=$rel?>js/defaultMap.js'></script>

    <div id="mapa"></div>
    <script language="javascript">
        var lat= <?=$_GET['lat']?$_GET['lat']:0?>;
        var lng = <?=$_GET['lon']?$_GET['lon']:0?>;
        var zoom = 16;
        $( document ).ready(function() {
            CrearMapa('mapa');
            mapSetZoom(zoom);
            mapSetCenter(lat,lng);
            
            var arr = [];
            arr['lat'] = lat;
            arr['lng'] = lng;
            arr['icono'] = '/imagenes/iconos/iconos/iconos-02.png';
            var marker = mapMarker(arr);
            setMap(marker,false);
        });

        function cerrarPopup(){
            <?php if($_GET['referer']){?>
                location.href = '<?=$_SERVER['HTTP_REFERER']?>';
            <?php }
            else{?>	
                window.parent.cerrarPopup();
            <?php }?>
        }
    </script>
    <?php
    exit;
}
?>

<script type="text/javascript">
    arrLang['confirmar'] = '<?=$lang->botonera->confirmar?>';
	arrLang['cancelar'] = '<?=$lang->botonera->cancelar?>';
</script>
<div id="main" class="sinColIzq">
<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<div class="mainBoxLICabezera">
		<h1><?=$lang->system->alertas?> (<span id="cantidad_de_alertas">0</span>)</h1>
	</div><!-- fin. mainBoxLICabezera -->
   <div id="mainBoxLI">
    <div id="Filtro" style="margin: 10px 0 0 20px;">
        <label>Filtrar por Alerta:</label>
        <select name="idevento" id="idevento">
            <option value="">Selecione</option>
            <option value="1">Detenido + 15 minutos fuera de zonas autorizadas</option>
            <option value="2">Fuera de ruta segura configurada</option>
            <option value="3">Cambio de rumbo</option>
            <option value="4">Equipo con GPS sin reporte Online</option>
            <option value="5">Equipo con GPS sin reportar +24 hs</option>
            <option value="6">Equipo sin GPS vinculado</option>
        </select>
    </div>
	<br />  
    	<table id="contenidoGrilla" cellpadding="0" cellspacing="0" border="0" class="widefat">
		<thead>
        	<tr class="titulo">
        		<?php if($_SESSION['idEmpresa'] == 4835 || $_SESSION['idEmpresa'] == 74){?>
                    <td width="10%"><?=$lang->system->movil?></td>
                    <td width="10%">Detectado el</td>
                    <td width="25%"><?=$lang->system->alertas?></td>
                    <td width="20%"><?=$lang->system->ubicacion?></td>
                    <td width="20%">Chofer</td>
                    <td width="5%" >-</td>
                <?php } else{ ?>
                    <td width="10%"><?=$lang->system->movil?></td>
                    <td width="5%" ><?=$lang->system->sentido?></td>
                    <td width="5%" ><?=$lang->system->velocidad?></td>
                    <td width="10%"><?=$lang->system->recibido?></td>
                    <td width="10%"><?=$lang->system->generado?></td>
                    <td width="25%"><?=$lang->system->alertas?></td>
                    <td width="5%" ><?=$lang->system->ocurrencias?>&nbsp;&nbsp;</td>
                    <td width="20%"><?=$lang->system->ubicacion?></td>
                    <td width="20%"><?=$lang->system->telefono?></td>
                    <td width="5%" >-</td>
                <?php }?>    
        	</tr>
        </thead>
        <input type="hidden" id="idempresa" value="<?=$_SESSION['idEmpresa']?>" />
        
        <tbody>
        	<!-- datos dinamicos -->
        </tbody>
        </table>
    </div>
</form>
</div>
<div id="play-alarma-ie"></div><!-- necesario para reproducir alarmas en IE -->

<div id="dlgConfirmarAlerta" title="Confirmar alerta" style="display:none">
    <table>
        <tr>
            <td style="white-space: nowrap; line-height:30px">
                <?=$lang->message->msj_rastreo_alertas?>:
            </td>
            <td>
                <select id="cmbMotivoConfirmacion"><?php
                    foreach($arrMotivosConfirmacion as $motivo ){
                        echo '<option value="'.$motivo['mc_id'].'">'.$motivo['mc_descripcion'].'</option>';
                    }?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="white-space: nowrap; line-height:30px">Observaciones</td>
            <td><input type="text" id="txtObs" autocomplete="off"></td>
        </tr>
    </table>
    
    <input type="hidden" id="alerta_id" value="" />
    <input type="hidden" id="alerta_ids" value="" />
</div>