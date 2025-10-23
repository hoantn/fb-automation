<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','Admin')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-semibold" href="/admin/dashboard">FB Automation</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbars">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbars">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/admin/pages">Pages</a></li>
        <li class="nav-item"><a class="nav-link" href="/admin/messages">Messages</a></li>
        <li class="nav-item"><a class="nav-link" href="/admin/broadcasts">Broadcasts</a></li>
        <li class="nav-item"><a class="nav-link" href="/admin/settings">Settings</a></li>
      </ul>
      <form class="d-flex" method="get" action="/admin/logout">
        <button class="btn btn-outline-dark">Logout</button>
      </form>
    </div>
  </div>
</nav>

<main class="container my-3">
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/axios@1.6.7/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@yield('scripts')
</body>
</html>
