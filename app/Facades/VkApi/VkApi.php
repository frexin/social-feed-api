<?php
/**
 * Created by PhpStorm.
 * User: akeinhell
 * Date: 16.07.16
 * Time: 2:13
 */

namespace App\Facades\VkApi;

use App\Facades\VkApi\Exceptions\VkException;
use App\Facades\VkApi\Models\Post;
use GuzzleHttp\Client;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VkApi
{
    private $params;

    /**
     * Vk constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.vk.com/method/',
        ]);
        $this->params = ['v' => '5.52'];
    }

    public function setToken($token)
    {
        $this->params['access_token'] = $token;
    }

    public function call($method, array $params = [])
    {
        // todo del this for rate limit
        sleep(1);
        $params   = array_merge($this->params, $params);
        $verify = !app()->environment('local') ? true : false;
        $response = $this->client->get($method, ['query' => $params, 'verify' => $verify])->getBody()->__toString();
        $json     = json_decode($response, true);
        if (!$json) {
            throw new BadRequestHttpException($response);
        }

        if ($error = array_get($json, 'error')) {
            throw new VkException($error);
        }

        return array_get($json, 'response');
    }

    /**
     * @param array $params
     * @return \Illuminate\Support\Collection
     * @throws VkException
     */
    public function getWall(array $params = [])
    {
        $data = $this->call('wall.get', $params);
        return collect(array_get($data, 'items', []))
            ->map(function ($item) {
                return new Post($item);
            });
    }

    /**
     * @param $ownerId
     * @param $videoIds
     * @param $accessKey
     * @return mixed
     * @throws VkException
     */
    public function videoGet($ownerId, $videoIds, $accessKey)
    {
        $data = $this->call('video.get', [
            'owner_id' => $ownerId,
            'videos'   => $this->getObjectIdFrom($ownerId, $videoIds, $accessKey),
        ]);
        return array_get($data, 'items', []);
    }

    /**
     * get doc and filter
     * @param      $ownerId
     * @param      $docId
     * @param      $accessKey
     * @return mixed
     * @throws VkException
     */
    public function docGetById($ownerId, $docId, $accessKey)
    {
        $data = $this->call('docs.getById', [
            'docs' => $this->getObjectIdFrom($ownerId, $docId, $accessKey),
        ]);
        return current($data);
    }

    /**
     * @param $ownerId
     * @param $objectId
     * @param $accessKey
     * @return string
     */
    private function getObjectIdFrom($ownerId, $objectId, $accessKey)
    {
        $pattern = ($accessKey) ? '%s_%s_%s' : '%s_%s';
        return sprintf($pattern, $ownerId, $objectId, $accessKey);
    }
}