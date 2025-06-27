<?php

declare(strict_types=1);

use Honed\Action\Operations\InlineOperation;
use Illuminate\Http\RedirectResponse;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->operation = InlineOperation::make('test');
});

it('can redirect', function () {
    expect($this->operation)
        ->getRedirect()->toBeNull()
        ->hasRedirect()->toBeFalse()
        ->redirect('users.index')->toBe($this->operation)
        ->getRedirect()->toBe('users.index')
        ->hasRedirect()->toBeTrue();
});

it('calls redirect', function ($redirect, $expected) {
    get('/')->assertOk();

    expect($this->operation->redirect($redirect))
        ->callRedirect()
        ->scoped(fn ($response) => $response
            ->toBeInstanceOf(RedirectResponse::class)
            ->getTargetUrl()->toBe($expected)
        );
})->with([
    'no redirect' => fn () => [null, url('/')],
    'url' => fn () => ['/users', route('users.index')],
    'route' => fn () => ['users.index', route('users.index')],
    'closure' => fn () => [fn () => to_route('users.index'), route('users.index')],
]);
