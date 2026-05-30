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
<body class="font-sans bg-gray-50 min-h-screen">

<div class="flex min-h-screen">
  <!-- Sidebar -->
  <aside class="w-64 bg-primary text-white flex-shrink-0 flex flex-col">
    <div class="p-6 border-b border-blue-900">
      <a href="<?= BASE_PATH ?>/" class="flex items-center gap-2">
        <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-8 w-auto brightness-0 invert">
        <span class="font-bold text-lg">ActShare</span>
      </a>
      <span class="text-xs text-blue-300 mt-1 block">Painel Admin</span>
    </div>

    <nav class="flex-1 p-4 space-y-1">
      <a href="<?= BASE_PATH ?>/admin" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
        Dashboard
      </a>
      <a href="<?= BASE_PATH ?>/admin/cursos" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        Cursos
      </a>
      <a href="<?= BASE_PATH ?>/admin/categorias" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        Categorias
      </a>
      <a href="<?= BASE_PATH ?>/admin/usuarios" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Usuários
      </a>
    </nav>

    <div class="p-4 border-t border-blue-900">
      <span id="admin-username" class="text-xs text-blue-300 block mb-2"></span>
      <button onclick="authLogout()" class="text-xs text-blue-300 hover:text-white transition-colors">Sair</button>
    </div>
  </aside>

  <!-- Main content -->
  <main class="flex-1 overflow-auto">
    <div class="p-8">

<script src="<?= BASE_PATH ?>/assets/js/auth.js?v=2"></script>
<script src="<?= BASE_PATH ?>/assets/js/api.js?v=2"></script>
<script>
  // Protege página admin
  document.addEventListener('DOMContentLoaded', () => {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login'; return; }
    if (user.role !== 'admin') { window.location.href = BASE + '/painel'; return; }
    const el = document.getElementById('admin-username');
    if (el) el.textContent = user.nome;
  });
</script>
