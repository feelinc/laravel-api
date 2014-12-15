<?php
namespace Sule\Api\Commands;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Console\Command;

use Sule\Api\OAuth2\Repositories\SessionManagementInterface;

class CleanExpiredTokens extends Command
{
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'api:cleanExpiredTokens';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'A command to clean the OAuth expired tokens';


  protected $sessions;

  /**
   * Create a new command instance.
   *
   * @param SessionManagementInterface $sessions an implementation of the session management interface
   * @return void
   */
  public function __construct(SessionManagementInterface $sessions)
  {
    parent::__construct();

    $this->sessions = $sessions;
  }

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function fire()
  {
    $result = $this->deleteExpiredTokens();

    $this->info($result . ' expired OAuth tokens were deleted');
  }

  /**
   * Deletes the sessions with expired authorization and refresh tokens from the db
   * 
   * @return int the number of sessions deleted
   */
  protected function deleteExpiredTokens()
  {
    return $this->sessions->deleteExpired();
  }

}
