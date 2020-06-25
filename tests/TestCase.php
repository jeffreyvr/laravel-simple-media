<?php

namespace Jeffreyvr\SimpleMedia\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Jeffreyvr\SimpleMedia\SimpleMediaServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            SimpleMediaServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        include_once __DIR__.'/../database/migrations/create_media_table.php.stub';
        include_once __DIR__.'/../database/migrations/create_media_relations_table.php.stub';
        include_once __DIR__.'/../database/migrations/create_posts_table.php.stub';
        (new \CreateMediaTable())->up();
        (new \CreateMediaRelationsTable())->up();
        (new \CreatePostsTable())->up();
    }
}