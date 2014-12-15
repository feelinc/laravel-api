<?php
namespace Sule\Api\OAuth2\Repositories;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface SessionManagementInterface
{
    public function deleteExpired();
}
