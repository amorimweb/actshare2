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

  // Desktop navs: usar apenas hidden/remover-hidden — o md:flex cuida do display
  const navPublicDesktop = document.getElementById('nav-public');
  const navAlunoDesktop  = document.getElementById('nav-aluno');
  // Mobile navs (dentro do menu hamburguer)
  const mobileNavPublic = document.getElementById('mobile-nav-public');
  const mobileNavAluno  = document.getElementById('mobile-nav-aluno');

  if (user) {
    guestEls.forEach(el => { el.classList.remove('flex'); el.classList.add('hidden'); });
    userEls.forEach(el  => { el.classList.remove('hidden'); el.classList.add('flex'); });

    navPublicDesktop?.classList.add('hidden');
    navPublicDesktop?.classList.remove('md:flex');
    navAlunoDesktop?.classList.remove('hidden');
    navAlunoDesktop?.classList.add('md:flex');
    mobileNavPublic?.classList.add('hidden');
    mobileNavAluno?.classList.remove('hidden');

    const firstName = user.nome ? user.nome.trim().split(' ')[0] : 'Aluno';
    const nameEl = document.getElementById('header-username');
    if (nameEl) nameEl.textContent = firstName;

    const initialsEl = document.getElementById('header-avatar-initials');
    if (initialsEl && firstName) {
      initialsEl.textContent = firstName.substring(0, 1).toUpperCase();
    }

    // Renderiza links dinâmicos no dropdown baseados no papel do usuário
    const dropdown = document.getElementById('user-dropdown');
    const mobileAuthUser = document.getElementById('mobile-auth-user');
    
    if (dropdown) {
      const existingPanels = dropdown.querySelectorAll('.panel-link');
      existingPanels.forEach(el => el.remove());
      
      if (user.role === 'admin') {
        const link = document.createElement('a');
        link.className = 'panel-link block px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-gray-50 transition-colors';
        link.href = _B() + '/admin';
        link.textContent = 'Painel Admin';
        dropdown.insertBefore(link, dropdown.firstChild);
      } else if (user.role === 'gestor') {
        const link = document.createElement('a');
        link.className = 'panel-link block px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-gray-50 transition-colors';
        link.href = _B() + '/gestor';
        link.textContent = 'Painel Gestor';
        dropdown.insertBefore(link, dropdown.firstChild);
      }
    }

    if (mobileAuthUser) {
      const existingPanels = mobileAuthUser.querySelectorAll('.panel-link');
      existingPanels.forEach(el => el.remove());
      
      if (user.role === 'admin') {
        const link = document.createElement('a');
        link.className = 'panel-link flex items-center px-5 py-3.5 text-sm font-bold text-secondary hover:bg-blue-50 transition-colors';
        link.href = _B() + '/admin';
        link.textContent = 'Painel Admin';
        const header = mobileAuthUser.querySelector('div');
        if (header) header.insertAdjacentElement('afterend', link);
      } else if (user.role === 'gestor') {
        const link = document.createElement('a');
        link.className = 'panel-link flex items-center px-5 py-3.5 text-sm font-bold text-secondary hover:bg-blue-50 transition-colors';
        link.href = _B() + '/gestor';
        link.textContent = 'Painel Gestor';
        const header = mobileAuthUser.querySelector('div');
        if (header) header.insertAdjacentElement('afterend', link);
      }
    }
  } else {
    guestEls.forEach(el => { el.classList.remove('hidden'); el.classList.add('flex'); });
    userEls.forEach(el  => { el.classList.remove('flex'); el.classList.add('hidden'); });

    navPublicDesktop?.classList.remove('hidden');
    navPublicDesktop?.classList.add('md:flex');
    navAlunoDesktop?.classList.add('hidden');
    navAlunoDesktop?.classList.remove('md:flex');
    mobileNavPublic?.classList.remove('hidden');
    mobileNavAluno?.classList.add('hidden');
  }
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
