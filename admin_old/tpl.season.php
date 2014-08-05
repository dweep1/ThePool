<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$season = getSeasons();

?>

<div class="fluid">
    <div class="page-row">
        <h1>Season Management</h1>
    </div>
    <div class="page-row">
    
        <div class="control">
            <div class="half-block">
                <h5>Manage Season</h5>
                <br/><br/>
                <input type="hidden" id="ms-id" name="ms-id" value="-1" />
                <input type="text" id="ms-name" name="ms-name" value="Name" /><i class="icon-rss-sign" title="The Name used to identify the current season"></i><br/>
                <input type="text" id="ms-weeks" name="ms-weeks" value="Week Count" /><i class="icon-time" title="The Number of weeks that the season will run"></i><br/>
                <input type="text" id="ms-game_count" name="ms-game_count" value="Game Count" /><i class="icon-tasks" title="The number of games held during the season, exclusing special games"></i><br/>
                <input type="date" id="ms-start" name="ms-start" value="Date Start" size="30" /><i class="icon-calendar-empty" title="The Start of the seasons first gameday"></i><br/>
                <input type="date" id="ms-end" name="ms-end" value="Date End" size="30" /><i class="icon-lock" title="The Last day of this seasons games"></i><br/>
                <br/>
                <button class="submit" id="season-changes" title="Only new seasons week and game count will change" >Submit Season</button>
                <button class="submit" id="season-lock" title="This will change the current season to the selected season, or if a current season is selected, it will lock/unlock the current season." >Current/Lock Season</button>
                <button class="submit clear">Clear</button><br/>
            </div>
            <div class="full-block">
                <h6>* Indicates Current Season</h6>
                <br/>
                <div class="scrollme">
                   <table id="seasons">
                        
                        <?php 
                        
                            include "./tpl.season.table.php";
                            
                        ?>
                       
                   </table>
               </div>
            </div>
        </div>
        
    </div>

</div>