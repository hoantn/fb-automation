@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">Settings</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="card p-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Dashboard refresh interval (seconds)</label>
            <input type="number" name="dashboard_refresh_interval" class="form-control @error('dashboard_refresh_interval') is-invalid @enderror"
                   value="{{ old('dashboard_refresh_interval', $dashboard_refresh_interval) }}" min="1" max="3600">
            @error('dashboard_refresh_interval') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Cache duration (seconds)</label>
            <input type="number" name="cache_duration" class="form-control @error('cache_duration') is-invalid @enderror"
                   value="{{ old('cache_duration', $cache_duration) }}" min="1" max="86400">
            @error('cache_duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="auto_reply_enabled" id="autoReply"
                   {{ old('auto_reply_enabled', $auto_reply_enabled) ? 'checked' : '' }}>
            <label class="form-check-label" for="autoReply">Enable auto reply</label>
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
