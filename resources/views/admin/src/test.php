<?php

$filePath = __DIR__ . '/example/cert/client.crt.pem';
$myFile = fopen($filePath, "w") or die("Unable to open file!");
echo fread($myFile, filesize($filePath));
fclose($myFile);