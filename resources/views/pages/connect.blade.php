<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connect Page</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Helvetica Neue',Arial;}
        .wrap{max-width:800px;margin:40px auto;padding:20px}
        .card{border:1px solid #ddd;border-radius:10px;padding:16px;margin-bottom:12px}
        .flex{display:flex;justify-content:space-between;align-items:center}
        button{padding:8px 12px;border-radius:8px;border:1px solid #111;background:#111;color:#fff;cursor:pointer}
        code{background:#f5f5f5;padding:2px 6px;border-radius:6px}
    </style>
</head>
<body>
<div class="wrap">
    <h2>Chọn Page để kết nối</h2>
    @if(!count($pages))
        <p>Không tìm thấy Page. Hãy đảm bảo app đã được cấp quyền <code>pages_show_list</code>.</p>
    @endif
    @foreach($pages as $p)
        <div class="card flex">
            <div>
                <div><strong>{{ $p['name'] ?? 'Page' }}</strong></div>
                <div>ID: <code>{{ $p['id'] ?? '' }}</code> | Category: {{ $p['category'] ?? '-' }}</div>
            </div>
            <form method="post" action="{{ route('pages.connect.post') }}">
                @csrf
                <input type="hidden" name="meta_page_id" value="{{ $p['id'] ?? '' }}">
                <input type="hidden" name="name" value="{{ $p['name'] ?? '' }}">
                <input type="hidden" name="access_token" value="{{ $p['access_token'] ?? '' }}">
                <button type="submit">Connect</button>
            </form>
        </div>
    @endforeach
</div>
</body>
</html>
