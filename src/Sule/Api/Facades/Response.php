<?php
namespace Sule\Api\Facades;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\Facades\Request as IlluminateRequest;

use Sule\Api\Resources\Resource;
use Sule\Api\Resources\Collection;

class Response extends \Illuminate\Support\Facades\Response
{

	/**
     * Return a new JSON response from the application.
     * Inject some headers
     *
     * @param  string|array  $data
     * @param  int    $status
     * @param  array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function json($data = array(), $status = 200, array $headers = array())
    {
        $etagMatch       = false;
        $headersToExpose = array();

        if (array_key_exists('ETag', $headers)) {
            $headersToExpose[] = 'ETag';

            // Empty the data if If-None-Match header match with ETag value
            $requestEtags = IlluminateRequest::getETags();

            if ( ! empty($requestEtags)) {
                foreach ($requestEtags as $etag) {
                    if ($headers['ETag'] == $etag) {
                        $etagMatch = true;
                        $data      = array();

                        break;
                    }
                }
            }

            unset($requestEtags);
        }

        if (array_key_exists('Link', $headers)) {
            $headersToExpose[] = 'Link';
        }

        if (array_key_exists('X-Rate-Limit-Limit', $headers)) {
            $headersToExpose[] = 'X-Rate-Limit-Limit';
        }

        if (array_key_exists('X-Rate-Limit-Remaining', $headers)) {
            $headersToExpose[] = 'X-Rate-Limit-Remaining';
        }

        if (array_key_exists('X-Rate-Limit-Reset', $headers)) {
            $headersToExpose[] = 'X-Rate-Limit-Reset';
        }

        $headers['Access-Control-Allow-Credentials'] = 'true';
        $headers['Access-Control-Allow-Origin']      = '*';
        $headers['Access-Control-Expose-Headers']    = implode(', ', $headersToExpose);

        if ($etagMatch) {
            $response = parent::make('', 304, $headers);
        } else {
            $response = parent::json($data, $status, $headers);
        }

        $response->setCharset('utf-8');

        if (array_key_exists('ETag', $headers)) {
            $response->setCache(array(
                'etag' => $headers['ETag']
            ));
        }

        return $response;
    }

    /**
     * Return a new JSON response from the application.
     * Inject some headers
     *
     * @param  string|array  $data
     * @param  int    $status
     * @param  array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function resourceJson($data = array(), $status = 200, array $headers = array())
    {
        $resource = new Resource($data);
        $etag = $resource->getEtag();

        if ( ! empty($etag)) {
            $headers['ETag'] = $etag;
        }

        unset($resource);

        return self::json($data, $status, $headers);
    }

    /**
     * Return a new JSON response from the application.
     * Inject some headers
     *
     * @param  string|array  $data
     * @param  int    $status
     * @param  array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function collectionJson($data = array(), $status = 200, array $headers = array())
    {
        $collection = new Collection($data);
        $etag = $collection->getEtags();

        if ( ! empty($etag)) {
            $headers['ETag'] = $etag;
        }

        unset($collection);

        return self::json($data, $status, $headers);
    }

}
