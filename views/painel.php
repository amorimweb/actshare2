<?php $pageTitle = 'Meu Painel — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-10">
  <!-- Cabeçalho do painel -->
  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Meu Painel</h1>
      <p id="painel-saudacao" class="text-gray-500 text-sm mt-1"></p>
    </div>
    <div class="flex gap-2">
      <div id="painel-admin-link" class="hidden">
        <a href="<?= BASE_PATH ?>/admin" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors">
          Painel Admin
        </a>
      </div>
      <div id="painel-gestor-link" class="hidden">
        <a href="<?= BASE_PATH ?>/gestor" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors">
          Painel do Gestor
        </a>
      </div>
    </div>
  </div>

  <!-- Meus Cursos -->
  <section>
    <h2 class="text-lg font-semibold text-gray-700 mb-5">Meus Cursos</h2>
    <div id="matriculas-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div class="col-span-full text-center py-12 text-gray-400">
        <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
        <p>Carregando...</p>
      </div>
    </div>
    <div id="matriculas-empty" class="hidden text-center py-12">
      <p class="text-gray-400 mb-4">Você ainda não está matriculado em nenhum curso.</p>
      <a href="<?= BASE_PATH ?>/cursos" class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-blue-900 transition-colors">
        Explorar cursos
      </a>
    </div>
  </section>
</div>

<script src="<?= BASE_PATH ?>/assets/js/painel.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login'; return; }

    document.getElementById('painel-saudacao').textContent = `Bem-vindo(a), ${user.nome}`;
    if (user.role === 'admin') document.getElementById('painel-admin-link').classList.remove('hidden');
    if (user.role === 'gestor') document.getElementById('painel-gestor-link').classList.remove('hidden');

    carregarMatriculas();
  });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
