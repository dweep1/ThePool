<?php

/**
 * The Class basis of a dated object, with a start and end date.
 */
abstract class event extends Logos_MySQL_Object{

    public $date_start;
    public $date_end;

    public static function getCurrent(){

        $className = get_called_class();

        $now = new DateTime("now");

        if(isset($_SESSION['current_'.$className])){

            $object = new $className($_SESSION['current_'.$className]);

            $dateEnd = new DateTime($object->date_end);

            if($className == "season")
                $dateEnd->add(new DateInterval("P14D"));

            if($dateEnd >= $now)
                return $object;

        }

        $todayDate = $now->format("Y-m-d");

        $query = MySQL_Core::fetchQuery(
            "SELECT * FROM {$className} WHERE DATE(date_end) >= :today AND DATE(date_start) <= :today ORDER BY date_end LIMIT 1",
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

    /**
     * Loads a selected object from the database
     * @param Mixed $id is either the Id of an object in the database, or the object itself
     * @param Boolean $default is what the default, if there is no selected object, should be;
     * @return Object of selected class name;
     * @throws PDO error if database is unreachable
     */
    public static function selected($id = null, $default = false){
        $className = get_called_class();
        if($id === null){
            $selected = (isset($_SESSION['selected_'.$className])) ? new $className($_SESSION['selected_'.$className]) : (is_object($default) ? $default : new $className($default));
        }else{
            if(is_object($id)){
                $selected = $id;
            }else{
                $selected = new $className($id);
            }
            $_SESSION['selected_'.$className] = $selected->toArray();
        }
        return $selected;
    }

}