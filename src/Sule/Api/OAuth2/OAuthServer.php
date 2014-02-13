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
     * Exception error HTTP status codes
     * @var array
     *
     * RFC 6749, section 4.1.2.1.:
     * No 503 status code for 'temporarily_unavailable', because
     * "a 503 Service Unavailable HTTP status code cannot be
     * returned to the client via an HTTP redirect"
     */
    protected static $exceptionHttpStatusCodes = array(
        'invalid_request'           =>  400,
        'unauthorized_client'       =>  400,
        'access_denied'             =>  401,
        'unsupported_response_type' =>  400,
        'invalid_scope'             =>  400,
        'server_error'              =>  500,
        'temporarily_unavailable'   =>  400,
        'unsupported_grant_type'    =>  501,
        'invalid_client'            =>  401,
        'invalid_grant'             =>  400,
        'invalid_credentials'       =>  400,
        'invalid_refresh'           =>  400
    );

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
     * @param  string $owner    The owner type
     * @param  string $owner_id The owner id
     * @param  array  $options  Additional options to issue an authorization code
     * @return string           An authorization code
     */
    public function newAuthorizeRequest($owner, $owner_id, $options)
    {
        return $this->authServer->getGrantType('authorization_code')->newAuthoriseRequest($owner, $owner_id, $options);
    }

    /**
     * Perform the access token flow
     * 
     * @return Response the appropriate response object
     */
    public function performAccessTokenFlow()
    {
        try {

            // Get user input
            $input = Input::all();

            // Tell the auth server to issue an access token
            $response = $this->authServer->issueAccessToken($input);

        } catch (ClientException $e) {

            // Throw an exception because there was a problem with the client's request
            $response = array(
                'message'     => $this->authServer->getExceptionType($e->getCode()),
                'description' => $e->getMessage()
            );

            // make this better in order to return the correct headers via the response object
            $error = $this->authServer->getExceptionType($e->getCode());
            $headers = $this->authServer->getExceptionHttpHeaders($error);

            return Response::resourceJson($response, self::$exceptionHttpStatusCodes[$error], $headers);

        } catch (Exception $e) {

            // Throw an error when a non-library specific exception has been thrown
            $response = array(
                'message'     => 'undefined_error',
                'description' => $e->getMessage()
            );

            return Response::resourceJson($response, 500);
        }

        return Response::resourceJson($response);
    }
}
