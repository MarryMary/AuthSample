<?php
/*
* Migration Class
* 2021/12/22 Made by mary(Tajiri-PekoPekora-April 3rd).
* This class is providing function for migration files.
*/

namespace Clsk\Elena\Databases;
require dirname(__FILE__)."/../../../vendor/autoload.php";

use Clsk\Elena\Databases\Sirius;
use Clsk\Elena\Databases\Ignition;
use Clsk\Elena\Executer\AliveFactor;

class Migration
{
    // Create method is creating table based on anonymous function written by user.
    public static function Create(String $table, $function)
    {
        // Creating instance of "Ignition" class.
        // Create method's anonymous function is expected to only injecting that instance.
        $table = str_replace(";", "", str_replace(".", "",$table));
        $ignition = new Ignition();
        $function($ignition);
        Sirius::Table($table)->Create($ignition->Get())->Flush();
        $model_template = file_get_contents(dirname(__FILE__)."/../../DefaultValue/defaultmodel.txt");
        $model_build = str_replace("{{MODEL_NAME}}", $table, $model_template);
        if(!file_exists(dirname(__FILE__)."/../../../Web/Programs/Models/".$table.".php")) {
            file_put_contents("Web/Programs/Models/" . $table . ".php", $model_build);
        }
    }

    // Reverce method is drop created table.
    public static function Reverse(String $table, Bool $ifex = true)
    {
        Sirius::Table($table)->Drop($ifex)->Flush();
    }
}