<?php

    include "./bootstrap.php";

    $user = users::returnCurrentUser();

    if($user === false || !$user->verifyAuth())
        header("Location: ./logout.php");

    $creditCost = options::loadSingle(["name" => "credit_cost"]);

    $thisWeek = week::getCurrent();

?>
<!DOCTYPE html>
<html>
<head>

    <title>The Pool</title>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="./css/index.css?ver=<?php echo VERSION ?>" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>
    <script>

        week_id = <?php echo $thisWeek->id; ?>;

    </script>

</head>
<body class="height-100" data-ng-app="myHome">

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

<?php

    $validCredit = credit::validCredit($user->id);

    if($validCredit === false)
        $validCredit = credit::validCredit($user->id, -1);

?>


<div id="content-area" data-ng-controller="RowController">
    <div class="width-50 fluid-row first">

        <div class="fluid-row aligncenter">
            <?php if(season::getCurrent()->type !== "playoff"){ ?>
                <h5>Pool Size: ~$<?php echo week::getPoolAmount($thisWeek->id); ?></h5>
            <?php }else{ ?>
                <h5>Pool Size: ~$<?php echo week::getPoolAmount(false, true); ?></h5>
            <?php } ?>
            Remaining Credits: <?php echo credit::getCreditCount(null, -1); ?><?php if($validCredit !== false): ?><b><a href="./settings.php" style="padding:10px;">Buy Credits</a></b><?php endif; ?>
        </div>

        <?php
            if($validCredit === false):
        ?>

            <div class="fluid-row aligncenter">
                <div class="fluid-row slim">
                    You have no valid credits to spend on this week.<br/>
                    In order to place your picks, please buy a credit first.</div>
                <div class="fluid-row slim">
                    <b>Click the below button to buy a credit</b><br/>
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="PayPalForm" name="PayPalForm"  target="_top">
                        <input type="hidden" name="cmd" value="_xclick">
                        <input type="hidden" name="business" value="harr8142@bellsouth.net">
                        <input type="hidden" name="amount" value="<?php echo $creditCost->value; ?>">
                        <input type="hidden" name="undefined_quantity" value="1">
                        <input type="hidden" name="item_name" value="Credit Week - The Pool">
                        <input type="hidden" name="item_number" value="<?php echo $user->pay_key; ?>">
                        <input type="hidden" name="custom" value="<?php echo $user->pay_key; ?>">
                        <input type="hidden" name="currency_code" value="USD">
                        <input type="hidden" name="cancel_return" value="http://<?php echo $_SERVER['HTTP_HOST'] ?>/settings.php?success=false">
                        <input type="hidden" name="return" value="http://<?php echo $_SERVER['HTTP_HOST'] ?>/settings.php?success=true">
                        <input type="hidden" name="notify_url" value="http://<?php echo $_SERVER['HTTP_HOST'] ?>/_listeners/paypal_ipn.php">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                    </form>
                </div>
            </div>

            <script src="./js/pick.js?ver=<?php echo VERSION ?>"></script>

        <?php
            elseif($validCredit !== false):
        ?>

            <?php

                if(season::getCurrent()->type !== "playoff" ):

            ?>

            <div class="fluid-row slim alignleft">
                <h6>Search:</h6> <input type="text" data-ng-model="search" />
                <button class="ui-button dark float-right" ng-click="doSave()">Save Picks</button>
                <button class="ui-button dark float-right" ng-click="doRefresh()">Discard Changes</button>
            </div>

            <div class="fluid-row slim float-right">
                <div id="changeBox">You have unsaved picks.</div>
            </div>

            <div class="fluid-row slim alignleft" ng-cloak>

                <h6 class="remaining">Remaining Numbers: <b data-ng-if="remaining.length <= 0">NONE</b> <b data-ng-repeat="item in remaining | orderBy:'value'">{{ item.value }},</b></h6>
                <h5>Closed Picks <i data-trans-for="current_picks" class="fa fa-bars"></i></h5>

                <div class="clear-fix"></div>

                <div data-trans-id="current_picks">

                    <ul class="ui-games-list">

                        <li data-ng-repeat="item in games | filter:search | orderBy:'id'"
                            data-ng-if="item.gameLock != false" data-picked-id="{{ item.pick.team_id }}" data-bad-value="{{ item.pick.bad }}" >

                            <div data-team-id="{{ item.away_team.id }}"
                                 data-ng-class="{true: 'team alignleft picked', false: 'team alignleft'}[item.pick.team_id == item.away_team.id]"
                                 style="background-image: url('{{ item.away_team.image_url }}')">

                                <div class="gradient-left">
                                    <h5>{{ item.away_team.city }}</h5>
                                    <h6>{{ item.away_team.team_name }}</h6>
                                </div>

                            </div>


                            <div class="middle">

                                <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}"
                                       data-ng-model="item.pick.value" data-game-id="{{ item.id }}" disabled />

                            </div>


                            <div data-team-id="{{ item.home_team.id }}"
                                 data-ng-class="{true: 'team alignright float-right picked', false: 'team alignright float-right'}[item.pick.team_id == item.home_team.id]"
                                 style="background-image: url('{{ item.home_team.image_url }}')">

                                <div class="gradient-right">
                                    <h5>{{ item.home_team.city }}</h5>
                                    <h6>{{ item.home_team.team_name }}</h6>
                                </div>

                            </div>

                            <div class="display-date">{{ item.display_date }}</div>

                            <div class="clear-fix"></div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="fluid-row slim alignleft" ng-cloak>

                <h6 class="remaining">Remaining Numbers: <b data-ng-if="remaining.length <= 0">NONE</b> <b data-ng-repeat="item in remaining | orderBy:'value'">{{ item.value }},</b></h6>
                <h5>Open Picks <i data-trans-for="open_picks" class="fa fa-bars"></i></h5>

                <div data-trans-id="open_picks">

                    <ul class="ui-games-list">

                        <li data-ng-repeat="item in games | filter:search | orderBy:'date'"
                            data-ng-if="item.gameLock == false" data-picked-id="{{ item.pick.team_id }}" data-bad-value="{{ item.pick.bad }}" >

                            <div data-ng-click="item.pick.team_id = item.away_team.id;"
                                 data-pick-id="{{ item.pick.id }}" data-team-id="{{ item.away_team.id }}"
                                 data-ng-class="{true: 'team alignleft picked', false: 'team alignleft'}[item.pick.team_id == item.away_team.id]"
                                 style="background-image: url('{{ item.away_team.image_url }}')">

                                <div class="gradient-left">
                                    <h5>{{ item.away_team.city }}</h5>
                                    <h6>{{ item.away_team.team_name }}</h6>
                                </div>

                            </div>


                            <div class="middle">

                                <i class="fa fa-minus-circle" data-game-id="{{ item.id }}"
                                   data-ng-click="item.pick.value = (item.pick.value - 0) - 1;"></i>

                                <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}"
                                       data-ng-model="item.pick.value" data-game-id="{{ item.id }}"  data-ng-change="item.pick.value = (item.pick.value - 0)" ng-delay="500" />

                                <i class="fa fa-plus-circle" data-game-id="{{ item.id }}"
                                   data-ng-click="item.pick.value = (item.pick.value - 0) + 1;"></i>

                            </div>


                            <div data-ng-click="item.pick.team_id = item.home_team.id"
                                 data-pick-id="{{ item.pick.id }}" data-team-id="{{ item.home_team.id }}"
                                 data-ng-class="{true: 'team alignright float-right picked', false: 'team alignright float-right'}[item.pick.team_id == item.home_team.id]"
                                 style="background-image: url('{{ item.home_team.image_url }}')">

                                <div class="gradient-right">
                                    <h5>{{ item.home_team.city }}</h5>
                                    <h6>{{ item.home_team.team_name }}</h6>
                                </div>

                            </div>

                            <div class="display-date">{{ item.display_date }}</div>

                            <div class="clear-fix"></div>

                        </li>
                    </ul>
                </div>

            </div>

            <div class="fluid-row slim alignright">
                <button class="ui-button dark large" ng-click="doSave()">Save Picks</button>
            </div>

            <script src="./js/pick.js?ver=<?php echo VERSION ?>"></script>
            <script>
                $("#changeBox").velocity("fadeOut", { visibility: "hidden", duration: 0});
            </script>

            <?php

                else:

                    //playoff interface
            ?>


            <div class="fluid-row slim alignleft">
                <h6 style="padding-left:10%;">Drag and Drop Picks</h6>
                <button class="ui-button dark float-right" ng-click="doSave()">Save Picks</button>
                <button class="ui-button dark float-right" ng-click="doRefresh()">Discard Changes</button>
            </div>

            <div class="clear-fix"></div>

            <div class="fluid-row slim aligncenter" ng-cloak>
                <div class="playoff-row" ng-class="teamCount >= teams.length ? 'width-65' : 'width-50'">
                    <div class="btn-droppable team-large"
                         ng-show="item.drag"
                         ng-repeat="item in points | orderBy: '-value'"
                         data-drop="{{ item.drag }}"
                         ng-model='item.team'
                         jqyoui-droppable="{multiple:false}">

                        <div class="gradient-left" >
                            <div class="team alignleft">
                                <h6>Pick</h6>
                                <h6>&nbsp;</h6>
                                <h6>&nbsp;</h6>
                            </div>
                            <div class="team-stats alignright">
                                <h1>{{ item.value }}</h1>
                            </div>
                        </div>

                        <div class="btn-draggable team-assign"
                             ng-show="item.team"
                             data-drag="{{ !item.team.locked }}"
                             data-jqyoui-options="{revert: 'invalid'}"
                             ng-model="item.team"
                             jqyoui-draggable="{index: {{$index}}, animate:true}"
                             style="background-image: url('{{ item.team.image_url }}')">

                            <div class="gradient-left" >
                                <div class="team alignleft">
                                    <h5>{{ item.team.city }}</h5>
                                    <h6>{{ item.team.team_name }}</h6>
                                </div>

                                <div class="team-stats alignright">
                                    <h1>{{ item.value }}</h1>
                                </div>
                            </div>
                        </div>

                    </div>

                    <h5 class="aligncenter">Closed Picks</h5>

                    <div class="team-large disabled"
                         ng-show="item.drag == false"
                         ng-repeat="item in points | orderBy: '-value'">

                        <div class="gradient-left" >
                            <div class="team alignleft">
                                <h2>{{ item.value }}</h2>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="width-50 playoff-row">

                    <div class="btn-draggable team-large"
                         ng-repeat="item in teams track by $index"
                         data-drag="{{ !item.locked }}"
                         ng-model="item"
                         ng-show="item"
                         data-jqyoui-options="{revert: 'invalid'}"
                         jqyoui-draggable="{index: {{$index}}, animate:true}"
                         style="background-image: url('{{ item.image_url }}')">

                        <div class="gradient-left" >
                            <div class="team alignleft">
                                <h5>{{ item.city }}</h5>
                                <h6>{{ item.team_name }}</h6>
                            </div>

                            <div class="team-stats alignright">
                                <h6>vs</h6>
                                <h6>{{ item.vs.city }}</h6>
                                <h6>{{ item.vs.team_name }}</h6>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
            <script src="./js/jquery.ui.touch-punch.min.js"></script>

            <script src="./js/angular-dragdrop.min.js"></script>

            <script src="./js/playoffPick.js?ver=<?php echo VERSION ?>"></script>

            <?php
                endif;
            ?>

        <?php
            endif;
        ?>

    </div>

    <div id="team-info" class="fluid-row width-50 dark float-right secondary">

        <?php

            include "./tpl.picks.teaminfo.php";

        ?>

    </div>

    <div class="clear-fix"></div>

</div>

<script src="./js/combiner.php?ver=<?php echo VERSION ?>"></script>


<?php

    include "./_footer.php";

?>
