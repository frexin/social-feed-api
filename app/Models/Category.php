<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $hidden = ['updated_at', 'source'];

    protected $casts = [
        "comments_enabled" => 'boolean',
        "comments_links"   => 'boolean',
        "comments_quest"   => 'boolean',
        "active"           => 'boolean',
    ];

    public function sources()
    {
        return $this->hasMany(Source::class);
    }
}
