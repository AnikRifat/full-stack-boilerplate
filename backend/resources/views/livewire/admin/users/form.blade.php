<div class="page">
    <x-notices />
    <x-page-header :title="$userId ? ($employee ? __('Edit employee') : __('Edit user')) : ($employee ? __('Add employee') : __('Add user'))" :description="__('Accounts share one identity and permission system.')" :back="route('admin.'.$this->module().'.index')" :back-label="$employee ? __('Employees') : __('Users')" />
    <form wire:submit="save" class="stack">
        <x-card :title="__('Account details')">
            <div class="form-grid">
                <x-form.input name="name" :label="__('Full name')" wire:model="name" required maxlength="255" autocomplete="off" />
                <x-form.input name="email" :label="__('Email address')" type="email" wire:model="email" required autocomplete="off" />
                <x-form.input name="password" :label="__('Password')" type="password" wire:model="password" autocomplete="new-password" :help="$userId ? __('Leave blank to keep the current password.') : __('12+ characters with letters and numbers.')" />
                <x-form.input name="password_confirmation" :label="__('Confirm password')" type="password" wire:model="password_confirmation" autocomplete="new-password" />
            </div>
            <x-form.checkbox name="isActive" :label="__('Account is active and can sign in')" wire:model="isActive" />
        </x-card>
        @if($employee)
            <x-card :title="__('Employee profile')">
                <div class="form-grid">
                    <x-form.input name="employeeCode" :label="__('Employee code')" wire:model="employeeCode" required maxlength="80" autocomplete="off" />
                    <x-form.input name="phone" :label="__('Phone')" type="tel" wire:model="phone" maxlength="40" autocomplete="off" />
                    <x-form.input name="jobTitle" :label="__('Job title')" wire:model="jobTitle" maxlength="255" />
                    <x-form.input name="department" :label="__('Department')" wire:model="department" maxlength="255" />
                </div>
            </x-card>
        @endif
        <x-card :title="__('Roles and access')">
            <div class="form-grid">
                <x-form.select name="role" :label="__('Primary role')" wire:model.live="role" :options="$roleOptions" :disabled="! auth()->user()->hasPermission('roles.assign')" :help="__('Disabled roles cannot be newly assigned. The super admin is protected.')" />
            </div>
            <fieldset class="stack-sm"><legend>{{ __('Extra system roles') }}</legend>
                <div class="flex flex-wrap gap-x-6 gap-y-2">@foreach($registry->systemRoles() as $key)<div wire:key="extra-{{ $key }}"><x-form.checkbox name="extraRoles" :id="'extra-'.$key" :label="$registry->label($key)" :value="$key" wire:model.live="extraRoles" :disabled="! auth()->user()->hasPermission('roles.assign')" /></div>@endforeach</div>
            </fieldset>
            @can('permissions.manage')
                <fieldset class="stack-sm"><legend>{{ __('Personal permissions') }}</legend><p class="muted">{{ __('Uncheck an ability to restrict this account. Personal permissions can never exceed the union of its roles.') }}</p>
                    <div class="permission-grid">@foreach(config('permissions.catalogue') as $group => $abilities)<div class="permission-group" wire:key="permission-group-{{ $loop->index }}"><h3>{{ __($group) }}</h3>@foreach($abilities as $ability)<div wire:key="ability-{{ $ability }}"><x-form.checkbox name="permissions" :id="'permission-'.$ability" :label="str($ability)->replace('.', ' ')->headline()" :value="$ability" wire:model="permissions" :disabled="! in_array($ability, $ceiling, true)" /></div>@endforeach</div>@endforeach</div>
                </fieldset>
            @endcan
        </x-card>
        <x-form.actions :submit="$employee ? __('Save employee') : __('Save user')" :cancel="route('admin.'.$this->module().'.index')" />
    </form>
</div>
