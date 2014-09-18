<?php

    include "./_header.php";

    FormValidation::generate();

    $user = users::returnCurrentUser();

    if($user === false || !$user->verifyAuth())
        header("Location: ./logout.php");

    $teams = teams::getTeamsList();

    if(isset($_GET["success"])){
        $_SESSION["result"] = ($_GET["success"] == "true" || $_GET["success"] == true) ? "Successfully Submitted Purchase of The Pool credits.<br/><br/>
        Once the transaction has completed, the credits will be added to your account." : "You have canceled your Purchase of The Pool credits";
    }


?>
<!DOCTYPE html>
<html>
<head>

    <title>The Pool - Settings</title>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="./css/index.css?ver=<?php echo VERSION ?>" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>
    <script src="./js/combiner.php?ver=<?php echo VERSION ?>"></script>

</head>
<body class="height-100">

<?php if(isset($_SESSION['result'])): ?>

    <div class="ui-message-background hidden" data-background-id="1"></div>
    <div class="ui-message-box" data-type="result" data-message-id="1">
        <i class="fa fa-times-circle float-right ui-message-close" data-close-id="1"></i>
        <h5>Result</h5>
        <div class="faux-row"><?php echo $_SESSION['result']; ?></div>
    </div>

<?php endif; ?>

<?php

include "./menu.php";

?>

