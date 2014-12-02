

<div class="width-90 full fluid-row aligncenter">
    <?php

        if(class_exists("users")){
            $user = users::returnCurrentUser();

            if(is_object($user)){
                if((int) $user->user_level === 0)
                    echo "<a href='./admin/index.php'>Enter Admin Area</a>";
            }

        }

        if(isset($_SESSION['result']))
            unset($_SESSION['result']);

    ?>
</div>

</body>
</html>