<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Smitmartijn\PausableJobs\Facades\JobPause;

class PausableJobsWidget extends Widget
{
  protected static string $view = 'filament.widgets.pausable-jobs-widget';

  protected int $refreshInterval = 5;

  public array $jobStates = [];

  public function mount(): void
  {
    $this->loadJobStates();
  }

  public function loadJobStates(): void
  {
    $jobClasses = $this->getJobClasses();
    $this->jobStates = collect($jobClasses)
      ->mapWithKeys(fn($class) => [
        $class => !JobPause::isPaused($class)
      ])
      ->toArray();
  }

  public function toggleJob(string $jobClass): void
  {
    if ($this->jobStates[$jobClass]) {
      JobPause::pauseJobClass($jobClass);
    } else {
      JobPause::resumeJobClass($jobClass);
    }

    $this->jobStates[$jobClass] = !$this->jobStates[$jobClass];
  }

  protected function getJobClasses(): array
  {
    $jobsPath = app_path('Jobs');
    $jobClasses = [];

    if (!is_dir($jobsPath)) {
      return [];
    }

    $files = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($jobsPath)
    );

    foreach ($files as $file) {
      if ($file->isFile() && $file->getExtension() === 'php') {
        $className = 'App\\Jobs\\' . str_replace(
          ['/', '.php'],
          ['\\', ''],
          substr($file->getPathname(), strlen(app_path('Jobs')) + 1)
        );

        if (class_exists($className)) {
          $reflection = new \ReflectionClass($className);
          if (
            $reflection->isInstantiable() &&
            in_array('Smitmartijn\\PausableJobs\\Concerns\\PausableJob', array_keys($reflection->getTraits()))
          ) {
            $jobClasses[] = $className;
          }
        }
      }
    }

    // Sort the job classes alphabetically
    sort($jobClasses);
    return $jobClasses;
  }
}
