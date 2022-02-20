<?php
function chkArg(): bool {
    if (!in_array('f', array_keys($_GET)) || strlen($_GET['f']) == 0) {
        http_response_code(403);
        return false;
    }
    $fileName = $_GET['f'];
    $fileNameArr = explode('/', $fileName);
    if (count($fileNameArr) > 1) {
        http_response_code(403);
        return false;
    }
    $fileNameArr = explode('\\', $fileName);
    if (count($fileNameArr) > 1) {
        http_response_code(403);
        return false;
    }
    $fileNameArr = explode('.', $fileName);
    if (count($fileNameArr) != 2) {
        http_response_code(403);
        return false;
    }
    if (strcmp($fileNameArr[count($fileNameArr) - 1], 'plist') != 0) {
        http_response_code(403);
        return false;
    }
    return true;
}
function loadFile(): string|false {
    $fileName = $_GET['f'];
    if (!file_exists($fileName)) {
        http_response_code(404);
        return false;
    }
    $data = file_get_contents($fileName);
    if (!$data || strlen($data) == 0) {
        return false;
    }
    return $data;
}
function subString(string $str, string $begin, string $end, bool $inStartEnd = false) {
    $pos1 = strpos($str, $begin);
    if (!$inStartEnd) {
        $pos1 += strlen($begin);
    }
    $pos2 = strpos($str, $end);
    $dataXML = substr($str, $pos1, $pos2 - $pos1);
    if ($inStartEnd) {
        $dataXML += $end;
    }
    return $dataXML;
}
function clearEmpty(array $arr): array {
    $newArr = array();
    for ($i = 0; $i < count($arr); $i++) {
        $str = $arr[$i];
        $str = str_replace("\r", '', $str);
        $str = str_replace("\n", '', $str);
        $str = trim($str);
        if (strlen($str) > 0) {
            array_push($newArr, $str);
        }
    }
    return $newArr;
}
function findKeyValue(array $arr) {
    $nowKey = '';
    $nowVal = '';
    $newArr = array();
    for ($i = 0; $i < count($arr); $i++) {
        $val = $arr[$i];
        $isEven = $i % 2 === 0;
        if ($isEven) {
            $nowKey = $val;
        } else {
            $nowVal = $val;
            $newArr[strip_tags($nowKey)] = strip_tags($nowVal);
            $nowKey = '';
            $nowVal = '';
        }
    }
    return $newArr;
}
if (!chkArg()) {
    return;
}
$d = loadFile();
if (!$d) {
    return;
}
$d = subString($d, '<?xml version=', '</plist>');
$d = subString($d, '<dict>', '</dict>');
$da = explode("\n", $d);
$da = clearEmpty($da);
$da = findKeyValue($da);
header('Content-type:application/json');
echo json_encode($da);
