<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include_once "./admin.header.php";

global $si;

    if(isset($_GET['si']))
        $_SESSION['si'] = $_GET['si'];
    else if(!isset($_SESSION['si']))
        $_SESSION['si'] = 1;

    $si = $_SESSION['si'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Praxus Admin</title>
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet" type="text/css" />
	<link href="./css/redactor.css" rel="stylesheet" type="text/css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular.min.js"></script>
</head>
<body data-ng-app="myApp">

    <div id="fieldbody"></div>

    <div id="loadedContent"></div>

    <header id="header">

        <a href="javascript:void(0)">
            <h3>Sidekick Admin</h3>
        </a>
        <div class="right con">
            <a title="Reset Current Season Statistics" style="padding-right:20px; font-size:20px; line-height:18px; vertical-align:middle;" href="javascript::void(0)"><i class="fa fa-flask"></i></a>
            <a title="Reset This Sites Cookies" style="padding-right:20px; font-size:20px; line-height:18px; vertical-align:middle;" href="javascript::void(0)"><i class="fa fa-hdd-o"></i></a>
            <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
        </div>

    </header>

	<?php
		include "menu.php";
	?>



	<section id="content-area">

        <?php

            $page = getAdminContent($si);

            if($page !== false)
                include "{$page}";

        ?>

	</section>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="./js/modernizr.js"></script>
<script defer src="./js/redactor.min.js"></script>
<script src="./js/admin.js"></script>
<script defer src="../js/bug_report.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular-animate.min.js"></script>
<script>

    angular.module('myApp', ['ngAnimate']);

    <?php

        if(isset($_SESSION['result'])){
            echo "displayFieldMessage('{$_SESSION['result']}', 10000);";

            unset($_SESSION['result']);
        }

    ?>
</script>

</body>
</html>