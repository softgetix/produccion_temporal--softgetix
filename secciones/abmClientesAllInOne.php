<?php $seccion = 'abmClientes';?>
<style> 
	#main{ margin-left:5px; margin-right:5px; } 
	a.agregarZona{ text-decoration:none;}
	a.agregarZona span{background:url(imagenes/mas_.png) left 2px no-repeat; line-height:12px; padding-left:18px; color:#333; }
</style>
<div id="main">
	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   	<div class="mainBoxLICabezera">
   		<h1><?=$lang->system->abm_clientes?></h1>
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
        <input name="hidId" id="hidId" type="hidden" value="<?php if (isset($id)) { echo $id; } ?>" />
        <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
        <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<?php
		switch ($operacion){
			case 'listar':
			require_once 'includes/botoneraABMs.php';			
		?>
	</div><!-- fin mainBoxLICabezera -->
	<div id="mainBoxLI">
		<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
			<tr class="titulo">
				<td width="6%" align="center"><?=$lang->system->nro_entidad?></td>
				<td width="10%" align="center"><?=$lang->system->email?></td>
				<td width="6%" align="center"><?=$lang->system->codigo_activacion?></td>
				<td width="6%" align="center"><?=$lang->system->cuenta_activa?></td>
				<td width="6%" align="center"><?=$lang->system->cant_moviles_vs_cant_licencias?></td>
				<td width="6%" align="center"><?=$lang->system->terminos_condiciones?></td>
				<td width="10%" align="center"><?=$lang->system->zonas_panico?></td>				
				<td width="8%" align="center"><?=$lang->system->reset_clave?></td>
				<td width="6%" align="center"><?=$lang->system->probador_panico?></td>					
				<td width="4%" align="center"><?=$lang->system->habilitado?></td>
			</tr>
            
	<?php if($arrEntidades){
		for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
		 	$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
		   	/*switch($arrEntidades[$i]['cl_habilitado']){
				case 0:$imagen="cruz.png";break;	
				case 1:$imagen="resp_ok.png";break;
				case 2:$imagen="alerta.png";break;
			}*/?>
			<tr class="<?=$class?>">
				<input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['cl_id']?>" value="<?=$arrEntidades[$i]['cl_id']?>" style="display:none"/>
				<td align="center">									
					<?php if($arrEntidades[$i]['cl_borrado']){?>
                    	<?=$arrEntidades[$i]['cl_razonSocial']?>	
                    <?php }
					else{?>
                    <a href="javascript:mostrarPopup('boot.php?c=abmAllInOne&action=popup&idCliente=<?=$arrEntidades[$i]['cl_id']?>',600,360)">
                    	<?=$arrEntidades[$i]['cl_razonSocial']?>									
                    </a>
                    <?php }?>
				</td>
				<td align="center"><?=$arrEntidades[$i]['cl_email']?></td>
				<td align="center"><?=generarCodigoValidacion(escapear_string($arrEntidades[$i]['cl_email']))?></td>
				<td align="center"><?=($arrEntidades[$i]['cantidadUsuarios']>0)?$lang->system->si:$lang->system->no?></td>	
				<td align="center">
					<a href="javascript:mostrarPopup('boot.php?c=abmAllInOneMoviles&action=popup&idCliente=<?=$arrEntidades[$i]['cl_id']?>',740,450)"><?=(int)$arrEntidades[$i]['cantidadMoviles']?></a>
                    <?='/'.(int)$arrEntidades[$i]['us_cant_licencias']?>
                </td>
				<td align="center"><?=($arrEntidades[$i]['us_ultimo_acceso'] != NULL)?$lang->system->si:$lang->system->no?></td>
				<td align="center">
					<?php if($arrEntidades[$i]['cantidadUsuarios']>0){
                    	$nroPanico = 0;
						foreach($arrEntidades[$i]['zonasPanico'] as $item){?>
							<p id="panic_<?=$item['nro_panico']?>_<?=$arrEntidades[$i]['us_id']?>">									
								<a href="javascript:mostrarPopup('boot.php?c=abmReferencias&action=popupMod&panico=<?=$item['nro_panico']?>&cli=<?=$arrEntidades[$i]['cl_id']?>&usr=<?=$arrEntidades[$i]['us_id']?>&refer=<?=$item['id_panico']?>',894,520)">
									<?=encode($item['desc_panico'])?>
								</a>
							</p>
                        	<?php $nroPanico++;
						}?>
					 	
                        <p id="panic_<?=$nroPanico+1?>_<?=$arrEntidades[$i]['us_id']?>">
                        	<a href="javascript:mostrarPopup('boot.php?c=abmReferencias&action=popup&panico=<?=$nroPanico+1?>&cli=<?=$arrEntidades[$i]['cl_id']?>&usr=<?=$arrEntidades[$i]['us_id']?>',915,520)" class="agregarZona">
								<span><?=$lang->system->agregar_zona?></span>
							</a>									
						</p>
					<?php }
					else{ ?>
						<p style="color:#666;"><?=$lang->system->msg_agregar_zonas?></p>
					<?php } ?>
				</td>	
				<td align="center">
                	<?php $ide = 're-email-'.$i?>
                    <a href="javascript:SendMailResetPass('<?=$arrEntidades[$i]['cl_email']?>','<?=$_SESSION['DIRCONFIG']?>','<?=$ide?>')" title="<?=$lang->system->title_cambio_clave?>" id="<?=$ide?>"><img src="imagenes/send_mail.png" /></a>
                </td>
					
				<?php if(count($arrEntidades[$i]['zonasPanico'])>0 && $arrEntidades[$i]['zonasPanico'] != NULL){?>					         
					<td align="center">
						<p id="probador_<?=$arrEntidades[$i]['us_id']?>">
							<a href="boot.php?c=abmProbadorDePanico&identificador_cliente=<?=$arrEntidades[$i]['cl_id']?>">
								<?=$lang->system->probar_panico?>
							</a>
						</p>
					</td>								 
				<?php } 
				else{?>
					<td align="center">									
						<p id="probador_<?=$arrEntidades[$i]['us_id']?>">	
							<span style="color:#666;"><?=$lang->system->msg_definir_zona?></span>
						</p>	
					</td>
				<?php }?>
					
              	<td align="center">
                	<?php if($arrEntidades[$i]['cl_borrado']){?>
                        <a  href="javascript:;" onclick="javascript:enviarModificacion('habilitarAllInOne',<?=$arrEntidades[$i]['cl_id']?>)" style="text-decoration:underline" title='Habilitar el Cliente'>
                            <img src="imagenes/cruz.png" />
                        </a>
                    <?php }
					else{?>
                    	<a href="javascript:;" onclick="javascript:enviarModificacion('bajaAllInOne',<?=$arrEntidades[$i]['cl_id']?>)" style="text-decoration:underline" title='Baja Cliente'>
                            <img src="imagenes/resp_ok.png"/>
                        </a>
					<?php }?>
                </td>
					
				<?php /* ?>							 
					<td align="center">							 							 
					<?php $nopermitido = 0;
						$fecha_permitida = strtotime('+15 minute', strtotime($arrEntidades[$i]["us_acceso_fallido"])); //us_ultimo_acceso
						if($fecha_permitida > strtotime("now")){ $nopermitido=1; };
						if(($arrEntidades[$i]["us_cant_fallido"]>=5)&&($nopermitido==1)){
							echo "<span style='color:#C06161'>Usuario Bloqueado</span>";
						}
						else{
							if($arrEntidades[$i]["us_expira"]!=''){
								if((strtotime($arrEntidades[$i]["us_expira"]))>(strtotime("now"))){?>										
									<a href="javascript: auto_ingresar(<?=$arrEntidades[$i]['us_id']?>,'<?=$_SESSION["pass"];?>')"><img src="imagenes/acceso_directo.png" /></a>
								<?php }
								else{
									echo "<span style='color:#666666'>Expir&oacute; Cuenta</span>";
								}
							}
							else{?>										
								<a href="javascript: auto_ingresar(<?=$arrEntidades[$i]['us_id']?>,'<?=$_SESSION["pass"];?>')"><img src="imagenes/acceso_directo.png" /></a>										
							<?php }
						}?>
					</td>	
				<?php }*/ ?>
			</tr>
		<?php }
		}
		else{?>
			<tr class="filaPar">
				<td colspan="11" align="center" style="padding-top:40px;padding-bottom:40px;"><i><?=$lang->message->msj_utilice_buscador?></i></td>
			</tr>
		<?php }?>
		</table>
	</div><!-- fin. #mainBoxLI -->
<?php
	   	break;
		}?>
	</form>
</div>