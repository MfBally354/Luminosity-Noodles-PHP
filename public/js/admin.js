/* ============================================
   LUMINOSITY NOODLES — Admin JS
   ============================================ */

// ── Update Order Status ──
function updateStatus(selectEl) {
  const id     = selectEl.dataset.id;
  const status = selectEl.value;

  fetch(BASE_URL + '/api/orders/' + id, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ status, csrf: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showAdminToast(`Order #${id} → ${status}`, 'success');
      // Update badge color in same row
      const row = selectEl.closest('tr');
      const badge = row?.querySelector('.status-badge');
      if (badge) {
        const colors = {
          pending:    '#f59e0b',
          processing: '#3b82f6',
          cooking:    '#f97316',
          delivering: '#a78bfa',
          done:       '#22c55e',
          cancelled:  '#ef4444'
        };
        const labels = {
          pending: 'Menunggu', processing: 'Diproses', cooking: 'Dimasak',
          delivering: 'Diantar', done: 'Selesai', cancelled: 'Batal'
        };
        badge.style.color       = colors[status] || '#aaa';
        badge.style.borderColor = colors[status] || '#aaa';
        badge.textContent       = labels[status] || status;
      }
    } else {
      showAdminToast('Gagal update status', 'error');
    }
  })
  .catch(() => showAdminToast('Network error', 'error'));
}

// ── Live Order Polling (dashboard) ──
let lastOrderCount = document.querySelectorAll('#orders-tbody tr').length;

function pollNewOrders() {
  fetch(BASE_URL + '/api/poll-orders')
    .then(r => r.json())
    .then(data => {
      if (!data.data) return;
      const newCount = data.data.length;
      if (newCount > lastOrderCount) {
        showAdminToast(`🔔 ${newCount - lastOrderCount} pesanan baru masuk!`, 'info');
        lastOrderCount = newCount;
        // Re-render tbody if on dashboard
        renderOrdersTable(data.data);
      }
    })
    .catch(() => {});
}

function renderOrdersTable(orders) {
  const tbody = document.getElementById('orders-tbody');
  if (!tbody) return;

  const colors = { pending:'#f59e0b', processing:'#3b82f6', cooking:'#f97316', delivering:'#a78bfa', done:'#22c55e', cancelled:'#ef4444' };
  const labels = { pending:'Menunggu', processing:'Diproses', cooking:'Dimasak', delivering:'Diantar', done:'Selesai', cancelled:'Batal' };
  const statuses = ['pending','processing','cooking','delivering','done','cancelled'];

  tbody.innerHTML = orders.map(o => {
    const c = colors[o.status] || '#aaa';
    const l = labels[o.status] || o.status;
    const opts = statuses.map(s => `<option value="${s}" ${o.status===s?'selected':''}>${s.charAt(0).toUpperCase()+s.slice(1)}</option>`).join('');
    const date = new Date(o.created_at).toLocaleString('id-ID', {day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'});
    const total = 'Rp ' + parseFloat(o.total).toLocaleString('id-ID');
    return `<tr>
      <td>#${o.id}</td>
      <td>${escHtml(o.user_name)}</td>
      <td>${total}</td>
      <td><span class="status-badge" style="color:${c};border-color:${c}">${l}</span></td>
      <td>${date}</td>
      <td><select class="status-select" data-id="${o.id}" onchange="updateStatus(this)">${opts}</select></td>
    </tr>`;
  }).join('');
}

// Poll every 15 seconds on dashboard
if (document.getElementById('orders-tbody')) {
  setInterval(pollNewOrders, 15000);
}

// ── Admin Toast ──
function showAdminToast(msg, type = 'success') {
  const typeStyles = {
    success: { border: 'rgba(34,197,94,0.5)',  icon: '✅' },
    error:   { border: 'rgba(239,68,68,0.5)',   icon: '❌' },
    info:    { border: 'rgba(168,85,247,0.5)',  icon: '🔔' },
  };
  const s = typeStyles[type] || typeStyles.success;

  let toast = document.getElementById('admin-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'admin-toast';
    toast.style.cssText = `
      position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999;
      background:var(--bg-card2); border:1px solid;
      padding:.85rem 1.25rem; border-radius:12px;
      font-size:.9rem; font-family:var(--font-body);
      color:var(--text-primary);
      box-shadow:0 0 20px rgba(0,0,0,0.4);
      transform:translateY(20px); opacity:0;
      transition:all .3s ease;
      max-width:300px;
      display:flex; align-items:center; gap:.6rem;
    `;
    document.body.appendChild(toast);
  }

  toast.style.borderColor = s.border;
  toast.innerHTML = `<span>${s.icon}</span><span>${escHtml(msg)}</span>`;
  toast.style.opacity = '1';
  toast.style.transform = 'translateY(0)';

  clearTimeout(toast._t);
  toast._t = setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(20px)';
  }, 3500);
}

// ── Delete confirm with custom modal ──
function confirmDelete(url, name) {
  if (confirm(`Hapus "${name}"? Tindakan ini tidak bisa dibatalkan.`)) {
    window.location.href = url;
  }
}

// ── Chart.js stats refresh ──
function refreshStats() {
  fetch(BASE_URL + '/api/stats')
    .then(r => r.json())
    .then(data => {
      // Update stat cards
      const todayCount   = document.getElementById('stat-today-count');
      const todayRevenue = document.getElementById('stat-today-revenue');
      if (todayCount)   todayCount.textContent   = data.today?.count ?? 0;
      if (todayRevenue) todayRevenue.textContent = 'Rp ' + parseFloat(data.today?.revenue ?? 0).toLocaleString('id-ID');
    })
    .catch(() => {});
}

// Refresh stats every 30 seconds
if (document.querySelector('.stats-grid')) {
  setInterval(refreshStats, 30000);
}

// ── HTML Escape helper ──
function escHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(String(str)));
  return d.innerHTML;
}

// ── Image preview on menu form ──
const imageInput = document.querySelector('input[name="image"]');
if (imageInput) {
  imageInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      let preview = document.getElementById('img-preview');
      if (!preview) {
        preview = document.createElement('img');
        preview.id = 'img-preview';
        preview.style.cssText = 'height:80px;border-radius:8px;margin-top:.5rem;display:block';
        this.parentNode.insertBefore(preview, this.nextSibling);
      }
      preview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });
}

// ── Sidebar toggle (mobile) ──
const sidebarToggle = document.getElementById('sidebar-toggle');
if (sidebarToggle) {
  sidebarToggle.addEventListener('click', () => {
    document.querySelector('.sidebar')?.classList.toggle('open');
  });
}
