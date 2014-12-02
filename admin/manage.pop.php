<?php


include "./admin.header.php";

$id = (int)$_GET['id'];
$objectType = $_GET['className'];

$item = new $objectType($id);

?>


<div id="background" class="clear-fix">
    <div id="overlay" style="width:350px;">

        <div class="help"></div>

        <h3 class="aligncenter"><?php echo $objectType; ?></h3><br/>

        <i id="closePopup" class="right fa fa-times-circle"></i>

        <form action="admin.listener.php" method="post">

        <?php

            $submitType = 0;

            echo "<input type='hidden' name='className' value='$objectType' />";

            foreach($item->toArray() as $key => $value){

                if($item->loaded === false){
                    $type = "text";

                    if($key === 'id')
                        $type = null;
                    else if (strpos($key,'date') !== false)
                        $type = "date";


                    if($type !== null)
                        echo "<input data-help='$key' type='$type' name='$key' value='{$key}' />";


                }else{

                    $submitType = 1;

                    $type = "text";

                    if($key === 'id')
                        $type = "hidden";
                    else if(Core::isValidDateTimeString($value))
                        $type = "date";

                    if($type !== null)
                        echo "<input data-help='$key' class='non' type='$type' name='$key' value='$value' />";


                }


            }

            echo "<input type='hidden' name='submitType' value='$submitType' />";


        ?>

            <input class="ui-button right" type="submit" value="submit" />
        </form>

    </div>
</div>

<script>


    $("input[type='text']:not([no-default]), input[type='hidden']:not([no-default])").focus(function() {
        if (this.value === this.defaultValue) {
            this.value = '';

            var attr = $(this).attr('data-password');

            if (typeof attr !== typeof undefined && attr !== false) {
                this.type = 'password';
            }
        }
    }).blur(function() {
        if (this.value === '') {
            this.value = this.defaultValue;

            var attr = $(this).attr('data-password');

            if (typeof attr !== typeof undefined && attr !== false) {
                this.type = 'text';
            }
        }
    });

    dateTimeFormat();

</script>