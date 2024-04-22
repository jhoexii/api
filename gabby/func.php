<?php
class functions
{
    var $success_message;
    var $error_message;
	var $siteTitle;
	var $sitename;

	/**
     * Query the database
     *
     * @param $query The query string
     * @return mixed The result of the mysqli::query() function
     */
    public function sql_query($query) {
        // Connect to the database
        $connection = $this -> connect();

        // Query the database
        $result = $connection -> query($query);

        return $result;
    }

	function sql_fetchassoc($query)
	{
		$result = mysqli_fetch_assoc($query);
		return $result;
	}
	
	function sql_numrows($query)
	{
		$result = $query->num_rows;
		return $result;
	}
	
	function CSRFtoken()
	{
		$session = session_id();
		$result = md5($session);
		return $result;
	}
	
	function checkToken($id)
	{
		$chkToken = $this->CSRFtoken();
		$time = time();
		$active = $time + 1999999999800;
		$user_id = $id;
		$query = $this->sql_query("SELECT * FROM users WHERE user_id='".$user_id."'");
		$row = $this->sql_fetchassoc($query);
		$this->sql_query("UPDATE users SET token='', login_status='offline', login_timestamp=0 WHERE login_timestamp<$time");
		if($row['token'] == $chkToken)
		{
			$this->sql_query("UPDATE users SET token='".$chkToken."', login_status='online', login_timestamp='".$active."' WHERE user_id='".$row['user_id']."'");
		}else{
			echo "<script> alert('Opps! Sorry! Your Token is Expired!...'); location.assign('".$this->base_url()."logout.php'); </script>";		
		}
	}
	
    /**
     * Fetch rows from the database (SELECT query)
     *
     * @param $query The query string
     * @return output
     */
    public function select($query) {
        $result = $this -> sql_query($query);
        if($result === false) {
            return false;
        }
		$row = $this -> index_arr_values($result -> fetch_assoc());
        return $row[0];
    }
	
	/**
     * Fetch rows from the database (SELECT query)
     *
     * @param $query The query string
     * @return bool False on failure / array Database rows on success
     */
    public function select_row($query) {
        $rows = array();
        $result = $this -> sql_query($query);
        if($result === false) {
            return false;
        }
        while ($row = $this->sql_fetchassoc($result))  {
            $rows[] = $row;
        }
        return $rows;
    }
	
	public function return_result($query){
		$result = $this->sql_query($query);
		$output=array();
		while ($row = $this->sql_fetchassoc($result)) {
			array_push($output,$row);
		}
		return $output;
	}
    

    /**
     * Quote and escape value for use in a database query
     *
     * @param string $value The value to be quoted and escaped
     * @return string The quoted and escaped string
     */
    
	
	/**
	* Return the values of associative array as indexed array
	*
	*@param associative array
	*@return indexed array
	*/
	public function index_arr_values($value){
		return array_values($value);
	}
	
	/**
	* Return the keys of associative array as indexed array
	*
	*@param associative array
	*@return indexed array
	*/
	public function index_arr_keys($value){
		return array_keys($value);
	}

