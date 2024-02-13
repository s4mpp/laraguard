<?php

namespace S4mpp\Laraguard\Commands;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use S4mpp\Laraguard\Helpers\{Credential, User};

final class MakeUser extends Command
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
        try {
            $guard = $this->option('guard');

            $panel = Laraguard::getPanel($guard);

            throw_if(!$panel, 'Invalid guard/panel');

            $model = $panel->getModel();

            $name = $this->option('name') ?? 'User';

            $email = $this->option('email') ?? Credential::suggestEmail($model, $name);
        
            $validator = Validator::make([
                'name' => $name,
                'email' => $email,
            ], [
                'name' => ['required', 'string', 'min:3', 'max:150', "regex:/^[a-zA-ZãáàéèíìõóòúùÁÀÉÈÍÌÓÒÚÙçÇ.' ]+$/u"],
                'email' => ['required', 'string', 'email', 'unique:'.$model->getTable()],
            ]);

            $validator->stopOnFirstFailure()->validate();

            $password = Credential::generatePassword();

            $user = new $model;
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);

            $user->save();

            $this->line('User created successfully:');

            $this->info('Name: '.$name);
            $this->info('E-mail: '.$email);
            $this->info('Password: '.$password);
            $this->info('URL: '.route($panel->getRouteName('login')));

            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
