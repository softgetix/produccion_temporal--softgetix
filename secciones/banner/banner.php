<style>
    .banner{height:auto; padding:10px 20px; margin:0px 10px 10px 10px; display:none;}
    .banner p{font-size:24px; line-height:30px;}
</style>
<?php
    $src = 'secciones/banner/banner_agenteoficialadt.xml'; 
    if(file_exists($src)){
        $xml = simplexml_load_file($src);
        
        if($xml->banner){
            $banner = '';
            $count = 0;
            foreach($xml->banner as $item){
                $count++;
                $link = isset($item->link)?str_replace('[#]','&',$item->link):null;

                $banner.= '<div class="banner" id="banner_'.$count.'" style="background:'.$item->color_background.'">';
                $banner.= '<center>';
                $banner.= !empty($link)?'<a href="'.$link.'" target="_blank" class="no_decoration">':'';
                $banner.= '<p style="color:'.$item->color_text.'">'.$item->message.'</p>';
                $banner.= !empty($link)?'</a>':'';
                $banner.= '</center>';
                $banner.= '</div>';
            }
        }
        echo $banner;
    }
?>
<script anguage="javascript" type="text/javascript">
$(document).ready(function(){
    var $banners = $(".banner").toArray().length;
    var $show = 1;
    $('#banner_'+$show).fadeIn( "slow" );
    function changeBanner() {
        $('#banner_'+$show).hide();

        $show = $show + 1;
        if($show > $banners){
            $show = 1;
        }

        //$('#banner_'+$show).show();
        $('#banner_'+$show).fadeIn( "slow" );
        
    }
    setInterval(changeBanner, 5000);

});
</script>