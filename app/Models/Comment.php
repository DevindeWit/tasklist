<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;
    /**
     * Task that this comment belongs to
     */
    public function task() : BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * User that this comment belongs to
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
