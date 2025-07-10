<?php

declare(strict_types=1);

use Honed\Action\Attributes\Remember;
use Workbench\App\Batches\UserProductBatch;

beforeEach(function () {
    $this->batch = UserProductBatch::make();
});

it('creates attribute', function () {
    $attribute = new Remember();

    expect($attribute)->toBeInstanceOf(Remember::class);
});

it('annotates properties', function () {
    $reflection = new ReflectionClass($this->batch);

    $properties = $reflection->getProperties();

    $remembered = [];

    foreach ($properties as $property) {
        if ($property->getAttributes(Remember::class) !== []) {
            $remembered[] = $property;
        }
    }

    expect($remembered)
        ->toBeArray()
        ->toHaveCount(2);
});
