<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$season = getSeasons();

?>

<tr>
   <th width="5%">ID</th>
   <th width="10%">Year</th>
   <th>Name</th>
   <th width="5%">Weeks</th>
   <th width="5%">Games</th>
   <th width="20%">Start Date</th>
   <th width="20%">End Date</th>
   
</tr>
<?php
if($season !== false){
    foreach($season as $value){ 
        
        $id = $value['id'];
        
        if(intval($id) === intval(pool::getCurrentSeason())){
            $id .= "*";
        }
        
        $year = $value['year'];
        $name = $value['text_id'];
        $weeks = $value['week_count'];
        $games = $value['game_count'];
        $start_date = date("m/d/Y", strtotime($value['date_start']));
        $end_date = date("m/d/Y", strtotime($value['date_end']));

?>

<tr season="<?php echo $id;?>">
   <td><?php echo $id;?></td>
   <td><?php echo $year;?></td>
   <td><?php echo $name;?></td>
   <td><?php echo $weeks;?></td>
   <td><?php echo $games;?></td>
   <td><?php echo $start_date;?></td>
   <td><?php echo $end_date;?></td>
</tr>
        
<?php 
        }
    } 
?>