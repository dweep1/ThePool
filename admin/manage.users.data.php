<?php

include "./admin.header.php";

$id = (int)$_GET['id'];
$objectType = $_GET['className'];

$user = new $objectType($id);
$teams = teams::getTeamsList();

?>

<div class="fluid-row width-90 alignleft">
    <form action="listn.users.php" id="subForm" method="post">
        <input type='hidden' name='className' value='<?php echo $objectType; ?>' />
        <input type='hidden' id='submitType' name='submitType' value='1' />
        <input type='hidden' id='id' name='id' value='<?php echo $user->id; ?>' />

        <div class="fluid-row slim">
            <button type="button" class="ui-buttons dark" id="submitButton">Save Changes</button>
            <button type="button" class="ui-buttons dark" id="deleteUser">Delete</button>
        </div>

        <div class="fluid-row slim">
            <label for="username">Username: </label> <input type="text" class="float-right" no-default id="username" name="username" value="<?php echo $user->username; ?>" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="email">Email: </label> <input type="text" class="float-right" no-default id="email" name="email" value="<?php echo $user->email; ?>" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="paypal">Paypal Email: </label> <input type="text" class="float-right" no-default id="paypal" name="paypal" value="<?php echo $user->paypal; ?>" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="password">New Password: </label> <input type="password" class="float-right" no-default id="password" name="password" value="" />
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="favorite_team_id">Favorite Team: </label>
            <select id="favorite_team_id" name="favorite_team_id" class="float-right">
                <option value="0" >Favorite Team</option>
                <?php
                foreach($teams as $value){

                    if($user->favorite_team_id == $value->id)
                        echo "<option value='{$value->id}' selected>{$value->city} {$value->team_name}</option>";
                    else
                        echo "<option value='{$value->id}'>{$value->city} {$value->team_name}</option>";
                }
                ?>
            </select>
            <div class="clear-fix"></div>
        </div>

        <div class="fluid-row slim">
            <label for="access_level">Ghost Mode</label>
            <input type="checkbox" id="access_level" name="access_level" value="-1" <?php if(intval ($user->access_level) === -1) echo "checked"; ?>>
            <br/><br/>
            <label for="user_level">Admin</label>
            <input type="checkbox" id="user_level" name="user_level" value="0" <?php if(intval ($user->user_level) === 0 && $user->user_level !== null) echo "checked"; ?>>
        </div>

        <div class="fluid-row"></div>

    </form>
</div>

<script>
    $(document).on("mousedown", "#submitButton", function (e) {

        $value = parseInt($("#id").val()) || 0;

        if ($value <= 0)
            $("#submitType").val("0");

        $("#subForm").submit();

    });
    $(document).on("mousedown", "#deleteUser", function (e) {

        var $confirm = confirm("Are you sure you want to delete <?php echo $user->username; ?>?");

        if ($confirm == true){
           $("#submitType").val("2");

           $("#subForm").submit();
        }

    });
</script>