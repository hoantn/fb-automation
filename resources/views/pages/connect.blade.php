@extends('layouts.app')
@section('title', 'Connect Page')
@section('content')
<div class="mb-6"><h1 class="text-xl font-semibold">Chọn Page để kết nối</h1><p class="text-sm text-slate-500">Danh sách lấy từ Facebook /me/accounts</p></div>
@if(empty($pages))
<div class="rounded-lg border bg-white p-6 text-sm text-slate-500">Không có page nào hoặc thiếu quyền. Hãy login lại bằng SSO rồi thử lại.</div>
@else
<div class="space-y-4">
@foreach($pages as $pg)
@php
$pid=$pg['id']??'';$name=$pg['name']??'—';$cat=$pg['category']??'—';$token=$pg['access_token']??null;
@endphp
<div class="rounded-lg border bg-white p-4">
  <div class="flex items-center justify-between">
    <div><div class="font-medium">{{ $name }}</div><div class="mt-1 text-xs text-slate-500">ID: <span class="font-mono">{{ $pid }}</span><span class="mx-2">·</span>Category: {{ $cat }}</div></div>
    <form method="POST" action="{{ url('/pages/connect') }}">@csrf<input type="hidden" name="meta_page_id" value="{{ $pid }}"><input type="hidden" name="name" value="{{ $name }}">@if($token)<input type="hidden" name="access_token" value="{{ $token }}">@endif<button class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">Connect</button></form>
  </div>
</div>
@endforeach
</div>
@endif
@endsection
