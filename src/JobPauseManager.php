<?php

namespace Smitmartijn\PausableJobs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class JobPauseManager
{
  protected $driver;

  public function __construct()
  {
    $this->driver = config('pausable-jobs.driver', 'redis');
  }

  public function pauseJobClass(string $jobClass): void
  {
    if ($this->driver === 'redis') {
      Redis::set($this->getKey($jobClass), 1);
    } else {
      Cache::put($this->getKey($jobClass), true);
    }

    if (config('pausable-jobs.logging.enabled')) {
      Log::channel(config('pausable-jobs.logging.channel'))
        ->info("Job class {$jobClass} was paused");
    }
  }

  public function resumeJobClass(string $jobClass): void
  {
    if ($this->driver === 'redis') {
      Redis::del($this->getKey($jobClass));
    } else {
      Cache::forget($this->getKey($jobClass));
    }

    if (config('pausable-jobs.logging.enabled')) {
      Log::channel(config('pausable-jobs.logging.channel'))
        ->info("Job class {$jobClass} was resumed");
    }
  }

  public function isPaused(string $jobClass): bool
  {
    if ($this->driver === 'redis') {
      return (bool) Redis::exists($this->getKey($jobClass));
    }

    return (bool) Cache::get($this->getKey($jobClass));
  }

  protected function getKey(string $jobClass): string
  {
    return "job:pause:{$jobClass}";
  }
}
