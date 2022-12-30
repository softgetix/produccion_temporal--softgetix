<?php 

global $objPerfil;
global $arrNavBarSolapas;
$op_menu = 'viajes';
if($arrNavBarSolapas[$op_menu]){
    foreach($arrNavBarSolapas[$op_menu] as $solapa_menu){
        if(!empty($solapa_menu)){?>
        <a class="izquierda float_l <?=($_GET['c']==$solapa_menu)?'active':''?>" href="<?=$_SERVER['REDIRECT_URL'].'?c='.$solapa_menu?>">
            <?=$lang->solapa->$solapa_menu?$lang->solapa->$solapa_menu:($lang->menu->$solapa_menu?$lang->menu->$solapa_menu:$solapa_menu)?>
        </a>
        <?php }
    }
}
?>
                            

