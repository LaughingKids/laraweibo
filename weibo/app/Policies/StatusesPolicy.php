<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Statuses;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusesPolicy
{
    use HandlesAuthorization;

    public function destroy(User $user,Statuses $status)
    {
        return $user->id === $status->user_id;
    }
}
