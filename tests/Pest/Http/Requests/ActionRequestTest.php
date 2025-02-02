<?php

declare(strict_types=1);

use Honed\Action\Http\Requests\ActionRequest;

beforeEach(function () {
    $this->controller = fn (ActionRequest $request) => back();
});