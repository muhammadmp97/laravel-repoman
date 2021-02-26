<?php

namespace WebPajooh\LaravelRepoMan;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use WebPajooh\LaravelRepoMan\Middleware\RepoManMiddleware;

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

		$this->registerMiddleware(RepoManMiddleware::class);
	}

    private function registerMiddleware($middleware)
    {
        $this->app[Kernel::class]->pushMiddleware($middleware);
    }
}
