<?php 

global $user;
$user = true;
global $require_login;
$require_admin = true;
global $require_admin;
$require_admin = true;

include "_header.php";
include "./com/admin.funk.php";

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Taskman</title>
<link href="./css/style.css" rel="stylesheet" type="text/css" />
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
</head>
<body>

    <header id="header">
        <a href="#"><h3>Taskman</h3></a>
        
        <div class="right con">
            right content
        </div>
    </header>
    
    <section class="page-content">
    
        <nav id="menu">
            <a href="#"><span class="menu-item selected"><i class="icon-home"></i>Dashboard<img src="./images/ui/sidebar-menu-arrow.png" class="right arrow" /></span></a>
            <a href="#"><span class="menu-item"><i class="icon-user"></i>Users<img src="./images/ui/sidebar-menu-arrow.png" class="right arrow" /></span></a>
        </nav>
    
        <section id="content">
            <div class="fluid">
                <div class="page-row">
                    <h1>Dashboard</h1><h6>general action items</h6>
                </div>
                <div class="page-row">
                    <h3>Projects</h3>
                    
                    <div class="task-area project">
                        <div class="task-item" onclick="javascript:void(0)">
                            <div class="title">Title</div>
                            <div class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas luctus nisi sed sem tempus lobortis pretium ipsum dignissim. Aenean non ligula non massa luctus feugiat. </div>
                            <div class="date">12/12/12</div>
                            <div class="actions">Actions</div> 
                        </div>
                        
                        <div class="task-item" onclick="javascript:void(0)">
                            <div class="title">Current Work</div>
                            <div class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas luctus nisi sed sem tempus lobortis pretium ipsum dignissim. Aenean non ligula non massa luctus feugiat. </div>
                            <div class="date">12/12/12</div>
                            <div class="actions">Delete - Edit - Close</div> 
                        </div>
                        
                        <div class="task-item closed" onclick="javascript:void(0)">
                            <div class="title">Closed Project</div>
                            <div class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas luctus nisi sed sem tempus lobortis pretium ipsum dignissim. Aenean non ligula non massa luctus feugiat. </div>
                            <div class="date">12/12/12</div>
                            <div class="actions">Delete - Edit - Open</div> 
                        </div>
                    </div>
                    
                </div>
                
                <div class="page-row">
                    <h3>Proposals</h3>
                    
                    <div class="task-area proposal">
                    
                        <div class="task-item" onclick="javascript:void(0)">
                        
                            <div class="title">Title</div>
                            <div class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas luctus nisi sed sem tempus lobortis pretium ipsum dignissim. Aenean non ligula non massa luctus feugiat. </div>
                            
                            <div class="lower">
                                <div class="date">12/12/12</div>
                                <div class="actions">Actions</div> 
                            </div>
                            
                        </div>
                        
                        <div class="task-item" onclick="javascript:void(0)">
                        
                            <div class="title">Title</div>
                            <div class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas luctus nisi sed sem tempus lobortis pretium ipsum dignissim. Aenean non ligula non massa luctus feugiat. </div>
                            
                            <div class="lower">
                                <div class="date">12/12/12</div>
                                <div class="actions">Accept - Edit - Decline</div> 
                            </div>
                            
                        </div>
                        
                        <div class="task-item" onclick="javascript:void(0)">
                        
                            <div class="title">Title</div>
                            <div class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas luctus nisi sed sem tempus lobortis pretium ipsum dignissim. Aenean non ligula non massa luctus feugiat. </div>
                            
                            <div class="lower">
                                <div class="date">12/12/12</div>
                                <div class="actions">Accept - Edit - Decline</div>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                    
                    
                </div>
            </div>
        </section>
        
    </section>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    
</body>
</html>