@extends('layouts.app')
@section('title', 'FB Automation · Home')
@section('content')
<div class="mb-6 flex items-center justify-between">
  <div><h1 class="text-xl font-semibold">Home</h1><p class="text-sm text-slate-500">Tổng quan & Pages đã liên kết</p></div>
  <div class="flex gap-3">
    <a href="{{ url('/auth/facebook/redirect') }}" class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-900">Facebook SSO</a>
    <a href="{{ url('/pages/connect') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">Connect Page</a>
  </div>
</div>
<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
  <div class="rounded-lg border bg-white p-4"><div class="text-xs uppercase text-slate-500">Pages</div><div class="mt-2 text-2xl font-semibold">{{ $pages->count() }}</div></div>
  <div class="rounded-lg border bg-white p-4"><div class="text-xs uppercase text-slate-500">Today</div><div class="mt-2 text-2xl font-semibold">—</div></div>
  <div class="rounded-lg border bg-white p-4"><div class="text-xs uppercase text-slate-500">Broadcasts</div><div class="mt-2 text-2xl font-semibold">—</div></div>
  <div class="rounded-lg border bg-white p-4"><div class="text-xs uppercase text-slate-500">Worker</div><div class="mt-2 text-2xl font-semibold">fb-webhook / fb-send</div></div>
</div>
<div class="rounded-lg border bg-white">
  <div class="border-b px-4 py-3 font-medium">Pages đã liên kết</div>
  @if($pages->isEmpty())
  <div class="px-4 py-6 text-sm text-slate-500">Chưa có Page nào. Vào <a href="{{ url('/pages/connect') }}" class="text-indigo-600 hover:underline">Connect Page</a> để liên kết.</div>
  @else
  <div class="divide-y">@foreach($pages as $p)<div class="px-4 py-4 flex items-center justify-between">
    <div><div class="font-medium">{{ $p->name }}</div><div class="mt-1 text-xs text-slate-500">Meta Page ID: <span class="font-mono">{{ $p->meta_page_id ?? '—' }}</span><span class="mx-2">·</span>Token:@if($p->access_token)<span class="rounded bg-emerald-50 px-2 py-0.5 text-emerald-700">OK</span>@else<span class="rounded bg-amber-50 px-2 py-0.5 text-amber-700">Thiếu</span>@endif</div></div>
    <div class="flex items-center gap-2"><a href="{{ url('/'.$p->id.'/inbox') }}" class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50">Inbox</a><a href="{{ url('/pages/connect') }}" class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50">Re-Connect</a></div>
  </div>@endforeach</div>
  @endif
</div>
<div class="mt-6 rounded-lg border bg-white p-4">
  <div class="font-medium mb-2">Ghi chú test nhanh</div>
  <ul class="list-disc pl-5 text-sm text-slate-600 space-y-1">
    <li>Webhook URL (GET/POST): <code class="bg-slate-100 px-1 py-0.5 rounded">{{ url('/webhook/facebook') }}</code></li>
    <li>Verify Token: đặt trong <code>.env</code> với key <code>WEBHOOK_VERIFY_TOKEN</code></li>
    <li>Nhắn tin vào Page từ tài khoản khác → mở <code>/{page_id}/inbox</code> để xem.</li>
    <li>Worker: <code>php artisan queue:work --queue=fb-webhook,fb-send</code></li>
  </ul>
</div>
@endsection
