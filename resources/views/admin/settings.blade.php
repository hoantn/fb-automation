<x-app-layout :title="'Admin • Settings'">
  <div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Settings</h1>

    @if(session('success'))
      <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ url('/admin/settings') }}">
      @csrf
      <div class="card p-5 space-y-5">
        <div>
          <label class="block text-sm font-medium mb-1">Dashboard refresh (seconds)</label>
          <input type="number" name="dashboard_refresh_interval" min="1" max="3600"
                 value="{{ old('dashboard_refresh_interval', $dashboard_refresh_interval) }}"
                 class="w-full border rounded px-3 py-2">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Cache duration (seconds)</label>
          <input type="number" name="cache_duration" min="1" max="86400"
                 value="{{ old('cache_duration', $cache_duration) }}"
                 class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex items-center gap-2">
          <input type="checkbox" id="auto_reply_enabled" name="auto_reply_enabled"
                 @checked(old('auto_reply_enabled', $auto_reply_enabled))>
          <label for="auto_reply_enabled" class="text-sm">Enable auto-reply</label>
        </div>

        <div class="pt-2">
          <button class="btn" type="submit">Save</button>
        </div>
      </div>
    </form>
  </div>
</x-app-layout>
