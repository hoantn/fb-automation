/* public/assets/app.js
 * Lightweight 5s polling hook for dashboard numbers (optional).
 * If backend exposes GET /admin/dashboard/metrics -> {pages, customers, messages_today, last_broadcast}
 * this script will auto-update elements with IDs: m-pages, m-customers, m-msg, m-bc.
 * If the endpoint doesn't exist it fails silently.
 */
(function () {
  const el = (id) => document.getElementById(id);
  async function tick () {
    try {
      const res = await fetch('/admin/dashboard/metrics', { headers: { 'X-Requested-With':'XMLHttpRequest' } });
      if (!res.ok) return;
      const m = await res.json();
      if (el('m-pages')) el('m-pages').textContent = m.pages ?? '—';
      if (el('m-customers')) el('m-customers').textContent = m.customers ?? '—';
      if (el('m-msg')) el('m-msg').textContent = m.messages_today ?? '—';
      if (el('m-bc')) el('m-bc').textContent = m.last_broadcast ?? '—';
    } catch (e) {
      // ignore to keep UI smooth
    }
  }
  setInterval(tick, 5000);
  // first paint
  setTimeout(tick, 1200);
})();