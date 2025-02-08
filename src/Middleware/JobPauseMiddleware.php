<?php

namespace Smitmartijn\PausableJobs\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Smitmartijn\PausableJobs\JobPauseManager;

class JobPauseMiddleware
{
  protected $pauseManager;

  public function __construct()
  {
    $this->pauseManager = app(JobPauseManager::class);
  }

  public function handle($job, Closure $next)
  {
    $jobClass = get_class($job);
    if ($this->pauseManager->isPaused($jobClass)) {
      // Check/set first pause time against the maximum time to retry
      if (!isset($job->firstPauseTime)) {
        $job->firstPauseTime = now();
      } else {
        $maxRetryTime = config('pausable-jobs.max_retry_time', 3600);
        if (now()->diffInSeconds($job->firstPauseTime) > $maxRetryTime) {
          if (config('pausable-jobs.logging.enabled')) {
            Log::channel(config('pausable-jobs.logging.channel'))
              ->info("Job {$jobClass} exceeded max retry time of {$maxRetryTime} seconds and was not retried");
          }
          $job->delete();
          throw new \Exception("Job {$jobClass} exceeded max retry time of {$maxRetryTime} seconds and was not retried");
          return;
        }
      }

      // Get properties and create new instance
      $properties = get_object_vars($job);
      $newJob = new $jobClass();
      foreach ($properties as $key => $value) {
        if ($key !== 'job') {
          $newJob->{$key} = $value;
        }
      }

      // Re-dispatch with same settings
      $delay = now()->addSeconds(config('pausable-jobs.retry_after', 30));
      dispatch($newJob)
        ->onConnection($job->job->getConnectionName())
        ->onQueue($job->job->getQueue())
        ->delay($delay);

      if (config('pausable-jobs.logging.enabled')) {
        Log::channel(config('pausable-jobs.logging.channel'))
          ->info("Job {$jobClass} with id " . $job->job->getJobId() . " was paused and will retry in " . config('pausable-jobs.retry_after') . " seconds");
      }

      $job->delete();
      return;
    }

    return $next($job);
  }
}
