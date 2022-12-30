<div id="main" class="sinColIzq">
    <div class="solapas gum clear">
        <form name="frm_<?=$seccion ?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;">
            <div style="height:100%" class="contenido flaps clear"> 
            
                <iframe id="iframe" src="https://www.intraate.com/login" style="width:100%;"></iframe>

            <span class="clear"></span>
			</div><!-- fin. contenido--> 
	    </form>
    </div>
</div>
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