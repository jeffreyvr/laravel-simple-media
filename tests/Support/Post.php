<?php

namespace Jeffreyvr\SimpleMedia\Tests\Support;

use Jeffreyvr\SimpleMedia\HasMedia;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasMedia;

    protected $guarded = [];
}