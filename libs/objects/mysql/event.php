<?php

error_reporting(E_ALL);

/**
 * The Class basis of a dated object, with a start and end date.
 */
abstract class event extends Selectable{

    public $date_start;
    public $date_end;

    /**
     * @return $this
     */

    public static function getCurrent(){

        $className = get_called_class();

        $now = new DateTime("now");

        $todayDate = $now->format("Y-m-d");

        $query = MySQL_Core::fetchQuery(
            "SELECT * FROM `{$className}` WHERE DATE(date_end) >= :today AND DATE(date_start) <= :today ORDER BY date_end ASC LIMIT 1",
            [":today" => $todayDate]
        );

        try {

            $object = $query->fetchObject($className);

        }catch(PDOException $pe) {

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        if(isset($object) && is_object($object)){
            $_SESSION['current_'.$className] = $object->toArray();
            return $object;
        }

        return false;

    }

    public function getPrevious(){

        $className = get_called_class();

        if ($this->id) {
          $query = MySQL_Core::fetchQuery(
              "SELECT * FROM `{$className}` WHERE id = (SELECT MAX(id) FROM `{$className}` WHERE id < :last_id)",
              [":last_id" => $this->id]
          );
        } else {
          $query = MySQL_Core::fetchQuery(
              "SELECT * FROM `{$className}` WHERE id = (SELECT MAX(id) FROM `{$className}` ORDER BY id DESC LIMIT 1)"
          );
        }

        try {

            return $query->fetchObject($className);

        }catch(PDOException $pe) {

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        return false;

    }


}
