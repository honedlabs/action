<?php

declare(strict_types=1);

use Honed\Action\ActionFactory;
use Honed\Action\Http\Requests\ActionRequest;
use Honed\Action\Testing\RequestFactory;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->id = Str::uuid()->toString();
});

it('validates inline', function () {
    $httpRequest = \Illuminate\Http\Request::create('/', 'POST', [
        'id' => $this->id,
        'name' => 'edit',
        'type' => ActionFactory::Inline,
        'record' => '1',
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(ActionRequest::class);
    $request->setContainer($this->app);

    $request->validateResolved();

    expect($request)
        ->isInline()->toBeTrue()
        ->ids()->toEqual([1]);
});

it('validates bulk', function () {
    $httpRequest = \Illuminate\Http\Request::create('/', 'POST', [
        'id' => $this->id,
        'name' => 'edit',
        'type' => ActionFactory::Bulk,
        'only' => [1, 2, 3],
        'all' => false,
        'except' => [],
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(ActionRequest::class);
    $request->setContainer($this->app);

    $request->validateResolved();

    expect($request)
        ->isBulk()->toBeTrue()
        ->ids()->toEqual([1, 2, 3]);
});

it('validates page', function () {
    $httpRequest = \Illuminate\Http\Request::create('/', 'POST', [
        'id' => $this->id,
        'name' => 'edit',
        'type' => ActionFactory::Page,
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(ActionRequest::class);
    $request->setContainer($this->app);

    $request->validateResolved();

    expect($request)
        ->isPage()->toBeTrue()
        ->ids()->toEqual([]);
});

it('fakes', function () {
    expect(ActionRequest::fake())
        ->toBeInstanceOf(RequestFactory::class);
});
