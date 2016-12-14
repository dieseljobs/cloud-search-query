<?php

namespace TheLHC\CloudSearchQuery\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TheLHC\CloudSearchQuery\CloudSearchQueryServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Dotenv\Dotenv;

class TestCase extends BaseTestCase
{

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $dotenv = new Dotenv(__DIR__);
        $dotenv->load();
        $app['config']->set(
            'cloud_search_query.endpoint',
            getenv('ENDPOINT')
        );
    }

    /**
     * Get package service providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            CloudSearchQueryServiceProvider::class
        ];
    }

}
