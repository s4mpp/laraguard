<?php

namespace S4mpp\Laraguard\Commands;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Console\Command;
use S4mpp\Laraguard\Helpers\User;
use Illuminate\Support\Facades\Hash;
use S4mpp\Laraguard\Helpers\Credential;

class MakeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraguard:make-user {--name= : Name User} {--email= : E-mail user} {--guard=web : Guard panel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            $guard = $this->option('guard');

            $panel = Laraguard::getPanel($guard);

            $model = $panel->getModel();

            $name = $this->option('name') ?? Credential::suggestName();
            Credential::validateName($name);

            $email = $this->option('email') ?? Credential::suggestEmail($model, $name);
            Credential::validateEmail($panel->getModel(), $email);

            $password = Credential::generatePassword();

            $user = new $model;
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            
            $user->save();

            $this->info('User created successfully:');
            $this->info('Name: '.$name);
            $this->info('E-mail: '.$email);
            $this->info('Password: '.$password);

            $this->info('URL: '.route($panel->getRouteName('login')));
        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }

        return 0;
    }
}
