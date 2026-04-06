<?php

return [
    'route_prefix' => 'dynamic-forms',
    'route_middleware' => ['web'],
    'public_route_prefix' => 'forms',
    'public_route_middleware' => ['web'],
    'storage_disk' => env('DYNAMIC_FORM_STORAGE_DISK', 'public'),
    'upload_directory' => 'dynamic-forms',
];
