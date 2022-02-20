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
        header('content-type:text/html;charset="utf-8"');
        echo '链接已过期';
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
if (in_array('delete', array_keys($_GET))) {
    @unlink($_GET['f']);
    header('content-type:text/html;charset="utf-8"');
    exit('已永久删除服务器上的记录');
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
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0,minimal-ui:ios">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#f2f1f6">
    <meta name="apple-mobile-web-app-status-bar-style" content="#f2f1f6">
    <meta name="format-detection" content="telephone=no">
    <title>我的设备</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <p>&emsp;</p>
    <div class="groupTitle key">我的设备信息</div>
    <div class="group">
        <div class="item">
            <?php
            $first = true;
            foreach ($da as $key => $value) {
                if ($first) {
                    $first = false;
                } else {
                    echo '<hr/>';
                }
                echo "<div class=\"val\">$value</div>";
                echo "<div class=\"key\">$key</div>";
            }
            ?>
        </div>
    </div>
    <div class="groupTitle key">长按以复制文本。</div>
    <p>&emsp;</p>
    <div class="groupTitle key">不会永久存储任何您的设备信息</div>
    <div class="group">
        <div class="item">
            <a href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '&delete'; ?>" target="_self">
                <div class="val">删除服务器上的记录</div>
                <div class="key">本链接将会失效</div>
            </a>
            <hr />
            <a href="index.html" target="_self">
                <div class="val">重新获取</div>
                <div class="key">重新安装描述文件</div>
            </a>
            <hr />
            <a href="https://github.com/kagurazakayashi/udid" target="_blank">
                <div class="val">开源代码</div>
                <div class="key">访问 Github 仓库</div>
            </a>
        </div>
    </div>
    <div class="groupTitle key">by 神楽坂雅詩</div>
    <p>&emsp;</p>
</body>

</html>