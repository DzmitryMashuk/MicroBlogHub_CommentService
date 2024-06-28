<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    public const STATUS_ACTIVE = 1;

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'status',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            if (is_null($comment->status)) {
                $comment->status = self::STATUS_ACTIVE;
            }
        });
    }
}
