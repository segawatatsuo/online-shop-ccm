<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    | デフォルトディスク
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    | クラウドディスク
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    | ディスク設定（disks配列）
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [ //公開アクセス可能なファイル用
            'driver' => 'local',
            'root' => storage_path('app/public'), //保存場所：storage/app/public
            'url' => env('APP_URL') . '/storage', //http://your-app.com/storage/filename
            'visibility' => 'public',
        ],

        's3' => [ //Amazon S3クラウドストレージ用
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
        ],

        // 新しく 'uploads' ディスクを定義する場合 (任意)

        'uploads' => [
            'driver' => 'local',
            'root' => public_path('uploads'), // public/uploads ディレクトリに保存
            'url' => env('APP_URL') . '/uploads',
            'visibility' => 'public',
        ],


        'admin' => [
            'driver' => 'local',
            'root' => public_path('uploads'), // 任意で public/uploads や public/admin_uploads など
            'url' => env('APP_URL') . '/uploads',
            'visibility' => 'public',
        ],



    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
