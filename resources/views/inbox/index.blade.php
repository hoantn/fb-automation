<!doctype html>
<html>
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Inbox - {{ $page->name }}</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial;margin:0}
.head{padding:16px 20px;border-bottom:1px solid #eee;display:flex;justify-content:space-between}
.list{max-width:900px;margin:20px auto}
.item{border:1px solid #e5e5e5;border-radius:10px;padding:12px 16px;margin:10px 0;display:flex;justify-content:space-between;align-items:center}
a.btn{padding:8px 12px;border-radius:8px;border:1px solid #111;background:#111;color:#fff;text-decoration:none}
</style>
</head>
<body>
<div class="head">
  <div><b>Inbox ·</b> {{ $page->name }}</div>
  <div><a class="btn" href="/">Home</a></div>
</div>
<div class="list">
  @forelse($convs as $c)
    <div class="item">
      <div>
        <div><b>{{ $c->customer->name ?? ('PSID '.$c->customer->psid) }}</b></div>
        <small>Lần cuối: {{ $c->last_message_at ?? '—' }}</small>
      </div>
      <div>
        <a class="btn" href="/{{ $page->id }}/inbox/{{ $c->customer_id }}">Xem</a>
      </div>
    </div>
  @empty
    <p>Chưa có hội thoại.</p>
  @endforelse

  {{ $convs->links() }}
</div>
</body>
</html>
