<?php

declare(strict_types=1);

use Honed\Action\ActionFactory;
use Honed\Action\Testing\PageActionRequest;

beforeEach(function () {
    $this->request = new PageActionRequest();
});

it('has data', function () {
    expect($this->request)
        ->getData()->scoped(fn ($data) => $data
            ->toBeArray()
            ->toHaveKeys(['type', 'id', 'name'])
            ->{'type'}->toBe(ActionFactory::Page)
        )->data(['type' => 'test'])->toBe($this->request)
        ->getData()->scoped(fn ($data) => $data
            ->toBeArray()
            ->toHaveKeys(['type', 'id', 'name'])
            ->{'type'}->toBe('test')
        );
});
