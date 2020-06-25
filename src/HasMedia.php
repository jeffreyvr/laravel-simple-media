<?php

namespace Jeffreyvr\SimpleMedia;

use Jeffreyvr\SimpleMedia\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasMedia
{
    public function media() : BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_relations', 'media_id', 'relation_id');
    }

    public function mediaByGroup($group) : BelongsToMany
    {
        return $this->media()->where('media.group', $group);
    }

    public function attachImageFromRequest($file, $customAttributes = []) : Model
    {
        $media = Media::uploadImageFromRequest($file, $customAttributes);

        $this->media()->attach($media->id, ['relation_type' => get_class($this)]);

        return $this;
    }

    public function attachImage($file, $customAttributes = []) : Model
    {
        $media = Media::uploadImage($file, $customAttributes);

        $this->media()->attach($media->id, ['relation_type' => get_class($this)]);

        return $this;
    }

    public function attachFileFromRequest($file, $customAttributes = []) : Model
    {
        $media = Media::uploadFileFromRequest($file, $customAttributes);

        $this->media()->attach($media->id, ['relation_type' => get_class($this)]);

        return $this;
    }

    public function attachFile($file, $customAttributes = []) : Model
    {
        $media = Media::uploadFile($file, $customAttributes);

        $this->media()->attach($media->id, ['relation_type' => get_class($this)]);

        return $this;
    }
}