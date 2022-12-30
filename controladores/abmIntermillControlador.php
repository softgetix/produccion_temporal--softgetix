<?php

$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";
$sinDefaultCSS = $sinDefaultJS = true;

function index($objSQLServer, $seccion, $mensaje = "") {
    $action = isset($_GET['action']) ? $_GET['action'] : 'index';
    
	 $filtro = "";
    if ($action === 'buscar') {
        busqueda($objSQLServer, $seccion);
    } else if ($action === 'calendar') {
        //calendar($objSQLServer, $seccion);
    } else if ($action === 'popup') {
        //alta($objSQLServer,$seccion,$mensaje,array(),true);
    } else {
        $operacion = 'listar';
        $tipoBotonera = 'LI';
        $extraCSS = array(
			'css/estilosAbmListadoDefault.css',
			'css/demo_page.css',
			'css/demo_table_jui.css',
			'css/TableTools.css',
			'css/estilosPopup.css',
			'css/smoothness/jquery-ui-1.8.4.custom.css',
		);
			
        $extraJS = array(
			'js/jquery.dataTables.js',
			'js/jquery.autofill.js', 
			'js/media/js/TableTools.js',
			'js/media/js/ZeroClipboard.js',
			'js/jquery.ui.js',
			'js/popupHostFunciones.js', 
			'js/jquery.blockUI.js'
		);

        $extraCSS[] = 'css/fullcalendar.css';
        $extraJS[] = 'js/jquery-ui-1.8.17.custom.min.js';
        $extraJS[] = 'js/fullcalendar.min.js';
        $extraJS[] = 'js/abmIntermillListar.js';

        require 'includes/template.php';
    }
}

function busqueda($objSQLServer, $seccion, $return = false) {
    global $lang;

    require_once 'clases/clsIntermill.php';
	$objIntermill = new Intermill($objSQLServer);

    $inicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
    $fin = isset($_GET['fin']) ? $_GET['fin'] : '';
	
    if ($inicio == '') {
        $fin = date("Y-m-d");
        $inicio = date("Y-m-d", time() - 7200);
    }else {
        $inicio = dateToDataBase($inicio);
        if ($fin != '') {
			$fin = dateToDataBase($fin);
		} else {
			$fin = $inicio;
		}
    }
    $inicio .= ' 00:00:00';
    $fin .= ' 23:59:59';

	if(strtotime($inicio) < strtotime("2012-02-29 23:59:59")) {
		$inicio = "2012-03-01 00:00:00";
		if (strtotime($fin) <= strtotime("2012-02-29 23:59:59") && strtotime($fin) > 0) {
			$fin = "2012-03-01 00:00:00";
		}
	}
	
    $busqueda = $objIntermill->obtenerDatos($inicio, $fin);
    if ($busqueda !== false){
		$busqueda = agrupar($busqueda);
	}
    
    if ($return === true) {
		return $busqueda;
	}

    if ($busqueda) {
        $temp2->aaData = $busqueda;
        $json = json_encode($temp2);
        header('Content-Type: application/json');
       	echo $json;
    } else {
        $out->msg = $lang->message->sin_resultados;
        $out->status = 2;
        $out->aaData = array();
        $json = json_encode($out);
        header('Content-Type: application/json');
        echo $json;
    }
}

