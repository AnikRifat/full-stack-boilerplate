<div class="page">
    <x-notices />
    <x-page-header :title="__('Welcome back, :name', ['name' => auth()->user()->name])" :description="__('Your application\'s core administration.')" />
    @php
        $stats = array_filter([
            ['label' => __('Users'), 'value' => $userCount, 'emoji' => '👤', 'route' => 'admin.users.index'],
            ['label' => __('Employees'), 'value' => $employeeCount, 'emoji' => '👥', 'route' => 'admin.employees.index'],
            ['label' => __('Assignable roles'), 'value' => $roleCount, 'emoji' => '🔐', 'route' => 'admin.roles.index'],
            ['label' => __('Media files'), 'value' => $mediaCount, 'emoji' => '🖼️', 'route' => 'admin.media'],
        ], fn (array $stat): bool => $stat['value'] !== null);
    @endphp
    @if($stats)
        <div class="stats">
            @foreach($stats as $stat)
                <x-stat wire:key="stat-{{ $loop->index }}" :label="$stat['label']" :value="number_format($stat['value'])" :emoji="$stat['emoji']" :href="route($stat['route'])" />
            @endforeach
        </div>
    @endif
    @php
        $shortcuts = array_filter([
            ['ability' => 'users.create', 'label' => __('Add a user'), 'hint' => __('Invite someone to sign in'), 'emoji' => '➕', 'href' => route('admin.users.index', ['sheet' => 'create'])],
            ['ability' => 'employees.create', 'label' => __('Add an employee'), 'hint' => __('Account with a staff profile'), 'emoji' => '🧑‍💼', 'href' => route('admin.employees.index', ['sheet' => 'create'])],
            ['ability' => 'media.upload', 'label' => __('Upload a file'), 'hint' => __('Images and documents'), 'emoji' => '📤', 'href' => route('admin.media')],
            ['ability' => 'settings.view', 'label' => __('Application settings'), 'hint' => __('Name, support email, sign-up'), 'emoji' => '⚙️', 'href' => route('admin.settings')],
        ], fn (array $shortcut): bool => auth()->user()->can($shortcut['ability']));
    @endphp
    <x-card :title="__('Start with the essentials')" :description="__('Manage people, control access, update application settings and keep your files in one media library. Add your project\'s modules when you need them.')">
        @if($shortcuts)
            <div class="quick-actions">
                @foreach($shortcuts as $shortcut)
                    <a class="card quick-action" href="{{ $shortcut['href'] }}" wire:navigate wire:key="shortcut-{{ $loop->index }}"><span class="quick-action-emoji" aria-hidden="true">{{ $shortcut['emoji'] }}</span><span><strong>{{ $shortcut['label'] }}</strong><span class="muted">{{ $shortcut['hint'] }}</span></span></a>
                @endforeach
            </div>
        @endif
    </x-card>
</div>
