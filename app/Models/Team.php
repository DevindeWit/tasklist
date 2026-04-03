<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;
    /**
     * Get the users that belong to the team.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the projects that belong to the team.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
