@extends('admin.layouts.master')
@section('title','Dashboard')
@section('content')
<div class="row g-3">
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body">
    <div class="text-muted small">Pages</div><div class="fs-3 fw-bold" id="stat-pages">0</div>
  </div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body">
    <div class="text-muted small">Customers</div><div class="fs-3 fw-bold" id="stat-customers">0</div>
  </div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body">
    <div class="text-muted small">Messages today</div><div class="fs-3 fw-bold" id="stat-messages-today">0</div>
  </div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body">
    <div class="text-muted small">Last broadcast</div><div id="stat-last-broadcast" class="small text-muted">—</div>
  </div></div></div>
</div>

<div class="card mt-3 shadow-sm"><div class="card-body">
  <h6 class="mb-3">Messages per hour (today)</h6>
  <canvas id="chart-hourly" height="80"></canvas>
</div></div>

<div class="card mt-3 shadow-sm"><div class="card-body">
  <h6 class="mb-3">Latest conversations</h6>
  <div class="table-responsive">
    <table class="table table-sm">
      <thead><tr><th>ID</th><th>Page</th><th>Customer</th><th>Last message</th></tr></thead>
      <tbody id="tbl-latest"></tbody>
    </table>
  </div>
</div></div>
@endsection

@section('scripts')
<script>
const REFRESH = {{ config('system.dashboard_refresh_interval',5) }} * 1000;
let chart;
function renderChart(data) {
  const ctx = document.getElementById('chart-hourly');
  if (chart) chart.destroy();
  chart = new Chart(ctx, {
    type: 'line',
    data: { labels: [...Array(24).keys()].map(h => h+':00'), datasets: [{ label:'Messages', data }] },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
}
async function fetchStats() {
  const res = await axios.get('/admin/api/stats');
  const d = res.data;
  document.getElementById('stat-pages').innerText = d.pages;
  document.getElementById('stat-customers').innerText = d.customers;
  document.getElementById('stat-messages-today').innerText = d.messages_today;
  document.getElementById('stat-last-broadcast').innerText = d.last_broadcast ? (`#${d.last_broadcast.id} • ${d.last_broadcast.status} • ${d.last_broadcast.created_at}`) : '—';
  renderChart(d.hourly);
  const tbody = document.getElementById('tbl-latest');
  tbody.innerHTML = d.latest_convs.map(x => `<tr><td>${x.id}</td><td>${x.page_id}</td><td>${x.customer ?? ''}</td><td>${x.last_message_at ?? ''}</td></tr>`).join('');
}
fetchStats();
setInterval(fetchStats, REFRESH);
</script>
@endsection
