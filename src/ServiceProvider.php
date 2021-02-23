<?php

namespace WebPajooh\LaravelRepoMan;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
	public function register()
	{
		//
	}

	public function boot()
	{
		$this->mergeConfigFrom( __DIR__ . '/config/repoman.php', 'repoman');

		$this->publishes([
			__DIR__ . '/config/repoman.php' => config_path('repoman.php'),
		]);
	}
}