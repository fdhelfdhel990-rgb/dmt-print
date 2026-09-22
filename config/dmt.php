<?php

return [
    'admin' => [
        'name' => env('DMT_ADMIN_NAME', 'Owner Admin'),
        'email' => env('DMT_ADMIN_EMAIL'),
        'password' => env('DMT_ADMIN_PASSWORD'),
    ],
    'uploads' => [
        'max_kb' => (int) env('DMT_MAX_UPLOAD_KB', 10240),
        'allowed' => array_values(array_filter(explode(',', env('DMT_ALLOWED_UPLOADS', 'jpg,jpeg,png,pdf,zip')))),
    ],
];
