<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}


$selected_week = pool::getSelectedWeekID();
$user_id = pool::getSelectedUserID();
$picks = pool::getBigPickData($selected_week, $user_id);
$games = getGamesByWeek($selected_week);
$teams = pool::getBigTeamData();

?>

<tr>
    <th width="5%">ID</th>
	<th width="20%">Away Team</th>
	<th width="20%">Home Team</th>
    <th>Points</th>
	<th>Game Date</th>
	<th>Winner</th>
	<th>Result</th>
</tr>
<?php
if($games !== false){
    foreach($games as $value){

        $home = $teams[$value['home_team']];
		$away = $teams[$value['away_team']];

        $id = $value['id'];
		$winner = $teams[$value['winning_team']];
		$pick = false;
		$picked_id = 0;
		$pick_value = 0;
		$result = -1;
		$date = date("D, m/d", strtotime($value['date']));

		if(isset($picks[$id])){
			$pick = $picks[$id];

			$picked_id = $pick['team_id'];
			$pick_value = $pick['value'];

			$result = (intval($pick['result']) !== -1) ? $pick_value*$pick['result'] : "N/A";
		}else{
			$result = "No Pick";
		}

?>

<tr pickrow="" pick_id="<?php echo (isset($pick['id'])) ? $pick['id'] : -1 ; ?>" game_id="<?php echo $id; ?>">
    <td><?php echo $id;?></td>
    <td <?php echo ($picked_id == $away['id']) ? "sel" : ""; ?>><?php echo $away['city'];?></td>
    <td <?php echo ($picked_id == $home['id']) ? "sel" : ""; ?>><?php echo $home['city'];?></td>
    <td><?php echo $pick_value;?></td>
	<td><?php echo $date;?></td>
    <td><?php echo $winner['city'];?></td>
    <td><?php echo $result;?></td>
</tr>
        
<?php 
        }
    } 
?>