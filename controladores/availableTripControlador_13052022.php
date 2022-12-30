<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

function index($objSQLServer, $seccion,$msg=''){

	$us_id = $_SESSION['idUsuario']; 
	 
	$query = "EXEC db_ws_trip_available {$us_id}";
	
	$query = !empty($_POST['origin_id']) ? $query.", ".$_POST['origin_id'] :$query.', 0';
	$query = !empty($_POST['route_id']) ? $query.", ".$_POST['route_id'] :$query.', 0';
	$query = !empty($_POST['s_trip_code']) ? $query.", '".$_POST['s_trip_code']."'" :$query;
	
	//die ($query);

	$result = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);		
	 
	$origen = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_starting_point_list {$us_id}"),3);

	$ruta = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_route_id {$us_id}"),3);

	$patente = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_vehicles_available {$us_id}"),3);

	$conductor = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_drivers_available {$us_id}"),3);

	$extraCSS[]='css/abmViajes.css';
	$extraCSS[] = 'css/ui/jquery.ui.datepicker.css';
	$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$extraJS[] = 'js/jquery/jquery.datepicker.js';
	$extraCSS[]='css/estilosPopup.css';
    require("includes/template.php");
}



function verDetalle($objSQLServer, $seccion){

		$us_id = $_SESSION['idUsuario'];

		if(isset($_POST['accion']) && !empty($_POST['accion'])){
			if ($_POST['accion'] == "ver_detalle") {

				if (isset($_POST['trip_id']) && !empty($_POST['trip_id'])) {
					$trip_detail = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_available_details {$us_id},".$_POST['trip_id']));
					echo json_encode($trip_detail);die;
				}else{
					echo json_encode("Error");die;
				}	
			} 
		}
}

function assignTrip($objSQLServer, $seccion){

	$us_id = $_SESSION['idUsuario'];

	if (isset($_POST['trip_id']) && !empty($_POST['trip_id'])) {
	 if (isset($_POST['vehicle_id']) && !empty($_POST['vehicle_id'])) {
	  if (isset($_POST['driver_id']) && !empty($_POST['driver_id'])) {
	    if (isset($_POST['second_vehicle_id']) && isset($_POST['configuration_id']) && isset($_POST['load_id'])) {
			if (isset($_POST['tara_id']) && !empty($_POST['tara_id'])) {
			  	$asignado = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC dbo.db_ws_trip_assigned_to {$us_id},".$_POST['trip_id'].",".$_POST['vehicle_id'].",".$_POST['driver_id'].",".$_POST['second_vehicle_id'].",".$_POST['configuration_id'].",".$_POST['load_id'].",".$_POST['tara_id'].",".$_POST['hour']));
			  	
				if ($asignado[0]['result'] == '-1')
					{
					$msg['error'] = true;
					$msg['msg'] = "Carga tomada por otro transportista.";
					}
					else
					{
					$msg['msg'] = "Carga tomada correctamente.";
					}
				

			}else{
				$msg['error'] = true;
				$msg['msg'] = "Carga no tomada. Tara no definida.";
		   }  
		}else{
		    $msg['error'] = true;
			$msg['msg'] = "Carga no tomada. verifique sus datos.";
		}
	  }else{
	  	    $msg['error'] = true;
			$msg['msg'] = "Carga no tomada. COnductor no asignado.";
	  }
	 }else{
	      $msg['error'] = true;
		  $msg['msg'] = "Carga no tomada. Patente / Semi no asignado.";
	 }
	}else{
		$msg['error'] = true;
		$msg['msg'] = "Carga no tomada. Carga no encontrada.";
	}

	index($objSQLServer, $seccion,$msg);

}

function addComboboxValue($objSQLServer, $seccion){

		$us_id = $_SESSION['idUsuario'];

		if(isset($_POST['patente']) && !empty($_POST['patente'])){

			$Semi = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC db_ws_trip_second_vehicles_available {$us_id},".$_POST['patente']));

			$Configuracion = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC [db_ws_trip_configuration_available] {$us_id},".$_POST['patente']));

			$Cargabruta = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC [db_ws_trip_load_available] {$us_id},".$_POST['patente']));

			$Tara = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery("EXEC [db_ws_trip_tara_available] {$us_id},".$_POST['patente']));

			$combodetail = ["Semi"=>$Semi,"Configuracion"=>$Configuracion,"Cargabruta"=>$Cargabruta,"Tara"=>$Tara];
			
			echo json_encode($combodetail);die;

		}else{
			echo json_encode("Error");die;
		}	

}