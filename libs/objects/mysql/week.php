<?php

class week extends event{

    public $season_id;
    public $week_number;

    /*public static function getCurrent(){
        return new week(28);
    }*/

    public static function getPoolAmount($week_id = false, $countSeason = false){

        $pickNumber = 0;
        $multiple = options::loadSingle(["name" => "credit_weekly_value"]);

        if($countSeason === false){
            $week_id = ($week_id) ?: week::getCurrent()->id;

            $pickNumber = count(pick::query(["groupBy" => "user_id"])->getList(["week_id" => $week_id]));

        }else{

            $countSeason = $countSeason === true ? season::getCurrent()->id : $countSeason;

            $pickNumber = count(pick::query(["groupBy" => "user_id"])->getList(["season_id" => $countSeason]));

        }

        return ($pickNumber*10)*($multiple->value/100);

    }

    public function getGameCount(){

        $games = $this->getGames(true);

        return count($games);

    }

    public static function getNextLock($offset){

        $thisWeek = self::getCurrent();

        if($thisWeek === false)
            return false;

        $games = $thisWeek->getGames(true);

        if(count($games) <= 0)
            return false;

        $count = 0;

        $game = $games[$count];

        while($game->isLocked() === true){

            if(strpos(Core::getDay($game->date),'Mon') !== false)
                break;

            $count++;

            $next = isset($games[$count]) ? $games[$count] : false;

            if($next === false)
                break;

            if((int) $next->week_id !== (int) $game->week_id)
                break;
            else
                $game = $next;

        }

        $gameLock = $game->getLockTime();

        return self::getTimezoneLockTime($gameLock, $offset);

    }

    public static function getTimezoneLockTime($gameDate, $offset){

        $newOffset = (240 - $offset)/60;

        $EST = Core::getTimezone();

        $game = new DateTime($gameDate, $EST);

        if($newOffset >= 0){
            $game->add(new DateInterval("PT{$newOffset}H"));
        }else{
            $newOffset = $newOffset*(-1);
            $game->sub(new DateInterval("PT{$newOffset}H"));
        }

        return $game->format('Y-m-d H:i:s');

    }

    public function getStructured($user_id = null, $noIndex = false){

        $currentUser = users::returnCurrentUser();

        $this->games = $this->getGames($noIndex);

        $picks = $this->getPicks($user_id);
        $teams = teams::getTeamsList();

        $tempRanking = users::getPlayerRanking($currentUser->id, $this->id);

        $this->week_score = stat_log::getUserStats(6, array('week_id' => $this->id, 'user_id' => $currentUser->id))[0]['value'] ?: 0;
        $this->week_rank = Core::formatNumber((((int) $tempRanking > 0) ? $tempRanking : "N/A" ));

        $tempRanking = users::getPlayerRanking($currentUser->id);

        $this->total_score = stat_log::getUserStats(6, array('user_id' => $currentUser->id))[0]['value'] ?: 0;
        $this->total_rank = Core::formatNumber((((int) $tempRanking > 0) ? $tempRanking : "N/A" ));

        if(!is_bool($this->games)){
            foreach($this->games as $key => $val){

                $this->games[$key]->gameLock = $this->games[$key]->isLocked();

                $this->games[$key]->away_team = $teams[$this->games[$key]->away_team];
                $this->games[$key]->home_team = $teams[$this->games[$key]->home_team];

                $this->games[$key]->away_score = (int) $this->games[$key]->away_score;
                $this->games[$key]->home_score = (int) $this->games[$key]->home_score;

                $gameDate = new DateTime($this->games[$key]->date);
                //$gameDate->setTime(0,0,0);

                $this->games[$key]->date_name = $gameDate->format('l');
                $this->games[$key]->display_date = $gameDate->format('D, m/d');

                if(isset($picks[$val->id]))
                    $this->games[$key]->pick = $picks[$val->id];
                else{
                    $user = $currentUser;

                    $dataArray = array("season_id" => $this->season_id, "week_id" => $this->id, "game_id" => $val->id
                    ,"team_id" => -1, "user_id" => $user->id, "value" => 0, "result" => -1);

                    $newPick = new pick($dataArray);

                    $this->games[$key]->pick = $newPick;
                }

            }
        }
    }

    public function getGames($noIndex = false){

        $tempStore = [];

        $games = game::query(["orderBy" => "id asc"])->getList(["week_id" => $this->id]);

        foreach($games as $value){

            if((int) $value->home_team !== 0)
                $tempStore[$value->id] = $value;

        }

        if($noIndex)
            $tempStore = array_values($tempStore);

        return (!is_bool($games)) ? ((isset($tempStore)) ? $tempStore : false ) : $games;

    }

    public function getPicks($user_id = null){

        if($user_id === null)
            $user_id = users::returnCurrentUser()->id;

        $picks = pick::query(["orderBy" => "game_id ASC"])->getList(["week_id" => $this->id, "user_id" => $user_id]);

        if(!is_bool($picks)){
            foreach($picks as $value){
                $value->value = (int) $value->value;
                $tempStore[$value->game_id] = $value;
            }
        }

        return (!is_bool($picks)) ? ((isset($tempStore)) ? $tempStore : false ) : $picks;

    }

}