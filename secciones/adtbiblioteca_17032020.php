<?php //require_once 'includes/botoneraABMs.php';?>			
<div id="main" class="sinColIzq">
    <?php require_once 'banner/banner.php';?>			    
    <div class="solapas gum clear">
    <!--<div class="mainBoxLICabecera">-->
        <form name="frm_<?=$seccion ?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;" enctype="multipart/form-data">
            <div class="esp">
                <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
                <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion?>" />
                <input type="hidden" name="action" value="<?=$_REQUEST['action']?>" />
            </div>
            <a class="izquierda float_l <?=($solapa=='adt_manual_de_marca')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=adt_manual_de_marca'?>">Manual de Marca</a>
            <a class="izquierda float_l <?=($solapa=='adt_terminos_y_condiciones')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=adt_terminos_y_condiciones'?>">Términos y condiciones</a>
            <a class="izquierda float_l <?=($solapa=='adt_promociones')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=adt_promociones'?>">Promociones</a>
            <a class="izquierda float_l <?=($solapa=='adt_lista_de_precios')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=adt_lista_de_precios'?>">Lista de precios</a>
            <a class="izquierda float_l <?=($solapa=='adt_novedades')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=adt_novedades'?>">Novedades</a>
			<a class="izquierda float_l <?=($solapa=='adt_normas')?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$seccion.'&solapa=adt_normas'?>">Normas de instalaci&oacute;n</a>
            <div style="height:100%" class="contenido flaps clear"> 
                
                <table width="100%" height="100%">
                <tbody>
                <?php if($nro_accion == 3){?>
                    <?php if($arrListado){
                        foreach($arrListado as $i => $item){?>
                            <tr class="<?=((count($arrListado) - 1)==$i)?'tr-last':''?>">
                                <td>
                                <center>
                                    <br><img src="<?=$item['ac_url']?>">    
                                </center>
                                </td>
                            </tr>
                        <?php }
                    }?>
                <tbody>
                </table>    
                <?php }else{?>
                <table width="100%" height="100%">
                <thead>
                    <tr>
                        <td><center><span class="campo1">Descripción</span></center></td>
                        <td><center><span class="campo1">Archivo</span></center></td>
                    </tr>
                </thead>
                <tbody>
                <?php if($arrListado){
                    foreach($arrListado as $i => $item){
                        $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                        <tr class="<?=$class?> <?=((count($arrListado) - 1)==$i)?'tr-last':''?>">
                            <td>
                                <center>
                                    <a href="<?=$item['ac_url']?>" target="_blank" class="no_decoration"><?=encode($item['ac_descripcion'])?></a>
                                </center>
                            </td>
                            <td>
                                <a href="<?=$item['ac_url']?>" target="_blank"><?=$item['ac_url']?></a>
                            </td>
                        </tr>
                    <?php } 
                        include('secciones/footer_LI.php');
                    }
                    else{?>
                        <tr class="tr-last">
                            <td class="td-last" colspan="6"><center><?=$lang->message->sin_resultados?></center></td>
                        </tr>
                    <?php }?>
                </tbody>
                </table>
                <?php }?>
            	<span class="clear"></span>
			</div><!-- fin. contenido--> 

            <!--<iframe id="iframe" src="https://agenteoficialadt.com/biblioteca/" style="width:100%;"></iframe>-->
	    </form>
    </div>
</div>
<!--
<script type="text/javascript">
	$(document).ready(function(){
		resizeIframe();
	
		$(window).resize(function() {
            resizeIframe();
        });
    });
	 
	function resizeIframe(){
		var $height = $(window).height(); 
		$("#iframe").height(parseInt($height)-140);
	}	
</script>
-->