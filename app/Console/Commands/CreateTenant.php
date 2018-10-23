<?php

namespace App\Console\Commands;

use App\User;
use Hyn\Tenancy\Environment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;

use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;

class CreateTenant extends Command
{
    protected $signature = 'tenant:create {name} {email}';

    protected $description = 'Creates a tenant with the provided name and email address e.g. php artisan tenant:create boise boise@example.com';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');

        if ($this->tenantExists($name, $email)) {
            $this->error("A tenant with name '{$name}' and/or '{$email}' already exists.");

            return;
        }

        $hostname = $this->registerTenant($name, $email);
        app(Environment::class)->hostname($hostname);

        $this->info("Tenant '{$name}' is created and is now accessible at {$hostname->fqdn}");
        $this->info("Admin {$email} can log in using password password");
    }

    private function tenantExists($name, $email)
    {
        return User::where('name', $name)->orWhere('email', $email)->exists();
    }

    private function registerTenant($name, $email)
    {
        // create a user
		$user = new User;
		$user->user_name 	= $email;
		$user->site 		= $name;
		$user->email 		= $email;
		$user->password 	= Hash::make('password');

		$user->save();


        // $user = User::create(['site' => $name, 'user_name' => 'teststring', 'email' => $email, 'password' => Hash::make('password')]);

		$website 			= new Website;
		$website->user_id 	= $user->id;
		$website->uuid 		= $user->id.'_'.strtoupper(str_random(10));

		// $website->save();

		// dd($website);
		app(WebsiteRepository::class)->create($website);

		$hostname = new Hostname;
		$hostname->user_id 	= $user->id;
		$baseUrl = config('app.url_base');
        $hostname->fqdn = "{$name}.{$baseUrl}";
		$hostname = app(HostnameRepository::class)->create($hostname);
		app(HostnameRepository::class)->attach($hostname, $website);

        return $hostname;
    }

}
