<?php
header('Content-type: text/html');
$code = file_get_contents("index.php");
highlight_string($code);
include_once("analyticstracking.php");