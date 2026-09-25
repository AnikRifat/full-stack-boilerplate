<?php

namespace Tests\Feature;

use App\Livewire\Admin\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_or_member_cannot_access_profile(): void
    {
        $this->get('/admin/profile')->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create(['role' => 'member']))->get('/admin/profile')->assertForbidden();
    }

    public function test_admin_user_can_view_profile_page(): void
    {
        $user = User::factory()->create(['role' => 'administrator', 'name' => 'Jane Admin', 'email' => 'jane@example.test']);

        $this->actingAs($user)->get('/admin/profile')->assertOk()
            ->assertSee('Jane Admin')->assertSee('jane@example.test')
            ->assertSee(__('Change password'))->assertSee(__('Profile information'));
    }

    public function test_user_can_change_password_with_valid_current_password(): void
    {
        $user = User::factory()->create(['role' => 'administrator', 'password' => 'OldPassword1234']);
        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('current_password', 'OldPassword1234')
            ->set('password', 'NewSecretPass1234')
            ->set('password_confirmation', 'NewSecretPass1234')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertSet('current_password', '')
            ->assertSet('password', '')
            ->assertSet('password_confirmation', '');

        $this->assertTrue(Hash::check('NewSecretPass1234', $user->fresh()->password));
    }

    public function test_password_change_requires_the_current_password_and_a_strong_new_one(): void
    {
        $user = User::factory()->create(['role' => 'administrator', 'password' => 'CorrectPassword123']);
        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('current_password', 'WrongPassword123')
            ->set('password', 'NewSecretPass1234')
            ->set('password_confirmation', 'NewSecretPass1234')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
        Livewire::test(Profile::class)
            ->set('current_password', 'CorrectPassword123')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('updatePassword')
            ->assertHasErrors(['password']);

        $this->assertTrue(Hash::check('CorrectPassword123', $user->fresh()->password));
    }

    public function test_profile_update_normalises_email_and_keeps_it_unique(): void
    {
        User::factory()->create(['email' => 'taken@example.test']);
        $user = User::factory()->create(['role' => 'administrator', 'name' => 'Old Name', 'email' => 'old@example.test']);
        $this->actingAs($user);

        Livewire::test(Profile::class)->set('email', 'taken@example.test')->call('updateProfile')->assertHasErrors(['email']);
        Livewire::test(Profile::class)->set('name', '  New Name ')->set('email', ' NEW@Example.test ')->call('updateProfile')->assertHasNoErrors();

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.test', $user->email);
        $this->assertSame('administrator', $user->role);
    }
}
