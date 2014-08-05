<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$keys = getKeys();

?>

<tr>
   <th width="5%">ID</th>
   <th>Email</th>
   <th width="20%">Link</th>
   <th width="20%">Status</th>
</tr>
<?php
if($keys !== false){
    foreach($keys as $value){ 

        $email = $value['email'];
        $id = $value['id'];
        $link = getKeyFormat($value['auth_key']);
        $status = $value['status'];
        
        if(intval($status) === 0){
            $status = '<button link-id="'.$id.'">Resend</button>';
        }else{
            $status = 'Key Used';
        }
        
        if(strlen ($email) < 6){
            $email = "No Email Given";
            $status = '<button email-id="'.$id.'">Reset</button>';
        }


?>

<tr>
   <td><?php echo $id;?></td>
   <td><?php echo $email;?></td>
   <td><a href="<?php echo $link;?>">Link</a></td>
   <td><?php echo $status;?></td>
</tr>
        
<?php 
        }
    } 
?>