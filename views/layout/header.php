<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'ActShare') ?></title>
  <link rel="icon" href="<?= BASE_PATH ?>/assets/img/logo-act2.png">
  <script>const BASE = '<?= BASE_PATH ?>';</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary:   { DEFAULT: '#00007F', dark: '#000060' },
            secondary: { DEFAULT: '#10b981' },
          },
          fontFamily: { sans: ['Inter', 'sans-serif'] },
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/app.css">
</head>
<body class="font-sans bg-white text-gray-800">

<header id="app-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Logo -->
      <a href="https://actshare.com.br" target="_blank" class="flex items-center gap-2">
        <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-8 w-auto">
        <span class="text-primary font-bold text-lg hidden sm:block">ActShare</span>
      </a>

      <!-- Nav desktop (Público) -->
      <nav class="hidden md:flex items-center gap-6" id="nav-public">
        <a href="<?= BASE_PATH ?>/cursos" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Cursos</a>
        <a href="<?= BASE_PATH ?>/treinamentos/abertos" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Treinamentos Abertos</a>
        <a href="<?= BASE_PATH ?>/treinamentos/gravados" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Gravados</a>
      </nav>

      <!-- Nav desktop (Aluno Logado) -->
      <nav class="hidden md:flex items-center gap-6" id="nav-aluno">
        <a href="<?= BASE_PATH ?>/painel" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Área do Aluno</a>
        <a href="<?= BASE_PATH ?>/certificado" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Certificado</a>
        <a href="https://wa.me/5511999999999" target="_blank" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors flex items-center gap-1">
          Fale Conosco
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
      </nav>

      <!-- Auth area / Carrinho / Avatar -->
      <div class="flex items-center gap-4" id="header-auth">
        <!-- Carrinho -->
        <a href="<?= BASE_PATH ?>/carrinho" class="text-gray-600 hover:text-primary transition-colors relative" aria-label="Carrinho">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span id="cart-count-badge" class="absolute -top-1 -right-1 bg-secondary text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">0</span>
        </a>

        <!-- Guest -->
        <div id="auth-guest" class="hidden items-center gap-3">
          <a href="<?= BASE_PATH ?>/login" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Entrar</a>
          <a href="<?= BASE_PATH ?>/registro" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">Cadastrar</a>
        </div>

        <!-- Logged-in User -->
        <div id="auth-user" class="hidden items-center gap-3 relative">
          <div class="relative">
            <button onclick="toggleUserDropdown(event)" class="flex items-center gap-2 focus:outline-none group">
              <div id="header-avatar-initials" class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shadow-sm group-hover:bg-primary-dark transition-colors uppercase">
                A
              </div>
              <span id="header-username" class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors hidden sm:inline-block"></span>
              <svg class="w-4 h-4 text-gray-500 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50">
              <a href="<?= BASE_PATH ?>/meus-dados" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Meus Dados</a>
              <a href="<?= BASE_PATH ?>/alterar-senha" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Alterar Senha</a>
              <a href="https://wa.me/5511999999999" target="_blank" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Ajuda / Suporte</a>
              <hr class="border-gray-100 my-1">
              <button onclick="authLogout()" class="w-full text-left block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium">Sair</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile menu btn -->
      <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-600" onclick="toggleMobileMenu()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>
  </div>

  <!-- Mobile menu -->
  <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-2">
    <!-- Public mobile links -->
    <div id="mobile-nav-public" class="space-y-1">
      <a href="<?= BASE_PATH ?>/cursos" class="block text-sm text-gray-700 py-2">Cursos</a>
      <a href="<?= BASE_PATH ?>/treinamentos/abertos" class="block text-sm text-gray-700 py-2">Treinamentos Abertos</a>
      <a href="<?= BASE_PATH ?>/treinamentos/gravados" class="block text-sm text-gray-700 py-2">Gravados</a>
    </div>

    <!-- Logged mobile links -->
    <div id="mobile-nav-aluno" class="space-y-1">
      <a href="<?= BASE_PATH ?>/painel" class="block text-sm text-gray-700 py-2">Área do Aluno</a>
      <a href="<?= BASE_PATH ?>/certificado" class="block text-sm text-gray-700 py-2">Certificado</a>
      <a href="https://wa.me/5511999999999" target="_blank" class="block text-sm text-gray-700 py-2">Fale Conosco</a>
    </div>

    <!-- Guest Actions -->
    <div id="mobile-auth-guest" class="hidden pt-2 border-t border-gray-100 space-y-2">
      <a href="<?= BASE_PATH ?>/login" class="block text-sm text-gray-700 py-2">Entrar</a>
      <a href="<?= BASE_PATH ?>/registro" class="block bg-primary text-white text-sm text-center py-2 rounded-lg">Cadastrar</a>
    </div>

    <!-- Logged User Actions -->
    <div id="mobile-auth-user" class="hidden pt-2 border-t border-gray-100 flex-col space-y-1">
      <div class="px-2 py-1 text-xs font-semibold text-gray-400 uppercase">Minha Conta</div>
      <a href="<?= BASE_PATH ?>/meus-dados" class="block text-sm text-gray-700 py-1.5 px-2 hover:bg-gray-50 rounded">Meus Dados</a>
      <a href="<?= BASE_PATH ?>/alterar-senha" class="block text-sm text-gray-700 py-1.5 px-2 hover:bg-gray-50 rounded">Alterar Senha</a>
      <a href="https://wa.me/5511999999999" target="_blank" class="block text-sm text-gray-700 py-1.5 px-2 hover:bg-gray-50 rounded">Ajuda / Suporte</a>
      <button onclick="authLogout()" class="block text-sm text-red-600 py-1.5 px-2 hover:bg-red-50 rounded w-full text-left font-medium">Sair</button>
    </div>
  </div>
</header>

<script>
function toggleUserDropdown(event) {
  event.stopPropagation();
  const dropdown = document.getElementById('user-dropdown');
  dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', () => {
  const dropdown = document.getElementById('user-dropdown');
  if (dropdown && !dropdown.classList.contains('hidden')) {
    dropdown.classList.add('hidden');
  }
});
</script>

<div class="pt-16">
<script src="<?= BASE_PATH ?>/assets/js/auth.js"></script>
<script src="<?= BASE_PATH ?>/assets/js/api.js"></script>
