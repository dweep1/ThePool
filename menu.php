
<script src="./js/jquery.tinytimer.js"></script>
<script src="./js/pickClock.js?ver=<?php echo VERSION ?>"></script>
<script>
    $(document).ready(function(){
        lockTimer();
        pickCount();
    });
</script>

<nav id="main-nav">

    <div id="pick-clock">
        <div id="lockHold" title="N Days, Hours:Minuets:Seconds">
            <span id="day"></span>
            Picks Lock: <span id="lockClock"></span>
        </div>

        <div class="sep"></div>

        <div id="pickHold"></div>

        <div class="sep"></div>
    </div>

    <ul>
        <li id="expand-menu"><h2><i class="fa fa-bars"style="padding-left:1px;"></i></h2></li>
        <li><a href="./home.php"><h2><i class="fa fa-home" style="padding-left:2px;"></i> Home</h2></a></li>
        <li><a href="./picks.php"><h2><i class="fa fa-check-square-o" style="padding-left:2px;"></i> Picks</h2></a></li>
        <li><a href="./dashboard.php"><h2><i class="fa fa-tachometer" style="padding-left:0px;"></i> Dashboard</h2></a></li>
        <li><a href="./results.php"><h2><i class="fa fa-bar-chart-o" style="margin-left:-1px;"></i> Results</h2></a></li>
        <li class="spacer" style="height:60px; width:60px;"></li>
        <li><a href="./rules.php"><h2><i class="fa fa-gavel" style="padding-left:0px;"></i> Rules</h2></a></li>
        <li><a href="./settings.php"><h2><i class="fa fa-cogs" style="padding-left:0px;"></i> Settings</h2></a></li>
        <li><a href="./logout.php"><h2><i class="fa fa-sign-out" style="padding-left:2px;"></i> Logout</h2></a></li>
    </ul>

</nav>