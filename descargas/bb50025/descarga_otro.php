<?php
$browser = strpos($_SERVER['HTTP_USER_AGENT'],"lackBerry8520");
//if (!($browser == true))  { echo 'Acceso Denegado';die(); }
$final = 'archivos/'.md5(rand(0,1000)).'.jad';
$inicio = 'archivos/DemoLocalizart.jad';
copy($inicio,$final);
?>
<script>
    function redir() {
        window.location.href = "descargas/bb50025/<?php echo $final?>";
    }
    function erase() {
        window.open("descargas/bb50025/borrar.php?f=<?php echo $final?>&p=0");
    }
    
    erase();
    setTimeout("redir()",1000);
</script>
<?
//header("Location: http://200.32.10.146/localizart/descargas/bb50025/archivos/".$inicio);
die();
?>