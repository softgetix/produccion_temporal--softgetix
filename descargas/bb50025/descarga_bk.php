<?php
$browser = strpos($_SERVER['HTTP_USER_AGENT'],"lackBerry8520");
//if (!($browser == true))  { echo 'Acceso Denegado';die(); }
$final = md5(rand(0,1000)).'.jad';
$inicio = 'DemoLocalizart.jad';
?>
<iframe src="archivos/DemoLocalizart.jad" style="position:relative;left:0px;top:0px;width:100%;height:350px;overflow-x:hidden"></iframe>
<?php
die();
?>