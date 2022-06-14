<?php
use Clsk\Elena\Router\StarNavigator;

StarNavigator::Get("/", function(){return Controller("HelloController", "Index");});