<?php
require_once 'db.php';
function encrypt_key($paswd)
	{
	  $mykey=getEncryptKey();
	  $encryptedPassword=encryptPaswd($paswd,$mykey);
	  return $encryptedPassword;
	}
	 
	// function to get the decrypted user password
	function decrypt_key($paswd)
	{
	  $mykey=getEncryptKey();
	  $decryptedPassword=decryptPaswd($paswd,$mykey);
	  return $decryptedPassword;
	}
	 
	function getEncryptKey()
	{
		$secret_key = md5('eugcar');
		$secret_iv = md5('sanchez');
		$keys = $secret_key . $secret_iv;
		return encryptor('encrypt', $keys);
	}
	function encryptPaswd($string, $key)
	{
	  $result = '';
	  for($i=0; $i<strlen ($string); $i++)
	  {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	  }
		return base64_encode($result);
	}
	 
	function decryptPaswd($string, $key)
	{
	  $result = '';
	  $string = base64_decode($string);
	  for($i=0; $i<strlen($string); $i++)
	  {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	  }
	 
		return $result;
	}
	
	function encryptor($action, $string) {
		$output = false;

		$encrypt_method = "AES-256-CBC";
		//pls set your unique hashing key
		$secret_key = md5('eugcar sanchez');
		$secret_iv = md5('sanchez eugcar');

		// hash
		$key = hash('sha256', $secret_key);
		
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		//do the encyption given text/string/number
		if( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		else if( $action == 'decrypt' ){
			//decrypt the given text/string/number
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}

		return $output;
	}


	$data = '';
	$query = $db->sql_query("SELECT user_name, user_pass FROM users WHERE duration > 0 AND is_freeze = 0 || vip_duration > 0 AND is_freeze = 0 ORDER by user_id DESC");
	
	if($query->num_rows > 0)
	{
		while($row = $query->fetch_assoc())
		{
			$data .= '';
			$username = $row['user_name'];
			$password = decrypt_key($row['user_pass']);
			$password = encryptor('decrypt',$password);	
			$data .= '/usr/sbin/useradd -p $(openssl passwd -1 '.$password.') -s /bin/false -M '.$username.' &> /dev/null;'.PHP_EOL;
		
		}
	}
	$location = '/var/www/html/dolor/app/prem';
	$fp = fopen($location, 'w');
	fwrite($fp, $data) or die("Unable to open file!");
	fclose($fp);
	
	
	$data2 = '';
			$query2 = $db->sql_query("SELECT user_name, user_pass FROM users WHERE (vip_duration > 0) AND is_freeze = 0 ORDER by user_id DESC");
			
		if($query2->num_rows > 0)
		{
			while( $row2 = mysqli_fetch_assoc($query2) )
			{
				$data2 .= '';
				$username2 = $row2['user_name'];
				$password2 = decrypt_key($row2['user_pass']);
				$password2 = encryptor('decrypt',$password2);	
			
				$data2 .= '/usr/sbin/useradd -p $(openssl passwd -1 '.$password2.') -s /bin/false -M '.$username2.' &> /dev/null;'.PHP_EOL;
			}
		}
	$location2 = '/var/www/html/dolor/app/vip';
	$fp2 = fopen($location2, 'w');
	fwrite($fp2, $data2) or die("Unable to open file!");
	fclose($fp2);
	
	$data3 = '';
        $query3 = $db->sql_query("SELECT user_name FROM users WHERE (duration <= 0 AND vip_duration <= 0) OR is_freeze = 1 ORDER by user_id DESC");
        
        
        while( $row3 = mysqli_fetch_assoc($query3) )
        {
        	$data3 .= '';
        	$username3 = $row3['user_name'];
        	$data3 .= '/usr/sbin/userdel -r -f '.$username3.' &> /dev/null;'.PHP_EOL;
        	//$data3 .= 'sh /etc/openvpn/delete_user.sh '.$username3.' &> /dev/null;'.PHP_EOL;
        }
	$location3 = '/var/www/html/dolor/app/xprem';
	$fp3 = fopen($location3, 'w');
	fwrite($fp3, $data3) or die("Unable to open file!");
	fclose($fp3);
	
	$data4 = '';
        $query4 = $db->sql_query("SELECT user_name FROM users WHERE (vip_duration <= 0) OR is_freeze = 1 ORDER by user_id DESC");
        
        
        while( $row4 = mysqli_fetch_assoc($query4) )
        {
        	$data4 .= '';
        	$username4 = $row4['user_name'];
        	$data4 .= '/usr/sbin/userdel -r -f '.$username4.' &> /dev/null;'.PHP_EOL;
        	//$data4 .= 'sh /etc/openvpn/delete_user.sh '.$username4.' &> /dev/null;'.PHP_EOL;
        }
	$location4 = '/var/www/html/dolor/app/xvip';
	$fp4 = fopen($location4, 'w');
	fwrite($fp4, $data4) or die("Unable to open file!");
	fclose($fp4);
	
	$data5 = '';
			$query5 = $db->sql_query("SELECT user_name, user_pass FROM users WHERE (private_duration > 0) AND is_freeze = 0 ORDER by user_id DESC");
			
		if($query5->num_rows > 0)
		{
			while( $row5 = mysqli_fetch_assoc($query5) )
			{
				$data5 .= '';
				$username5 = $row5['user_name'];
				$password5 = decrypt_key($row5['user_pass']);
				$password5 = encryptor('decrypt',$password5);	
			
				$data5 .= '/usr/sbin/useradd -p $(openssl passwd -1 '.$password5.') -s /bin/false -M '.$username5.' &> /dev/null;'.PHP_EOL;
			}
		}
	$location5 = '/var/www/html/dolor/app/priv';
	$fp5 = fopen($location5, 'w');
	fwrite($fp5, $data5) or die("Unable to open file!");
	fclose($fp5);
	
	$data6 = '';
        $query6 = $db->sql_query("SELECT user_name FROM users WHERE (private_duration <= 0) OR is_freeze = 1 ORDER by user_id DESC");
        
        
        while( $row6 = mysqli_fetch_assoc($query6) )
        {
        	$data6 .= '';
        	$username6 = $row6['user_name'];
        	$data6 .= '/usr/sbin/userdel -r -f '.$username6.' &> /dev/null;'.PHP_EOL;
        	//$data6 .= 'sh /etc/openvpn/delete_user.sh '.$username6.' &> /dev/null;'.PHP_EOL;
        }
	$location6 = '/var/www/html/dolor/app/xpriv';
	$fp6 = fopen($location6, 'w');
	fwrite($fp6, $data6) or die("Unable to open file!");
	fclose($fp6);
	
	$data7 = '';
        $query7 = $db->sql_query("SELECT user_name FROM users_delete");
        
        while( $row7 = mysqli_fetch_assoc($query7) )
        {
        	$data7 .= '';
        	$username7 = $row7['user_name'];
        	$data7 .= '/usr/sbin/userdel -r -f '.$username7.' &> /dev/null;'.PHP_EOL;
        }
	$location7 = '/var/www/html/dolor/app/deleted';
	$fp7 = fopen($location7, 'w');
	fwrite($fp7, $data7) or die("Unable to open file!");
	fclose($fp7);
?>