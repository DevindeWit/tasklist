<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['body'])]
class Comment extends Model
{
    use SoftDeletes, HasFactory;

    public string $body_md {
        get => Str::markdown($this->body ?? '');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task() : BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
