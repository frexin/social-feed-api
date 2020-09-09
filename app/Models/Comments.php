<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model {

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function post() {
        return $this->belongsTo(Post::class);
    }

    public function getPostNameAttribute() {
        return $this->post->text;
    }

    public function getUserNameAttribute() {
        return $this->user->name;
    }
}
