<?php

namespace S4mpp\Laraguard\Commands;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Console\Command;
use S4mpp\Laraguard\Helpers\{Credential, User};
use Illuminate\Support\Facades\{Hash, Validator};

/**
 * @codeCoverageIgnore
 */
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
    public function handle(): int
    {
        try {
            $guard = $this->option('guard');

            if (! is_string($guard)) {
                throw new \Exception('Invalid guard name');
            }

            $panel = Laraguard::getPanel($guard);

            throw_if(! $panel, 'Invalid guard/panel');

            $model = $panel?->getModel();

            if (! $model) {
                throw new \Exception('Invalid model');
            }

            $name = $this->option('name') ?? 'User';

            if (! is_string($name)) {
                throw new \Exception('Invalid name');
            }

            $email = $this->option('email');

            if (! is_string($email) && ! is_null($email)) {
                throw new \Exception('Invalid email');
            }

            $email ??= Credential::suggestEmail($model, $name);

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
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        return 0;
    }
}
