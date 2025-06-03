<?php

declare(strict_types=1);

use Honed\Action\Http\Requests\InvokableRequest;
use Honed\Action\Testing\RequestFactory;
use Illuminate\Http\Request;

it('validates inline', function () {
    $httpRequest = Request::create('/', 'POST', [
        'name' => 'edit',
        'type' => 'inline',
        'record' => '1',
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(InvokableRequest::class);
    $request->setContainer($this->app);

    $request->validateResolved();

    expect($request)
        ->isInline()->toBeTrue()
        ->ids()->toEqual([1]);
});

it('validates bulk', function () {
    $httpRequest = Request::create('/', 'POST', [
        'name' => 'edit',
        'type' => 'bulk',
        'only' => [1, 2, 3],
        'all' => false,
        'except' => [],
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(InvokableRequest::class);
    $request->setContainer($this->app);

    $request->validateResolved();

    expect($request)
        ->isBulk()->toBeTrue()
        ->ids()->toEqual([1, 2, 3]);
});

it('validates page', function () {
    $httpRequest = Request::create('/', 'POST', [
        'name' => 'edit',
        'type' => 'page',
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(InvokableRequest::class);
    $request->setContainer($this->app);

    $request->validateResolved();

    expect($request)
        ->isPage()->toBeTrue()
        ->ids()->toEqual([]);
});

it('fakes', function () {
    expect(InvokableRequest::fake())
        ->toBeInstanceOf(RequestFactory::class);
});
