<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_role'])]
class TeamUser extends Pivot
{
    use SoftDeletes;

    protected $table = 'team_user';
}
