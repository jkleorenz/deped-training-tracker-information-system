<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Admin and sub-admin can view list of all personnel.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isSubAdmin();
    }

    /**
     * Admin and sub-admin can view any user; personnel can view only themselves.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->isAdmin() || $user->isSubAdmin()) {
            return true;
        }
        return $user->id === $model->id;
    }

    /**
     * Admin and sub-admin can create personnel.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSubAdmin();
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
