<?php
/*
 * 衝突しないUUID V4を生成します。
 */

$pattern = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx';
$chars = str_split($pattern);

foreach ($chars as $i => $char) {
    if ($char === 'x') {
        $chars[$i] = dechex(random_int(0, 15));
    } elseif ($char === 'y') {
        $chars[$i] = dechex(random_int(8, 11));
    }
}

$uuid = implode('', $chars);