<div id="content-area">
    <div class="width-50 fluid-row aligncenter settings first">

        <div class="fluid-row aligncenter">
            Remaining Credits: <?php echo credit::getCreditCount(null, -1); ?><br/>

            <div class="fluid-row slim">
                <h6>Purchase Credit</h6><br/><br/>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="PayPalForm" name="PayPalForm"  target="_top">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="harr8142@bellsouth.net">
                    <input type="hidden" name="amount" value="10.00">
                    <input type="hidden" name="undefined_quantity" value="1">
                    <input type="hidden" name="item_name" value="Credit Week - The Pool">
                    <input type="hidden" name="item_number" value="<?php echo $user->pay_key; ?>">
                    <input type="hidden" name="custom" value="<?php echo $user->pay_key; ?>">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="cancel_return" value="http://www.whats-your-confidence.com/settings.php?success=false">
                    <input type="hidden" name="return" value="http://www.whats-your-confidence.com/settings.php?success=true">
                    <input type="hidden" name="notify_url" value="http://www.whats-your-confidence.com/_listeners/paypal_ipn.php">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
            </div>
        </div>

        <form action="./_listeners/listn.settings.php" id="changeUser" method="post">
            <input type="hidden" name="submitType" value="0" />
            <input type="hidden" id="access_level" name="access_level" value="<?php echo $user->access_level; ?>" />

            <div id="errorMe" class="fluid-row width-90 alignleft <?php echo (!isset($_GET['password'])) ? "" : "error"; ?>">
                <h6>Confirm Password</h6>  <input id="confirm_password" type="password" name="confirm_password" autocomplete="off" />
                <button type="button" class="ui-button dark save-button float-right">Save Account Changes</button>
            </div>

            <div class="fluid-row width-90 slim aligncenter">

                <div class="fluid-row"><h4>Add/Edit Account Info</h4></div>

                <div class="fluid-row slim over-90">
                    <label for="username">Username: </label> <input class="float-right" type="text" no-default id="username" name="username" value="<?php echo $user->username; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row slim over-90">
                    <label for="first_name">First Name: </label> <input class="float-right" type="text" no-default id="first_name" name="first_name" value="<?php echo $user->first_name; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row slim over-90">
                    <label for="last_name">Last Name: </label> <input class="float-right" type="text" no-default id="last_name" name="last_name" value="<?php echo $user->last_name; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row slim over-90 <?php echo (strlen($user->paypal) > 3) ? "" : "error"; ?>">
                    <label for="username">Paypal Email: </label> <input class="float-right" type="text" no-default id="paypal" name="paypal" value="<?php echo $user->paypal; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row slim"></div>

                <div class="fluid-row slim over-90">
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

            </div>

            <div class="fluid-row width-90 slim aligncenter">

                <div class="fluid-row"><h4>Change Login Info</h4></div>

                <div class="fluid-row slim over-90">
                    <label for="email">Email: </label> <input type="text" class="float-right" no-default id="email" name="email" value="<?php echo $user->email; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row slim"></div>

                <div class="fluid-row slim over-90">
                    <label for="password">New Password: </label> <input class="float-right" type="password" id="password" name="password" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row slim over-90">
                    <label for="confirm">Confirm Password: </label> <input class="float-right" type="password" id="confirm" name="confirm" />
                    <div class="clear-fix"></div>
                </div>

            </div>

            <div class="fluid-row width-90 slim alignleft">

                <div class="fluid-row alignleft">
                    <button type="button" class="ui-button save-button dark float-right">Save Account Changes</button>
                </div>

            </div>

        </form>


    </div>

    <div class="fluid-row width-50 dark height-100 float-right secondary aligncenter">

        <div class="fluid-row width-90 alignleft">

            <form action="./_listeners/listn.settings.php" id="rivalForm" method="post">
                <input type="hidden" name="submitType" value="1" />
                <input type="hidden" id="hiddenID" name="hiddenID" value="-1" />

                <div class="fluid-row">
                    <h4 style="display:inline-block;">Add Rival</h4>
                    <button class="ui-button float-right">Save Rival</button>
                </div>

                <div class="fluid-row width-60 slim over-90">
                    <label for="username">Username or Email: </label> <input class="float-right" type="text" no-default name="username" value="" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row width-60 slim over-90">
                    <label for="first_name">Custom Name: </label> <input class="float-right" type="text" no-default name="rival_custom_name" value="" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row">
                    <h4>Your Rivals</h4>
                </div>

                <ul class="ui-rank-list">
                    <li class="title">
                        <div class="width-70">Name</div>
                        <div class="width-25">Delete</div>
                    </li>

                    <?php

                    $rivals = new rivals;
                    $rivals = $rivals->getList("id desc", array("user_id" => $user->id));

                    if(!is_bool($rivals)){
                        foreach($rivals as $value){

                            $name = (strlen($value->rival_custom_name) > 2) ? $value->rival_custom_name : $value->rival_name;

                            echo   "<li>
                                            <div class='width-70'>{$name}</div>
                                            <div class='width-25'><i data-rival-id='{$value->id}' class='fa fa-times'></i></div>
                                        </li>";


                        }
                    }

                    ?>

                </ul>
            </form>

        </div>

        <div class="fluid-row width-90 alignleft">

            <div class="fluid-row">
                <h4>Transactions</h4>
            </div>

            <div class="fluid-row width-60 slim over-90">
                Paypal Email: <?php echo (strlen($user->paypal) > 3) ? $user->paypal : "NONE"; ?>
                <br/>
                Remaining Credits: <?php echo credit::getCreditCount(null, -1); ?><br/>
            </div>

            <div class="fluid-row width-60 slim over-90">
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="PayPalForm" name="PayPalForm"  target="_top">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="harr8142@bellsouth.net">
                    <input type="hidden" name="amount" value="10.00">
                    <input type="hidden" name="undefined_quantity" value="1">
                    <input type="hidden" name="item_name" value="Credit Week - The Pool">
                    <input type="hidden" name="item_number" value="<?php echo $user->pay_key; ?>">
                    <input type="hidden" name="custom" value="<?php echo $user->pay_key; ?>">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="cancel_return" value="http://www.whats-your-confidence.com/settings.php?success=false">
                    <input type="hidden" name="return" value="http://www.whats-your-confidence.com/settings.php?success=true">
                    <input type="hidden" name="notify_url" value="http://www.whats-your-confidence.com/_listeners/paypal_ipn.php">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
            </div>

            <ul class="ui-rank-list">
                <li class="title">
                    <div class="width-25">Date</div>
                    <div class="width-25">Used</div>
                    <div class="width-40">Transaction ID</div>
                </li>

                <?php

                    $credits = new credit;
                    $credits = $credits->getList("id desc", array("user_id" => $user->id));

                    foreach($credits as $value){

                        $used = ((int) $value->week_id <= -1) ? "No" : "Yes";
                        $date = new DateTime($value->date);
                        $result = $date->format('D M jS, Y');

                        echo   "<li>
                                    <div class='width-25'>{$result}</div>
                                    <div class='width-25'>{$used}</div>
                                    <div class='width-40'>{$value->nid}</div>
                                </li>";


                    }

                ?>

            </ul>

        </div>


    </div>

    <div class="clear-fix"></div>

</div>

<script>

    $(document).on("mousedown", "[data-rival-id]", function (e) {

        $("#hiddenID").val($(this).attr("data-rival-id"));

        $("#rivalForm").submit();

    });

    $(document).on("mousedown", ".save-button", function (e) {

        var $confirmPW = $("#confirm_password").val();

        if($confirmPW.length <= 3){

            var $error = $("#errorMe");
            $error.addClass("error");

            $('html, body').animate({
                scrollTop: ($error.offset().top)-130
            }, 1000);

            createMessageBox(
                {type: "error", title: "error", message: "In order to your account info, you must first confirm your password."},
                function($messageID){ toggleDisplayMessageBox($messageID);}
            );
        }else{
            $("#changeUser").submit();
        }

    });


    $(document).on("mousedown", "#togglePrivate", function (e) {

        var $level = parseInt($("#access_level").val());

        if($level == 0)
            $("#access_level").val("-1");
        else
            $("#access_level").val("0");


        $("#changeUser").submit();

    });

</script>

<?php

    include "./_footer.php";

?>