<?php 

global $user;
$user = true;
global $require_login;
$require_login = true;
global $require_admin;
$require_admin = true;

include "_header.php";
include "./com/admin.funk.php";

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>NFL - The Pool - Admin Section</title>
<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
<link href="./css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
<link href="./css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>

    <header id="header">
        <a href="javascript:void(0)"><img src="./images/pool-logo.png" style="height:32px; vertical-align:middle;width:auto;" /><h3>The Pool Admin</h3></a>
        
        <div class="right con">
			<a href="javascript:resetStats()" style="padding-right:20px; font-size:20px; line-height:18px; vertical-align:middle;" title="Reset Current Season Statistics"><i class="icon-beaker"></i></a>

            <a href="javascript:resetCookies()" style="padding-right:20px; font-size:20px; line-height:18px; vertical-align:middle;" title="Reset This Sites Cookies"><i class="icon-hdd"></i></a>

			<a href="../logout.php"><i class="icon-signout"></i> Logout</a>
        </div>
    </header>
    
    <section class="page-content">
    
        <nav id="menu">
            <span class="menu-item" ajax="tpl.user.php" ><i class="icon-certificate"></i>User Registration<img src="./images/ui/sidebar-menu-arrow.png" class="right arrow" /></span>
            <span class="menu-item" ajax="tpl.control.php" ><i class="icon-group"></i>User Control<img src="./images/ui/sidebar-menu-arrow.png" class="right arrow" /></span>
            <span class="menu-item" ajax="tpl.season.php" ><i class="icon-calendar"></i>Season Management<img src="./images/ui/sidebar-menu-arrow.png" class="right arrow" /></span>
            <span class="menu-item" ajax="tpl.game.php" ><i class="icon-trophy"></i>Game Management<img src="./images/ui/sidebar-menu-arrow.png" class="right arrow" /></span>
			<span class="menu-item" ajax="tpl.pickmgt.php" ><i class="icon-ticket"></i>Pick Management<img src="./images/ui/sidebar-menu-arrow.png" class="right arrow" /></span>
			<span class="menu-item" ajax="tpl.settings.php" ><i class="icon-rocket"></i> Communications<img src="./images/ui/sidebar-menu-arrow.png" class="right arrow" /></span>
        </nav>
    
        <section id="content">
            
        </section>
        
    </section>
    
    <div id="fieldbody"></div>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
    <script src="./js/jquery.cookie.js"></script>
    <script src="./js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="./js/Chart.min.js"></script>
    <script src="./js/admin.js"></script>
    
</body>
</html>