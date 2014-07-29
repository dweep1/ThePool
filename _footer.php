<script src="./js/bug_report.js"></script>

<?php

    if(isset($_SESSION['result']))
        unset($_SESSION['result']);

    include "./tpl.debug.php";

?>

</body>
</html>