<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Endpoint
    |--------------------------------------------------------------------------
    |
    | You can specify a global endpoint to handle all action requests
    | when using the provided Route macro.
    |
    */

    'endpoint' => '/actions',


    'handler' => \Honed\Action\ActionHandler::class,
];
