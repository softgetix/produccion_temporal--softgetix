<?php
$file = fopen('ultimo.txt','w');
fclose($file);
$dir = 'archivos';
$handle = opendir($dir);
$i = 0;
while ($contents = readdir($handle)) {
    $i++;
    @unlink($dir.'/'.$contents);
    if ($i > 50) die();
}
?>
