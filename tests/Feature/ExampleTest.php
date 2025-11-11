<?php

test('the application returns a successful response', function () {
    $response = $this->get('/admin');

    $response->assertStatus(302); // Redirects to login
});
