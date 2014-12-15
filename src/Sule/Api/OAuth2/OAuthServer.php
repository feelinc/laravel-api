<?php
namespace Sule\Api\OAuth2;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\Facades\Input;

use League\OAuth2\Server\Authorization as Authorization;

use Sule\Api\Facades\Response;

use League\OAuth2\Server\Exception\ClientException;
use Exception;

class OAuthServer
{
  /**
   * The OAuth authorization server
   * @var [type]
   */
  protected $authServer;

  /**
   * Create a new OAuthServer
   * 
   * @param Authorization $authServer the OAuth Authorization Server to use
   */
  public function __construct(Authorization $authServer)
  {
    $this->authServer = $authServer;
  }

  /**
   * Pass the method call to the underlying Authorization Server
   * 
   * @param  string $method the method being called
   * @param  array|null $args the arguments of the method being called
   * @return mixed the underlying method retuned value
   */
  public function __call($method, $args)
  {
    switch (count($args)) {
      case 0:
        return $this->authServer->$method();
      case 1:
        return $this->authServer->$method($args[0]);
      case 2:
        return $this->authServer->$method($args[0], $args[1]);
      case 3:
        return $this->authServer->$method($args[0], $args[1], $args[2]);
      case 4:
        return $this->authServer->$method($args[0], $args[1], $args[2], $args[3]);
      default:
        return call_user_func_array(array($this->authServer, $method), $args);
    }
  }

  /**
   * Return the Auth Server.
   *
   * @return Authorization
   */
  public function getAuthServer()
  {
    return $this->authServer;
  }

  /**
   * Check the authorization code request parameters
   * 
   * @throws \OAuth2\Exception\ClientException
   * @return array Authorize request parameters
   */
  public function checkAuthorizeParams()
  {
    $input = Input::all();
    return $this->authServer->getGrantType('authorization_code')->checkAuthoriseParams($input);
  }

  /**
   * Authorize a new client
   * @param  string $owner  The owner type
   * @param  string $owner_id The owner id
   * @param  array  $options  Additional options to issue an authorization code
   * @return string       An authorization code
   */
  public function newAuthorizeRequest($owner, $owner_id, $options)
  {
    return $this->authServer->getGrantType('authorization_code')->newAuthoriseRequest($owner, $owner_id, $options);
  }

}
