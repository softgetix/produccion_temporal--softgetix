<?php
$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

function index($objSQLServer, $seccion, $mensaje = "") {
	require_once 'clases/clsReferencias.php';
    require_once 'clases/clsUsuarios.php';
    $action = isset($_GET['action']) ? $_GET['action'] : 'listar';
    
	if ($action == 'popup'){
        alta($objSQLServer, 'abmReferencias', $mensaje, true);
		exit;
    }
	elseif($action == 'popupMod'){
		modificar($objSQLServer, 'abmReferencias', $mensaje, $_GET['refer'], true);
	}
	elseif($action == 'popupModWpInteligente'){
		modificarWpInteligente($objSQLServer, 'abmReferencias', $mensaje, $_GET['id_referencia'], true);
	}
	elseif($action == 'importarExcel'){
		importarReferencias($objSQLServer, $seccion, $mensaje, $_GET['content_file']);	
	}
	elseif($action == 'stock'){
		listarStock($objSQLServer, $seccion, $_GET['idRef']);	
	}
	else{
		$operacion = 'listar';

		$tipoBotonera = 'LI';
		$objReferencia = new Referencia($objSQLServer);
		$objUsuario = new Usuario($objSQLServer);
		
		$filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"] : '';
		$method = (isset($_GET['method'])) ? $_GET['method'] : null;
		if ($method == 'export_ptr') {
			$filtro = (isset($_GET['filtro'])) ? $_GET['filtro'] : "";
		}
	
		$txtFiltro = $filtro;
		if($_GET['viewAll']){
			$txtFiltro = 'getAllReg';
			$filtro = '';
		}
		
		$arrEntidades = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION["idEmpresa"], $txtFiltro);
		$cantRegistros = count($arrEntidades);
	
        $extraCSS = array('css/demo_page.css', 'css/demo_table_jui.css', 'css/TableTools.css', 'css/smoothness/jquery-ui-1.8.4.custom.css');
        $extraCSS[] = 'css/estilosPopup.css';
        $extraJS[] = 'js/popupHostFunciones.js';
        $extraJS[] = 'js/jquery/jquery-ui-1.8.14.autocomplete.min.js';
        $extraJS[] = 'js/jquery/combobox.js';
		$extraJS[] = 'js/jquery.blockUI.js';
		require("includes/template.php");
    }
}

function alta($objSQLServer, $seccion, $mensaje = "", $popup = false) {
    global $lang;
	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
    
	require_once 'clases/clsReferencias.php';
    $objReferencia = new Referencia($objSQLServer);
	
	$arrGrupos = $objReferencia->getReferenciasGrupos();
	
	if(tienePerfil(17)){
		$grupoZonaPanico = $objReferencia->obtenerGrupoPanico();
	}	
	
	if(tienePerfil(19)){
		$arrTipoRef = $objReferencia->getTipoReferencias();
		$arrTipoCamino = $objReferencia->getTipoCamino();
	}
	
	if(tienePerfil(array(5,8,9,12,19))){
		$arrProvincias = $objReferencia->getProvincia($_SESSION['idPais']);
	}

	
	$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$extraJS[] = 'js/boxes.js';
	$extraJS[] = 'js/openLayers/OpenLayers.js';
	$extraJS[] = 'js/defaultMap.js';
	$operacion = 'alta';
    if ($popup) {
		$extraCSS[] = 'css/estilosAbmPopup.css';
        $extraCSS[] = 'css/popup.css';
        $extraJS[] = 'js/popupFunciones.js?1';
		$extraJS[] = 'js/jquery.blockUI.js';
		if (isset($_GET['ref'])) {
            $tipoBotonera = 'A';
        } else {
            $tipoBotonera = 'AM';
        }
        require("includes/frametemplate.php");
    } else {
        $tipoBotonera = 'AM';
        require("includes/template.php");
    }
}

function modificar($objSQLServer, $seccion = "", $mensaje = "", $idReferencia = 0, $popup = false) {
   	global $lang;
	$id = $_POST['hidId']?$_POST['hidId']:(($idReferencia) ? $idReferencia : 0);
	
	require_once 'clases/clsReferencias.php';
    $objReferencia = new Referencia($objSQLServer);
    
	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	if($popup==false){	
		$mPermitido = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION['idEmpresa'], $filtro =  NULL,$id);
	}	
	else{
		$arr_datos['idEmpresa'] = $_SESSION['idEmpresa'];
		$arr_datos['cl_id'] = $_GET['cli'];
		$arr_datos['us_id'] = $_GET['usr'];
		$arr_datos['re_id'] = $_GET['refer'];
		$mPermitido = $objReferencia->validarReferencia($arr_datos);
	}
	if($id==0){$mPermitido=0;}
	validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////

	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
	$arrGrupos = $objReferencia->getReferenciasGrupos();
	
	if(tienePerfil(17)){
		$grupoZonaPanico = $objReferencia->obtenerGrupoPanico();
	}
	
	if(tienePerfil(19)){
		$arrTipoRef = $objReferencia->getTipoReferencias();
		$arrTipoCamino = $objReferencia->getTipoCamino();
	}
	
	if(tienePerfil(array(5,8,9,12,19))){
		$arrProvincias = $objReferencia->getProvincia($_SESSION['idPais']);
	}
	
	
    $arrEntidades = $objReferencia->obtenerReferencias($id);
	$arrPuntos = $objReferencia->obtenerCoordenadas($id);
	comun();

	if($arrEntidades[0]['re_recoleccion_re_id']){
		$txtRecoleccion = $objReferencia->obtenerReferencias($arrEntidades[0]['re_recoleccion_re_id']);
		$txtRecoleccion = $txtRecoleccion[0]['re_nombre'];
	}

	$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$extraJS[] = 'js/boxes.js';
	$extraJS[] = 'js/openLayers/OpenLayers.js';
	$extraJS[] = 'js/defaultMap.js';
  	$operacion = 'modificar';
    $tipoBotonera = 'AM';
    if (!$popup) {
        require("includes/template.php");
    } else {
		$extraCSS[] = 'css/estilosAbmPopup.css';
        $extraCSS[] = 'css/popup.css';
        $extraJS[] = 'js/popupFunciones.js?1';
		$extraJS[] = 'js/jquery.blockUI.js';
        $popup = true;
        require("includes/frametemplate.php");
    }
}

