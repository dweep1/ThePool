<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

?>

<div class="fluid">
    <div class="page-row">
        <h2>User Communications</h2>
    </div>
    <div class="page-row">
    
        <div class="control">
            <div class="half-block">
                <h3>Send Notification Email</h3>
                <br/><br/>
                <textarea name="email-messag" id="email-message">Enter An Extra Message To Send to Recipients</textarea>
				<br/><br/>
                <select id="email-group" name="email-group">
                    <option value="0">Select Recipients</option>
					<option value="0">Users w/ Recent Picks (Past 3 Weeks)</option>
					<option value="1">Users w/ Picks (Past Season)</option>
					<option value="-1">Testing</option>
	            </select><label for="uc-team"><br/><br/>
                <br/><br/>
                <button class="submit" id="submit-email">Submit Info</button><br/>
                
            </div>
        </div>

    </div>

</div>