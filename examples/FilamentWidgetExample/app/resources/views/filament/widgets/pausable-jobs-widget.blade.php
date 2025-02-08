<x-filament-widgets::widget>
  <x-filament::card>
    <div class="px-4 py-2">
      <h2 class="text-lg font-bold tracking-tight">
        Pausable Jobs Manager
      </h2>
      <div class="mt-2 space-y-4">
        @foreach ($jobStates as $jobClass => $isRunning)
        <div class="flex items-center justify-between">
          <span class="text-sm">{{ str_replace("App\\Jobs\\", "", $jobClass) }}</span>

          <x-filament::button wire:click="toggleJob('{{ addslashes($jobClass) }}')" x-on:click="close"
            :color="$isRunning ? 'success' : 'danger'" size="xs">
            {{ $isRunning ? 'Running' : 'Paused' }}
          </x-filament::button>
        </div>
        @endforeach
      </div>
    </div>
  </x-filament::card>
</x-filament-widgets::widget>