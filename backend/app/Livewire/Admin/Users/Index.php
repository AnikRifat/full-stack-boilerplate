<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Concerns\WithFormSheet;
use App\Livewire\Concerns\WithTableTools;
use App\Models\User;
use App\Support\Permissions;
use App\Support\TableExport;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFormSheet, WithPagination, WithTableTools;

    #[Locked]
    public bool $employee = false;

    public string $search = '';

    public string $status = '';

    public function mount(bool $employee = false): void
    {
        $this->employee = $employee;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function module(): string
    {
        return $this->employee ? 'employees' : 'users';
    }

    public function render(): View
    {
        Gate::authorize($this->module().'.view');

        return view('livewire.admin.users.index', [
            'users' => $this->tableQuery()->paginate(15),
            'permissions' => app(Permissions::class),
        ])->layout('layouts.admin');
    }

    /** @return Builder<User> every account except the super admin, filtered like the list */
    protected function tableQuery(): Builder
    {
        Gate::authorize($this->module().'.view');
        $search = mb_substr(trim($this->search), 0, 100);

        return User::with('employee')->where('role', '!=', Permissions::ROOT_ROLE)
            ->when($this->employee, fn ($query) => $query->whereHas('employee'))
            ->when($this->status !== '', fn ($query) => $query->where('is_active', $this->status === 'active'))
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')
                ->when($this->employee, fn ($q) => $q->orWhereHas('employee', fn ($e) => $e->where('employee_code', 'like', '%'.$search.'%')))))
            ->latest('id');
    }

    protected function tableExport(): TableExport
    {
        $permissions = app(Permissions::class);

        return new TableExport($this->employee ? __('Employees') : __('Users'), array_filter([
            'name' => ['label' => __('Name'), 'value' => fn (User $user): string => $user->name],
            'email' => ['label' => __('Email address'), 'value' => fn (User $user): string => $user->email],
            'code' => $this->employee ? ['label' => __('Employee code'), 'value' => fn (User $user): ?string => $user->employee?->employee_code] : null,
            'job' => $this->employee ? ['label' => __('Job title'), 'value' => fn (User $user): ?string => $user->employee?->job_title] : null,
            'department' => $this->employee ? ['label' => __('Department'), 'value' => fn (User $user): ?string => $user->employee?->department] : null,
            'phone' => $this->employee ? ['label' => __('Phone'), 'value' => fn (User $user): ?string => $user->employee?->phone] : null,
            'role' => ['label' => __('Role'), 'value' => fn (User $user): string => $permissions->label($user->role)],
            'status' => ['label' => __('Status'), 'value' => fn (User $user): string => $user->is_active ? __('Active') : __('Inactive')],
            'created' => ['label' => __('Created'), 'value' => fn (User $user) => $user->created_at, 'type' => 'date'],
        ]));
    }
}
