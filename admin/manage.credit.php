
<?php

global $ROOT_DB_PATH;

$ROOT_DB_PATH = "../_db/";

include "./admin.header.php";

$user_id = (int)$_GET['id'];
$user = new users($user_id);

$objectType = $_GET['className'];

$item = new $objectType($id);

FormValidation::generate();

?>


<div id="background" class="clear-fix">
    <div id="overlay" style="width:350px;">

        <div class="help"></div>

        <h3 class="aligncenter"><?php echo $objectType; ?></h3><br/>

        <i id="closePopup" class="right fa fa-times-circle"></i>

        <form action="admin.listener.php" method="post">
            <input type='hidden' name='submitType' value='0' />
            <input type='hidden' name='className' value='<?php echo $objectType ?>' />
            <input data-help='User ID for (<?php echo $user->username ?>)' type='text' name="user_id" value="<?php echo $user_id ?>" />
            <input data-help='Transaction ID' type='text' name="nid" value="xxxxxxxxxxxx" />
            <input data-help='Amount (in USD)' type='text' name="amount" value="10.00" />
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

</script>