<?php
require_once 'clases/clsAbms.php';
class AlertasXGeocerca extends Abm{

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_alertas','al');
		
		$this->diasSemana = array(
			1 => 'Lunes',
			2 => 'Martes',
			3 => 'Miercoles',
			4 => 'Jueves',
			5 => 'Viernes',
			6 => 'Sabado',
			7 => 'Domingo',
    	);
	
	}

	function obtenerRegistros($id){
		$strSQL = " SELECT * FROM tbl_alertas WITH(NOLOCK) ";
		$strSQL.= " WHERE al_id = ".(int)$id;
		
		$objRegistros = $this->objSQL->dbQuery($strSQL);
		$arrRegistros = $this->objSQL->dbGetAllRows($objRegistros,3);
		return $arrRegistros;
	}
	
	function modificarGeocercasAlerta($idAlerta, $arrGeocercas){
		$strSQL="delete from tbl_alertas_referencias where ar_al_id={$idAlerta}";
		$this->objSQL->dbQuery($strSQL);
		$strSQL="insert into tbl_alertas_referencias(ar_al_id,ar_re_id) values({$idAlerta},@1)";
		foreach($arrGeocercas as $idGeo){
			$this->objSQL->dbQuery(str_replace('@1',$idGeo,$strSQL));
		}
	}

	function modificarEventosAlerta($idAlerta, $arrEventos){
		$strSQL="delete from tbl_alertas_eventos where ae_al_id={$idAlerta}";
		$this->objSQL->dbQuery($strSQL);
		$strSQL="insert into tbl_alertas_eventos(ae_al_id,ae_re_id) values({$idAlerta},@1)";
		foreach($arrEventos as $idEvento){
			$this->objSQL->dbQuery(str_replace('@1',$idEvento,$strSQL));
		}
	}

	function modificarMovilesAlerta($idAlerta, $arrMoviles){
		$strSQL="delete from tbl_alertas_moviles where am_al_id={$idAlerta}";
		$this->objSQL->dbQuery($strSQL);
		$strSQL="insert into tbl_alertas_moviles(am_al_id,am_mo_id) values({$idAlerta},@1)";
		if($arrMoviles[0]){
			foreach($arrMoviles as $idMovil){
				$this->objSQL->dbQuery(str_replace('@1',$idMovil,$strSQL));
			}
		}
	}

	function modificarUsuariosAlerta($idAlerta, $arrUsuarios){ // A
		
		$strSQL="delete from tbl_alertas_usuarios where au_al_id={$idAlerta}";
		$this->objSQL->dbQuery($strSQL);
		
		$strSQL="insert into tbl_alertas_usuarios(au_al_id,au_us_id) values({$idAlerta},@1)";
		if ($arrUsuarios && $arrUsuarios[0]){
			foreach($arrUsuarios as $idUs){
				$this->objSQL->dbQuery(str_replace('@1',$idUs,$strSQL));
			}
		}
	}

	function obtenerGeocercasAlerta($idAlerta){
		$strSQL = " SELECT ar_re_id as id, re_nombre as dato ";
		$strSQL.= " FROM tbl_alertas_referencias WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_referencias WITH(NOLOCK) ON re_id = ar_re_id ";
		$strSQL.= " WHERE ar_al_id = ".(int)$idAlerta;
		$objRes = $this->objSQL->dbQuery($strSQL);
		$res = $this->objSQL->dbGetAllRows($objRes);
		return $res;
	}

	function obtenerEventosAlerta($idAlerta){
		$strSQL = " SELECT ae_re_id AS id, tr_descripcion as dato ";
		$strSQL.= " FROM tbl_alertas_eventos WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_traduccion_reportes WITH(NOLOCK) ON tr_id = ae_re_id ";
		$strSQL.= " WHERE ae_al_id = ".(int)$idAlerta;
		$objRes = $this->objSQL->dbQuery($strSQL);
		$res = $this->objSQL->dbGetAllRows($objRes);
		return $res;
	}

	function obtenerMovilesAlerta($idAlerta){
		$strSQL = " SELECT am_mo_id AS id, mo_matricula as dato ";
		$strSQL.= " FROM tbl_alertas_moviles WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = am_mo_id ";
		$strSQL.= " WHERE am_al_id = ".(int)$idAlerta;
		$objRes = $this->objSQL->dbQuery($strSQL);
		$res = $this->objSQL->dbGetAllRows($objRes);
		return $res;
	}

	function obtenerUsuariosAlerta($idAlerta){
		$strSQL = " SELECT au_us_id AS id, us_nombre+', '+us_apellido AS dato ";
		$strSQL.= " FROM tbl_alertas_usuarios WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_id = au_us_id ";
		$strSQL.= " WHERE au_al_id = ".(int)$idAlerta;
		$objRes = $this->objSQL->dbQuery($strSQL);
		$res = $this->objSQL->dbGetAllRows($objRes);
		return $res;
	}
	
	function obtenerMovilesAlertasRecientes($idUsuario = 0, $tiempo = 1, $hy_id = 0){
		$strSQL = " SELECT al_id, al_nombre, mo_matricula, hy_id "; 
		$strSQL.= " FROM tbl_alertas_usuarios WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_alertas WITH(NOLOCK) ON al_id = au_al_id ";
		$strSQL.= " INNER JOIN tbl_alertas_moviles WITH(NOLOCK) ON al_id = am_al_id ";
		$strSQL.= " INNER JOIN tbl_moviles WITH(NOLOCK) ON mo_id = am_mo_id ";
		$strSQL.= " INNER JOIN tbl_history_".getFechaServer('dmY')." WITH(NOLOCK) ON hy_al_id = al_id ";
		$strSQL.= " INNER JOIN tbl_unidad WITH(NOLOCK) ON hy_un_id = un_id AND un_mo_id = mo_id ";
		$strSQL.= " WHERE au_us_id = ".(int)$idUsuario;
		$strSQL.= " AND DATEDIFF(MINUTE, hy_fechaGenerado, CURRENT_TIMESTAMP) <= ".$tiempo;
		$strSQL.= " AND ((".(int)$hy_id." = 0) OR (hy_id > ".(int)$hy_id.")) ";
		$strSQL.= " ORDER BY hy_id ASC ";
		$objRes = $this->objSQL->dbQuery($strSQL);
		$res = $this->objSQL->dbGetAllRows($objRes);
		return $res;
	}
	
	function getAlertas($datos){
		$selectTop = ' TOP 30 ';
		if($datos['filtro'] == 'getAllReg'){
			$selectTop = $datos['filtro'] = '';
		}
		elseif(!empty($datos['filtro'])){
			$selectTop = '';
		}
		
		$sql = " SELECT ".$selectTop." al_id, al_nombre, us_nombre +' '+ us_apellido AS usuario, al_us_id ";
		$sql.= " FROM tbl_alertas WITH(NOLOCK) ";
		$sql.= " INNER JOIN tbl_usuarios WITH(NOLOCK) ON us_id=al_us_id ";
		$sql.= " WHERE al_borrado=0 ";
		
		if ($datos['idTipoEmpresa']!=3) {
			$sql.= " AND al_cl_id = (SELECT us_cl_id FROM tbl_usuarios WHERE us_id = ".(int)$datos['idUsuario'].")";
			if (($datos['idTipoEmpresa']==1) && ($datos['idPerfil'])!=34) {
				$sql.= ' AND al_us_id = '.$datos['idUsuario'];
			}		
		}
		
		if ($datos['filtro']!=""){
			$sql.=" AND (al_nombre LIKE '%".$datos['filtro']."%' OR us_nombre LIKE '%".$datos['filtro']."%' OR us_apellido LIKE '%".$datos['filtro']."%')";
		}
		
		if($datos['idAlerta']!=0){
			$sql.="AND al_id = ".$datos['idAlerta'];
		}
		$sql.= " ORDER BY usuario, al_nombre"; 
				
		$rs = $this->objSQL->dbQuery($sql);
		return  $this->objSQL->dbGetAllRows($rs,3);	
	}
	
	function traduccionDescripcionAlerta($idAlerta){
		global $lang;
		
		$sql= " SELECT  	
				CASE WHEN (SELECT ae_re_id FROM tbl_alertas_eventos WITH(NOLOCK) WHERE ae_al_id = al_id AND ae_re_id = 15) IS NOT NULL THEN 'si' ELSE 'no' END AS 'evento_ingreso'
				,CASE WHEN (SELECT ae_re_id FROM tbl_alertas_eventos WITH(NOLOCK) WHERE ae_al_id = al_id AND ae_re_id = 14) IS NOT NULL THEN 'si' ELSE 'no' END AS 'evento_egreso'
				FROM tbl_alertas WITH(NOLOCK) 
				WHERE al_id = ".(int)$idAlerta;
		$rs = $this->objSQL->dbQuery($sql);
		$arrEventos = $this->objSQL->dbGetRow($rs,0,3);	
		
		$sql= " SELECT ae_re_id as id FROM tbl_alertas_eventos WITH(NOLOCK) WHERE ae_al_id = ".(int)$idAlerta." AND ae_re_id not in (14,15) ";
		$rs = $this->objSQL->dbQuery($sql);
		$arrEventosOtros = $this->objSQL->dbGetAllRows($rs,3);		
		
		$sql = " SELECT mo_matricula FROM tbl_alertas WITH(NOLOCK) 
				INNER JOIN tbl_alertas_moviles WITH(NOLOCK) ON am_al_id = al_id 
				INNER JOIN tbl_moviles WITH(NOLOCK) ON am_mo_id = mo_id
				WHERE al_id = ".(int)$idAlerta;
		$rs = $this->objSQL->dbQuery($sql);
		$arrMoviles = $this->objSQL->dbGetAllRows($rs,3);	
		
		$sql = " SELECT re_nombre FROM tbl_alertas WITH(NOLOCK) 
				INNER JOIN tbl_alertas_referencias WITH(NOLOCK) ON ar_al_id  = al_id 
				INNER JOIN tbl_referencias WITH(NOLOCK) ON ar_re_id  = re_id
				WHERE al_id = ".(int)$idAlerta;
		$rs = $this->objSQL->dbQuery($sql);
		$arrReferencias = $this->objSQL->dbGetAllRows($rs,3);	
		
		$auxEventos = $separador = '';
		if($arrEventos['evento_ingreso'] == 'si'){
			$auxEventos.= '<b><spam style="color:blue;">'.$lang->system->ingreso.'</spam></b>'; 
			$separador = ' o ';
		}
		if($arrEventos['evento_egreso'] == 'si'){
			$auxEventos.= $separador.'<b><spam style="color:blue;">'.$lang->system->egreso.'</spam></b>'; 
		}
		
		$auxMoviles = $separador = '';
		if($arrMoviles){
			if(count($arrMoviles) > 1){
				$auxMoviles.= $lang->system->alertas_txt6.' ';	
			}
			else{
				$auxMoviles.= $lang->system->alertas_txt5.' ';	
			}
			foreach($arrMoviles as $item){
				$auxMoviles.= $separador.'<b><spam style="color:blue;">'.encode($item['mo_matricula']).'</spam></b>';
				$separador = ', ';
			}	
		}
		
		$auxReferencias = $separador = '';
		if($arrReferencias){
			foreach($arrReferencias as $item){
				$auxReferencias.= $separador.'<b><spam style="color:blue;">'.encode($item['re_nombre']).'</spam></b>';
				$separador = ' o ';
			}	
		}
		
		
		$objIdioma = new Idioma();
		$eventos = $objIdioma->getEventos($_SESSION['idioma']);
		$auxEventosOtros = $separador = '';
		if($arrEventosOtros){
			foreach($arrEventosOtros as $item){
				$dato = 'evento_'.(int)$item['id'];
				$auxEventosOtros.= $separador.'<b><spam style="color:blue;">'.$eventos->$dato->__toString().'</spam></b>';
				$separador = ', ';
			}	
		}
	
		if(!empty($auxReferencias)){
			return $lang->system->alertas_txt4.' '.$auxEventos.' '.$auxMoviles.' '.$lang->system->alertas_txt1.' '.$auxReferencias.(!empty($auxEventosOtros)?(' '.$lang->system->alertas_txt7.' '.$auxEventosOtros):'');
		}
		else{
			return 	$lang->system->alertas_txt8.' '.$auxEventosOtros.' '.$auxMoviles;
		}
	}

	function traduccionDescripcionAlertaMobile($idAlerta){
		global $lang;
		
		$sql = " SELECT re_nombre FROM tbl_alertas WITH(NOLOCK) 
				INNER JOIN tbl_alertas_referencias WITH(NOLOCK) ON ar_al_id  = al_id 
				INNER JOIN tbl_referencias WITH(NOLOCK) ON ar_re_id  = re_id
				WHERE al_id = ".(int)$idAlerta;
		$rs = $this->objSQL->dbQuery($sql);
		$arrReferencias = $this->objSQL->dbGetAllRows($rs,3);	
		
		$auxReferencias = $separador = '';
		if($arrReferencias){
			foreach($arrReferencias as $item){
				$auxReferencias.= $separador.'<b><spam style="color:blue;">'.encode($item['re_nombre']).'</spam></b>';
				$separador = ' o ';
			}	
		}
		
		return str_replace('[REFERENCIA]', $auxReferencias, $lang->system->alertas_txt9);
	}
	
	
	function validarEdicionAlertas($idAlerta, $idUsuario, $arrMoviles = NULL){
		//-- Se obtiene el propietario de la alerta como primera validación --//
		if($idUsuario == $_SESSION['idUsuario']){
			return true;
		}
		/*
		if(!empty($arrMoviles)){
			//-- Se obtiene cant de moviles que estan en la Alerta y no lo tiene el usuario--//
			$sql = " SELECT COUNT(am_mo_id) AS cant_moviles_alerta "; 
			$sql.= " FROM tbl_alertas_moviles "; 
			$sql.= " INNER JOIN tbl_moviles ON mo_id = am_mo_id ";
			$sql.= " WHERE am_al_id = ".(int)$idAlerta." AND mo_borrado = 0 ";
			$sql.= " AND am_mo_id NOT IN (".$arrMoviles.") ";
			$rs = $this->objSQL->dbQuery($sql);
			$res = $this->objSQL->dbGetRow($rs,0,3);
			$moviles_excedentes = $res['cant_moviles_alerta'];
		}
		else{
			$moviles_excedentes = -1;	
		}
		
		if($moviles_excedentes === 0){
			return true;	
		}
		else{
			
		}*/	
		
		return false;
	}
	
	function setAlertasPorDias($idAlerta, $dia, $inicio = false, $fin = false){
		
		$horaIni = $inicio?$inicio:'00:00:00';
		$horaFin = $fin?$fin:'00:00:00';
		
		$sql_update = " UPDATE tbl_alertas_dia_semana SET ";
        $sql_update.= " ds_hora_inicio = '".$horaIni."', ds_hora_fin = '".$horaFin."' ";
		$sql_update.= " WHERE ds_al_id = ".(int)$idAlerta." AND ds_dia = ".(int)$dia;
		
		$sql_insert = " INSERT INTO tbl_alertas_dia_semana(ds_al_id,ds_dia ,ds_dia_descripcion,ds_hora_inicio,ds_hora_fin) ";
        $sql_insert.= " VALUES(".(int)$idAlerta.",".(int)$dia.",'".$this->diasSemana[(int)$dia]."','".$horaIni."','".$horaFin."') ";
		
		$strSQL = "";
		if(!$inicio && !$fin){
			$strSQL = $sql_update;	
		}
		else{
			$sql = " SELECT COUNT(*) as cant FROM tbl_alertas_dia_semana WITH(NOLOCK) WHERE ds_al_id=".(int)$idAlerta." AND ds_dia=".(int)$dia;	
			$rs = $this->objSQL->dbQuery($sql);
			$objAlerta = $this->objSQL->dbGetRow($rs,0,3);
			if($objAlerta['cant']){
				$strSQL = $sql_update;	
			}
			else{
				$strSQL = $sql_insert;		
			}	
		}
		
		return $this->objSQL->dbQuery($strSQL);		
	}
	
	function obtenerAlertasPorDias($idAlerta){
        $strSQL = " SELECT * FROM tbl_alertas_dia_semana WITH(NOLOCK) WHERE ds_al_id = ".(int)$idAlerta;
        $objAlertasDiaSemana = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($objAlertasDiaSemana, 3);
	}
	
	function obtenerUsuarios($idUsuario){
		$strSQL = " SELECT 
			us_id AS id, us_nombreUsuario as dato, us_nombre, us_apellido, us_mailContacto, us_mailAlertas, us_telefono, 
			us_celular, us_nextel, us_pe_id, us_cl_id, us_usuarioCreador, us_fechaCreado, pe_nombre as pe_descripcion, cl_razonSocial,
			cl_tipo
		FROM tbl_usuarios WITH(NOLOCK)
		LEFT JOIN tbl_perfiles WITH(NOLOCK) ON us_pe_id = pe_id
		LEFT JOIN tbl_clientes WITH(NOLOCK) ON us_cl_id = cl_id	
		WHERE us_borrado = 0 AND us_cl_id IN (SELECT us_cl_id FROM tbl_usuarios WHERE us_id = ".(int)$idUsuario.")
		ORDER BY dato ";
		$obj = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($obj, 3);
	
	}
}