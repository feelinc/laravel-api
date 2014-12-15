<?php
namespace Sule\Api\Resources;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sule\Api\Resources\ResourceInterface;

class Resource implements ResourceInterface 
{

    protected $data;

    protected $etag = false;

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * Retrieve ETag for single resource
    *
    * @return string ETag for resource
    */
    public function getEtag()
    {
        if ( $this->etag === false ) {
            $this->etag = $this->generateEtag();
        }

        return $this->etag;
    }

    /**
    * Generate ETag for single resource
    *
    * @return string ETag, using md5
    */
    protected function generateEtag()
    {
        $keys      = array();
        $updatedAt = '';

        if (is_array($this->data)) {
            $keys = array_keys($this->data);

            if (isset($this->data['updated_at'])) {
                $updatedAt = $this->data->updated_at;
            }
        }

        if (is_object($this->data)) {
            $objectVars = get_object_vars($this->data);
            $keys = array_keys($objectVars);
            unset($objectVars);

            if (isset($this->data->updated_at)) {
                $updatedAt = $this->data->updated_at;
            }
        }

        $etag = '';

        if (empty($keys)) {
            return $etag;
        }

        foreach($keys as $item) {
            $etag .= $item;
        }

        $etag .= $updatedAt;

        return md5( $etag );
    }

}