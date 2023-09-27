<?php

function get_var($var_name) {

    switch ($var_name) {
        case 'BT_API_SETREP_DB_NAME':
            return 'XXXXXXXXXX';
            break;
        case 'BT_API_SETREP_DB_PWD':
            return 'XXXXXXXXXXXXXXXXXXX';
            break;
        case 'BT_API_SETREP_DB_HOST':
            return 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            break;
        case 'BT_API_SETREP_DB_USER':
            return 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            break;
        case 'BT_API_DB_NAME':
            return 'XXXXXXXXXX';
            break;
        case 'BT_API_DB_PWD':
            return 'XXXXXXXXXXXXXXXXXXX';
            break;
        case 'BT_API_DB_HOST':
            return 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            break;
        case 'BT_API_DB_USER':
            return 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            break;    
        default:
            return false;
    }
	
}