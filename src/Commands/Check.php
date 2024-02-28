<?php

namespace S4mpp\Laraguard\Commands;

use Illuminate\Support\Str;
use S4mpp\Laraguard\Laraguard;
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;

/**
 * @codeCoverageIgnore
 */
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
    public function handle(): int
    {
        foreach (Laraguard::getPanels() as $panel) {
            $this->line(Str::repeat('=', 32));
            $this->line('Panel: '.$panel->getTitle());
            $this->line(Str::repeat('=', 32));

            $guard = $panel->getGuardName();

            Config::has('auth.guards.'.$guard) ? $this->info('Guard '.$guard.' OK') : $this->error('Guard '.$guard.' NOT FOUND');

            Config::has('auth.passwords.'.$guard) ? $this->info('Password resetter '.$guard.' OK') : $this->error('Password resetter '.$guard.' NOT FOUND');

            $model = $panel->getModel();

            if ($model) {
                $name_model = get_class($model);

                $this->info('Model '.$name_model.' OK');

                method_exists($model, 'notify') ? $this->info('Notification Model OK') : $this->error('Notification Model NOT FOUND');

                is_a($model, User::class) ? $this->info('Authentication Model OK') : $this->error('Authentication Model NOT FOUND');
            } else {
                $this->error('Model NOT FOUND');
                $this->error('Notification Model NOT FOUND');
                $this->error('Authentication Model NOT FOUND');
            }

            $this->newLine();
        }

        return 0;
    }
}
