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
     * Retrieve a file from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile|array
     */
    public function file($key = null, $default = null)
    {
        return parent::file($key, $default);
    }

    /**
     * Determine if the uploaded data contains a file.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasFile($key)
    {
        return parent::hasFile($key);
    }

    /**
     * Returns the request body content.
     *
     * @param Boolean $asResource If true, a resource will be returned
     *
     * @return string|resource The request body content or a resource to read the body stream.
     *
     * @throws \LogicException
     */
    public function getContent($asResource = false)
    {
        return parent::getContent($asResource);
    }

    /**
     * Retrieve a server variable from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public function server($key = null, $default = null)
    {
        return parent::server($key, $default);
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
     * Returns the path being requested relative to the executed script.
     *
     * The path info always starts with a /.
     *
     * Suppose this request is instantiated from /mysite on localhost:
     *
     *  * http://localhost/mysite              returns an empty string
     *  * http://localhost/mysite/about        returns '/about'
     *  * http://localhost/mysite/enco%20ded   returns '/enco%20ded'
     *  * http://localhost/mysite/about?var=1  returns '/about'
     *
     * @return string The raw path (i.e. not urldecoded)
     *
     * @api
     */
    public function getPathInfo()
    {
        return parent::getPathInfo();
    }

    /**
     * Gets the scheme and HTTP host.
     *
     * If the URL was called with basic authentication, the user
     * and the password are not added to the generated string.
     *
     * @return string The scheme and HTTP host
     */
    public function getSchemeAndHttpHost()
    {
        return parent::getSchemeAndHttpHost();
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
     * @param  string $clientSecret
     * @return boolean
     */
    public function validateMD5Data($clientSecret = '')
    {
        $md5 = $this->header('CONTENT_MD5');

        if (parent::isJson()) {
            $content = parent::getContent();

            if (empty($md5) and empty($content)) {
                return true;
            }

            return (md5($content.$clientSecret) == $md5);
        }

        $query = parent::instance()->query->all();

        if ( ! empty($query)) {
            foreach($query as $key => $item) {
                if (str_contains($key, '/')) {
                    unset($query[$key]);
                }
            }
        }

        if (empty($md5) and empty($query)) {
            return true;
        }

        return (md5(http_build_query($query).$clientSecret) == $md5);
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
