<?php

namespace Jeffreyvr\SimpleMedia\Tests;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Jeffreyvr\SimpleMedia\Media as Media;
use Jeffreyvr\SimpleMedia\Tests\Support\Post;

class MediaTest extends TestCase
{
    /** @test */
    public function it_can_handle_a_file()
    {
        Storage::fake(config('simple-media.disk'));

        $media = Media::uploadFile(__DIR__.'/SupportFiles/file.txt');

        $this->assertDatabaseHas('media', [
            'file_name' => 'file.txt',
        ]);

        Storage::disk(config('simple-media.disk'))->assertExists('file.txt');
    }

    /** @test */
    public function it_makes_filenames_unique()
    {
        Storage::fake(config('simple-media.disk'));

        $media1 = Media::uploadFile(__DIR__.'/SupportFiles/file.txt');
        $media2 = Media::uploadFile(__DIR__.'/SupportFiles/file.txt');

        Storage::disk(config('simple-media.disk'))->assertExists('file.txt');
        Storage::disk(config('simple-media.disk'))->assertExists('file-1.txt');
    }

    /** @test */
    public function it_can_handle_a_image()
    {
        Storage::fake(config('simple-media.disk'));

        config()->set('simple-media.image_sizes', [
            'thumbnail' => [
                'width' => 150,
                'height' => 150,
                'crop' => true
            ],
            'medium' => [
                'width' => 400,
                'height' => 300,
                'crop' => true
            ]
        ]);

        $media = Media::uploadImage(__DIR__ . '/SupportFiles/image.jpg');

        $this->assertDatabaseHas('media', [
            'file_name' => 'image.jpg',
            'group' => 'images'
        ]);

        $media->diskManager()->storage()->assertExists('image.jpg');
        $media->diskManager()->storage()->assertExists('thumbnail-image.jpg');
        $media->diskManager()->storage()->assertExists('medium-image.jpg');
    }

    /** @test */
    public function it_can_return_the_original_url()
    {
        Storage::fake(config('simple-media.disk'));

        $media = Media::uploadFile(__DIR__.'/SupportFiles/file.txt');

        $this->assertEquals('/storage/file.txt', $media->getOriginalUrl());
    }

    /** @test */
    public function it_can_handle_uploads_from_request()
    {
        $this->app['router']->get('/upload', function () {
            $post = Post::create([
                'title' => 'test'
            ]);

            $post->attachImageFromRequest('file', ['file_name' => 'custom_file_name.jpg']);

            $this->assertEquals('/storage/custom_file_name.jpg', $post->media()->first()->getOriginalUrl());
        });

        $fileUpload = new UploadedFile(
            __DIR__ . '/SupportFiles/image.jpg',
            __DIR__ . '/SupportFiles/image.jpg',
            'image/jpeg',
            filesize(__DIR__ . '/SupportFiles/image.jpg')
        );

        $result = $this->call('get', 'upload', [], [], ['file' => $fileUpload]);

        $this->assertEquals(200, $result->getStatusCode());

        Storage::disk(config('simple-media.disk'))->delete('custom_file_name.jpg');
    }

    /** @test */
    public function it_can_return_image_urls_by_size()
    {
        Storage::fake(config('simple-media.disk'));

        config()->set('simple-media.image_sizes', [
            'thumbnail' => [
                'width' => 100,
                'height' => 100,
                'crop' => false
            ]
        ]);

        $media = Media::uploadImage(__DIR__ . '/SupportFiles/image.jpg');

        $this->assertArrayHasKey('thumbnail', $media->getImageUrls());
        $this->assertArrayHasKey('original', $media->getImageUrls());
        $this->assertEquals(Storage::disk(config('simple-media.disk'))->url('thumbnail-'.$media->file_name), $media->getImageUrlBySize('thumbnail'));
    }

    /** @test */
    public function it_removes_files_upon_deletion()
    {
        Storage::fake(config('simple-media.disk'));

        config()->set('simple-media.image_sizes', [
            'thumbnail' => [
                'width' => 100,
                'height' => 100,
                'crop' => false
            ]
        ]);

        $media = Media::uploadImage(__DIR__ . '/SupportFiles/image.jpg');

        $media->diskManager()->storage()->assertExists('image.jpg');

        $media->delete();

        $media->diskManager()->storage()->assertMissing('image.jpg');
        $media->diskManager()->storage()->assertMissing('thumbnail-image.jpg');
    }

    /** @test */
    public function it_can_store_a_file_on_a_different_disk()
    {
        Storage::fake('local');

        $media = Media::uploadImage(__DIR__ . '/SupportFiles/image.jpg', [
            'disk' => 'local'
        ]);

        $media->diskManager()->storage()->assertExists('image.jpg');
    }
}