function modificarWpInteligente($objSQLServer, $seccion = "", $mensaje = "", $idReferencia = 0, $popup = false){
	global $lang;
    $id = (isset($_POST["chkId"])) ? $_POST["chkId"][0] : (($idReferencia) ? $idReferencia : 0);
	
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	$arrGrupos = $objReferencia->getReferenciasGrupos();
	$arrEntidades = $objReferencia->obtenerReferencias($id);
    $arrPuntos = $objReferencia->obtenerCoordenadas($id);
   	$arrPuntos = $arrPuntos[0];
	$strPuntos = '('.$arrPuntos['rc_latitud'].', '.$arrPuntos['rc_longitud'].');';
    comun();
	
	$extraCSS[] = 'css/estilosAbmPopup.css';
    $extraCSS[] = 'css/popup.css';
    $extraJS[] = 'js/openLayers/OpenLayers.js';
	$extraJS[] = 'js/defaultMap.js';
	$extraJS[] = 'js/popupFunciones.js?1';
	$extraJS[] = 'js/jquery.blockUI.js';
    $popup = true;
    $operacion = 'editar_wp_inteligente';
    $tipoBotonera = 'AM';
    require("includes/frametemplate.php");
}


function baja($objSQLServer, $seccion) {
	global $lang;
    require_once 'clases/clsReferencias.php';
    $objReferencia = new Referencia($objSQLServer);
   	$id = $_POST['hidId']?$_POST['hidId']:0;
	
    ////////////////////////////////////////////////////Protejo contra inyeccion JS////////////////////////////////////////
	  $mPermitido=$objReferencia->obtenerReferenciasPorEmpresa2($_SESSION['idEmpresa'], $filtro =  NULL,$id);
	  $hablitado=validarModificar($mPermitido,$objSQLServer);
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	if($id){
		$msj = "";
		$error = false;
		$err['viajes'] = array();
		$err['alertas'] = array();
		
			$err_ref = false;
			
			// Verificar si la referecnia a eliminar tiene Viajes asignados 
			$sql = " SELECT DISTINCT(vi_codigo) AS viaje, re_nombre";
			$sql.= " FROM tbl_referencias  ";
			$sql.= " INNER JOIN tbl_viajes_destinos ON vd_re_id = re_id  ";
			$sql.= " INNER JOIN tbl_viajes ON vd_vi_id = vi_id  ";
			$sql.= " WHERE re_id = ".(int)$id." AND vi_borrado = 0 ";
			$objViajes = $objSQLServer->dbQuery($sql);
			$arrObjRow = $objSQLServer->dbGetAllRows($objViajes,3);
			if($arrObjRow){	
				$err_ref = true;
				$coma = "";
				foreach($arrObjRow as $objRow){
					@$err['viajes'][$objRow['re_nombre']].= $coma.$objRow['viaje']; 
					$coma = ", ";
				}
			} 
			
			// Verificar si el movil a eliminar tiene Alertas 
			$sql = " SELECT DISTINCT(al_nombre) as alerta, re_nombre  ";
			$sql.= " FROM tbl_referencias ";
			$sql.= " INNER JOIN tbl_alertas_referencias ON ar_re_id = re_id  ";
			$sql.= " INNER JOIN tbl_alertas ON al_id = ar_al_id ";
			$sql.= " WHERE re_id = ".$id." AND al_borrado = 0 ";
			$objAlertas = $objSQLServer->dbQuery($sql);
			$arrObjRow = $objSQLServer->dbGetAllRows($objAlertas, 3);
			if($arrObjRow){	
				$err_ref = true;
				$coma = "";
				foreach($arrObjRow as $objRow){
					@$err['alertas'][$objRow['re_nombre']].= $coma.$objRow['alerta']; 
					$coma = ", ";
				}
			}
			
			if($err_ref == false){
				$coordGuardadas = $objReferencia->obtenerCoordenadas($id);
				if($objReferencia->eliminarRegistro($id)){
					$objReferencia->generarLog(2,$id,$lang->system->baja_referencia.': '.$coordGuardadas[0]['re_nombre'].(!empty($coordGuardadas[0]['re_numboca'])?' - ID #'.$coordGuardadas[0]['re_numboca']:'')); 							
				}
			}
		
		if(count($err['viajes']) > 0){
			$msj.= "<strong>".$lang->message->msj_baja_referencia_viajes."</strong>";
			foreach($err['viajes'] as $k => $item){
				$msj.= "<br>- ".$lang->system->referencia.": <i>".$k."</i>, ".$lang->system->asociado_a.": <i>".$item."</i>";	
			}
			$error = true;	
		}
		
		if(count($err['alertas']) > 0){
			if($msj != ""){$msj.="<br><br>";}
			$msj.= "<strong>".$lang->message->msj_baja_referencia_alertas."</strong>";
			foreach($err['alertas'] as $k => $item){
				$msj.= "<br>- ".$lang->system->referencia.": <i>".$k."</i>, ".$lang->system->asociado_a.": <i>".$item."</i>";	
			}
			$error = true;	
		}
		
		if ($error == true) {
			if(strlen($msj) > 503){
				$msj = substr($msj,0,500)."..."; 	
			}
		}
		else {
			$msj = $lang->message->ok->msj_baja;
		}	
	}
    index($objSQLServer, $seccion, $msj);
}

