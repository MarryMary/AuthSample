<?php
namespace Web\Programs\Controllers;

/* HelloController コントローラー
* ここに処理を記述します。
*
*/
use Clsk\Elena\Session\Session;
use Clsk\Elena\Tools\TimeToTime;
use Clsk\Elena\Tools\FileReader;
use Clsk\Elena\Request\RequestReceptor;
use Clsk\Elena\Executer\Provide;

class HelloController{
    public function Index()
    {
        $lang = "ja";
        $title = "Welcome to ClearSky Framework!!";

        echo Provide::Reader("SampleTemplate/Hello", compact("lang", "title"));
    }
}