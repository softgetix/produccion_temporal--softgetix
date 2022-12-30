<?php
class Informes {

    function Informes($objSQLServer) {
        $this->objSQL = $objSQLServer;
    }
	
	function vistaListadoMoviles($arrMovilesUsuario, $etiquetaTodos = true){
		global $lang;
		?>
		<fieldset id="listado-moviles">
            <?php if($etiquetaTodos){?>
            <ul>
                <li>
                    <label for="group-all-movil"><?=$lang->system->todos_moviles?>
                        <input type="checkbox" id="group-all-movil" onchange="javascript:checkGroup('all-movil')" class="float_r" />
                    </label>
                </li>
            </ul>
            <?php }?>
            <?php $idGrupoAnterior = $txtGrupoAnterior = $txtGrupo = $arbol = NULL;
            foreach($arrMovilesUsuario as $item){
                
                if($item['gm_id'] != $idGrupoAnterior){
                    
                    if($txtGrupo != NULL){
                        $arbol.= $this->getArbolMoviles($idGrupoAnterior, $arbol, $txtGrupoAnterior, $txtGrupo, $etiquetaTodos);
                    }
                    
                    $txtGrupoAnterior = $item['gm_nombre'];
                    $idGrupoAnterior = $item['gm_id'];
                    $txtGrupo = NULL;
                }
                
				$txtGrupo.= '<li>';
                $txtGrupo.= '	<label for="movil-'.$item['mo_id'].'">'.encode((strlen($item['movil'])>28)?(substr($item['movil'],0,25).'...'):$item['movil']);
                if($etiquetaTodos){
				$txtGrupo.= '	<input type="checkbox" id="movil-'.$item['mo_id'].'" class="check-grupo-'.(int)$item['gm_id'].' float_r" name="arrMovil[]" value="'.$item['mo_id'].'" />';
				}
				else{
				$txtGrupo.= '	<input type="radio" id="movil-'.$item['mo_id'].'" name="idMovil" value="'.$item['mo_id'].'" />';		
				}
				$txtGrupo.= '	</label>';
                $txtGrupo.= '</li>';
            }
            if($txtGrupo != NULL){
                $arbol.= $this->getArbolMoviles($idGrupoAnterior, $arbol, $txtGrupoAnterior, $txtGrupo, $etiquetaTodos);
            }
            ?>
            <?=$arbol?>
        </fieldset>	
	<?php }

	function getArbolMoviles($idGrupo, $arbol, $txtGrupoAnterior, $txtGrupo, $etiquetaTodos){
		global $lang;
		$idGrupo = $idGrupo?$idGrupo:0;
		$txtGrupoAnterior = $txtGrupoAnterior?$txtGrupoAnterior:$lang->system->sin_grupo;
		$arbol = '<ul class="clear">';
		$arbol.= '	<li class="clear">';
		$arbol.= '		<div class="group">';
		$arbol.= '			<a href="javascript:deployGroup('.$idGrupo.');" class="float_l" id="deploy-'.$idGrupo.'" style="display:inline-block;">'.((strlen($txtGrupoAnterior)>21)?(substr($txtGrupoAnterior,0,18).'...'):$txtGrupoAnterior).'</a>';
		if($etiquetaTodos){
		$arbol.= '	    	<input type="checkbox" id="group-'.$idGrupo.'" onchange="javascript:checkGroup('.$idGrupo.')"  class="float_r"/>';
		}
		else{
		//$arbol.= '	    	<span style="width:23px; height:13px; margin: 6px 5px 0 0; display:inline-block;">&nbsp;</span>';		
		}
		$arbol.= '		</div>';
		$arbol.= '	    <ul id="ul-group-'.$idGrupo.'" style="display:none">';
		$arbol.= 		$txtGrupo;
		$arbol.= '	    </ul>';
		$arbol.= '	</li>';
		$arbol.= '</ul>';
		return $arbol;
	}
	
	function vistaListadoEventos($arrEventos){
		global $lang;
		?>
		<fieldset id="listado-eventos">
            <ul>
                <li>
                    <label for="group-all-event"><?=$lang->system->todos_eventos?>
                        <input type="checkbox" id="group-all-event" onchange="javascript:checkGroup('all-event')" />
                    </label>
                    <ul>
                        <?php foreach($arrEventos as $item){?>
                        <li>
                            <label for="evento-<?=$item['id']?>"><?=(strlen($item['dato'])>28)?(substr($item['dato'],0,25).'...'):$item['dato']?>
                                <input type="checkbox" id="evento-<?=$item['id']?>" name="arrEvento[]" value="<?=$item['id']?>" />
                            </label>
                        </li>
                    <?php }?>
                    </ul>
                </li>
            </ul>    
        </fieldset> 
		<?php
	}
	
	function obtenerReporteKmsRecorridos($fechaInicio, $fechaFin, $arrMoviles){
        
		$valido_hasta = date('Y-m-d H:i:s',strtotime('+3 minute', strtotime(date('Y-m-d H:i:s'))));
		$fechaInicio = date('Y-m-d',strtotime($fechaInicio)).' 00:00';
        $fechaFin = date('Y-m-d',strtotime($fechaFin)).' 23:59';
		$strMoviles = $coma = '';
		foreach($arrMoviles as $idMovil){
			$strMoviles.= $coma.(int)$idMovil;
			$coma = ', ';
		}
		
		if($strMoviles){
			$query = "EXEC informeKMsRecorridos '$fechaInicio', '$fechaFin', '$strMoviles'";
			$result = $this->objSQL->dbQuery($query);
			$objKms = $this->objSQL->dbGetAllRows($result, 3);
			if($objKms){
				return $objKms;
			}	
		}
		return false;
	}
	
	function obtenerReporteViajes($fechaInicio, $fechaFin, $arrMoviles, $idUsuario) {
		//$query = "EXEC informeMarchaParada '$arrMoviles', '$fechaInicio', '$fechaFin', '$idUsuario'";
		$query = "EXEC informeMarchaParada '$fechaInicio', '$fechaFin', '$arrMoviles'";
		$result = $this->objSQL->dbQuery($query);
		$objViajes = $this->objSQL->dbGetAllRows($result, 3);
		
		if(isset($objViajes[0]['Resultado'])){
			 return $objViajes[0]['Resultado'];
		}
		elseif($objViajes){
			return $objViajes;
		}
		return false;
	}
	
	function obtenerReporteMailsEnviados($idUsuario, $idMoviles, $fechaInicio, $fechaFin){
		$query = "EXEC informeMailsEnviadosAlertas	'$idUsuario', '1', '$idMoviles', '$fechaInicio', '$fechaFin'";	
		$result = $this->objSQL->dbQuery($query);
		$objMails = $this->objSQL->dbGetAllRows($result, 3);
		if($objMails){
        	return $objMails;
        }
        return false;
	}
}
