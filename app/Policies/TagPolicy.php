<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->household_id !== null;
    }

    public function view(User $user, Tag $tag): bool
    {
        return $user->household_id === $tag->household_id;
    }

    public function create(User $user): bool
    {
        return $user->household_id !== null;
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->household_id === $tag->household_id;
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->household_id === $tag->household_id
            && $user->isHouseholdOwner();
    }

    public function restore(User $user, Tag $tag): bool
    {
        return false;
    }

    public function forceDelete(User $user, Tag $tag): bool
    {
        return false;
    }
}
