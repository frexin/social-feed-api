<?php
/**
 * Created by PhpStorm.
 * User: akeinhell
 * Date: 16.07.16
 * Time: 2:15
 */

namespace App\Facades\VkApi\Models;


class BaseModel
{
    /**
     * @var array
     */
    protected $data;

    /**
     * BaseModel constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $name
     * @return mixed
     */
    function __get($name)
    {
        return array_get($this->data, $name);
    }

    /**
     * @param string $name
     * @return bool
     */
    function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    public function toArray()
    {
        return $this->data;
    }
}