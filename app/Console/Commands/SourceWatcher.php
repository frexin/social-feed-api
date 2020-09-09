<?php

namespace App\Console\Commands;

use App\Facades\Vk;
use App\Models\Attachment;
use App\Models\Post;
use App\Models\Source;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use VkApi;

class SourceWatcher extends Command
{
    const POSTS_LIMIT = 500;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:source 
                            {--i|id= : source id} 
                            {--a|alias= : source alias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавляет подходящие источники в очередь';

    /**
     * @var Client
     */
    private $client;

    /**
     * SourceWatcher constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client([
        ]);
    }

    /**
     * Execute the console command.
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $keys      = ['id', 'alias'];
        $searchKey = $searchVal = null;
        foreach ($keys as $key) {
            if ($this->option($key)) {
                $searchKey = $key;
                $searchVal = $this->option($key);
                break;
            }
        }

        $sources = $searchKey ? [Source::where($searchKey, '=', $searchVal)->firstOrFail()] :
            Source::getParseReady();

        if (!$sources) {
            $this->warn('No sources to parse');
        }

        $this->initVk();
        foreach ($sources as $source) {
            try {
                $this->parseSource($source);
            } catch (\Exception $e) {
                $this->error('Error parsing ' . $source->name);
                throw new \Exception($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

    /**
     * @param $link
     * @return bool
     */
    private function getGroupName($link)
    {
        $info      = parse_url($link);
        $groupName = trim(array_get($info, 'path'), '/');

        return $groupName ?: false;
    }

    /**
     * @param Source $source
     */
    private function parseSource($source)
    {
        $categoryId = $source->category->id;

        $source->touch();
        $group = $this->getGroupName($source->link);
        if (!$group) {
            $this->warn('skip source: ' . $source->name);
            return;
        }
        $this->info('Start parse ' . $group);

        $offset   = 0;
        $count    = 0;
        $posts    = [];
        $lastPostId = $source->last_post_id;
        $newPostDate = Carbon::now('UTC')->addHours(-6)->timestamp;
        $stop = false;

        while ($array = $this->getPosts($group, $offset, $source->likes, $newPostDate, $lastPostId, $stop)) {
            $posts = array_merge($posts, $array);
            if ($stop) {
                break;
            }
            $offset += count($array);
            if ($offset >= static::POSTS_LIMIT || empty($array)) {
                break;
            }
        }

        if ($posts) {
            $lastPostId = $posts[0]->id;
        }

        $this->output->progressStart(count($posts));
        foreach ($posts as $vkPost) {
            $this->output->progressAdvance();
            if (Post::whereSyncId($vkPost->id)->first()) {
                continue;
            }
            $post = new Post([
                'sync_id'     => $vkPost->id,
                'text'        => $vkPost->text,
                'category_id' => $categoryId,
                'source_id'   => $source->id,
                'source'      => $source->link . '?w=wall'.$vkPost->from_id . '_' .$vkPost->id,
            ]);
            $post->save();

            if ($vkPost->attachments) {
                $this->saveAttachments($post, $vkPost->attachments, $source->alias);
            }
            $count++;
        }

        if ($lastPostId) {
            $source->last_post_id = $lastPostId;
            $source->save();
        }

        $this->output->progressFinish();
        $this->line('added :' . $count . ' posts');
    }

    /**
     * @param $group
     * @param $offset
     * @return array
     * @throws \Exception
     */
    private function getPosts($group, $offset, $likes, $newPostDate, $lastPostId, &$stop)
    {
        $wall = Vk::getWall([
            'domain' => $group,
            'offset' => $offset,
            'filter' => 'owner',
            'count'  => 100,
        ]);
        $data = [];

        foreach ($wall as $post) {
            if ($lastPostId && $lastPostId == $post->id) {
                $stop = true;
                return $data;
            }

            if (!$post->is_pinned && !$post->isRepost()) {
                if ($post->date >= $newPostDate) {
                    $data[] = $post;
                    continue;
                }
                if ($post->likes['count'] >= $likes) {
                    $data[] = $post;
                }
            }
        }

        return $data;
    }

    /**
     * @param Post                                   $post
     * @param \App\Facades\VkApi\Models\Attachment[] $attachments
     * @return bool
     */
    private function saveAttachments($post, $attachments, $alias)
    {
        $attachs = [];
        foreach ($attachments as $attachment) {
            $type = $attachment->getType();

            switch ($type) {
                case 'photo':
                    $attach = $this->savePhoto($attachment, $alias);
                    break;
                case 'video':
                    $attach = $this->saveVideo($attachment);
                    break;
                case 'doc' :
                    $attach = $this->saveGif($attachment, $alias);
                    break;
                default:
                    $attach = null;
            }
            if ($attach) {
                $attach->post_id = $post->id;
                $attachs[]       = $attach;
            }
            continue;
        }
        if (!$attachs) {
            return false;
        }

        return $post->attachments()->saveMany($attachs);
    }

    /**
     * @param string      $alias
     * @param string      $link
     * @param string|null $fileName
     * @return string
     */
    private function save($alias, $link, $fileName = null)
    {
        $alias    = $this->getGroupName($alias);
        $fileName = date('dmY') . '_' . ($fileName ? basename($fileName) : basename($link));
        $path     = implode(DIRECTORY_SEPARATOR, [$alias, $fileName]);
        if (!\Storage::disk('images')->exists($alias)) {
            \Storage::disk('images')->makeDirectory($alias);
        }

        if (!\Storage::disk('images')->exists($path)) {
            $this->client->get($link, ['sink' => config('filesystems.disks.images.root') . '/' . $path]);
        }
        return 'images/' . $path;
    }

    /**
     * @param \App\Facades\VkApi\Models\Attachment $attachment
     * @return Attachment
     */
    private function savePhoto($attachment, $alias)
    {
        $link = $attachment->getLink();
        $path = $this->save($alias, $link);
        $info = [
            'type' => 'photo',
            'link' => $attachment->getLink(),
            'path' => $path,
        ];
        return new Attachment($info);
    }

    /**
     * init vk
     * TODO get normal token
     */
    private function initVk()
    {
        $token = \Cache::rememberForever('VK_TOKEN', function () {
            $this->info('Go to : http://u.to/8nA8CA and copy token');
            return $this->ask('token: ');
        });
        VkApi::setToken($token);
    }

    /**
     * @param $attachment
     * @return Attachment
     */
    private function saveVideo($attachment)
    {
        $accessKey = isset($attachment->access_key) ? $attachment->access_key : null;
        $video = VkApi::videoGet($attachment->owner_id, $attachment->id, $accessKey);

        if ($link = current($video)) {
            $info = [
                'type' => 'video',
                'link' => array_get($link, 'player'),
                'path' => null,
            ];
            return new Attachment($info);
        }
    }

    /**
     * @param \App\Facades\VkApi\Models\Attachment $attachment
     * @param string                               $alias
     * @return Attachment
     */
    private function saveGif($attachment, $alias)
    {
        $accessKey = isset($attachment->access_key) ? $attachment->access_key : null;
        $gif = VkApi::docGetById($attachment->owner_id, $attachment->id, $accessKey);

        if ($gif) {
            $url      = array_get($gif, 'url');
            $ext      = array_get($gif, 'ext');
            $fileName = $attachment->id . '.' . $ext;

            $info = [
                'type' => 'gif',
                'link' => $url,
                'path' => $this->save($alias, $url, $fileName),
            ];
            return new Attachment($info);
        }
    }

}
