<?php
require_once 'db.php';

	$data = '';
	$query = $db->sql_query("SELECT username, userpass FROM users WHERE (duration > 0 || vip_duration > 0) AND freeze = 'no' ORDER by user_id DESC");
	
	
	while( $row = mysqli_fetch_assoc($query) )
	{
				$data .= '';
				$username = $row['username'];
				$user_pass = $row['userpass'];
				//$data .= '/usr/sbin/userdel -r -f '.$username.' &> /dev/null;'.PHP_EOL;
				$data .= '/usr/sbin/useradd -p $(openssl passwd -1 '.$user_pass.') -s /bin/false -M '.$username.' &> /dev/null;'.PHP_EOL;
				//$data .= '/usr/sbin/useradd -s /bin/false -M '.$username.' &> /dev/null;'.PHP_EOL;
				//$data .=$username.'Pass='.$user_pass.' && echo -e "'.$username.'Pass\n'.$username.'Pass\n"|passwd '.$username.' &> /dev/null;'.PHP_EOL;
	}
	$location = '/var/www/html/api/gabby/app/prem';
	$fp = fopen($location, 'w');
	fwrite($fp, $data) or die("Unable to open file!");
	fclose($fp);
	
	$data2 = '';
			$query2 = $db->sql_query("SELECT username, userpass FROM users WHERE (vip_duration > 0) AND freeze = 'no' ORDER by user_id DESC");
			
			
			while( $row2 = mysqli_fetch_assoc($query2) )
			{
				$data2 .= '';
				$username2 = $row2['username'];
				$user_pass2 = $row2['userpass'];
				//$data2 .= '/usr/sbin/userdel -r -f '.$username2.' &> /dev/null;'.PHP_EOL;
				//$data2 .= '/usr/sbin/useradd -s /bin/false -M '.$username2.' &> /dev/null;'.PHP_EOL;
				//$data2 .=$username2.'Pass='.$user_pass2.' && echo -e "'.$username2.'Pass\n'.$username2.'Pass\n"|passwd '.$username2.' &> /dev/null;'.PHP_EOL;
				$data2 .= '/usr/sbin/useradd -p $(openssl passwd -1 '.$user_pass2.') -s /bin/false -M '.$username2.' &> /dev/null;'.PHP_EOL;
			}
	$location2 = '/var/www/html/api/gabby/app/vip';
	$fp2 = fopen($location2, 'w');
	fwrite($fp2, $data2) or die("Unable to open file!");
	fclose($fp2);
	
	$data3 = '';
        $query3 = $db->sql_query("SELECT username FROM users WHERE (duration <= 0 AND vip_duration <= 0) OR freeze = 'yes' ORDER by user_id DESC");
        
        
        while( $row3 = mysqli_fetch_assoc($query3) )
        {
        	$data3 .= '';
        	$username3 = $row3['username'];
        	$data3 .= '/usr/sbin/userdel -r -f '.$username3.' &> /dev/null;'.PHP_EOL;
        	//$data3 .= 'sh /etc/openvpn/delete_user.sh '.$username3.' &> /dev/null;'.PHP_EOL;
        }
	$location3 = '/var/www/html/api/gabby/app/xprem';
	$fp3 = fopen($location3, 'w');
	fwrite($fp3, $data3) or die("Unable to open file!");
	fclose($fp3);
	
	$data4 = '';
        $query4 = $db->sql_query("SELECT username FROM users WHERE (vip_duration <= 0) OR freeze = 'yes' ORDER by user_id DESC");
        
        
        while( $row4 = mysqli_fetch_assoc($query4) )
        {
        	$data4 .= '';
        	$username4 = $row4['username'];
        	$data4 .= '/usr/sbin/userdel -r -f '.$username4.' &> /dev/null;'.PHP_EOL;
        	//$data4 .= 'sh /etc/openvpn/delete_user.sh '.$username4.' &> /dev/null;'.PHP_EOL;
        }
	$location4 = '/var/www/html/api/gabby/app/xvip';
	$fp4 = fopen($location4, 'w');
	fwrite($fp4, $data4) or die("Unable to open file!");
	fclose($fp4);
	
	$data5 = '';
        $query5 = $db->sql_query("SELECT username FROM users_delete");
        
        while( $row5 = mysqli_fetch_assoc($query5) )
        {
        	$data5 .= '';
        	$username5 = $row5['username'];
        	$data5 .= '/usr/sbin/userdel -r -f '.$username5.' &> /dev/null;'.PHP_EOL;
        }
	$location5 = '/var/www/html/api/gabby/app/deleted';
	$fp5 = fopen($location5, 'w');
	fwrite($fp5, $data5) or die("Unable to open file!");
	fclose($fp5);
?>