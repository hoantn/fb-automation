@extends('admin.layouts.master')
@section('title','Settings')
@section('content')
@if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="card-title">Hệ thống</h5>
    <form method="post" action="/admin/settings">
      @csrf
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="sw1" name="auto_reply_enabled" value="1" @if($config['auto_reply_enabled']) checked @endif>
        <label class="form-check-label" for="sw1">Bật Auto-reply</label>
      </div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Dashboard refresh (seconds)</label>
          <input type="number" class="form-control" name="dashboard_refresh_interval" min="3" max="60" value="{{ $config['dashboard_refresh_interval'] }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Cache duration (seconds)</label>
          <input type="number" class="form-control" name="cache_duration" min="5" max="120" value="{{ $config['cache_duration'] }}" required>
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-dark">Lưu cấu hình</button>
      </div>
    </form>
  </div>
</div>
@endsection
