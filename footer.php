<style>
.footer{width:100%;position:fixed;bottom:0px;padding:4px 0; background:#00237E; font-size: 10px;}
.footer span{color:#00237E;; float:left;line-height:14px; margin:0 10px;}
.footer p,.footer a{line-height:14px; font-size:10px; text-decoration:none; margin:0px; color:#FFF; }
.footer a{font-weight:bold !important;}
.footer .float_l{float:left; margin-left:10px;}
.footer .float_r{float:right; margin-right:10px;}
.zopim{margin-bottom:22px !important;}
</style>

<?php if(FOOTER == true){?>    
<div class="footer">
	<p class="float_l">Powered by <a href="http://www.localizar-t.com.ar">Localizar-T</a></p>
	
    <span>
    <pingdom_http_custom_check>
        <status>OK</status>
    	<response_time>96.777</response_time>
	</pingdom_http_custom_check>
    </span>
    <span>
    	SERVER: <?=gethostbyname(gethostname())?>&nbsp;
    </span>
    <?=!ES_MOBILE ? '<p id="dia-semana" class="float_r">&nbsp;</p>' : ''?>	
	<div class="clear"></div>
</div>
<?php }?>    

<?php if(strpos($_SERVER['SCRIPT_FILENAME'],'index.php') !== FALSE || strpos($_SERVER['SCRIPT_FILENAME'],'cambiarPass.php') !== FALSE){}
else{?>
	<script language="javascript">
        $(document).ready(function(){getFecha();});
    </script>
<?php } ?>

<?php if(strpos($_SERVER['REQUEST_URI'],'shootup') !== FALSE){?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-89501817-1', 'auto');
  ga('send', 'pageview');
</script>
<?php }elseif(strpos($_SERVER['REQUEST_URI'],'agenteoficialadt') !== FALSE){?>
	<!-- Start of adtargentina Zendesk Widget script -->
	<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=7ca973e2-4dfa-4d55-bf9c-77e7a3d015c8"> </script>
	<!-- End of adtargentina Zendesk Widget script -->
<?php }elseif(strpos($_SERVER['REQUEST_URI'],'palletswap') !== FALSE){?>
	<!-- Start of adtargentina Zendesk Widget script -->
	<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=cce9f025-0753-4d54-a555-29393225585b"> </script>
	<!-- End of adtargentina Zendesk Widget script -->
<?php }?>


