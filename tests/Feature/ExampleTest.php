<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    // root now redirects to login
    $response->assertRedirect('/login');
});