function guardarA($objSQLServer, $seccion){
	if($_POST['hidId'] && $_POST['HidPopUp']=="popup"){ guardarM($objSQLServer, $seccion, true); die(); }
   
    global $lang;
	require_once 'clases/clsReferencias.php';
    $objReferencia = new Referencia($objSQLServer);

    require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
	
    //CON ESTE FRAGMENTO SE OBTIENEN LOS CAMPOS QUE SE VAN A INSERTAR JUNTO CON LOS VALORES (DINAMICO)
    $campos = "";
    $valorCampos = "";
    $mensaje = validarCampos($objReferencia, $arrElementos);
	
	if(isset($_POST["txtBoca"]) && $_POST["txtBoca"]!=''){
		if($objReferencia->exiteNumBoca($_POST["txtBoca"])){
			$mensaje.="* ".$lang->message->error->msj_alta->__toString().'('.$lang->system->num_boca->__toString().')<br/> ';
		}
	}
	
	if(empty($mensaje)){
		for ($i = 0; $i < count($arrElementos) && $arrElementos; $i++) {
			$idCampo = $arrElementos[$i]["ig_idCampo"];
			if($arrElementos[$i]["ig_validacionExistencia"]) {
            	$campoValidador = @$_POST[$idCampo];
        	}
			
			$campos.= $arrElementos[$i]["ig_value"] . ",";
			//$valorCampos.= "''" .@$_POST[$idCampo]. "'',";
			$valorCampos.= (isset($_POST[$idCampo])?"''" .@$_POST[$idCampo]. "''":'NULL').',';
		}
	
		$campos.= "re_ubicacion";
		$txtUbicacion = isset($_POST["txtDireccion"]) ? $_POST["txtDireccion"] : "";
		$valorCampos.= "''".$txtUbicacion. "''";
	
		$campos.= ",re_radioIngreso";
		$radioIngreso = isset($_POST["cmbRadioIngreso"]) ? $_POST["cmbRadioIngreso"] : 50;
		$valorCampos.= ",''" . $radioIngreso . "''";
	
		$campos.= ",re_radioEgreso";
		$radioEgreso = isset($_POST["cmbRadioEgreso"]) ? $_POST["cmbRadioEgreso"] : 50;
		$radioEgreso = $radioIngreso;
		$valorCampos.= ",''" . $radioEgreso . "''";
		
		$tipoCamino = isset($_POST["cmbTipoCamino"])?"''".$_POST["cmbTipoCamino"]."''":0;
		if($tipoCamino){
			$campos.= ",re_tc_id";
			$valorCampos.= ",".$tipoCamino;
		}
		
		if(tienePerfil(array(5,8,9,12,19))){
			$campos.= ",re_numboca";
			$nBoca = (isset($_POST["txtBoca"]) && $_POST["txtBoca"]!='')?"''".$_POST["txtBoca"]."''":"null";
			$valorCampos.= ",".$nBoca;
			
			$campos.= ",re_provincia";
			$provincia = isset($_POST["cmbProvincia"])?"''".$_POST["cmbProvincia"]."''":"null";
			$valorCampos.= ",".$provincia;
			
			$campos.= ",re_localidad";
			$localidad = isset($_POST["cmbLocalidad"])?"''".$_POST["cmbLocalidad"]."''":"null";
			$valorCampos.= ",".$localidad;
		}
		
		$campos.= ",re_us_id";
		$valorCampos.= ",".(int)$_POST["hidUsuario"];
		
		if(tienePerfil(17)){
			$campos.= ",re_tipo_ubicacion";
			$tipoUbica = isset($_POST["cmbTipoUbicacion"])?"''".$_POST["cmbTipoUbicacion"]."''":"null";
			$valorCampos.= ",".$tipoUbica;
			
			$campos.= ",re_direccion";
			$direccion = (isset($_POST["txtNombreDireccion"]) && $_POST["txtNombreDireccion"] !='')?"''".$_POST["txtNombreDireccion"]."''":"null";
			$valorCampos.= ",".$direccion;
			
			$campos.= ",re_altura";
			$altura = (isset($_POST["txtAltura"]) && $_POST["txtAltura"]!='')?"''".$_POST["txtAltura"]."''":"null";
			$valorCampos.= ",".$altura;
			
			$campos.= ",re_piso";
			$piso = (isset($_POST["txtPiso"]) && $_POST["txtPiso"]!='')?"''".$_POST["txtPiso"]."''":"null";
			$valorCampos.= ",".$piso;
			
			$campos.= ",re_dpto";
			$dpto = (isset($_POST["txtDpto"]) && $_POST["txtDpto"]!='')?"''".$_POST["txtDpto"]."''":"null";
			$valorCampos.= ",".$dpto;
			
			$campos.= ",re_torre";
			$torre = (isset($_POST["txtTorre"]) && $_POST["txtTorre"]!='')?"''".$_POST["txtTorre"]."''":"null";
			$valorCampos.= ",".$torre;
					
			$campos.= ",re_entre";
			$entre = (isset($_POST["txtEntre"]) && $_POST["txtEntre"]!='')?"''".$_POST["txtEntre"]."''":"null";
			$valorCampos.= ",".$entre;
			
			//$campos.= ",re_y_entre";
			//$yentre = (isset($_POST["txtYEntre"]) && $_POST["txtYEntre"]!='')?"'".$_POST["txtYEntre"]."'":"null";
			//$valorCampos.= ",".$yentre;
			
			$campos.= ",re_localidad";
			$localidad = (isset($_POST["txtLocalidad"]) && $_POST["txtLocalidad"]!='' )?"''".$_POST["txtLocalidad"]."''":"null";
			$valorCampos.= ",".$localidad;
			
			$campos.= ",re_provincia";
			$provincia = isset($_POST["cmbProvincia"])?"''".$_POST["cmbProvincia"]."''":"null";
			$valorCampos.= ",".$provincia;
			
			$campos.= ",re_pais";
			$pais = isset($_POST["cmbPais"])?"''".$_POST["cmbPais"]."''":"null";
			$valorCampos.= ",".$pais;
							
			$campos.= ",re_panico";
			$panico = isset($_POST["hidPanico"])?"''".$_POST["hidPanico"]."''":"null";
			$valorCampos.= ",".$panico;
					
			$campos.= ",re_lt";
			$ltcode = (isset($_POST["txtLt"]) && $_POST["txtLt"]!='' )?"''".$_POST["txtLt"]."''":"null";
			$valorCampos.= ",".$ltcode;
			
			$campos.= ",re_cp";
			$codpos = (isset($_POST["txtCp"]) && $_POST["txtCp"]!='' )?"''".$_POST["txtCp"]."''":"null";
			$valorCampos.= ",".$codpos;
			
			$campos.= ",re_partido";
			$partido = (isset($_POST["txtPartido"]) && $_POST["txtPartido"]!='' )?"''".$_POST["txtPartido"]."''":"null";
			$valorCampos.= ",".$partido;
		}
		
		if(tienePerfil(array(9,10,11,12))){
			$campos.= ",re_email";
			$valorCampos.= ",".((isset($_POST["txtEmail"]) && $_POST["txtEmail"]!='')?"''".$_POST["txtEmail"]."''":"null");
		}

		if(tienePerfil(9)){
			$campos.= ",re_recoleccion_re_id";
			$valorCampos.= ",".((isset($_POST["idrecolecta"]) && $_POST["idrecolecta"]!='')?"''".$_POST["idrecolecta"]."''":"null");
		}
	}

		if(!$mensaje){
		$strPuntos = "";    
		if($id = $objReferencia->insertarRegistro($campos, $valorCampos)){//$campoValidador
            if(isset($_POST["hidPuntos"])){
                if($_POST["hidPuntos"] != ""){
					$_POST["hidPuntos"] = str_replace('(','',str_replace(')','',$_POST["hidPuntos"]));
					$arrPuntos = explode(";", $_POST["hidPuntos"]);
					
                    for ($i = 0; $i < count($arrPuntos) && $arrPuntos; $i++) {
                        $arrAux = explode(", ", $arrPuntos[$i]);
                        if ($arrAux[0]){
                            $strLat = trim($arrAux[0]);
                        	$strLng = trim($arrAux[1]);
                            $campos = "rc_latitud";
                            $campos .= ",rc_longitud";
                            $campos .= ",rc_re_id";
                            $valorCampos = "''" . $strLat . "''";
                            $valorCampos .= ",''" . $strLng . "''";
                            $valorCampos .= ",''" . $id . "''";
							$objReferencia->insertarCoordenadas($campos, $valorCampos);
                        }
                    }
					
					//-- Log System --//
					$arrPuntos = array_filter($arrPuntos);
					$objReferencia->generarLog(2,$id,$lang->system->alta_referencia.': '.$_POST['txtNombre'].(!empty($_POST['txtBoca'])?' - ID #'.$_POST['txtBoca']:'').' ['.implode(';',$arrPuntos).']'); 
					//-- --//
				}
            }
			
            $mensaje = $lang->message->ok->msj_alta->__toString();
            if($_POST['HidPopUp'] == '') {
            	index($objSQLServer, $seccion, $mensaje);
				exit;
            }
			else {
			    $jsonData['tipo'] = 'referencia';
                $jsonData['id'] = $id;
                $jsonData['nombre'] = $_POST["txtNombre"];
                $jsonData['movil'] = $idMovil;
                $jsonData['ok'] = 'ok';
                echo json_encode($jsonData);
				exit;
            }
        }
		else {
           	$mensaje = $lang->message->error->msj_alta->__toString();
		   	$jsonData['error'] = 'ok';
           	$jsonData['mensaje'] = $mensaje;
           	echo json_encode($jsonData);
			exit;
		}
	}
    
	if($mensaje){
		if (empty($_POST['HidPopUp'])) {
            alta($objSQLServer, $seccion, $mensaje);
        }
		else{
			$jsonData['error'] = 'ok';
            $jsonData['mensaje'] = $mensaje;
            echo json_encode($jsonData);
        }
    }
}

