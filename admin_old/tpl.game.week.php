<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$weeks = getWeeks();

?>

<tr>
   <th width="10%">ID</th>
   <th width="20%">Season ID</th>
   <th width="20%">Week Number</th>
   <th>Start Date</th>
   <th>End Date</th>
</tr>
<?php
if($weeks !== false){
    foreach($weeks as $value){ 
        
        $id = $value['id'];
        
        $season_id = $value['season_id'];
        $week_number = $value['week_number'];
        $start_date = date("m/d/Y", strtotime($value['date_start']));
        $end_date = date("m/d/Y", strtotime($value['date_end']));

?>

<tr week="<?php echo $id;?>">
   <td><?php echo $id;?></td>
   <td><?php echo $season_id;?></td>
   <td><?php echo $week_number;?></td>
   <td><?php echo $start_date;?></td>
   <td><?php echo $end_date;?></td>
</tr>
        
<?php 
        }
    } 
?>