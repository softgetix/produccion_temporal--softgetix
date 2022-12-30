<?php

class Abm {
	protected $objSQL;
	protected $tabla;
	protected $prefijo;
	protected $pa_consulta;

	function __construct($objSQLServer, $tabla, $prefijo, $pa_consulta = NULL) {
		$this->objSQL = $objSQLServer;
		$this->tabla=$tabla;
		$this->prefijo=$prefijo;
		$this->pa_consulta=$pa_consulta;
	}

	function eliminarRegistro($id, $tabla = NULL, $prefijo = NULL){
		if (is_array($id)) $id=implode(',',$id);
		$tabla = $tabla?$tabla:$this->tabla;
		$prefijo = $prefijo?$prefijo:$this->prefijo;
		if($id){
			$strSQL = " UPDATE ".$tabla." set ".$prefijo."_borrado = 1 ";
			$strSQL.= " WHERE ".$prefijo."_id IN (".$id.")";
			if($this->objSQL->dbQuery($strSQL)){
				return true;
			}	
		}
		return false;
	}

	/**
	 *@param string|array $campos lista separada por comas de los campos a insertar
	 *@param string|array $valorCampos lista separada por comas de los valores a insertar. ''campo_string''
	 *@param string $campoValidador
	 *@return bool
	 */
	function insertarRegistro($campos, $valorCampos, $campoValidador = NULL, $tabla = NULL){
		$tabla = $tabla?$tabla:$this->tabla;
		$procesarInsert = true;
		
		if($campos && $valorCampos){
			if (is_array($campos)) $campos=implode(',',$campos);
			if (is_array($valorCampos)) $valorCampos=implode(',',$valorCampos);
			
			if(!empty($campoValidador)){
				$duplicado = $this->obtenerRegistros(0,'getAllReg',$campoValidador,0,true);
				if($duplicado){
					$procesarInsert = false;
				}
			}
			
			if($procesarInsert){
				$valorCampos = str_replace("''","'",$valorCampos);
				$strSQL = " INSERT INTO ".$tabla."(".$campos.") VALUES(".$valorCampos.")";
				if($this->objSQL->dbQuery($strSQL)){
					$id = $this->objSQL->dbLastInsertId();
					if($id){
						return $id;	
					}
					return true;
				}
			}
		}
		return false;
	}

	/**
	 *@param string $set secion set del update. campo=''string'',campo2=numero
	 *@param int $id del registro a modificar
	 *@param string $campoValidador
	 *@return bool
	 */
	 
	function modificarRegistro($set, $id, $campoValidador = NULL, $tabla = NULL, $prefijo = NULL){
		$tabla = $tabla?$tabla:$this->tabla;
		$prefijo = $prefijo?$prefijo:$this->prefijo;
		$procesarUpdate = true;
		
		if($set && $id){
			
			if(!empty($campoValidador)){
				$duplicado = $this->obtenerRegistros(0,'getAllReg',$campoValidador,$id,true);
				if($duplicado){
					$procesarUpdate = false;
				}
			}
			
			if($procesarUpdate){
				$set = str_replace("''","'",$set);
				$strSQL = " UPDATE ".$tabla." SET ".$set." WHERE ".$prefijo."_id= ".(int)$id;
				var_Dump($strSQL);exit;
				if($this->objSQL->dbQuery($strSQL)){
					return 1;
				}
				else{
					return 2;
				}
			}
		}
		return 0;
	}

	function obtenerRegistros($id=0, $filtro="" ,$campoValidador='' ,$idValidador=0, $soloCantidad=false){
		$strSQL = " SELECT * ";
		$strSQL.= " FROM ".$this->tabla." WITH (NOLOCK) ";
		$strSQL.= " WHERE ".$this->prefijo."_borrado=0 ";
		if(!empty($campoValidador)){
			$strSQL.= " AND ".$campoValidador;
		}
		if(!empty($idValidador)){
			$strSQL.= " AND ".$this->prefijo."_id != ".$idValidador;
		}
		if($id){
			$strSQL.= " AND ".$this->prefijo."_id = ".$id;	
		}
		
		
		//$strSQL = "EXEC {$this->pa_consulta} {$id},'{$filtro}','{$campoValidador}',{$idValidador}";
		$objRegistros = $this->objSQL->dbQuery($strSQL);
		if (!$soloCantidad){
			$arrRegistros = $this->objSQL->dbGetAllRows($objRegistros,3);
			return $arrRegistros;
		}
		else{
			$intRows = $this->objSQL->dbNumRows($objRegistros);
			return $intRows;
		}
	}

	function obtenerTotalRegistros(){
		$strSQL = " SELECT count(".$this->prefijo."_id) ";
		$strSQL.= " FROM ".$this->tabla." WITH (NOLOCK) ";
		$strSQL.= " WHERE ".$this->prefijo."_borrado=0 ";
		$objProductos = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetRow($objProductos, 0, 2);
		return $objRow[0];
	}
	
	function generarLog($idTabla,$idRel,$log){
		
		$strSQL = " SELECT DATEADD(hour,server,GETDATE()) FROM zonaHoraria(NULL,".(int)$_SESSION['idUsuario'].") ";
		$objSQL = $this->objSQL->dbQuery($strSQL);
		$objRow = $this->objSQL->dbGetRow($objSQL, 0, 2);
		
		$params = array(
			'sl_st_id'=>(int)$idTabla
			,'sl_rel_id'=>(int)$idRel
			,'sl_descripcion'=>trim($log)
			,'sl_us_id'=>(int)$_SESSION['idUsuario']
			,'sl_us_nombre'=>$_SESSION['us_nombre'].' '.$_SESSION['us_apellido']
			,'sl_fecha_alta'=>$objRow[0]
		);
		return $this->objSQL->dbQueryInsert($params, 'tbl_system_log');
	}
}