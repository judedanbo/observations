<?php

it('can perform basic assertions', function () {
    expect(true)->toBeTrue();
    expect(1 + 1)->toBe(2);
    expect('Hello')->toBeString();
});

it('can interact with Laravel', function () {
    expect(config('app.name'))->toBeString();
    expect(app()->version())->toContain('11');
});

test('database connection works', function () {
    $this->assertDatabaseCount('users', 0);
});