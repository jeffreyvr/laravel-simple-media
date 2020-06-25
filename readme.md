# SimpleMedia For Laravel

SimpleMedia for Laravel is package to handle media attached or unattached to Eloquent models.

This package is very much a work in progess.

For a more fleshed out package, you might consider trying [Laravel Medialibrary](https://github.com/spatie/laravel-medialibrary) from [Spatie](https://github.com/spatie).

## Installation

```bash
composer require jeffreyvanrossum/laravel-simple-media --dev-master
```

## Configuration

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Jeffreyvr\SimpleMedia\SimpleMediaServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Jeffreyvr\SimpleMedia\SimpleMediaServiceProvider" --tag="config"
```

### Automatically generate additional image sizes

This can be handy if you for example would like to generate thumbnails of your images. You could define a thumbnail image size like this in the config:

```php
'image_sizes' => [
    'thumbnail' => [
        'width' => 100,
        'height' => 100,
        'crop' => true // determine if the image needs to be cropped
    ],
    // etc.
]
```

### Image drivers

The default image driver is `gd`. You could also specify `imagick` in the config file.

### Storage

You can specifiy which Storage disk should be used for your uploads in the configuration file. By default, it uses `public`.

## Usage

### Adding unattached files and images

To upload you can use the `uploadImage` and `uploadFile` methods on the `Media` model.

```php
Media::uploadImage($file); // for images

Media::uploadFile($file); // for files other then images
```

Calling these methods will automatically insert a record into the database. You specify/overwrite attributes by providing a second parameter to those methods.

```php
Media::uploadImage($file, ['name' => 'A custom name', 'group' => 'profile-images']);
```

You can handle uploads from the `Request` by using these methods:

```php
Media::uploadFileFromRequest($key);
Media::uploadImageFromRequest($key);
```

To retrieve media, you can do the following:

```php
Media::all();
Media::where('group', $group)->get();  // etc.
```

### Attaching to Eloquent models

To attach media to Eloquent models, you must first add the `HasMedia` trait to the model.

You specify/overwrite attributes by providing a second parameter (`array`) to those methods.

```php
$post->attachImage($file); // for images

$post->attachFile($file); // for files other then images
```

Or if you want to use a file from the request, you can do this:

```php
$post->attachImageFromRequest($key);
$post->attachFileFromRequest($key);
```

To retrieve the media, you can do the following:

```php
$post->media();
```

Blade:

```php
@foreach ($post->media as $file)
    {{$file->getOriginalUrl()}}
@endforeach
```

To retrieve media by group you can do this:

```php
$post->mediaByGroup('profile-images')->get();
```

### Deleting media

If you call the `delete` method on an instance of `Media`, it will delete the record and the associatated files on the disk.

```php
$media->delete();
```

## Notes
* If you don't specify a group, an image upload will get `images` as group by default. Other files will have `files` as default group.
* If a file name is used that is not unique within the destination folder, it will append a number to the name that will increment untill it is unique.
* The package does not restrict on file types. There is no file type validation, you could implement that yourself if needed.

## Todo
- [x] Implement some basic quality reduction and/or compression.
- [x] On delete, also delete the files from the disk.
- [x] Better support upload from request.
- [ ] Add more tests.