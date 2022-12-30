<?php
class ProbadorDePanico {
	var $objSQLServer;

	function ProbadorDePanico($objSQLServer) {
		$this->objSQL = $objSQLServer;
		return TRUE;
	}

	function obtenerClientes($idEempresa) {
		$strSQL = " SELECT cl_id, cl_razonSocial, cl_email ";
		$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " WHERE cl_id_distribuidor= ".$idEempresa." AND cl_borrado = 0 ";
		$strSQL.= " ORDER BY cl_razonSocial ";

		$objClientes = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objClientes);
		return $objRow;
	}
	
	function obtenerMoviles($idCliente, $idEmpresa) {
		$strSQL = " SELECT mo_id, mo_matricula, mo_identificador, mo_otros, mo_marca, mo_modelo ";
		$strSQL.= " FROM tbl_moviles WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios_moviles WITH(NOLOCK) ON um_mo_id = mo_id ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON um_us_id = us_id ";
		$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON us_cl_id = cl_id ";
		//$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON mo_id_cliente_facturar = cl_id ";
		$strSQL.= " WHERE mo_borrado = 0  AND cl_borrado = 0 ";
		$strSQL.= " AND cl_id = ".(int)$idCliente;
		//$strSQL.= " AND mo_id_cliente_facturar = ".(int)$idCliente." AND cl_id_distribuidor = ".(int)$idEmpresa;
		$strSQL.= " ORDER BY mo_matricula ";
		$objMoviles = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objMoviles, 3);
		return $objRow;
	}
	
	function obtenerReferencias($idCliente,$idEmpresa){
		$strSQL = " SELECT re_id, rg_nombre, re_ubicacion, re_nombre, re_panico ";
		$strSQL.= " FROM tbl_clientes WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON  cl_id = us_cl_id ";
		$strSQL.= " INNER JOIN tbl_referencias re WITH(NOLOCK) ON us_id = re_us_id ";
		$strSQL.= " INNER JOIN tbl_referencias_grupos WITH(NOLOCK) ON re_rg_id = rg_id ";
		$strSQL.= " WHERE cl_id = ".(int)$idCliente."  AND cl_id_distribuidor = ".(int)$idEmpresa ;
		$strSQL.= " AND cl_borrado = 0 AND us_borrado = 0 AND re_borrado = 0 AND re_panico > 0";
		//AND rg_borrado = 0 SE SACO PORQ LO BORRAMOS LOGICAMENTE PARA Q NO SE VISUALICE EN LA EDICION DE REFERENCIAS LAS REFERENCIAS DE TIPO SEGURIDAD ADT
		$strSQL.= " ORDER BY re_panico, re_id ";
		$objReferencias = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objReferencias, 3);
		return $objRow;
	}
	
	function obtenerUltimasPruebas($idCliente, $idEmpresa) {
		$strSQL = " SELECT hp_mo_id,hp_re_id, hp_fecha_inicio_prueba as fecha_hora";
		$strSQL.= " FROM tbl_historial_probador_panico WITH(NOLOCK) ";
		$strSQL.= " WHERE hp_estado = 1 AND hp_borrado = 0 ";
		$strSQL.= " AND hp_mo_id IN (
						SELECT mo_id FROM tbl_moviles WHERE mo_id_cliente_facturar = ".(int)$idCliente." AND mo_borrado = 0
					) ";
		$strSQL.= " AND hp_re_id IN (
						SELECT tbl_referencias.re_id 
						FROM tbl_clientes WITH(NOLOCK)
						INNER JOIN tbl_usuarios WITH(NOLOCK) ON tbl_clientes.cl_id = tbl_usuarios.us_cl_id 
						INNER JOIN tbl_referencias WITH(NOLOCK) ON tbl_usuarios.us_id = tbl_referencias.re_us_id 
						WHERE cl_id = ".(int)$idCliente." AND cl_id_distribuidor = ".(int)$idEmpresa."
						AND re_panico > 0 AND tbl_clientes.cl_borrado=0 AND tbl_usuarios.us_borrado=0 
						AND tbl_referencias.re_borrado = 0 ) ";
		$strSQL.= " ORDER BY hp_mo_id,hp_fecha_inicio_prueba ASC ";
		$objPruebas = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objPruebas, 3);
		return $objRow;
	}
	
	function obtenerDisponibilidadPruebas($idCliente, $minutos, $idEmpresa) {
		$strSQL = " SELECT mo_id,abs(DATEDIFF(MINUTE, sh_fechaGeneracion, (SELECT DATEADD(hour,server,GETDATE()) FROM zonaHoraria(un_mostrarComo,NULL)))) as diferencia, dg_entradas  ";
		$strSQL.= " , CASE WHEN (SELECT us_estado FROM tbl_unidad_servicios WHERE us_ust_id = 1 AND us_un_id = un_id) = 1 THEN 1
					ELSE (SELECT us_estado FROM tbl_unidad_servicios WHERE us_ust_id = 4 AND us_un_id = un_id) END as service_panic ";
		$strSQL.= " FROM tbl_sys_heart WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_dato_gp WITH(NOLOCK) ON (dg_sh_id = sh_id) ";
		$strSQL.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON tbl_sys_heart.sh_un_id = tbl_unidad.un_id ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON tbl_unidad.un_mo_id = tbl_moviles.mo_id ";
		$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON mo_id_cliente_facturar = cl_id ";
		$strSQL.= " WHERE un_borrado = 0 AND mo_borrado = 0 ";
		$strSQL.= " AND mo_id_cliente_facturar = ".(int)$idCliente;
		$strSQL.= " AND cl_id_distribuidor = ".(int)$idEmpresa;
		//---- Si en los ultimos 10 minutos probamos 3 panicos y no llegaroon no permitimos probar
		$strSQL.= " AND mo_id not in(
				SELECT sq.mo_id FROM (
					SELECT mo_id, COUNT (*) as cantidad from  tbl_historial_probador_panico WITH(NOLOCK)
					INNER JOIN tbl_moviles WITH(NOLOCK) ON hp_mo_id = mo_id
					WHERE hp_fecha_recepcion_panico is null and mo_id_cliente_facturar = ".(int)$idCliente."
					and hp_fecha_inicio_prueba > ((SELECT DATEADD(hour,server,GETDATE()) FROM zonaHoraria(un_mostrarComo,NULL)) - 0.007)
					group by mo_id
				) as sq
			WHERE sq.cantidad > 2) ";
		$strSQL.= " ORDER BY mo_id,diferencia ";
		
		$objPruebas = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetAllRows($objPruebas, 3);
		return $objRow;
	}
	
	function insertarPrueba($idMovil, $idReferencia) {
		
		//-- Borro lógicamente todas las pruebas para ese movil en esa zona. --//
		$strSQL = "	UPDATE tbl_historial_probador_panico SET hp_borrado = 1 WHERE hp_id in (
				SELECT hp_id FROM tbl_historial_probador_panico WITH(NOLOCK) 
				INNER JOIN tbl_referencias WITH(NOLOCK) ON hp_re_id = re_id
				WHERE re_panico = (SELECT re_panico FROM tbl_referencias WHERE re_id = ".(int)$idReferencia.") 
				AND hp_mo_id = ".(int)$idMovil."
			) ";
		$this->objSQL->dbQuery($strSQL);
		//-- --//
	
		$strSQL = "
			--// CORRIMIENTO HORARIO //--
			DECLARE @un_mostrarComo VARCHAR(30)
			SELECT @un_mostrarComo = un_mostrarComo FROM tbl_unidad WITH(NOLOCK) WHERE un_mo_id = ".(int)$idMovil."
		
			DECLARE @fechaServer DATETIME = CURRENT_TIMESTAMP;
			DECLARE @corregirServer INT = NULL;
			SELECT @corregirServer=server FROM zonaHoraria(@un_mostrarComo,NULL);
			
			IF(@corregirServer != '')
			BEGIN
				SET @fechaServer = DATEADD(HOUR, @corregirServer,@fechaServer);
			END	
			--// //--
	
			DECLARE @lat FLOAT;
			DECLARE @lng FLOAT;
			SELECT TOP 1 @lat=rc_latitud, @lng=rc_longitud FROM tbl_referencias_coordenadas WITH(NOLOCK) WHERE rc_re_id=".(int)$idReferencia."
				
			INSERT INTO tbl_historial_probador_panico(hp_mo_id, hp_re_id, hp_lat_ref_actual, hp_lon_ref_actual, hp_fecha_inicio_prueba, hp_borrado)
			VALUES(".(int)$idMovil.", ".(int)$idReferencia.", @lat, @lng, @fechaServer, 0);
			SELECT SCOPE_IDENTITY() as codigo; ";
		$objPruebas = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetRow($objPruebas, 0, 3);
		if($objRow){
			return $objRow["codigo"];
		}
		return 0;
	}
	
	function revisarPrueba($idPrueba, $idEmpresa){
		$strSQL = " SELECT isnull(hp_estado, -1) as resultado";
		$strSQL.= " FROM tbl_historial_probador_panico WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = hp_mo_id ";
		$strSQL.= " INNER JOIN tbl_clientes WITH(NOLOCK) ON mo_id_cliente_facturar = cl_id ";
		$strSQL.= " WHERE hp_borrado = 0 AND mo_borrado = 0 AND cl_borrado = 0 ";
		$strSQL.= " AND hp_id = ".(int)$idPrueba." AND cl_id_distribuidor = ".(int)$idEmpresa;
		$objPruebas = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetRow($objPruebas, 0, 3);
		$intRows = $this->objSQL->dbNumRows($objPruebas);
		$arrPruebas = array();
		if ($intRows) {
			return $objRow["resultado"];
		}
		return -1;
	}
	
	function revisarPruebasFallidasRecientes($movil, $referencia, $prueba_excluida, $minutos) {
		$strSQL = " SELECT COUNT(hp_id) FROM tbl_historial_probador_panico WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON un_mo_id = hp_mo_id ";
		$strSQL.= " WHERE hp_mo_id = ".(int)$movil." AND hp_re_id = ".(int)$referencia." AND hp_id <> ".(int)$prueba_excluida;
		$strSQL.= " AND hp_estado = 0  AND abs(DATEDIFF(MINUTE, hp_fecha_inicio_prueba, (SELECT DATEADD(hour,server,GETDATE()) FROM zonaHoraria(un_mostrarComo,NULL)))) <= ".(int)$minutos;
		//$strSQL.= " AND hp_borrado=0 ";
        $objPruebas = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetRow($objPruebas, 0);
		if($objRow){
			return $objRow[0];	
		}
		return 0;
	}
	
	function anularPrueba($idPrueba, $idEmpresa) {
		$strSQL = " UPDATE tbl_historial_probador_panico SET hp_estado = 0 WHERE hp_id=".(int)$idPrueba;
		$strSQL.= " AND hp_mo_id in (
					SELECT mo_id FROM tbl_moviles WITH(NOLOCK) 
					INNER JOIN tbl_clientes WITH(NOLOCK) ON mo_id_cliente_facturar = cl_id
					WHERE mo_borrado = 0 AND cl_borrado = 0  AND cl_id_distribuidor = ".(int)$idEmpresa." )";
		
		$objPruebas = $this->objSQL->dbQuery($strSQL);
	}
	
	function borrarPrueba($idReferencia,$idMovil, $idEmpresa) {
		$strSQL = " UPDATE tbl_historial_probador_panico SET hp_borrado = 1 WHERE hp_re_id=".(int)$idReferencia;
		$strSQL.= " AND hp_mo_id in (
					SELECT mo_id FROM tbl_moviles WITH(NOLOCK)
					INNER JOIN tbl_clientes WITH(NOLOCK) ON mo_id_cliente_facturar = cl_id
					WHERE mo_borrado = 0 AND cl_borrado = 0 AND cl_id_distribuidor = ".(int)$idEmpresa." )";
		$strSQL.= " AND hp_mo_id = ".(int)$idMovil;
		if($objPruebas = $this->objSQL->dbQuery($strSQL)){
			return true;	
		}
		return false;
	}
}
?>