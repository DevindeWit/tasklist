<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    /**
     * Get tasks that have this tag
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class)
            ->withPivot('added_by', 'added_at');
    }
}
