/* ============================================
   LUMINOSITY NOODLES — Main JS
   ============================================ */

// ── Navbar scroll effect ──
window.addEventListener('scroll', () => {
  document.getElementById('navbar')?.classList.toggle('scrolled', window.scrollY > 30);
});

// ── Order Modal State ──
let currentMenu = null;
let currentQty  = 1;
let selectedSpicy   = 'none';
let selectedToppings = [];

function openOrderModal(menu) {
  currentMenu     = menu;
  currentQty      = 1;
  selectedSpicy   = 'none';
  selectedToppings = [];

  document.getElementById('modal-menu-name').textContent = menu.name;
  document.getElementById('modal-qty').textContent = 1;

  // Show/hide groups
  document.getElementById('spicy-group').style.display   = menu.has_spicy   ? '' : 'none';
  document.getElementById('topping-group').style.display = menu.has_topping ? '' : 'none';

  // Reset spicy buttons
  document.querySelectorAll('.spicy-btn').forEach(b => {
    b.classList.toggle('active', b.dataset.spicy === 'none');
  });

  // Reset checkboxes
  document.querySelectorAll('.topping-item input[type="checkbox"]').forEach(cb => cb.checked = false);
  document.getElementById('modal-notes').value = '';

  updateModalTotal();
  document.getElementById('order-modal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('order-modal').style.display = 'none';
  document.body.style.overflow = '';
}

function changeQty(delta) {
  currentQty = Math.max(1, currentQty + delta);
  document.getElementById('modal-qty').textContent = currentQty;
  updateModalTotal();
}

function updateModalTotal() {
  if (!currentMenu) return;
  let total = currentMenu.price;
  selectedToppings.forEach(tid => {
    const t = TOPPINGS_DATA?.find(x => x.id == tid);
    if (t) total += parseFloat(t.extra_price);
  });
  total *= currentQty;
  document.getElementById('modal-total-price').textContent = 'Rp ' + total.toLocaleString('id-ID');
  document.getElementById('modal-menu-price').textContent  = 'Rp ' + (currentMenu.price).toLocaleString('id-ID');
}

// Spicy buttons
document.addEventListener('click', e => {
  if (e.target.classList.contains('spicy-btn')) {
    document.querySelectorAll('.spicy-btn').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    selectedSpicy = e.target.dataset.spicy;
  }
  if (e.target.closest('.topping-item input')) {
    const cb = e.target.closest('.topping-item')?.querySelector('input');
    if (!cb) return;
    const id = parseInt(cb.value);
    if (cb.checked) {
      if (!selectedToppings.includes(id)) selectedToppings.push(id);
    } else {
      selectedToppings = selectedToppings.filter(x => x !== id);
    }
    updateModalTotal();
  }
});

// Also listen for checkbox changes
document.addEventListener('change', e => {
  if (e.target.matches('.topping-item input[type="checkbox"]')) {
    const id = parseInt(e.target.value);
    if (e.target.checked) {
      if (!selectedToppings.includes(id)) selectedToppings.push(id);
    } else {
      selectedToppings = selectedToppings.filter(x => x !== id);
    }
    updateModalTotal();
  }
});

// Close modal on overlay click
document.getElementById('order-modal')?.addEventListener('click', e => {
  if (e.target === e.currentTarget) closeModal();
});

// ── Quick Add (from home page) ──
function quickAddCart(menuId, menuName) {
  fetch(BASE_URL + '/api/cart', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ menu_id: menuId, qty: 1, csrf: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      updateCartBadge(data.count);
      showToast(`${menuName} ditambahkan ke cart! 🛒`);
    } else {
      showToast('Gagal menambahkan ke cart', 'error');
    }
  });
}

// ── Submit to cart from modal ──
function submitToCart() {
  if (!currentMenu) return;
  const notes = document.getElementById('modal-notes')?.value || '';

  fetch(BASE_URL + '/api/cart', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      csrf:     CSRF_TOKEN,
      menu_id:  currentMenu.id,
      qty:      currentQty,
      spicy:    selectedSpicy,
      toppings: selectedToppings,
      notes:    notes
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      closeModal();
      updateCartBadge(data.count);
      showToast(`${currentMenu.name} ditambahkan! 🛒`);
    } else {
      showToast(data.error || 'Gagal menambahkan', 'error');
    }
  });
}

// ── Remove from cart ──
function removeFromCart(key) {
  fetch(BASE_URL + '/api/cart', {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ key, csrf: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(() => location.reload());
}

// ── Apply promo (cart page) ──
function applyPromo() {
  const code = document.getElementById('promo-input')?.value.trim().toUpperCase();
  if (!code) return;
  fetch(BASE_URL + '/api/promo', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ code, csrf: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.error) {
      showToast('❌ ' + data.error, 'error');
    } else {
      showToast(`✅ Promo ${data.promo.discount_percent}% berhasil diterapkan!`);
    }
  });
}

// ── Cart Badge ──
function updateCartBadge(count) {
  const badge = document.getElementById('cart-count');
  if (badge) {
    badge.textContent = count;
    badge.style.transform = 'scale(1.3)';
    setTimeout(() => badge.style.transform = '', 200);
  }
}

// ── Toast Notification ──
function showToast(msg, type = 'success') {
  let toast = document.getElementById('lum-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'lum-toast';
    toast.style.cssText = `
      position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999;
      background:var(--bg-card2); border:1px solid var(--border);
      padding:.85rem 1.25rem; border-radius:12px;
      font-size:.9rem; font-family:var(--font-body);
      color:var(--text-primary);
      box-shadow: 0 0 20px rgba(168,85,247,0.2);
      transform:translateY(20px); opacity:0;
      transition: all .3s ease;
      max-width: 300px;
    `;
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.style.borderColor = type === 'error' ? 'rgba(239,68,68,0.5)' : 'var(--accent-purple)';
  toast.style.opacity = '1';
  toast.style.transform = 'translateY(0)';
  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(20px)';
  }, 3000);
}
