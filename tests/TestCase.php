<?php 
namespace Mtrn\ApiService\Tests;

use Mtrn\ApiService\ApiServiceServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;


class TestCase extends TestbenchTestCase
{
    
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            ApiServiceServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}