<x-app-layout :title="'Chọn Page để kết nối'">
  <div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Chọn Page để kết nối</h1>

    @php $pages = $pages ?? []; @endphp

    @forelse($pages as $p)
      @php
        $pid     = data_get($p, 'id');
        $pname   = data_get($p, 'name');
        $pcat    = data_get($p, 'category');
        $ptoken  = data_get($p, 'access_token');
        $pscopes = data_get($p, 'scopes', []);
        if (is_array($pscopes)) $pscopes = json_encode($pscopes, JSON_UNESCAPED_UNICODE);
        $issued  = data_get($p, 'issued_by_user_id');
        $status  = data_get($p, 'status', 'active');
      @endphp

      <div class="card p-5 mb-4">
        <div class="flex items-center justify-between gap-4">
          <div>
            <div class="font-semibold text-base">{{ $pname }}</div>
            <div class="text-xs text-gray-500">
              ID: <span class="font-mono">{{ $pid }}</span>
              @if($pcat)<span class="mx-2">|</span> Category: {{ $pcat }}@endif
            </div>
          </div>

          <form method="POST" action="{{ url('/pages/connect') }}">
            @csrf
            <input type="hidden" name="page_id" value="{{ $pid }}">
            <input type="hidden" name="name" value="{{ $pname }}">
            <input type="hidden" name="access_token" value="{{ $ptoken }}">
            <input type="hidden" name="scopes" value="{{ $pscopes }}">
            <input type="hidden" name="issued_by_user_id" value="{{ $issued }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <button type="submit" class="btn">Connect</button>
          </form>
        </div>
      </div>
    @empty
      <div class="card p-6">
        <p class="text-gray-600 text-sm">Không có page nào để kết nối.</p>
      </div>
    @endforelse
  </div>
</x-app-layout>
