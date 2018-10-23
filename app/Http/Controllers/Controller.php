<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController {

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $vm;

    public function __construct() {

		// dd(auth()->guest());
		// dd(\Auth::guest());

		$website   = \Hyn\Tenancy\Facades\TenancyFacade::website();

		if ($website) {

			$this->db = app(\Hyn\Tenancy\Database\Connection::class)->tenantName();

			\View::addLocation(resource_path('views/_tenants'));
		}

		\View::addLocation(resource_path('views'));

	}
}