function guardarM($objSQLServer, $seccion) {
	global $lang;
   
	//GUARDA LAS MODIFICACIONES
    $idReferencia = (isset($_POST["hidId"])) ? $_POST["hidId"] : "";
	require_once 'clases/clsReferencias.php';
    $objReferencia = new Referencia($objSQLServer);
		
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
	
    $set = "";
	
	$mensaje = validarCampos($objReferencia, $arrElementos, $idReferencia);
    if(empty($mensaje)){
		for ($i = 0; $i < count($arrElementos) && $arrElementos; $i++) {
			$idCampo = $arrElementos[$i]["ig_idCampo"];
			if($arrElementos[$i]["ig_validacionExistencia"]) {
            	$campoValidador = @$_POST[$idCampo];
        	}
		
			$set.= $arrElementos[$i]["ig_value"] . "=" . "''" .$_POST[$idCampo]. "'',";
		}
		
		$radioIngreso = isset($_POST["cmbRadioIngreso"]) ? $_POST["cmbRadioIngreso"] : "50";
		$set.= "re_radioIngreso" . " = ''" . $radioIngreso . "''";
		
		$txtUbicacion = isset($_POST["txtDireccion"]) ? $_POST["txtDireccion"] : "";
		$set.= ",re_ubicacion"." = ''".$txtUbicacion."''";
	
		$radioEgreso = isset($_POST["cmbRadioEgreso"]) ? $_POST["cmbRadioEgreso"] : "50";
		$radioEgreso = $radioIngreso;
		$set.= ",re_radioEgreso" . " = ''" . $radioEgreso . "''";
		
		$tipoCamino = (int)$_POST["cmbTipoCamino"];
		if($tipoCamino){
			$set.= ",re_tc_id = ".$tipoCamino;
		}
				
		if(tienePerfil(array(5,8,9,12,19))){
			$idBoca = (isset($_POST["txtBoca"]) && $_POST["txtBoca"]!='')?"''".$_POST["txtBoca"]."''":"null";
			$set.= ",re_numboca"." = ".$idBoca;
			
			$provincia = isset($_POST["cmbProvincia"])?"''".$_POST["cmbProvincia"]."''":"null";
			$set.= ",re_provincia = ".$provincia;
			
			$localidad = isset($_POST["cmbLocalidad"])?"''".$_POST["cmbLocalidad"]."''":"null";
			$set.= ",re_localidad = ".$localidad ;
		}
	
		if(tienePerfil(17)){
		
			$tipoUbica = isset($_POST["cmbTipoUbicacion"])?"''".$_POST["cmbTipoUbicacion"]."''":"null";
			$set.= ",re_tipo_ubicacion = ".$tipoUbica;
			
			$direccion = (isset($_POST["txtNombreDireccion"]) && $_POST["txtNombreDireccion"] !='')?"''".$_POST["txtNombreDireccion"]."''":"null";
			$set.= ",re_direccion = ".$direccion;
			
			$altura = (isset($_POST["txtAltura"]) && $_POST["txtAltura"]!='')?"''".$_POST["txtAltura"]."''":"null";
			$set.= ",re_altura = ".$altura;
			
			$piso = (isset($_POST["txtPiso"]) && $_POST["txtPiso"]!='')?"''".$_POST["txtPiso"]."''":"null";
			$set.= ",re_piso = ".$piso;
			
			$dpto = (isset($_POST["txtDpto"]) && $_POST["txtDpto"]!='')?"''".$_POST["txtDpto"]."''":"null";
			$set.= ",re_dpto"." = ".$dpto;
			
			$torre = (isset($_POST["txtTorre"]) && $_POST["txtTorre"]!='')?"''".$_POST["txtTorre"]."''":"null";
			$set.= ",re_torre"." = ".$torre;
					
			$entre = (isset($_POST["txtEntre"]) && $_POST["txtEntre"]!='')?"''".$_POST["txtEntre"]."''":"null";
			$set.= ",re_entre"." = ".$entre;
			
			$yentre = (isset($_POST["txtYEntre"]) && $_POST["txtYEntre"]!='')?"''".$_POST["txtYEntre"]."''":"null";
			$set.= ",re_y_entre"." = ".$yentre;
			
			$localidad = (isset($_POST["txtLocalidad"]) && $_POST["txtLocalidad"]!='' )?"''".$_POST["txtLocalidad"]."''":"null";
			$set.= ",re_localidad"." = ".$localidad;
			
			$provincia = isset($_POST["cmbProvincia"])?"''".$_POST["cmbProvincia"]."''":"null";
			$set.= ",re_provincia"." = ".$provincia;
			
			$pais = isset($_POST["cmbPais"])?"''".$_POST["cmbPais"]."''":"null";
			$set.= ",re_pais"." = ".$pais;
			
			//$usuarioD = isset($_POST["hidUsuario"])?"''".$_POST["hidUsuario"]."''":0;
			//$set.= ",re_us_id"." = ".$usuarioD;
			
			$panico = isset($_POST["hidPanico"])?"''".$_POST["hidPanico"]."''":"null";
			$set.= ",re_panico"." = ".$panico;
			
			$ltcode = (isset($_POST["txtLt"]) && $_POST["txtLt"]!='')?"''".$_POST["txtLt"]."''":"null";
			$set.= ",re_lt"." = ".$ltcode;
	
			$codpos = (isset($_POST["txtCp"]) && $_POST["txtCp"]!='')?"''".$_POST["txtCp"]."''":"null";
			$set.= ",re_cp"." = ".$codpos;
			
			$partido = (isset($_POST["txtPartido"]) && $_POST["txtPartido"]!='')?"''".$_POST["txtPartido"]."''":"null";
			$set.= ",re_partido"." = ".$partido;	
		}
		
		if(tienePerfil(array(9,10,11,12))){
			$set.= ",re_email = ".((isset($_POST["txtEmail"]) && $_POST["txtEmail"]!='')?"''".$_POST["txtEmail"]."''":"null");
		}

		if(tienePerfil(9)){
			$set.= ",re_recoleccion_re_id = ".(isset($_POST["idrecolecta"])?"''".$_POST["idrecolecta"]."''":"null");
		}
	}
	
	if($_POST['refer-referencia'] == 'edicion-inteligencia'){
		$_POST["hidPuntos"] = str_replace('(','',str_replace(')','',$_POST["hidPuntos"]));
		$arrPuntos = explode(";", $_POST["hidPuntos"]);
		$arrAux = explode(", ", $arrPuntos[0]); 
		if ($arrAux[0]){
			$strLat = trim($arrAux[0]);
			$strLng = trim($arrAux[1]);
			
			if(distancia($_POST['hidLat'], $_POST['hidLng'], $strLat, $strLng)*1000 > 700){ 
				$mensaje.= $lang->message->ref_pointer_limit->__toString().".<br/> ";
			}
		}
		
		$set.= ", re_validar_usuario = 1 ";
	}
	
	if (!$mensaje) {
       ///-------		
		if(tienePerfil(17)){					
			$coordGuardadas = $objReferencia->obtenerCoordenadas($idReferencia);
			$LatGuardada = $coordGuardadas[0]['rc_latitud'];
			$LngGuardada = $coordGuardadas[0]['rc_longitud'];
			if($LatGuardada != $strLat && $LngGuardada != $strLng){
				$objReferencia->eliminarHistoricosZonaPanico($idReferencia);
			}	
		}
		///-------		
		
		$coordGuardadas = $objReferencia->obtenerCoordenadas($idReferencia);
		$cod = $objReferencia->modificarRegistro($set,$idReferencia);
		$objReferencia->eliminarCoordenadas($idReferencia);
        if (isset($_POST["hidPuntos"])) {
            if ($_POST["hidPuntos"] != "") {
				$_POST["hidPuntos"] = str_replace('(','',str_replace(')','',$_POST["hidPuntos"]));
                $arrPuntos = explode(";", $_POST["hidPuntos"]);
				
                for ($i = 0; $i < count($arrPuntos) && $arrPuntos; $i++) {
                    $arrAux = explode(", ", $arrPuntos[$i]);
                    if ($arrAux[0]) {
                        $strLat = trim($arrAux[0]);
                        $strLng = trim($arrAux[1]);
						
						$campos = "rc_latitud";
                        $campos .= ",rc_longitud";
                        $campos .= ",rc_re_id";
                        $valorCampos = "''" . $strLat . "''";
                        $valorCampos .= ",''" . $strLng . "''";
                        $valorCampos .= ",''" . $idReferencia . "''";
                        $objReferencia->insertarCoordenadas($campos, $valorCampos);
                    }
                }
            }
        }
		
		//-- Log System --//
		$strCoord = array();
		if($coordGuardadas){
			foreach($coordGuardadas as $kCoord => $coord){
				array_push($strCoord,$coord['rc_latitud'].', '.$coord['rc_longitud']);		
			}
		}
		
		$arrPuntos = array_filter($arrPuntos);
		$auxNumBoca = ($coordGuardadas[0]['re_numboca'] != $_POST['txtBoca'])?$_POST['txtBoca']:NULL;
		$auxLatLon = (implode(';',$strCoord) != implode(';',$arrPuntos))?implode(';',$arrPuntos):NULL;
		if(!empty($auxNumBoca) || !empty($auxLatLon)){
			$objReferencia->generarLog(2,$idReferencia,str_replace('[DATOS_EDITADOS]',$_POST['txtNombre'].(!empty($auxNumBoca)?' - ID #'.$auxNumBoca:'').(!empty($auxLatLon)?' ['.implode(';',$arrPuntos).']':''),str_replace('[DATOS_ACTUALES]',$coordGuardadas[0]['re_nombre'].(!empty($auxNumBoca)?' - ID #'.$coordGuardadas[0]['re_numboca']:'').(!empty($auxLatLon)?' ['.implode(';',$strCoord).']':''),$lang->system->edicion_referencia))); 						
		}
		//-- --//
		
		switch ($cod) {
			case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe->__toString();
			break;
			case 1:
				$mensaje = $lang->message->ok->msj_alta->__toString();
				if($_POST['HidPopUp'] == ''){
					index($objSQLServer, $seccion, $mensaje);
					exit;
				}
				else{
					$jsonData['tipo'] = 'referencia';
					$jsonData['id'] = $idReferencia;
					$jsonData['nombre'] = $_POST["txtNombre"];
					$jsonData['movil'] = $idMovil;
					$jsonData['ok'] = 'ok';
					echo json_encode($jsonData);
					exit;
			   }
			break;
			case 2:
				$mensaje = $lang->message->error->msj_modificar->__toString();
			break;
		}
    }
	
	if($mensaje){
		if($_POST['HidPopUp'] == ''){
            modificar($objSQLServer, $seccion, $mensaje, $idReferencia);
			exit;
        }
		else{
			$jsonData['error'] = 'ok';
            $jsonData['mensaje'] = $mensaje;
            echo json_encode($jsonData);
			exit;
        }
    }
}

