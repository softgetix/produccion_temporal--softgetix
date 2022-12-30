<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");


include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
##-- validar movil --##
include "includes/caja_negra.php";
caja_negra($_GET['idMovil'],'moviles',0,$objSQLServer,0);
##-- --##
include "includes/validarUsuario.php";
include "clases/clsNomenclador.php";
require_once('includes/tipomovil.inc.php');

require_once 'includes/navbar_permisos.php';
require_once 'clases/clsPerfiles.php';
$objPerfil = new Perfil($objSQLServer);

$arrGrayedEvents = array(
    980, // Falta de reporte
    987, // Falta de reporte de mas de 24hs
    //999, // Evento no definido
);


$return = "";
$idMovil = (int)$_GET['idMovil'];
if ($idMovil > 0) {
   	include ('clases/clsIdiomas.php');
	$objIdioma = new Idioma();
	$lang = $objIdioma->getIdiomas($_SESSION['idioma']);
	
	require_once("clases/clsRastreo.php");
	$objRastreo = new Rastreo($objSQLServer);
	//-- Obtener datos del procedimiento para actualizar ventana flotante rederecho --//
	$sinReportar60Dias = tienePerfil(16)?true:false;
	$arrReportes = $objRastreo->obtenerReportesMovilesUsuario($_SESSION["idUsuario"], $idMovil, false,false, $sinReportar60Dias);
	//-- -//
	
	//-- Telemetria --//
	require_once("clases/clsEquipos.php");
	$objEquipo = new Equipo($objSQLServer);
	$arrTelemetria[1] = $objEquipo->obtenerUnidadTelemetria($idMovil, 1, true);
	$arrTelemetria[2] = $objEquipo->obtenerUnidadTelemetria($idMovil, 2, true);
	$arrTelemetria[3] = $objEquipo->obtenerUnidadTelemetria($idMovil, 3, true);
	//-- --//
	
	for ($i = 0; $i < count($arrReportes); $i++) {
	    if ($arrReportes[$i]["mo_id"] == $idMovil){
	        $tmpReporte = $arrReportes[$i];
            if (!isset($arrReportes[$i]["ubicacion"])) {
				$objNomenclador = new Nomenclador($objSQLServer);
                $geocodificacion = $objNomenclador->obtenerNomenclados($arrReportes[$i]["sh_latitud"], $arrReportes[$i]["sh_longitud"], $arrReportes[$i]["movil"]);
                $arrReportes[$i]["ubicacion"] = $geocodificacion;
				$arrReportes[$i]["nivelUbicacion"] = 1;
            }
            
			//CHEQUEO SI EL MOTOR SE ENCUENTRA ENCENDIDO O APAGADO
            $bEncendido = getEstadoMotor($arrReportes[$i]);
			$textoMotor = $bEncendido?$lang->system->motor_encendido:$lang->system->motor_apagado;
			
            //----------------------------------------------------------------------------------------------------------

            /******************************************
                BEGIN: INFO DATOS GPS - TABLAS
            ******************************************/

            $datos = '<table id="tbl_infodatogps" class="colIzq" width="100%">';
          
		if($arrReportes[$i]['mo_id_tipo_movil'] == TIPOMOVIL::CELLPHONE){
		  	$datos.= '	<tr>';
            $datos.= '		<td>&nbsp;</td>';
			$datos.= '		<td>';
			$datos.= '<ul>';
			
			$arrReportes[$i]['entradas'] = (int)$arrReportes[$i]['entradas'];
			$datos.= '<li class="f-right i-status '.($arrReportes[$i]['entradas']?(
			($arrReportes[$i]['entradas'] <= 25)?'bateria_2':
			(($arrReportes[$i]['entradas'] > 25 && $arrReportes[$i]['entradas'] <= 50)?'bateria_3':
			(($arrReportes[$i]['entradas'] > 50 && $arrReportes[$i]['entradas'] <= 75)?'bateria_4':'bateria_5'
			))):'bateria_1').'" title="'.($arrReportes[$i]['entradas']?($lang->system->bateria.' '.$arrReportes[$i]['entradas'].'%'):$lang->system->bateria_baja).'"></li>';
			
			
			$txt_senial = $lang->system->sin_cobertura;
			$class_senial = 'signal_1';
			if($arrReportes[$i]['sh_senial']){
				if($arrReportes[$i]['sh_senial'] < 0){
					$arrReportes[$i]['sh_senial'] = $arrReportes[$i]['sh_senial']*-1;
				}
				if($arrReportes[$i]['sh_senial'] > 0 && $arrReportes[$i]['sh_senial'] <= 25){
					$txt_senial = $lang->system->cobertura_mala;
					$class_senial = 'signal_2';
				}
				elseif($arrReportes[$i]['sh_senial'] > 25 && $arrReportes[$i]['sh_senial'] <= 50){
					$txt_senial = $lang->system->cobertura_regular;
					$class_senial = 'signal_3';
				}
				elseif($arrReportes[$i]['sh_senial'] > 50 && $arrReportes[$i]['sh_senial'] <= 75){
					$txt_senial = $lang->system->cobertura_buena;
					$class_senial = 'signal_4';
				}
				else{
					$txt_senial = $lang->system->cobertura_muy_buena;
					$class_senial = 'signal_5';
				}
			}
			$datos.= '<li class="f-right i-status '.$class_senial.'" title="'.$txt_senial.'"></li>';
			
			$datos.= '<li class="f-right i-status '.($arrReportes[$i]['sh_estado_wifi']?'wifi_on':'wifi_off').'" title="'.($arrReportes[$i]['sh_estado_wifi']?$arrReportes[$i]['sh_wifi_name']:$lang->system->wifi_apagado).'"></li>';
			
			$datos.= '<li class="f-right i-status '.($arrReportes[$i]['sh_estado_gps']?'gps_on':'gps_off').'" title="'.($arrReportes[$i]['sh_estado_gps']?$lang->system->gps_encendido:$lang->system->gps_apagado).'"></li>';
			
			$datos.= '</ul>';
			$datos.= '		</td>';
			$datos.= '	</tr>';
		 }
		    $datos.= '	<tr>';
            $datos.= '		<td>'.$lang->system->movil.'</td>';
			$datos.= '		<td>'.encode($arrReportes[$i]['movil']);
			//-- Si el último reporte es manor a 30min se habilita el popup	--//			
			$diff_ultimoHistorico = (strtotime(date('Y-m-d H:i:s')) - strtotime(str_replace('/','-',$arrReportes[$i]["sh_fechaGeneracion"])))/60;
		if($diff_ultimoHistorico <= 30 && !tienePerfil(16)){
			$datos.= '<a href="javascript:;" style="float:right; margin-left: 5px;" onclick="seguirMovilinPicture('.$arrReportes[$i]['mo_id'].','.$arrReportes[$i]['sh_latitud'].','.$arrReportes[$i]['sh_longitud'].');"><img src="imagenes/raster/black/new_window_16x16.png" /></a>';
		}
			//-- --//
			$datos.= '		</td>';
			$datos.= '	</tr>';
		
		$horaConexion = '';
		if($arrReportes[$i]['mo_id_tipo_movil'] != TIPOMOVIL::CELLPHONE){
			$horaConexion = formatearFecha($arrReportes[$i]["sh_fechaGeneracion"],'short');
			$datos.= '	<tr>';
            $datos.= '		<td>'.$lang->system->matricula.'</td>';
			$datos.= '		<td>'.$arrReportes[$i]['mo_matricula'].'</td>';
            $datos.= '	</tr>';
            $datos.= '	<tr>';
            $datos.= '		<td>'.$lang->system->dia_hora_gps.'</td>';
			$datos.= '		<td>'.$horaConexion.'</td>';
            $datos.= '	</tr>';
		}
		else{
			$horaConexion = formatearFecha($arrReportes[$i]["sh_fechaRecepcion"],'short');
		}
			$datos.= '	<tr>';
            $datos.= '		<td>'.$lang->system->ultima_conexion.'</td>';
			$datos.= '		<td>'.formatearFecha($arrReportes[$i]["sh_fechaRecepcion"],'short').'</td>';
            $datos.= '	</tr>';
            
		if($arrReportes[$i]['mo_id_tipo_movil'] != TIPOMOVIL::CELLPHONE){
            if($arrReportes[$i]["um_velocidadMaxima"] > 0 && $arrReportes[$i]["dg_velocidad"] > 0){
				if($arrReportes[$i]["dg_velocidad"] < $arrReportes[$i]["um_velocidadMaxima"]){
                    $fontColor = "#008000";//VERDE
                    $bgColorV = "";
                }
				elseif(($arrReportes[$i]["dg_velocidad"] >= $arrReportes[$i]["um_velocidadMaxima"]) && ($arrReportes[$i]["dg_velocidad"] <= ($arrReportes[$i]["um_velocidadMaxima"] * 1.1))) {
                    $fontColor = "#E8E800"; //AMARILLO
                    $bgColorV = "#808080";
                }
				else {
                    $fontColor = "#FF6A6A";//ROJO
                    $bgColorV = "";
                }
            }
			else {
                $fontColor = "#000000";
                $bgColorV = "";
            }
		

			$velocidad = formatearVelocidad($arrReportes[$i]["dg_velocidad"]);
            $datos.= '	<tr>';
            $datos.= '		<td>'.$lang->system->velocidad.'</td>';
			$datos.= '		<td><span class="textoGrande" style="color:'.$fontColor.';background-color:'.$bgColorV.'">'.$velocidad.'</span></td>';
            $datos.= '	</tr>';
			$datos.= '	<tr>';
			$datos.= '		<td>'.$lang->system->sentido.'</td>';
			$datos.= '		<td>'.$lang->system->$arrReportes[$i]["dg_curso"].'</td>';
            $datos.= '	</tr>';
		}	
			//-- Ubicacion --//
            if ($arrReportes[$i]["ubicacion"]){$ubicacion = $arrReportes[$i]["ubicacion"];}
            else{$ubicacion = "-";}
			$lat = round($arrReportes[$i]["sh_latitud"], 4);
            $lon = round($arrReportes[$i]["sh_longitud"], 5);
            //-- --//
            $datos.= '	<tr>';
            $datos.= '		<td>'.$lang->system->ubicacion.'</td>';
			$datos.= '		<td><a href="javascript:mapSetCenter('.$lat.','.$lon.')"><span id="infoNomenclado_'.$arrReportes[$i]['mo_id'].' title="('.$lat.','.$lon.')">'.str_replace("'",'',$ubicacion).'</span></a></td>';
            $datos.= '	</tr>';
			$datos.= '	<tr>';
            $datos.= '		<td>'.$lang->system->coordenadas.'</td>';
			$datos.= '		<td>';
			if(!tienePerfil(16)){
				$datos.= '		<a href="http://maps.google.com/maps?q='.$lat.','.$lon.'('.$arrReportes[$i]['movil'].')" style="float:left" target="_blank">'.$lat.'<br>'.$lon.'</a>';
				$datos.= '		<a href="javascript:;" onclick=javascript:enviar("export_kml",'.$arrReportes[$i]["mo_id"].'); style="float:right" title="'.$lang->system->descargar_ubicacion.'"><span class="icon21x21 download" ></span></a>';
     		}
			else{
				$datos.= '		<span>'.$lat.','.$lon.'</span>';	
			}
			$datos.= '		</td>';
			$datos.= '	</tr>';
			
			if(!tienePerfil(16)){
				$datos.= '	<tr>';
            	$datos.= '		<td>'.$lang->system->evento.'</td>';
				$datos.= '		<td>'.$arrReportes[$i]['tr_descripcion'].'</td>';
            	$datos.= '	</tr>';
            }
			
            if(!in_array($arrReportes[$i]['mo_id_tipo_movil'], array(TIPOMOVIL::CELLPHONE, TIPOMOVIL::TAG_PERSONA, TIPOMOVIL::TAG_MOVIL))){
                $datos.= '	<tr>';
                $datos.= '		<td>'.$lang->system->motor.'</td>';
				$datos.= '		<td>'.$textoMotor.'</td>';
                $datos.= '	</tr>';
                $datos.= '	<tr>';
				$datos.= '		<td>'.$lang->system->dato_gps.'</td>';
				$datos.= '		<td>'.htmlentities($arrReportes[$i]['sh_datoGPS']).'</td>';
                $datos.= '	</tr>';
				$datos.= '	<tr>';
				$datos.= '		<td>'.$lang->system->senial_gps.'</td>';
				$cobertura = 'cobertura_'.$objRastreo->getSenalGPS((int)$arrReportes[$i]['edadgps'],(int)$arrReportes[$i]["sh_datoGPS"]);
				$datos.= '		<td>'.$lang->system->$cobertura.'</td>';
                $datos.= '	</tr>';
                $datos.= '	<tr>';
                $datos.= '		<td>'.$lang->system->equipo_instalado.'</td>';
				$datos.= '		<td>'.$arrReportes[$i]['un_mostrarComo'].'</td>';
                $datos.= '	</tr>';
				if(trim($arrReportes[$i]['mo_aux1']) != ''){
				$datos.= '	<tr>';
				$datos.= '	<td>F20</td><td>'.$arrReportes[$i]['mo_aux1'].'</td>';
				$datos.= '	</tr>';
				}
			}
			if ($arrReportes[$i]['mo_id_tipo_movil'] != TIPOMOVIL::CELLPHONE){
            	$datos.= '	<tr>';
				$datos.= '		<td>'.$lang->system->empresa.'</td>';
				$datos.= '		<td>'.(trim($arrReportes[$i]['nombreEmpresa']) == ''?'&nbsp;':encode($arrReportes[$i]['nombreEmpresa'])).'</td>';
				$datos.= '	</tr>';                
				$datos.= '	<tr>';
				$datos.= '		<td>'.$lang->system->telefono.'</td>';
				$datos.= '		<td>'.(trim( $arrReportes[$i]['telEmpresa']) == ''?'&nbsp;':$arrReportes[$i]['telEmpresa']).'</td>';
				$datos.= '</tr>';                
			}
			
            if(in_array($arrReportes[$i]['mo_id_tipo_movil'], array(TIPOMOVIL::CAR, TIPOMOVIL::TRUCK))){
                $arrReportes[$i]['mo_marca'] = trim($arrReportes[$i]['mo_marca']);
                $arrReportes[$i]['mo_modelo'] = trim($arrReportes[$i]['mo_modelo']);
                $arrReportes[$i]['mo_anio'] = trim($arrReportes[$i]['mo_anio']);
                $dt = $dd = null;
                if(!empty($arrReportes[$i]['mo_marca'])){
                    $dt = $lang->system->marca;
                    $dd = $arrReportes[$i]['mo_marca'];
                }
                if(!empty($arrReportes[$i]['mo_modelo'])){
                    $dt .= '/'.$lang->system->modelo;
                    $dd .= '/'.$arrReportes[$i]['mo_modelo'];
                }
                if($dt != NULL){
                    $datos.= '	<tr>';
                    $datos.= '		<td>'.$dt.'</td>';
					$datos.= '		<td>'.$dd.'</td>';
                    $datos.= '	</tr>';
                }
                // ---------------
                $dt = $dd = NULL;
                if(!empty($arrReportes[$i]['mo_anio'])){
                    $dt = $lang->system->anio;
                    $dd = $arrReportes[$i]['mo_anio'];
                }
                if(!empty($arrReportes[$i]['mo_color'])){
                    if($dt != NULL){
                        $dt.= '/';
                    }
                    $dt.= $lang->system->color;
                    if($dd != NULL) {
                        $dd .= '/';
                    }
                    $dd .= '/'.$arrReportes[$i]['mo_color'];
                }
                if ($dt != NULL) {
                    $datos.= '	<tr>';
                    $datos.= '		<td>'.$dt.'</td>';
					$datos.= '		<td>'.$dd.'</td>';
                    $datos.= '	</tr>';                      
                }
           
		   		//-- Telemetria --//
				if(
					(!empty($arrTelemetria[1]['ut_unidad']) && !empty($arrReportes[$i]['dr_telemetria_1'])) 
					|| (!empty($arrTelemetria[2]['ut_unidad']) && !empty($arrReportes[$i]['dr_telemetria_2'])) 
					|| (!empty($arrTelemetria[3]['ut_unidad']) && !empty($arrReportes[$i]['dr_telemetria_3'])) 
				){
					$separador = '';
					$datos.= '	<tr>';
					$datos.= '		<td>'.$lang->system->telemetria.'</td>';
					$datos.= '		<td>';
					if(!empty($arrTelemetria[1]['ut_unidad']) && !empty($arrReportes[$i]['dr_telemetria_1'])){
						$datos.= number_format($arrReportes[$i]['dr_telemetria_1'],2,',','.').encode($arrTelemetria[1]['ut_unidad']);
						$separador = '/';
					}
					if(!empty($arrTelemetria[2]['ut_unidad']) && !empty($arrReportes[$i]['dr_telemetria_2'])){
						$datos.= $separador.number_format($arrReportes[$i]['dr_telemetria_2'],2,',','.').encode($arrTelemetria[2]['ut_unidad']);
						$separador = '/';
					}
					if(!empty($arrTelemetria[3]['ut_unidad']) && !empty($arrReportes[$i]['dr_telemetria_3'])){
						$datos.= $separador.number_format($arrReportes[$i]['dr_telemetria_3'],2,',','.').encode($arrTelemetria[3]['ut_unidad']);
						$separador = '/';
					}
					$datos.= '		</td>';
					$datos.= '	</tr>';
				}
				//-- --//
				
				$arrReportes[$i]['conductor1'] = trim($arrReportes[$i]['conductor1']);
				$arrReportes[$i]['conductor2'] = trim($arrReportes[$i]['conductor2']);
				if(!empty($arrReportes[$i]['conductor1'])){
					$datos.= '	<tr>';
					$datos.= '		<td>'.$lang->system->conductor.' 1</td>';
					$datos.= '		<td>'.$arrReportes[$i]['conductor1'].'</td>';
					$datos.= '	</tr>';
				}
				
				if(!empty($arrReportes[$i]['conductor2'])){
					$datos.= '	<tr>';
					$datos.= '		<td>'.$lang->system->conductor.' 2</td>';
					$datos.= '		<td>'.$arrReportes[$i]['conductor2'].'</td>';
					$datos.= '	</tr>';
				}
            }
			$datos.= '</table>';

            /******************************************
                END: INFO DATOS GPS - TABLAS
            ******************************************/

            $varLink = '<div id="botonera-infogps" style="margin-top:10px;" class="button-center">';
            if($objPerfil->validarSeccion('informes')){
				$varLink.= '<a href="javascript:enviarHistorico('.$arrReportes[$i]['mo_id'].');" class="button extra-wide colorin" style="margin:0px 0px 5px 0px; width:190px;">';
                $varLink.= $lang->botonera->informe_historico;
                $varLink.= '</a>';
            }
            
            if($objPerfil->validarSeccion('abmReferencias') && !tienePerfil(array(9,10,11,12))){
                $varLink.= '<a href="javascript:agregarRef('.$arrReportes[$i]["sh_latitud"].",".$arrReportes[$i]["sh_longitud"].",".$arrReportes[$i]["mo_id"].');" class="button extra-wide colorin" style="margin:0px 0px 5px 0px; width:190px;">';
                $varLink.=  $lang->botonera->agregar_geozona;
                $varLink.= '</a>';
            }

            if($objPerfil->validarSeccion('LogControl')){
				$varLink.= '<a class="button extra-wide colorin" href="logcontrol.php?un_mostrarComo='.$arrReportes[$i]['un_mostrarComo'].'"  target="_blank" id="btnLogControl" style="margin:0px 0px 5px 0px; width:190px;">';
				$varLink.=	$lang->botonera->log_control;
                $varLink.= '</a>';
            }

            $bProgrammingEnabled = false;
			
            if($objPerfil->validarSeccion('verificarEquipo') && $arrReportes[$i]["mo_id_tipo_movil"] != TIPOMOVIL::CELLPHONE){
                $bProgrammingEnabled = true;
				$varLink.= '<a class="button extra-wide colorin" href="javascript:;" id="btnProgramacion" onclick="btnProgramacionEquipos();" style="margin:0px 0px 5px 0px; width:190px;">';
				$varLink.=	$lang->botonera->programacion;
				$varLink.= '</a>';
            }
            
            if($objPerfil->validarSeccion('abmEquiposMoviles') && $arrReportes[$i]["mo_id_tipo_movil"] == TIPOMOVIL::CELLPHONE){
                $varLink.= '<a class="button extra-wide colorin" href="javascript:;" id="btnHorario" onclick="btnHorario();" style="margin:0px 0px 5px 0px; width:190px;">';
				$varLink.=	$lang->botonera->configurar_horarios;
				$varLink.= '</a>';
            }
            
            if($objPerfil->validarSeccion('abmMoviles') && tienePerfil(array(5,13,19))){
                $varLink.= '<a class="button extra-wide colorin" href="javascript:;" id="btnConf" onclick="btnConf();" style="margin:0px 0px 5px 0px; width:190px;">';
				$varLink.=	$lang->botonera->configurar_movil;
				$varLink.= '</a>';
			}
            $varLink.= '</div>';
            
            if($bProgrammingEnabled){
                require_once('clases/clsGruposComandos.php');
                $objComandos = new GrupoComandos($objSQLServer);
				$arrFavCommands = $objComandos->obtenerComandosFavoritosCombo($arrReportes[$i]['un_mostrarComo']);
				$html = '<div>';
                $html.= '	<frameset>';
                $html.= '		<legend>'.$lang->system->comandos_favoritos.'</legend>';
                $html.= '		<select id="cboFavCommand" style="width:150px; float:left;">';
								foreach($arrFavCommands as $cfid => $cfdata){
                $html.= '			<option value="'.$cfdata['co_codigo'].'">'.encode($cfdata['co_nombre']).'</option>';
                				}
                $html.= '		</select>';
				$html.= '		<button id="btn_EnviarCmdFav">'.$lang->botonera->enviar.'</button>';
                $html.= '	</frameset>';
				$html.= '	<div style="clear:both;"></div>';
				$html.= '	<div id="lbl_StatusCmdFav" class="status-cmd" style="display:none;"></div>';
                $html.= '</div>';
                $varLink .= $html;
            }
            //
			if((int)$_GET['height']){
				$height = (int)$_GET['height'];}
			else{
				$height = '350';}
					
			$datos.= $varLink;		
            $datos = '<div id="datos_puros_gps" style="height:'.$height.'px; overflow-y: auto; border-bottom: solid 1px rgba(100, 100, 100, 0.2);">'.$datos.'</div>';
          
		  //-- Actualizo datos del Arbol para el movilseleccionado --//
		  	if(in_array($arrReportes[$i]['dr_valor'], $arrGrayedEvents)){//evento falta de reporte y evento falta de reporte mas de 24 hs
				$bgcolor = 'gray';
			}
			elseif($arrReportes[$i]['mo_id_tipo_movil'] == TIPOMOVIL::CELLPHONE){
				$bgcolor = ($arrReportes[$i]['dg_velocidad'] > 0)?'transparent':'green';
			}
			else{
				$bgcolor = $bEncendido?(($arrReportes[$i]['dg_velocidad'] > 0)?'transparent':'green'):'red';
			}
			
		  $return .= '
			   $("#movil-info_'.$arrReportes[$i]["mo_id"].' .movil-fechagen").html("'.$horaConexion .'");	
			   var tr_descripcion = "'.$arrReportes[$i]['tr_descripcion'].'";
			   tr_descripcion = (tr_descripcion.length > TIPOEVENTO_MAX_LENGTH)?tr_descripcion.substr(0, TIPOEVENTO_MAX_LENGTH - 3)+"...":tr_descripcion;
			   $("#movil-info_'.$arrReportes[$i]["mo_id"].' .tipo-evento").html(tr_descripcion);
			   $("#movil-info_'.$arrReportes[$i]["mo_id"].' .tipo-evento").attr("title","'.$arrReportes[$i]['tr_descripcion'].'");
			   $("#movil-info_'.$arrReportes[$i]["mo_id"].' .velocidad-movil").html("'.$velocidad.'");
			   $("#movil-info_'.$arrReportes[$i]["mo_id"].' .movil-motor").css("background-color","'.$bgcolor.'");
			   ';
		  //-- --//  

		$return .= '	   
			   
			   if(g_bEmbedGPSPanel){
                    $("#divDatosInfoGps").html(\''.$datos.'\');}
                else{
                    $("#infogps-contenido").html(\''.$datos.'\');}
				
				mostrarNomenclado('.$arrReportes[$i]["mo_id"].');
                g_unMostrarComo = "'.$arrReportes[$i]["un_mostrarComo"].'";
                bindEvents();
            ';
			
            die(trim($return));
        }
    }
} elseif ($idMovil == -1) {
    die(trim($return));
}
