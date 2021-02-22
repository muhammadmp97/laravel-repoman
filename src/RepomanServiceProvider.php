<?php

namespace WebPajooh\Repoman;

use Illuminate\Support\ServiceProvider;

class RepomanServiceProvider extends ServiceProvider
{
	public function register()
	{
		//
	}

	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/config/repoman.php' => config_path('repoman.php'),
			]);
		}

		$this->mergeConfigFrom( __DIR__ . '/config/repoman.php', 'repoman');
	}
}