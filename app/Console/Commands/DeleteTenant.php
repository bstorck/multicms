<?php

namespace App\Console\Commands;

// use Hyn\Tenancy\Contracts\Repositories\CustomerRepository;
use App\User;
// use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
// use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Environment;
// use Hyn\Tenancy\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;

use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;

class DeleteTenant extends Command
{
    protected $signature = 'tenant:delete {id}';
    protected $description = 'Deletes a tenant of the provided name. Only available on the local environment e.g. php artisan tenant:delete boise';

    public function handle()
    {

        if (!app()->isLocal()) {
            $this->error('This command is only avilable on the local environment.');

            return;
        }

        $id = $this->argument('id');

        $this->deleteTenant($id);
    }

    private function deleteTenant($id)
    {
		try {
			$user = User::findOrFail($id);

			if ($this->confirm('Are you sure you want to delete this user?')) {
				$hostname 	= Hostname::where('user_id', $user->id)->firstOrFail();
				$website 	= Website::where('user_id', $user->id)->firstOrFail();
				app(HostnameRepository::class)->delete($hostname, true);
	            app(WebsiteRepository::class)->delete($website, true);
	            $this->info("User successfully deleted.");
			}

			User::destroy($user->id);
			//
			// $hostname 	= $user->hostnames->first();


		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}

		// dd("made");
    }
}
