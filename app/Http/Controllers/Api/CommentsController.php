<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Abuse;
use App\Models\Comments;
use App\Models\Post;
use App\Models\User;
use Mail;

class CommentsController extends Controller {
    const ROLE_ADMIN = 1;
    const ROLE_MODERATOR = 2;
    /**
     * Display a listing of the resource.
     *
     * @param $post
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post) {
        return response()->json(['data' => $this->formatCommentsResponse($post->comments)]);
    }

    public function abuse($id) {
        $abuse = Abuse::where(['user_id' => \Auth::user()->id, 'comment_id' => $id])->first();
        $comment = Comments::where(['id' => $id])->first();

        if (!$abuse) {
            $abuse = new Abuse();
            $moderators = User::join('role_user', 'users.id', '=', 'role_user.user_id')->whereIn('role_user.role_id',
                [self::ROLE_ADMIN, self::ROLE_MODERATOR])->get()->map(function($m) {
                return $m->email;
            })->toArray();
            Mail::send('emails.abuse', ['user' => \Auth::user(), 'comment' => $comment], function($m) use ($moderators) {
                $m->from('noreply@ruvi.tv', 'Ruvi App');
                $m->to($moderators);
            });
            $abuse->user_id = \Auth::user()->id;
            $abuse->comment_id = $comment->id;
            $abuse->save();
        }

        return response()->json([
            'success' => $abuse,
        ]);
    }

    public function create($id) {
        $user = \Auth::user();
        $request = \Request::instance();

        $comment = new Comments();
        $comment->post_id = $id;
        $comment->user_id = $user->id;
        $comment->text = $request->getContent();

        $comment->save();

        return response()->json(['comment' => $comment]);
    }

}
