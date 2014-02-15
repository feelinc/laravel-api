<?php
namespace Sule\Api;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\ServiceProvider;

use Sule\Api\Api;
use Sule\Api\Facades\Request;
use Sule\Api\OAuth2\OAuthServer;

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
		$this->package('sule/api', 'sule/api', 'sule/api');

        // Load the filters
        include __DIR__.'/../../filters.php';

        // Load the routes
        require_once __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $config = $this->app['config']->get('sule/api::oauth2');

        echo '<pre>';
        print_r($config);
        echo '</pre>';
        exit();

        $this->registerOAuthServer($config);
        $this->registerApi($config);

        unset($config);

        // Register artisan commands
		$this->registerCommands();
	}

    /**
     * Register the OAuth server.
     *
     * @param array $config
     * @return void
     */
    private function registerOAuthServer(Array $config)
    {
        $this->app->bind('League\OAuth2\Server\Storage\ClientInterface', 'Sule\Api\OAuth2\Repositories\FluentClient');
        $this->app->bind('League\OAuth2\Server\Storage\ScopeInterface', 'Sule\Api\OAuth2\Repositories\FluentScope');
        $this->app->bind('League\OAuth2\Server\Storage\SessionInterface', 'Sule\Api\OAuth2\Repositories\FluentSession');
        $this->app->bind('Sule\Api\OAuth2\Repositories\SessionManagementInterface', 'Sule\Api\OAuth2\Repositories\FluentSession');

        $this->app['api.authorization'] = $this->app->share(function ($app) use ($config) {

            $server = $app->make('League\OAuth2\Server\Authorization');

            // add the supported grant types to the authorization server
            foreach ($config['grant_types'] as $grantKey => $grantValue) {

                $server->addGrantType(new $grantValue['class']($server));
                $server->getGrantType($grantKey)->setAccessTokenTTL($grantValue['access_token_ttl']);

                if (array_key_exists('callback', $grantValue)) {
                    $server->getGrantType($grantKey)->setVerifyCredentialsCallback($grantValue['callback']);
                }

                if (array_key_exists('auth_token_ttl', $grantValue)) {
                    $server->getGrantType($grantKey)->setAuthTokenTTL($grantValue['auth_token_ttl']);
                }

                if (array_key_exists('refresh_token_ttl', $grantValue)) {
                    $server->getGrantType($grantKey)->setRefreshTokenTTL($grantValue['refresh_token_ttl']);
                }

                if (array_key_exists('rotate_refresh_tokens', $grantValue)) {
                    $server->getGrantType($grantKey)->rotateRefreshTokens($grantValue['rotate_refresh_tokens']);
                }
            }

            $server->requireStateParam($config['state_param']);

            $server->requireScopeParam($config['scope_param']);

            $server->setScopeDelimeter($config['scope_delimiter']);

            $server->setDefaultScope($config['default_scope']);

            $server->setAccessTokenTTL($config['access_token_ttl']);

            return new OAuthServer($server);

        });

        // $this->app['api.resource'] = $this->app->share(function ($app) {

        //     return $app->make('League\OAuth2\Server\Resource');

        // });
    }

    /**
     * Register the Api.
     *
     * @param array $config
     * @return void
     */
    public function registerApi(Array $config)
    {
        $this->app['api'] = $this->app->share(function ($app) use ($config) {
            return new Api(
                $config, 
                new Request(), 
                $app['api.authorization'], 
                $app['api.resource']
            );
        });
    }

	/**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        // Command to create a new OAuth client
        $this->app['command.api.newOAuthClient'] = $this->app->share(function ($app) {
            return $app->make('Sule\Api\Commands\NewOAuthClient');
        });

        // Command to create a new OAuth scope
        $this->app['command.api.newOAuthScope'] = $this->app->share(function ($app) {
            return $app->make('Sule\Api\Commands\NewOAuthScope');
        });

        // Command to clean expired OAuth tokens
        $this->app['command.api.cleanExpiredTokens'] = $this->app->share(function ($app) {
            return $app->make('Sule\Api\Commands\CleanExpiredTokens');
        });

        $this->commands(
        	'command.api.newOAuthClient', 
            'command.api.newOAuthScope', 
            'command.api.cleanExpiredTokens'
        );
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('api', 'api.authorization', 'api.resource');
	}

}