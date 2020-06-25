<?php

namespace Jeffreyvr\SimpleMedia;

use Jeffreyvr\SimpleMedia\Media;

trait HandlesFiles
{
    public static function uploadFile($file, $customAttributes = []) : Media
    {
        $media = (new Media())
            ->setDisk($customAttributes['disk'] ?? null);

        $filePath = $media->diskManager()
            ->uniqueFileName($customAttributes['file_name'] ?? basename($file));

        $media->diskManager()
            ->storage()
            ->put($filePath, file_get_contents($file));

        $media->fill($media->compileFileAttributes(array_merge([
            'group' => 'files',
            'file_name' => basename($filePath)
        ], $customAttributes)))->save();

        return $media;
    }

    public static function uploadFileFromRequest($requestName, $customAttributes = []) : Media
    {
        $file = request()->file($requestName);

        if (empty($customAttributes['file_name'])) {
            $customAttributes['file_name'] = $file->getClientOriginalName();
        }

        return self::uploadFile($file->path(), $customAttributes);
    }
}