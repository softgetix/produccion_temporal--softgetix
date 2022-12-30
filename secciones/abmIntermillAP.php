<div id="main" class="sinColIzq">
    <form name="frm_<?php echo $seccion; ?>" id="frm_<?php echo $seccion ?>" action="?c=<?php echo $seccion ?>" method="post">
        <div class="esp">
            <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?php echo $operacion; ?>" />
            <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
            <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?php echo $seccion; ?>" />
        </div>

        <div id="intermil-arribos-partidas">
            <ul>
                <li><a href="#intermil-arribos"><span onclick="setTimeout('mostrar();',2000);">Arribos</span></a></li>
                <li><a href="#intermil-partidas"><span onclick="mostrar();">Partidas</span></a></li>
                <li><a href="#intermil-graficos"><span onclick="mostrar('grafico');">Intermil Online</span></a></li>
                <?php if ($objPerfil->validarSeccion('abmCruces')): ?>
                    <li class="generar-viaje"><a onclick="generarViaje();" href="#Viajes"><span>Generar viaje</span></a></li>
                <?php endif; ?>
            </ul>
            <div id="intermil-arribos">
                <div id="mainBoxLIA">
                </div>
            </div>

            <div id="intermil-partidas">
                <div id="mainBoxLI">
                </div>
            </div>
            
            <div id="intermil-graficos">
                <div id="mainBoxLI">
                </div>
            </div>

            <div id="intermil-viajes">
                <div id="mainBoxLI">
                </div>
            </div>
        </div>
        <div class="intermil-ap-links">
            <a target="_blank" class="link" href="http://www.localizar-t.com/trafico/admin/arribos.php?g=8">Arribos Tr&aacute;fico Online</a> |  
            <a target="_blank" class="link" href="http://www.localizar-t.com/trafico/admin/partidas.php?g=8">Partidas Tr&aacute;fico Online</a>
       </div>
        <div id="intermil-actualizacion">
            <p>Los datos se actualizar&aacute;n en <span id="intermil-timer"></span> minutos</p>
        </div>
    </form>
</div>
</div>