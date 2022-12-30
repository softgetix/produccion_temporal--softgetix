<div id="main" class="sinColIzq">
	<div class="solapas clear"><!-- gum-->
    	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" enctype="multipart/form-data">
          	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
			<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		    <input name="hidId" id="hidId" type="hidden" value="<?=(int)$id?>" />    
          
            <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
            <input name="hidZoom" id="hidZoom" type="hidden" value="12" />
            <input name="hidNombreDeteccionCircular" id="hidNombreDeteccionCircular" type="hidden" value="Radio de Ingreso" />
            <input name="hidNombreDeteccionOtros" id="hidNombreDeteccionOtros" type="hidden" value="Desvio M&aacute;ximo" />
            <input type="hidden" name="HidPopUp" id="HidPopUp" value="<?=$popup?'popup':(isset($_GET['action'])?'popup':NULL)?>" />
            
            
            <div style="height:100%" class="contenido clear"> 
			<?php switch($operacion){
				case 'alta':
				case 'modificar':
						
					$lt = isset($_REQUEST["lt"])?$_REQUEST["lt"]:$_SESSION['lat'];
					$lg = isset($_REQUEST["lg"])?$_REQUEST["lg"]:$_SESSION['lng'];
				
					switch($operacion){
						case 'alta':
							$strPuntos = $lt.', '.$lg.';';
							
							$idMovil = isset($_POST["idMovil"])  ? $_POST["idMovil"] : "";
							$arrEntidades[0]['re_radioIngreso'] = isset($_POST['cmbRadioIngreso']) ? $_POST['cmbRadioIngreso'] : 0;
							$arrEntidades[0]['re_radioEgreso'] = 500;//= isset($_POST['cmbRadioEgreso']) ? $_POST['cmbRadioEgreso'] : 0;
							$arrEntidades[0]['re_tr_id'] = isset($_POST['cmbTipoReferencia']) ? $_POST['cmbTipoReferencia'] : 0;
							if ($lt != 0 && is_numeric($lg)) {
								$_POST["hidPuntos"] = '(' . $lt . ', ' . $lg . ');';
								$arrEntidades[0]['re_tr_id'] = 1;
							}
						break;
						case 'modificar':
							for ($i = 0;$i < count($arrPuntos);$i++){
								$strPuntos .= "(".$arrPuntos[$i]["rc_latitud"].", ".$arrPuntos[$i]["rc_longitud"].");";	
							}
						break;
					}
						
					?>
                	<input name="hidPuntos" id="hidPuntos" type="hidden" value=";<?=isset($_POST["hidPuntos"]) ? $_POST["hidPuntos"] : $strPuntos?>" />
					<script language="javascript">
                        var imgW = 24;
                        var imgH = 35;
                    </script>
                    <?php
                    $disabled = ($arrEntidades[0]['re_tr_id'] == 2)?'disabled':'';
                    if (!($arrEntidades[0]['re_radioIngreso'])) $arrEntidades[0]['re_radioIngreso'] = 100;
    
                    if(tienePerfil(17)){
                        require_once "referencias/edit_referencia_adt.php";
					}
					elseif(tienePerfil(9)){
                        require_once "referencias/edit_referencia_paquetizado.php";
					}
					elseif(tienePerfil(27)){
						require_once "referencias/edit_referencia_forza.php";
					}
                    else{
                        require_once "referencias/edit_referencia_default.php";
                    }
				break;
				case 'listarstock':
					require_once "forza/listar_stock.php";
				break;
				case 'listardetallestock':
					require_once "forza/listar_detalle_stock.php";
				break;
				default:
					require_once "referencias/{$operacion}.php";
				break;
			}?>
            	<span class="clear"></span>
			</div><!-- fin. contenido--> 
        </form>  
	</div> <!-- fin. solapas-->   
</div>