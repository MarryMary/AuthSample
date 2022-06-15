<?php
$clearsky_root = dirname(__FILE__)."/../Images/";
$tempfile = $_FILES["UserPict"]['tmp_name'];
include dirname(__FILE__).'/../Process/UUID.php';
$filename = $uuid.$_FILES["UserPict"]['name'];

if (is_uploaded_file($tempfile)) {
    if ( move_uploaded_file($tempfile , $clearsky_root.$filename )) {
        if (isset($_POST['UserImageX']) && isset($_POST['UserImageY']) && isset($_POST['UserImageW']) && isset($_POST['UserImageH'])) {
            try {
                if (pathinfo($_FILES["UserPict"]['name'], PATHINFO_EXTENSION) == "jpeg" || pathinfo($_FILES["UserPict"]['name'], PATHINFO_EXTENSION) == "jpg") {
                    $im = imagecreatefromjpeg($clearsky_root.$filename);
                } else if (pathinfo($_FILES["UserPict"]['name'], PATHINFO_EXTENSION) == "png") {
                    $im = imagecreatefrompng($clearsky_root.$filename);
                } else if (pathinfo($_FILES["UserPict"]['name'], PATHINFO_EXTENSION) == "gif") {
                    $im = imagecreatefromgif($clearsky_root.$filename);
                }
                $im2 = imagecrop($im, ['x' => $_POST['UserImageX'], 'y' => $_POST['UserImageY'], 'width' => $_POST['UserImageW'], 'height' => $_POST['UserImageH']]);
                if ($im2 !== FALSE) {
                    imagejpeg($im2, $clearsky_root.$filename);
                    imagedestroy($im2);
                }
                imagedestroy($im);
                $file = '/AuthSample/Images/'.$filename;
            } catch (\Exception $e) {
                header('Location: /AuthSample/login.php');
            }
        }else{
            header('Location: /AuthSample/login.php');
        }
    } else {
        header('Location: /AuthSample/login.php');
    }
}else{
    header('Location: /AuthSample/login.php');
}