@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Settings</h4>
    <p class="text-muted small mb-0">Configure platform settings and preferences</p>
</div>

<div class="row g-3">
    {{-- Left nav --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-2">
                <nav class="nav flex-column settings-side-nav gap-1">
                    <a href="{{ route('admin.settings.edit', ['tab' => 'general']) }}"
                    class="nav-link settings-side-link {{ $tab === 'general' ? 'active' : '' }}">
                        <i class="bi bi-gear me-2"></i> General
                    </a>
                    <a href="{{ route('admin.settings.edit', ['tab' => 'notifications']) }}"
                    class="nav-link settings-side-link {{ $tab === 'notifications' ? 'active' : '' }}">
                        <i class="bi bi-bell me-2"></i> Notifications
                    </a>
                    <a href="{{ route('admin.settings.edit', ['tab' => 'security']) }}"
                    class="nav-link settings-side-link {{ $tab === 'security' ? 'active' : '' }}">
                        <i class="bi bi-shield-lock me-2"></i> Security
                    </a>
                    <a href="{{ route('admin.settings.edit', ['tab' => 'appearance']) }}"
                    class="nav-link settings-side-link {{ $tab === 'appearance' ? 'active' : '' }}">
                        <i class="bi bi-palette me-2"></i> Appearance
                    </a>
                </nav>
            </div>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                {{-- GENERAL --}}
                @if($tab === 'general')
                    <h5 class="fw-bold mb-4">General Settings</h5>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tab" value="general">

                        <div class="mb-3">
                            <label class="form-label">Platform Name</label>
                            <input type="text" name="platform_name" class="form-control"
                                   value="{{ old('platform_name', $settings['platform_name']) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Support Email</label>
                            <input type="email" name="support_email" class="form-control"
                                   value="{{ old('support_email', $settings['support_email']) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Support Phone</label>
                            <input type="text" name="support_phone" class="form-control"
                                   value="{{ old('support_phone', $settings['support_phone']) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="{{ old('whatsapp', $settings['whatsapp']) }}"
                                   placeholder="2557...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Default Currency</label>
                            <select name="default_currency" class="form-select">
                                @foreach(['TZS' => 'TZS — Tanzanian Shilling', 'KES' => 'KES — Kenyan Shilling', 'USD' => 'USD — US Dollar', 'UGX' => 'UGX — Ugandan Shilling'] as $code => $label)
                                    <option value="{{ $code }}" @selected(old('default_currency', $settings['default_currency']) === $code)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Timezone</label>
                            <select name="timezone" class="form-select">
                                @foreach([
                                    'Africa/Dar_es_Salaam' => 'Africa/Dar es Salaam (EAT)',
                                    'Africa/Nairobi' => 'Africa/Nairobi (EAT)',
                                    'UTC' => 'UTC',
                                ] as $tz => $label)
                                    <option value="{{ $tz }}" @selected(old('timezone', $settings['timezone']) === $tz)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Settings
                        </button>
                    </form>
                @endif

                {{-- NOTIFICATIONS --}}
                @if($tab === 'notifications')
                    <h5 class="fw-bold mb-4">Notification Settings</h5>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tab" value="notifications">

                        <div class="mb-3">
                            <label class="form-label">Large sale threshold (TZS)</label>
                            <input type="number" name="large_sale_threshold" class="form-control"
                                   value="{{ old('large_sale_threshold', $settings['large_sale_threshold']) }}">
                            <div class="form-text">Notify owner/manager when a sale is at or above this amount.</div>
                        </div>
                        <div class="form-check mb-4">
                            <input type="checkbox" name="notify_email" value="1" class="form-check-input" id="ne"
                                   @checked(old('notify_email', $settings['notify_email']) == '1')>
                            <label class="form-check-label" for="ne">Enable email notifications (requires SMTP)</label>
                        </div>
                        <button class="btn btn-primary"><i class="bi bi-save"></i> Save Settings</button>
                    </form>
                @endif

                {{-- SECURITY --}}
                @if($tab === 'security')
                    <h5 class="fw-bold mb-4">Security</h5>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tab" value="security">

                        <div class="form-check mb-4">
                            <input type="checkbox" name="registration_open" value="1" class="form-check-input" id="ro"
                                   @checked(old('registration_open', $settings['registration_open']) == '1')>
                            <label class="form-check-label" for="ro">Allow new user registration</label>
                        </div>
                        <button class="btn btn-primary"><i class="bi bi-save"></i> Save Settings</button>
                    </form>
                @endif

                {{-- APPEARANCE — logo only --}}
                @if($tab === 'appearance')
                    <h5 class="fw-bold mb-4">Appearance</h5>
                    <p class="text-muted small">Upload platform logo (used on admin header / emails later). PNG or JPG, max 1MB.</p>

                    @if($settings['platform_logo'])
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $settings['platform_logo']) }}"
                                 alt="Logo" class="rounded border bg-body-tertiary p-2"
                                 style="max-height: 80px;">
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tab" value="appearance">

                        <div class="mb-4">
                            <label class="form-label">Platform logo</label>
                            <input type="file" name="platform_logo" class="form-control" accept="image/png,image/jpeg,image/webp">
                        </div>
                        <button class="btn btn-primary"><i class="bi bi-save"></i> Save Settings</button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>

<style>
    .settings-side-nav {
        width: 100%;
    }

    .settings-side-link {
        display: flex;
        align-items: center;
        border-radius: 0.5rem;
        padding: 0.65rem 0.85rem;
        color: var(--bs-body-color);
        text-decoration: none;
        font-size: 0.925rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        box-sizing: border-box;
    }

    .settings-side-link:hover {
        background: var(--bs-tertiary-bg);
        color: var(--bs-body-color);
    }

    .settings-side-link.active {
        background: var(--bs-primary-bg-subtle, #e0f2fe);
        color: var(--bs-primary);
        font-weight: 600;
    }

    [data-bs-theme="dark"] .settings-side-link.active {
        background: #1e3a5f;
        color: #93c5fd;
    }

    .card.overflow-hidden {
        overflow: hidden;
    }
</style>
@endsection