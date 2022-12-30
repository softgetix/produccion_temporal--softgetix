<div class="maincontent" id="maincontent">
<?php switch ($operacion){ case 'listar':?>
<div class="ppal">
    
	<?php if($arrElementos[0]['wp_barra_control']==1){ require_once('wizardsElementos.php');} ?>
	<form action="?c=<?=$seccion?>" method="post" id="formWizard">
	<?php if(!is_array($arrElementos)){ echo '<div class="error">Error: No se encuentra el nodo solicitado:'.$_POST['nodo'].' Para el wizard:'.$_POST['wizard'].'</div>';die();} ?>
    <div class="titulo"><?=decode($arrElementos[0]['wn_titulo']);?></div>
	
	<?php if($arrElementos[0]['wp_barra_control']==1){?>
	<div class="texto"><?=decode($arrElementos[0]['wn_texto']); ?></div>
	<?php }else{ echo "<br>&nbsp;<br>"; } ?>
	
	
    <?php if(isset($arrElementos[0]['wn_imagen'])){?><div class="imagen"><img class="imgnodo" src="<?=$arrElementos[0]['wn_imagen']?>" border=0 /></div><?php } ?>
    <?php if($arrElementos[0]['wp_barra_control']!=1){ 
		echo "<style>div.elementos{position:static !important;}</style>"; 
	 
	 	if(!$TyC_sinBotonera){
			require_once('wizardsElementos.php');	
		}
	 }  ?>
    



    <input type="hidden" name="wizard" id="wizard" value="<?=$wizard ?>" />
    <input type="hidden" name="nodo" id="nodo" />
    <input type="hidden" name="ruta" id="ruta" value="<?=$ruta?>" />       
    <input type="hidden" name="trace" id="trace" value="<?=$trace?>">	
    <input type="hidden" name="curso" id="curso" value="<?=$curso?>" />
   </form>   
			
<?php break;} ?>			

</div>       