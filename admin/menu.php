<?php

    global $si;

    @include "./admin.header.php";

    $menu_items = new admin_pages();

    $menu_items = $menu_items->getList('order_weight ASC');

    if(!checkPermissions(users::returnCurrentUser()))
        exit;

?>

<section id="side-nav">

	<nav>
		<ul>
            <?php

                if($menu_items !== false){

                    foreach($menu_items as $menu_item){

                        $selected = "";

                        if($si == $menu_item->id)
                            $selected = 'class="selected"';


                        echo "<li data-link=\"index.php?si={$menu_item->id}\" $selected >{$menu_item->icon} {$menu_item->title}</li>";

                    }
                }

            ?>
		</ul>
	</nav>

</section>