function validarCampos($objReferencia, $arrElementos,$idReferencia = NULL){
	global $lang;
	$mensaje = '';
	
	for ($i = 0; $i < count($arrElementos) && $arrElementos; $i++) {
		$msjError = "";
	 	$msjError = checkAll($arrElementos[$i], @$_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	if(tienePerfil(17)){
		$msjError = checkString(trim(@$_POST["txtLt"]), 4, 12, "LT", 1); 
		if(trim(@$_POST["txtLt"])!=""){ if(!preg_match('/^[\w|\\\\|\/|\-]+$/', trim(@$_POST["txtLt"]))){ $msjError = $lang->system->msg_ref_lt_formato->__toString(); } }
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkString(trim(@$_POST["txtNombreDireccion"]), 1, 255, $lang->system->direccion, 1);
        //echo $msjError; exit;
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkString(trim(@$_POST["txtAltura"]), 1, 255, $lang->system->altura, 0);                            
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkString(trim(@$_POST["txtPiso"]), 1, 255, $lang->system->piso, 0);                            
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkString(trim(@$_POST["txtDpto"]), 1, 255, $lang->system->dpto, 0);                            
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkString(trim(@$_POST["txtTorre"]), 1, 255, $lang->system->torre, 0);                            
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkString(trim(@$_POST["txtEntre"]), 1, 255, $lang->system->entre, 0);                            
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkString(trim(@$_POST["txtLocalidad"]), 1, 255, $lang->system->localidad, 1);                          
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkNumber(@$_POST["cmbProvincia"], 1, 500, $lang->system->provincia, 1);                            
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkString(trim(@$_POST["txtPartido"]), 1, 255, $lang->system->ciudad, 1);                            
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		$msjError = checkString(trim(@$_POST["txtCp"]), 1, 255, $lang->system->cod_postal, 0);                            
		if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
		
		// Validar que la zona creada no intersecte con las demas				
		$arrayOtrasZonas = $objReferencia->obtenerOtrasZonasSegurasADT($_POST["hidUsuario"],$_POST["hidPanico"]);
		
		if (isset($_POST["hidPuntos"])){
            if ($_POST["hidPuntos"] != ""){
				$_POST["hidPuntos"] = str_replace('(','',str_replace(')','',$_POST["hidPuntos"]));
                $arrPuntos = explode(";", $_POST["hidPuntos"]);
                $arrAux = explode(", ", $arrPuntos[0]);
				if ($arrAux[0]){
					//$strLat = substr($arrAux[0], 1, strlen($arrAux[0]));
					$strLat = trim($arrAux[0]);
					$strLng = substr($arrAux[1], 0, strlen($arrAux[1]) - 1);
					
					for($jj=0;$jj<count($arrayOtrasZonas);$jj++){
						$numero  = $arrayOtrasZonas[$jj]['re_panico'];
						$strLat2 = $arrayOtrasZonas[$jj]['rc_latitud'];
						$strLng2 = $arrayOtrasZonas[$jj]['rc_longitud'];
						
						//paso a metros la distancia, que tiene que ser menor a 2000m para considerar que las zonas intersectan
						if(distancia($strLat, $strLng, $strLat2, $strLng2)*1000 < 2000){ 
							$mensaje.="* ".str_replace('[NUMBER-ZONA]',$numero,$lang->system->msg_ref_interseccion_zonas->__toString())."<br/> ";
						}
					}
				}                
            }
        }
		///-------		
		
		$arrayRefLt = $objReferencia->obtenerReferenciaPorLT(@$_POST["txtLt"],$idReferencia);
		if($arrayRefLt!=NULL && count($arrayRefLt)>0){
			$mensaje.="* ".$lang->system->msg_ref_lt_en_uso->__toString();
		}		
		
		if($strLat==NULL || $strLat==0 || $strLng==NULL || $strLng==0){
			$mensaje.="* ".$lang->system->msg_ref_error_posicion->__toString();
		}
	}
	
	return $mensaje;	
}

function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}

function guardarAsignacion($objSQLServer, $seccion) {
    global $lang;

    $idUsuario = (isset($_POST["hidId"])) ? $_POST["hidId"] : "";
    $idReferencia = $_POST['hidIdReferencia'];
    $arrMoviles = explode(",", $_POST['cmbMovilesAsignadosSerialize']);

    foreach ($arrMoviles as $i => $arrMovil) {
        $arrMoviles[$i] = trim($arrMovil);
    }

    require_once 'clases/clsMoviles.php';
    $objMovil = new Movil($objSQLServer);

    // Si no hay vehiculos, quiza se hayan quitado todos
    if ($objMovil->eliminarAsignacionesMovilesReferencia($idReferencia)) {
        foreach ($arrMoviles as $idMovil) {
            if ($objMovil->insertarAsignacionMovilReferencia($idReferencia, $idMovil)) {
                $mensaje = $lang->message->ok->msj_modificar->__toString();
            } else {
                $mensaje = $lang->message->error->msj_modificar->__toString();
            }
        }
        index($objSQLServer, $seccion, $mensaje);
    } else {
        $mensaje = $lang->message->error->msj_modificar->__toString();
        modificar($objSQLServer, $seccion, $mensaje, $idUsuario);
    }
}

function modificarAsignacion($objSQLServer, $seccion = "", $mensaje = "", $idUsuario = 0) {
    global $lang;
    require_once 'clases/clsMoviles.php';
    $operacion = 'modificarAsignacion';
    $tipoBotonera = 'AM';

    $idReferencia = (isset($_POST["chkId"])) ? $_POST["chkId"][0] : (($idUsuario) ? $idUsuario : 0);
    $idUsuario = $_SESSION['idUsuario'];
    $idPerfil = $_SESSION['idPerfil'];
    unset($_SESSION['rastreo_182_1']);
    $IdEmpresa = ($_SESSION["idTipoEmpresa"] <= 2) ? $_SESSION["idEmpresa"] : 0;

    $objMovil = new Movil($objSQLServer);
    $arrMoviles = $objMovil->obtenerRegistros();

    $arrMovilesReferencia = $objMovil->obtenerAsignacionMovilReferencia($idReferencia);

    if (is_array($arrMovilesReferencia)) {
        foreach ($arrMovilesReferencia as $tmp) {
            foreach ($arrMoviles as $moId => $tmp2) {
                if ($tmp['mo_id'] == $tmp2['mo_id']) {
                    unset($arrMoviles[$moId]);
                }
            }
        }
    }
    require("includes/template.php");
}

function comun() {
    global $extraCSS;
    if (isset($_GET['action']) && $_GET['action'] == 'popup') {
        $extraCSS[] = "css/popup.css";
    }
}

function export_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST["hidFiltro"]))?$_POST["hidFiltro"] : '');
	
	require_once 'clases/clsReferencias.php';
    $objReferencia = new Referencia($objSQLServer);
    
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	if(empty($txtFiltro)){
		$txtFiltro = 'getAllReg';
	}
	$arrEntidades = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION["idEmpresa"], $txtFiltro);
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->$seccion)
		->setSubject($lang->menu->$seccion)
		->setDescription($lang->menu->$seccion)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->nombre)
		->setCellValue('B1',$lang->system->direccion)
		->setCellValue('C1',$lang->system->provincia)
		->setCellValue('D1',$lang->system->localidad)
		->setCellValue('E1','Lat')
		->setCellValue('F1','Lng')
		->setCellValue('G1',$lang->system->grupo);
		
		if(tienePerfil(array(5,8,9,12,19))){
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('H1',$lang->system->num_boca);
		}
	
	$arralCol = array('A','B','C','D','E','F','G','H');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('E','F','G','H');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','B','C','D');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		
		$auxLatLng = explode(',',$row['LatLng']);
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['re_nombre']))
			->setCellValue('B'.$i, encode($row['re_ubicacion']))
			->setCellValue('C'.$i, encode($row['pr_nombre']))
			->setCellValue('D'.$i, encode($row['lo_nombre']))
			->setCellValue('E'.$i, $auxLatLng[0])
			->setCellValue('F'.$i, $auxLatLng[1])
			->setCellValue('G'.$i, encode($row['rg_nombre']));
			
			if(tienePerfil(array(5,8,9,12,19))){
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('H'.$i, $row['re_numboca']);
			}
		$i++;	
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->$seccion);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ','-',$lang->menu->$seccion)).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

