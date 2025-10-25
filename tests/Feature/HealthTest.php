<?php

use function Pest\Laravel\getJson;

it('returns health ok', function () {
    getJson('/api/v1/health')
        ->assertOk();
});
