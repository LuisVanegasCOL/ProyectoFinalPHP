<?php
$ip = $_SERVER['REMOTE_ADDR'];
$data = file_get_contents("http://ip-api.com/json/{$ip}");
echo $data;
?> 