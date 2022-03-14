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
    |
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
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        'uploads' => [
            'driver' => 'local',
            'root'   => public_path('uploads'),
        ],

        'attributes' => [
            'driver'      => 'local',
            'simple_path' => 'uploads/attributes',
            'root'        => public_path('uploads/attributes'),
        ],

        'UsersImages' => [
            'driver' => 'local',
            'simple_path' => 'uploads/user',
            'root' => public_path('uploads/user'),
        ],
        'CustomersImages' => [
            'driver' => 'local',
            'simple_path' => 'uploads/customer',
            'root' => public_path('uploads/customer'),
        ],
        'Tutorial' => [
            'driver' => 'local',
            'simple_path' => 'uploads/tutorial',
            'root' => public_path('uploads/tutorial'),
        ],
        'Tutorial_Cover' => [
            'driver' => 'local',
            'simple_path' => 'uploads/tutorial_coverphoto',
            'root' => public_path('uploads/tutorial_coverphoto'),
        ],
        'Restaurants' => [
            'driver' => 'local',
            'simple_path' => 'uploads/restaurant',
            'root' => public_path('uploads/restaurant'),
        ],
        'Category' => [
            'driver' => 'local',
            'simple_path' => 'uploads/category',
            'root' => public_path('uploads/category'),
        ],
        'Foods' => [
            'driver' => 'local',
            'simple_path' => 'uploads/food',
            'root' => public_path('uploads/food'),
        ],
        'Up_Ads' => [
            'driver' => 'local',
            'simple_path' => 'uploads/up_ads',
            'root' => public_path('uploads/up_ads'),
        ],
        'Down_Ads' => [
            'driver' => 'local',
            'simple_path' => 'uploads/down_ads',
            'root' => public_path('uploads/down_ads'),
        ],
        'Notification' => [
            'driver' => 'local',
            'simple_path' => 'uploads/notification',
            'root' => public_path('uploads/notification'),
        ],
        'Rider' => [
            'driver' => 'local',
            'simple_path' => 'uploads/rider',
            'root' => public_path('uploads/rider'),
        ],
        'ParcelType' => [
            'driver' => 'local',
            'simple_path' => 'uploads/parcel/parcel_type',
            'root' => public_path('uploads/parcel/parcel_type'),
        ],
        'ParcelExtraCover' => [
            'driver' => 'local',
            'simple_path' => 'uploads/parcel/parcel_extra_cover',
            'root' => public_path('uploads/parcel/parcel_extra_cover'),
        ],
        'ParcelImage' => [
            'driver' => 'local',
            'simple_path' => 'uploads/parcel/parcel_image',
            'root' => public_path('uploads/parcel/parcel_image'),
        ],

    ],

];