function agrupar($arr) {
    //echo "<pre>";
    //pr($arr);die();
    $patenteAnterior = "";
    $NombreCorto = 0;
    $numeroOrden = -999;
    $nuevoarray = array();
    
    
    // Remuevo los registros sin nro de orden y estadia menor a 15 min.
    foreach ($arr as $k => $arr2) {
        // 210312 Se cambión  ($arr2["NumeroOrden"] == "-"  por  ($arr2["NumeroOrden"] == 0 
        
	if ($arr2["NumeroOrden"] == 0 && $arr2['TiempoEstadia'] < 15 && $arr2['TiempoEstadia'] >= 0) {
            if ($arr2['Vehiculo'] == 'FDF842') {
                $a = 1;
            }
            //pr($arr[$k]);
            //echo "Borrando: "; pr($arr[$k]);
            unset($arr[$k]);
        }
    }

    foreach ($arr as $reg) {
        //pr($reg);die();
        $msg = null;
        $new = false;
		
        if ($reg['Vehiculo'] == 'FDF842') {
            $a = 1;
        }
        
        if ($reg['Vehiculo'] == $patenteAnterior && $reg['NombreCorto'] == $NombreCorto && $reg['NumeroOrden'] == $numeroOrden) {
			
            $q = count($nuevoarray) - 1;
            
            //$nuevoarray[$q]['Vehiculo'] .= " *";
            $nuevoarray[$q]['FechaEgreso'] = $reg['FechaEgreso'];
            //$nuevoarray[$q][6] = $reg[6];
            
            $nuevoarray[$q]['FechaEgresoProgramado'] = $reg['FechaEgresoProgramado'];
            //$diff = ($nuevoarray[$q]['Egreso'] - $nuevoarray[$q]['Ingreso']) / 60;
            //$egreso = $nuevoarray[$q]['Egreso'];
            $egreso = $reg['Egreso'];
            $ingreso = $nuevoarray[$q]['Ingreso'];
            $diff = ($egreso - $ingreso) / 60;
            if ($diff > 0) {
				$nuevoarray[$q]['TiempoEstadia'] = (int) $diff;
			} else {
				$nuevoarray[$q]['TiempoEstadia'] = "";
			}
            //$nuevoarray[$q]['msg'] .= $msg . "<br/>";
        } else {
            //$reg['msg'] = $msg;
            $nuevoarray[] = $reg;
        }

        $NombreCorto = $reg['NombreCorto'];
        $patenteAnterior = $reg['Vehiculo'];
        $numeroOrden = $reg['NumeroOrden'];
    }
    
    // Remuevo los registros sin nro de orden y estadia menor a 15 min.
    foreach ($nuevoarray as $k => $arr) {
        if ($arr['NumeroOrden'] == 0 && $arr['TiempoEstadia'] < 15 && $arr['TiempoEstadia'] >= 0) {
			//pr($nuevoarray[$k]);
			//echo "Borrando: "; pr($nuevoarray[$k]);
            unset($nuevoarray[$k]);
        }
    }    
    
	// Si hay una deteccion sin orden y una con orden, agruparlas conservando el 
	$NombreCorto = $patenteAnterior = $numeroOrden = -1;
	$original = $nuevoarray;
	foreach ($nuevoarray as $k => $reg){
		

		if ($NombreCorto == $reg['NombreCorto'] && $patenteAnterior == $reg['Vehiculo'])
		{
			// Si el registro anterior no pertenece a ninguna orden (es 0) 
			// y el actual pertenece a una orden, quitamos el anterior
			// y conservamos la fecha de ingreso
			if ($numeroOrden == 0 && $reg['NumeroOrden'] > 0) {
				//echo "Borrando: "; 
				//pr($nuevoarray[$k]);
				// Pongo la fecha de ingreso real.
				$nuevoarray[$k]['FechaIngreso'] = $original[$keyAnterior]['FechaIngreso'];
				$nuevoarray[$k]['Ingreso'] = $original[$keyAnterior]['Ingreso'];
				$diff = (int) ($nuevoarray[$k]['Egreso'] - $nuevoarray[$k]['Ingreso']) / 60;
				if ($diff > 0) {
					$diff = intval($diff);
					$nuevoarray[$k]['TiempoEstadia'] = "{$diff}m (".tiempo_minutos($diff).")";
				} else {
					$nuevoarray[$k]['TiempoEstadia'] = "";
				}
				unset($nuevoarray[$keyAnterior]);
			}

			// Si el registro posterior a una orden con numero 
			// es una orden sin numero, toma su fecha de egreso.
			if ($numeroOrden > 0 && $reg['NumeroOrden'] == 0)
			{
				if ($nuevoarray[$keyAnterior]['noPoseeIngreso'] == 1) 
				{
					// Al numero de orden le sigue una deteccion automatica.
					// Copiamos la fecha de ingreso.
					$nuevoarray[$keyAnterior]['FechaIngreso'] = $reg['FechaIngreso'];
					$nuevoarray[$keyAnterior]['Ingreso'] = $reg['Ingreso'];
				}
				
				$nuevoarray[$keyAnterior]['FechaEgreso'] = $reg['FechaEgreso'];
				$nuevoarray[$keyAnterior]['Egreso'] = $reg['Egreso'];
				$nuevoarray[$keyAnterior]['noPoseeIngreso'] = 0;
				$nuevoarray[$keyAnterior]['noPoseeEgreso'] = 0;

				$diff = (int) ($nuevoarray[$keyAnterior]['Egreso'] - $nuevoarray[$keyAnterior]['Ingreso']) / 60;
				$nuevoarray[$keyAnterior]['TiempoEstadia'] = (int) $diff;
				unset($nuevoarray[$k]);
				
				if ($diff > 0) {
					$diff = intval($diff);
					$nuevoarray[$keyAnterior]['TiempoEstadia'] = "{$diff}m (".tiempo_minutos($diff).")";
				} else {
					$nuevoarray[$keyAnterior]['TiempoEstadia'] = "";
				}
			} else {
				$orig = (int) $nuevoarray[$k]['TiempoEstadia'];
				if ($orig > 0) {
					$nuevoarray[$k]['TiempoEstadia'] = "{$orig}m (".tiempo_minutos($orig).")";
				}
			}
		} else {
			$orig = intval($nuevoarray[$k]['TiempoEstadia']);
			if ($orig > 0) {
				$nuevoarray[$k]['TiempoEstadia'] = "{$orig}m (".tiempo_minutos($orig).")";
			} else {
				$nuevoarray[$k]['TiempoEstadia'] = "";
			}
		}

		$NombreCorto = $reg['NombreCorto'];
		$patenteAnterior = $reg['Vehiculo'];
		$numeroOrden = $reg['NumeroOrden'];
		$keyAnterior = $k;
	}

	$array = array();
	
	foreach ($nuevoarray as $arr) {
		
		if ($arr['noPoseeIngreso'] == 1 && $arr['noPoseeEgreso'] == 1 && !in_array($arr['IdReferencia'], array(4349, 4485, 4348, 4332))) {
			$arr['NombreCorto'] .= " *";// . $arr['IdReferencia'] ;
		}
		
		//$br = htmlspecialchars("\n"); $arr['nombrecliente']
		$palabras = explode(" ", $arr['nombrecliente']);
		foreach ($palabras as $k => $palabra) {
			$palabras[$k] = ucfirst(strtolower($palabra));
		}
		$arr['nombrecliente'] = implode(" ", $palabras);
		$arr['nombrecliente'] = str_replace("S.a.", "S.A.", $arr['nombrecliente']);
		
		if (isset($arr['Conductor']))
		{
			if ($arr['Conductor'] == $arr['Vehiculo']) {
				$arr['Conductor'] = "";
			} else if ($arr['Conductor'] != "") {
				$arr['Conductor'] = " / " . $arr['Conductor'];
			}
		}
		else
		{
			$arr['Conductor'] = "(sin conductor)";
		}
		
		$aaData = array(
			$arr['NumeroOrden'],
			htmlspecialchars($arr['Vehiculo'] . encode($arr['Conductor']) . "<sep>" . $arr['nombrecliente']),
			(string) htmlentities($arr['NombreCorto']),
			//$arr['FechaIngresoProgramado'],
			$arr['FechaIngreso'],
			$arr['FechaEgreso'],
			//$arr['FechaEgresoProgramado'],
			(string) $arr['TiempoEstadia'],
			(string) $arr['noPoseeIngreso'],
			(string) $arr['noPoseeEgreso'],
			$arr['IdReferencia'],
			$arr['nombrecliente'],
			encode($arr['Vehiculo'] . $arr['Conductor'])
		);

		$array[] = $aaData;
	}
	// Codigo
	// Vehiculo
	// Nombre Corto
	// Ingreso Programado
	// Ingreso
	// Egreso
	// EgresoProgramado
	// Estadia

    return $array;
}

