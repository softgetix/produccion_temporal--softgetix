<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje=""){
	
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	
	if ($action == 'guardarHorario') {
		guardarHorario($objSQLServer);
	}
	elseif($action == 'guardarHorarioSimple') {
		guardarHorarioSimple($objSQLServer);
	}
	elseif($action == 'borrarHorario') {
		borrarHorario($objSQLServer);
	}
	elseif($action === 'popup'){
		modificarHorarios($objSQLServer,'abmEquiposMoviles',$_GET['id'],true);	
	}
}

function modificarHorarios($objSQLServer, $seccion="", $idMovil=0, $popup=false){
	global $lang;
	require_once 'clases/clsEquipos.php';
	require_once 'clases/clsMoviles.php';
	$operacion = 'modificarHorarios';
	$tipoBotonera='AM';
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idMovil)? $idMovil: 0);
	$objMovil = new Movil($objSQLServer);
	$arrEntidades = $objMovil->obtenerRegistros($id);
	if($arrEntidades[0]['un_id']==NULL){
		$operacion='ErrorEquipoAsociado';
		$mensaje = 'El Movil no tiene equipo asociado';
                return;
	}else{
		$arrHorarios = $objMovil->obtenerHorariosMovil($arrEntidades[0]['un_id']);
                //pr($arrHorarios);
                if (is_array($arrHorarios)) {
					foreach ($arrHorarios as $horario) {
						if ($horario['Dia'] < 5 && isset($lav) == false) {
							$lav = $horario;
						}
						if ($horario['Dia'] > 5 && isset($fds) == false) {
							$fds = $horario;
						}
					}
				}
	}

        switch ($arrEntidades[0]['un_tipo_loc'])
        {
            case 'gps': { $precision = 3; break; }
            case 'antena': { $precision = 1; break; }
            default: { $precision = 2; break; }
        }
        
        switch ($arrEntidades[0]['un_tiempo'] / 60)
        {
            case 1: { $frecuencia = 1; break; }
            case 3: { $frecuencia = 2; break; }
            case 5: { $frecuencia = 3; break; }
            case 10: { $frecuencia = 4; break; }
            case 30: { $frecuencia = 5; break; }
            default: { $frecuencia = 1; break; }
        }
        //echo $frecuencia;
        

        if (isset($lav))
        {
            if ($lav['Tipo'] == 'gps') {
                $lav_precision = 3;
            } else if ($lav['Tipo'] == 'antena') {
                $lav_precision = 1;
            } else {
                $lav_precision = 2;
            }

            if ($lav['Tiempo'] == '30 min') {
                $lav_frecuencia = 5;
            } else if ($lav['Tiempo'] == '10 min') {
                $lav_frecuencia = 4;
            } else if ($lav['Tiempo'] == '5 min') {
                $lav_frecuencia = 3;
            } else if ($lav['Tiempo'] == '3 min') {
                $lav_frecuencia = 2;
            } else {
                $lav_frecuencia = 1;
            }
        } else {
            $lav_frecuencia = 1;
            $lav_precision = 1;
        }
        
        if (isset($fds))
        {
            if ($fds['Tipo'] == 'gps') {
                $fds_precision = 3;
            } else if ($fds['Tipo'] == 'antena') {
                $fds_precision = 1;
            } else {
                $fds_precision = 2;
            }

            if ($fds['Tiempo'] == '30 min') {
                $fds_frecuencia = 5;
            } else if ($fds['Tiempo'] == '10 min') {
                $fds_frecuencia = 4;
            } else if ($fds['Tiempo'] == '5 min') {
                $fds_frecuencia = 3;
            } else if ($fds['Tiempo'] == '3 min') {
                $fds_frecuencia = 2;
            } else {
                $fds_frecuencia = 1;
            }
        } else {
            $fds_frecuencia = 1;
            $fds_precision = 1;
        }
        
	if($popup){
		$extraCSS[] = 'css/estilosAbmPopup.css';
		$extraCSS[] = 'css/popup.css';
		$extraJS[] = 'js/popupFunciones.js?1';
		require("includes/frametemplate.php");
	}else{
		require("includes/template.php");
	}
}


