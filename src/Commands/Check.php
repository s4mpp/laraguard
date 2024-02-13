<?php

namespace S4mpp\Laraguard\Commands;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;

final class Check extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraguard:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the paneis is configured correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (Laraguard::getPanels() as $panel) {
            $this->line('Panel: '.$panel->getTitle());

            $guard = $panel->getGuardName();

            Config::has('auth.guards.'.$guard) ? $this->info('Guard '.$guard.' OK') : $this->error('Guard '.$guard.' NOT FOUND');

            Config::has('auth.passwords.'.$guard) ? $this->info('Password resetter '.$guard.' OK') : $this->error('Password resetter '.$guard.' NOT FOUND');

            $model = $panel->getModel();

            $name_model = get_class($model) ?? null;

            $model ? $this->info('Model '.$name_model.' OK') : $this->error('Model '.$name_model.' NOT FOUND');

            method_exists($model, 'notify') ? $this->info('Notification Model OK') : $this->error('Notification Model NOT FOUND');

            is_subclass_of($model, User::class) ? $this->info('Authentication Model OK') : $this->error('Authentication Model NOT FOUND');

            $this->newLine();
        }

        return 0;
    }
}
