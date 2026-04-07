<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'code', 'description', 'status'])]
class Project extends Model
{
    use SoftDeletes, HasFactory;

    public function team() : BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class);
    }
}
