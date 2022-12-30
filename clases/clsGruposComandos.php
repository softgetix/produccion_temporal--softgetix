<?php
require_once 'clases/clsAbms.php';
class GrupoComandos extends Abm{
	private $comandosDisponibles;
	private $comandosAsignados;

	function __construct($objSQLServer) {
		parent::__construct($objSQLServer,'tbl_grupo','gr');
		$this->comandosDisponibles=array();
		$this->comandosAsignados=array();
	}

	function obtenerRegistros($id = 0, $filtro="" ,$campoValidador='' ,$idValidador=0, $soloCantidad=false, $noProcesarDisponibles=false){
		if ($id!=0){
			$strSQL = "	SELECT gc_gr_id, co_id, co_nombre, co_codigo, co_tipo, co_instrucciones ";
			$strSQL.= " FROM tbl_grupo_comando gc WITH(NOLOCK) ";
			$strSQL.= " INNER JOIN tbl_comando co WITH(NOLOCK) ON gc.gc_co_id=co.co_id AND co.co_borrado=0 ";
			$strSQL.= " WHERE gc.gc_gr_id = CASE ".(int)$id." WHEN 0 THEN gc.gc_gr_id ELSE ".(int)$id." END ";
			
			$objRegistros = $this->objSQL->dbQuery($strSQL);
			$temp = $this->objSQL->dbGetAllRows($objRegistros, 3);
			$arrRegistros = array();
			if($temp){
				foreach($temp as $item){
					$arrRegistros[$item['co_id']] = $item;	
				}
			}
			$this->comandosAsignados = $arrRegistros;
			
			if(!$noProcesarDisponibles){
				require_once 'clases/clsComandos.php';
				$objComandos = new Comando($this->objSQL);
				$arrRegistros2 = $objComandos->obtenerRegistros();

				//array_dif no anda, lo tuve que hacer a mano. pasar a procedimientos si es muy pesado con muchos registros
				$temp = array();
				foreach ($arrRegistros2 as $a){
					if (!isset($arrRegistros[$a['co_id']])){
						$temp[$a['co_id']] = $a;
					}
				}
				$this->comandosDisponibles = $temp;
			}
		}
			
		$strSQL = " SELECT gr_id, gr_nombre ";
		$strSQL.= " FROM tbl_grupo WITH(NOLOCK) WHERE gr_borrado = 0 ";
		if($id){
			$strSQL.= " AND gr_id = ".(int)$id;
		}
		$arrRegistros = $this->objSQL->dbGetAllRows($objRegistros,3);
		return $arrRegistros;
	}
	
	function obtenerComandosDisponibles(){
		return $this->comandosDisponibles;
	}
	function obtenerComandosAsignados(){
		return $this->comandosAsignados;
	}

	function obtenerComandosFavoritosCombo($equipo = NULL){
		$strSQL = " SELECT distinct (co_id) as co_id, co_nombre, co_codigo ";
		$strSQL.= " FROM tbl_comando WITH(NOLOCK) ";
		if(!empty($equipo)){
			$strSQL.= " INNER JOIN tbl_unidad WITH(NOLOCK) on un_mod_id = co_mo_id ";
		}
		$strSQL.= " WHERE co_borrado = 0 AND co_favorito = 1 ";
		if(!empty($equipo)){
			$strSQL.= " AND un_mostrarComo = '".$equipo."' ";
		}
		$strSQL.= " ORDER BY co_nombre ";
		
		$objRegistros = $this->objSQL->dbQuery($strSQL);
		$temp = $this->objSQL->dbGetAllRows($objRegistros, 3);
		$arrRegistros = array();
		if($temp){
			foreach($temp as $item){
				$arrRegistros[$item['co_id']] = $item;	
			}
		}
		return $arrRegistros;
	}

	function eliminarComandosGrupo($id){
		$strSQL = " DELETE FROM tbl_grupo_comando WHERE gc_gr_id = ".$id;
		if($this->objSQL->dbQuery($strSQL)){
			return true;
		}	
		return false;
	}

	function insertarComandosGrupo($id, $comandos){
		$comandos=explode(',',trim($comandos,','));
		foreach($comandos as $comando){
			$strSQL = " INSERT INTO tbl_grupo_comando(gc_gr_id, gc_co_id) ";
			$strSQL.= " VALUES(".$id.", '".$comandos."') ";
			$this->objSQL->dbQuery($strSQL);
		}
		return true;
	}
	
	function obtenerGrupoComandos($idGrupo = 0){
		$strSQL = " SELECT gc_gr_id, co_id, co_nombre, co_codigo, co_tipo, co_instrucciones ";
		$strSQL.= " FROM tbl_grupo_comando gc WITH(NOLOCK) ";
		$strSQL.= " INNER JOIN tbl_comando co WITH(NOLOCK) ON gc.gc_co_id=co.co_id AND co.co_borrado=0 ";
		if($idGrupo){
			$strSQL.= " WHERE gc.gc_gr_id = ".(int)$idGrupo;	
		}
		$obj = $this->objSQL->dbQuery($strSQL);
		return $this->objSQL->dbGetAllRows($obj, 3);
	}
}