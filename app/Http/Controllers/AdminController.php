<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Interfaces\AdminRepositoryInterface;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminController extends Controller
{
    private AdminRepositoryInterface $userRepository;

    public function __construct(AdminRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(): View
    {
        $users = $this->userRepository->getAllUsers();

        return view('admin.dashboard', compact('users'));
    }

    public function edit(int $id): View
    {
        $user = $this->userRepository->findUserById($id);

        return view('admin.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $attributes = $request->validated();
        $this->userRepository->updateUser($user, $attributes);

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = $this->userRepository->findUserById($id);
        $this->userRepository->deleteUser($user);

        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully');
    }
}
