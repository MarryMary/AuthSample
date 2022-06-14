<?php
/*
* ModelChaser Class
* 2021/12/22 Made by mary(Tajiri-PekoPekora-April 3rd).
* This class is providing model system.
* For example, fetch data from sql based on model class name, fetched data to object, and so on.
*/
namespace Clsk\Elena\Databases;

require dirname(__FILE__)."/../../../vendor/autoload.php";

use Clsk\Elena\Databases\Sirius;
use Clsk\Elena\Exception\ExceptionThrower\ClearSkySiriusException;

class ModelChaser
{
    private $result;
    private $qb;
    private $insert_array;
    private static $insert;

    // Table chaser is tracks the table you want to work with from the model class name you decide or the table name you specify in the model.
    private static function TableChaser()
    {
        // Here, the caller is searched, and if a table name is specified separately, that is given priority and used.
        $table = self::class;
        $table = explode("\\", $table);
        if(isset(self::$table)){
            $table = self::$table;
        }else{
            $table = $table[3];
        }
        if(!isset(self::$primarykey)){
            self::$primarykey = 'id';
        }
        return $table;
    }

    public function all()
    {
        return (object)$this->qb->Fetch()->Flush()->All();
    }

    public function pull()
    {
        return (object)$this->qb->Fetch()->Flush()->One();
    }

    public function drop()
    {
        $this->qb->Delete()->Flush();
        return True;
    }

    public static function gather()
    {
        return (object)Sirius::Table(self::TableChaser())->Fetch()->Flush()->All();
    }

    public static function top()
    {
        return (object)Sirius::Table(self::TableChaser())->Fetch()->Flush()->All();
    }

    public function check(Array $insert)
    {
        if(isset($this->protected) || isset($this->open))
        {
            $insert_array = array();
            foreach($insert as $key => $value){
                if(isset($this->protected) && !in_array($key, $this->protected) && isset($this->open) && in_array($key, $this->open)){
                    $insert_array[$key] = $value;
                }
            }
            $this->insert_array = $insert_array;
            return $this;
        }else{
            throw new ClearSkySiriusException('To protect the system, specify either $open or $protected.');
            exit;
        }
    }

    public function push(Array $insert)
    {
        $insert_array = array();
        if(isset($this->protected) || isset($this->open)) {
            foreach ($insert as $key => $value) {
                if (isset($this->protected) && !in_array($key, $this->protected) && isset($this->open) && in_array($key, $this->open)) {
                    $insert_array[$key] = $value;
                }
            }
        }else{
            throw new ClearSkySiriusException('To protect the system, specify either $open or $protected.');
            exit;
        }
        Sirius::Table(self::TableChaser())->Insert($insert_array)->Flush();
        return (object) $insert_array;
    }

    public function erasure()
    {
        $this->qb->Delete()->Flush();
    }

    public function change(Array $update)
    {
        $update_array = array();
        if(isset($this->protected) || isset($this->open)) {
            foreach ($update as $key => $value) {
                if (isset($this->protected) && !in_array($key, $this->protected) && isset($this->open) && in_array($key, $this->open)) {
                    $update_array[$key] = $value;
                }
            }
        }else{
            throw new ClearSkySiriusException('To protect the system, specify either $open or $protected.');
            exit;
        }
        $this->qb->Update($update_array)->Flush();
        return (object) $update_array;
    }

    // get from primarykey
    public static function appoint($PrimaryKeyIs)
    {
        $instance = new static;
        $instance->qb = Sirius::Table(self::TableChaser())->Where(self::$primarykey, "=", $PrimaryKeyIs);
        return $instance;
    }

    public static function search($terms, $is)
    {
        $instance = new static;
        $instance->qb = Sirius::Table(self::TableChaser())->Where($terms, "=", $is);
        return $instance;
    }
}