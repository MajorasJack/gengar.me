<?php

declare(strict_types=1);

use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\Event;

it('reports healthy as plain text', function (): void {
    $this->get('/up')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertContent('ok');
});

it('dispatches the health diagnosis event', function (): void {
    Event::fake([DiagnosingHealth::class]);

    $this->get('/up');

    Event::assertDispatched(DiagnosingHealth::class);
});
