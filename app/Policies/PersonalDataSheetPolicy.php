<?php

namespace App\Policies;

use App\Models\PersonalDataSheet;
use App\Models\User;

class PersonalDataSheetPolicy
{
    /**
     * User can view/edit PDS if they can view the owner.
     */
    public function view(User $user, PersonalDataSheet $pds): bool
    {
        return $user->id === $pds->user_id || $user->isAdminOrSubAdmin();
    }

    public function update(User $user, PersonalDataSheet $pds): bool
    {
        return $user->id === $pds->user_id || $user->isAdminOrSubAdmin();
    }
}
