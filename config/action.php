<?php

declare(strict_types=1);

return [
    
    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    |
    | You can specify whether to wrap all database actions within a transaction
    | by default. This can be overridden on a per-action basis.
    |
    */

    'transact' => false,

    /*
    |--------------------------------------------------------------------------
    | Endpoint
    |--------------------------------------------------------------------------
    |
    | You can override the default endpoint for the action handler, this will
    | allow you to customise the named route and the endpoint which is used
    | to handle the action requests.
    |
    | Ensure you specify the route bindings for the unit and operation classes.
    |
    */

    'uri' => '_actions/{unit}/{operation}',

    'name' => 'actions',

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

    'handler' => null,
];
