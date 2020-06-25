<?php

namespace Jeffreyvr\SimpleMedia;

use Exception;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

trait HandlesImages
{
    public static function uploadImage($file, $customAttributes = []) : Media
    {
        $media = (new Media())
            ->setDisk($customAttributes['disk'] ?? null);

        $filePath = $media->diskManager()
            ->uniqueFilePath($customAttributes['file_name'] ?? basename($file));

        $image = $media->imageManager()
            ->make($file)
            ->save($filePath, config('simple-media.image_quality'));

        $media->fill($media->compileFileAttributes(array_merge([
            'group' => 'images',
            'file_name' => basename($filePath)
        ], $customAttributes)))->save();

        if ($sizes = config('simple-media.image_sizes') ) {
            foreach ($sizes as $sizeName => $sizeDetails) {
                $media->addImageSize($sizeName);
            }
        }

        return $media;
    }

    public static function uploadImageFromRequest($requestName, $customAttributes = []) : Media
    {
        $file = request()->file($requestName);

        if (empty($customAttributes['file_name'])) {
            $customAttributes['file_name'] = $file->getClientOriginalName();
        }

        return self::uploadImage($file->path(), $customAttributes);
    }

    public function getImageUrls() : array
    {
        $urls = [
            'original' => $this->getOriginalUrl()
        ];

        if (!empty($this->image_sizes)) {
            foreach ( $this->image_sizes as $size => $image ) {
                $urls[$size] = $this->diskManager()->storage()->url($image['file_name']);
            }
        }

        return $urls;
    }

    public function getImageUrlBySize($size) : string
    {
        return $this->getImageUrls()[$size] ?? '';
    }

    /**
     * @param string $size The name of the size.
     * @return Image|Exception
     */
    public function addImageSize($size)
    {
        $this->generateImageBySize($size);

        $currentSizes = $this->image_sizes;

        $currentSizes[$size] = [
            'disk' => $this->disk,
            'file_name' => "$size-$this->file_name",
            'file_size' => $this->diskManager()->storage()->size("$size-$this->file_name")
        ];

        $this->image_sizes = $currentSizes;

        return $this->save();
    }

    public function imageManager() : ImageManager
    {
        return new ImageManager(array('driver' => config('simple-media.image_driver')));
    }

    public function generateImageBySize($size) : void
    {
        $sizeDetails = config("simple-media.image_sizes.$size", []);

        $path = $this->diskManager()->storage()->getDriver()->getAdapter()->getPathPrefix() . '/' . $this->file_name;

        $image = $this->imageManager()->make($path);

        if ( $sizeDetails['crop'] ) {
            $image->fit($sizeDetails['width'], $sizeDetails['height'], function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $image->resize($sizeDetails['width'], $sizeDetails['height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $this->diskManager()->storage()->put($size . '-' . $this->file_name, (string) $image->encode(null, config('simple-media.image_quality')));

        $this->file_size = $this->diskManager()->storage()->size("$this->file_name");
    }
}