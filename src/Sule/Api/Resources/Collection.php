<?php
namespace Sule\Api\Resources;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sule\Api\Resources\CollectionInterface;

use Illuminate\Support\Contracts\ArrayableInterface;

use Sule\Api\Resources\Resource;

class Collection implements CollectionInterface
{

    protected $data;

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
    * Return ETag based on collection of items
    *
    * @return string    md5 of all ETags
    */
    public function getEtags()
    {
        $etag = '';

        if ($this->data instanceof ArrayableInterface) {
            $this->data = $this->data->toArray();
        }

        foreach ( $this->data as $item ) {
            $item = new Resource($item);
            $etag .= $item->getEtag();
            unset($item);
        }

        return md5( $etag );
    }

}