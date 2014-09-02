<!DOCTYPE html>
<html>
<head>

    <title>The Pool - Rules</title>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="./css/index.css?ver=<?php echo VERSION ?>" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
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
    <div class="fluid-row first aligncenter">
        <div class="fluid-row dark width-50 alignleft">
            <div class="fluid-row">
                <h2>Rules</h2>
                <br/>
                <h5>Basics</h5>
            </div>
            <div class="fluid-row slim">
                The concept of the pool is simple: In order of confidence from 16 to 1, pick and number the teams that you think will win each week, with 16 being your highest confidence points (This number based on games played for any given week). You can only use each point value once, in any given week.

                <ul>
                    <li>
                        No points will be subtracted for losses. If two (2) teams are tied at the end of regulation and overtime then no one gets points for that game.  If a game is not played, no points are awarded. There must be a clear-cut winner to receive points for a game.
                    </li>
                </ul>
            </div>

            <div class="fluid-row slim">
                The player, who accumulates the most points each week, wins the week. If two or more players share the highest point total for a week, then the winner is determined by the tie breaker.
                <ul>
                    <li>
                        The “Tie Breaker” will be the “Pick Ratio”, the person who had the most correct picks for that week.
                    </li>
                    <li>
                         If two (2) or more people are tied at the end of all games and has the same “Pick Ratio”.  The weekly pot will be split evenly.
                    </li>
                </ul>
            </div>

            <div class="fluid-row slim">
                A fee of $10 is to be collected a week per entry. The preferred means of paying entry fees is through a <a href="https://www.paypal.com/">PayPal</a> account. Paypal also allows use of a credit/debit card without signing up for an account.  Entry fees must be paid prior to submitting your sheet for that week’s pool.  The system will not take your entry if you have not paid. Ultimately, it’s your responsibility to make arrangements for payment of your entries each week. “NO PAY, NO PLAY…NO EXCEPTIONS”. Therefore its encouraged that you pay multiple weeks in advance.
            </div>

            <div class="fluid-row slim">
                At the end of the football season the person who has the most total points, is the overall winner. The final tie breaker is determined by who has the better overall pick ratio (correct picks / total picks).
                <ul>
                    <li>
                        <i>Jackpot is rolling!!</i> Money will be added to the jackpot from entry fees each week
                    </li>
                    <li>
                        Payout will be as follows: 1st Place Winner will receive 60%, the 2nd Place Winner will receive 25% and 15% will be go towards fees associated with pool.
                    </li>
                </ul>
            </div>

            <div class="fluid-row slim">
                <h5>Making and Changing Entries</h5>
            </div>
            <div class="fluid-row slim">
                When filling in an entry form, you do not have to make a pick for each game. This is helpful if there are early games scheduled for a given week (games played on a Thursday, Friday or Saturday). You may make your picks for these games beforehand and complete the rest later.
                <br/><br/>
                Games are automatically locked out on the entry form according to their scheduled date and time. Early games are locked at the start of the individual game. All remaining games (including the Monday Night Football game) are locked at the scheduled start time of the first Sunday game.
                <br/><br/>
                Note: All times displayed on the schedule is Eastern Standard Time.
                <br/><br/>
                You may change your pick for any game up until the time that game is locked.
                <br/><br/>
                Entries must be completed on time. Once a game is locked, you may not change your pick for it. If you did not make a pick for a particular game, it is counted as a loss. If you submit a partial entry and either forget or are unable to complete it, the games you did not pick will count as losses.
                <br/><br/>
                If you have trouble accessing the site, logging in or completing your entry, please contact the <a class="bug-ui-report" href="javascript:void();">Administrator</a> for help. If you have any questions, please contact the <a class="bug-ui-report" href="javascript:void();">Administrator</a>.
            </div>
        </div>
    </div>
</div>

<?php

include "./_footer.php";

?>

