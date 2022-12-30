<?php
require_once 'clases/clsAbms.php';
class Usuario extends Abm{	

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_usuarios','us');
	}
	
	function obtenerUsuarios($id=0,$filtro="", $nombreUsuario="", $empresa=0){
		
		$selectTop = ' TOP 30 ';
		if($filtro == 'getAllReg'){
			$selectTop = $filtro = '';
		}
		elseif(!empty($filtro)){
			$selectTop = '';
		}
		
		$sql="SELECT ".$selectTop." us_id, us_nombreUsuario,us_pasaporte,us_zona,us_grupo, us_nombre, us_apellido, us_mailContacto, us_mailAlertas, us_telefono, us_expira,us_cant_fallido,us_ultimo_acceso, us_acceso_fallido,	us_celular, us_nextel, us_pe_id, us_cl_id, us_usuarioCreador, us_fechaCreado, pe_nombre, cl_razonSocial,
		us_pass, us_keyId, CONVERT(varchar, us_expira , 111) as us_expira,us_comentarios, cl_tipo, us_accesoMobile, us_ipAutorizada 
		FROM tbl_usuarios WITH(NOLOCK)
		LEFT JOIN tbl_perfiles WITH(NOLOCK) ON (us_pe_id = pe_id)
		LEFT JOIN tbl_clientes WITH(NOLOCK) ON (us_cl_id = cl_id)
		where us_borrado=0";
		
		if($id!=0){
		 $sql.="AND us_id =".$id;	
		}
		
		if($empresa!=0){
		 $sql.="AND us_cl_id =".$empresa;	
		}
		
		if($filtro!=""){
		 $sql.=" AND (
		 		us_nombre like '%".$filtro."%' 
				OR us_apellido like '%".$filtro."%'
				OR us_nombreUsuario like '%".$filtro."%'
			)";	
		}
		
		if($nombreUsuario!=""){
		 $sql.=" AND (us_nombreUsuario = '".$nombreUsuario."')";	
		}
		
		$sql.="order by us_nombre, us_apellido";

		$objUsuarios = $this->objSQL->dbQuery($sql);
		$arrUsuarios = $this->objSQL->dbGetAllRows($objUsuarios);
		return $arrUsuarios;
	}
	
	function login($arrDatos, $validDoubleHash = false) {
		$arrUsuarios = array();
		if (is_array($arrDatos)) {
			$usuario = $arrDatos["usuario"];
			$password = $arrDatos["pass"];
			$sql = " SELECT *
				,CASE c1.cl_tipo WHEN 1 THEN cl_abbr ELSE (SELECT c2.cl_abbr FROM tbl_clientes c2 WHERE c2.cl_id = c1.cl_id_distribuidor) END AS nombreAgente
				,CASE c1.cl_tipo WHEN 1 THEN us_cl_id ELSE c1.cl_id_distribuidor END AS idAgente, us_pass_vence ";
			$sql.= " FROM tbl_usuarios u WITH(NOLOCK) ";
			$sql.= " INNER JOIN tbl_clientes c1 WITH(NOLOCK) ON (us_cl_id = cl_id) ";
			$sql.= " INNER JOIN tbl_perfiles WITH(NOLOCK) ON (us_pe_id = pe_id) ";
			$sql.= " INNER JOIN tbl_provincias WITH(NOLOCK) ON (cl_pr_id = pr_id) ";
			$sql.= " WHERE us_borrado = 0 AND cl_habilitado in (1,2) ";
			$sql.= " AND (us_expira is NULL OR CONVERT(varchar, us_expira, 111) > CONVERT(varchar, GETDATE(), 111)) ";
			$sql.= " AND us_nombreUsuario = '".$usuario."' AND us_pass = '".$password."' ";
			$sql.= " AND (us_acceso_fallido IS NULL 
						OR (us_acceso_fallido > DATEADD(minute, -15, GETDATE()) AND us_cant_fallido < 5)
						OR (us_acceso_fallido < DATEADD(minute, -15, GETDATE()))
					)";
			$objUsuarios = $this->objSQL->dbQuery($sql);
			$arrUsuarios = $this->objSQL->dbGetAllRows($objUsuarios, 3);
			
			if($arrUsuarios){//-- Login OK --//
			
				$sql = " UPDATE tbl_usuarios  ";
				if($arrUsuarios[0]["us_ultimo_acceso"] == NULL){
					$sql.= " SET us_acceso_fallido = NULL, us_cant_fallido = 0 ";
				}
				else{
					$sql.= " SET us_ultimo_acceso = CURRENT_TIMESTAMP, us_acceso_fallido = NULL, us_cant_fallido = 0 ";
				}
				$sql.= " WHERE us_id = ".(int)$arrUsuarios[0]['us_id'];
				$this->objSQL->dbQuery($sql);
				
				//define idioma del usuario
				$arrUsuarios[0]['cl_idioma_definida'] = trim($arrUsuarios[0]['cl_idioma_definida']);
				if(empty($arrUsuarios[0]['cl_idioma_definida'])){
					$sql = " SELECT cl_idioma_definida FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = ".(int)$arrUsuarios[0]['cl_id_distribuidor']; //idioma del Agente
					$objAgente = $this->objSQL->dbQuery($sql);
					$arrAgente = $this->objSQL->dbGetRow($objAgente,0,3);
					$arrAgente['cl_idioma_definida'] = trim($arrAgente['cl_idioma_definida']);
					if(!empty($arrAgente['cl_idioma_definida'])){
						$arrUsuarios[0]['cl_idioma_definida'] = trim($arrAgente['cl_idioma_definida']); 
					}
					else{
						$arrUsuarios[0]['cl_idioma_definida'] = trim($arrUsuarios[0]['pr_idioma']).'_'.trim($arrUsuarios[0]['pr_region']);
					}
				}
				//-- --//
					
				return $arrUsuarios;
			}
                        elseif($validDoubleHash == true){
                           return false; 
                        }
                        else{//-- Login ERROR --//
				##-- Corroboro q el usuario exista --##
				$sql = " SELECT us_id, us_ultimo_acceso, us_acceso_fallido, us_cant_fallido ";
				$sql.= " FROM tbl_usuarios WITH(NOLOCK) WHERE us_nombreUsuario = '".$usuario."' AND us_borrado = 0 ";
				$objUsuarios = $this->objSQL->dbQuery($sql);
				$arrUserError = $this->objSQL->dbGetAllRows($objUsuarios, 3);
				if($arrUserError){
					$cant_fallido = (int)$arrUserError[0]['us_cant_fallido'];
					$dif_fallido = round(((int)(strtotime(date('Y-m-d H:i:s')) - strtotime(date('Y-m-d H:i:s',strtotime($arrUserError[0]['us_acceso_fallido']))))/60));
					
					if($dif_fallido < 15 && $cant_fallido < 5){
						$cant_fallido++;
						$sql = " UPDATE tbl_usuarios ";
						$sql.= " SET us_cant_fallido = ".(int)$cant_fallido.", us_acceso_fallido = CURRENT_TIMESTAMP ";
						$sql.= " WHERE us_id = ".(int)$arrUserError[0]['us_id'];
						$this->objSQL->dbQuery($sql);
					}
					elseif($dif_fallido > 15){
						$cant_fallido = 1;
						$sql = " UPDATE tbl_usuarios ";
						$sql.= " SET us_cant_fallido = ".(int)$cant_fallido.", us_acceso_fallido = CURRENT_TIMESTAMP ";
						$sql.= " WHERE us_id = ".(int)$arrUserError[0]['us_id'];
						$this->objSQL->dbQuery($sql);
					}
				}
			}
		}
		return false;
	}

	function obtenerPassword($idUsuario) {
		$sql = " SELECT * FROM tbl_usuarios u WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON (us_cl_id = cl_id) ";
		$sql.= " INNER JOIN tbl_perfiles WITH(NOLOCK) ON (us_pe_id = pe_id) ";
		$sql.= " INNER JOIN tbl_provincias WITH(NOLOCK) ON (cl_pr_id = pr_id) ";
		$sql.= " WHERE us_id = ".(int)$idUsuario;
		$sql.= " AND us_borrado = 0 AND cl_habilitado in (1,2) ";
		$objUsuarios = $this->objSQL->dbQuery($sql);
		$arrUsuarios = $this->objSQL->dbGetAllRows($objUsuarios, 1);
		
		//define idioma del usuario
		$arrUsuarios[0]['cl_idioma_definida'] = trim($arrUsuarios[0]['cl_idioma_definida']);
		if(empty($arrUsuarios[0]['cl_idioma_definida'])){
			$sql = " SELECT cl_idioma_definida FROM tbl_clientes WITH(NOLOCK) WHERE cl_id = ".(int)$arrUsuarios[0]['cl_id_distribuidor']; //idioma del Agente
			$objAgente = $this->objSQL->dbQuery($sql);
			$arrAgente = $this->objSQL->dbGetRow($objAgente,0,3);
			$arrAgente['cl_idioma_definida'] = trim($arrAgente['cl_idioma_definida']);
			if(!empty($arrAgente['cl_idioma_definida'])){
				$arrUsuarios[0]['cl_idioma_definida'] = trim($arrAgente['cl_idioma_definida']); 
			}
			else{
				$arrUsuarios[0]['cl_idioma_definida'] = trim($arrUsuarios[0]['pr_idioma']).'_'.trim($arrUsuarios[0]['pr_region']);
			}
		}
		//-- --//
		return $arrUsuarios;
	}

	function loginPorId($idUsuario, $Contrasenna) {
		$sql = "SELECT us_nombreUsuario FROM tbl_usuarios WITH(NOLOCK) WHERE us_id = ".(int)$idUsuario." and us_borrado = 0 ";
		$objUsuarios = $this->objSQL->dbQuery($sql);
		$arrUsuarios = $this->objSQL->dbGetRow($objUsuarios,0,3);
		if ($arrUsuarios) {
            //--Ini. Se implementa HASH256 y que conviva con md5 hasta que todos migren a HASH256 mediante el cambio de clave..
            $arrDatos = array ("usuario" => $arrUsuarios["us_nombreUsuario"],"pass" => hash('sha256',trim($Contrasenna)));
            $result = $this->login($arrDatos, true);
            if($result == false){//-- si es false, verificamos si no posee hash256 (borrar cuando se desida sacar por completo md5)
                $arrDatos = array ("usuario" => $arrUsuarios["us_nombreUsuario"],"pass" => md5(trim($Contrasenna)));
                $result = $this->login($arrDatos, false);
            }
            //--Fin.
            if($result[0]['us_id']){
                return true;
            }
		}
		return false;
	}

	function verificarClienteHabilitado($idUsuario) {
		if ($idUsuario){
			$strSQL = " SELECT cl_habilitado FROM tbl_usuarios u WITH(NOLOCK) ";
			$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON (us_cl_id = cl_id) ";
			$strSQL.= " WHERE us_id = ".(int)$idUsuario." AND us_borrado = 0 AND cl_habilitado in (1,2) ";
			$objUsuarios = $this->objSQL->dbQuery($strSQL);
			$fila = $this->objSQL->dbGetRow($objUsuarios, 0);
			if ($fila){
				return $fila["cl_habilitado"];
			}
		}
		return 0;
	}

	function actualizarPassword($idUsuario='', $pass=''){
            $vence_pass = date('Y-m-d', strtotime('+60 day',strtotime(date('Y-m-d'))));
            if ((int)$idUsuario && $pass){
		$sql = " UPDATE tbl_usuarios ";
		$sql.= " SET us_pass = '".$pass."', us_cant_fallido = 0 ,us_acceso_fallido = NULL";
		$sql.= " ,us_reset_code = NULL  ";
                $sql.= " ,us_pass_vence = '".$vence_pass."'";
		$sql.= " WHERE us_borrado = 0 AND (us_id = ".(int)$idUsuario.")";
                $objUsuarios = $this->objSQL->dbQuery($sql);
                if ($objUsuarios) return true;
            }
            return false;
	}
	
	function validarCambioPassword($datos){
		$sql = " SELECT us_reset_date, us_reset_code, us_reset_count FROM tbl_usuarios WITH(NOLOCK) where us_id = ".(int)$datos['idUsuario'];
		$rs = $this->objSQL->dbQuery($sql);
		if($reg = $this->objSQL->dbGetRow($rs,0,3)){

			
			if(
				strtotime('+60 minute',strtotime($reg['us_reset_date'])) >= strtotime(date('Y-m-d H:i'))//-- Si la fecha de solicitud de seteo es menor a 5min
				&& trim($reg['us_reset_code']) == trim($datos['reset_code']) //-- Si el codigo de validación se corresponde con el solicitado
				&& $reg['us_reset_count'] <= 3 //-- si la cantidad de cambios solicitados es menor a 3
			){
				return true;			
			}
		}
		return false;	
	}
	
	function habilitarCambioPassword($idUsuario, $reset_count, $reset_code){
		$sql = " UPDATE tbl_usuarios SET us_reset_date = CURRENT_TIMESTAMP, us_reset_count = ".(int)$reset_count.", us_reset_code = '".$reset_code."' WHERE us_id = ".(int)$idUsuario;
		$this->objSQL->dbQuery($sql);
	}

	function obtenerPassActual($idUsuario, $pass) {
		$arrUsuarios = array();
		if ($idUsuario && $pass){
			$strSQL = " SELECT us_pass FROM tbl_usuarios WITH(NOLOCK) ";
			$strSQL.= " WHERE us_id = ".(int)$idUsuario." AND us_pass = '".$pass."' AND us_borrado = 0 ";
			$objUsuarios = $this->objSQL->dbQuery($strSQL);
			$arrUsuarios = $this->objSQL->dbGetAllRows($objUsuarios);
			return $arrUsuarios;
		}
		return false;
	}

	function obtenerUsuariosListado($datos){
		
		$selectTop = ' TOP 30 ';
		if($datos['filtro'] == 'getAllReg'){
			$selectTop = $datos['filtro'] = '';
		}
		elseif(!empty($datos['filtro'])){
			$selectTop = '';
		}
		
		$sql = " SELECT ".$selectTop." us_id, us_nombreUsuario,us_expira, us_cant_fallido, us_ultimo_acceso, us_acceso_fallido, us_nombre, us_apellido, us_mailContacto, us_mailAlertas, us_telefono, 
		us_celular, us_nextel, us_pe_id, us_cl_id, us_usuarioCreador, us_fechaCreado, pe_nombre, cl_razonSocial,
		us_pass, us_keyId, us_comentarios, cl_tipo ";
		$sql.= " FROM tbl_usuarios AS usu WITH(NOLOCK) ";
		$sql.= " LEFT JOIN tbl_perfiles WITH(NOLOCK) ON (us_pe_id = pe_id) ";
		$sql.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON (us_cl_id = cl_id) ";
		$sql.= " WHERE us_borrado=0 ";
		
		if((int)@$datos['idEmpresa'] > 0) {
			$sql.= " AND (cl_id_distribuidor = ".(int)$datos['idEmpresa'] . " OR cl_id = " . (int)$datos['idEmpresa'] . ")";
		}
		if((int)@$datos['idTipoEmpresaExcluyente'] > 0){
			$sql.= " AND cl_tipo <> ".(int)$datos['idTipoEmpresaExcluyente'];}
		if(!empty($datos['filtro'])){
			$sql.= " AND (
					us_nombre like '%".$datos['filtro']."%' 
					OR us_apellido like '%".$datos['filtro']."%'
					OR us_nombreUsuario like '%".$datos['filtro']."%'
				) ";
			
		}
		
		if($datos['exclirUsuario']){
			$sql.= " AND us_id NOT IN (".(int)$datos['exclirUsuario'].") ";
		}
		
		if(!empty($datos['criterioOrden'])){
			$sql.= " ORDER BY ".$datos['criterioOrden']." ".$datos['orden'];
		}
		else{
			$sql.= " ORDER BY us_nombre, us_apellido ";
		}

		$objUsuarios = $this->objSQL->dbQuery($sql);
		$arrUsuarios = $this->objSQL->dbGetAllRows($objUsuarios);
		return $arrUsuarios;
	}
	
	function obtenerUsuariosPorEmpresa($idEmpresa, $idTipoEmpresaExcluyente = 0){
		$strSQL = " SELECT us_id, us_nombreUsuario, us_nombre, us_apellido, us_mailContacto, us_mailAlertas, us_telefono, 
	us_celular, us_nextel, us_pe_id, us_cl_id, us_usuarioCreador, us_fechaCreado, cl_razonSocial,
	us_pass, us_keyId, cl_tipo ";
		$strSQL.= " FROM tbl_usuarios WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_perfiles WITH(NOLOCK) ON (us_pe_id = pe_id) ";
		$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON (us_cl_id = cl_id) ";
		$strSQL.= " WHERE us_borrado = 0 ";
		
		if($idEmpresa){
			$strSQL.= " AND cl_id_distribuidor = ".(int)$idEmpresa;
		}
		
		if($idTipoEmpresaExcluyente){
			$strSQL.= " AND cl_tipo <> ".(int)$idTipoEmpresaExcluyente;
		}
		$strSQL.= " ORDER BY us_nombre, us_apellido ";
		
		$objUsuarios = $this->objSQL->dbQuery($strSQL);
		$arrUsuarios = $this->objSQL->dbGetAllRows($objUsuarios, 1);
		return $arrUsuarios;
	}
	
	function obtenerUsuariosSP($idUsuario,$filtro = NULL){
		$strSQL = "
			DECLARE @TablaIdUsuarioRaiz TABLE(
			id INT IDENTITY(1,1) PRIMARY KEY
			,idUsuario INT
			)

			INSERT INTO @TablaIdUsuarioRaiz (idUsuario)
			SELECT ISNULL(b.us_id,0)
			FROM tbl_usuarios a
			INNER JOIN  tbl_usuarios b ON a.us_id = b.us_usuarioCreador 	
			WHERE b.us_borrado = 0
			AND a.us_id = ".(int)$idUsuario." 

			DECLARE @MinId INT
			SELECT @MinId = MIN(id) FROM @TablaIdUsuarioRaiz
			DECLARE @MaxId INT
			SELECT @MaxId = MAX(id) FROM @TablaIdUsuarioRaiz

			IF @MinId > 0
			BEGIN
				DECLARE @IdHijo INT	
				DECLARE @Flag BIT
				SET @Flag = 1
		
				WHILE @Flag = 1
				BEGIN
					
					IF @MinId <> 1
					BEGIN
						SET @MinId = @MaxId + 1
						
						SELECT @MaxId = MAX(id) FROM @TablaIdUsuarioRaiz
						
						IF @MinId > @MaxId
							SET @Flag = 0
					END		
					
					WHILE @MinId <= @MaxId
					BEGIN
						SELECT @IdHijo = IdUsuario FROM @TablaIdUsuarioRaiz WHERE id = @MinId
						
						INSERT INTO @TablaIdUsuarioRaiz (idUsuario)
						SELECT us_id
						FROM tbl_usuarios 
						WHERE us_borrado = 0
						AND us_usuarioCreador = @IdHijo
						
						SET @MinId = @MinId + 1
					END
				END
		
				SELECT 
					b.us_id, b.us_nombreUsuario,b.us_expira,b.us_cant_fallido, b.us_nombre, b.us_apellido, b.us_mailContacto, b.us_mailAlertas, b.us_telefono, 
					b.us_celular, b.us_nextel, b.us_pe_id, b.us_cl_id, b.us_usuarioCreador, b.us_fechaCreado, cl_razonSocial,
					b.us_pass, b.us_keyId, cl_tipo
				FROM @TablaIdUsuarioRaiz a
				INNER JOIN  tbl_usuarios b WITH(NOLOCK) on a.idUsuario = b.us_id 
				LEFT JOIN tbl_perfiles WITH(NOLOCK) ON us_pe_id = pe_id
				LEFT JOIN tbl_clientes WITH(NOLOCK) ON us_cl_id = cl_id	
				WHERE (((us_nombre LIKE '%".$filtro."%') OR (us_apellido LIKE '%".$filtro."%')))
				ORDER BY us_id 
			END
			ELSE
				SELECT 0 AS Resultado ";

		$objUsuarios = $this->objSQL->dbQuery($strSQL);
		$intRows = $this->objSQL->dbNumRows($objUsuarios);
		if ($intRows) {
			$arrUsuarios = $this->objSQL->dbGetAllRows($objUsuarios, 1);
			if (isset($arrUsuarios[0]["us_id"]))
				return $arrUsuarios;
		}
		return false;
	}

	function insertarPreferenciasMovil($idUsuario){
		if($idUsuario){
			$strSQL = " INSERT INTO dbo.tbl_usuario_preferencias(up_us_id,up_vm_id) VALUES(".(int)$idUsuario.", 2)";
			if($objUsuarios = $this->objSQL->dbQuery($strSQL)) return true;
		}
		return false;
	}

	function obtenerVistasMoviles($idUsuario){
		$strSQL = " SELECT vm_id,vm_descripcion ";
		$strSQL.= " FROM tbl_usuario_preferencias WITH(NOLOCK) ";
		$strSQL.= " LEFT JOIN tbl_vista_moviles WITH(NOLOCK) ON (vm_id = up_vm_id) ";
		$strSQL.= " WHERE vm_borrado = 0 AND up_us_id = ".(int)$idUsuario;
		$objUsuarios = $this->objSQL->dbQuery($strSQL);
		$arrUsuarios = $this->objSQL->dbGetAllRows($objUsuarios, 1);
		return $arrUsuarios;
	}

	function setearIngresoCuenta($idUsuario) {		
		if ($idUsuario){			
			$objUsuarios = $this->objSQL->dbQuery("UPDATE tbl_usuarios SET us_ultimo_acceso = CURRENT_TIMESTAMP WHERE us_id = ".(int)$idUsuario);
			if ($objUsuarios) return true;
		}
		return false;
	}
	
	function get_tipoUsuario($idUsuario){
		$sql = " SELECT cl_tipo, cl_tipo_cliente, cl_id ";
		$sql.= " FROM tbl_usuarios ";
		$sql.= " INNER JOIN tbl_clientes ON us_cl_id = cl_id ";
		$sql.= " WHERE us_id = ".(int)$idUsuario;
		$rs = $this->objSQL->dbQuery($sql);
		$res = $this->objSQL->dbGetRow($rs);
		
		if($res['cl_tipo'] == 2 && $res['cl_tipo_cliente'] == 1){
			$this->idCliente = $res['cl_id'];
			return 'dador'; // Es Dador
		}
		elseif($res['cl_tipo'] == 2 && $res['cl_tipo_cliente'] == 2){
			return 'transportista'; // Es Transportista	
		}
		elseif($res['cl_tipo'] == 1 || $res['cl_tipo'] == 3){
			return 'empresa'; // Es Empresa Satelital o Localizar-t	
		}
		return false;	
	}

	function getPermisoWebServices($idUsuario){
		$sql = " SELECT uws_access FROM tbl_usuarios_webservices WHERE uws_us_id = ".(int)$idUsuario;
		$rs = $this->objSQL->dbQuery($sql);
		$reg = $this->objSQL->dbGetRow($rs,0,3);
		return (int)$reg['uws_access'];
	}
	
	function setPermisoWebServices($idUsuario, $permiso = 0){
		$sql = " SELECT COUNT(*) as cant FROM tbl_usuarios_webservices WHERE uws_us_id = ".(int)$idUsuario;
		$rs = $this->objSQL->dbQuery($sql);
		$reg = $this->objSQL->dbGetRow($rs,0,3);
		if($reg['cant'] > 0){
			$sql = " UPDATE tbl_usuarios_webservices SET uws_access = ".(int)$permiso;
			$sql.= " WHERE uws_us_id = ".(int)$idUsuario;	
			$this->objSQL->dbQuery($sql);
		}
		elseif((int)$permiso){
			$sql = " INSERT INTO tbl_usuarios_webservices(uws_us_id, uws_access) ";
			$sql.= " VALUES(".(int)$idUsuario.",".(int)$permiso.")";	
			$this->objSQL->dbQuery($sql);
		}
	}
	
	function obtenerUsuarioPorMail($mail) {
		$sql = " SELECT us_id, us_nombre, us_apellido, us_reset_date, us_reset_count ";
		$sql.= " FROM tbl_usuarios ";
		$sql.= " WHERE us_borrado = 0 ";
		$sql.= " AND us_nombreUsuario = '".$mail."' AND us_borrado = 0 ";
		$objUsuarios = $this->objSQL->dbQuery($sql);
		if($arrUsuarios = $this->objSQL->dbGetRow($objUsuarios, 0, 3)){
			return $arrUsuarios;	
		}
		return false;
	}
	
	function obtenerUsuariosPorPerfil($arrPerfil, $idCliente){
		$strSQL = " SELECT us_id, us_nombreUsuario, us_nombre, us_apellido ";
		$strSQL.= " FROM tbl_usuarios ";
		$strSQL.= " WHERE us_borrado = 0 AND us_pe_id IN (".implode(',',$arrPerfil).") AND us_cl_id = ".(int)$idCliente;
		$objUsuarios = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($objUsuarios,  3);
	}
}