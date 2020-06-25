<?php

namespace Jeffreyvr\SimpleMedia;

use Illuminate\Support\Facades\Storage;

class DiskManager
{
    public $diskName;

    public function __construct($diskName)
    {
        $this->diskName = $diskName;

        return $this;
    }

    public function setDiskName($diskName) : self
    {
        $this->diskName = $diskName;

        return $this;
    }

    public function getDiskName() : string
    {
        if (empty($this->diskName) ) {
            return config('simple-media.disk');
        }
        return $this->diskName;
    }

    public function storage() : object
    {
        return Storage::disk($this->getDiskName());
    }

    public function uniqueFileName($filename) : string
    {
        return basename($this->uniqueFilePath($filename));
    }

    public function uniqueFilePath($filename) : string
    {
        $filePath = $this->storage()->path('/') . $filename;

        $pathParts = pathinfo($filePath);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $i = 1;

        while (file_exists($filePath)) {
            $filePath = $pathParts['dirname'] . '/' . $pathParts['filename'] . '-' . $i . '.' .$extension;
            $i++;
        }

        return $filePath;
    }
}