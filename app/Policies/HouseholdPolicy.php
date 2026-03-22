<?php

namespace App\Policies;

use App\Models\Household;
use App\Models\User;

class HouseholdPolicy
{
    public function view(User $user, Household $household): bool
    {
        return $user->household_id === $household->id;
    }

    public function update(User $user, Household $household): bool
    {
        return $household->owner_id === $user->id;
    }

    public function addMember(User $user, Household $household): bool
    {
        return $household->owner_id === $user->id
            && $household->members()->count() < $household->max_members;
    }

    public function removeMember(User $user, Household $household, User $target): bool
    {
        return $household->owner_id === $user->id
            && $target->id !== $household->owner_id;
    }
}
