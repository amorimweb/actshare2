<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Admin — ActShare') ?></title>
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
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/app.css">
  <style>
    .sidebar-link { display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:8px; font-size:13px; font-weight:500; color:#94a3b8; transition:all .15s; }
    .sidebar-link:hover { background:rgba(255,255,255,.1); color:#fff; }
    .sidebar-link.active { background:rgba(255,255,255,.15); color:#fff; }
    .sidebar-link svg { width:16px; height:16px; flex-shrink:0; }
    
    /* Custom scrollbar for sidebar nav */
    nav::-webkit-scrollbar {
      width: 5px;
    }
    nav::-webkit-scrollbar-track {
      background: transparent;
    }
    nav::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.15);
      border-radius: 10px;
    }
    nav::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.3);
    }
    nav {
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
    }
  </style>
</head>
<body class="font-sans bg-slate-100 min-h-screen">

<?php
$adminPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$adminPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
?>

<div class="flex min-h-screen">

  <!-- Sidebar -->
  <aside class="w-60 bg-primary text-white flex-shrink-0 flex flex-col fixed inset-y-0 left-0 z-40 shadow-xl">

    <!-- Logo -->
    <div class="px-5 py-5 border-b border-white/10">
      <a href="<?= BASE_PATH ?>/" class="flex items-center gap-2.5">
        <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-8 w-auto brightness-0 invert">
        <span class="font-bold text-base tracking-tight">ActShare</span>
      </a>
      <div class="mt-2 flex items-center gap-1.5">
        <span class="inline-block w-1.5 h-1.5 rounded-full bg-secondary"></span>
        <span class="text-[11px] text-slate-400 font-medium uppercase tracking-widest">Painel Admin</span>
      </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

      <p class="px-3 pt-2 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Geral</p>

      <a href="<?= BASE_PATH ?>/admin" class="sidebar-link <?= $adminPath === 'admin' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
        Dashboard
      </a>

      <p class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Conteúdo</p>

      <a href="<?= BASE_PATH ?>/admin/cursos" class="sidebar-link <?= str_starts_with($adminPath, 'admin/cursos') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        Cursos
      </a>

      <a href="<?= BASE_PATH ?>/admin/alunos" class="sidebar-link <?= $adminPath === 'admin/alunos' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 12c0 3.5-4.03 6.5-9 6.5s-9-3-9-6.5c0-.53.086-1.04.245-1.518L12 14z"/></svg>
        Alunos
      </a>

      <a href="<?= BASE_PATH ?>/admin/categorias" class="sidebar-link <?= $adminPath === 'admin/categorias' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        Categorias
      </a>

      <a href="<?= BASE_PATH ?>/admin/instrutores" class="sidebar-link <?= $adminPath === 'admin/instrutores' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        Instrutores
      </a>

      <a href="<?= BASE_PATH ?>/admin/perguntas" class="sidebar-link <?= $adminPath === 'admin/perguntas' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Banco de Questões
      </a>

      <a href="<?= BASE_PATH ?>/admin/satisfacao" class="sidebar-link <?= $adminPath === 'admin/satisfacao' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        Pesquisa de Satisfação
      </a>

      <p class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Comercial</p>

      <a href="<?= BASE_PATH ?>/admin/pedidos" class="sidebar-link <?= $adminPath === 'admin/pedidos' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-4-7 4V5a2 2 0 012-2h10a2 2 0 012 2v16z"/></svg>
        Pedidos
      </a>

      <a href="<?= BASE_PATH ?>/admin/clientes" class="sidebar-link <?= $adminPath === 'admin/clientes' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M19 21H5m0 0H3m8-14h.01M11 11h.01M11 15h.01M15 7h.01M15 11h.01M15 15h.01"/></svg>
        Clientes
      </a>

      <a href="<?= BASE_PATH ?>/admin/cupons" class="sidebar-link <?= $adminPath === 'admin/cupons' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
        Cupons de Desconto
      </a>

      <a href="<?= BASE_PATH ?>/admin/combos" class="sidebar-link <?= $adminPath === 'admin/combos' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        Combos
      </a>

      <p class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Certificados</p>

      <a href="<?= BASE_PATH ?>/admin/certificados" class="sidebar-link <?= $adminPath === 'admin/certificados' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Emissão Manual
      </a>

      <p class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Pessoas</p>

      <a href="<?= BASE_PATH ?>/admin/usuarios" class="sidebar-link <?= $adminPath === 'admin/usuarios' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Usuários
      </a>

    </nav>

    <!-- Footer sidebar -->
    <div class="px-4 py-4 border-t border-white/10">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-xs font-bold text-white" id="admin-avatar">A</div>
        <div class="min-w-0">
          <p id="admin-username" class="text-sm font-medium text-white truncate"></p>
          <p class="text-[11px] text-slate-400">Administrador</p>
        </div>
      </div>
      <div class="flex gap-2">
        <a href="<?= BASE_PATH ?>/" target="_blank"
           class="flex-1 flex items-center justify-center gap-1 text-[11px] text-slate-400 hover:text-white border border-white/10 rounded-md py-1.5 transition-colors">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          Site
        </a>
        <button onclick="authLogout()"
          class="flex-1 flex items-center justify-center gap-1 text-[11px] text-slate-400 hover:text-red-400 border border-white/10 rounded-md py-1.5 transition-colors">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Sair
        </button>
      </div>
    </div>

  </aside>

  <!-- Main content -->
  <main class="flex-1 ml-60 min-h-screen flex flex-col">

    <!-- Topbar -->
    <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-30">
      <div>
        <h1 class="text-base font-semibold text-slate-800"><?= htmlspecialchars($pageTitle ?? 'Admin') ?></h1>
      </div>
      <div class="flex items-center gap-3">
        <a href="<?= BASE_PATH ?>/" target="_blank" class="text-xs text-slate-500 hover:text-primary transition-colors flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          Ver site
        </a>
      </div>
    </header>

    <div class="flex-1 p-8">

<script src="<?= BASE_PATH ?>/assets/js/auth.js?v=2"></script>
<script src="<?= BASE_PATH ?>/assets/js/api.js?v=2"></script>
<script src="<?= BASE_PATH ?>/assets/js/masks.js?v=1"></script>
<script src="<?= BASE_PATH ?>/assets/js/sort-utils.js?v=1"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login'; return; }
    if (user.role !== 'admin') { window.location.href = BASE + '/painel'; return; }
    const nome = user.nome || 'Admin';
    const el = document.getElementById('admin-username');
    if (el) el.textContent = nome;
    const av = document.getElementById('admin-avatar');
    if (av) av.textContent = nome.charAt(0).toUpperCase();
  });
</script>
