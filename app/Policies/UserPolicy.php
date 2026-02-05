<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Admin can view list of all personnel.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin can view any user; personnel can view only themselves.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $model->id;
    }

    /**
     * Only admin can create/update/delete personnel (for admin panel).
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}
