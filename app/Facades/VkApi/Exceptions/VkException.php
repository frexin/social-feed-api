<?php
/**
 * Created by PhpStorm.
 * User: akeinhell
 * Date: 16.07.16
 * Time: 2:27
 */

namespace App\Facades\VkApi\Exceptions;


class VkException extends \Exception
{
    /**
     * VkException constructor.
     * @param array $message
     */
    public function __construct($message = [])
    {
        parent::__construct(array_get($message, 'error_msg', 'failed vk request'));
    }
}