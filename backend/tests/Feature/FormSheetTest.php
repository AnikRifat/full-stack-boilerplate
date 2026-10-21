<?php

namespace Tests\Feature;

use App\Livewire\Admin\Roles\Index as RoleIndex;
use App\Livewire\Admin\Users\Index as UserIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FormSheetTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_and_edit_sheets_render_the_form_over_the_list(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $user = User::factory()->create(['name' => 'Karim Hasan']);

        Livewire::test(UserIndex::class)->call('openSheet', 'create')->assertSet('sheet', 'create')
            ->assertSee(__('Add user'))->assertSeeLivewire('admin.users.form');
        Livewire::test(UserIndex::class)->call('openSheet', 'edit:'.$user->id)
            ->assertSee(__('Edit user'))->assertSeeLivewire('admin.users.form')
            ->call('closeSheet')->assertSet('sheet', '')->assertDontSeeLivewire('admin.users.form');
        $this->get(route('admin.users.index', ['sheet' => 'edit:'.$user->id]))->assertOk()->assertSee(__('Edit user'))->assertSee('Karim Hasan');
        Livewire::test(RoleIndex::class)->call('openSheet', 'edit:administrator')->assertSee(__('Edit role'))->assertSeeLivewire('admin.roles.form');
    }

    public function test_sheets_keep_the_form_rules(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $root = User::where('role', 'owner')->sole();

        $this->get(route('admin.users.index', ['sheet' => 'edit:'.$root->id]))->assertForbidden();
        $this->get(route('admin.employees.index', ['sheet' => 'edit:'.User::factory()->create()->id]))->assertNotFound();
        $this->get(route('admin.users.index', ['sheet' => 'edit:999999']))->assertNotFound();
    }

    public function test_sheets_keep_the_form_permissions(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'administrator', 'denied_permissions' => ['users.create', 'users.update']]));

        $this->get(route('admin.users.index'))->assertOk();
        $this->get(route('admin.users.index', ['sheet' => 'create']))->assertForbidden();
        Livewire::test(UserIndex::class)->call('openSheet', 'edit:'.User::factory()->create()->id)->assertForbidden();
    }
}
