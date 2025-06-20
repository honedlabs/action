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

    'endpoint' => 'actions',

    /*
    |--------------------------------------------------------------------------
    | Handler
    |--------------------------------------------------------------------------
    |
    | You can specify the handler class to be used for executing batch groups,
    | when using the provided route macro. This allows you to customize the
    | behaviour of the handler, such as adding additional logic or performing
    | additional authorization checks. By default, the package will use the
    | 'Honed\Action\Handlers\ActionHandler' class.
    |
    */

    'handler' => Honed\Action\Handlers\BatchHandler::class,
];
