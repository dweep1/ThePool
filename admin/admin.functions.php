<?php

    include_once "./admin.header.php";

    function getAdminContent($si){

        $current_page = new admin_pages($si);

        if(checkPermissions($current_page))
            return $current_page->template_file;

        return false;

    }

    function getPermission($name){

        $permission = new permissions();
        $permission->load($name, array('type' => 'name'));


    }

    function checkPermissions($page, $user = null){

        $uLevel = 0; //user level
        $pLevel = 0; //page level

        if($user == null){

            $user = getSidekickUser();

        }

        if(is_array($user) && isset($user['permission_level'])){
            $uLevel = $user['permission_level'];
        }else if (is_object($user) && isset($user->permission_level)){
            $uLevel = $user->permission_level;
        }else{
            return false;
        }

        if(is_array($page) && isset($page['permission_level'])){
            $pLevel = $page['permission_level'];
        }else if (is_object($page) && isset($page->permission_level)){
            $pLevel = $page->permission_level;
        }else{
            return false;
        }

        if($uLevel >= $pLevel)
            return true;

        return false;

    }

    function getSidekickUser($forum_id = null){

        if($forum_id == null){

            global $user;

            $forum_id = $user->data['user_id'];

        }

        $matching_user = new users();
        $matching_user->load($forum_id, array('type' => 'forum_id'));

        if($matching_user !== false)
            return $matching_user->toArray();

        return false;

    }

?>