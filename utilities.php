<?php

include_once ('constants.php');

$conn_admin = null;

function ab_log($log) {
	
	$now = date('[d/m/Y H:i:s] ');
	$log = $now . $log;
	$log .= "\n\n";
	
	file_put_contents('ab_log.txt', $log, FILE_APPEND);
	
}

function get_token($key, $token, $time_stamp = null) {

	if (!$time_stamp) {
		$time_stamp = time();
	}

	if ($time_stamp % 2 == 0) {
		$token = $token . $time_stamp . $token;
	} else {
		$token = $time_stamp . $token;
	}

	$token = hash("sha256", $token);
	return $time_stamp . '_' . $key . '_' . $token;

}

function get_env_var($var_name, $raise_err = true) {

	$result = getenv($var_name);

	if ($result === false) {
		$result = ini_get($var_name);		
		if ($result === false) {
			if (file_exists('env-vars.php')) {
				require_once('env-vars.php');
				if (function_exists('get_var'))
					$result = get_var($var_name);		
			}
		}
	}

	if ($raise_err && (!$result))
		throw new Exception("Enviroment variable \"$var_name\" not found.");

	return $result;

}

function chk_api_token($api_token) {
    
    global $conn_admin;

    if ($conn_admin === null) {
        $db_name = get_env_var('BT_API_DB_NAME');
        $db_password = get_env_var('BT_API_DB_PWD');
        $db_host = get_env_var('BT_API_DB_HOST');
        $db_user = get_env_var('BT_API_DB_USER');
        $conn_admin = new mysqli($db_host, $db_user, $db_password, $db_name);
    
        if ($conn_admin->connect_error) {
            throw new Exception("Connection to database failed: " . $conn_admin->connect_error);
        }
    }

    $result = false;
    $curr_time_stamp = time();
    $token_parts = explode('_', $api_token);
    $time_stamp = intval($token_parts[0]);
    
    if (($time_stamp >= ($curr_time_stamp - API_TOKEN_TIMESTAMP_SPAN)) && ($time_stamp <= ($curr_time_stamp + API_TOKEN_TIMESTAMP_SPAN))) {
        $user_key = $token_parts[1];
        $sql = "SELECT id_sys_user, api_token FROM sys_user WHERE user_key = '$user_key'";
        if ($conn_admin) {
			$user_token = null;
			$queryres = $conn_admin->query($sql);
			if ($queryres->num_rows > 0) {
				while($row = $queryres->fetch_assoc()) {
					$result = (int)$row["id_sys_user"];
					$user_token = $row["api_token"];
				}
				$test_token = get_token($user_key, $user_token, $time_stamp);
				if ($test_token !== $api_token)
					$result = false;
			}
        } else {
            throw new Exception("API DB connection failed");
        }
    } else {
		throw new Exception("Token timestamp is wrong or expired");
    }

    return $result;

}

function normalize_sql_str($value, $db): string {

    return mysqli_real_escape_string($db, stripslashes($value));

}
