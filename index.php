<!DOCTYPE html>
<html>
<head>

    <title>The Pool</title>
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="./css/index.css" rel="stylesheet" type="text/css" />

</head>
<body class="height-100">

<nav id="main-nav">

    <div id="logo"><h3>TP</h3></div>
    <ul>
        <li><i class="fa fa-bars"></i></li>
    </ul>

</nav>

<div id="content-area" class="height-100">
    <div class="width-50 height-100 fluid-row">
        <h1>Style Guide</h1>
    </div>

    <div class="fluid-row width-50 height-100 dark float-right">
        <h2>Hierarchy</h2>
    </div>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>

    $(document).ready(function(){

        resize();

        $(window).resize(function () {
            resize();
        });

        function resize(){
            var $content = $("#content-area");

            var $height = ($(window).height())*0.976;

            $content.css({"height": $height});
        }
    });

</script>

</body>
</html>