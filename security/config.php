<?php
// Security database configuration
return [
    'database' => [
        'host' => 'junction.proxy.rlwy.net',
        'database' => 'erpz',
        'username' => 'root',
        'password' => 'cyIgFzjjbzRiVbiHkemiUCKftdfPqBOn',
        'port' => 14359
    ],
    'tables' => [
        'roles' => 'roles',
        'groups' => 'groups',
        'group_roles' => 'group_roles',
        'user_groups' => 'user_groups',
        'permissions' => 'permissions',
        'role_permissions' => 'role_permissions'
    ],
    
    'default_roles' => [
        'admin' => [
            'name' => 'Administrator',
            'permissions' => ['*']
        ],
        'manager' => [
            'name' => 'Manager',
            'permissions' => ['view_users', 'manage_content', 'view_reports']
        ],
        'user' => [
            'name' => 'User',
            'permissions' => ['view_own_profile', 'edit_own_profile']
        ]
    ],
    
    'permissions' => [
        'user_management' => [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles'
        ],
        'content_management' => [
            'view_content',
            'create_content',
            'edit_content',
            'delete_content',
            'publish_content'
        ],
        'system' => [
            'system_settings',
            'view_logs',
            'manage_backups'
        ]
    ]
];
