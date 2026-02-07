<?php

namespace Shaanid\PayPal\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Shaanid\PayPal\PayPalServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            PayPalServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
