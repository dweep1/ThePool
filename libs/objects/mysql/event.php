<?php

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



}