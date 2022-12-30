<?php global $arrNavBar?>
<div id="localizartMenu" class="menu">
	<ul class="nivel_1">
    <?php
	$arbol = '';
	if($arrNavBar){
		foreach($arrNavBar as $key_1 => $level_1){
                    /* código contiene restricción para no permitir seleccionar desde el menú la sección activa.
                     * 
                     * if(!is_array($level_1)){
			$arbol.= '<li '.(($key_1 == $_GET['c'])?'class="active_menu"':'').'>
							<a href="'.(($key_1 == $_GET['c'])?'javascript:;':'boot.php?c='.$key_1).'" '.($level_1 == 'target_blank'?'target="_blank"':'').'>
								'.($lang->menu->$key_1?$lang->menu->$key_1:$key_1).'
							</a>
						</li>';
                    }
                    else{
			$arbol.= '<li>';
			$arbol.= '	<a href="javascript:;">'.($lang->menu->$key_1?$lang->menu->$key_1:$key_1).'</a>';
			$arbol.= '	<ul>';
			foreach($arrNavBar[$key_1] as $key_2 => $level_2){
                            if(!is_array($level_2)){
				$arbol.= '<li><a href="'.(($key_2 == $_GET['c'])?'javascript:;':'boot.php?c='.$key_2).'" '.($level_2 == 'target_blank'?'target="_blank"':'').'>'.($lang->menu->$key_2?$lang->menu->$key_2:$key_2).'</a></li>';
                            }
                            else{
                                $arbol.= '<li>';
				$arbol.= '	<a href="javascript:;">'.($lang->menu->$key_2?$lang->menu->$key_2:$key_2).'</a>';
				$arbol.= '	<ul>';
				foreach($arrNavBar[$key_1][$key_2] as $key_3 => $level_3){
                                    $arbol.= '<li><a href="'.(($key_3 == $_GET['c'])?'javascript:;':'boot.php?c='.$key_3).'" '.($level_3 == 'target_blank'?'target="_blank"':'').'>'.($lang->menu->$key_3?$lang->menu->$key_3:$key_3).'</a></li>';
				}
				$arbol.= '	</ul>';
				$arbol.= '</li>';
                            }
			}
			$arbol.= '	</ul>';	
			$arbol.= '</li>';
                    }
                    */
                    
                    if(!is_array($level_1)){
						if(!empty($level_1) && $level_1 != 'target_blank'){ //--Considero que tiene un link externo
							$arbol.= '<li><a href="'.$level_1.'" target="_blank">'.($lang->menu->$key_1?$lang->menu->$key_1:$key_1).'</a></li>';
						}
						else{
							$arbol.= '<li '.(($key_1 == $_GET['c'])?'class="active_menu"':'').'><a href="boot.php?c='.$key_1.'" '.($level_1 == 'target_blank'?'target="_blank"':'').'>'.($lang->menu->$key_1?$lang->menu->$key_1:$key_1).'</a></li>';
						}
                    }
                    else{
                    	$arbol.= '<li>';
			$arbol.= '	<a href="javascript:;">'.($lang->menu->$key_1?$lang->menu->$key_1:$key_1).'</a>';
			$arbol.= '	<ul>';
			foreach($arrNavBar[$key_1] as $key_2 => $level_2){
                            if(!is_array($level_2)){
				$arbol.= '<li><a href="boot.php?c='.$key_2.'" '.($level_2 == 'target_blank'?'target="_blank"':'').'>'.($lang->menu->$key_2?$lang->menu->$key_2:$key_2).'</a></li>';
                            }
                            else{
				$arbol.= '<li>';
				$arbol.= '	<a href="javascript:;">'.($lang->menu->$key_2?$lang->menu->$key_2:$key_2).'</a>';
				$arbol.= '	<ul>';
                                    foreach($arrNavBar[$key_1][$key_2] as $key_3 => $level_3){
					$arbol.= '<li><a href="'.(($key_3 == $_GET['c'])?'javascript:;':'boot.php?c='.$key_3).'" '.($level_3 == 'target_blank'?'target="_blank"':'').'>'.($lang->menu->$key_3?$lang->menu->$key_3:$key_3).'</a></li>';
                                    }
				$arbol.= '	</ul>';
				$arbol.= '</li>';
                            }
			}
			$arbol.= '	</ul>';	
			$arbol.= '</li>';
                    }   
		}
	}
	echo $arbol;
	?>
    </ul>
    <br style="clear: left">
</div>
<div id="help-modal" style="display:none;"></div>

<script type="text/javascript">
    $mainMenuElems = $(".mainMenuElem");
    $mainMenuElems.each( function(ind, val){
        var $this = $(this);
        var $link = $("a", $this);
        
        $this.bind("click", function(ev){
            window.location.href = $link.attr("href");
        });
    });

</script>