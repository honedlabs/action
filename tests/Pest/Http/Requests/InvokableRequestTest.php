<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Honed\Action\ActionFactory;
use Honed\Action\Testing\RequestFactory;
use Honed\Action\Http\Requests\InvokableRequest;

beforeEach(function () {
    $this->id = Str::uuid()->toString();
});

it('validates inline', function () {
    $httpRequest = Request::create('/', 'POST', [
        'id' => $this->id,
        'name' => 'edit',
        'type' => ActionFactory::INLINE,
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
        'id' => $this->id,
        'name' => 'edit',
        'type' => ActionFactory::BULK,
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
        'id' => $this->id,
        'name' => 'edit',
        'type' => ActionFactory::PAGE,
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
