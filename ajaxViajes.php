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
		case 'popup-agregar-movil-arauco':
			include_once 'includes/conn.php';
			require_once 'clases/clsViajes.php';

			$id_viaje = (int)$_POST['id_viaje'];
			$id_transportista = (int)$_POST['id_transportista'];
			$id_movil = (int)$_POST['id_movil'];
			$id_usuario = (int)$_SESSION['idUsuario'];
			$id_conductor = (int)$_POST['id_conductor'];

			$objViaje = new Viajes($objSQLServer, $_POST['id_viaje']);

			if(tienePerfil(5)){
				$transportistas = $objViaje->getTransportista(array('id_usuario' => $id_usuario));
				$arrMotivosCambio = $objViaje->getMotivoViajes();
			}
			

			$conductor = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_drivers_available {$id_usuario},{$id_transportista}"),3);
			$patente = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_vehicles_available {$id_usuario}, {$id_transportista}, {$id_viaje}"),3);
			
			$Semi = array();
			$Configuracion = array();
			$Cargabruta = array();
			$Tara = array();

			if($id_movil){
				$Semi = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_second_vehicles_available {$id_usuario},{$id_movil},{$id_viaje}"));
				$Configuracion = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_configuration_available {$id_usuario},{$id_movil},{$id_viaje}"));
				$Cargabruta = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_load_available {$id_usuario},{$id_movil},{$id_viaje}"));
				$Tara = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_tara_available {$id_usuario},{$id_movil},{$id_viaje}"));
			}
			?>
            <form name="form_popup" id="form_popup">
        	    <input type="hidden" name="accion" value="popup-guardar-patente-arauco" />
				<?php if(isset($transportistas)){?>
				<fieldset>
                	<select name="popup_transportista" id="popup_transportista" onchange="javacript:getPatentesConductoresArauco(<?=$id_usuario ?>, this.value, <?=$id_viaje?>, 'patente','conductor');">
                    	<option value=""><?=$lang->system->seleccione_transportista?></option>
                        <?php foreach($transportistas as $item){?>
                        <option value="<?=$item['cl_id']?>" <?=($item['cl_id']==$id_transportista)?'selected':''?>><?=encode($item['cl_razonSocial'])?></option>
						<?php }?>
					</select>
				</fieldset>
				<? }?>
                <fieldset>
                	<select name="vehicle_id" id="patente" onchange="javacript:patenteChange(this, <?=$id_usuario?>);">
                    	<option value="">Patente Tractor</option>
						<?php foreach($patente as $item){?>
                        <option value="<?=$item['vehicle_id']?>" <?=($item['vehicle_id']==$id_movil || $item['mo_default'])?'selected':''?>><?=encode($item['vehicle'])?></option>
						<?php }?>
					</select>
				</fieldset>
				<fieldset>
                	<select name="second_vehicle_idSemi" id="Semi">
                    	<option value="">Patente Semirremolque</option>
						<?php foreach($Semi as $item){?>
                        <option value="<?=$item['second_vehicle_id']?>" <?=($item['second_vehicle_default'])?'selected':''?>><?=encode($item['second_vehicle'])?></option>
						<?php }?>
					</select>
				</fieldset>
				<fieldset>
                	<select name="configuration_id" id="Configuracion">
                    	<option value="">Configuración</option>
						<?php foreach($Configuracion as $item){?>
                        <option value="<?=$item['configuration_id']?>" <?=( $item['configuration_default'])?'selected':''?>><?=encode($item['configuration_description'])?></option>
						<?php }?>
					</select>
				</fieldset>
				<fieldset>
                	<select name="load_id" id="Cargabruta">
                    	<option value="">Carga bruta</option>
						<?php foreach($Cargabruta as $item){?>
                        <option value="<?=$item['load_id']?>" <?=( $item['load_default'])?'selected':''?>><?=encode($item['load_description'])?></option>
						<?php }?>
					</select>
				</fieldset>
				<fieldset>
                	<select name="tara_id" id="Tara">
                    	<option value="">Tara</option>
						<?php foreach($Tara as $item){?>
                        <option value="<?=$item['tara_id']?>" <?=( $item['tara_default'])?'selected':''?>><?=encode($item['tara_description'])?></option>
						<?php }?>
					</select>
				</fieldset>
				<fieldset>
                	<select name="driver_id" id="conductor">
					<option value="">Conductor</option>
					    <?php foreach($conductor as $item){?>
                        <option value="<?=$item['driver_id']?>" <?=($item['driver_id']==$id_conductor)?'selected':''?>><?=encode($item['driver'])?></option>
						<?php }?>
					</select>
				</fieldset>
				<fieldset>
					<select id="hour" name="hour"> 
						<option value="">Hora estimada de arribo</option>
						<option value="0">00:00</option>
						<option value="1">01:00</option>
						<option value="2">02:00</option>
						<option value="3">03:00</option>
						<option value="4">04:00</option>
						<option value="5">05:00</option>
						<option value="6">06:00</option>
						<option value="7">07:00</option>
						<option value="8">08:00</option>
						<option value="9">09:00</option>
						<option value="10">10:00</option>
						<option value="11">11:00</option>
						<option value="12">12:00</option>
						<option value="13">13:00</option>
						<option value="14">14:00</option>
						<option value="15">15:00</option>
						<option value="16">16:00</option>
						<option value="17">17:00</option>
						<option value="18">18:00</option>
						<option value="19">19:00</option>
						<option value="20">20:00</option>
						<option value="21">21:00</option>
						<option value="22">22:00</option>
						<option value="23">23:00</option>
					</select> 
				</fieldset>
				<?php if(isset($arrMotivosCambio)){?>
				<fieldset>
                    <label for="motivo_name"><?=$lang->message->msj_motivo_update?>: </label>			
                    <select id="motivo_name" name="motivo_name" >
                        <option value="0"><?=$lang->system->ninguno?></option>
                        <?php foreach($arrMotivosCambio as $item){ ?>
                            <option value="<?=$item['vmc_id']?>"><?=encode($item['vmc_descripcion'])?></option>
                        <?php } ?>
                    </select>
				</fieldset>
				<?php }?>
				<fieldset>
                	<a href="javascript:setVehiculoConductorDeliveryArauco(<?=$id_viaje?>, $('#patente').val(), $('#Semi').val(), $('#Configuracion').val(), $('#Cargabruta').val(), $('#Tara').val(), $('#conductor').val(), $('#hour').val(), $('#motivo_name').val());" class="button colorin" style="width:322px;"><?=$lang->botonera->guardar?></a>
				</fieldset>
				<?php if(tienePerfil(5)){?>
				<br>
				<fieldset>
                	<a href="javascript:setRevocarAsignacionVehiculoArauco(<?=$id_viaje?>, $('#motivo_name').val());" class="button colorRed" style="width:322px;">Revocar Asignación</a>
				</fieldset>
				<?php }?>
                <p id="motivo_error" style="display:none;color:#AC0C0C; margin-top:5px;"></p>
			</form>
			<?php 
			exit;
		break;	
		case 'get-patentes-process':
			include_once 'includes/conn.php';

			$id_usuario = (int)$_POST['id_usuario'];
			$id_transportista = (int)$_POST['transportista'];
			$id_viaje = (int)$_POST['id_viaje'];

			$patente = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_vehicles_available {$id_usuario}, {$id_transportista}, {$id_viaje}"),3);
			echo json_encode($patente);
			exit;
		break;	
		case 'get-patente-change':
			include_once 'includes/conn.php';

			$id_usuario = (int)$_POST['id_usuario'];
			$id_movil = (int)$_POST['patente'];
			$id_viaje = (int)$_POST['id_viaje'];

			$Semi = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_second_vehicles_available {$id_usuario},{$id_movil}"));
			$Configuracion = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_configuration_available {$id_usuario},{$id_movil}"));
			$Cargabruta = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_load_available {$id_usuario},{$id_movil}"));
			$Tara = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_tara_available {$id_usuario},{$id_movil}"));

			echo json_encode(array('semi' => $Semi, 'config' => $Configuracion, 'cargabruta' => $Cargabruta, 'tara' => $Tara));
		break;	
		case 'get-conductores-process':
			include_once 'includes/conn.php';

			$id_usuario = (int)$_POST['id_usuario'];
			$id_transportista = (int)$_POST['transportista'];
			
			$conductor = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_drivers_available {$id_usuario},{$id_transportista}"),3);
			echo json_encode($conductor);
			exit;
		break;
		case 'popup-guardar-patente-arauco':
			include_once 'includes/conn.php';
			
			$us_id = $_SESSION['idUsuario'];
			$id_viaje = !empty($_POST['id_viaje']) ? (int)$_POST['id_viaje'] : NULL;
			$id_movil = !empty($_POST['id_movil']) ? (int)$_POST['id_movil'] : NULL;
			$id_semi = !empty($_POST['id_semi']) ? (int)$_POST['id_semi'] : NULL;
			$configuracion = !empty($_POST['configuracion']) ? trim($_POST['configuracion']) : NULL;
			$cargabruta = !empty($_POST['cargabruta']) ? trim($_POST['cargabruta']) : NULL;
			$tara = !empty($_POST['tara']) ? trim($_POST['tara']) : NULL;
			$id_conductor = !empty($_POST['id_conductor']) ? (int)$_POST['id_conductor'] : NULL;
			$hora = !empty($_POST['hora']) ? trim($_POST['hora']) : NULL;
			$motivo = !empty($_POST['motivo']) ? trim($_POST['motivo']) : NULL;
			
			
			$msg = array('error' => false, 'msg' => NULL);
		
			if (empty($id_viaje)) {
				$msg['error'] = true;
				$msg['msg'] = "Identificador de viaje no encontrado.";
			}			
			elseif (empty($id_movil)) {
				$msg['error'] = true;
				$msg['msg'] = "Debe indicar una patente.";
			}
			elseif (empty($id_semi) || empty($configuracion) || empty($cargabruta)) {
				$msg['error'] = true;
				$msg['msg'] = "Debe indicar el Semi, Configuración y Carga Bruta.";
			}
			elseif (empty($id_conductor)) {
				$msg['error'] = true;
				$msg['msg'] = "Debe indicar un conductor.";
			}
			elseif (empty($tara)) {
				$msg['error'] = true;
				$msg['msg'] = "Debe indicar el valor de Tara.";
			}
			
			if(!$msg['error']){
				$objSQLServer->dbQuery("EXEC dbo.db_ws_trip_assigned_to {$us_id},{$id_viaje},{$id_movil}, {$id_conductor}, {$id_semi}, {$configuracion}, {$cargabruta} , {$tara} ,{$hora}");
				$msg['msg'] = "Patente asignada correctamente.";
			}

			echo json_encode($msg);
			exit;
		break;
		case 'popup-revocar-asingacion-arauco':
			include_once 'includes/conn.php';
			
			$us_id = $_SESSION['idUsuario'];
			$id_viaje = !empty($_POST['id_viaje']) ? (int)$_POST['id_viaje'] : NULL;
			$motivo = !empty($_POST['motivo']) ? (int)$_POST['motivo'] : NULL;
			
			$msg = array('error' => false, 'msg' => NULL);
		
			if (empty($id_viaje)) {
				$msg['error'] = true;
				$msg['msg'] = "Identificador de viaje no encontrado.";
			}			
			elseif (empty($motivo)) {
				$msg['error'] = true;
				$msg['msg'] = "Debe indicar el motivo de la revocación.";
			}
			
			if(!$msg['error']){
				$objSQLServer->dbQuery("EXEC db_ws_trip_canceled {$us_id},{$id_viaje},{$motivo}");
				$msg['msg'] = "Asignación revocada!";
			}

			echo json_encode($msg);
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
				//$datos['zona'] = $_POST['geozona'];
				//--Obtenemos por id de referencia
					$aux = explode('#',$_POST['geozona']);
					$datos['re_id'] = end($aux);
					$datos['zona'] = trim(str_replace('#'.$datos['re_id'], '',$_POST['geozona']));
				//--
				
				$datos['no_id_zona'] = $_POST['id_geozona'];

				if($_SESSION['seccion'] == 'retirosforza' || $_SESSION['seccion'] == 'entregasforza'){
					include_once 'clases/clsReferencias.php';
					$objReferencia = new Referencia($objSQLServer);
	
					$listado = ($_SESSION['seccion'] == 'entregasforza') ? 29 : 30;
					$zonas = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION['idEmpresa'], NULL, $datos['re_id'], '119,120', $listado);
				}
				else{
					$zonas = $objViaje->getRuteo($datos);
				}
					
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

			if($_SESSION['seccion'] == 'retirosforza' || $_SESSION['seccion'] == 'entregasforza'){
				include_once 'clases/clsReferencias.php';
				$objReferencia = new Referencia($objSQLServer);

				$listado = ($_SESSION['seccion'] == 'entregasforza') ? 29 : 30;
				$aux = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION['idEmpresa'], $_POST['buscar'], NULL, '119,120', $listado);
				$zonas['geozonas'] = array();
				if($aux){
					foreach($aux as $k => $z){

						$etiqueta = trim($z['re_numboca']).' '.trim($z['re_nombre']).(!empty($z['re_ubicacion'])? (', '.trim($z['re_ubicacion'])) : '');
						array_push($zonas['geozonas'], array(
							'etiqueta' => encode(trim($etiqueta))
							,'re_nombre' => (encode($z['re_nombre']).' #'.$z['re_id'])
						));
					}
				}
			}
			else{
				include_once 'clases/clsViajes.php';
				$objViaje = new Viajes($objSQLServer);
				$datos['zona_like'] = $_POST['buscar'];
				$datos['top'] = 30;
				$datos['no_id_zona'] = $_POST['id_geozona'];

				$zonas['geozonas'] = $objViaje->get_geozonas($datos);
				foreach($zonas['geozonas'] as $k => $z){
					$zonas['geozonas'][$k]['etiqueta'] = encode($z['etiqueta']);
					$zonas['geozonas'][$k]['re_nombre'] = encode($z['re_nombre']).' #'.$z['re_id'];
				}
			}

			echo json_encode($zonas);
		break;	
		case 'calculo-fecha':
			$_POST['duracion'] = str_replace('NaN','0',$_POST['duracion']);
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

			//--Ajuste Pallets swapp
			if($_SESSION['seccion'] == 'entregasforza' || $_SESSION['seccion'] == 'retirosforza'){
				array_unshift($transportista, array('cl_id' => 13318, 'cl_razonSocial' => 'Transportista no informado'));
			}
			//--

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

			//--Ajuste Pallets swapp
			if($_SESSION['seccion'] == 'entregasforza' || $_SESSION['seccion'] == 'retirosforza'){
				array_unshift($conductor, array('co_id' => 9929, 'co_nombre' => 'Conductor no informado', 'co_apellido' => ''));
			}
			//--
			
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
		case 'change-facturado':
			include_once $rel.'includes/conn.php';
			
			$idviaje = (int)$_POST['idviaje'];
			$facturado = ($_POST['facturado'] == 1) ? 1 : 0;
			if($idviaje){
				if($objSQLServer->dbQueryUpdate(array('vi_facturado' => $facturado), 'tbl_viajes', 'vi_id = '.$idviaje)){
					echo 'true';
					exit;
				}
			}
			echo 'false';
			exit;
		break;
		case 'msg-motivo-mail':
			include_once $rel.'includes/conn.php';
			
			$vi_codigo = trim($_POST['vi_codigo']);
			if($vi_codigo){
				$query = "EXEC Pallets_pedido_turno_por_email '{$vi_codigo}'";
				if($objSQLServer->dbQuery($query)){
					return true;
				}
			}
			return false;
			exit;
		break;
		default:
			echo "error";
		break;
	}
}
exit;
