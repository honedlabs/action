<?php

declare(strict_types=1);

use Honed\Action\Concerns\KeepsSelected;

class KeepsSelectedTest
{
    use KeepsSelected;
}

beforeEach(function () {
    $this->test = new KeepsSelectedTest;
});

it('sets', function () {
    expect($this->test)
        ->keepsSelected()->toBeFalse()
        ->keepSelected()->toBeInstanceOf(KeepsSelectedTest::class)
        ->keepsSelected()->toBeTrue();
});
