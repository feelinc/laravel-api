<?php
namespace Sule\Api;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\ServiceProvider;

use Sule\Api\Commands\NewOAuthClient;

class ApiServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('sule/api');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerCommands();
	}

	/**
     * Register the artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        // Remove any expired link command
        $this->app['command.api.newOAuthClient'] = $this->app->share(
            function ($app) {
                return new NewOAuthClient();
            }
        );

        $this->commands(
        	'command.api.newOAuthClient'
        );
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('api');
	}

}