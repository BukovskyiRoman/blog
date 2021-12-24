<?php

namespace App\Models;

use App\Events\AddNewComment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Comment extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * @return void
     */
    protected static function booted()
    {
        static::saved(function ($comment) {
            //return $comment->id;
            Cache::put('comments-last-write-time', Carbon::now(), 86400 * 30);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function like() {
        return $this->hasMany(Like::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
