<?php
include dirname(__FILE__).'/../Tools/Session.php';

SessionStarter();
header('Location: /AuthSample/login.php');