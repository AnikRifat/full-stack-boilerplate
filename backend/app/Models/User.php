<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Support\Permissions;
use App\Concerns\HasMedia;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasMedia, Notifiable;

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed',
            'is_active' => 'boolean', 'extra_roles' => 'array', 'denied_permissions' => 'array'];
    }

    public function isRoot(): bool { return $this->role === Permissions::ROOT_ROLE; }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function hasPermission(string $permission): bool
    {
        return app(Permissions::class)->allows($this, $permission);
    }
}
