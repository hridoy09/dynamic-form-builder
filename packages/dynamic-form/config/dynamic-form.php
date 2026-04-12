<?php

return [
    'route_prefix' => 'dynamic-forms',
    'route_middleware' => ['web'],
    'public_route_prefix' => 'forms',
    'public_route_middleware' => ['web'],
    'storage_disk' => env('DYNAMIC_FORM_STORAGE_DISK', 'public'),
    'upload_directory' => 'dynamic-forms',
    'webhook_timeout' => (int) env('DYNAMIC_FORM_WEBHOOK_TIMEOUT', 10),
    'notification_from_address' => env('DYNAMIC_FORM_NOTIFICATION_FROM_ADDRESS'),
    'notification_from_name' => env('DYNAMIC_FORM_NOTIFICATION_FROM_NAME', env('APP_NAME', 'Laravel')),
];
