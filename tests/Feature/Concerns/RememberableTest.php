<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Workbench\App\Batches\UserProductBatch;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->batch = UserProductBatch::make();

    $this->batch->define();
});

it('is rememberable', function () {
    expect($this->batch)
        ->isNotRememberable()->toBeFalse()
        ->isRememberable()->toBeTrue()
        ->notRememberable()->toBe($this->batch)
        ->isNotRememberable()->toBeTrue()
        ->rememberable()->toBe($this->batch)
        ->isRememberable()->toBeTrue();
});

it('does not remember unset properties', function () {
    expect($this->batch->getRemembered())
        ->toBeArray()
        ->toHaveCount(1)
        ->toHaveKey('scalar')
        ->{'scalar'}->toBeString();
});

it('does not remember null properties', function () {
    expect($this->batch->user(null)->getRemembered())
        ->toBeArray()
        ->toHaveCount(1)
        ->toHaveKey('scalar')
        ->{'scalar'}->toBeString();
});

it('remembers properties', function () {
    expect($this->batch->user($this->user)->getRemembered())
        ->toBeArray()
        ->toHaveCount(2)
        ->toHaveKeys(['scalar', 'user'])
        ->{'scalar'}->toBeString()
        ->{'user'}->toBeString();
});

it('serializes scalars', function ($scalar) {
    $remembered = $this->batch->scalar($scalar)->getRemembered();

    expect(base64_decode($remembered['scalar']))
        ->toBe((string) $scalar);
})->with([
    'string' => ['scalar' => 'string'],
    'int' => ['scalar' => 1],
    'float' => ['scalar' => 1.0],
    'bool' => ['scalar' => true],
]);

it('serializes models', function () {
    $remembered = $this->batch->user($this->user)->getRemembered();

    expect(base64_decode($remembered['user']))
        ->toBe(User::class.'|'.$this->user->getKey());
});

it('throws exception when serializing non-scalar values', function () {
    $this->batch->scalar(new stdClass())->getRemembered();
})->throws(RuntimeException::class);

it('only retrieves once', function () {
    $remembered = $this->batch->getRemembered();

    expect($this->batch->getRemembered())
        ->toBe($remembered);
});

it('sets properties from remembered data', function () {
    $remembered = $this->batch
        ->user($this->user)
        ->scalar(2)
        ->getRemembered();

    $batch = UserProductBatch::make();

    $batch->define();

    $request = Request::create('/', Request::METHOD_POST, $remembered);

    expect($batch)
        ->getUser()->toBeNull()
        ->getScalar()->toBe(1)
        ->setRemembered($request)->toBeNull()
        ->getUser()->toBeInstanceOf(User::class)
        ->getScalar()->toBe(2);
})->todo();
