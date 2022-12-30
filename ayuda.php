<?php
//ORDEN: IDIOMA (VARIABLE DE SESION CON EL ID DEL IDIOMA), TIPO DE BOTONERA (LISTADO->'LI' O ALTA-MODIFICACION->'AM'), SECCION (EJ: abmUsuarios)
//echo $tipoBotonera." - ".$seccion;

if (isset($textoAyuda)){
	echo $textoAyuda;
}

$resp_ayuda = '';
$idioma = $_SESSION['language'];

switch ($idioma){
	case 'en':
	if($tipoBotonera=="LI" || $tipoBotonera=="LIasignacion2"){
			switch ($seccion){
				case 'abmReferencias':
					$resp_ayuda = '<div style="float:left; margin-top:5px;"><b>My places</b></div>';
					$resp_ayuda.= '<br><br>
					You can register and save your favorites places through this option.
					To create a new one use the "Add" function.
					My places can be used to create alerts.
					';
				break;
			}
		}elseif($tipoBotonera=="AM"){
			switch ($seccion){
				case 'abmReferencias':
					$resp_ayuda = '
					<b>To create or edit a place you must:</b><br/><br/>
					1.Give a name to the place you are creating.<br/><br/>
					2.Select a size. We recommend you medium if the cellphone has not GPS.<br/><br/>
					3.Complete with an address or locate the place through the mouse option.<br/><br/>
					4.Save the place you created.<br/><br/>
					';
				break;
			}
		}
		break;
	break;
	default:
		if($tipoBotonera=="LI" || $tipoBotonera=="LIasignacion2"){
			switch ($seccion){
				case 'abmClientes':
					$resp_ayuda = '
					Esta secci&oacute;n le permite visualizar el listado de clientes. A trav&eacute;s de la misma usted podr&aacute; dar de alta, modificar y borrar clientes.<br/><br/>
					Si desea buscar un cliente en particular le recomendamos utilizar el buscador que se encuentra en la parte superior del listado.
					';
					break;
				case 'abmGrupoMoviles':
					$resp_ayuda = '
					Esta secci&oacute;n le permite visualizar el listado de grupos de m&oacute;viles. A trav&eacute;s de la misma usted podr&aacute; dar de alta, modificar y borrar grupos de m&oacute;viles.<br/><br/>
					Si desea buscar un grupo de m&oacute;viles en particular le recomendamos utilizar el buscador que se encuentra en la parte superior del listado.<br/><br/>
					';
					break;
				case 'abmTraduccionesReportes':
					$resp_ayuda = '
					Esta secci&oacute;n le permite visualizar el listado de traducciones de reportes. A trav&eacute;s de la misma usted podr&aacute; dar de alta, modificar y borrar traducciones de reportes.<br/><br/>
					Si desea buscar una traduccion de reporte en particular le recomendamos utilizar el buscador que se encuentra en la parte superior del listado.
					';
					break;
				case 'abmEquipos':
					$resp_ayuda = '
					Esta secci&oacute;n le permite visualizar el listado de equipos. A trav&eacute;s de la misma usted podr&aacute; dar de alta, modificar y borrar equipos.<br/><br/>
					Si desea buscar un equipo en particular le recomendamos utilizar el buscador que se encuentra en la parte superior del listado.
					';
					break;
				case 'abmMarcaEquipos':
					$resp_ayuda = '
					Esta secci&oacute;n le permite visualizar el listado de marcas de equipos. A trav&eacute;s de la misma usted podr&aacute; dar de alta, modificar y borrar marcas de equipos.<br/><br/>
					Si desea buscar una marca de equipo en particular le recomendamos utilizar el buscador que se encuentra en la parte superior del listado.
					';
					break;
				case 'abmModeloEquipos':
					$resp_ayuda = '
					Esta secci&oacute;n le permite visualizar el listado de modelos de equipos. A trav&eacute;s de la misma usted podr&aacute; dar de alta, modificar y borrar modelos de equipos.<br/><br/>
					Si desea buscar un modelo de equipo en particular le recomendamos utilizar el buscador que se encuentra en la parte superior del listado.
					';
					break;
				case 'abmConductores':
					$resp_ayuda = '<div style="float:left; margin-top:5px;"><b>Conductores</b></div>';
					/*$resp_ayuda.= '
					<a href="https://www.youtube.com/watch?v=y15_A126-Tg&feature=youtu.be" target="_blank" style="float:right;" title="Ver video tutorial">
                    	<img src="imagenes/videos_youtube.png" />
                    </a>
					<br>';*/
					$resp_ayuda.= '<br><br>Esta secci&oacute;n le permite visualizar el listado de choferes. A trav&eacute;s de la misma usted podr&aacute; dar de alta, modificar y borrar choferes.<br/><br/>
					Si desea buscar un chofer en particular le recomendamos utilizar el buscador que se encuentra en la parte superior del listado.
					';
					break;
				case 'abmDefinicionesEntradas':
					$resp_ayuda = '
					Esta secci&oacute;n le permite visualizar el listado de definiciones de entradas. A trav&eacute;s de la misma usted podr&aacute; dar de alta, modificar y borrar definiciones de entradas.<br/><br/>
					Si desea buscar una definicion de entrada en particular le recomendamos utilizar el buscador que se encuentra en la parte superior del listado.
					';
					break;
				case 'abmReferencias':
					$resp_ayuda = '<div style="float:left; margin-top:5px;"><b>Geozonas / Lugares</b></div>';
					/*$resp_ayuda.= '
					<a href="https://www.youtube.com/watch?v=S0fJBi_YIIw&feature=youtu.be" target="_blank" style="float:right;" title="Ver video tutorial">
                    	<img src="imagenes/videos_youtube.png" />
                    </a>
					<br>';*/
					$resp_ayuda.= '<br><br>
					Sus geozonas / lugares son puntos de inter&eacute;s que pueden ser visualizados en el sistema.<br>
					Utilice el bot&oacute;n "Agregar" para configurar una nueva geozona / lugar.<br>
					Las geozonas / lugares pueden ser utilizadas en sus alertas.
					';
					break;
				case 'abmHubs':
					/*$resp_ayuda = '
					<div style="float:left; margin-top:5px;"><b>Hubs</b></div>
					<a href="https://www.youtube.com/watch?v=LdFivH0_-BY&list=UUxo_nJH__JGmCARr9jIYKlw" target="_blank" style="float:right;" title="Ver video tutorial">
                    	<img src="imagenes/videos_youtube.png" />
                    </a>
					<br><br>
					';*/
				break;				
			}
		}elseif($tipoBotonera=="AM"){
			switch ($seccion){
				case 'abmClientes':
					$resp_ayuda = '
					Esta secci&oacute;n le permite crear/modificar un cliente. <br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				case 'abmGrupoMoviles':
					$resp_ayuda = '
					Esta secci&oacute;n le permite crear/modificar un grupo de moviles. <br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				case 'abmTraduccionesReportes':
					$resp_ayuda = '
					Esta secci&oacute;n le permite crear/modificar una tradcci&oacute;n de reporte. <br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				case 'abmEquipos':
					$resp_ayuda = '
					Esta secci&oacute;n le permite crear/modificar un equipo. <br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				case 'abmMarcaEquipos':
					$resp_ayuda = '
					Esta secci&oacute;n le permite crear/modificar una marca de equipo. <br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				case 'abmModeloEquipos':
					$resp_ayuda = '
					Esta secci&oacute;n le permite crear/modificar un modelo de equipo. <br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				case 'abmConductores':
					$resp_ayuda = '
					Esta secci&oacute;n le permite crear/modificar un chofer. <br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				case 'abmDefinicionesEntradas':
					$resp_ayuda = '
					Esta secci&oacute;n le permite crear/modificar una definicion de entrada. <br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				case 'abmReferencias':
					$resp_ayuda = '
					<b>Para crear o modificar una geozona / lugar usted debe:</b><br/><br/>
					1.Asignarle el nombre con el que desea usarla en el sistema.<br/><br/>
					2.Asociarla a una categor&iacute;a disponible (le ayudar&aacute; a buscarla en todo momento).<br/><br/>
					3.Asignarle un tama&ntilde;o: Se recomienda utilizar mediana o grande si el dispositivo no tiene GPS o si el m&oacute;vil no se dentendr&aacute; en esta zona.<br/><br/>
					4.Localizar la referencia en el mapa a trav&eacute;s de "direcci&oacute;n en mapa". Tenga presente que podr&aacute; modificar tambi&eacute;n la ubicaci&oacute;n arrastrando con el mouse la zona.<br/><br/>
					Recuerde una vez terminada la creaci&oacute;n o modificaci&oacute;n oprimir "guardar".
					';
					break;
				case 'cuenta':
					$resp_ayuda = '
					Esta secci&oacute;n le permite modificar:<br/><br/>
					- Su contrase&ntilde;a. <br/>
					- Su pregunta y respuesta secreta. <br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				/*
				case 'abmConfMoviles':
					$resp_ayuda = '
					Esta secci&oacute;n le permite modificar:<br/><br/>
					- La velocidad m&aacute;xima del m&oacute;vil. <br/>
					- El &iacute;cono identificador del m&oacute;vil. <br/><br/>
					Estos cambios se veran reflejados en el apartado de "Rastreo".<br/><br/>
					Los campos marcados con un * (asterisco) deber&aacute;n ser completados de forma obligatoria.<br/><br/>
					';
					break;
				*/	
				case 'envioComandos':
					$resp_ayuda = '
					Esta secci&oacute;n le configurar uno o varios equipos remotamente.<br/><br/>
					';
					break;
			}
		}
		break;
}

return $resp_ayuda;