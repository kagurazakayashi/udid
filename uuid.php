<?php

/**
 * 建立新的隨機 UDID
 * @return {string} 新的隨機 UDID
 */
function uuid(): string {
    $rand = mt_rand();
    $uniqueID = uniqid($rand, true);
    $chars = md5($uniqueID);
    $subs = array(
        [0, 8],
        [8, 4],
        [12, 4],
        [16, 4],
        [20, 12]
    );
    $units = array();
    foreach ($subs as $s) {
        $unit = substr($chars, $s[0], $s[1]);
        array_push($units, $unit);
    }
    $uuids = array(
        substr($chars, 0, 8),
        substr($chars, 8, 4),
        substr($chars, 12, 4),
        substr($chars, 16, 4),
        substr($chars, 20, 12)
    );
    $uuidStr = implode('-', $uuids);
    return strtoupper($uuidStr);
}
if (in_array("uuid", array_keys($_GET)) && strcmp($_GET["uuid"], "new") == 0) {
    header("Content-type:text/plain");
    echo uuid();
}
