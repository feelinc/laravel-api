<?php
namespace Sule\Api\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Sule\Api\Models\OauthClient;

class NewOAuthClient extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'kotakin:newOAuthClient';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new OAuth client.';

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
		$clientName   = $this->argument('name');
		$clientId     = $this->option('id');
		$clientSecret = $this->option('secret');

		if (empty($clientId)) {
			$clientId = Str::random(40);
		}

		if (empty($clientSecret)) {
			$clientSecret = Str::random(40);
		}

		$oAuthClient = OauthClient::create(array(
			'id'     => $clientId,
			'secret' => $clientSecret,
			'name'   => $clientName
		));

		if ($oAuthClient->exists) {
			$this->info('Client Name: '.$clientName);
			$this->info('Client Id: '.$clientId);
			$this->info('Client Secret: '.$clientSecret);
			$this->info('Created');
		} else {
			$this->error('Client Name: '.$clientName);
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
                'name', 
                InputArgument::REQUIRED, 
                'Client Name'
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
                'id', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'Client Id', 
                ''
            ),
            array(
                'secret', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'Client Secret', 
                ''
            )
        );
	}

}