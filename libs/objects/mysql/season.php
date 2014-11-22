<?php


class season extends event{

    public $year;
    public $game_count;
    public $text_id;
    public $week_count;

    /**
     * Creates a new NFL season based on $this seasons game/week count.
     * @return bool
     */
    public function createNewSeason(){

        if($this->createNew() === false)
            return false;

        $date = new DateTime($this->date_start);

        $tempGames = [];

        for($i = 0; $i < $this->week_count; $i++){

            $start = $date->format('Y-m-d H:i:s');
            $date->modify('next monday');
            $end = $date->format('Y-m-d H:i:s');
            $number = $i + 1;

            $tempWeek = new week(["date_start" => $start, "date_end" => $end, "week_number" => $number, "season_id" => $this->id]);

            if($tempWeek->createNew() === false)
                return false;

            $gameDate = new DateTime($end);
            $gameDate->modify('-1 Day');
            $gameDate = $gameDate->format('Y-m-d H:i:s');

            for($k = 0; $k < ($this->game_count/$this->week_count); $k++){

                array_push($tempGames, ["week_id" => $tempWeek->id, "season_id" => $this->id, "date" => $gameDate]);

            }

        }

        game::createMultiple($tempGames);

        return true;

    }

    public function deleteSeason(){

        $weeks = week::loadMultiple(["season_id" => $this->id]);

        if(is_bool($weeks))
            return false;

        foreach($weeks as $week){

            $games = game::loadMultiple(["week_id" => $week->id]);

            if(is_bool($games))
                return false;

            foreach($games as $game){
                if($game->remove() === false)
                    return false;

            }

            if($week->remove() === false)
                return false;

        }

        return $this->remove();

    }

}