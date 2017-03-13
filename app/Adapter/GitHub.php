<?php
/**
 * Created by PhpStorm.
 * User: etulika
 * Date: 3/7/17
 * Time: 8:14 PM
 */
namespace Vrann\Adapter;

class GitHub extends \Github\Api\AbstractApi
{
    /**
     * Send a GET request with query parameters.
     *
     * @param string $path           Request path.
     * @param array  $parameters     GET parameters.
     * @param array  $requestHeaders Request Headers.
     *
     * @return array|string
     */
    public function getAll($path, array $parameters = array(), array $requestHeaders = array())
    {
        return $this->get($path, $parameters, $requestHeaders);
    }
}