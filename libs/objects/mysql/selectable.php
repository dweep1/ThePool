<?php
/**
 * Created by PhpStorm.
 * User: HellsAn631
 * Date: 12/4/14
 * Time: 2:00 PM
 */

class Selectable extends Logos_MySQL_Object{
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