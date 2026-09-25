<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        Gate::authorize('admin.access');
        /** @var User $user */
        $user = auth()->user();

        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile(): void
    {
        Gate::authorize('admin.access');
        /** @var User $user */
        $user = auth()->user();

        $this->name = trim($this->name);
        $this->email = strtolower(trim($this->email));

        $data = $this->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [], [
            'name' => __('full name'),
            'email' => __('email address'),
        ]);

        $user->fill($data)->save();

        session()->flash('success', __('Profile updated successfully.'));
    }

    public function updatePassword(): void
    {
        Gate::authorize('admin.access');
        /** @var User $user */
        $user = auth()->user();

        $data = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(12)->letters()->numbers()],
        ], [], [
            'current_password' => __('current password'),
            'password' => __('new password'),
            'password_confirmation' => __('confirm new password'),
        ]);

        $user->forceFill([
            'password' => $data['password'],
        ])->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('success', __('Password changed successfully.'));
    }

    public function render(): View
    {
        /** @var User $user */
        $user = auth()->user();
        $permissions = app(Permissions::class);

        return view('livewire.admin.profile', [
            'user' => $user,
            'permissions' => $permissions,
        ])->layout('layouts.admin');
    }
}
