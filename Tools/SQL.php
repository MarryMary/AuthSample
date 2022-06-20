<?php
/*
 * PDOインスタンスを作成するファイルです
 */

include_once dirname(__FILE__).'/../Template/ServiceData.php';

$dsn = trim(strtolower($RDB)).':charset='.trim($DB_CHARSET).';dbname='.$DB_NAME.';host='.$DB_HOST.':'.$DB_PORT;
$username = $DB_USER;
$password = $DB_PASS;

$pdo = new PDO($dsn, $username, $password);
