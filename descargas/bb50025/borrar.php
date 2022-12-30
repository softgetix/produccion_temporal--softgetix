<?php

usleep(4000000);

$file = isset($_GET['f']) ? $_GET['f'] : '';

if (!$file) die();

unlink(''.$file);

?>

<script>
window.close();
</script>