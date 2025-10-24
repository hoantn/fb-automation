{{-- resources/views/home.blade.php --}}
<x-app-layout :title="'Home · FB Automation'">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <section class="card p-6">
      <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
      <div class="flex gap-3">
        <a href="{{ url('/auth/facebook/redirect') }}" class="btn">Facebook SSO</a>
        <a href="{{ url('/pages/connect') }}" class="btn btn-secondary">Connect Page</a>
      </div>
      <p class="text-sm text-gray-500 mt-4">Dùng để test đăng nhập và liên kết Page nhanh.</p>
    </section>

    <section class="card p-6">
      <h2 class="text-lg font-semibold mb-4">Config gợi ý</h2>
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div><dt class="metric-label">APP_URL</dt><dd class="mt-0.5 font-mono text-xs">{{ config('app.url') }}</dd></div>
        <div><dt class="metric-label">FACEBOOK_CLIENT_ID</dt><dd class="mt-0.5 font-mono text-xs">{{ env('FACEBOOK_CLIENT_ID') }}</dd></div>
        <div class="sm:col-span-2">
          <dt class="metric-label">FACEBOOK_REDIRECT_URI</dt>
          <dd class="mt-0.5 font-mono text-xs truncate">{{ url('/auth/facebook/callback') }}</dd>
        </div>
        <div><dt class="metric-label">FACEBOOK_APP_SECRET</dt><dd class="mt-0.5 font-mono text-xs">*** set ***</dd></div>
        <div><dt class="metric-label">WEBHOOK_VERIFY_TOKEN</dt><dd class="mt-0.5 font-mono text-xs">*** set ***</dd></div>
        <div class="sm:col-span-2">
          <dt class="metric-label">Webhook URL (GET/POST)</dt>
          <dd class="mt-0.5 font-mono text-xs truncate">{{ url('/webhook/facebook') }}</dd>
        </div>
      </dl>
    </section>
  </div>

  <section class="card mt-6">
    <div class="p-6">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Pages đã liên kết</h2>
        <a href="{{ url('/pages/connect') }}" class="btn btn-secondary">Connect Page</a>
      </div>

      @php $pages = $pages ?? \App\Models\Page::query()->latest()->get(); @endphp

      @if($pages->isEmpty())
        <p class="text-sm text-gray-500 mt-4">Chưa có Page nào. Vào <a href="{{ url('/pages/connect') }}" class="text-gray-900 underline">Connect Page</a> để liên kết.</p>
      @else
      <div class="overflow-x-auto mt-4">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500 border-b">
              <th class="py-2 pr-4">Tên Page</th>
              <th class="py-2 pr-4">Meta Page ID</th>
              <th class="py-2 pr-4">Token</th>
              <th class="py-2 pr-4">Actions</th>
            </tr>
          </thead>
          <tbody id="linked-pages">
          @foreach($pages as $p)
            <tr class="border-b last:border-0">
              <td class="py-2 pr-4 font-medium">{{ $p->name }}</td>
              <td class="py-2 pr-4 font-mono">{{ $p->meta_page_id }}</td>
              <td class="py-2 pr-4">
                @if($p->access_token) <span class="inline-flex items-center rounded bg-green-50 text-green-700 px-2 py-0.5">OK</span>
                @else <span class="inline-flex items-center rounded bg-yellow-50 text-yellow-700 px-2 py-0.5">Missing</span>
                @endif
              </td>
              <td class="py-2 pr-4">
                <a href="{{ url('/'.$p->id.'/inbox') }}" class="text-gray-900 hover:underline">Inbox</a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </section>

  <section class="card mt-6 p-6">
    <h2 class="text-lg font-semibold mb-2">Ghi chú test nhanh</h2>
    <ul class="list-disc pl-5 text-sm text-gray-600 space-y-1">
      <li>Webhook URL (GET/POST): <code class="font-mono">{{ url('/webhook/facebook') }}</code></li>
      <li>Verify Token: đặt trong <code class="font-mono">.env</code> với key <code class="font-mono">WEBHOOK_VERIFY_TOKEN</code>.</li>
      <li>Nhắn tin vào Page từ tài khoản khác → mở <code class="font-mono">/{page_id}/inbox</code> để xem.</li>
      <li>Gửi trả lời tại trang hội thoại; worker: <code class="font-mono">php artisan queue:work --queue=fb-webhook,fb-send</code></li>
    </ul>
  </section>
</x-app-layout>
