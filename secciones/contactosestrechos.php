<div id="main" class="sinColIzq">
	<div class="solapas gum clear">
    	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
          	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
            <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
            <input name="hidId" id="hidId" type="hidden" value="" />
            
            <div style="height:100%" class="contenido flaps clear"> 
            <?php $tipoBotonera = 'LI';
                include('includes/botoneraABMs.php');?>    
                <span class="clear" style="clear:both; margin-bottom:5px"></span>
                
                <table width="100%" height="100%">
                    <thead>
                        <tr>
                        <td><span class="campo1"><?=$lang->system->matricula?></span></td>
                        <td><span class="campo1"><?=$lang->system->nombre?></span></td>
                        <td><span class="campo1">Documento o Legajo</span></td>
                        <td><span class="campo1"><?=$lang->system->cliente?></span></td>
                        <td class="td-last"><center><span class="campo1">Contactos Estechos</span></center></td>    
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result){
                            foreach($result as $i => $item) {
                                $class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
                                <tr class="<?=$class?> <?=((count($result)-1) == $i)?'tr-last':''?>">
                                <td><?=$item['mo_matricula']?></td>
                                <td><span><?=$item['mo_otros']?></span></td>
                                <td><?=$item['mo_identificador']?></td>
                                <td><?=$item['cl_razonSocial']?></td>
                                <td class="td-last">
                                    <center>
                                    <a id="botonGuardar" class="button_xls exp_excel" onclick="enviar('exportar_xls','<?=$item['un_mostrarComo']?>');" style="margin-bottom:10px; display:inline;" href="javascript:;">Exportar registro digital</a>
                                    </center>
                                </td>    
                            <?php }?>
                        <?php }
                        else{?>
                        <tr class="tr-last">
                            <td class="td-last" colspan="4"><center><?=$lang->message->sin_resultados?></center></td>
                        </tr>
                        <?php }?>    
                    </tbody>
                </table>
                <!-- -->
            	<span class="clear"></span>
			</div><!-- fin. contenido--> 
        </form>  
	</div> <!-- fin. solapas-->   
</div>