function guardarHorario_old($objSQLServer) {
	global $lang;
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil ($objSQLServer);
	
	$id		= isset($_GET['id']) 		? $_GET['id'] 			: null;
	$dia		= isset($_GET['dia']) 		? $_GET['dia'] 			: null;
	$desde 			= isset($_GET['desde']) 		? $_GET['desde']			: '0000';
	$hasta 			= isset($_GET['hasta']) 		? $_GET['hasta']			: '0000';
	$tiempo 			= isset($_GET['tiempo']) 		? $_GET['tiempo']			: null;
	$tipo			= isset($_GET['tipo']) 		? $_GET['tipo']			: null;
	if($resultado  = $objMovil->insertarHorarioMovil($id,$dia,$desde,$hasta,$tiempo,$tipo))
	{
			$return['result']=$resultado; 
			$return['msg']="ok";
	}else{
			$return['result']=$resultado;
			$return['msg']="Error";
	}
	echo json_encode($return);
}

function guardarHorario($objSQLServer) {
	global $lang;
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil ($objSQLServer);
	
	
	$id		= isset($_GET['id']) 		? $_GET['id'] 			: null;
	$dia		= isset($_GET['dia']) 		? $_GET['dia'] 			: null;
	$desde 			= isset($_GET['desde']) 		? $_GET['desde']			: '0000';
	$hasta 			= isset($_GET['hasta']) 		? $_GET['hasta']			: '0000';
	$tiempo 			= isset($_GET['tiempo']) 		? $_GET['tiempo']			: null;
	$tipo			= isset($_GET['tipo']) 		? $_GET['tipo']			: null;
	$precision = isset($_GET['precision']) ? $_GET['precision'] : null;
	$frecuencia = isset($_GET['frecuencia']) ? $_GET['frecuencia'] : null;
	$activo = isset($_GET['activo']) ? $_GET['activo'] : null;
	
	switch ($precision) {
		case 1: { $tipo = 'antena'; break; }
		case 2: { $tipo = 'antena / gps'; break; }
		case 3: { $tipo = 'gps'; break; }
	}
	
	switch ($frecuencia) {
		case 1: { $t = 1; break; }
		case 2: { $t = 3; break; }
		case 3: { $t = 5; break; }
		case 4: { $t = 10; break; }
		case 5: { $t = 30; break; }
		default: { $t = 5; break; }
	}
	$tiempo = $t * 60;	

	$return = array('result' => "", "msg" => "ok");
	
	if ($dia == 'lav' || $dia == 'fds')
	{
		borrarHorarios($objSQLServer);
		
		$desde = str_replace(":", "", $desde);
		$hasta = str_replace(":", "", $hasta);		
		
		if ($dia == 'lav') {
			$dias = array(1,2,3,4,5);
		} else {
			$dias = array(6,7);
		}
		
			
                if ($activo == 1)
                {
                    foreach ($dias as $dia) {
                            $resultado  = $objMovil->insertarHorarioMovil($id,$dia,$desde,$hasta,$tiempo,$tipo);
                            if ($resultado === false) {
                                    $return['result']="false";
                                    $return['msg']="Error";
                                    break;
                            }
                    }
                }
	}
	die(json_encode($return));
}

function guardarHorarioSimple($objSQLServer) {
	global $lang;
	
	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
    $arrDatos = array();
	
	$id = isset($_GET['id']) ? $_GET['id'] : null;
	$precision = isset($_GET['precision']) ? $_GET['precision'] : null;
	$frecuencia = isset($_GET['frecuencia']) ? $_GET['frecuencia'] : null;
	
	switch ($precision) {
		case 1: { $tipo = 'antena'; break; }
		case 2: { $tipo = 'antena / gps'; break; }
		case 3: { $tipo = 'gps'; break; }
	}
	
	switch ($frecuencia) {
		case 1: { $t = 1; break; }
		case 2: { $t = 3; break; }
		case 3: { $t = 5; break; }
		case 4: { $t = 10; break; }
		case 5: { $t = 30; break; }
		default: { $t = 5; break; }
	}
	
        $arrDatos['un_tipo_loc'] = $tipo;
        $arrDatos['un_tiempo'] = $t * 60;	

        $resultado  = $objEquipo->modificarUnidad($id, $arrDatos);
        
        
        $return['result'] = "";
        $return['msg'] = "ok";
	echo json_encode($return);
}

