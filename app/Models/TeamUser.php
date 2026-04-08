<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamUser extends Pivot
{
    use SoftDeletes;

    protected $table = 'team_user';

    protected $fillable = [
        'user_role',
    ];
}