function export_xls($objSQLServer, $seccion){
	global $lang;
	
	$_GET['inicio'] = isset($_POST['fechaDesde'])?$_POST['fechaDesde']:'';
    $_GET['fin'] = isset($_POST['fechaHasta'])?$_POST['fechaHasta']:'';
	if(empty($_GET['inicio'])){
		unset($_GET['inicio']);	
	}
	if(empty($_GET['fin'])){
		unset($_GET['fin']);	
	}
	$regis = busqueda($objSQLServer, $seccion, true);
	asort($regis);
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle('Historico')
		->setSubject('Historico')
		->setDescription('Historico')
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
		
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'Código')
		->setCellValue('B1', $lang->system->matricula.'/'.$lang->system->conductor)
		->setCellValue('C1', $lang->system->transportista)
		->setCellValue('D1', 'Lugar')
		->setCellValue('E1', 'Ingreso Real')
		->setCellValue('F1', 'Egreso Real')
		->setCellValue('G1', 'Estadía');
						
	$arralCol = array('A','B','C','D','E','F','G');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','E','F','G');
	$objPHPExcel->alignCenter($alingCenterCol);
						
	$i = 2;
	foreach($regis as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row[0]))
			->setCellValue('B'.$i, encode($row[10]))
			->setCellValue('C'.$i, encode($row[9]))
			->setCellValue('D'.$i, encode($row[2]))							
			->setCellValue('E'.$i, $row[3])
			->setCellValue('F'.$i, $row[4])							
			->setCellValue('G'.$i, $row[5]);
		$i++;
	}
					
	$objPHPExcel->getActiveSheet()->setTitle(' Historico');
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="historico-'.date('d').date('m').date('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}