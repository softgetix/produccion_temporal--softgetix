<!--
<script type="text/javascript" src="js/jquery/colorbox/jquery.colorbox-min.js"></script>
<link rel=stylesheet type="text/css" href="js/jquery/colorbox/colorbox.css" />
-->        
<?php
class FiltrosCol{
	function __construct($col){
		$this->col = $col;
        $this->filtroCol = array();
        $this->isRadio = array();
	}
	
    function label($nameObject, $label, $classLabel, $isRadio = false){
	global $lang;?>
	
        <?php /* FILTRO ORIGINAL
        <div id="filterTable-<?=$nameObject?>" class="filterTable">
            <a href="javascript:;">
        	<span class="<?=$classLabel?>"><?=$label?></span>
                <img src="imagenes/<?=($_POST[$nameObject.'CheckAll'] || count($_POST[$nameObject]) == 0)?'filtroListen.jpg':'filtroListen_on.jpg'?>"/>
                <span style=" clear:both"></span>
            </a>
            <dl style="display: none;">
                <dt>
                    <input type="checkbox" name="<?=$nameObject?>CheckAll" id="<?=$nameObject?>CheckAll" onChange="javascript:checkFilterAll(this)" <?=($_POST[$nameObject.'CheckAll'] || count($_POST[$nameObject]) == 0)?'checked':''?> />
                    <label for="<?=$nameObject?>CheckAll">(<?=$lang->system->seleccionar_todo?>)</label>
                </dt>
                <dt>
                    <a href="javascript:;" onclick="javascript:enviar('filtrarCol')" style="line-height:20px;"><?=$lang->system->aplicar?></a>
                    <br />
                </dt>
            </dl>
        </div>
        */
        ?>

        <div id="filterTable-<?=$nameObject?>" class="filterTable">
            <a href="javascript:;">
        	<span class="<?=$classLabel?>"><?=$label?></span>
                <img src="imagenes/<?=(($isRadio == false && ($_POST[$nameObject.'CheckAll'] || count($_POST[$nameObject]) == 0)) || ($isRadio == true && ($_POST[$nameObject][0] == -1 || count($_POST[$nameObject]) == 0)))?'filtroListen.jpg':'filtroListen_on.jpg'?>"/>
                <span style=" clear:both"></span>
            </a>
            <div class="showFilter" style="display:none;" id="<?=$nameObject?>ShowFilter">
                <div class="contenedorFilter">
                <center><strong style="font-size: 14px; line-height: 24px;"><?=$label?></strong></center>
                <hr /><br />
                <dl tipo="<?=$nameObject?>CheckAll">
                    <dt <?=($isRadio?'style="display:none;"':'')?> >
                        <input type="checkbox" name="<?=$nameObject?>CheckAll" id="<?=$nameObject?>CheckAll" onChange="javascript:checkFilterAll(this)" <?=($_POST[$nameObject.'CheckAll'] || count($_POST[$nameObject]) == 0)?'checked':''?> />
                        <label for="<?=$nameObject?>CheckAll">(<?=$lang->system->seleccionar_todo?>)</label>
                    </dt>
                </dl>
                <br />    
                <center>
                    <a href="javascript:;" onclick="javascript:enviar('filtrarCol');" class="button colorin" style=""><?=$lang->system->aplicar?></a>
                    <a href="javascript:;" onclick="javascript:;" class="button cancel" style=""><?=$lang->botonera->cancelar?></a>
                </center>
                </div>
            </div>
	</div>
        
        <?php /*
        <!-- Lo nuevo CON POPUP -->
        <div id="filterTable-<?=$nameObject?>" class="filterTable">
            <a id="filterTablePopup-<?=$nameObject?>" href="#div_filterTablePopup-<?=$nameObject?>">
                <span class="<?=$classLabel?>"><?=$label?></span>
                <img src="imagenes/<?=($_POST[$nameObject.'CheckAll'] || count($_POST[$nameObject]) == 0)?'filtroListen.jpg':'filtroListen_on.jpg'?>"/>
                <span style=" clear:both"></span>
            </a>
            <div style="display:none;">
                
                <dl id="div_filterTablePopup-<?=$nameObject?>" class="filterTablePopup">
                    <dt>
                        <center><strong style="font-size: 14px;"><?=$label?></strong></center>
                        <hr /><br />
                    </dt>
                    <dt>
                        <input type="checkbox" name="<?=$nameObject?>CheckAll" id="<?=$nameObject?>CheckAll" onChange="javascript:checkFilterAll(this)" <?=($_POST[$nameObject.'CheckAll'] || count($_POST[$nameObject]) == 0)?'checked':''?> />
                        <label for="<?=$nameObject?>CheckAll">(<?=$lang->system->seleccionar_todo?>)</label>
                    </dt>
                    <dt>
                        <br />    
                        <center><a href="javascript:;" onclick="javascript:serializeFilter('<?=$nameObject?>');" class="button colorin" style="width:90%;"><?=$lang->system->aplicar?></a></center>
                    </dt>
                </dl>
            </div>
        </div>
        
        <script lang="javascript">
            $(document).ready(function() {
                $("#filterTablePopup-<?=$nameObject?>").colorbox({
                    inline: true,
                    width: "480px",
                    overlayClose: true,
                    escKey: true,
                    closeButton: true
                });
            });
        </script>    
        <!-- -->
         * */ ?>
        
    <?php }
  
