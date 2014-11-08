<?php

class rivals extends Logos_MySQL_Object{

    public $user_id;
    public $rival_id;
    public $rival_name;
    public $rival_custom_name = "";

    public static function findRival($userID, $rivalsList){

        foreach($rivalsList as $value){
            if((int)$userID === (int)$value->rival_id)
                return $value;
        }

        return false;
    }

}