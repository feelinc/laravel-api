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

    /**
     * Retrieve a header from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public function header($key = null, $default = null)
    {
        return parent::header($key, $default);
    }

    /**
     * Get the data format expected in the response.
     *
     * @return string
     */
    public function format($default = 'html')
    {
        return parent::format($default);
    }

    /**
     * Returns the user.
     *
     * @return string|null
     */
    public function getUser()
    {
        return parent::getUser();
    }

    /**
     * Returns the password.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return parent::getPassword();
    }

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        return parent::isJson();
    }

    /**
     * Check client request is having content type form.
     *
     * @return boolean
     */
    public function isFormRequest()
    {
        return str_contains($this->header('CONTENT_TYPE'), '/form-data');
    }

    /**
     * Validate the request MD5 data header.
     *
     * @return boolean
     */
    public function validateMD5Data()
    {
        $md5 = $this->header('CONTENT_MD5');

        if (empty($md5)) {
            return false;
        }

        if (parent::isJson()) {
            return (md5(parent::getContent()) == $md5);
        }

        $query = parent::instance()->query->all();

        if ( ! empty($query)) {
            foreach($query as $key => $item) {
                if (str_contains($key, '/')) {
                    unset($query[$key]);
                }
            }
        }

        return (md5(http_build_query($query)) == $md5);
    }

    /**
     * Validate the request User-Agent is not empty.
     *
     * @return boolean
     */
    public function validateUserAgent()
    {
        $userAgent = $this->header('USER_AGENT');

        return ( ! empty($userAgent));
    }

}
