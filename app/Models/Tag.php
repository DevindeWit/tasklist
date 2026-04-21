<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'hex_color', 'description', 'project_id'])]
class Tag extends Model
{
    use SoftDeletes, HasFactory;

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class)
            ->withPivot('added_by')
            ->withTimestamps();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
