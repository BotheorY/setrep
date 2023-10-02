<?php

include_once ('commons.php');

error_reporting(E_ALL & ~E_WARNING);
//error_reporting(E_ALL);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

date_default_timezone_set('Europe/Rome');

$output = ['succeeded' => true];
$err = '';

try {
        
    $call_data = get_api_call_data();

    if (!$call_data)
        throw new Exception("Data sent is incomplete or incorrect.");

    if ($conn === null) {
        $db_name = get_env_var('BT_API_SETREP_DB_NAME');
        $db_password = get_env_var('BT_API_SETREP_DB_PWD');
        $db_host = get_env_var('BT_API_SETREP_DB_HOST');
        $db_user = get_env_var('BT_API_SETREP_DB_USER');
        $conn = new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($conn->connect_error) {
            throw new Exception("Connection to database failed: " . $conn->connect_error);
        }   
    }
    
    $user_id = (int)chk_api_token($call_data['token']);

    if (!$user_id) {
        throw new Exception("Wrong token.");
    }

    $app_code = trim($call_data['app']);

    if (!chk_app_code($app_code)) {
        throw new Exception("App code not found.");
    }

    $app_code = "'" . normalize_sql_str($app_code, $conn) . "'";
    $req_ok = false;

    if (!empty($call_data['sectcode'])) {
        $section_code = "'" . normalize_sql_str(trim($call_data['sectcode']), $conn) . "'";
        if (empty($call_data['keycode'])) {
    /*******************************************************************************
    GET SECTION KEYS VALUES LIST
    *******************************************************************************/
            $req_ok = true;
            $sql =  "
                        SELECT 
                            setting_name, 
                            setting_code, 
                            setting_value 
                        FROM 
                            (
                                (
                                    app_sections_settings INNER JOIN app_sections 
                                        ON app_sections_settings.id_app_sections = app_sections.id_app_sections
                                ) INNER JOIN app 
                                    ON app_sections.id_app = app.id_app
                            ) LEFT JOIN app_setting 
                                ON app_sections_settings.id_app_sections_settings = app_setting.id_app_sections_settings 
                        WHERE 
                            (app_code = $app_code) 
                            AND (section_code = $section_code) 
                            AND (ISNULL(id_user) OR (id_user = $user_id)) 
                        ORDER BY 
                            setting_code ASC
                    ";
            $conn->set_charset("utf8mb4");
            $queryres = $conn->query($sql);
            $data = [];
            if ($queryres) {
                foreach ($queryres as $value) {
                    $item = [];
                    $item['name'] = $value['setting_name'];
                    $item['code'] = $value['setting_code'];
                    $item['value'] = $value['setting_value'];
                    $data[] = $item;
                }
            }
            $output['data'] = $data;
        } else {
            $setting_code = "'" . normalize_sql_str(trim($call_data['keycode']), $conn) . "'";
            if (isset($call_data['value'])) {
        /*******************************************************************************
        SET KEY VALUE
        *******************************************************************************/
                $req_ok = true;
                $setting_value = $call_data['value'];
                if ($setting_value) {
                    $setting_value = "'" . normalize_sql_str($setting_value, $conn) . "'";
                } else {
                    $setting_value = 'NULL';
                }
                $sql =  "
                            SELECT 
                                id_app_sections_settings 
                            FROM 
                                (
                                    app_sections_settings INNER JOIN app_sections 
                                        ON app_sections_settings.id_app_sections = app_sections.id_app_sections
                                ) INNER JOIN app 
                                    ON app_sections.id_app = app.id_app
                            WHERE 
                                (app_code = $app_code) 
                                AND (section_code = $section_code) 
                                AND (setting_code = $setting_code) 
                        ";
                $queryres = $conn->query($sql);
                if ($queryres) {
                    foreach ($queryres as $value) {
                        $id_app_sections_settings = $value['id_app_sections_settings'];
                        $sql =  "
                                    INSERT INTO 
                                        app_setting 
                                    (
                                        id_app_sections_settings, 
                                        id_user, 
                                        setting_value
                                    ) VALUES (
                                        $id_app_sections_settings, 
                                        $user_id, 
                                        $setting_value
                                    )
                                    ON DUPLICATE KEY UPDATE 
                                        setting_value = $setting_value
                                ";
                        $conn->set_charset("utf8mb4");
                        if (!$conn->query($sql)) {
                            throw new Exception($conn->error);
                        }
                    }
                }
            } else {
        /*******************************************************************************
        GET KEY VALUE
        *******************************************************************************/
                $req_ok = true;
                $sql =  "
                            SELECT 
                                setting_name, 
                                setting_code, 
                                setting_value 
                            FROM 
                                (
                                    (
                                        app_sections_settings INNER JOIN app_sections 
                                            ON app_sections_settings.id_app_sections = app_sections.id_app_sections
                                    ) INNER JOIN app 
                                        ON app_sections.id_app = app.id_app
                                ) LEFT JOIN app_setting 
                                    ON app_sections_settings.id_app_sections_settings = app_setting.id_app_sections_settings 
                            WHERE 
                                (app_code = $app_code) 
                                AND (section_code = $section_code) 
                                AND (setting_code = $setting_code) 
                                AND (ISNULL(id_user) OR (id_user = $user_id)) 
                        ";
                $conn->set_charset("utf8mb4");
                $queryres = $conn->query($sql);
                $output['data'] = null;
                if ($queryres) {
                    foreach ($queryres as $value) {
                        $output['data'] = $value['setting_value'];
                    }
                }
            }                
        }            
    }

    if (!$req_ok) {
/*******************************************************************************
GET SECTIONS LIST
*******************************************************************************/
        $sql =  "
                    SELECT 
                        section_name, 
                        section_code
                    FROM 
                        app_sections INNER JOIN app 
                            ON app_sections.id_app = app.id_app
                    WHERE 
                        app_code = $app_code 
                    ORDER BY 
                        section_code ASC
                ";
        $queryres = $conn->query($sql);
        $data = [];
        if ($queryres) {
            foreach ($queryres as $value) {
                $item = [];
                $item['name'] = utf8_encode($value['section_name']);
                $item['code'] = $value['section_code'];
                $data[] = $item;
            }
        }
        $output['data'] = $data;
    }

} catch (Exception $e) {
    if ($err)
        $err .= " \n";
    $err .= $e->getMessage();
}

if ($err) {
    $output['succeeded'] = false;
    $output['err'] = $err;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE);

?>