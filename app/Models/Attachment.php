<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'type',
        'link',
        'path',
    ];

    public function getPathAttribute($value)
    {
        return \URL::to($value, [], true);
    }
}
