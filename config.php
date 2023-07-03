<?php
    // DATABASE CONFIG
    $db_config = [
        'db_host' => 'localhost:3306',
        'db_name' => 'crm_data',
        'db_user' => 'root',
        'db_pass' => ''
    ];

    // DEFAULT QUERY STRING
    $dfQueryStr_config = [
        'key' => $_GET['key'] ?? '',
        'report' => $_GET['report'] ?? '',
        'year' => $_GET['year'] ?? '2023',
        'dayInterval' => $_GET['day'] ?? '7',
        'projectID' => $_GET['pid'] ?? '219',
        'userID' => $_GET['userid'] ?? '',
        'search' => $_GET['s'] ?? '',
        'mail' => $_GET['mail'] ?? '',
        'expenseID' => $_GET['eid'] ?? '',
    ];