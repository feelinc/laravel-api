<?php
namespace Sule\Api\Commands;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Console\Command;
use Illuminate\Support\Str;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Sule\Api\OAuth2\Models\OauthScope;

class NewOAuthScope extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'api:newOAuthScope';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new OAuth scope.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$scope     = $this->argument('scope');
		$name    = $this->argument('name');
		$description = $this->option('description');

    if (empty($name)) {
			$name = $scope;
		}

		$oAuthScope = OauthScope::create(array(
			'scope'     => $scope,
			'name'    => $name,
			'description' => $description
		));

		if ($oAuthScope->exists) {
      $this->info('Scope: '.$scope);
			$this->info('Created');
		} else {
			$this->info('Scope: '.$scope);
			$this->error('Failed');
		}
	}

	/**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      array(
        'scope', 
        InputArgument::REQUIRED, 
        'Scope'
      ),
      array(
        'name', 
        InputArgument::OPTIONAL, 
        'Scope name'
      )
    );
  }

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
      array(
        'description', 
        null, 
        InputOption::VALUE_OPTIONAL, 
        'Scope description', 
        ''
      )
    );
	}

}