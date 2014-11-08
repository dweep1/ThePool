<?php

class admin_log extends Logos_MySQL_Object{

    public $type = "error";
    public $subject;
    public $log_data;
    public $location;

    //admin_log::generateLog(array( "type" => "", "subject" => "", "log_data" => "", "location" => $_SERVER['REQUEST_URI']))

    public static function generateLog($data){

        return self::createSingle($data);

    }

}