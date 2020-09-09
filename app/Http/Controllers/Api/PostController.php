<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Attachment;
use App\Models\Comments;
use App\Models\Favorite;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use DB;

class PostController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($fav = false) {
        $user = \Auth::user();

        $category_id = request('category_id');
        $content_type = request('content_type');

        $where_clause = [];

        /**
         * @var $qb Builder
         */
        $qb = Post::limit($this->getLimit());

        $qb->offset($this->getOffset())->select('posts.id', 'posts.category_id', 'source_id', 'text', 'active', 'posts.created_at', 'source')
            ->leftJoin('attachments', 'posts.id', '=', 'attachments.post_id')
            ->orderBy('posts.id', 'desc');

        if ($user && !$category_id) {
            $qb->join('user_categories', 'posts.category_id', '=', 'user_categories.category_id');
            $where_clause[] = ['user_categories.user_id', '=', $user->id];
        }

        if ($category_id) {
            $where_clause[] = ['posts.category_id', '=', $category_id];
        }

        if ($content_type) {
            if ($content_type == 'article') {
                $where_clause[] = ['attachments.type', '=', 'photo'];
//                $qb->orWhereNull('type');
            }
            elseif ($content_type == 'video') {
                $qb->whereIn('attachments.type', ['video', 'gif']);
            }
        }

        if ($fav) {
            $qb->join('favorites', 'posts.id', '=', 'favorites.post_id');
            $where_clause[] = ['favorites.user_id', '=', $user->id];
        }

        $qb->where($where_clause);
        $qb->groupBy('sync_id');
        $qb->orderBy('sync_id', 'desc');

        $posts = $qb->get();

        $posts->map($this->formatPost(true));

        return $this->getPagination($posts, Post::count());
    }

    public function favorites() {
        return $this->index(true);
    }

    public function favorite($id) {
        $user = \Auth::user();

        $fav = new Favorite();
        $fav->post_id = $id;
        $fav->user_id = $user->id;

        try {
            $fav->save();
        } catch (QueryException $e) {
            $fav = Favorite::where([
                ['post_id', '=', $id], ['user_id', '=', $user->id]
            ])->first();

            if ($fav) {
                $fav->delete();
            }
        }

        $post = Post::find($id);
        $cb = $this->formatPost();

        return $cb($post);
    }

    public function like($id) {
        $user = \Auth::user();

        $like = new Like();
        $like->post_id = $id;
        $like->user_id = $user->id;

        try {
            $like->save();
        } catch (QueryException $e) {
            $like = Like::where([
                ['post_id', '=', $id], ['user_id', '=', $user->id]
            ])->first();

            if ($like) {
                $like->delete();
            }
        }

        $post = Post::find($id);
        $cb = $this->formatPost();

        return $cb($post);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post) {
        $formatter = $this->formatPost(true);
        return response()->json($formatter($post));
    }

    private function formatPost($includeCategory = false) {
        return function (Post $post) use ($includeCategory) {
            $post->images = $post
                ->attachments
                ->filter(function (Attachment $item) {
                    return in_array($item->type, ['photo', 'gif']);
                })
                ->map(function (Attachment $item) {
                    return str_replace('\\', '/', $item->path);
                })
                ->toArray();
            $post->videos = $post
                ->attachments
                ->filter(function (Attachment $item) {
                    return in_array($item->type, ['video']);
                })
                ->map(function (Attachment $item) {
                    return $item->link;
                })
                ->toArray();

            if ($user = \Auth::user()) {
                $post->isLike = Like::where('user_id', '=', $user->id)->where('post_id', '=', $post->id)->first() != null ? 1 : 0;
                $post->isFavorite = Favorite::where('user_id', '=', $user->id)->where('post_id', '=', $post->id)->first() != null ? 1 : 0;
            }

            if ($includeCategory) {
                $post->category;
            }

            $post->commentsCount = $post->comments->count();
            $post->likesCount = $post->likes->count();
            $post->favsCount = $post->favorites()->count();

            return $post;
        };
    }


}
