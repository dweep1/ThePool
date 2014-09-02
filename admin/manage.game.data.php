<?php

global $ROOT_DB_PATH;

$ROOT_DB_PATH = "../_db/";

include "./admin.header.php";

$id = (int)$_GET['id'];
$objectType = $_GET['className'];

$obj = new $objectType($id);
$teams = teams::getTeamsList();

?>

<div class="fluid-row width-90 alignleft no-padding">
    <form action="listn.games.php" id="subForm" method="post">
        <input type='hidden' name='className' value='<?php echo $objectType; ?>' />
        <input type='hidden' id='submitType' name='submitType' value='1' />
        <input type='hidden' id='id' name='id' value='<?php echo $id; ?>' />

        <div class="fluid-row no-padding">
            <button type="button" class="ui-buttons dark" id="submitButton">Save Changes</button>
            <button type="button" class="ui-buttons dark" id="markBye">Mark as "Bye"</button>
        </div>

        <div class="fluid-row slim"> </div>

        <div class="fluid-row slim">
            <label for="away_team">Away Team: </label>
            <select id="away_team" name="away_team" class="float-right">
                <option value="0" >Away Team</option>
                <?php
                    foreach($teams as $value){

                        if($obj->away_team == $value->id)
                            echo "<option value='{$value->id}' selected>{$value->team_name}</option>";
                        else
                            echo "<option value='{$value->id}'>{$value->team_name}</option>";
                    }
                ?>
            </select>
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="username">Away Score: </label> <input type="text" class="float-right" id="away_score" name="away_score" value="<?php echo $obj->away_score; ?>" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row aligncenter slim">
            @
        </div>

        <div class="fluid-row slim">
            <label for="paypal">Home Score: </label> <input type="text" class="float-right" id="home_score" name="home_score" value="<?php echo $obj->home_score; ?>" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="home_team">Home Team: </label>
            <select id="home_team" name="home_team" class="float-right">
                <option value="0" >Home Team</option>
                <?php
                foreach($teams as $value){

                    if($obj->home_team == $value->id)
                        echo "<option value='{$value->id}' selected>{$value->team_name}</option>";
                    else
                        echo "<option value='{$value->id}'>{$value->team_name}</option>";
                }
                ?>
            </select>
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row aligncenter slim">
            <hr/>
        </div>

        <div class="fluid-row slim">
            <label for="date_start">Game Date: </label> <input type="date" class="float-right" no-default id="date" name="date" value="<?php echo $obj->date; ?>" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row"></div>

    </form>
</div>

<script>

    $(document).ready(setTimeout(dateTimeFormat(),200));

    $(document).on("mousedown", "#submitButton", function (e) {

        $("#subForm").submit();

    });

    $(document).on("mousedown", "#markBye", function (e) {

        var $confirm = confirm("Are you sure you want to mark this game as a bye week?");

        if ($confirm == true){
           $("#submitType").val("2");

           $("#subForm").submit();
        }

    });
</script>