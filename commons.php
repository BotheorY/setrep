<?php

include_once ('utilities.php');

$conn = null;

function get_api_call_data() {
    
    $data = file_get_contents('php://input');

    if ($data) {
        $data = json_decode($data, true);
        if ((empty($data)) || (!isset($data['token'])))
            $data = null;
    }
    
    if ((!$data) && (!empty($_POST)) && isset($_POST['token']))
        $data = $_POST;

    if ((!$data) && (!empty($_GET)) && isset($_GET['token'])) {
        $data = $_GET;
    }    
    
    if ($data && (!isset($data['app'])))
        $data = null;

    return $data;

}

function chk_app_code($app_code) {

    global $conn;

    if ($conn) {
        $app_code = trim($app_code);
        if ($app_code) {
            $app_code = "'" . normalize_sql_str($app_code, $conn) . "'";
        } else {
            $app_code = "NULL";
        }
		$sql =  "SELECT * FROM app WHERE app_code = $app_code";
        $queryres = $conn->query($sql);
        if ($queryres && ($queryres->num_rows > 0))
            return true;
    } else {
        throw new Exception("SetRep DB connection failed");
    }

    return false;

}
