<?php

namespace Smitmartijn\PausableJobs\Facades;

use Illuminate\Support\Facades\Facade;

class JobPause extends Facade
{
  protected static function getFacadeAccessor()
  {
    return 'job-pause';
  }
}
