<?php

declare(strict_types=1);

use Honed\Action\Http\Requests\DispatchableRequest;
use Honed\Action\Support\Constants;
use Honed\Action\Testing\RequestFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->id = Str::uuid()->toString();
});

it('requires id', function () {
    $httpRequest = Request::create('/', 'POST', [
        'name' => 'edit',
        'type' => Constants::INLINE,
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(DispatchableRequest::class);
    $request->setContainer($this->app);

    $request->validateResolved();
})->throws(ValidationException::class);

it('validates inline', function () {
    $httpRequest = Request::create('/', 'POST', [
        'id' => $this->id,
        'name' => 'edit',
        'type' => Constants::INLINE,
        'record' => '1',
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(DispatchableRequest::class);
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
        'type' => Constants::BULK,
        'only' => [1, 2, 3],
        'all' => false,
        'except' => [],
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(DispatchableRequest::class);
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
        'type' => Constants::PAGE,
    ]);

    $this->app->instance('request', $httpRequest);
    $request = $this->app->make(DispatchableRequest::class);
    $request->setContainer($this->app);

    $request->validateResolved();

    expect($request)
        ->isPage()->toBeTrue()
        ->ids()->toEqual([]);
});

it('fakes', function () {
    expect(DispatchableRequest::fake())
        ->toBeInstanceOf(RequestFactory::class);
});