function importarReferencias($objSQLServer, $seccion, $mensaje = "", $content_file = NULL){
	if($content_file){
		header("Content-Description: File Transfer"); 
		header("Content-Type: application/force-download"); 
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",true);
		header("Content-Disposition: attachment; filename=\"documento_errores.txt\";" );
		header("Content-Transfer-Encoding: binary");
		$content_file = urldecode(base64_decode($content_file));
		$content_file = str_replace('<br>',"\r\n",$content_file);
		$content_file = str_replace('<br />',"\r\n",$content_file);
		$content_file = str_replace('&lt;br&gt;',"\r\n",$content_file);
		print $content_file;	
		exit;
	}
	
	global $lang; 
	$msg = 'error'; 
	if(isset($_FILES['archivo'])){
		if(empty($_FILES['archivo']['name'])){
			
			$mensaje = $lang->message->interfaz_generica->msj_seleccione_archivo;
		}
		else{
			switch ((int)$_FILES['archivo']['error']) {
				case UPLOAD_ERR_OK:
					$extens = extension_archivo($_FILES['archivo']["name"]);
					
					if ($extens != 'xls' && $extens != 'xlsx') {
						$mensaje = $lang->message->interfaz_generica->msj_extension_xls;
					}
					else{
						require_once 'clases/clsReferencias.php';
    					$objReferencia = new Referencia($objSQLServer);
						$result = $objReferencia->importarReferencias($_FILES['archivo']);
						$mensaje = $result['msg'];
						$msg = ($result['result'] == 'ok')?'ok':$msg;
						if($result['result'] == 'ok'){ $noError = true;}
					}
				break;
				case UPLOAD_ERR_INI_SIZE:	//Superó directiva "upload_max_filesize" del php.ini.
				case UPLOAD_ERR_FORM_SIZE:	//Superó MAX_FILE_SIZE (HTML).
					$mensaje = $lang->message->interfaz_generica->msj_tamanio_max_archivo;
				break;
				case UPLOAD_ERR_PARTIAL:	//Archivo truncado.
				case UPLOAD_ERR_NO_FILE:	//Archivo no subido.
				case UPLOAD_ERR_NO_TMP_DIR:	//Pérdida del tmp_dir.
				case UPLOAD_ERR_CANT_WRITE:	//No se pudo escribir en disco.
					$mensaje= $lang->message->error->upload_archivo;
				break;
				case UPLOAD_ERR_EXTENSION:	//La extensión es incorrecta.
					$mensaje = $lang->message->interfaz_generica->msj_extension_xls;
				break;
			}
		}
	}
	
	$extraCSS[] = 'css/estilosAbmPopup.css';
    $extraCSS[] = 'css/popup.css';
    $extraJS[] = 'js/popupFunciones.js?1';
	$extraJS[] = 'js/jquery.blockUI.js';
	
	$tipoBotonera = 'AM';
	$operacion = 'importarExcel';
	$popup = true;
	require("includes/frametemplate.php");
}

function listarStock($objSQLServer, $seccion, $idRef) {
	global $lang;
	$idRef = intval($idRef);

	$strSQL = "Exec sp_stock_pallets {$idRef},{$_SESSION['idUsuario']}";
	$arrListado = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);
	
	$operacion = 'listadostock';
    $extraCSS[] = 'css/estilosAbmPopup.css';
    $extraCSS[] = 'css/popup.css';
    $extraJS[] = 'js/popupFunciones.js?1';
	$extraJS[] = 'js/jquery.blockUI.js';
	$extraJS[] = 'js/popupHostFunciones.js';

	$operacion = 'listarstock';
    
    require("includes/frametemplate.php");
}
