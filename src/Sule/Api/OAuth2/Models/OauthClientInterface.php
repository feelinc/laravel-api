<?php
namespace Sule\Api\OAuth2\Models;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface OauthClientInterface
{

  /**
   * Returns the OAuth client's table name.
   *
   * @return string
   */
  public function getTable();

  /**
   * Returns the OAuth client's ID.
   *
   * @return mixed
   */
  public function getId();

  /**
   * Saves the OAuth client.
   *
   * @param  array  $options
   * @return bool
   */
  public function save(array $options = array());

  /**
   * Delete the OAuth client.
   *
   * @return bool
   */
  public function delete();

}
