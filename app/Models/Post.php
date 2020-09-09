<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    protected $fillable = ['category_id', 'source_id', 'sync_id', 'text', 'source'];

    protected $hidden = ['category_id', 'source_id', 'sync_id', 'updated_at', 'attachments', 'comments'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function attachments() {
        return $this->hasMany(Attachment::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function source() {
        return $this->belongsTo(Source::class);
    }

    public function comments() {
        return $this->hasMany(Comments::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function favorites() {
        return $this->hasMany(Favorite::class);
    }
}
