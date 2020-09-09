<?php
/**
 * Created by PhpStorm.
 * User: akeinhell
 * Date: 16.07.16
 * Time: 2:32
 */

namespace App\Facades\VkApi\Models;

class Post extends BaseModel
{
    /**
     * @var Attachment[]
     */
    public $attachments;

    /**
     * Post constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->attachments = collect(array_get($data, 'attachments', []))
            ->map(function ($item) {
                return new Attachment($item);
            })
            ->toArray();
    }

    public function isRepost()
    {
        return isset($this->copy_history);
    }

}