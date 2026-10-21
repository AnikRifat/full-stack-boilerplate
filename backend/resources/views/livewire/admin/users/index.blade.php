<div class="page">
    <x-notices />
    <x-page-header :title="$employee ? __('Employees') : __('Users')" :description="__('Manage accounts, active status and assigned roles.')">
        @can($this->module().'.create')<x-slot:actions><x-button icon="plus" :href="route('admin.'.$this->module().'.create')" :navigate="false" wire:click.prevent="openSheet('create')">{{ $employee ? __('Add employee') : __('Add user') }}</x-button></x-slot:actions> @endcan
    </x-page-header>
    <x-card flush>
        <x-slot:toolbar>
            <x-toolbar :active="(int) ($status !== '')">
                <x-form.input name="search" :label="$employee ? __('Search by name, email or code') : __('Search by name or email')" wire:model.live.debounce.300ms="search" type="search" maxlength="100" :placeholder="$employee ? __('Name, email or code…') : __('Name or email…')" />
                <x-slot:filters>
                    <x-form.select name="status" :label="__('Status')" wire:model.live="status" :options="['' => __('Any status'), 'active' => __('Active'), 'inactive' => __('Inactive')]" />
                </x-slot:filters>
                <x-slot:clear><x-button variant="ghost" icon="filter-x" x-on:click="$wire.set('status', '')">{{ __('Clear filters') }}</x-button></x-slot:clear>
                <x-slot:actions><x-table.export :columns="$this->tableColumns()" /></x-slot:actions>
            </x-toolbar>
        </x-slot:toolbar>
        <x-table.bulk />
        <x-table :caption="$employee ? __('Employees') : __('Users')">
            <x-slot:head><x-table.check-all :ids="$users->pluck('id')->all()" /><th>{{ __('Name') }}</th>@if($employee)<th>{{ __('Employee code') }}</th><th>{{ __('Department') }}</th>@endif<th>{{ __('Role') }}</th><th>{{ __('Status') }}</th><th class="actions-col"><span class="sr-only">{{ __('Actions') }}</span></th></x-slot:head>
            @forelse($users as $user)
                <tr wire:key="user-{{ $user->id }}">
                    <x-table.check :value="$user->id" :label="$user->name" />
                    <td><div class="flex items-center gap-3"><span class="avatar" aria-hidden="true">{{ collect(preg_split('/\s+/', trim($user->name)))->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->join('') }}</span><div><strong>{{ $user->name }}</strong><p class="muted">{{ $user->email }}</p></div></div></td>
                    @if($employee)<td>{{ $user->employee->employee_code }}</td><td>{{ $user->employee->department ?: '—' }}@if($user->employee->job_title)<p class="muted">{{ $user->employee->job_title }}</p>@endif</td>@endif
                    <td><x-badge tone="primary">{{ $permissions->label($user->role) }}</x-badge>@if($user->extra_roles)<p class="muted">{{ __('+ :count extra', ['count' => count($user->extra_roles)]) }}</p>@endif</td>
                    <td><x-badge.active :active="$user->is_active" /></td>
                    <td><div class="row-actions">@can($this->module().'.update')<x-button variant="ghost" size="sm" icon="pencil" :href="route('admin.'.$this->module().'.edit', $user)" :navigate="false" wire:click.prevent="openSheet('edit:{{ $user->id }}')" :label="__('Edit :name', ['name' => $user->name])">{{ __('Edit') }}</x-button>@endcan</div></td>
                </tr>
            @empty
                <x-table.empty :colspan="$employee ? 7 : 5" emoji="👥">{{ $employee ? __('No employees found.') : __('No users found.') }}</x-table.empty>
            @endforelse
        </x-table>
        {{ $users->links() }}
    </x-card>
    <x-sheet :label="$employee ? __('Employee') : __('User')" size="lg">
        @if($this->sheetAction() === 'create')
            <livewire:admin.users.form :employee="$employee" :key="'sheet-'.$sheet" />
        @elseif($this->sheetAction() === 'edit')
            <livewire:admin.users.form :user="\App\Models\User::query()->findOrFail((int) $this->sheetArgument())" :employee="$employee" :key="'sheet-'.$sheet" />
        @endif
    </x-sheet>
</div>
