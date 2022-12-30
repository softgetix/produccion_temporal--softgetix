<?php
require_once 'clases/clsNomenclador.php';
class NotificacionAlertas extends Nomenclador{
    var $objSQLServer;
    protected $ultimoId = 0;

    function __construct($objSQLServer){
        $this->objSQL = $objSQLServer;
        return TRUE;
    }

    function obtenerAlertasAgrupadas($alertas){
        $res = $alertas;

        if(is_array($res)){
            $res = array_reverse($res); // Invierto el array
        }

        if(is_array($res)){
            $referencias = array(); //Guardo el id de cada combinacion.
            $maxId = 0;

            foreach($res as $id => $item){
                if ( $item['id'] > $maxId){
                    $maxId = $item['id'];
                }

                $movil = $item['matricula'];
                $id_reporte = $item['id_reporte'];
                $id_alerta = $item['alerta_id'];
				$id_referencia = (int)$item['re_id'];	

                $res[$id]['ocurrencias'] = 1;
                $res[$id]['ids'] = $res[$id]['id'];
                
                $res[$id]['arr_ids'] = array();
                $res[$id]['arr_ids'][] = $res[$id]['id'];

                $hoy = date("Y-m-d");
                
                $fecha = date("Y-m-d", strtotime($item['generado']));
                if ($fecha == $hoy) {
                    $res[$id]['generado'] = "Hoy, " . date("H:i", strtotime($item['generado']));
                }
                
                $fecha = date("Y-m-d", strtotime($item['recibido']));
                if ($fecha == $hoy) {
                    $res[$id]['recibido'] = "Hoy, " . date("H:i", strtotime($item['recibido']));
                }

                if (isset($referencias[$movil][$id_reporte][$id_alerta][$id_referencia])){
                    $idanterior = $referencias[$movil][$id_reporte][$id_alerta][$id_referencia];
                    $res[$idanterior]['ocurrencias']++;
                    $res[$idanterior]['ids'] .= "," . $res[$id]['id'];
                    $res[$idanterior]['arr_ids'][] = $res[$id]['id'];
                    unset($res[$id]);
                }
                else{
                    $referencias[$movil][$id_reporte][$id_alerta][$id_referencia] = $id;
                }
            }

            $this->ultimoId = $maxId;

            foreach ($res as $id => $item){
                if ( $res[$id]['nomenclado'] == null){
                    $res[$id]['nomenclado'] = $this->obtenerNomenclados($item['latitud'], $item['longitud']);
                }
            }
            
            $final_res = array();
            foreach ($res as $id => $data){
                $final_res[ 'alert_id_'.$data['id'] ] = $data;
                unset($data);
            }
        }
		else{
            $res = false;
            $final_res = false;
        }
        
        return $final_res;
    }
    
   function obtenerAlertasDelAgenteNoConfirmadas($ultimoId = 0, $cantidadSelect = 100, $returnCantidad = false){
		$seteado = false;
		
        if($ultimoId == 0){
            $ultimoId = "( SELECT IDENT_CURRENT('tbl_mail_enviado') ) - 5000";
        }

		if(!$returnCantidad){
		require_once 'clases/clsIdiomas.php';
		$objIdioma = new Idioma();
		$eventos = $objIdioma->getEventos($_SESSION['idioma']);
		
		$strSQL = " DECLARE @temp_eventos TABLE (id INT,evento VARCHAR(50))";
		foreach($eventos->children() as $k => $ev){
			$idEv = explode('_',$k);
			if($idEv[1]){
				$strSQL.= " INSERT INTO @temp_eventos VALUES(".(int)$idEv[1].", '".trim($ev)."') ";
			}
		}
			
		$strSQL.= "
            SELECT TOP {$cantidadSelect}
                ME.me_id AS id,
                MO.mo_matricula as matricula, 
                ME.me_dr_id as id_reporte,
                ISNULL(evento,'".$eventos->default->__toString()." ('+CONVERT(VARCHAR,ME.me_dr_id)+')') as descripcion,
				ME.me_rumbo as sentido,
                ME.me_velocidad as velocidad,
                ME.me_fechaRecibido as recibido,
                ME.me_fechaGPS as generado,
                ME.me_latitud as latitud,
                ME.me_longitud as longitud,
                MO.mo_id as movilid,
                ME.me_nomenclado as nomenclado,
                RE.re_id as re_id,
				RE.re_nombre as referencia,
                CL.cl_razonSocial as nombreEmpresa,
				MO.mo_otros as tel ";
		}
		else{
			$strSQL = "SELECT COUNT(me_id) AS total_alertas_noconf ";
		}
        
		$strSQL.= " FROM tbl_mail_enviado ME WITH(NOLOCK)
                INNER JOIN tbl_moviles MO  WITH(NOLOCK) ON (MO.mo_id = ME.me_mo_id AND mo_borrado = 0)
                INNER JOIN tbl_clientes CL WITH(NOLOCK) ON (CL.cl_id = MO.mo_id_cliente_facturar AND cl_borrado = 0)
			";
		
		if(!$returnCantidad){
			$strSQL.= " 
				LEFT JOIN @temp_eventos ON ME.me_dr_id = id
				LEFT  JOIN tbl_referencias RE WITH(NOLOCK) ON (RE.re_id = ME.me_ar_id AND re_borrado = 0) ";
		}
				
        $strSQL.= "       
            WHERE
				ME.me_al_id IS NULL
                AND CL.cl_id_distribuidor = ".(int)$_SESSION['idEmpresa']."
                AND ME.me_id > {$ultimoId}
                AND ME.me_atendido_por = 0
		";
		
		if(tienePerfil(17)){
			$strSQL.= " AND ME.me_dr_id = 84 ";
		}
		else{
			$strSQL.= " AND ME.me_dr_id != 84 ";
		}
		
		if(!$returnCantidad){
			$strSQL.= " ORDER BY ME.me_id DESC";
		}
		
		$objRes = $this->objSQL->dbQuery($strSQL);
		$arrAlertas = $this->objSQL->dbGetAllRows($objRes);
		return $arrAlertas;
	}
	
	function obtenerUltimoId(){
		return $this->ultimoId;
	}

    function confirmarAlerta($id, $idUsuario, $idMotivoConfirmacion = 0, $observacion = NULL){
        /*$strSQL = "
            UPDATE tbl_mail_enviado SET 
                me_atendido_por = {$idUsuario},
                me_mc_id = {$idMotivoConfirmacion}
            WHERE
                me_id = {$id}
                AND me_atendido_por = 0";
        */
        $strSQL = "EXEC confirmacion_Alertas {$id},{$idUsuario},{$idMotivoConfirmacion},'{$observacion}'";
        $objRes = $this->objSQL->dbQuery($strSQL);

        return $objRes;
    }
}
