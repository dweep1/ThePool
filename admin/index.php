<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include_once "./admin.header.php";

FormValidation::generate();
$user = users::returnCurrentUser();

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
	<title>The Pool Admin</title>
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet" type="text/css" />
	<link href="./css/redactor.css" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular-animate.min.js"></script>
    <script src="./js/modernizr.js"></script>
    <script defer src="./js/redactor.min.js"></script>
    <script defer src="../js/bug_report.js"></script>
    <script src="../js/general.js"></script>
    <script src="./js/admin.js"></script>
    <script>

        <?php

            if(isset($_SESSION['result'])){
                echo "displayFieldMessage('{$_SESSION['result']}', 10000);";

                unset($_SESSION['result']);
            }

        ?>
    </script>

</head>
<body data-ng-app="myAdmin">

    <div id="fieldbody"></div>

    <div id="loadedContent"></div>

    <?php if($user->verifyAdmin() === false): ?>

        <div class="ui-message-background hidden"></div>
        <div class="ui-message-box aligncenter" data-type="hidden">
            <h6>Admin Login</h6>
            <form action="./admin.login.php" method="post">
                <input type="hidden" name="submitType" value="0" />
                <div class="faux-row"><input type="text" name="password" value="Password" data-password /></div>
                <div class="faux-row"><input type="submit" class="ui-button float-right" value="Submit"></div>
            </form>
        </div>

        <script>

            angular.module('myAdmin', ['ngAnimate']);

        </script>

    </body>
    </html>

    <?php exit; endif; ?>


    <header id="header">

        <h3>Sidekick Admin</h3>
        <div class="right con">
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

    <script>

        angular.module('myAdmin', ['ngAnimate']);

    </script>

</body>
</html>