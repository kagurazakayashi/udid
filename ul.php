<?php
require "uuid.php";
$data = file_get_contents("php://input");
$fileName = uuid() . ".plist";
// if (!$data) {
//     return;
// }
$file = fopen($fileName, "w");
fwrite($file, $data);
fclose($file);
header("HTTP/1.1 301 Moved Permanently");
header("Location:info.php?f=$fileName", TRUE, 301);
