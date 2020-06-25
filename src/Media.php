<?php

namespace Jeffreyvr\SimpleMedia;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Jeffreyvr\SimpleMedia\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Jeffreyvr\SimpleMedia\HandlesFiles;
use Jeffreyvr\SimpleMedia\HandlesImages;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HandlesImages, HandlesFiles;

    protected $guarded = [];

    protected $table = 'media';

    protected $casts = [
        'image_sizes' => 'array',
    ];

    public function diskManager($disk = null) : DiskManager
    {
        return new DiskManager($disk ?? $this->disk);
    }

    public function setDisk($disk) : self
    {
        $this->disk = $disk;

        return $this;
    }

    public function getDisk()
    {
        return $this->disk;
    }

    public function getOriginalUrl() : string
    {
        return Storage::disk($this->disk)->url($this->file_name);
    }

    /**
     * @param array $customAttributes Array that holds the file attributes.
     * @return array
     */
    public function compileFileAttributes($customAttributes) : array
    {
        $filename = $customAttributes['file_name'] ?? basename($this->file_name);

        return array_merge([
            'group' => 'files',
            'name' => $filename,
            'file_name' => $filename,
            'mime_type' => $this->diskManager()->storage()->mimeType($filename),
            'disk' => $this->diskManager()->getDiskName(),
            'file_size' => $this->diskManager()->storage()->size($filename),
            'order' => null
        ], $customAttributes);
    }

    public function ownersByType($ownerType) : BelongsToMany
    {
        return $this->belongsToMany($ownerType, 'media_relations', 'media_id', 'relation_id');
    }
}