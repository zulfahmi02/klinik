<?php

test('public visitors cannot create staff accounts', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});
