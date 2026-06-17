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
            primary:   { DEFAULT: '#0C1323', dark: '#060c17' },
            secondary: { DEFAULT: '#10b981' },
          },
          fontFamily: { sans: ['Inter', 'sans-serif'] },
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/app.css?v=4">
</head>
<body class="font-sans bg-white text-gray-800">

<?php
$showCourseNav = !empty($showCourseNav);
$courseNavGroups = [
  'interpretacao-das-normas' => ['label' => 'Interpretação das Normas', 'courses' => []],
  'auditor-interno' => ['label' => 'Auditor Interno', 'courses' => []],
  'auditor-lider' => ['label' => 'Auditor Líder', 'courses' => []],
  'automotivo' => ['label' => 'Automotivo', 'courses' => []],
  'seguranca-da-informacao' => ['label' => 'Segurança da Informação', 'courses' => []],
];

if ($showCourseNav) try {
  require_once __DIR__ . '/../../includes/db.php';
  $db = getDB();
  $stmt = $db->query("
    SELECT c.id, c.titulo, cat.slug AS categoria_slug
    FROM cursos c
    INNER JOIN categorias cat ON cat.id = c.categoria_id
    WHERE c.ativo = 1
      AND c.publico = 1
      AND cat.slug IN ('interpretacao-das-normas', 'auditor-interno', 'auditor-lider', 'automotivo', 'seguranca-da-informacao')
    ORDER BY FIELD(cat.slug, 'interpretacao-das-normas', 'auditor-interno', 'auditor-lider', 'automotivo', 'seguranca-da-informacao'), c.titulo
  ");
  foreach ($stmt->fetchAll() as $course) {
    $slug = $course['categoria_slug'];
    if (isset($courseNavGroups[$slug])) {
      $courseNavGroups[$slug]['courses'][] = $course;
    }
  }
} catch (Throwable $e) {
  // O menu continua navegável mesmo se o banco estiver indisponível.
}
?>

<header id="app-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Logo -->
      <a href="<?= BASE_PATH ?>/" class="flex items-center gap-2">
        <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-11 w-auto">
        <span class="text-primary font-bold text-lg hidden sm:block">ActShare</span>
      </a>

      <!-- Nav desktop (Wrapper para centralizar) -->
      <div class="hidden md:flex flex-1 justify-center items-center">
        <!-- Nav desktop (Público) -->
        <nav class="hidden md:flex items-center gap-6" id="nav-public">
          <a href="<?= BASE_PATH ?>/cursos" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">TREINAMENTOS</a>
          <a href="<?= BASE_PATH ?>/treinamentos/abertos" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">TREINAMENTOS ABERTOS</a>
          <a href="<?= BASE_PATH ?>/treinamentos/gravados" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">GRAVADOS</a>
        </nav>

        <!-- Nav desktop (Aluno Logado) -->
        <nav class="hidden md:flex items-center gap-6" id="nav-aluno">
          <a href="<?= BASE_PATH ?>/painel" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">ÁREA DO ALUNO</a>
          <a href="<?= BASE_PATH ?>/certificado" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">CERTIFICADO</a>
          <a href="https://wa.me/5511999999999" target="_blank" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors flex items-center gap-1">
            FALE CONOSCO
          </a>
        </nav>
      </div>

      <!-- Lado direito: auth desktop + itens mobile fixos + hamburger -->
      <div class="flex items-center gap-3">

        <!-- Carrinho (sempre visível) -->
        <a href="<?= BASE_PATH ?>/carrinho" class="relative text-gray-600 hover:text-primary transition-colors" aria-label="Carrinho">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span id="cart-count-badge" class="absolute -top-1 -right-1 bg-secondary text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">0</span>
        </a>

        <!-- Botão Entrar — mobile: botão com borda | desktop: link texto -->
        <div id="auth-guest" class="hidden items-center">
          <a href="<?= BASE_PATH ?>/login"
             class="md:hidden inline-flex items-center border border-primary text-primary text-sm font-semibold px-4 py-1.5 rounded-lg hover:bg-primary hover:text-white transition-colors">
            Entrar
          </a>
          <a href="<?= BASE_PATH ?>/login"
             class="hidden md:inline-block text-sm font-medium text-gray-600 hover:text-primary transition-colors">
            Entrar
          </a>
        </div>

        <!-- Avatar (logado) — sempre visível -->
        <div id="auth-user" class="hidden items-center gap-2 relative">
          <div class="relative">
            <button onclick="toggleUserDropdown(event)" class="flex items-center gap-2 focus:outline-none group">
              <div id="header-avatar-initials" class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shadow-sm group-hover:bg-primary-dark transition-colors uppercase">
                A
              </div>
              <span id="header-username" class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors hidden sm:inline-block"></span>
              <svg class="w-4 h-4 text-gray-500 group-hover:text-primary hidden md:block transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <!-- Dropdown desktop -->
            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50">
              <a href="<?= BASE_PATH ?>/meus-dados" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Meus Dados</a>
              <a href="<?= BASE_PATH ?>/alterar-senha" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Alterar Senha</a>
              <a href="https://wa.me/5511999999999" target="_blank" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Ajuda / Suporte</a>
              <hr class="border-gray-100 my-1">
              <button onclick="authLogout()" class="w-full text-left block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium">Sair</button>
            </div>
          </div>
        </div>

        <!-- Hamburger (só mobile) -->
        <button id="mobile-menu-btn" class="md:hidden p-2 -mr-1 text-gray-600 hover:text-primary transition-colors" onclick="toggleMobileMenu()" aria-label="Menu">
          <svg id="hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

      </div>
    </div>
  </div>

  <?php if ($showCourseNav): ?>
  <div class="border-t border-gray-100 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="course-strip flex items-center justify-between gap-4 py-3 overflow-x-auto scrollbar-hide" style="scrollbar-width:none;-ms-overflow-style:none;">
        
        <!-- Menu Categorias -->
        <div class="flex items-center gap-2">
          <a href="<?= BASE_PATH ?>/cursos" class="course-pill shrink-0 rounded-md bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors uppercase">
            Todos os Cursos
          </a>

          <?php foreach ($courseNavGroups as $slug => $group): ?>
            <div class="relative group">
              <a href="<?= BASE_PATH ?>/cursos?categoria=<?= htmlspecialchars($slug) ?>" class="course-pill shrink-0 inline-flex items-center gap-1 uppercase rounded-md bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">
                <?php
                  // Nomes adaptados de acordo com o pedido
                  $label = $group['label'];
                  if ($slug === 'interpretacao-das-normas') $label = 'Interpretação Normas';
                  echo htmlspecialchars($label);
                ?>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </a>
              <?php if (!empty($group['courses'])): ?>
                <div class="invisible opacity-0 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 absolute left-0 top-full z-50 mt-2 w-72 rounded-lg border border-gray-100 bg-white shadow-xl transition-all">
                  <div class="p-2">
                    <?php foreach ($group['courses'] as $course): ?>
                      <a href="<?= BASE_PATH ?>/cursos/<?= (int) $course['id'] ?>" class="block rounded-md px-3 py-2 uppercase text-xs font-medium leading-snug text-gray-600 hover:bg-blue-50 hover:text-primary transition-colors">
                        <?= htmlspecialchars($course['titulo']) ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

          <!-- Mais -->
          <div class="relative group">
            <button class="course-pill shrink-0 inline-flex items-center gap-1 uppercase rounded-md bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">
              Mais
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="invisible opacity-0 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 absolute left-0 top-full z-50 mt-2 w-48 rounded-lg border border-gray-100 bg-white shadow-xl transition-all">
              <div class="p-2">
                <a href="<?= BASE_PATH ?>/cursos?categoria=compliance" class="block rounded-md px-3 py-2 uppercase text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-primary transition-colors">Compliance</a>
                <a href="<?= BASE_PATH ?>/cursos?categoria=tecnologia" class="block rounded-md px-3 py-2 uppercase text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-primary transition-colors">Tecnologia</a>
                <a href="<?= BASE_PATH ?>/cursos?categoria=soft-skills" class="block rounded-md px-3 py-2 uppercase text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-primary transition-colors">Soft Skills</a>
                <a href="<?= BASE_PATH ?>/cursos?categoria=juridico" class="block rounded-md px-3 py-2 uppercase text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-primary transition-colors">Jurídico</a>
                <a href="<?= BASE_PATH ?>/cursos?categoria=gestao" class="block rounded-md px-3 py-2 uppercase text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-primary transition-colors">Gestão</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Barra de Busca à Direita -->
        <div class="flex items-center bg-gray-100 rounded-lg px-2 py-1.5 border border-gray-200 shrink-0">
          <input type="text" id="course-search-input" onkeyup="handleHeaderSearch(event)" placeholder="Busca..." class="bg-transparent text-xs w-28 md:w-36 focus:outline-none placeholder-gray-400">
          <svg class="w-4 h-4 text-gray-400 cursor-pointer hover:text-primary transition-colors" onclick="triggerHeaderSearch()" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

      </div>
    </div>
  </div>

  <script>
  function handleHeaderSearch(e) {
    if (e.key === 'Enter') {
      triggerHeaderSearch();
    }
  }
  function triggerHeaderSearch() {
    const query = document.getElementById('course-search-input').value.trim();
    if (window.location.pathname.includes('/cursos')) {
      if (typeof renderizarCursos === 'function') {
        renderizarCursos(new URLSearchParams(window.location.search).get('categoria'));
      }
    } else {
      window.location.href = BASE + '/cursos?busca=' + encodeURIComponent(query);
    }
  }
  </script>
  <?php endif; ?>

  <!-- Mobile menu -->
  <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white shadow-lg divide-y divide-gray-100">

    <!-- Seção 1: Navegação principal (público) -->
    <div id="mobile-nav-public">
      <div class="px-5 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Menu</div>
      <a href="<?= BASE_PATH ?>/cursos"              class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">TREINAMENTOS</a>
      <a href="<?= BASE_PATH ?>/treinamentos/abertos" class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">TREINAMENTOS ABERTOS</a>
      <a href="<?= BASE_PATH ?>/treinamentos/gravados" class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">TREINAMENTOS GRAVADOS</a>
    </div>

    <!-- Seção 1: Navegação principal (aluno logado) -->
    <div id="mobile-nav-aluno" class="hidden">
      <div class="px-5 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Menu</div>
      <a href="<?= BASE_PATH ?>/painel"      class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">ÁREA DO ALUNO</a>
      <a href="<?= BASE_PATH ?>/certificado" class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">CERTIFICADO</a>
      <a href="https://wa.me/5511999999999" target="_blank" class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">FALE CONOSCO</a>
    </div>

    <!-- Seção 2: Categorias de cursos -->
    <div>
      <div class="px-5  pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">TREINAMENTOS</div>
      <a href="<?= BASE_PATH ?>/cursos" class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">TODOS OS TREINAMENTOS</a>
      <?php foreach ($courseNavGroups as $slug => $group): ?>
        <a href="<?= BASE_PATH ?>/cursos?categoria=<?= htmlspecialchars($slug) ?>"
           class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary uppercase transition-colors">
          <?= htmlspecialchars($group['label']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Seção conta (logado) — aparece após as categorias quando logado -->
    <div id="mobile-auth-user" class="hidden">
      <div class="px-5 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Minha Conta</div>
      <a href="<?= BASE_PATH ?>/meus-dados"    class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">Meus Dados</a>
      <a href="<?= BASE_PATH ?>/alterar-senha" class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">Alterar Senha</a>
      <a href="https://wa.me/5511999999999" target="_blank" class="flex items-center px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">Ajuda / Suporte</a>
      <button onclick="authLogout()" class="w-full flex items-center px-5 py-3.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">Sair</button>
    </div>

    <!-- Placeholder para não logado (sem seção conta no menu) -->
    <div id="mobile-auth-guest" class="hidden"></div>

  </div>
</header>

<script>
function toggleUserDropdown(event) {
  event.stopPropagation();
  document.getElementById('user-dropdown')?.classList.toggle('hidden');
}

function toggleMobileMenu() {
  const menu   = document.getElementById('mobile-menu');
  const hIcon  = document.getElementById('hamburger-icon');
  const xIcon  = document.getElementById('close-icon');
  const isOpen = !menu.classList.contains('hidden');
  menu.classList.toggle('hidden', isOpen);
  hIcon?.classList.toggle('hidden', !isOpen);
  xIcon?.classList.toggle('hidden', isOpen);
}

document.addEventListener('click', (e) => {
  // Fecha dropdown de usuário ao clicar fora
  const dropdown = document.getElementById('user-dropdown');
  if (dropdown && !dropdown.classList.contains('hidden')) {
    dropdown.classList.add('hidden');
  }
  // Fecha menu mobile ao clicar fora do header
  const header = document.getElementById('app-header');
  if (header && !header.contains(e.target)) {
    const menu  = document.getElementById('mobile-menu');
    const hIcon = document.getElementById('hamburger-icon');
    const xIcon = document.getElementById('close-icon');
    if (menu && !menu.classList.contains('hidden')) {
      menu.classList.add('hidden');
      hIcon?.classList.remove('hidden');
      xIcon?.classList.add('hidden');
    }
  }
});
</script>

<div class="<?= $showCourseNav ? 'app-main-shell' : 'app-main-shell-simple' ?>">
<script src="<?= BASE_PATH ?>/assets/js/auth.js?v=2"></script>
<script src="<?= BASE_PATH ?>/assets/js/api.js?v=2"></script>
