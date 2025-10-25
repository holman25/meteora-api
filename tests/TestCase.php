<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.api_key' => 'testing_key']);

        // Enviar el header en TODAS las requests de Feature tests
        $this->withHeader('X-Api-Key', 'testing_key');
    }
}
