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
    <p id="dia-semana" class="float_r">&nbsp;</p>	
	<div class="clear"></div>
</div>
<?php }?>    

<?php if(strpos($_SERVER['SCRIPT_FILENAME'],'index.php') !== FALSE || strpos($_SERVER['SCRIPT_FILENAME'],'cambiarPass.php') !== FALSE){}
else{?>
	<script language="javascript">
        $(document).ready(function(){getFecha();});
    </script>
	<?php 
	//Chat habilitado para todos los usuarios logueados menos Fiber y ADT
	$arrClientes = array(179,5297,5298,5299,8227,8229,9048,11273);
	if(!in_array($_SESSION['idEmpresa'],$arrClientes) && !in_array($_SESSION['idAgente'],$arrClientes)){?>
	<script>
		window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments);},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display:none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(e){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("//assets.zendesk.com/embeddable_framework/main.js","localizart.zendesk.com");
		zE(function(){
			zE.hide();
			//zE.activate(false);
			zE.identify({name:'<?=$_SESSION['us_nombre'].' '.$_SESSION['us_apellido']?>',email:'<?=$_SESSION['nombreUsuario']?>',organization:'<?=$_SESSION['DIRCONFIG']?>'});
			//zE.setLocale('es');
		});
	</script>
	<?php }
}?>

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

<script type="application/javascript" charset="UTF-8" src="https://cdn.agentbot.net/core/0bb5feada89371d707b91e695c7677ae.js"></script>


<?php }?>

