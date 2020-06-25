<?php

namespace Jeffreyvr\SimpleMedia\Tests;

use Illuminate\Support\Facades\Storage;
use Jeffreyvr\SimpleMedia\Tests\Support\Post;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OwnerTest extends TestCase
{
    /** @test */
    public function a_model_can_have_files_attached()
    {
        Storage::fake(config('simple-media.disk'));

        $post = Post::create([
            'title' => 'test'
        ]);

        $post->attachImage(__DIR__ . '/SupportFiles/image.jpg');

        $this->assertInstanceOf(BelongsToMany::class, $post->media());
        $this->assertEquals($post->media()->first()->group, 'images');
    }

    /** @test */
    public function a_model_can_retrieve_files_by_type()
    {
        Storage::fake(config('simple-media.disk'));

        $post = Post::create([
            'title' => 'test'
        ]);

        $post->attachImage(__DIR__ . '/SupportFiles/image.jpg');
        $post->attachFile(__DIR__ . '/SupportFiles/file.txt');

        $this->assertEquals($post->mediaByGroup('images')->first()->group, 'images');
        $this->assertEquals($post->mediaByGroup('files')->first()->group, 'files');
    }
}