<?php

return [
    // Use the 'redis' or 'cache' driver - this is where the job pause state is stored
    'driver' => env('PAUSABLE_JOBS_DRIVER', 'redis'),
    // The number of seconds to wait before retrying a paused job
    'retry_after' => env('PAUSABLE_JOBS_RETRY_AFTER', 30),
    // The maximum number of seconds a job can be retried before it is deleted
    'max_retry_time' => env('PAUSABLE_JOBS_MAX_RETRY_TIME', 3600),
    // Logging configuration, which will log when a job is retried because its paused
    'logging' => [
        'enabled' => env('PAUSABLE_JOBS_LOGGING', true),
        'channel' => env('PAUSABLE_JOBS_LOG_CHANNEL', 'stack'),
    ],
];