	/**
	Get Client's IP
	*/
	function getClientIP() {
		if (isset($_SERVER)) {
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
				return $_SERVER["HTTP_X_FORWARDED_FOR"];
			}
			if (isset($_SERVER["HTTP_CLIENT_IP"])){
				return $_SERVER["HTTP_CLIENT_IP"];
			}
			return $_SERVER["REMOTE_ADDR"];
		}
		if (getenv('HTTP_X_FORWARDED_FOR')){
			return getenv('HTTP_X_FORWARDED_FOR');
		}
		if (getenv('HTTP_CLIENT_IP')){
			return getenv('HTTP_CLIENT_IP');
		}
		return getenv('REMOTE_ADDR');
	}
	
	/**
	* Set Session For User Login
	*/
	
	function setLogin($uid,$code){
		$user_info = $this -> select_row("SELECT * FROM `users` WHERE `user_id`=$uid");
		$_SESSION['user']['user_id'] = $uid;
		$_SESSION['user']['username'] = $user_info[0]['username'];
		$time = time();
		$active = $time + 1800;
		$this ->sql_query("UPDATE users 
		SET 
		`ipaddress`='".$this -> getClientIP()."', 
		`login_status`='online',
		`code`='$code'
		WHERE `user_id`='$uid'");
		
	}
	
	/**
	* Calculate time
	*/
	function calc_time($seconds) {
		$hours=0;
		$minutes=0;
		$days = (int)($seconds / 86400);
		$seconds -= ($days * 86400);
		if ($seconds) {
			$hours = (int)($seconds / 3600);
			$seconds -= ($hours * 3600);
		}
		if ($seconds) {
			$minutes = (int)($seconds / 60);
			$seconds -= ($minutes * 60);
		}
		$time = array('days'=>(int)$days,
				'hours'=>(int)$hours,
				'minutes'=>(int)$minutes,
				'seconds'=>(int)$seconds);
		return $time;
	}
	function getdays($seconds) {
		$hours=0;
		$minutes=0;
		$days = (int)($seconds / 86400);
		$seconds -= ($days * 86400);
		if ($seconds) {
			$hours = (int)($seconds / 3600);
			$seconds -= ($hours * 3600);
		}
		if ($seconds) {
			$minutes = (int)($seconds / 60);
			$seconds -= ($minutes * 60);
		}
	$time = (int)$days." Days ".(int)$hours." Hours ".(int)$minutes." Minutes";
		return $time;
	}

	/**
	* Generate Code
	*/
	function ran_code($x) {
		$pwd="";
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i <= $x)
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pwd = $pwd . $tmp;
			$i++;
		}
		return $pwd;
	}
	
	function generate_codes() {
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$pwd = '';
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i <= 4)
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pwd = $pwd . $tmp;
			$i++;
		}
		return $pwd;
	}
	/**
	* Get site settings
	*/
	function site_settings(){
		$variable = $this -> return_result("SELECT * FROM `settings`");
		$settings= array();
		foreach($variable as $setting){
			$settings =array($setting['name']=>$setting['value']) +$settings;
		}
		return $settings;
	}
	
	
    function SetWebsiteTitle($siteTitle)
    {
        $this->siteTitle = $siteTitle;
    }

    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }

    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }

	function base_url()
	{
        $Folder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] .'/';

        return $Folder;
	}

    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }

	function encrypt_key($paswd)
	{
	  $mykey=$this->getEncryptKey();
	  $encryptedPassword=$this->encryptPaswd($paswd,$mykey);
	  return $encryptedPassword;
	}

	// function to get the decrypted user password
	function decrypt_key($paswd)
	{
	  $mykey=$this->getEncryptKey();
	  $decryptedPassword=$this->decryptPaswd($paswd,$mykey);
	  return $decryptedPassword;
	}

	function getEncryptKey()
	{
		$secret_key = md5('jhoe');
		$secret_iv = md5('xii');
		$keys = $secret_key . $secret_iv;
		return $keys;
		//return $this->encryptor('encrypt', $keys);
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
		$secret_key = $this->encrypt_key(md5('jhoe'));
		$secret_iv = $this->encrypt_key(md5('xii'));
		
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
	
	function getBrowser()
	{
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}

		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		elseif(preg_match('/Firefox/i',$u_agent))
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		elseif(preg_match('/Chrome/i',$u_agent))
		{
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		elseif(preg_match('/Safari/i',$u_agent))
		{
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		elseif(preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Opera';
			$ub = "Opera";
		}
		elseif(preg_match('/Netscape/i',$u_agent))
		{
			$bname = 'Netscape';
			$ub = "Netscape";
		}

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}

		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'    => $pattern
		);
	}

    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }

    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = '';
		$errormsg .= "<div class='alert alert-danger'>";
		$errormsg .= "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;";
		$errormsg .= "</button>";
		$errormsg .= "<strong>".nl2br(htmlentities($this->error_message))."</strong>";
		$errormsg .= "</div>";
        return $errormsg;
    }

    function GetSuccessMessage()
    {
        if(empty($this->success_message))
        {
            return '';
        }
        $successmsg = '';
		$successmsg .= "<div class='alert alert-success'>";
		$successmsg .= "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;";
		$successmsg .= "</button>";
		$successmsg .= "<strong>".nl2br(htmlentities($this->success_message))."</strong>";
		$successmsg .= "</div>";
        return $successmsg;
    }

    function HandleSuccess($suc)
    {
		$this->success_message = $suc."\r\n";
    }

    function HandleError($err)
    {
		$this->error_message = $err."\r\n";
    }

    function HandleDBError($err)
    {
        $connection = $this -> connect();
		$this->HandleError($err."\r\n ". mysqli_error($connection). ":");
    }
	public function escape($value) {
        $connection = $this -> connect();
        return  $connection -> real_escape_string($value);
    }
    function SanitizeForSQL($str)
    {
		$connection = $this -> connect();
		
        if( function_exists("mysqli_real_escape_string") )
        {
			$ret_str = $connection->real_escape_string($str);
        }
        else
        {
              $ret_str = addslashes($str);
        }
        return $ret_str;
    }

    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }
	
	function openvpnLogs($log) {
		$handle = fopen($log, "r");
		$uid = 0;
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			unset($match);
			if (preg_match("^Updated,(.+)", $buffer, $match)) { 
				$status['updated'] = $match[1];
			}
			if (preg_match("/^(.+),(\d+\.\d+\.\d+\.\d+\:\d+),(\d+),(\d+),(.+)$/", $buffer, $match)) {
				if ($match[1] <> "Common Name") {
					$cn = $match[1];

					$userlookup[$match[2]] = $uid;

					$status['users'][$uid]['CommonName'] = $match[1];
					$status['users'][$uid]['RealAddress'] = $match[2];
					$status['users'][$uid]['BytesReceived'] = $match[3];
					$status['users'][$uid]['BytesSent'] = $match[4];
					$status['users'][$uid]['Since'] = $match[5];

					$uid++;
				}
			}

			if (preg_match("/^(\d+\.\d+\.\d+\.\d+),(.+),(\d+\.\d+\.\d+\.\d+\:\d+),(.+)$/", $buffer, $match)) {
				if ($match[1] <> "Virtual Address") {
					$address = $match[3];

					$uid = $userlookup[$address];

					$status['users'][$uid]['VirtualAddress'] = $match[1];
					$status['users'][$uid]['LastRef'] = $match[4];
				}
			}

		}

		fclose($handle);

		return($status);
	}

	function sizeformat($bytesize){
		$i=0;
		while(abs($bytesize) >= 1024){
			$bytesize=$bytesize/1024;
			$i++;
			if($i==4) break;
		}

		$units = array("Bytes","KB","MB","GB","TB");
		$newsize=round($bytesize,2);
		return("$newsize $units[$i]");
	}
}
?>