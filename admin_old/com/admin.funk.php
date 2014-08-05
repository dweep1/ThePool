<?php 

function getKeyFormat($key){
    
    $format = "http://".@$_SERVER[HTTP_HOST]."/register.php?key=".$key;
    
    return $format;
    
}


function getKeys(){

    $array = array(0 => false);

    $count = 0;

    $query = DB::sql("SELECT * FROM `keys` ORDER BY id DESC");

    if(DB::sql_row_count($query) > 0){

        while($result = DB::sql_fetch($query)){

            $array[$count] = $result;
            $count++;

        }
    }

    if($array[0] === false){
        return false;
    }else{
        return $array;
    }

}

function getUsers(){

    $array = array(0 => false);

    $count = 0;

    $query = DB::sql("SELECT * FROM `users` ORDER BY id ASC");

    if(DB::sql_row_count($query) > 0){

        while($result = DB::sql_fetch($query)){

            $array[$count] = $result;
            $count++;

        }
    }

    if($array[0] === false){
        return false;
    }else{
        return $array;
    }

}

include "../_functions/global.funk.php";

?>