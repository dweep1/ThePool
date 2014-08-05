<?php 

    if(!defined('DB_LINK')){
        @include "_header.php";
        @include "./com/admin.funk.php";
    }
    
    $season = getSeasons();
    $weeks = getWeeks();
    $games = getGames();
    $teams = getTeams('city', 'asc');
    
    $includes;
    $title;
    $button = false;
           
    if(isset($_COOKIE['LOGOSselected_season'])){
       
       if(isset($_COOKIE['LOGOSselected_week'])){
       
           $includes = "./tpl.game.table.php";
           $title = "Game Selection";
           $button = true;
           
       }else{
           
           $title = "Week Selection";
           $includes = "./tpl.game.week.php";
           $button = true;
           
       }
       
    }else{
       
       $title = "Season Selection";
       $includes = "./tpl.game.season.php";
       
    }

?>

<div class="fluid">
    <div class="page-row">
        <h1>Game Management</h1>
    </div>
    <div class="page-row">
        <div class="control">
            <?php if($title ==  "Game Selection"): ?>
                <script defer>
                    var datefield = document.createElement("input");
        		    datefield.setAttribute("type", "date");
        		    
        			if (datefield.type != "date"){ //if browser doesn't support input type="date", initialize date picker widget:
        				$("#mg-date").datepicker();
        			}
                </script>
                <div class="half-block">
                    <h5>Manage Game</h5>
                    <br/><br/>
					<input type="hidden" id="mg-id" name="ms-id" value="-1" />

					<select id="mg-away" name="mg-away">
						<option value="0">Away Team</option>
						<?php
						foreach($teams as $value){

							$name = $value['team_name'];
							$city = $value['city'];
							$id =  $value['id'];

							echo "<option value=\"$id\">$city $name</option>";

						}
						?>
					</select><label for="mg-away"><i class="icon-plane" title="The Away Team"></i></label><br/>
					<input type="text" id="mg-awayScore" name="mg-awayScore" value="Away Score" /><i class="icon-reorder" title="Away Score"></i><br/>
	                <h6>vs</h6><br/><br/>
					<select id="mg-home" name="mg-home">
						<option value="0">Home Team</option>
						<?php
						foreach($teams as $value){

							$name = $value['team_name'];
							$city = $value['city'];
							$id =  $value['id'];

							echo "<option value=\"$id\">$city $name</option>";

						}
						?>
					</select><label for="mg-home"><i class="icon-home" title="The Home Team"></i></label><br/>
					<input type="text" id="mg-homeScore" name="mg-homeScore" value="Home Score" /><i class="icon-reorder" title="Home Score"></i><br/>
                    <h6>on</h6><br/>
                    <input type="date" id="mg-date" name="mg-date" value="Date" size="30" /><i class="icon-bullseye" title="Game Day"></i><br/>
                    <br/>
                    <button class="submit" id="game-change" title="Change the Games Stats" >Submit Game</button>
                    <button class="submit" id="game-bye" title="Mark this game as a BYE game for two teams that don't play this week" >Bye Game</button>
                    <button class="submit clear">Clear</button><br/>
                </div>
            <?php endif; ?>
            <div class="full-block">
               
               <h4><?php echo $title;?></h4>
               <?php if($button): ?>
               <button class="submit" onclick="backOffGM()">Back</button><br/>
               <?php endif; ?>
               <br/>
               <div class="scrollme">
                   <table id="gamet">
                        
                        <?php 
                        
                        include $includes;
                            
                        ?>
                       
                   </table>
               </div>
               <br/>
               
            </div>
            
            
        </div>
        
    </div>

</div>