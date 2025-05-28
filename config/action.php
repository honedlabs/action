<?php

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

    /*
    |--------------------------------------------------------------------------
    | Chunking
    |--------------------------------------------------------------------------
    |
    | You can configure how the bulk actions should handle chunking of records
    | by default. These can also be set on a per-action basis if needed.
    |
    */

    /** Whether to enable chunking of records */
    'chunk' => false,

    /** The size of the chunk to use when chunking the records */
    'chunk_size' => 200,

    /** Whether to chunk the records by id */
    'chunk_by_id' => true,

    /*
    |--------------------------------------------------------------------------
    | Confirmation
    |--------------------------------------------------------------------------
    |
    | You can configure the dismiss and submit messages for the confirmation
    | class globally here.
    |
    */

    /** The message to display when dismissing the confirmation */
    'dismiss' => 'Cancel',

    /** The message to display when confirming the action */
    'submit' => 'Confirm',
];
