<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chunking
    |--------------------------------------------------------------------------
    |
    | You can configure how the bulk actions should handle chunking of records
    | by default. These can also be set on a per-action basis if needed.
    |
    */

    'chunk' => false,
    'chunk_size' => 200,
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

    'confirm' => [
        'dismiss' => 'Cancel',
        'submit' => 'Confirm',
    ],
];
