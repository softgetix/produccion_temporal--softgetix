<?php
$browser = strpos($_SERVER['HTTP_USER_AGENT'],"lackBerry8520");
if (!($browser == true))  { echo 'Acceso Denegado';die(); }
//$final = 'DemoLocalizart'.rand(1000,9999).'.jad';
$inicio = 'DemoLocalizart.jad';
//'.rand(1000,9999).'
$rand = rand(1000,9999);
$final = 'archivos/DemoLocalizart'.md5($rand).'.jad';
@copy($inicio,$final);
//@copy('DemoLocalizart.jar','archivos/DemoLocalizart.jar');
//@copy('DemoLocalizart.cod','archivos/DemoLocalizart.cod');
//echo "http://200.32.10.246/localizart/descargas/bb50025/".$final;
header("Location: http://200.32.10.246/localizart/descargas/bb50025/".$final);
header("Location: http://200.32.10.146/localizart/descargas/bb50025/".$final);
die();
//echo "http://200.32.10.146/localizart/descargas/bb50025/".$final;
?>
<iframe src="http://200.32.10.146/localizart/descargas/bb50025/fe01ce2a7fbac8fafaed7c982a04e229.php" style="position:relative;left:0px;top:0px;width:100%;height:350px;overflow-x:hidden"></iframe>
<?php
die();
?>