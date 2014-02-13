<?php
namespace Sule\Api;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sule\Api\Facades\Request;
use Sule\Api\Facades\Response;
use Sule\Api\OAuth2\OAuthServer;
use Sule\Api\OAuth2\Models\OauthClient;
use Sule\Api\OAuth2\Repositories\FluentClient;
use Sule\Api\OAuth2\Repositories\FluentSession;

use DateTime;

use League\OAuth2\Server\Exception\ClientException;

class Api
{

    protected $request;

    protected $OAuth;

    protected $client = null;

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
     * Create a new instance.
     *
     * @param  Request $request
     * @return void
     */
    public function __construct(Request $request, OAuthServer $OAuth)
    {
        $this->request = $request;
        $this->OAuth   = $OAuth;
    }

    /**
     * Return a new JSON response.
     *
     * @param  string|array  $data
     * @param  int    $status
     * @param  array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function resourceJson($data = array(), $status = 200, array $headers = array())
    {
        $headers = array_merge($headers, $this->getRequestLimitHeader());

        return Response::resourceJson($data, $status, $headers);
    }

    /**
     * Return a new JSON response.
     *
     * @param  string|array  $data
     * @param  int    $status
     * @param  array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function collectionJson($data = array(), $status = 200, array $headers = array())
    {
        $headers = array_merge($headers, $this->getRequestLimitHeader());

        return Response::collectionJson($data, $status, $headers);
    }

    /**
     * Perform the access token flow
     * 
     * @return Response the appropriate response object
     */
    public function performAccessTokenFlow()
    {
        $authServer = $this->getOAuth()->getAuthServer();

        try {

            // Get user input
            $input = $this->getRequest()->all();

            // Tell the auth server to issue an access token
            return $this->resourceJson($authServer->issueAccessToken($input));

        } catch (ClientException $e) {

            // Throw an exception because there was a problem with the client's request
            $response = array(
                'message'     => $authServer->getExceptionType($e->getCode()),
                'description' => $e->getMessage()
            );

            // make this better in order to return the correct headers via the response object
            $error   = $authServer->getExceptionType($e->getCode());
            $headers = $authServer->getExceptionHttpHeaders($error);

            return $this->resourceJson($response, self::$exceptionHttpStatusCodes[$error], $headers);

        } catch (Exception $e) {

            // Throw an error when a non-library specific exception has been thrown
            $response = array(
                'message'     => 'undefined_error',
                'description' => $e->getMessage()
            );

            return $this->resourceJson($response, 500);
        }
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
     * Return the OAuth.
     *
     * @return Request
     */
    public function getOAuth()
    {
        return $this->OAuth;
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
     * Check client request limit and update.
     *
     * @return boolean
     */
    public function checkRequestLimit()
    {
        $isLimitReached = false;
        $client         = $this->getClient();
        
        if ( ! is_null($client)) {
            $currentTotalRequest = 1;
            $currentTime         = time();
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
                $client->last_request_at       = $dateTime->setTimestamp($currentTime)->format('Y-m-d H:i:s');

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
            $headers['X-Rate-Limit-Limit']     = $client->request_limit;
            $headers['X-Rate-Limit-Remaining'] = $client->request_limit - $client->current_total_request;
            $headers['X-Rate-Limit-Reset']     = strtotime($client->request_limit_until) - time();
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
        $clientId     = $this->getRequest()->input('client_id', '');
        $clientSecret = $this->getRequest()->input('client_secret', null);
        $redirectUri  = $this->getRequest()->input('redirect_uri', null);
        $accessToken  = $this->getRequest()->input('validateAccessToken', '');

        if ( ! empty($accessToken)) {
            $sessionRepository = new FluentSession();
            $sesion            = $sessionRepository->validateAccessToken($accessToken);

            if ($sesion !== false) {
                $clientId = $sesion->client_id;
            }

            unset($sessionRepository);
            unset($sesion);
        }

        if ( ! empty($clientId)) {
            $clientRepository = new FluentClient();
            $client           = $clientRepository->getClient($clientId, $clientSecret, $redirectUri);

            if ($client !== false) {
                $client['id']     = $clientId;
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
        }

        unset($clientId);
        unset($clientSecret);
        unset($redirectUri);
        unset($accessToken);
    }

}
