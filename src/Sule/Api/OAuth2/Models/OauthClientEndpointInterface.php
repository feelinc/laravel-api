<?php
namespace Sule\Api\OAuth2\Models;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface OauthClientEndpointInterface
{

    /**
     * Returns the OAuth client endpoint's table name.
     *
     * @return string
     */
    public function getTable();

    /**
     * Returns the OAuth client endpoint's ID.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Saves the OAuth client endpoint.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = array());

    /**
     * Delete the OAuth client endpoint.
     *
     * @return bool
     */
    public function delete();

}
