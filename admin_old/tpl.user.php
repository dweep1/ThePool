<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$keys = getKeys();

?>

<div class="fluid">
    <div class="page-row">
        <h1>User Regestration</h1>
    </div>
    <div class="page-row">
    
        <div class="control">
            
            <div class="half-block">
                <h5>User Regestration Links</h5>
                <br/><br/>
                <input type="text" id="generate-reg-link" name="key" value=" " /><i class="icon-link" title="Regestration Link"></i><br/>
                <input type="text" id="link-email" name="email" value="Enter Email (optional)" /><i class="icon-share-alt" title="Email a link to someone"></i><br/>
                <br/>
                <button class="submit" id="generate-link">Generate Link</button><br/>
            </div>
            <div class="half-block">
               <div class="scrollme">
                   <table id="userkeys">
                        
                        <?php 
                        
                            include "./tpl.user.table.php";
                            
                        ?>
                       
                   </table>
               </div>
            </div>
        </div>
        
    </div>

</div>