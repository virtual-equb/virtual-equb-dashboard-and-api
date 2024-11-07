<?php

/** custom    config */
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
    'permissions' => [
    'City' => [
        'create city', 'view city', 'update city', 'delete city'
    ],
    'Sub City' => [
        'create sub_city', 'view sub_city', 'update sub_city', 'delete sub_city'
    ],
    'Country' => [
        'create country', 'view country', 'update country', 'delete country'
    ],
    'Country Code' => [
        'create country_code', 'view country_code', 'update country_code', 'delete country_code'
    ],
    'Equb' => [
        'create equb', 'view equb', 'update equb', 'delete equb'
    ],
    'Equb Taker' => [
        'create equb_taker', 'view equb_taker', 'update equb_taker', 'delete equb_taker'
    ],
    'Permission' => [
        'create permission', 'view permission', 'update permission', 'delete permission'
    ],
    'Role' => [
        'create role', 'view role', 'update role', 'delete role'
    ],
    'Equb Type' => [
        'create equb_type', 'view equb_type', 'update equb_type', 'delete equb_type'
    ],
    'Lottery Winner' => [
        'create lottery_winner', 'view lottery_winner', 'update lottery_winner', 'delete lottery_winner'
    ],
    'Main Equb' => [
        'create main_equb', 'view main_equb', 'update main_equb', 'delete main_equb'
    ],
    'Member' => [
        'create member', 'view member', 'update member', 'delete member',
        'deactivate member', 'rate member', 'send member_notification', 'add member_equb', 'approve member', 'reject member'
    ],
    'Notification' => [
        'create notification', 'view notification', 'update notification', 'delete notification',
        'approve notification', 'resend notification'
    ],
    'Rejected Date' => [
        'create rejected_date', 'view rejected_date', 'update rejected_date', 'delete rejected_date'
    ],
    'User' => [
        'create user', 'view user', 'update user', 'delete user',
        'deactivate user', 'reset user_password', 'activate user'
    ],
    'Payment' => [
        'create payment', 'view payment', 'update payment', 'delete payment',
        'export payment_data'
    ],
    'Dashboard' => [
        'view dashboard', 'view dashboard_daily_summary', 'view dashboard_all_projection',
        'view dashboard_members', 'view dashboard_summary',
        'view member_report', 'view member_report_by_equb_type', 'view collected_by_report',
        'view equb_report', 'view paid_lottories_report', 'view unpaid_lottories_report',
        'view unpaid_lottories_by_date_report', 'view reserved_lottery_date_report', 
        'view payment_report', 'view unpaid_payment_report', 'view filter_equb_by_end_date_reports',
        'view dashboard_lottery_winner', 'view dashboard_projection_equb_type'
    ],
    'Test' => [
        'test'
    ],
    'Equb Payment' => [
        'add equb_payment'
    ],
    'Equb Lottery' => [
        'add equb_lottery'
    ],
    'Deactivate Equb' => [
        'deactivate equb', 'deactivate equb_type', 'deactivate equb_for_draw'
    ],
    'Export Data' => [
        'export payment_data', 'export off_date_data', 'export notification_data', 
        'export user_data', 'export reports_data'
    ],
    'Draw Equb Type Winner' => [
        'draw equb_type_winner'
    ],
    'Check Member Lottery Date' => [
        'check member_lottery_date'
    ]
]
/*'permissions' => [
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
],*/

];

