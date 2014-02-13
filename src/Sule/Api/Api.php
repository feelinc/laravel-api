<?php
namespace Sule\Api;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sule\Api\Facades\Request;
use Sule\Api\OAuth2\Models\OauthClient;
use Sule\Api\OAuth2\Repositories\FluentClient;
use Sule\Api\OAuth2\Repositories\FluentSession;

use DateTime;

class Api
{

    protected $request;

    protected $client = null;

	/**
     * Create a new instance.
     *
     * @param  Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
     * Return the current client.
     *
     * @return OauthClient|null
     */
    public function getClient()
    {
        if (is_null($this->client)) {
            $this->fetchClientFromRequest();
        }

        return $this->client;
    }

    /**
     * Fetch client identifed from current request.
     *
     * @return array
     */
    private function fetchClientFromRequest()
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

}
