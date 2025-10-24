{{-- resources/views/components/app-layout.blade.php --}}
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'FB Automation' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body{font-family:Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial}
    .card{background:#fff;border-radius:12px;box-shadow:0 1px 2px rgba(0,0,0,.05);border:1px solid rgba(2,6,23,.06)}
    .btn{display:inline-flex;align-items:center;border-radius:10px;padding:.5rem .75rem;font-size:.875rem;font-weight:600;background:#0f172a;color:#fff}
    .btn:hover{background:#000}
    .btn-secondary{background:#fff;color:#0f172a;border:1px solid #e5e7eb}
    .btn-secondary:hover{background:#f8fafc}
  </style>
</head>
<body class="bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 py-8">
    {{ $slot }}
  </div>
</body>
</html>
