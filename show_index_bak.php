<?php
header('Content-type: text/html');
$code = file_get_contents("index_bak.php");
highlight_string($code);
