<?php
require_once 'clases/clsAbms.php';
class Cliente extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_clientes','cl');
		$this->allData = false;
	}

	function eliminarCliente($id){
		if($id){
			//-- Validar si el cliente no tiene usuarios con moviles activos --//
			$sql = " SELECT COUNT(us_id) as cant FROM tbl_usuarios WITH(NOLOCK) ";
			$sql.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_us_id = us_id ";
			$sql.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
			$sql.= " WHERE us_cl_id IN ($id) AND mo_borrado = 0 ";
			$objRes = $this->objSQL->dbQuery($sql);
			$resRow = $this->objSQL->dbGetRow($objRes, 0,3);
			//--
			
			//-- Validar si el cliente no tiene moviles activos --//
			$sql = " SELECT COUNT(*) as cant FROM tbl_moviles WITH(NOLOCK) WHERE mo_id_cliente_facturar = ".(int)$id;
			$objRes = $this->objSQL->dbQuery($sql);
			$resRow2 = $this->objSQL->dbGetRow($objRes, 0,3);
			//--
			
			if($resRow['cant'] == 0 && $resRow2['cant'] == 0){
				if($this->eliminarRegistro($id)){
					return true;
				}
			}
			else{
				return 'error_tiene_moviles';	
			}
		}
		return false;
	}

	function eliminarClienteAllInOne($id){
		if($id){
			//-- Baja del Cliente --//
			if($this->eliminarRegistro($id)){
			
				//-- Baja de Moviles --//
				$sql = " SELECT um_mo_id FROM tbl_usuarios WITH(NOLOCK) ";
				$sql.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_us_id = us_id ";
				$sql.= " WHERE us_cl_id = 181 ";
				$objRes = $this->objSQL->dbQuery($sql);
				$resRow = $this->objSQL->dbGetAllRows($objRes, 3);
				foreach($resRow as $mo){
					$this->eliminarRegistro($mo['um_mo_id'],'tbl_moviles','mo');
				}
							
				//-- Baja de Usuarios --//
				$sql = " SELECT us_id FROM tbl_usuarios WITH(NOLOCK) ";
				$sql.= " WHERE us_cl_id IN ($id) AND us_borrado = 0";
				$objRes = $this->objSQL->dbQuery($sql);
				$resRow = $this->objSQL->dbGetAllRows($objRes, 3);
				foreach($resRow as $us){
					$this->eliminarRegistro($us['us_id'],'tbl_usuarios','us');
				}
				
				return true;
			}
		}
		return false;
	}
	
	function habilitarClienteAllInOne($id){
		if($id){
			//-- Habilitar Cliente --//
			$paramsCl['cl_borrado'] = 0;
			if($this->objSQL->dbQueryUpdate($paramsCl, 'tbl_clientes', 'cl_id = '.(int)$id)){
				//-- Activar Usuarios --//
				$sql = " SELECT us_id FROM tbl_usuarios WITH(NOLOCK) ";
				$sql.= " WHERE us_cl_id IN ($id) AND us_borrado = 1";
				$objRes = $this->objSQL->dbQuery($sql);
				$resRow = $this->objSQL->dbGetAllRows($objRes, 3);
				foreach($resRow as $us){
					$paramsUs['us_borrado'] = 0;
					$this->objSQL->dbQueryUpdate($paramsUs, 'tbl_usuarios', 'us_id = '.(int)$us['us_id']);
				}
				 
				return true;
			}
		}
		return false;
	}



	function obtenerClientes($id=0, $filtro="", $idDistribuidor=0, $perfilAllInOne = false) {
		
		$selectTop = ' TOP 30 ';
		if($filtro == 'getAllReg'){
			$selectTop = $filtro = '';
		}
		elseif(!empty($filtro)){
			$selectTop = '';
		}
		
		$strSQL="SELECT ".$selectTop."
		clientes.cl_id_fletero,clientes.cl_id, clientes.cl_razonSocial, clientes.cl_cuit, clientes.cl_telefono, 
		clientes.cl_fax, clientes.cl_email, clientes.cl_direccion, clientes.cl_direccion_nro, clientes.cl_direccion_piso, clientes.cl_direccion_dpto,
		clientes.cl_habilitado, clientes.cl_id_distribuidor,clientes.cl_abbr, clientes.cl_tipo_cliente
		,clientes.cl_pai_id, tbl_pais.pa_nombre, clientes.cl_localidad,	clientes.cl_pr_id, pr_nombre, clientes.cl_par_id, 
		clientes.cl_lo_id , lo_nombre, clientes.cl_tipo, clientes2.cl_razonSocial distribuidor, 
		clientes.cl_ibrutos, clientes.cl_iva, clientes.cl_cpostal
		,pr_idioma, pr_region,  clientes.cl_idioma_definida, clientes.cl_paquete, clientes.cl_cant_dadores, clientes.cl_urlAutorizada 
		,clientes.cl_borrado
		";
		if($perfilAllInOne){
			$strSQL.=
			" , (select count(us_id) from tbl_usuarios WITH(NOLOCK) where us_cl_id = clientes.cl_id) cantidadUsuarios
			  , (select TOP 1 us_id from tbl_usuarios WITH(NOLOCK) where us_cl_id = clientes.cl_id) us_id
			  , (select TOP 1 us_acceso_fallido from tbl_usuarios WITH(NOLOCK) where us_cl_id = clientes.cl_id) us_acceso_fallido
			  , (select TOP 1 us_cant_fallido from tbl_usuarios WITH(NOLOCK) where us_cl_id = clientes.cl_id) us_cant_fallido
			  , (select TOP 1 us_expira from tbl_usuarios WITH(NOLOCK) where us_cl_id = clientes.cl_id) us_expira
			  , (select TOP 1 us_ultimo_acceso from tbl_usuarios WITH(NOLOCK) where us_cl_id = clientes.cl_id) us_ultimo_acceso
			  , NULL zonasPanico
			  , us_cant_licencias ";			
		}	
	$strSQL.="	
	FROM tbl_clientes clientes WITH(NOLOCK)
	INNER JOIN tbl_clientes clientes2 WITH(NOLOCK) ON (clientes.cl_id_distribuidor = clientes2.cl_id) 
	left join tbl_pais WITH(NOLOCK) ON (clientes.cl_pai_id = tbl_pais.pa_id)
	left join tbl_provincias WITH(NOLOCK) ON (clientes.cl_pr_id = pr_id)
	left join tbl_localidad WITH(NOLOCK) ON (clientes.cl_lo_id = lo_id) ";
	
	if($perfilAllInOne){
		$strSQL.="	LEFT JOIN tbl_usuarios WITH(NOLOCK) ON us_cl_id = clientes.cl_id	";
	}
	
	$strSQL.="	WHERE clientes.cl_borrado ".(($perfilAllInOne || $this->allData)?' IN (1,0) ':'= 0 ');
	
	if($id>0){
		$strSQL .= "AND clientes.cl_id =".$id;
	}
	
	if($filtro!=""){
		$strSQL .= "AND (clientes.cl_razonSocial like '%".$filtro."%' OR clientes.cl_email like '%".$filtro."%' )";
	}
	
	if($idDistribuidor>0){
		$strSQL .= " AND clientes.cl_id_distribuidor =".$idDistribuidor;
	}
	
	$strSQL .= " ORDER BY ".($this->allData?'cl_borrado ASC,':'')."clientes.cl_razonSocial ";
	
	$objClientes = $this->objSQL->dbQuery($strSQL);
	$arrClientes = $this->objSQL->dbGetAllRows($objClientes, 3);
	
	if($arrClientes){
		//------------------------- obtengo zonas de panico y cant moviles
		if($perfilAllInOne){	
			$intRows = $this->objSQL->dbNumRows($objClientes);	
			for($i=0;$i<$intRows;$i++){			
				if($arrClientes[$i]['us_id']){
					$sql2 = " SELECT re_id, re_ubicacion as re_nombre, re_panico ";
					$sql2.= " FROM tbl_referencias WITH(NOLOCK) ";
					$sql2.= " WHERE re_borrado = 0 AND re_us_id = ".$arrClientes[$i]['us_id']." AND re_panico > 0 ";
					$sql2.= " ORDER BY re_panico ";
					
					$objClientes = $this->objSQL->dbQuery($sql2);
					$arrZonas = $this->objSQL->dbGetAllRows($objClientes, 3);
					if($arrZonas){
						$arrClientes[$i]['zonasPanico'] = array();
						for($j=0;$j<count($arrZonas);$j++){
							if(isset($arrZonas[$j]['re_id'])){
								$array = array('id_panico'=>$arrZonas[$j]['re_id'],'desc_panico'=>$arrZonas[$j]['re_nombre'],'nro_panico'=>$arrZonas[$j]['re_panico']);
								array_push($arrClientes[$i]['zonasPanico'],$array);
							}
						}								
					}
						
					$sql3 = " SELECT COUNT(um_id) ";
					$sql3.= " FROM tbl_usuarios_moviles WITH(NOLOCK) ";
					$sql3.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = um_mo_id ";
					$sql3.= " WHERE um_us_id = ".(int)$arrClientes[$i]['us_id']." AND mo_borrado = 0 ";
					$objClientes = $this->objSQL->dbQuery($sql3);
					$arrCanMo = $this->objSQL->dbGetAllRows($objClientes, 2);
					$arrClientes[$i]['cantidadMoviles'] = (int)$arrCanMo[0][0];
					}			
				}
			}
			//---------------------------------------------			
			
			return $arrClientes;
		}
		return false;
	}
	
	function obtenerUrlAutorizada($id_cliente){
		$sql = " SELECT CASE WHEN (cl2.cl_urlAutorizada IS NULL OR cl2.cl_urlAutorizada = '') THEN cl1.cl_urlAutorizada ELSE cl2.cl_urlAutorizada END cl_urlAutorizada ";
		$sql.= " FROM tbl_clientes cl1 WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_clientes cl2 WITH(NOLOCK) ON cl1.cl_id_distribuidor = cl2.cl_id ";
		$sql.= " WHERE cl1.cl_id = ".(int)$id_cliente;
		$objClientes = $this->objSQL->dbQuery($sql);	  
		$arrURL = $this->objSQL->dbGetRow($objClientes, 0, 3);
		if(!empty($arrURL['cl_urlAutorizada'])){
			return trim($arrURL['cl_urlAutorizada']);	
		}
		return false;
	}
		
	function obtenerClientesFletes($id){
	  $strSQL ='SELECT * FROM tbl_clientes WITH(NOLOCK) WHERE cl_tipo_cliente IN(2) AND cl_id_distribuidor='.(int)$id." AND cl_borrado = 0 ORDER BY cl_razonSocial ";
	  $objClientesFlete = $this->objSQL->dbQuery($strSQL);
	  $arrClientesFlete = $this->objSQL->dbGetAllRows($objClientesFlete);
	  return $arrClientesFlete;              	
	}
	
	function obtenerAgentes(){
		$strSQL = "SELECT cl_id_distribuidor FROM tbl_clientes WITH(NOLOCK) WHERE cl_tipo in(2) AND cl_id=".$_SESSION['idEmpresa']; //Uso in porque seguramente en un futuor pondré tipo 3.
		$objAgente = $this->objSQL->dbQuery($strSQL);
	  	if($objRowAgente = $this->objSQL->dbGetRow($objAgente)){
	  		return $objRowAgente;
			              	
		}
		else{
		    return false;	
		}
	}
	
	function obtenerAgentes2(){
		$strSQL = " SELECT cl_id, cl_razonSocial, cl_abbr
					FROM tbl_clientes WITH(NOLOCK) 
					WHERE cl_tipo = 1 AND cl_borrado = 0 AND cl_habilitado = 1
					ORDER BY cl_razonSocial ";
		$objAgente = $this->objSQL->dbQuery($strSQL);
	  	if($objRowAgente = $this->objSQL->dbGetAllRows($objAgente)){
	  		return $objRowAgente;
		}
	}
	
	function validarIdiomaCliente($id_provincia,$idioma){
		$sql = " SELECT * FROM tbl_provincias WITH(NOLOCK) ";
		$sql.= " WHERE pr_id = ".(int)$id_provincia;
		$objRes = $this->objSQL->dbQuery($sql);
		$resRow = $this->objSQL->dbGetRow($objRes, 0,3);
		
		if(trim($resRow['pr_idioma']).'_'.trim($resRow['pr_region']) == trim($idioma)){
			return true;
		}
		return false;
	}
	
	function getCantDadores(){
		$sql = " SELECT COUNT(cl_id) as cant FROM tbl_clientes WITH(NOLOCK) ";
		$sql.= " WHERE cl_id_distribuidor  = (SELECT us_cl_id from tbl_usuarios where us_id = ".(int)$_SESSION['idUsuario'].") ";
		$sql.= " AND cl_tipo_cliente = 1 AND cl_borrado = 0 ";
		$objRes = $this->objSQL->dbQuery($sql);
		$resRow = $this->objSQL->dbGetRow($objRes, 0,3);
		return $resRow['cant'];
	}
	
	function obtenerCredenciales($idCliente){
		$strSQL = " SELECT cl_id, cl_clientID, cl_clientSecret FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = ".(int)$idCliente;
		$objRes = $this->objSQL->dbQuery($strSQL);
		$resRow = $this->objSQL->dbGetRow($objRes, 0,3);
		return $resRow;
	}
	
	function insertarRelAgentesEventos($idCliente, $arrEventos){
		$return = false;
		if($arrEventos && $idCliente){
			$return = true;
			foreach($arrEventos as $id){
				$campos = array('dra_dr_id','dra_cl_id');
				$valorCampos = array((int)$id,(int)$idCliente);
				if(!$this->insertarRegistro($campos, $valorCampos, NULL, 'tbl_definicion_reportes_agentes')){
					$return = false;	
				}	
			}	
		}
		return $return;
	}
	
	function modificarRelAgentesEventos($idCliente, $arrEventos){
		$return = false;
		if($idCliente){
			$strSQL = " DELETE FROM tbl_definicion_reportes_agentes WHERE dra_cl_id = ".(int)$idCliente;
			if($this->objSQL->dbQuery($strSQL)){
				$return = $this->insertarRelAgentesEventos($idCliente, $arrEventos);
			}
		}
		return $return;
	}
}