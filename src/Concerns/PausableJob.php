<?php

namespace Smitmartijn\PausableJobs\Concerns;

use Smitmartijn\PausableJobs\Middleware\JobPauseMiddleware;

trait PausableJob
{
  // Used to store the time the job was first paused to honor the max retry time
  public $firstPauseTime;

  public function middleware()
  {
    return [new JobPauseMiddleware()];
  }

  public function __sleep()
  {
    $properties = array_keys(get_object_vars($this));
    return array_merge($properties, ['firstPauseTime']);
  }
}
