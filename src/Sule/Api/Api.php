<?php
namespace Sule\Api;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

use Sule\Api\OAuth2\OAuthServer;
use Sule\Api\OAuth2\Resource;
use Sule\Api\OAuth2\Models\OauthClient;
use Sule\Api\OAuth2\Repositories\FluentClient;
use Sule\Api\OAuth2\Repositories\FluentSession;

use Sule\Api\Facades\Response;

use DateTime;

use League\OAuth2\Server\Exception\ClientException;
use League\OAuth2\Server\Exception\InvalidAccessTokenException;

class Api
{

  /**
   * The config.
   *
   * @var Array
   */
  protected $config;

  /**
   * The Request.
   *
   * @var Request
   */
  protected $request;

  /**
   * The Response.
   *
   * @var Response
   */
  protected $response;

  /**
   * The OAuthServer.
   *
   * @var OAuthServer
   */
  protected $OAuth;

  /**
   * The Resource.
   *
   * @var Resource
   */
  protected $resource;

  /**
   * The current client.
   *
   * @var OauthClient
   */
  protected $client = null;

  /**
   * The current access token.
   *
   * @var string
   */
  protected $accessToken = '';

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
    'invalid_request'       =>  400,
    'unauthorized_client'     =>  400,
    'access_denied'       =>  401,
    'unsupported_response_type' =>  400,
    'invalid_scope'       =>  400,
    'server_error'        =>  500,
    'temporarily_unavailable'   =>  400,
    'unsupported_grant_type'  =>  501,
    'invalid_client'      =>  401,
    'invalid_grant'       =>  400,
    'invalid_credentials'     =>  400,
    'invalid_refresh'       =>  400
  );

	/**
   * Create a new instance.
   *
   * @param  array $config
   * @param  Request $request
   * @param  Response $response
   * @param  OAuthServer $OAuth
   * @param  Resource $resource
   * @return void
   */
  public function __construct(
    Array $config, 
    Request $request, 
    Response $response, 
    OAuthServer $OAuth, 
    Resource $resource
  )
  {
    $this->config   = $config;
    $this->request  = $request;
    $this->response = $response;
    $this->OAuth  = $OAuth;
    $this->resource = $resource;
  }

  /**
   * Return the configuration.
   *
   * @param string $key
   * @return mixed
   */
  public function getConfig($key)
  {
    if (empty($key)) {
      return '';
    }

    $keys  = explode('.', $key);
    $value = array();

    if (isset($this->config[$keys[0]])) {
      $value = $this->config[$keys[0]];
    }

    if (empty($value)) {
      return $value;
    }

    $totalKey = count($keys);

    if ($totalKey > 1) {
      for ($i = 1; $i < $totalKey; ++$i) {
        if (isset($value[$keys[$i]])) {
          $value = $value[$keys[$i]];
        }
      }
    }

    unset($keys);

    return $value;
  }

  /**
   * Return the request handler.
   *
   * @return Request
   */
  public function getRequest()
  {
    return $this->request;
  }

  /**
   * Return the response handler.
   *
   * @return Response
   */
  public function getResponse()
  {
    return $this->response;
  }

  /**
   * Return the OAuth.
   *
   * @return Request
   */
  public function getOAuth()
  {
    return $this->OAuth;
  }

  /**
   * Return the resource handler.
   *
   * @return Resource
   */
  public function getResource()
  {
    return $this->resource;
  }

  /**
   * Return the current client.
   *
   * @return OauthClient|null
   */
  public function getClient()
  {
    if (is_null($this->client)) {
      $this->identifyClientFromRequest();
    }

    return $this->client;
  }

  /**
   * Return current access token.
   *
   * @return string
   */
  public function getAccessToken()
  {
    return $this->accessToken;
  }

  /**
   * Return a new JSON response.
   *
   * @param  string|array  $data
   * @param  int  $status
   * @param  array  $headers
   * @return \Illuminate\Http\JsonResponse
   */
  public function resourceJson($data = array(), $status = 200, array $headers = array())
  {
    $headers = array_merge($headers, $this->getRequestLimitHeader());
    $headers = array_merge($headers, $this->getConfig('headers'));

    return $this->getResponse()->resourceJson($data, $status, $headers);
  }

  /**
   * Return a new JSON response.
   *
   * @param  string|array  $data
   * @param  int  $status
   * @param  array  $headers
   * @return \Illuminate\Http\JsonResponse
   */
  public function collectionJson($data = array(), $status = 200, array $headers = array())
  {
    $headers = array_merge($headers, $this->getRequestLimitHeader());
    $headers = array_merge($headers, $this->getConfig('headers'));

    return $this->getResponse()->collectionJson($data, $status, $headers);
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
    return $this->getOAuth()->getAuthServer()
          ->getGrantType('authorization_code')
          ->newAuthoriseRequest($owner, $owner_id, $options);
  }

  /**
   * Perform the access token flow
   * 
   * @param boolean $directResponse
   * @param array $input
   * @return Response the appropriate response object | array
   */
  public function performAccessTokenFlow($directResponse = true, Array $input = array())
  {
    $authServer = $this->getOAuth()->getAuthServer();

    try {

      // Get user input
      if (empty($input)) {
        $input = $this->getRequest()->all();
      }

      // Tell the auth server to issue an access token
      if ($directResponse) {
        return $this->resourceJson($authServer->issueAccessToken($input));
      }

      return $authServer->issueAccessToken($input);

    } catch (ClientException $e) {

      // Throw an exception because there was a problem with the client's request
      $response = array(
        'message'   => $authServer->getExceptionType($e->getCode()),
        'description' => $e->getMessage()
      );

      // make this better in order to return the correct headers via the response object
      $error   = $authServer->getExceptionType($e->getCode());
      $headers = $authServer->getExceptionHttpHeaders($error);

      if ($directResponse) {
        return $this->resourceJson($response, self::$exceptionHttpStatusCodes[$error], $headers);
      }

      $response['status']  = self::$exceptionHttpStatusCodes[$error];
      $response['headers'] = $headers;

      return $response;

    } catch (Exception $e) {

      // Throw an error when a non-library specific exception has been thrown
      $response = array(
        'message'   => 'undefined_error',
        'description' => $e->getMessage()
      );

      if ($directResponse) {
        return $this->resourceJson($response, 500);
      }

      $response['status'] = 500;

      return $response;
    }
  }

  /**
   * Validate OAuth token
   *
   * @param array $scope additional filter arguments
   * @return Response|null a bad response in case the request is invalid
   */
  public function validateAccessToken(Array $scopes = array())
  {
    try {
      $this->getResource()->isValid($this->getConfig('oauth2.http_headers_only'));
    } catch (InvalidAccessTokenException $e) {
      return $this->resourceJson(array(
        'message'   => 'forbidden',
        'description' => $e->getMessage(),
      ), 403);
    }

    if ( ! empty($scopes)) {
      foreach ($scopes as $item) {
        if ( ! $this->getResource()->hasScope($item)) {
          return $this->resourceJson(array(
            'message'   => 'forbidden',
            'description' => 'Only access token with scope '.$item.' can be use in this endpoint',
          ), 403);
        }
      }
    }
  }

  /**
   * Validate request user-agent.
   *
   * @return boolean
   */
  public function validateUserAgent()
  {
    $userAgent = $this->getRequest()->header('USER_AGENT');

    return ( ! empty($userAgent));
  }

  /**
   * Check client content MD5.
   *
   * @return boolean
   */
  public function isValidMD5()
  {
    // Get client secret if available
    $client     = $this->getClient();
    $clientSecret = '';
    if ( ! is_null($client)) {
      $clientSecret = $client->secret;
    }
    unset($client);

    // Get md5 data from header
    $md5 = $this->getRequest()->header('CONTENT_MD5');
    
    // Do validation for JSON data
    if ($this->getRequest()->isJson()) {
      $content = $this->getRequest()->getContent();

      if (empty($md5) and empty($content)) {
        return true;
      }

      return (md5($content.$clientSecret) == $md5);
    }

    // Do validation for others than JSON data
    $input = $this->getRequest()->all();

    if ( ! empty($input)) {
      foreach($input as $key => $item) {
        if (str_contains($key, '/')) {
          unset($input[$key]);
        }
      }
    }

    if (empty($md5) and empty($input)) {
      return true;
    }

    return (md5(http_build_query($input).$clientSecret) == $md5);
  }

  /**
   * Check client request limit and update.
   *
   * @return boolean
   */
  public function checkRequestLimit()
  {
    $isLimitReached = false;
    $client     = $this->getClient();
    
    if ( ! is_null($client)) {
      $currentTotalRequest = 1;
      $currentTime     = time();
      $requestLimitUntil   = $currentTime;
      
      $requestLimitUntil = strtotime($client->request_limit_until);
      if ($requestLimitUntil < 0) {
        $requestLimitUntil = $currentTime;
      }

      if ($currentTime <= $requestLimitUntil) {
        $currentTotalRequest = $client->current_total_request + 1;

        if ($currentTotalRequest > $client->request_limit) {
          $isLimitReached = true;
        }
      }

      if ($currentTime > $requestLimitUntil) {
        $dateTime = new DateTime('+1 hour');

        $requestLimitUntil = $dateTime->getTimestamp();

        unset($dateTime);
      }

      if ( ! $isLimitReached) {
        $dateTime = new DateTime();

        $client->current_total_request = $currentTotalRequest;
        $client->request_limit_until   = $dateTime->setTimestamp($requestLimitUntil)->format('Y-m-d H:i:s');
        $client->last_request_at     = $dateTime->setTimestamp($currentTime)->format('Y-m-d H:i:s');

        $client->save();

        unset($dateTime);
      }
    }

    unset($client);

    return $isLimitReached;
  }

  /**
   * Return request limit header.
   *
   * @return array
   */
  private function getRequestLimitHeader()
  {
    $headers = array();
    $client  = $this->getClient();

    if ( ! is_null($client)) {
      $headers['X-Rate-Limit-Limit']   = $client->request_limit;
      $headers['X-Rate-Limit-Remaining'] = $client->request_limit - $client->current_total_request;
      $headers['X-Rate-Limit-Reset']   = strtotime($client->request_limit_until) - time();
    }

    unset($client);

    return $headers;
  }

  /**
   * Identify client identifed from current request.
   *
   * @return array
   */
  private function identifyClientFromRequest()
  {
    $clientId   = $this->getRequest()->input('client_id', '');
    $clientSecret = $this->getRequest()->input('client_secret', null);
    $redirectUri  = $this->getRequest()->input('redirect_uri', null);

    try {
      $this->accessToken  = $this->getResource()->determineAccessToken();

      $sessionRepository = new FluentSession();
      $sesion      = $sessionRepository->validateAccessToken($this->accessToken);

      if ($sesion !== false) {
        $clientId   = $sesion['client_id'];
        $clientSecret = $sesion['client_secret'];
      }

      unset($sessionRepository);
      unset($sesion);
    } catch (InvalidAccessTokenException $e) {}

    if ( ! empty($clientId)) {
      $clientRepository = new FluentClient();
      $client       = $clientRepository->getClient($clientId, $clientSecret, $redirectUri);

      if ($client !== false) {
        $client['id']   = $clientId;
        $client['secret'] = $client['client_secret'];

        unset($client['client_id']);
        unset($client['client_secret']);
        unset($client['redirect_uri']);
        unset($client['metadata']);

        $this->client = new OauthClient();

        $this->client->fill($client);
        $this->client->exists = true;
        $this->client->syncOriginal();
      }

      unset($client);
      unset($clientRepository);
    }

    unset($clientId);
    unset($clientSecret);
    unset($redirectUri);
  }

}
