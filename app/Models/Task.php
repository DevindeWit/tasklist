<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['title', 'descriptions', 'status', 'priority', 'due_date', 'estimate_minutes'])]

class Task extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Automatically convert markdown to HTML via property hook.
     */
    public string $description_md {
        get => Str::markdown($this->description ?? '');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function watchers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('notify_email');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)
            ->withPivot('added_by')
            ->withTimestamps();
    }
}
