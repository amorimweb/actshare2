<?php $pageTitle = 'Dashboard — Admin'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

  <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
      <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    </div>
    <div>
      <p class="text-xs text-slate-500 font-medium">Cursos</p>
      <p id="stat-cursos" class="text-2xl font-bold text-slate-800">—</p>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
      <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
    </div>
    <div>
      <p class="text-xs text-slate-500 font-medium">Categorias</p>
      <p id="stat-categorias" class="text-2xl font-bold text-slate-800">—</p>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
    <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
      <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    </div>
    <div>
      <p class="text-xs text-slate-500 font-medium">Alunos</p>
      <p id="stat-alunos" class="text-2xl font-bold text-slate-800">—</p>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
    <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
      <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
    </div>
    <div>
      <p class="text-xs text-slate-500 font-medium">DB Status</p>
      <p id="stat-db" class="text-2xl font-bold text-slate-800">—</p>
    </div>
  </div>

</div>

<!-- Ações rápidas -->
<div class="bg-white rounded-xl border border-slate-200 p-6 mb-8">
  <h2 class="text-sm font-semibold text-slate-700 mb-4">Ações rápidas</h2>
  <div class="flex flex-wrap gap-3">
    <a href="<?= BASE_PATH ?>/admin/cursos"
       class="inline-flex items-center gap-2 bg-primary text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-primary-dark transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Novo Curso
    </a>
    <a href="<?= BASE_PATH ?>/admin/categorias"
       class="inline-flex items-center gap-2 border border-slate-200 text-slate-700 text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-slate-50 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Nova Categoria
    </a>
    <a href="<?= BASE_PATH ?>/admin/usuarios"
       class="inline-flex items-center gap-2 border border-slate-200 text-slate-700 text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-slate-50 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
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
      document.getElementById('stat-db').textContent         = db.cursos !== undefined ? '✓ Online' : '✗ Erro';
    } catch (e) {
      console.error(e);
    }
  }
  document.addEventListener('DOMContentLoaded', carregarStats);
</script>

    </div><!-- /p-8 -->
  </main>
</div>
</body>
</html>
