<?php


class season extends event{

    public $year;
    public $game_count;
    public $text_id;
    public $week_count;

    /*public static function getCurrent(){
        return new season(3);
    }*/

    public function createNew(){

        if($this->save() === false)
            return false;

        $date = new DateTime($this->date_start);

        for($i = 0; $i < $this->week_count; $i++){

            $start = $date->format('Y-m-d H:i:s');
            $date->modify('next monday');
            $end = $date->format('Y-m-d H:i:s');
            $number = $i + 1;

            $tempWeek = new week(array("date_start" => $start, "date_end" => $end, "week_number" => $number, "season_id" => $this->id));

            if($tempWeek->save() === false)
                return false;

            $gameDate = new DateTime($end);
            $gameDate->modify('-1 Day');
            $gameDate = $gameDate->format('Y-m-d H:i:s');

            for($k = 0; $k < ($this->game_count/$this->week_count); $k++){

                $tempGame = new game(array("week_id" => $tempWeek->id, "season_id" => $this->id, "date" => $gameDate));

                if($tempGame->save() === false)
                    return false;

            }

        }

        return true;

    }

    public function deleteSeason(){

        $weeks = new week();
        $weeks = $weeks->getList(null, array("season_id" => $this->id));

        if(is_bool($weeks))
            return false;

        foreach($weeks as $week){

            $games = new game();
            $games = $games->getList(null, array("week_id" => $week->id));

            if(is_bool($games))
                return false;

            foreach($games as $game){
                if($game->erase() === false)
                    return false;

            }

            if($week->erase() === false)
                return false;

        }

        return $this->erase();

    }

}