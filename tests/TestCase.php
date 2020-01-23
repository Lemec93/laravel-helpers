<?php

namespace Lemec93\Support\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTest;
use Lemec93\Support\Traits\FixturesTrait;

class TestCase extends BaseTest
{
    use FixturesTrait;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }
}
