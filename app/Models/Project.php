<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    /**
     * Get the team that the project belongs to.
     */
    public function team() : BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the tasks that belong to the project.
      */
    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class);
    }
}
