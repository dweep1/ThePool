<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$users = getUsers();
$teams = getTeams('city', 'asc');

?>

<div class="fluid">
    <div class="page-row">
        <h1>User Control</h1>
    </div>
    <div class="page-row">
    
        <div class="control">
            <div class="half-block">
                <h3>Selected User</h3>
                <br/><br/>
                <input type="hidden" id="uc-user_id" name="uc-user_id" value="-1" />
                <input type="text" id="uc-username" name="uc-username" value="Username" /><label for="uc-username"><i class="icon-user" title="Username that the use logs into"></i></label><br/>
                <input type="text" id="uc-email" name="uc-email" value="Email" /><label for="uc-email"><i class="icon-envelope" title="User Email used to contact the user"></i></label><br/>
                <input type="text" id="uc-pass" name="uc-pass" value="New Password" /><label for="uc-pass"><i class="icon-barcode" title="Enter a new password to overwrite the current password or leave it blank"></i></label><br/><br/>
                <select id="uc-team" name="uc-team">
                    <option value="0">Favorite Team</option>
            	    <?php 
                        foreach($teams as $value){
    
            	            $name = $value['team_name'];
            	            $city = $value['city'];
            	            $id =  $value['id'];
            	            
            	            echo "<option value=\"$id\">$city $name</option>";
            	    
            	        }
            	    ?>
	            </select><label for="uc-team"><i class="icon-lemon" title="The Users Favorite Team"></i></label><br/><br/>
                <label title="Ghost Mode will hide the user from any public stats list,&#013;While they still can play the game." for="uc-access">Ghost Mode</label>
                <input type="checkbox" id="uc-access" name="uc-access"><br/>
                <label title="Admin will give them access to this back-end through the backend login" for="uc-admin">Admin</label>
                <input type="checkbox" id="uc-admin" name="uc-admin"><br/>
                <br/><br/>
                <button class="submit" id="submit-changes">Submit Info</button><button class="submit" id="submit-delete">Delete</button><button class="submit clear">Clear</button><br/>
                
            </div>
            <div class="half-block">
               <div class="scrollme">
                   <table id="usercontrol">
                        
                        <?php 
                        
                            include "./tpl.control.table.php";
                            
                        ?>
                       
                   </table>
               </div>
            </div>
        </div>

    </div>

</div>