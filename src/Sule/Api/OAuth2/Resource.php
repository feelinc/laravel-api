<?php
namespace Sule\Api\OAuth2;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Storage\SessionInterface;
use League\OAuth2\Server\Exception\InvalidAccessTokenException;

class Resource extends \League\OAuth2\Server\Resource
{

    /**
     * Sets up the Resource
     *
     * @param SessionInterface  The Session Storage Object
     */
    public function __construct(SessionInterface $session)
    {
        parent::__construct($session);
    }
    
    /**
     * Reads in the access token from the headers.
     *
     * @param $headersOnly Limit Access Token to Authorization header only
     * @throws InvalidAccessTokenException  Thrown if there is no access token presented
     * @return string
     */
    public function determineAccessToken($headersOnly = false)
    {
        try {
            return parent::determineAccessToken($headersOnly);
        } catch (InvalidAccessTokenException $e) {
            return '';
        }
    }
}
