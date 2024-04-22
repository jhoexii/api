<?php
date_default_timezone_set("Asia/Manila");
$dbhost = '107.152.37.75';
$dbuser = 'gabby';
$dbpass = '2023';
$dbname = 'gabby';
header('Content-Type: application/json');
$connection = new MySQLi($dbhost, $dbuser, $dbpass, $dbname);
if ($connection->connect_errno)
{
    printf("Failed to connect to database");
    exit();
}

if (isset($_GET['username'], $_GET['password'], $_GET['device_id'], $_GET['device_model']))
{

    function calc_time($seconds)
    {
        $hours = 0;
        $minutes = 0;
        $days = (int)($seconds / 86400);
        $seconds -= ($days * 86400);
        if ($seconds)
        {
            $hours = (int)($seconds / 3600);
            $seconds -= ($hours * 3600);
        }
        if ($seconds)
        {
            $minutes = (int)($seconds / 60);
            $seconds -= ($minutes * 60);
        }
        $time = array(
            'days' => (int)$days,
            'hours' => (int)$hours,
            'minutes' => (int)$minutes,
            'seconds' => (int)$seconds
        );
        return $time;
    }
    $username = $_GET['username'];
    $password = $_GET['password'];
    $deviceid = $_GET['device_id'];
    $devicemodel = $_GET['device_model'];
    $get_result = $connection->query("SELECT COUNT(*) FROM users where username='$username' AND userpass='$password'")->fetch_array(MYSQLI_NUM);
    if ($get_result[0] != 0)
    {
        $user = $_GET['username'];
        $pass = $_GET['password'];
        $id = $_GET['device_id'];
        $model = $_GET['device_model'];
        $PRE = "username='$user' AND userpass='$pass' AND freeze='no' AND duration > 0";
        $VIP = "username='$user' AND userpass='$pass' AND freeze='no' AND vip_duration > 0";
        $chckrow = $connection->query("SELECT * FROM users WHERE $PRE OR $VIP");

        $row = $chckrow->fetch_assoc();

        if ($row['vip_duration'] > 0)
        {
            $myrow = $row['vip_duration'];
        }
        elseif ($row['vip_duration'] == 0 and $row['duration'] > 0)
        {
            $myrow = $row['duration'];
        }

        $dur = calc_time($myrow);
        $pdays = $dur['days'] . " days";
        $phours = $dur['hours'] . " hours";
        $pminutes = $dur['minutes'] . " minutes";
        $pseconds = $dur['seconds'] . " seconds";
        if ($myrow == 0)
        {
            $get_duration = "none";
        }
        else
        {
            $myduration = strtotime($pdays . $phours . $pminutes . $pseconds);
            $get_duration = date('Y-m-d h:i:s', $myduration);
        }
		if ($row['vip_duration'] == 0 and $row['duration'] == 0 or $row['freeze']=='yes')
        {
			$json_data = array(
					"auth" => true,
					"expiry" => 'none',
					"device_match" => false
				);
			echo json_encode($json_data);
		}else if ($row['user_level'] != 'Normal' and $row['iac_duration'] == 0)
        {
			$json_data = array(
					"auth" => true,
					"expiry" => 'none',
					"device_match" => false
				);
			echo json_encode($json_data);
		}else{
			$deviceidrow = $row['device_id'];
			if ($deviceidrow == '' or $deviceidrow == $id)
			{
				$json_data = array(
					"auth" => true,
					"expiry" => $get_duration,
					"device_match" => true
				);
				$update = $connection->query("UPDATE users SET `device_id`='" . $id . "',`device_model`='" . $model . "' WHERE username='$user' AND userpass='$pass'");
				echo json_encode($json_data);
			}
			else
			{
				$json_data = array(
					"auth" => false,
					"expiry" => 'none',
					"device_match" => false
				);
				echo json_encode($json_data);

			}
		}

    }
    else
    {
        $json_data = array(
            "auth" => false,
            "expiry" => 'none',
            "device_match" => 'none'
        );
        echo json_encode($json_data);

    }

}
else
{
    $json_data = array(
        "auth" => false,
        "expiry" => 'none',
        "device_match" => 'none'
    );
    echo json_encode($json_data);

}

$connection->close();

?>
