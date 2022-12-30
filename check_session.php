<?php
session_start();
//ini_set("session.gc_maxlifetime",144);
//session_cache_expire(144);

//echo $_COOKIE['PHPSESSID'];
echo '<pre>';
print_r($_SESSION);
echo '</pre>';


//echo "<br>".ini_set("session.gc_maxlifetime");
//echo "<br>".session_cache_expire();
?>