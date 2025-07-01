<?php

declare(strict_types=1);

return [

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

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    |
    | You can create your own action stubs inside the `stubs` directory with a
    | name following the format `honed.action.{id}.stub`. The value provided
    | will be prefixed to the model name. You can supply a {{ model }},
    | {{ action }} and {{ modelVariable }} placeholders to be replaced. These
    | can be created when passing the '--action' option to the 'make:action'
    | command.
    |
    */
    'actions' => [
        'associate' => 'Associate',
        'attach' => 'Attach',
        'detach' => 'Detach',
        'destroy' => 'Destroy',
        'dispatch' => 'Dispatch',
        'dissociate' => 'Dissociate',
        'force-destroy' => 'Force Destroy',
        'replicate' => 'Replicate',
        'restore' => 'Restore',
        'store' => 'Store',
        'sync' => 'Sync',
        'toggle' => 'Toggle',
        'update' => 'Update',
        'upsert' => 'Upsert',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model names
    |--------------------------------------------------------------------------
    |
    | You can create multiple actions for a model with one command via the
    | 'make:actions' command. You can supply the actions to be created with
    | the key being the action name and the value being the verb name. These
    | are generated under a directory of the model name, and a name generated
    | using the action name and the model name joined.
    |
    */
    'model_actions' => [
        'store' => 'Store',
        'update' => 'Update',
        'destroy' => 'Destroy',
    ],
];
