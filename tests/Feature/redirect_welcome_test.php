<?php

use function Pest\Laravel\get;

it('redirects welcome page to login', function () {
    $response = get('/');
    $response->assertRedirect('/login');
});