function borrarHorario($objSQLServer) {
	global $lang;
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil ($objSQLServer);
	$id		= isset($_GET['id']) 		? $_GET['id'] 			: null;
	if($objMovil->borrarHorarioMovil($id))
	{
			$return['msg']="ok";
	}else{
		
			$return['msg']="Error";
	}
	echo json_encode($return);
}

/**
 * Tengo que borrar los horarios para poder insertar los nuevos
 */
function borrarHorarios($objSQLServer) {
	global $lang;
	require_once 'clases/clsMoviles.php';
	
	$objMovil = new Movil ($objSQLServer);
	$id = isset($_GET['id']) 		? $_GET['id'] 			: null;
	$dia = isset($_GET['dia']) 		? $_GET['dia'] 			: null;
	$idMovil = isset($_GET['movilId']) ? $_GET['movilId'] : null;
	$return = array();
        
	if ($dia == 'lav' || $dia == 'fds') 
	{
		$arrEntidades = $objMovil->obtenerRegistros($idMovil);
		$arrHorarios = $objMovil->obtenerHorariosMovil($arrEntidades[0]['un_id']);
		if (is_array($arrHorarios))
		{
			if ($dia == 'lav') {
				$dias = array(1,2,3,4,5);
			} else {
				$dias = array(6,7);
			}
			
			foreach ($dias as $dia) {
				foreach ($arrHorarios as $horario) {
					if ($horario['Dia'] == $dia) {
						if($objMovil->borrarHorarioMovil($horario['Id']))
						{
								$return['msg']="ok";
						}else{
							
								$return['msg']="Error";
						}
					}
				}
				
			}
		}
	}
	
    //echo json_encode($return);

}

function guardarAsignacion($objSQLServer, $seccion){
	global $lang;
	//GUARDA LAS MODIFICACIONES
	$idMovil = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	$idEquipoPrimario = (isset($_POST["hidIdEquipoPrimario"]))? $_POST["hidIdEquipoPrimario"]:"";

	//OBTENGO UNA CADENA CON LOS ID DE LOS EQUIPOS ASIGNADOS SEPARADOS POR COMA
	$list2Serialised = (isset($_POST["hidEquiposSerializados"]))? $_POST["hidEquiposSerializados"]:"";

	//SEPARO LOS ID EN UN ARRAY
	$arrEquipos = explode(",", $list2Serialised);

	//RECORRO EL ARRAY Y LE ELIMINO LOS ESPACIOS EN BLANCO DEL PRINCIPIO Y FINAL DE CADA ELEMENTO
	for($i=0; $i < count($arrEquipos) && $arrEquipos; $i++){
		$arrEquipos[$i]=trim($arrEquipos[$i]);
	}

	//ORDENO EL ARRAY DE MENOR A MAYOR
	sort($arrEquipos);

	if($arrEquipos){
		require_once 'clases/clsEquipos.php';
		$objEquipo = new Equipo($objSQLServer);
		$flag=0;
		if($objEquipo->eliminarAsignacionesEquiposMovil($idMovil)){
			$max = count($arrEquipos);
			for($i=0; $i < $max; $i++){
				if($objEquipo->insertarAsignacionEquipoMovil($idMovil,$arrEquipos[$i])){
					if($arrEquipos[$i]==$idEquipoPrimario){
						$objEquipo->asignarEquipoPrimario($arrEquipos[$i]);
					}else{
						$objEquipo->desasignarEquipoPrimario($arrEquipos[$i]);
					}
					$mensaje = $lang->message->ok->msj_modificar;

				}else{
					$mensaje = $lang->message->error->msj_modificar;
					$flag=1;
					break;
				}
			}
			if($flag==0)index($objSQLServer, $seccion, $mensaje);
			else modificar($objSQLServer, $seccion, $mensaje,$idMovil);
		}else{
			$mensaje = $lang->message->error->msj_modificar;
			modificar($objSQLServer, $seccion, $mensaje,$idMovil);
		}
	}else{
		$mensaje = $lang->message->error->msj_modificar;
		//redireccionar a la modificacion con los datos cargados.
		modificar($objSQLServer, $seccion, $mensaje,$idMovil);
	}
}

function volver($objSQLServer, $seccion){
	index($objSQLServer, $seccion);
}

