<?php 
switch($operacion){
	case 'alta':
	case 'modificar':?>
        <input type="hidden" name="idUser" id="idUser" value="<?=trim(intval($_POST['hidId']))?>" />
        <div id="botonesABM" style="width:100%;">
        	<a id="botonVolver" href="boot.php?c=<?=$seccion?>&solapa=<?=$solapa?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
        </div>
		<span class="clear"></span>
        <br />
        <?php require_once 'includes/interfazGraficaABMs.php';?>
        <!-- Asignar Moviles -->
        <br />
        <fieldset>
            <legend><?=$lang->system->asignar_moviles?></legend>
            <table width="100%">
                <tr>
                    <td valign="middle" height="20" align="right" width="20%">Fecha Expiraci&oacute;n&nbsp;&nbsp;</td>
                    <td style="text-align:left;" width="80%"><input name="txtExpiracion" id="txtExpiracion" value="<?=isset($_POST['txtExpiracion'])?$_POST['txtExpiracion']:(!empty($arrEntidades[0]['us_expira'])?date('d-m-Y',strtotime($arrEntidades[0]['us_expira'])):null)?>" style="width:300px;"  class="date" size="50" type="text"></td>
                    <td></td>
                </tr>
            </table>
        </fieldset>    
        <br />
        <fieldset>
            <legend><?=$lang->system->asignar_moviles?></legend>
            <table>
				<?php if(is_array($arrClientes)){?>
				<tr>
					<td><?=$lang->system->cliente?></td>
					<td>
					<input type="hidden" name="cmbDistribuidor" id="cmbDistribuidor" value="<?=$_SESSION["idEmpresa"]?>" />
					<select name="cmbClientes" id="cmbClientes" style="width:304px;" onChange='simple_ajax("ajaxObtenerMovilesCliente.php?idCliente=" + this.value + "&idUser=<?=trim(intval($_POST['hidId']))?>&idDistribuidor=" + document.getElementById("cmbDistribuidor").value);'>
						<option value="0">&lt;&lt;&nbsp;<?=$lang->system->todos?>&nbsp;&gt;&gt;</option>
						<?php foreach($arrClientes as $cliente){?>
							<option value="<?=$cliente['id']?>"  <?=($arrDatos['idCliente']==$cliente['id'])?'selected':''?> ><?=encode($cliente['dato'])?></option>
						<?php }?>
					</select>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="2">
					<table>
						<tr>
                            <td style="text-align:left !important;"><?=$lang->system->lista_moviles?></td>
                            <td>&nbsp;</td>
                            <td style="text-align:left !important;"><?=$lang->system->moviles_asignados?></td>
						</tr>
						<tr>
							<td> 
								<select size="20" name="cmbMoviles" multiple="multiple" id="cmbMoviles"  style="width: 300px;">
								<?php if($arrMoviles){
									for($i=0;$i<count($arrMoviles) && $arrMoviles;$i++){ ?>
                                        <option value="<?=$arrMoviles[$i]["id"]?>"><?=encode($arrMoviles[$i]["dato"])?></option>
                                    <?php }
								}?>
								</select>
							</td>
							<td  align="center" style="vertical-align: middle">
								<input name="B1" class="texto" onclick="javaScript:Move(document.getElementById('cmbMoviles'), document.getElementById('cmbMovilesAsignados'));" value="&gt;&gt;" type="button" /><br><br>
								<input name="B2" class="texto" onclick="javaScript:Move(document.getElementById('cmbMovilesAsignados'), document.getElementById('cmbMoviles'));" value="&lt;&lt;" type="button" />
							</td>
							<td>
								<select size="20" name="cmbMovilesAsignados[]" multiple="multiple" id="cmbMovilesAsignados" style="width: 300px;">
								<?php for($i=0;$i<count($arrMovilesUsuario) && $arrMovilesUsuario;$i++){ ?>
									<option value="<?=$arrMovilesUsuario[$i]["id"]?>"><?=encode($arrMovilesUsuario[$i]["dato"])?></option>
								<?php }?>
								</select>
							</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
		</fieldset>
        <!-- -->
        <br />
        <center>
        	<a href="javascript:;" onclick="javascript: $('#cmbMovilesAsignados option').attr('selected', 'selected'); enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px;"><?=$lang->botonera->guardar?></a>
       	</center>
        <br />
        <script type="text/javascript">
		if(document.getElementById('cmbCliente').value > 0){		
			simple_ajax('ajaxObtenerPerfiles.php?idDistribuidor='+document.getElementById('cmbCliente').value+'&sel=<?=$arrEntidades[0]['us_pe_id']?>&p=1')
            simple_ajax("ajaxObtenerMovilesCliente.php?idCliente=0&idUser=<?=trim(intval($_POST['hidId']))?>&idDistribuidor="+document.getElementById('cmbCliente').value);
		}
		//-----
		</script>
	<?php break;
	default:?>
    <?php 
	$lang->botonera->agregar_nuevo = $lang->botonera->agregar_usuarios?$lang->botonera->agregar_usuarios:$lang->botonera->agregar_nuevo;
	$tipoBotonera = tienePerfil(array(5,9,13,19))?'LI-NewItem-Export':'LI-NewItem'; 
	include('includes/botoneraABMs.php');
	?>
    <table width="100%" height="100%">
        <thead>
            <tr>
                <td><span class="campo1"><?=$lang->system->nombre_apellido?></span></td>
                <td><span class="campo1"><?=$lang->system->usuario?></span></td>
                <td><span class="campo1"><?=$lang->system->perfil?></span></td>
                <td><span class="campo1"><?=$lang->system->empresa?></span></td>
                <td><center><span class="campo1"><?=$lang->system->estado_cuenta?></span></center></td>
                <?php if(tienePerfil(array(5,9,13,19))){?>
                <td><center><span class="campo1"><?=$lang->system->ingresar_como?></span></center></td>
                <?php }?>
                <td class="td-last"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>
            </tr>
        </thead>
        <tbody>
        <?php if($arrEntidades){
            foreach($arrEntidades as $i => $user){
                $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>">
                    <?php if($_SESSION['idTipoEmpresa'] == 3  && $user['cl_tipo'] != 1){?>
                        <td><?=trim($user['us_nombre'].' '.$user['us_apellido'])?></td>
                    <?php }
                    else{?>
                        <td>
                        	<input type="hidden" name="chkId[]" id="chk_<?=$user['us_id']?>" value="<?=$user['us_id']?>"/>
                            <a style="text-decoration:underline" href="javascript:enviarModificacion('modificar',<?=$user['us_id']?>)">
                                <?=trim($user['us_nombre'].' '.$user['us_apellido'])?>
                            </a>
                        </td>
                    <?php }?>
                    
                    <td><?=$user['us_nombreUsuario']?></td>
                    <td>
                    <?php
                        $auxperfil = trim($user['pe_nombre']);
                        echo isset($lang->perfiles->$auxperfil)?$lang->perfiles->$auxperfil:$auxperfil;?>
                    </td>
                    <td><?=trim($user['cl_razonSocial'])?></td>
                    <td>
                        <center>
                        <?php  $nopermitido = 0; $btn_ingresar_como = false;
                           $fecha_permitida = strtotime('+15 minute', strtotime($user["us_acceso_fallido"])); //us_ultimo_acceso
                            if($fecha_permitida > strtotime("now")){$nopermitido=1;};
                            
                            if(($user["us_cant_fallido"]>=5)&&($nopermitido==1)){
                                echo '<span style="color:#C06161">'.$lang->system->usuario_bloqueado.'</span>';
                            }
                            elseif($user["us_expira"]!=''){
                                if((strtotime($user["us_expira"]))>(strtotime("now"))){
									echo $lang->system->usuario_activo;
									$btn_ingresar_como = true;
								}
                                else{
                                    echo '<span style="color:#666666">'.$lang->system->expiro_cuenta.'</span>';
                                }
                            }
                            else{
                                echo $lang->system->usuario_activo;
								$btn_ingresar_como = true;
                            }
                        ?>
                        </center>
                    </td>
                    <?php if(tienePerfil(array(5,9,13,19))){?>
                    <td>
                    	<center>
						<?php if($btn_ingresar_como){?>
                           <a href="javascript: auto_ingresar(<?=$user['us_id']?>,'<?=$_SESSION["pass"];?>')"><img src="imagenes/acceso_directo.png" /></a>
						<?php }?>
                        </center>
                    </td>
                    <?php }?>
                    <td class="no_padding td-last">
                    	<?php if($_SESSION['idTipoEmpresa'] == 3  && $user['cl_tipo'] != 1){}
						else{?>
                    	<center><a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$user['us_id']?>)"><img src="imagenes/cerrar.png" /></a></center>
                		<?php }?>
                    </td>
                </tr>
            <?php } 
				$cantRegistros = count($arrEntidades);
				include('secciones/footer_LI.php');
            }
            else{?>
                <tr class="tr-last">
                    <td class="td-last" colspan="7"><center><?=$lang->message->sin_resultados?></center></td>
                </tr>
            <?php }?>
        </tbody>
    </table>
	<?php break;
}?>

<?php if($operacion == 'modificar'){?>
	<script type="text/javascript">
		if(!$("#chkCambiarPass").is(':checked')){
			$('#txtPass, #txtRepetirPass').attr('disabled','disabled');
		}
	</script>
<?php }
	elseif($operacion == 'alta'){?>
    <script type="text/javascript">
		$("#chkCambiarPass").attr('checked',true);
		$("#chkCambiarPass").attr('disabled',true);
	</script>	
<?php }?>
