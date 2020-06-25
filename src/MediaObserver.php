<?php

namespace Jeffreyvr\SimpleMedia;

use Jeffreyvr\SimpleMedia\Media;
use Illuminate\Support\Facades\Storage;

class MediaObserver
{
    public function deleted(Media $media)
    {
        if (!empty($media->image_sizes) && is_array($media->image_sizes)) {
            foreach($media->image_sizes as $imageSizeName => $imageSizeDetails) {
                Storage::disk($media->disk)->delete($imageSizeDetails['file_name']);
            }
        }

        Storage::disk($media->disk)->delete($media->file_name);
    }
}