<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$week_id = pool::getSelectedWeekID();

if($week_id !== false){
    $games = pool::getGamesByWeek($week_id);
}else{
    $games = getGames();
}

?>

<tr>
    <th width="5%">ID</th>
    <th style="max-width:100px;">Season</th>
    <th width="5%">Week</th>
    <th width="30%">Home Team</th>
    <th width="5%">Home Score</th>
    <th width="5%"></th>
    <th width="5%">Away Score</th>
    <th width="30%">Away Team</th>
    <th width="10%">Date</th>  
</tr>

<?php

if($games !== false){
    foreach($games as $value){ 
        
        $id = $value['id'];
        $season_name = pool::getSeasonID($value['season_id']);
        $week_number = pool::getWeekNumber($value['week_id']);
        $home = pool::getTeamName($value['home_team']);
        $away = pool::getTeamName($value['away_team']);
        $h_score = $value['home_score'];
        $a_score = $value['away_score'];
        $date = date("D, m/d", strtotime($value['date']));
        
        if(intval($value['winning_team']) == -1){
            ?>
            
<tr game="<?php echo $id;?>">
    <td><?php echo $id;?></td>
    <td colspan="1">BYE WEEK</td>
    <td colspan="3"><?php echo $home;?></td>
    <td colspan="3"><?php echo $away;?></td>
    <td><?php echo $date;?></td>
</tr>
            
            <?php
        }else{

?>

<tr game="<?php echo $id;?>">
    <td><?php echo $id;?></td>
    <td style="max-width:100px;"><?php echo $season_name;?></td>
    <td><?php echo $week_number;?></td>
    <td><?php echo $home;?></td>
    <td><?php echo $h_score;?></td>
    <td>vs</td>
    <td><?php echo $a_score;?></td>
    <td><?php echo $away;?></td>
    <td><?php echo $date;?></td>
</tr>
        
<?php 
            }
        }
    } 
?>