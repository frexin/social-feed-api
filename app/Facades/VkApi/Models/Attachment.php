<?php
/**
 * Created by PhpStorm.
 * User: akeinhell
 * Date: 16.07.16
 * Time: 4:01
 */

namespace App\Facades\VkApi\Models;

class Attachment extends BaseModel
{
    protected $data;

    /**
     * Attachment constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        if ($attachmentType = array_get($data, 'type')) {
            $this->data                   = array_get($data, $attachmentType, []);
            $this->data['attachmentType'] = $attachmentType;
        };
    }

    public function getLink()
    {
        switch ($this->getType()) {
            case 'photo':
                return $this->getPhoto();
                break;
            default:
                throw new \Exception('get link for :' . $this->getType() . ' not implemented');
        }
    }

    public function getType()
    {
        return $this->attachmentType;
    }

    function preg_grep_keys($pattern, $input, $flags = 0)
    {
        $keys = preg_grep($pattern, array_keys($input), $flags);
        $vals = array();
        foreach ($keys as $key) {
            $vals[$key] = $input[$key];
        }
        return $vals;
    }

    private function getPhoto()
    {
        return last($this->preg_grep_keys('/photo_[0-9]+/', $this->data));
    }

    private function getVideo()
    {
        return \VkApi::videoGet(array_get($this->data, 'owner_id'), array_get($this->data, 'id'));
    }
}