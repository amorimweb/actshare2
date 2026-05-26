<?php $pageTitle = 'Dashboard Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<h1 class="text-2xl font-bold text-gray-800 mb-8">Dashboard</h1>

<!-- Cards de estatísticas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
  <?php
  $cards = [
    ['id' => 'stat-cursos',     'label' => 'Cursos',     'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
    ['id' => 'stat-categorias', 'label' => 'Categorias', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
    ['id' => 'stat-alunos',     'label' => 'Alunos',     'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
    ['id' => 'stat-db',         'label' => 'DB Status',  'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2'],
  ];
  foreach ($cards as $c):
  ?>
  <div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center justify-between mb-3">
      <span class="text-sm text-gray-500"><?= $c['label'] ?></span>
      <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $c['icon'] ?>"/>
      </svg>
    </div>
    <div id="<?= $c['id'] ?>" class="text-2xl font-bold text-gray-800">—</div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Ações rápidas -->
<div class="bg-white rounded-xl border border-gray-200 p-6">
  <h2 class="font-semibold text-gray-700 mb-4">Ações rápidas</h2>
  <div class="flex flex-wrap gap-3">
    <a href="<?= BASE_PATH ?>/admin/cursos" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors">
      + Novo Curso
    </a>
    <a href="<?= BASE_PATH ?>/admin/categorias" class="border border-primary text-primary text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors">
      + Nova Categoria
    </a>
    <a href="<?= BASE_PATH ?>/admin/usuarios" class="border border-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
      Ver Usuários
    </a>
  </div>
</div>

<script>
  async function carregarStats() {
    try {
      const [cursos, categorias, usuarios, db] = await Promise.all([
        apiFetch(BASE + '/api/cursos'),
        apiFetch(BASE + '/api/categorias'),
        apiFetch(BASE + '/api/admin/usuarios'),
        apiFetch(BASE + '/api/debug/db'),
      ]);
      document.getElementById('stat-cursos').textContent     = cursos.length;
      document.getElementById('stat-categorias').textContent = categorias.length;
      document.getElementById('stat-alunos').textContent     = usuarios.filter(u => u.role === 'aluno').length;
      document.getElementById('stat-db').textContent         = db.ok ? '✓ Online' : '✗ Erro';
    } catch (e) {
      console.error(e);
    }
  }
  document.addEventListener('DOMContentLoaded', carregarStats);
</script>

    </div><!-- /p-8 -->
  </main>
</div><!-- /flex -->
</body>
</html>
