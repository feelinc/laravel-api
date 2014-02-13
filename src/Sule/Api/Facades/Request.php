<?php
namespace Sule\Api\Facades;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Request extends \Illuminate\Support\Facades\Request
{

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function all()
    {
        return parent::all();
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public function input($key = null, $default = null)
    {
        return parent::input($key, $default);
    }

}
