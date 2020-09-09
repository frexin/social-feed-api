<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AccessToken extends Model
{
    protected $fillable = ['token', 'expired_at', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string
     */
    public static function generate()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * @return void;
     */
    public function updateExpiredAt()
    {
        $this->expired_at = Carbon::now()->addDays(7)->toDateTimeString();
    }

    /**
     * @return bool
     */
    public function expired()
    {
        $now = Carbon::now();
        $expiredAt = Carbon::createFromTimestamp(strtotime($this->expired_at));
        if ($now->gte($expiredAt)) {
            return true;
        }
        return false;
    }
}
