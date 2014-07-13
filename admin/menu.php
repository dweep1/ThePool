<?php

    global $si;
    global $user;

    @include_once "./admin.header.php";

    $menu_items = new admin_pages();

    $menu_items = $menu_items->getList('id ASC');

    if(isset($_SESSION['sidekick'])){
        $sidekickUser = $_SESSION['sidekick'];
    }else{
        $sidekickUser = getSidekickUser();
    }

?>

<section id="side-nav">

	<nav>
		<ul>
            <?php

                if($menu_items !== false){

                    foreach($menu_items as $menu_item){

                        if(checkPermissions($menu_item, $sidekickUser)){

                            $selected = "";

                            if($si == $menu_item->id)
                                $selected = 'class="selected"';


                            echo "<li data-link=\"index.php?si={$menu_item->id}\" $selected >{$menu_item->icon} {$menu_item->title}</li>";

                        }

                    }
                }

            ?>
		</ul>
	</nav>

</section>