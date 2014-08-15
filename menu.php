
<script src="./js/jquery.tinytimer.js"></script>
<script src="./js/pickClock.js"></script>
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

    <div id="logo"><h3>TP</h3></div>

    <ul>
        <li id="expand-menu"><h2><i class="fa fa-bars"style="padding-left:1px;"></i></h2></li>
        <li data-link="./home.php"><h2><i class="fa fa-home" style="padding-left:2px;"></i> Home</h2></li>
        <li data-link="./picks.php"><h2><i class="fa fa-check-square-o" style="padding-left:2px;"></i> Picks</h2></li>
        <li data-link="./dashboard.php"><h2><i class="fa fa-tachometer" style="padding-left:0px;"></i> Dashboard</h2></li>
        <li data-link="./results.php"><h2><i class="fa fa-bar-chart-o" style="margin-left:-1px;"></i> Results</h2></li>
        <li class="spacer" style="height:60px; width:60px;"></li>
        <li data-link="./rules.php"><h2><i class="fa fa-gavel" style="padding-left:0px;"></i> Rules</h2></li>
        <li data-link="./settings.php"><h2><i class="fa fa-cogs" style="padding-left:0px;"></i> Settings</h2></li>
        <li data-link="./logout.php"><h2><i class="fa fa-sign-out" style="padding-left:2px;"></i> Logout</h2></li>
    </ul>

</nav>