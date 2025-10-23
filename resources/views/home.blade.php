<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FB Automation – Home</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial;margin:0;background:#fafafa}
    .wrap{max-width:1000px;margin:30px auto;padding:0 16px}
    .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
    .card{background:#fff;border:1px solid #eee;border-radius:12px;padding:16px;margin:10px 0}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px}
    .btn{display:inline-block;padding:10px 14px;border:1px solid #111;border-radius:8px;background:#111;color:#fff;text-decoration:none}
    .btn.secondary{background:#fff;color:#111}
    .row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    code{background:#f4f4f4;padding:2px 6px;border-radius:6px}
    table{width:100%;border-collapse:collapse}
    th,td{border-bottom:1px solid #f0f0f0;padding:10px;text-align:left}
    small.muted{color:#888}
  </style>
</head>
<body>
<div class="wrap">

  <div class="head">
    <h2>FB Automation · Home</h2>
    <div class="row">
      @if (session('status')) <small class="muted">{{ session('status') }}</small> @endif
      @if($user)
        <form method="post" action="{{ route('logout') }}">
          @csrf
          <button class="btn secondary" type="submit">Đăng xuất ({{ $user->name ?? 'User' }})</button>
        </form>
      @else
        <a class="btn" href="{{ route('fb.redirect') }}">Đăng nhập Facebook</a>
      @endif
    </div>
  </div>

  <div class="grid">
    <div class="card">
      <h3>Quick Actions</h3>
      <div class="row" style="margin-top:8px">
        <a class="btn" href="{{ route('fb.redirect') }}">Facebook SSO</a>
        @if($user)
          <a class="btn secondary" href="{{ route('pages.connect') }}">Connect Page</a>
        @endif
      </div>
      <p><small class="muted">Dùng để test đăng nhập và liên kết Page nhanh.</small></p>
    </div>

    <div class="card">
      <h3>Config gợi ý</h3>
      <table>
        <tbody>
          @foreach($configHints as $k => $v)
            <tr><td>{{ $k }}</td><td><code>{{ is_string($v) ? $v : json_encode($v) }}</code></td></tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <h3>Pages đã liên kết</h3>
    @if(!$user)
      <p>Chưa đăng nhập. Bấm <a href="{{ route('fb.redirect') }}">Facebook SSO</a> trước.</p>
    @else
      @if(!$pages->count())
        <p>Chưa có Page nào. Vào <a href="{{ route('pages.connect') }}">Connect Page</a> để liên kết.</p>
      @else
        <table>
          <thead>
            <tr>
              <th>Tên Page</th>
              <th>Meta Page ID</th>
              <th>Token</th>
              <th>Conversations</th>
              <th>Last</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          @foreach($pages as $p)
            <tr>
              <td>{{ $p['name'] }}</td>
              <td><code>{{ $p['meta_page_id'] }}</code></td>
              <td>{!! $p['has_token'] ? '<span style="color:green">OK</span>' : '<span style="color:#b00">Missing</span>' !!}</td>
              <td>{{ $p['inbox_count'] }}</td>
              <td>{{ $p['last_at'] ?? '—' }}</td>
              <td class="row">
                <a class="btn" href="/{{ $p['id'] }}/inbox">Inbox</a>
                <form method="post" action="/{{ $p['id'] }}/subscribe">
                  @csrf
                  <button class="btn secondary" type="submit">Subscribe</button>
                </form>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      @endif
    @endif
  </div>

  <div class="card">
    <h3>Ghi chú test nhanh</h3>
    <ul>
      <li>Webhook URL (GET/POST): <code>{{ url('/webhook/facebook') }}</code></li>
      <li>Verify Token: đặt trong <code>.env</code> với key <code>WEBHOOK_VERIFY_TOKEN</code></li>
      <li>Nhắn tin vào Page từ tài khoản khác &rarr; mở <code>/{page_id}/inbox</code> để xem.</li>
      <li>Gửi trả lời tại trang hội thoại; worker: <code>php artisan queue:work --queue=fb-webhook,fb-send</code></li>
    </ul>
  </div>

</div>
</body>
</html>
