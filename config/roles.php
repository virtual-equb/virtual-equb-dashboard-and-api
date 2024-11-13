<?php

return [

    'models' => [
        'role' => Spatie\Permission\Models\Role::class,
        'permission' => Spatie\Permission\Models\Permission::class,
    ],

    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_roles' => 'model_has_roles',
        'model_has_permissions' => 'model_has_permissions',
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        'model_morph_key' => 'model_id',
    ],

    'cache' => [
        'expiration_time' => 60 * 60, // 1 hour
        'key' => 'spatie.permission.cache',
        'store' => null,
    ],

    'display_permission_in_exception' => false, // Set to true to display permission names in exceptions

    'permissions' => [
        'Dashboard Management' => [
            'view_dashboard' => ['web', 'api'],
            'view_dashboard_daily_summary' => ['web', 'api'],
            'view_dashboard_all_projection' => ['web', 'api'],
            'view_dashboard_members' => ['web', 'api'],
            'view_dashboard_summary' => ['web', 'api'],
            'view_dashboard_lottery_winner' => ['web', 'api'],
            'view_dashboard_projection_equb_type' => ['web', 'api'],
        ],
        'Equb Management' => [
            'create equb' => ['web', 'api'],
            'view equb' => ['web', 'api'],
            'update equb' => ['web', 'api'],
            'delete equb' => ['web', 'api'],
        ],
        'Equb Taker Management' => [
            'create equb_taker' => ['web', 'api'],
            'view equb_taker' => ['web', 'api'],
            'update equb_taker' => ['web', 'api'],
            'delete equb_taker' => ['web', 'api'],
        ],
       'Main Equb Management' => [
    'create main_equb' => ['web', 'api'],
    'view main_equb' => ['web', 'api'],
    'update main_equb' => ['web', 'api'],
    'delete main_equb' => ['web', 'api'],
],
        'Equb Type Management' => [
            'create equb_type' => ['web', 'api'],
            'view equb_type' => ['web', 'api'],
            'update equb_type' => ['web', 'api'],
            'delete equb_type' => ['web', 'api'],
        ],
        'Lottery Winner Management' => [
            'create lottery_winner' => ['web', 'api'],
            'view lottery_winner' => ['web', 'api'],
            'update lottery_winner' => ['web', 'api'],
            'delete lottery_winner' => ['web', 'api'],
        ],
        'Member Management' => [
            'create member' => ['web', 'api'],
            'view member' => ['web', 'api'],
            'update member' => ['web', 'api'],
            'delete member' => ['web', 'api'],
            'deactivate member' => ['web', 'api'],
            'rate member' => ['web', 'api'],
            'send member_notification' => ['web', 'api'],
            'add member_equb' => ['web', 'api'],
            'approve member' => ['web', 'api'],
            'reject member' => ['web', 'api'],
        ],
        'Notification Management' => [
            'create notification' => ['web', 'api'],
            'view notification' => ['web', 'api'],
            'update notification' => ['web', 'api'],
            'delete notification' => ['web', 'api'],
            'approve notification' => ['web', 'api'],
            'resend notification' => ['web', 'api'],
        ],
        'Rejected Date Management' => [
            'create rejected_date' => ['web', 'api'],
            'view rejected_date' => ['web', 'api'],
            'update rejected_date' => ['web', 'api'],
            'delete rejected_date' => ['web', 'api'],
        ],
        'Payment Management' => [
            'create payment' => ['web', 'api'],
            'view payment' => ['web', 'api'],
            'update payment' => ['web', 'api'],
            'delete payment' => ['web', 'api'],
            'add equb_payment' => ['web', 'api'],
            'export payment_data' => ['web', 'api'],
        ],
        'User Management' => [
            'create user' => ['web', 'api'],
            'view user' => ['web', 'api'],
            'update user' => ['web', 'api'],
            'delete user' => ['web', 'api'],
            'deactivate user' => ['web', 'api'],
            'reset user_password' => ['web', 'api'],
            'activate user' => ['web', 'api'],
        ],
        'Permission Management' => [
            'create permission' => ['web', 'api'],
            'view permission' => ['web', 'api'],
            'update permission' => ['web', 'api'],
            'delete permission' => ['web', 'api'],
        ],
        'Role Management' => [
            'create role' => ['web', 'api'],
            'view role' => ['web', 'api'],
            'update role' => ['web', 'api'],
            'delete role' => ['web', 'api'],
        ],
       
        'Reporting' => [
            'view report' => ['web', 'api'],
            'view payment_report' => ['web', 'api'],
            'view unpaid_payment_report' => ['web', 'api'],
            'view collected_by_report' => ['web', 'api'],
            'view equb_report' => ['web', 'api'],
            'view paid_lottories_report' => ['web', 'api'],
            'view unpaid_lottories_report' => ['web', 'api'],
            'view unpaid_lottories_by_date_report' => ['web', 'api'],
            'view reserved_lottery_date_report' => ['web', 'api'],
            'view member_report' => ['web', 'api'],
            'view member_report_by_equb_type' => ['web', 'api'],
        ],
       
        'Location Management' => [
            'create city' => ['web', 'api'],
            'view city' => ['web', 'api'],
            'update city' => ['web', 'api'],
            'delete city' => ['web', 'api'],
            'create sub_city' => ['web', 'api'],
            'view sub_city' => ['web', 'api'],
            'update sub_city' => ['web', 'api'],
            'delete sub_city' => ['web', 'api'],
            'create country' => ['web', 'api'],
            'view country' => ['web', 'api'],
            'update country' => ['web', 'api'],
            'delete country' => ['web', 'api'],
            'create country_code' => ['web', 'api'],
            'view country_code' => ['web', 'api'],
            'update country_code' => ['web', 'api'],
            'delete country_code' => ['web', 'api'],
        ],
        'Miscellaneous' => [
            'test' => ['web', 'api'],
            'check member_lottery_date' => ['web', 'api'],
            'draw equb_type_winner' => ['web', 'api'],
            'export off_date_data' => ['web', 'api'],
            'export notification_data' => ['web', 'api'],
            'export user_data' => ['web', 'api'],
            'export reports_data' => ['web', 'api'],
        ],
    ],

];
