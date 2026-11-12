<?php

namespace App\Policies;

use App\Models\User;

class EmployePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'receptionniste'], true);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->role->name === 'admin';
    }

    public function update(User $user): bool
    {
        return $user->role->name === 'admin';
    }

    /** Performances, montants et taux de commission. */
    public function voirPerformances(User $user): bool
    {
        return $user->role->name === 'admin';
    }
}