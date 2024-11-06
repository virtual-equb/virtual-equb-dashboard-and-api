<?php

/** custom taskhub config */
return [
    /*
    |--------------------------------------------------------------------------
    | Role status labels
    |-------------------------------------------------------------------------- 
    */

    'role_labels' => [
        'admin' => "info",
        "Super Admin" => "danger",
        "General manager" => "primary",
        "member" => "warning",
        'default' => "dark"
    ],
    'priority_labels' => [
        'low' => "success",
        "high" => "danger",
        "medium" => "warning"
    ],

'permissions' => [
    'Main Equb' => ['create main_equb', 'view main_equb', 'update main_equb', 'delete main_equb'],
    'Equb Type' => ['create equb_type', 'view equb_type', 'update equb_type', 'delete equb_type'],
    'Equb' => ['create equb', 'view equb', 'update equb', 'delete equb'],
    'City' => ['create city', 'view city', 'update city', 'delete city'],
    'Sub City' => ['create sub_city','view sub_city', 'update sub_city', 'delete sub_city'],
    'User' => ['create user', 'view user', 'update user', 'delete user'],
 //   'Activity Log' => ['view_activity_log', 'manage_activity_log'],
    'Equb Taker' => ['create equb_taker', 'view equb_taker', 'update equb_taker', 'delete equb_taker'],
    'Lottery Winner' => ['create lottery_winner', 'view lottery_winner', 'update lottery_winner', 'delete lottery_winner'],
    'Member' => ['create member', 'view member', 'update member', 'delete member'],
    'Notification' => ['create notification', 'view notification', 'update notification', 'delete notification'],
    'Rejected Date' => ['create rejected_date', 'view rejected_date', 'update rejected_date', 'delete rejected_date'],
    'Report' => ['view report', 'update report'],
],

];

