<?php

namespace Smitmartijn\PausableJobs;

use Illuminate\Support\ServiceProvider;

class PausableJobsServiceProvider extends ServiceProvider
{
  public function boot()
  {
    $this->publishes([
      __DIR__ . '/../config/pausable-jobs.php' => config_path('pausable-jobs.php'),
    ], ['pausable-jobs', 'pausable-jobs-config']);
  }

  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__ . '/../config/pausable-jobs.php',
      'pausable-jobs'
    );

    $this->app->singleton('job-pause', function ($app) {
      return new JobPauseManager();
    });
  }
}
