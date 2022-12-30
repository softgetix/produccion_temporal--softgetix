<div id="main" class="sinColIzq">
    <?php //require_once 'banner/banner.php';?>			    
    <div class="solapas gum clear">
        <form name="frm_<?=$seccion ?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;">
            <div style="height:100%" class="contenido flaps clear"> 
            <table width="100%" height="100%">
            <tbody>    
            <?php if($arrListado){
                foreach($arrListado as $i => $item){?>
                <tr class="<?=((count($arrListado) - 1)==$i)?'tr-last':''?>">
                    <td style="background:<?=$item[0]['ac_color_fondo'] ? $item[0]['ac_color_fondo'] : '#FFFFF'?>">
                        <center>
                        <?php if(count($item) > 1){ //--Es carrousel ?>
                            <table style="width:auto;" height="100%">           
                            <tbody><tr><td style="border:none !important;">
                                <div class="carrousel" style="text-align:left;">
                                    <div class="cuadro">
                                        <div class="nav izq"><a><span>Ant</span></a></div>
                                        <div id="slideshow">
                                        <?php foreach($item as $elem){?>
                                            <img src="<?=$elem['ac_url']?>" width="auto" height="auto" border="0">
                                        <?php }?>
                                        </div>
                                        <div class="nav der"><a><span>Sig</span></a></div>
                                    </div> 
                                    <div style="clear:both"></div>
                                </div>          
                            </td></tr></tbody>    
                            </table>
                        <?php } else{?>
                            <img src="<?=$item[0]['ac_url']?>">
                            <?php if(!empty($item[0]['ac_descripcion'])){?>
                                <span class="clear"></span>
                                <label><?=$item[0]['ac_descripcion']?></label>
                            <?php }
                        }?>
                        </center>
                    </td>
                </tr>
                <?php }
            }?>
            <tbody>
            </table>
            <span class="clear"></span>
			</div><!-- fin. contenido--> 
	    </form>
    </div>
</div>
<script>
    $(window).load(function(){
        $(".carrousel").emiSlider({
            easing: "easeOutExpo",
            duration: 1000,
            next: ".nav.der a",
            previous: ".nav.izq a",
            carousel: "#slideshow",
            galeria: "#galeria",
            auto:true,
			bullet: "bullet_carrousel",
			slider_id: 0
        });
    });
</script>
