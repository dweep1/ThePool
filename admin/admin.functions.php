<?php

    include "./admin.header.php";

    function getAdminContent($si){

        $current_page = new admin_pages($si);

        return checkPermissions() ? $current_page->template_file : false;

    }

    function checkPermissions($user = null){

        if($user === null){
            $user = users::returnCurrentUser();

            if($user === false)
                return false;
        }

        return $user->verifyAdmin() ? true : false;

    }

?>