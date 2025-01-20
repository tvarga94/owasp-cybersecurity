<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\AdminRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminRepository implements AdminRepositoryInterface
{
    public function getAllUsers(): Collection
    {
        return User::where('is_admin', false)->get();
    }

    public function findUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function updateUser(User $user, array $attributes): bool
    {
        return $user->update($attributes);
    }

    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }
}
