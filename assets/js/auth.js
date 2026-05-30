// Gerenciamento de autenticação (substitui stores/auth.ts)
const AUTH_KEY = 'act_user';
var _B = _B || (() => (typeof BASE !== 'undefined' ? BASE : ''));

function authGetUser() {
  try { return JSON.parse(localStorage.getItem(AUTH_KEY)); } catch { return null; }
}

function authSetUser(user) {
  if (user) localStorage.setItem(AUTH_KEY, JSON.stringify(user));
  else localStorage.removeItem(AUTH_KEY);
}

async function authLogout() {
  try { await fetch(_B() + '/api/auth/logout', { method: 'POST', credentials: 'include' }); } catch {}
  authSetUser(null);
  window.location.href = _B() + '/login';
}

// Hidrata o header com estado de autenticação
async function authHydrate() {
  let user = authGetUser();

  // Valida token contra o servidor
  try {
    const res = await fetch(_B() + '/api/auth/me', { credentials: 'include' });
    const data = await res.json();
    if (data.user) { authSetUser(data.user); user = data.user; }
    else { authSetUser(null); user = null; }
  } catch {}

  updateHeaderAuth(user);
}

function updateHeaderAuth(user) {
  const guestEls = document.querySelectorAll('#auth-guest, #mobile-auth-guest');
  const userEls  = document.querySelectorAll('#auth-user, #mobile-auth-user');
  const navPublicEls = document.querySelectorAll('#nav-public, #mobile-nav-public');
  const navAlunoEls  = document.querySelectorAll('#nav-aluno, #mobile-nav-aluno');

  if (user) {
    guestEls.forEach(el => { el.classList.remove('flex'); el.classList.add('hidden'); });
    userEls.forEach(el  => { el.classList.remove('hidden'); el.classList.add('flex'); });
    navPublicEls.forEach(el => { el.classList.remove('flex'); el.classList.add('hidden'); });
    navAlunoEls.forEach(el => { el.classList.remove('hidden'); el.classList.add('flex'); });

    const firstName = user.nome ? user.nome.trim().split(' ')[0] : 'Aluno';
    const nameEl = document.getElementById('header-username');
    if (nameEl) nameEl.textContent = firstName;

    const initialsEl = document.getElementById('header-avatar-initials');
    if (initialsEl && firstName) {
      initialsEl.textContent = firstName.substring(0, 1).toUpperCase();
    }
  } else {
    guestEls.forEach(el => { el.classList.remove('hidden'); el.classList.add('flex'); });
    userEls.forEach(el  => { el.classList.remove('flex'); el.classList.add('hidden'); });
    navPublicEls.forEach(el => { el.classList.remove('hidden'); el.classList.add('flex'); });
    navAlunoEls.forEach(el => { el.classList.remove('flex'); el.classList.add('hidden'); });
  }
}

function toggleMobileMenu() {
  document.getElementById('mobile-menu')?.classList.toggle('hidden');
}

function updateCartCountBadge() {
  const badge = document.getElementById('cart-count-badge');
  if (!badge) return;
  try {
    const cart = JSON.parse(localStorage.getItem('act_carrinho')) || [];
    badge.textContent = cart.length;
  } catch {
    badge.textContent = '0';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  authHydrate();
  updateCartCountBadge();
});
