<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model {
    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public static function getParseReady() {
        return static::all();
    }

    public function getCategoryNameAttribute() {
        return $this->category->name;
    }
}
