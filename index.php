<?php
include 'Template/ServiceData.php';
// ここにアクセスされた場合はlogin.phpに遷移します。
header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');