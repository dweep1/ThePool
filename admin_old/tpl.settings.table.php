<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$users = getUsers();

?>

<tr>
   <th width="5%">ID</th>
   <th width="20%">Username</th>
   <th>Email</th>
   <th width="30%">Favorite Team</th>
   <th width="5%" style="cursor:help;" title="Ghost Mode will hide the user from any public stats list,&#013;While they still can play the game." >Ghost</th>
</tr>

<?php
if($users !== false){
    foreach($users as $value){ 

        $email = $value['email'];
        $id = $value['id'];
        $favorite_team = getFavoriteTeam($value['favorite_team_id']);
        $username = $value['username'];
        $access = $value['access_level'];

        if(strlen($favorite_team) < 3){
            $favorite_team = "None Selected";
        }
        
        if(intval($access) === -1){
            $access = "Yes";
        }else{
            $access = "No";
        }
        
       

?>

<tr user="<?php echo $id;?>">
   <td><?php echo $id;?></td>
   <td><?php echo $username;?></td>
   <td><?php echo $email;?></td>
   <td><?php echo $favorite_team;?></td>
   <td><?php echo $access;?></td>
</tr>
        
<?php 
        }
    } 
?>