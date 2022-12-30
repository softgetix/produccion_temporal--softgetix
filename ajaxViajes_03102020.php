<?php
error_reporting(0);
@session_start();
$idUsuario = $_SESSION["idUsuario"];
include "includes/funciones.php";

include ('clases/clsIdiomas.php');
$objIdioma = new Idioma();
$lang = $objIdioma->getIdiomas($_SESSION['idioma']);
if(isset($_POST['accion'])) {
	switch ($_POST['accion']){
		case 'get-vehiculos':
			if((int)$_POST['transportista']){
				include_once 'includes/conn.php';
				include_once 'clases/clsViajes.php';
				$objViaje = new Viajes($objSQLServer);
				$vehiculos = $objViaje->obtenerMovilesUsuario($_POST['transportista'],(int)$_POST['id_usuario']);
				echo json_encode($vehiculos);
			}
			else{echo false;}
			exit;
		break;
		case 'get-vehiculos-recomendados':
			if((int)$_POST['id_transportista'] && (int)$_POST['id_conductor']){
				include_once 'includes/conn.php';
				include_once 'clases/clsViajes.php';
				$objViaje = new Viajes($objSQLServer);
				$vehiculos = $objViaje->obtenerMovilesRecomendados((int)$_POST['id_transportista'],(int)$_POST['id_usuario'],(int)$_POST['id_conductor']);
				echo json_encode($vehiculos);
			}
			else{echo false;}
			exit;
		break;
		case 'popup-agregar-movil':
			include_once 'includes/conn.php';
			require_once 'clases/clsViajes.php';

			if($_POST['tipo'] == 'viaje' || $_POST['tipo'] == 'viajeCompleto'){
				$id_viaje = (int)$_POST['id_viaje'];
			}
			elseif($_POST['tipo'] == 'delivery'){
				$id_delivery = (int)$_POST['id_delivery'];
			}
			$id_transportista = (int)$_POST['id_transportista'];
			$id_movil = (int)$_POST['id_movil'];
			$id_usuario = (int)$_SESSION['idUsuario'];
			$id_conductor = (int)$_POST['id_conductor'];
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			
			$filtro['id_usuario'] = $id_usuario;
			$transportistas = $objViaje->getTransportista($filtro);
			
			if($id_movil && !$id_transportista){
				$id_transportista = $objViaje->getTrasnportistaPorMovil($id_movil);
			}
			
			
			if($id_transportista){
				$conductores = $objViaje->obtenerCondutoresPorEmpresa($id_transportista);
				$vehiculos = $objViaje->obtenerMovilesUsuario($id_transportista,$id_usuario);
				$movilRecomendado = $objViaje->obtenerMovilesRecomendados($id_transportista,$id_usuario,$id_conductor);
				$movilDisponible = $objViaje->obtenerMovilesDisponibles($id_transportista, $id_viaje, $id_delivery);
				
				//-- Quitar moviles de otras listas por Orden de Prioridad --//
				if($movilDisponible){
					foreach($movilDisponible as $item){
						if($movilRecomendado){
							foreach($movilRecomendado as $k => $subItem){
								if($subItem['id'] == $item['id']){
									unset($movilRecomendado[$k]);
									break;	
								}
							}
						}
						if($vehiculos){
							foreach($vehiculos as $k => $subItem){
								if($subItem['id'] == $item['id']){
									unset($vehiculos[$k]);
									break;	
								}
							}
						}
					}
				}
				if($movilRecomendado){
					foreach($movilRecomendado as $item){
						if($vehiculos){
							foreach($vehiculos as $k => $subItem){
								if($subItem['id'] == $item['id']){
									unset($vehiculos[$k]);
									break;	
								}
							}
						}
					}
				}
				//-- --//
				
			}
			$arrMotivosCambio = $objViaje->getMotivoViajes();
			?>
            <form name="form_popup" id="form_popup">
        	    <input type="hidden" name="accion" value="popup-guardar-conductor" />
                <fieldset>
                	<select name="popup_transportista" id="popup_transportista" onchange="javacript:getConductoresTrasnportista('popup_conductor', <?=$id_usuario ?>, this.value);">
                    	<option value=""><?=$lang->system->seleccione_transportista?></option>
                        <?php foreach($transportistas as $item){?>
                        <option value="<?=$item['cl_id']?>" <?=($item['cl_id']==$id_transportista)?'selected':''?>><?=encode($item['cl_razonSocial'])?></option>
						<?php }?>
					</select>
				</fieldset>
                <fieldset>
                	<select name="popup_conductor" id="popup_conductor" onchange="javacript:getVehiculosRecomendado('popup_vehiculo',$('#popup_transportista').val(), <?=$id_usuario ?>, this.value);">
                    	<option value=""><?=$lang->system->seleccione_conductor?></option>
                        <?php foreach($conductores as $item){?>
                        <option value="<?=$item['co_id']?>" <?=($item['co_id']==$id_conductor)?'selected':''?>><?=encode($item['co_nombre'].' '.$item['co_apellido'])?></option>
						<?php }?>
					</select>
				</fieldset>
				<fieldset>
                	<select name="popup_vehiculo" id="popup_vehiculo">
                    	<option value=""><?=$lang->system->seleccione_movil?></option>
                        <?php if($movilDisponible){?>
                        <option value="" disabled="disabled">---- <?=$lang->system->movil_disponible?> ----</option>
                        <?php foreach($movilDisponible  as $item){?>
                        <option value="<?=$item['id']?>" <?=($item['id']==$id_movil)?'selected':''?>><?=encode($item['dato'])?></option>
						<?php }?>
                       	<option value="" disabled="disabled">----  ----</option>
                        <?php }?>
                        
                        
						<?php if(count($movilRecomendado) > 0){?>
                        <option value="" disabled="disabled">---- <?=$lang->system->movil_recomendado?> ----</option>
                        <?php foreach($movilRecomendado  as $item){?>
                        <option value="<?=$item['id']?>" <?=($item['id']==$id_movil)?'selected':''?>><?=encode($item['dato'])?></option>
						<?php }?>
                       	<option value="" disabled="disabled">----  ----</option>
                        <?php }?>
                        
                        
                        <?php foreach($vehiculos  as $item){?>
                        <option value="<?=$item['id']?>" <?=($item['id']==$id_movil)?'selected':''?>><?=encode($item['dato'])?></option>
						<?php }?>
					</select>
				</fieldset>
                <fieldset>
                    <label for="motivo_name"><?=$lang->message->msj_motivo_update?>: </label>			
                    <select id="motivo_name" name="motivo_name" >
                        <option value="0"><?=$lang->system->ninguno?></option>
                        <?php foreach($arrMotivosCambio as $item){ ?>
                            <option value="<?=$item['vmc_id']?>"><?=encode($item['vmc_descripcion'])?></option>
                        <?php } ?>
                    </select>
				</fieldset>
                <fieldset>
                	<?php 
					switch($_POST['tipo']){
						case 'delivery':
							?><a href="javascript:setVehiculoConductorDelivery(<?=$id_delivery?>,$('#popup_transportista').val(), $('#popup_conductor').val(), $('#popup_vehiculo').val());" class="button colorin" style="width:325px;"><?=$lang->botonera->guardar?></a><?php
						break;
						case 'viaje':
							?><a href="javascript:setVehiculoConductorViaje(<?=$id_viaje?>,$('#popup_transportista').val(), $('#popup_conductor').val(), $('#popup_vehiculo').val());" class="button colorin" style="width:325px;"><?=$lang->botonera->guardar?></a><?php
						break;
						case 'viajeCompleto':
						?><a href="javascript:setVehiculoConductorDeliveryCompleto(<?=$id_viaje?>,$('#popup_transportista').val(), $('#popup_conductor').val(), $('#popup_vehiculo').val());" class="button colorin" style="width:325px;"><?=$lang->botonera->guardar?></a><?php
						break;
					}
					?>
                </fieldset>
                <p id="motivo_error" style="display:none;color:#AC0C0C; margin-top:5px;"><?=$lang->message->msj_motivo?>.</p>
			</form>
			<?php 
			exit;
		break;	
		case 'popup-agregar-conductor':
			include_once 'includes/conn.php';
			require_once 'clases/clsViajes.php';
			$id_viaje = (int)$_POST['id_viaje'];
			$movilID = (int)$_POST['id_movil'];
			$traspID = (int)$_POST['id_transportista'];
			$userID = (int)$_SESSION['idUsuario'];
			$conductorID = (int)$_POST['id_conductor'];
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			$conductores = $objViaje->obtenerCondutoresPorEmpresa($traspID);
			$vehiculos = $objViaje->obtenerMovilesUsuario($traspID,$userID);
			$movilRecomendado = $objViaje->obtenerMovilesRecomendados($traspID,$userID,$conductorID);
			$arrMotivosCambio = $objViaje->getMotivoViajes();
			?>
            <form name="form_popup" id="form_popup">
        	    <input type="hidden" name="accion" value="popup-guardar-conductor" />
                <fieldset>
                	<select name="popup_conductor" id="popup_conductor" onchange="javacript:getVehiculosRecomendado('popup_vehiculo',<?=$traspID?>, <?=$userID?>, this.value);">
                    	<option value=""><?=$lang->system->seleccione_conductor?></option>
                        <?php foreach($conductores as $item){?>
                        <option value="<?=$item['co_id']?>" <?=($item['co_id']==$conductorID)?'selected':''?>><?=encode($item['co_nombre'].' '.$item['co_apellido'])?></option>
						<?php }?>
					</select>
				</fieldset>
				<fieldset>
                	<select name="popup_vehiculo" id="popup_vehiculo">
                    	<option value=""><?=$lang->system->seleccione_movil?></option>
                        <?php if(count($movilRecomendado) > 0){?>
                        <option value="" disabled="disabled">---- <?=$lang->system->movil_recomendado?> ----</option>
                        <?php }?>
						<?php foreach($movilRecomendado  as $item){?>
                        <option value="<?=$item['id']?>" <?=($item['id']==$movilID)?'selected':''?>><?=encode($item['dato'])?></option>
						<?php }?>
                       	<?php if(count($movilRecomendado) > 0){?>
                        <option value="" disabled="disabled">----  ----</option>
                        <?php }?>
                        <?php foreach($vehiculos  as $item){?>
                        <option value="<?=$item['id']?>" <?=($item['id']==$movilID)?'selected':''?>><?=encode($item['dato'])?></option>
						<?php }?>
					</select>
				</fieldset>
                <fieldset>
                    <label for="motivo_name"><?=$lang->message->msj_motivo_update?>: </label>			
                    <select id="motivo_name" name="motivo_name" >
                        <option value="0"><?=$lang->system->ninguno?></option>
                        <?php foreach($arrMotivosCambio as $item){ ?>
                            <option value="<?=$item['vmc_id']?>"><?=encode($item['vmc_descripcion'])?></option>
                        <?php } ?>
                    </select>
				</fieldset>
                <fieldset>
                	<a href="javascript:setVehiculoConductor(<?=$id_viaje?>,$('#popup_conductor').val(), $('#popup_vehiculo').val());" class="button colorin" style="width:325px;"><?=$lang->botonera->guardar?></a>                        
				</fieldset>
                <p id="motivo_error" style="display:none;color:#AC0C0C; margin-top:5px;"><?=$lang->message->msj_motivo?>.</p>
			</form>
			<?php 
			exit;
		break;	
		case 'popup-guardar-conductor':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			$arrMotivosCambio = $objViaje->getMotivoViajes($_POST['motivo_id']);
			echo $objViaje->setConductorVehiculo((int)$_POST['id_conductor'], (int)$_POST['id_movil']);
			$objViaje->setLog($lang->system->motivo.': '.encode($arrMotivosCambio[0]['vmc_descripcion']),$_POST['motivo_id']);
			exit;
		break;
		case 'popup-guardar-conductor-viaje':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			$arrMotivosCambio = $objViaje->getMotivoViajes($_POST['motivo_id']);
			echo $objViaje->setConductorVehiculo((int)$_POST['id_conductor'], (int)$_POST['id_movil'], (int)$_POST['id_transportista']);
			$objViaje->setLog($lang->system->motivo.': '.($arrMotivosCambio[0]['vmc_descripcion']),$_POST['motivo_id']);//tenia encode y compia acentos
			exit;
		break;
		case 'popup-guardar-conductor-delivery':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			include_once 'clases/clsViajesDelivery.php';
			$objViaje = new ViajesDelivery($objSQLServer);
			
			$strSQL = " SELECT vd_vi_id FROM tbl_viajes_destinos WHERE vd_id IN (
							SELECT vdd_vd_id FROM tbl_viajes_destinos_delivery where vdd_id = ".(int)$_POST['id_delivery']."
						) ";
			$res = $objSQLServer->dbQuery($strSQL);
			$arr_viaje = $objSQLServer->dbGetRow($res,0,3);	
			$objViaje->id_viaje = $arr_viaje['vd_vi_id'];
			
			$arrMotivosCambio = $objViaje->getMotivoViajes($_POST['motivo_id']);
			echo $objViaje->setConductorVehiculo((int)$_POST['id_delivery'], (int)$_POST['id_transportista'], (int)$_POST['id_conductor'], (int)$_POST['id_movil']);
			$objViaje->setLog($lang->system->motivo.': '.($arrMotivosCambio[0]['vmc_descripcion']),$_POST['motivo_id']);//tenia encode y rompia acentos
			exit;
		break;
		case 'popup-guardar-conductor-delivery-completo':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			include_once 'clases/clsViajesDelivery.php';
			$objViaje = new ViajesDelivery($objSQLServer, $_POST['id_viaje']);
			$arrMotivosCambio = $objViaje->getMotivoViajes($_POST['motivo_id']);
			echo $objViaje->setConductorVehiculoCompleto((int)$_POST['id_conductor'], (int)$_POST['id_movil'], (int)$_POST['id_transportista']);
			$objViaje->setLog($lang->system->motivo.': '.($arrMotivosCambio[0]['vmc_descripcion']),$_POST['motivo_id']);//tenia encode y compia acentos
			exit;
		break;
		case 'popup-motivosCambios':
			include_once 'includes/conn.php';
			require_once 'clases/clsViajes.php';
			$id_viaje = $_POST['id_viaje'];
			$objViaje = new Viajes($objSQLServer, $id_viaje);
			$arrMotivosCambio = $objViaje->getMotivoViajes();
			?>
            <form id="motivo_form" method="post" action="">				
				<label for="motivo_name"><?=$lang->message->msj_motivo_update?>: </label>			
				<select id="motivo_name" name="motivo_name" style="width:350px;">
					<option value="0"><?=$lang->system->ninguno?></option>
					<?php foreach($arrMotivosCambio as $item){ ?>
						<option value="<?=$item['vmc_id']?>"><?=encode($item['vmc_descripcion'])?></option>
					<?php } ?>
				</select>
				<a href="javascript:setMotivoCambio(<?=$id_viaje?>);" class="button colorin" style="width:325px;"><?=$lang->botonera->guardar?></a>                        
                <p id="motivo_error" style="display:none;color:#AC0C0C; margin-top:5px;"><?=$lang->message->msj_motivo?>.</p>
			</form>
            <?php 
			exit;
		break;
		case 'reset-ingreso':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			echo $objViaje->resetFechaIngreso($_POST['id_ref']);
			exit;
		break;
		case 'reset-egreso':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			echo $objViaje->resetFechaEgreso($_POST['id_ref']);
			exit;
		break;
		case 'reset-ingreso-delivery':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			include_once 'clases/clsViajesDelivery.php';
			$objViaje = new ViajesDelivery($objSQLServer, $_POST['id_viaje']);
			echo $objViaje->resetFechaIngreso($_POST['vd_id']);
			exit;
		break;
		case 'reset-egreso-delivery':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			include_once 'clases/clsViajesDelivery.php';
			$objViaje = new ViajesDelivery($objSQLServer, $_POST['id_viaje']);
			echo $objViaje->resetFechaEgreso($_POST['vd_id']);
			exit;
		break;
		case 'assign-ingreso':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			if($_POST['id_delivery']){
				include_once 'clases/clsViajesDelivery.php';
				$objViaje = new ViajesDelivery($objSQLServer, $_POST['id_viaje']);
				$fecha = $objViaje->assignFechaIngreso($_POST['id_destino'], $_POST['id_delivery'], $_POST['fecha']);
			}
			elseif(isset($_POST['id_destino'])){
				$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
				$fecha = $objViaje->assignFechaIngreso($_POST['id_destino'], $_POST['fecha']);
			}	
			
			if(strtotime($_POST['fecha']) == strtotime($fecha)){
				echo formatearFecha($_POST['fecha']);	
			}
			exit;
		break;
		case 'assign-egreso':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			if($_POST['id_delivery']){
				include_once 'clases/clsViajesDelivery.php';
				$objViaje = new ViajesDelivery($objSQLServer, $_POST['id_viaje']);
				$fecha = $objViaje->assignFechaEgreso($_POST['id_destino'], $_POST['id_delivery'], $_POST['fecha']);
				echo $fecha;
			}
			elseif(isset($_POST['id_destino'])){
				$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
				$arrFecha = $objViaje->assignFechaEgreso($_POST['id_destino'], $_POST['fecha']);
				if(strtotime($_POST['fecha']) == strtotime($arrFecha['fecha'])){
					if($arrFecha['vd_ini_real']){
						echo $objViaje->getTiempoHM(strtotime($arrFecha['fecha']) - strtotime($arrFecha['vd_ini_real']));
					}
					else{
						echo formatearFecha($_POST['fecha']);	
					}
				}
			}
			exit;
		break;
		case 'pod-ingreso':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			if($_POST['id_destino']){
				include_once 'clases/clsViajesDelivery.php';
				$objViaje = new ViajesDelivery($objSQLServer, $_POST['id_viaje']);
				$fecha = $objViaje->podFechaIngreso($_POST['id_destino'], $_POST['fecha']);
			}
			
			if(strtotime($_POST['fecha']) == strtotime($fecha)){
				echo formatearFecha($_POST['fecha']);	
			}
			exit;
		break;
		case 'get-conductores':
			$conductores = array();
			if((int)$_POST['transportista'] > 0){
				include_once 'includes/conn.php';
				require_once 'clases/clsViajes.php';
				$objViaje = new Viajes($objSQLServer);
				$conductores = $objViaje->obtenerCondutoresPorEmpresa($_POST['transportista']);
			}
			echo json_encode($conductores);
			exit;
		break;
		case 'popup-rechazar-pedido':
			include_once 'includes/conn.php';
			require_once 'clases/clsViajes.php';
			$id_viaje = (int)$_POST['id_viaje'];
			$id_destino = (int)$_POST['id_destino'];
			$id_delivery = (int)$_POST['id_delivery'];
			$objViaje = new Viajes($objSQLServer, $id_viaje);
			$arrMotivosCambio = $objViaje->getMotivoViajes();
			?>
            <form id="motivo_form" method="post" action="">				
				<label for="motivo_name"><?=$lang->message->msj_motivo_update?>: </label>			
				<select id="motivo_name" name="motivo_name" style="width:350px;">
					<option value="0"><?=$lang->system->ninguno?></option>
					<?php foreach($arrMotivosCambio as $item){ ?>
						<option value="<?=$item['vmc_id']?>"><?=encode($item['vmc_descripcion'])?></option>
					<?php } ?>
				</select>
				<a href="javascript:setRechazarDelivery(<?=$id_viaje?>, <?=$id_destino?>, <?=$id_delivery?>);" class="button colorin" style="width:325px;"><?=$lang->botonera->guardar?></a>                        
                <p id="motivo_error" style="display:none;color:#AC0C0C; margin-top:5px;"><?=$lang->message->msj_motivo?>.</p>
			</form>
            <?php 
			exit;
		break;	
		case 'popup-guardar-rechazo-delivery':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			include_once 'clases/clsViajesDelivery.php';
			$id_viaje = (int)$_POST['id_viaje'];
			$id_destino = (int)$_POST['id_destino'];
			$id_delivery = (int)$_POST['id_delivery'];
			$id_motivo = (int)$_POST['id_motivo'];
			$rechazado = (int)$_POST['rechazado'];
			$objViaje = new ViajesDelivery($objSQLServer, $id_viaje);
			$arrMotivosCambio = $objViaje->getMotivoViajes($id_motivo);
			$objViaje->setLog("Motivo: ".encode($arrMotivosCambio[0]['vmc_descripcion']),$id_motivo);
			echo $objViaje->setRechazoDelivery($id_destino, $id_delivery, $rechazado);
			exit;
		break;
		case 'get-geozona-ruteo':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer);
			if(!empty($_POST['geozona'])){
				$datos['zona'] = $_POST['geozona'];
				$datos['no_id_zona'] = $_POST['id_geozona'];
				$zonas = $objViaje->getRuteo($datos);
					
				$zonas[0]['fecha'] = getFechaServer('d-m-Y');
				$zonas[0]['hora'] = (int)getFechaServer('H');
					
				if((int)getFechaServer('i') < 10){ $zonas[0]['min'] = '00';}
				else if((int)getFechaServer('i') < 20){ $zonas[0]['min'] = '10';}
				else if((int)getFechaServer('i') < 30){ $zonas[0]['min'] = '20';}
				else if((int)getFechaServer('i') < 40){ $zonas[0]['min'] = '30';}
				else if((int)getFechaServer('i') < 50){ $zonas[0]['min'] = '40';}
				else if((int)getFechaServer('i') < 60){ $zonas[0]['min'] = '50';}
				
				$zonas[0]['fecha_egreso'] = date('d-m-Y H:i',strtotime('+10 minute',strtotime($zonas[0]['fecha'].' '.$zonas[0]['hora'].':'.$zonas[0]['min'])));
				##--Se define la estadia por defecto--##
				$zonas[0]['duracion'] = $objViaje->getTiempo((int)$zonas[0]['re_permanencia']*60);
				##-- --##
					
				if((int)$zonas[0]['re_id']){
					echo $objViaje->filaRuteo($zonas[0])."[##]".$zonas[0]['re_id'];
				}
			}
		break;
		case 'autocomplete-geozonas':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer);
			$datos['zona_like'] = $_POST['buscar'];
			$datos['top'] = 30;
			$datos['no_id_zona'] = $_POST['id_geozona'];
			$zonas['geozonas'] = $objViaje->get_geozonas($datos);
			foreach($zonas['geozonas'] as $k => $z){
				$zonas['geozonas'][$k]['etiqueta'] = encode($z['etiqueta']);
				$zonas['geozonas'][$k]['re_nombre'] = encode($z['re_nombre']);
			}
			echo json_encode($zonas);
		break;	
		case 'calculo-fecha':
			$sumar = explode(" ",$_POST['duracion']);
			if($sumar[1] == 'min'){
				$calc = $sumar[0]." minute";}
			elseif($sumar[1] == 'hs'){
				$calc = $sumar[0]." hour";}
			elseif($sumar[1] == 'días' || $sumar[1] == 'dias'){
				$calc = ((int)$sumar[0] * 24)." hour";}
			elseif($sumar[1] == 'semana'){
				$calc = (((int)$sumar[0] * 7) * 24)." hour";}	
			
			if($_SESSION['language'] == 'en'){//-- Se setea la fecha a formato latinomearicana para realizar el calculo.
				$aux_1 = explode(' ',$_POST['fecha']);
				$aux_2 = explode('/',$aux_1[0]);
				$_POST['fecha'] = $aux_2[1].'-'.$aux_2[0].'-'.$aux_2[2].' '.$aux_1[1];
			}
			
			$fecha = date('d-m-Y H:i',strtotime('+'.$calc, strtotime($_POST['fecha'])));
			echo formatearFecha($fecha,'date').' '.date('H:i',strtotime($fecha)).formatearFecha($fecha,'pref_hour');
		break;
		case 'calculo-distancia':
			echo distancia($_POST['lat1'], $_POST['long1'], $_POST['lat2'], $_POST['long2']);
		break;
		case 'formatear-distancia':
			echo formatearDistancia($_POST['km']);
		break;
		case 'validar-movil':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			echo $objViaje->validarMovil((int)$_POST['id_movil'],$_POST['f_ini'],$_POST['f_fin']);
		break;
		case 'ruteo-automatico':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer);
			$id_ref = explode(',',$_POST['ids']);
			if(count($id_ref) > 1 ){
				$long1 = "";
				$lat1 = "";
				$iOrden = 0;
				foreach($id_ref as $item){
					$datos['re_id'] = (int)$item;
					if((int)$datos['re_id']){
						$ref = $objViaje->getRuteo($datos);
							
						if(empty($lat1) && empty($long1)){
							$lat1 = $ref[0]['rc_latitud'];
							$long1 = $ref[0]['rc_longitud'];
							$dist = 0;
						}
						else{
							$lat2 = $ref[0]['rc_latitud'];
							$long2 = $ref[0]['rc_longitud'];
							$dist = distancia($lat1, $long1, $lat2, $long2);
						}
							
						$orden[$datos['re_id']]['id'] = $datos['re_id'];
						$orden[$datos['re_id']]['dist'] = $dist;
						$orden[$datos['re_id']]['lat'] = $ref[0]['rc_latitud'];
						$orden[$datos['re_id']]['long'] = $ref[0]['rc_longitud'];
						$orden[$datos['re_id']]['ref'] = $ref[0]['re_nombre'];
						$orden[$datos['re_id']]['orden'] = $iOrden;	
						$iOrden++;
					}
				}
					
				$hasta = $iOrden;
				for($cant=0; $cant < ($hasta-1); $cant++){
					$iOrden = $cant;
					$newOrden = $objViaje->obtenerMenorDistancia($iOrden, $orden);
					unset($array_ordenar);
					$array_ordenar = $orden;
					unset($orden);
					$i = 0;
					$iOrden++;
					foreach($array_ordenar as $k => $item){
						//-- cargo al vector ordenar las REFERENCIAS QUE YA FUERON ORDENADAS
						if($i < $iOrden && $item[$k]['orden'] <= $i){
							$orden[$k] = $item;
							$i++;
						}
						//-- cargo al vector ordenar la NUEVA REFERENCIA CON LA MENOR DISTANCIA A LA ANTERIOR
						elseif($i == $iOrden && $k == $newOrden){
							$orden[$k] = $item;
							$orden[$k]['orden'] = $i;
							$lat1 = $item['lat'];
							$long1 = $item['long'];
							$i++;
						}
					}
						
					$iOrdenDesde = $i;
					//-- cargo al vector ordenar las REFERENCIAS QUE AUN NO HAN SIDO PROCESADAS
					foreach($array_ordenar as $k => $item){
						 if(!array_key_exists($k, $orden)){
						 	$lat2 = $item['lat'];
							$long2 = $item['long'];
							$dist = distancia($lat1, $long1, $lat2, $long2);
							$orden[$k]['id'] = $k;
							$orden[$k]['orden'] = $iOrdenDesde;
							$orden[$k]['dist'] = $dist;
							$orden[$k]['lat'] = $item['lat'];
							$orden[$k]['long'] = $item['long'];
							$orden[$k]['ref'] = $item['ref'];
							$iOrdenDesde ++;
						}
					}
				}
			}
				
			$resp = array();
			if($orden > 1){
				foreach($orden as $item){
					$resp[$item['orden']] = $item;
				}
			}
			echo json_encode($resp);
		break;
		case 'reload-combo-transportista':
			include_once 'includes/conn.php';
			require_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer);
			$datos['id_usuario'] = $_SESSION['idUsuario'];
			$transportista = $objViaje->getTransportista($datos);
			##Inicio. Codificar a utf8
			if($transportista[0]){
				foreach($transportista as $k => $item){
					foreach($item as $sk => $subItem){
						$transportista[$k][$sk]	= encode($subItem);
					}
				}
			}
			##Fin. Codificar a utf8
			echo json_encode($transportista);
		break;
		case 'reload-combo-movil-tipo':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer);
			$movil_tipo = $objViaje->getMovilTipo($_POST['id_transportista']);
			/*##Inicio. Codificar a utf8
			foreach($movil_tipo as $k => $item){
				foreach($item as $sk => $subItem){
					$movil_tipo[$k][$sk] = encode($subItem);
				}
			}
			##Fin. Codificar a utf8*/
			echo json_encode($movil_tipo);
		break;
		case 'reload-combo-conductor':
			$datos['id_transportista'] = $_POST['id_transportista'];
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer);
			$conductor = $objViaje->getConductor($datos);
			##Inicio. Codificar a utf8
			if($conductor[0]){
				foreach($conductor as $k => $item){
					foreach($item as $sk => $subItem){
						$conductor[$k][$sk] = encode($subItem);
					}
				}
			}
			##Fin. Codificar a utf8
			echo json_encode($conductor);
		break;
		case 'reload-combo-movil':
			$datos['tipo_movil'] = (int)$_POST['tipo_movil'];
			$datos['id_usuario'] = $_SESSION['idUsuario'];
			$datos['transportista'] = $_POST['transportista'];
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer);
			$movil = $objViaje->getMovil($datos);
			##Inicio. Codificar a utf8
			if($movil[0]){
				foreach($movil as $k => $item){
					foreach($item as $sk => $subItem){
						$movil[$k][$sk] = encode($subItem);
					}
				}
			}
			##Fin. Codificar a utf8
			echo json_encode($movil);
		break;
		case 'get-fecha-server':
			echo getFechaServer('d-m-Y H:i');
			exit;
		break;
		case 'validar-codViaje':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			echo $objViaje->validarCodViaje($_POST['cod_viaje']);
			exit;
		break;
		case 'popup-agregar-vehiculo':
			include_once 'includes/conn.php';
			require_once 'clases/clsViajes.php';
			$id_viaje = (int)$_POST['id_viaje'];
			$movilID = (int)$_POST['id_movil'];
			$traspID = (int)$_POST['id_transportista'];
			$userID = (int)$_SESSION['idUsuario'];
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			$vehiculos = $objViaje->obtenerMovilesUsuario($traspID,$userID);
			$movilRecomendado = $objViaje->obtenerMovilesRecomendados($traspID,$userID);
			?>
            <form name="form_popup" id="form_popup">
        	    <input type="hidden" name="accion" value="popup-guardar-conductor" />
                <fieldset>
                	<select name="popup_vehiculo" id="popup_vehiculo">
                    	<option value=""><?=$lang->system->seleccione_movil?></option>
                        <?php if(count($movilRecomendado) > 0){?>
                        <option value="" disabled="disabled">---- <?=$lang->system->movil_recomendado?> ----</option>
                        <?php }?>
						<?php foreach($movilRecomendado  as $item){?>
                        <option value="<?=$item['id']?>" <?=($item['id']==$movilID)?'selected':''?>><?=encode($item['dato'])?></option>
						<?php }?>
                       	<?php if(count($movilRecomendado) > 0){?>
                        <option value="" disabled="disabled">----  ----</option>
                        <?php }?>
                        <?php foreach($vehiculos  as $item){?>
                        <option value="<?=$item['id']?>" <?=($item['id']==$movilID)?'selected':''?>><?=encode($item['dato'])?></option>
						<?php }?>
					</select>
				</fieldset>
                <fieldset>
                	<span class="campo1"><?=$lang->system->observaciones?></span>
                	<textarea name="popup_observacion" id="popup_observacion"></textarea>
                </fieldset>
                <fieldset>
                	<a href="javascript:;" onclick="javascript:setVehiculoAssign(<?=$id_viaje?>);" class="button colorin" style="width:325px;"><?=$lang->botonera->guardar?></a>                        
				</fieldset>
                <p id="motivo_error" style="display:none;color:#AC0C0C; margin-top:5px;"><?=$lang->message->msj_motivo?>.</p>
			</form>
			<?php 
			exit;
		break;
		case 'popup-guardar-vehiculo':
			include_once 'includes/conn.php';
			include_once 'clases/clsViajes.php';
			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);
			echo $objViaje->setVehiculo((int)$_POST['id_movil']);
			if(!empty($_POST['observacion'])){
				$objViaje->setLog($lang->system->motivo.': '.encode($_POST['observacion']));
			}
			exit;
		break;
		case 'cross-docking':
			include_once 'includes/conn.php';
			$params['vi_crossdocking'] = (int)$_POST['value'];
			if($objSQLServer->dbQueryUpdate($params, 'tbl_viajes', 'vi_id = '.(int)$_POST['id_viaje'])){
				include_once 'clases/clsViajes.php';
				$objViaje = new Viajes($objSQLServer,(int)$_POST['id_viaje']);
				$objViaje->setLog($_POST['value']?'Cross docking ON':'Cross docking OFF');
				echo true;	
			}
			else{
				echo false;	
			}
		break;
		case 'save-cotizacion':
			include_once 'includes/conn.php';
			$response = array('isvalid' => true, "msg" => "");
			$idViaje = intval($_POST['id_viaje']) ? intval($_POST['id_viaje']) : NULL;
			$idTranportista = $_SESSION['idEmpresa'];

			if(!$idViaje){
				$response = array('isvalid' => false,"msg" => "Datos incorrectos");
			}
			if(!is_numeric($_POST['valor'])){
				$response = array('isvalid' => false,"msg" => "Ingrese una cotización válida.");
			}

			$query = " SELECT vc_id FROM tbl_viajes_cotizaciones WITH(NOLOCK) WHERE vc_vi_id = {$idViaje} AND vc_transportista = {$idTranportista} ";
			$row = $objSQLServer->dbGetRow($objSQLServer->dbQuery($query), 0, 3);
			if(!$row){
				$params = array(
					'vc_vi_id' => $idViaje
					, 'vc_transportista' => $idTranportista
					, 'vc_valor' => $_POST['valor']
					, 'vc_observaciones' => escapear_string(encode($_POST['observacion']))
				);
				if(!$objSQLServer->dbQueryInsert($params, 'tbl_viajes_cotizaciones')){
					$response = array('isvalid' => false,"msg" => "Los datos no se registradon, vuelva a intentar.");
				}
				else{
					$query = " SELECT cl_razonSocial FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = {$idTranportista}";
					$row = $objSQLServer->dbGetRow($objSQLServer->dbQuery($query), 0, 3);

					include_once 'clases/clsViajes.php';
					$objViaje = new Viajes($objSQLServer, $idViaje);
					$objViaje->setLog('Cotización Asignada: $'.$params['vc_valor'].' por '.$row['cl_razonSocial'].(!empty($params['vc_observaciones'])?('. Obs: '.$params['vc_observaciones']):''));
				}
			}
			else{
				$params = array(
					 'vc_valor' => $_POST['valor']
					 , 'vc_observaciones' => escapear_string(encode($_POST['observacion']))
				);

				if(!$objSQLServer->dbQueryUpdate($params, 'tbl_viajes_cotizaciones', 'vc_id = '.$row['vc_id'])){
					$response = array('isvalid' => false,"msg" => "Los datos no fueron actualizados, vuelva a intentar.");
				}
				else{
					$query = " SELECT cl_razonSocial FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = {$idTranportista}";
					$row = $objSQLServer->dbGetRow($objSQLServer->dbQuery($query), 0, 3);

					include_once 'clases/clsViajes.php';
					$objViaje = new Viajes($objSQLServer, $idViaje);
					$objViaje->setLog('Cotización Asignada: $'.$params['vc_valor'].' por '.$row['cl_razonSocial'].(!empty($params['vc_observaciones'])?('. Obs: '.$params['vc_observaciones']):''));
				}
			}

			echo json_encode($response);
		break;
		case 'asignar-cotizacion':
			include_once 'includes/conn.php';
			$response = array('isvalid' => true, "msg" => "");
			$idViaje = intval($_POST['id_viaje']) ? intval($_POST['id_viaje']) : NULL;
			$idTranportista = intval($_POST['idtransportista']) ? intval($_POST['idtransportista']) : NULL;

			if(!$idViaje || !$idTranportista){
				$response = array('isvalid' => false,"msg" => "Datos incorrectos");
			}
			
			$params = array('vi_transportista' => $idTranportista);

			if(!$objSQLServer->dbQueryUpdate($params, 'tbl_viajes', 'vi_id = '.$idViaje)){
				$response = array('isvalid' => false,"msg" => "El viaje no fue asignado, vuelva a intentar.");
			}
			else{
				$query = " SELECT cl_razonSocial FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = {$idTranportista}";
				$row = $objSQLServer->dbGetRow($objSQLServer->dbQuery($query), 0, 3);

				include_once 'clases/clsViajes.php';
				$objViaje = new Viajes($objSQLServer, $idViaje);
				$objViaje->setLog('Viaje asignado a: '.$row['cl_razonSocial']);
			}

			echo json_encode($response);
		break;
		case 'playavirtual-verificado':
			include_once 'includes/conn.php';
			$idViaje = intval($_POST['id_viaje']) ? intval($_POST['id_viaje']) : NULL;
			$verificado = isset($_POST['value']) ? (($_POST['value'] === '1' || $_POST['value'] === '0') ? $_POST['value'] : -1) : -1;
			if($verificado > -1 && $idViaje){
				$params = array('vi_verificado' => $verificado);
				if($objSQLServer->dbQueryUpdate($params, 'tbl_viajes', 'vi_id = '.$idViaje)){
					echo 'true';
				}
			}
			exit;
		break;
		default:
			echo "error";
		break;
	}
}
exit;
