<ul class="Barra_Menu">
	<?php foreach($permitido as $wizard){ ?>   
	<li  class="helperbutton" id="boton" onclick="cargarCapa(<?=$wizard['wi_id']; ?>)" class="nodeco"><?=encode($wizard['wi_descripcion'])?></li>
    <?php } ?>
</ul>
<div id="capaframe" class="capaframe" style="overflow:hidden;">
			
	<div "padding:20px !important;">
		<p style="font-weight:bold;font-size:16pt !important;font-family:arial !important;">Bienvenido al apartado de AYUDA.</p>
		<p style="font-size:12pt !important;font-family:arial !important;width:70%;margin:auto;padding:15px">
		En esta secci&oacute;n Ud. encontrar&aacute; una gu&iacute;a r&aacute;pida para utilizar el sistema y tambi&eacute;n podr&aacute; acceder a Preguntas Frecuentes.
		<br>
		La AYUDA est&aacute; organizada por secciones. Para acceder a cada una de ellas debe hacerlo haciendo click en la barra superior.	
		<br>&nbsp;
		</p>
	</div>
	
</div>