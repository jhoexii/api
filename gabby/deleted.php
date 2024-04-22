<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';

if(isset($_GET['key']) && !empty($_GET['key'])){
    
    $key = $_GET['key'];
    
    if($key = 'Tknetwork') {
        $data = '';
        $query = $db->sql_query("SELECT user_name FROM users_delete");
        
        while( $row = $db->sql_fetchrow($query) )
        {
        	$data .= '';
        	$username = $row['user_name'];
        	$data .= '/usr/sbin/userdel -r -f '.$username.' &> /dev/null;'.PHP_EOL;
        }
        
        echo $data;
    }else{
        echo 'Invalid Key!';
    }
}else{
    echo 'Invalid Key!';
}
?>