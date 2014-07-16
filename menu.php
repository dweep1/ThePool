
<?php

    include_once "./header.php";

    FormValidation::generate();

?>

<nav id="main-nav">

    <div id="logo"><h3>TP</h3></div>
    <ul>
        <li><h2><i class="fa fa-bars" style="padding-left:1px;"></i></h2></li>
        <li><h2 data-icon="" data-id="1"><i class="fa fa-user" style="padding-left:2px;"></i></h2></li>
        <li><h2><i class="fa fa-question-circle" style="padding-left:1px;"></i></i></h2></li>
    </ul>

    <div id="login-area" class="aligncenter hidden" data-menu="" data-menu-id="1">
        <h5>Login</h5>
        <form action="./_listeners/listn.login.php" method="post">
            <div class="faux-row"><input type="text" name="email" value="Email" /></div>
            <div class="faux-row"><input type="text" name="password" value="Password" data-password /></div>
            <div class="faux-row"><input type="hidden" name="confirm" value="Confirm Password" data-password /></div>
            <input type="hidden" name="submitType" value="0" />
            <div class="faux-row">
                <button type="button" class="ui-button float-left" data-button-type="register">Register</button>
                <button class="ui-button float-right">Submit</button>
                <div class="clear-fix"></div>
            </div>
        </form>

    </div>

</nav>

<script>
    setTimeout(function(){
        var $mi = $("[data-menu-id='1']");

        if($mi.hasClass("hidden"))
            toggleMenuItemOverlay($mi);

    },3000);
</script>