	function value($nameObject, $label, $value, $isRadio = false){
		global $lang;
		if(!empty($label)){
			//$this->filtroCol[$nameObject][$value] = substr($label,0,30).'...'; 
            $this->filtroCol[$nameObject][$value] = $label;
		}
		else{
			$this->filtroCol[$nameObject][-1] = '('.decode($lang->system->vacias).')'; 
        }
        
        $this->isRadio[$nameObject] = false;
        if($isRadio == true){
            $this->isRadio[$nameObject] = true;
        }
	}

	function aplicar(){?>
		<script language="javascript">
		$(document).ready(function(){
			<?php 
			foreach($this->filtroCol as $kCol => $itemCol){
				arsort($itemCol);
				foreach($itemCol as $k => $item){?>
                    var dt = '';
                    dt+= '<input type="<?=($this->isRadio[$kCol]?'radio':'checkbox')?>" name="<?=$kCol?>[]" value="<?=$k?>" <?=(in_array($k,$_POST[$kCol]) || count($_POST[$kCol]) == 0)?'checked':''?> id="<?=$kCol.'-'.$k?>" onchange="javascript:checkFilterItem(this)">';
                    dt+= '<label for="<?=$kCol.'-'.$k?>"><?=encode($item)?></label>';
                    $('#filterTable-<?=$kCol?> dl dt:eq(0)').after('<dt>'+dt+'</dt>');    
                <?php }?>
			<?php }?>
			
			
			<?php 
			foreach($this->col as $item){?>
				if($('#filterTable-<?=$item?> dl dt').size() <= 2){
					$('#filterTable-<?=$item?>').children('dl').remove();
					$('#filterTable-<?=$item?> a').children('img').remove();
					$('#filterTable-<?=$item?>  a').css('cursor','text');
				} 
			<?php }?>
		});
		</script>	
	<?php }

	function validar(){ 
        
        $existeFiltro = false;

        //--Ini. Si cambia se sección se borran los filtros
        if(isset($_SESSION['filtroscol']['seccion'])){
            if($_SESSION['filtroscol']['seccion'] != $_GET['c']){
                unset($_SESSION['filtroscol']);
            }
        }
        else{
            $_SESSION['filtroscol']['seccion'] = $_GET['c'];
        }
        //--Fin. Si cambia se sección se borran los filtros

        //--Ini. Si cambia se sección se borran los filtros
        if(isset($_SESSION['filtroscol']['hidId'])){
            if(isset($_POST['hidId'])){
                if($_SESSION['filtroscol']['hidId'] != $_POST['hidId']){
                    unset($_SESSION['filtroscol']);
                }
            }
        }
        else{
            $_SESSION['filtroscol']['hidId'] = $_POST['hidId'];
        }
        //--Fin. Si cambia se sección se borran los filtros

        foreach($this->col as $item){
            if(count($_POST[$item]) == count($this->filtroCol[$item]) || (count($_POST[$item]) != count($this->filtroCol[$item]) && count($_POST[$item]) > 0)){
                unset($_SESSION['filtroscol'][$item]);
            }
            else{
                if(isset($_SESSION['filtroscol'][$item])){
                    $_POST[$item] = $_SESSION['filtroscol'][$item];
                }
            }

            if(count($_POST[$item]) > 0 && count($_POST[$item]) != count($this->filtroCol[$item])){
                $existeFiltro = true;
                $_SESSION['filtroscol'][$item] = $_POST[$item];
			}
			else{
                unset($_POST[$item]);
			}
		}
		return $existeFiltro;
    }
}