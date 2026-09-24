<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    'uploads' => [
        'public' => env('PUBLIC_UPLOAD_DISK', 'public_uploads'),
        'private' => env('PRIVATE_UPLOAD_DISK', 'private_uploads'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => (function () {
        $sanitizeEndpoint = static function (?string $endpoint, ?string $bucket): ?string {
            if (! is_string($endpoint) || trim($endpoint) === '') {
                return null;
            }

            $trimmed = rtrim(trim($endpoint), '/');

            if (is_string($bucket) && trim($bucket) !== '') {
                $bucketSuffix = '/'.trim($bucket, '/');
                if (str_ends_with($trimmed, $bucketSuffix)) {
                    return substr($trimmed, 0, -strlen($bucketSuffix));
                }
            }

            return $trimmed;
        };

        return [

            'local' => [
                'driver' => 'local',
                'root' => storage_path('app/private'),
                'serve' => true,
                'throw' => false,
                'report' => false,
            ],

            'private_uploads' => [
                'driver' => env('PRIVATE_FILESYSTEM_DRIVER', 'local'),
                'root' => env('PRIVATE_FILESYSTEM_DRIVER', 'local') === 'local' ? storage_path('app/private') : '',
                'key' => env('PRIVATE_AWS_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID')),
                'secret' => env('PRIVATE_AWS_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY')),
                'region' => env('PRIVATE_AWS_DEFAULT_REGION', env('AWS_DEFAULT_REGION', 'auto')),
                'bucket' => env('PRIVATE_AWS_BUCKET', env('AWS_BUCKET')),
                'endpoint' => $sanitizeEndpoint(
                    env('PRIVATE_AWS_ENDPOINT', env('AWS_ENDPOINT')),
                    env('PRIVATE_AWS_BUCKET', env('AWS_BUCKET'))
                ),
                'use_path_style_endpoint' => env('PRIVATE_AWS_USE_PATH_STYLE_ENDPOINT', env('AWS_USE_PATH_STYLE_ENDPOINT', true)),
                'visibility' => 'private',
                'throw' => false,
                'report' => false,
            ],

            'public' => [
                'driver' => env('PUBLIC_FILESYSTEM_DRIVER', 'local'),
                'root' => env('PUBLIC_FILESYSTEM_DRIVER', 'local') === 'local' ? storage_path('app/public') : '',
                'url' => env('PUBLIC_FILESYSTEM_DRIVER', 'local') === 'local'
                    ? rtrim(env('APP_URL', 'http://localhost'), '/').'/storage'
                    : env('PUBLIC_AWS_URL', env('AWS_URL')),
                'key' => env('PUBLIC_AWS_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID')),
                'secret' => env('PUBLIC_AWS_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY')),
                'region' => env('PUBLIC_AWS_DEFAULT_REGION', env('AWS_DEFAULT_REGION', 'auto')),
                'bucket' => env('PUBLIC_AWS_BUCKET', env('AWS_BUCKET')),
                'endpoint' => $sanitizeEndpoint(
                    env('PUBLIC_AWS_ENDPOINT', env('AWS_ENDPOINT')),
                    env('PUBLIC_AWS_BUCKET', env('AWS_BUCKET'))
                ),
                'use_path_style_endpoint' => env('PUBLIC_AWS_USE_PATH_STYLE_ENDPOINT', env('AWS_USE_PATH_STYLE_ENDPOINT', true)),
                'visibility' => 'public',
                'throw' => false,
                'report' => false,
            ],

            'public_uploads' => [
                'driver' => env('PUBLIC_FILESYSTEM_DRIVER', 'local'),
                'root' => env('PUBLIC_FILESYSTEM_DRIVER', 'local') === 'local' ? storage_path('app/public') : '',
                'url' => env('PUBLIC_FILESYSTEM_DRIVER', 'local') === 'local'
                    ? rtrim(env('APP_URL', 'http://localhost'), '/').'/storage'
                    : env('PUBLIC_AWS_URL', env('AWS_URL')),
                'key' => env('PUBLIC_AWS_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID')),
                'secret' => env('PUBLIC_AWS_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY')),
                'region' => env('PUBLIC_AWS_DEFAULT_REGION', env('AWS_DEFAULT_REGION', 'auto')),
                'bucket' => env('PUBLIC_AWS_BUCKET', env('AWS_BUCKET')),
                'endpoint' => $sanitizeEndpoint(
                    env('PUBLIC_AWS_ENDPOINT', env('AWS_ENDPOINT')),
                    env('PUBLIC_AWS_BUCKET', env('AWS_BUCKET'))
                ),
                'use_path_style_endpoint' => env('PUBLIC_AWS_USE_PATH_STYLE_ENDPOINT', env('AWS_USE_PATH_STYLE_ENDPOINT', true)),
                'visibility' => 'public',
                'throw' => false,
                'report' => false,
            ],

            's3' => [
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'bucket' => env('AWS_BUCKET'),
                'url' => env('AWS_URL'),
                'endpoint' => $sanitizeEndpoint(
                    env('AWS_ENDPOINT'),
                    env('AWS_BUCKET')
                ),
                'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
                'throw' => false,
                'report' => false,
            ],

        ];
    })(),

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
