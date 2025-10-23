<!doctype html>
<html>
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Chat - {{ $page->name }}</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial;margin:0}
.head{padding:16px 20px;border-bottom:1px solid #eee;display:flex;justify-content:space-between}
.wrap{max-width:900px;margin:20px auto}
.msg{padding:8px 12px;border-radius:10px;max-width:70%;margin:8px 0}
.in{background:#f4f4f4}
.out{background:#111;color:#fff;margin-left:auto}
form{display:flex;gap:8px;margin-top:12px}
input[type=text]{flex:1;padding:10px;border:1px solid #ddd;border-radius:8px}
button{padding:10px 14px;border-radius:8px;border:1px solid #111;background:#111;color:#fff}
</style>
</head>
<body>
<div class="head">
  <div><a href="/{{ $page->id }}/inbox" style="text-decoration:none">&larr; Inbox</a></div>
  <div><b>{{ $customer->name ?? ('PSID '.$customer->psid) }}</b></div>
</div>
<div class="wrap">
  @foreach($messages as $m)
    <div class="msg {{ $m->direction === 'out' ? 'out':'in' }}">
      <div>{{ $m->text ?? '[attachment]' }}</div>
      <small>{{ $m->created_at }} · {{ $m->status ?? '' }}</small>
    </div>
  @endforeach

  @if (session('status')) <p>{{ session('status') }}</p> @endif

  <form method="post" action="/{{ $page->id }}/inbox/{{ $customer->id }}/send">
    @csrf
    <input type="text" name="text" placeholder="Nhập tin nhắn..." required>
    <button type="submit">Gửi</button>
  </form>
</div>
</body>
</html>
