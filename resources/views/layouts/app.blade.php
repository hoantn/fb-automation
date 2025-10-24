<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'FB Automation')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>html,body{font-family:Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial}</style>
</head>
<body class="bg-slate-50 text-slate-800">
  <header class="border-b bg-white">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
      <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white font-semibold">FB</span>
        <span class="text-lg font-semibold">Automation</span>
      </a>
      <nav class="flex items-center gap-3">
        <a href="{{ url('/') }}" class="text-sm hover:text-indigo-600">Home</a>
        <a href="{{ url('/pages/connect') }}" class="text-sm hover:text-indigo-600">Connect Page</a>
        @auth
        <form method="POST" action="{{ url('/logout') }}" class="inline">@csrf<button class="text-sm text-slate-600 hover:text-red-600">Logout ({{ auth()->user()->name }})</button></form>
        @endauth
      </nav>
    </div>
  </header>
  <main class="container mx-auto px-4 py-6">@include('partials.flash')@yield('content')</main>
  <footer class="mt-12 border-t bg-white"><div class="container mx-auto px-4 py-6 text-xs text-slate-500">© {{ date('Y') }} FB Automation · Dev preview UI</div></footer>
</body>
</html>
