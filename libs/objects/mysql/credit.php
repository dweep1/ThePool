<?php

class credit extends Logos_MySQL_Object{

    public $date;
    public $user_id;
    public $week_id = -1;
    public $nid;
    public $amount;

    public static function useCredit($user_id = null, $week_id = null){

        if($user_id === null)
            $user_id = users::returnCurrentUser()->id;

        if($week_id === null)
            $week_id = week::getCurrent()->id;

        $creditCost = options::loadSingle(["name" => "credit_cost"]);

        $credits = self::validCredit($user_id, $week_id);

        if($credits !== false && count($credits) > 0){
            return true;
        }else{

            $credits = credit::query(["orderBy" => "id ASC"])
                ->getList(["user_id" => $user_id, "week_id" => -1, "amount" => $creditCost->value]);

            if(count($credits) > 0){
                return credit::saveSingle(["week_id" => $week_id], ["id" => $credits[0]->id]);
            }

        }

        return false;

    }

    public static function validCredit($user_id = null, $week_id = null){

        $useCredit = options::loadSingle(["name" => "use_credit"]);

        $creditCost = options::loadSingle(["name" => "credit_cost"]);

        if((int) $useCredit->value <= 0)
            return true;

        if($user_id === null)
            $user_id = users::returnCurrentUser()->id;

        if($week_id === null)
            $week_id = week::getCurrent()->id;

        return self::loadSingle(["user_id" => $user_id, "week_id" => $week_id, "amount" => $creditCost->value]);

    }

    public static function generateCredit($data){

        return self::createSingle($data);

    }

    public static function getCreditCount($user_id, $week_id = null){

        if($user_id === null)
            $user_id = users::returnCurrentUser()->id;

        $creditCost = options::loadSingle(["name" => "credit_cost"]);

        if($week_id === null)
            return count(self::loadMultiple(["user_id" => $user_id, "amount" => $creditCost->value]));
        else
            return count(self::loadMultiple(["user_id" => $user_id, "week_id" => $week_id, "amount" => $creditCost->value]));

    }


}
