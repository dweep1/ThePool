<?php 

if(!defined('DB_LINK')){
    @include "_header.php";
    @include "./com/admin.funk.php";
}

$keys = getKeys();

?>

<div class="fluid">
    <div class="page-row">
        <h1>User Control</h6>
    </div>
    <div class="page-row">
    
        <div class="control">
            <div class="half-block">
               <table id="userkeys">
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
                    
                   
               </table>
            </div>
            <div class="half-block">
                <h5>User Regestration Links</h5>
                <br/><br/>
                <input type="text" id="generate-reg-link" name="key" value=" " /><i class="icon-link" title="Regestration Link"></i><br/>
                <input type="text" id="link-email" name="email" value="Enter Email (optional)" /><i class="icon-share-alt" title="Email a link to someone"></i><br/>
                <br/>
                <button class="submit" id="generate-link">Generate Link</button><br/>
            </div>
        </div>
        
    </div>

</div>