<?php

return [
    /**
     * Specificy the disc that needs to be used to store your media.
     */
    'disk' => env('SIMPLE_MEDIA_DISK', 'public'),

    /**
     * The quality of the image in a range from 0 to 100.
     */
    'image_quality' => 90,

    /**
     * The image driver that needs to be used. Either 'gd' or 'imagick'.
     */
    'image_driver' => 'gd',

    /**
     * You can specifiy additional image sizes that need to be made upon image upload.
     * If cropping is set to true, the image will be cropped to the specified
     * dimensions using center positions.
     */
    'image_sizes' => [
        // 'thumbnail' => [
        //     'width' => 100,
        //     'height' => 100,
        //     'crop' => true
        // ]
    ]
];