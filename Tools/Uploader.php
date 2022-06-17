<?php
/*
 * ファイルをアップロードするためのファイルです。
 */
// 必要ファイルのインクルード
include dirname(__FILE__) . '/UUID.php';

// どこにアップロードするか？
$clearsky_root = dirname(__FILE__)."/../Images/";
// 一時ファイルを代入
$tempfile = $_FILES["UserPict"]['tmp_name'];
// ファイル名をUUIDを前につけて設定
$filename = $uuid.$_FILES["UserPict"]['name'];

// ファイルはアップロードされているか？
if (is_uploaded_file($tempfile)) {
    // ファイルの保存ディレクトリへの移動は成功したか？
    if ( move_uploaded_file($tempfile , $clearsky_root.$filename )) {
        // X, Y, W, Hの画像切り取りに必要な値は入っているか？
        if (isset($_POST['UserImageX']) && isset($_POST['UserImageY']) && isset($_POST['UserImageW']) && isset($_POST['UserImageH'])) {

            // 画像の切り取りを試行
            try {

                // jpeg画像の場合
                if (pathinfo($_FILES["UserPict"]['name'], PATHINFO_EXTENSION) == "jpeg" || pathinfo($_FILES["UserPict"]['name'], PATHINFO_EXTENSION) == "jpg") {
                    $im = imagecreatefromjpeg($clearsky_root.$filename);
                //png画像の場合
                } else if (pathinfo($_FILES["UserPict"]['name'], PATHINFO_EXTENSION) == "png") {
                    $im = imagecreatefrompng($clearsky_root.$filename);
                //gif画像の場合
                } else if (pathinfo($_FILES["UserPict"]['name'], PATHINFO_EXTENSION) == "gif") {
                    $im = imagecreatefromgif($clearsky_root.$filename);
                }

                // 画像切り取りを実行
                $im2 = imagecrop($im, ['x' => $_POST['UserImageX'], 'y' => $_POST['UserImageY'], 'width' => $_POST['UserImageW'], 'height' => $_POST['UserImageH']]);

                // 画像切り取りに成功したら
                if ($im2 !== FALSE) {
                    // ファイルを再保存
                    imagejpeg($im2, $clearsky_root.$filename);
                    imagedestroy($im2);
                }

                // 元ファイルを破棄
                imagedestroy($im);

                // ファイルディレクトリを変数に代入
                $file = '/AuthSample/Images/'.$filename;
            // 切り取り時に問題が発生した場合
            } catch (\Exception $e) {
                header('Location: /AuthSample/login.php');
            }
        // 切り取りに必要なデータが送信されていなかった場合
        }else{
            header('Location: /AuthSample/login.php');
        }
    // ファイルのディレクトリ移動に失敗した場合
    } else {
        header('Location: /AuthSample/login.php');
    }
// ファイルが送信されていなかった場合
}else{
    header('Location: /AuthSample/login.php');
}