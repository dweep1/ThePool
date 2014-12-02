<?php

global $ROOT_DB_PATH;

$ROOT_DB_PATH = "../_db/";

include_once "./admin.header.php";

$settings = new options();
$settings = $settings->getList();

foreach($settings as $key => $value){
    $settings[$value->name] = $value;
    unset($settings[$key]);
}

?>

<div class="fluid-row width-90 alignleft no-padding">

    <form action="listn.settings.php" id="subForm" method="post">

        <div class="fluid-row no-padding">
            <button class="ui-buttons dark" id="submitButton">Save Changes</button>
        </div>

        <div class="fluid-row slim"> </div>

        <div class="fluid-row slim">
            <label for="credit_weekly_value">Credit Weekly Percentage: </label>
            <input type="text"  no-default name="credit_weekly_value" value="<?php echo $settings["credit_weekly_value"]->value; ?>" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="credit_season_value">Credit Season Percentage: </label>
            <input type="text"  no-default name="credit_season_value" value="<?php echo $settings["credit_season_value"]->value; ?>" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="credit_season_value">Credit Cost: </label>
            <input type="text" no-default name="credit_cost" value="<?php echo $settings["credit_cost"]->value; ?>" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="use_credit">Use Credits:</label>
            <input type="checkbox" name="use_credit" value="1" <?php if(intval($settings["use_credit"]->value) === 1) echo "checked"; ?>>
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