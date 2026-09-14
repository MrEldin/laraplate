<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Invocation Logging
    |--------------------------------------------------------------------------
    |
    | When enabled, every agent run is written to the "ai_invocations" table
    | along with its token usage, so spend can be attributed to the agent and
    | the record that caused it. Disable it if you ship your own telemetry.
    |
    */

    'logging' => [
        'enabled' => env('AI_LOG_INVOCATIONS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Semantic Search
    |--------------------------------------------------------------------------
    |
    | Entities using the HasAiSearch trait are indexed into "ai_embeddings".
    | Similarity is ranked in PHP, which is portable and needs no database
    | extension, but is linear in the number of indexed rows -- move the
    | ranking into pgvector once a type grows past roughly ten thousand rows.
    |
    */

    'search' => [
        'auto_index' => env('AI_AUTO_INDEX', true),
        'default_limit' => env('AI_SEARCH_LIMIT', 5),
        'minimum_score' => env('AI_SEARCH_MIN_SCORE', 0.0),
    ],

];
