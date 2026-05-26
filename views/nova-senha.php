<?php $pageTitle = 'Nova Senha — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gray-50 px-4 py-12">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Criar nova senha</h1>
      </div>

      <div id="success-msg" class="hidden bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">
        Senha alterada! <a href="<?= BASE_PATH ?>/login" class="font-medium underline">Fazer login</a>
      </div>

      <div id="invalid-token" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
        Link inválido ou expirado. <a href="<?= BASE_PATH ?>/recuperar-senha" class="underline">Solicitar novo link</a>
      </div>

      <form id="nova-senha-form" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
          <input type="password" id="nova-senha" required minlength="6"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Mínimo 6 caracteres">
        </div>

        <div id="nova-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3"></div>

        <button type="submit" id="nova-btn"
          class="w-full bg-primary text-white font-semibold py-3 rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-60">
          Salvar nova senha
        </button>
      </form>
    </div>
  </div>
</div>

<script>
  const token = new URLSearchParams(location.search).get('token');
  if (!token) {
    document.getElementById('nova-senha-form').classList.add('hidden');
    document.getElementById('invalid-token').classList.remove('hidden');
  }

  document.getElementById('nova-senha-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('nova-btn');
    const err = document.getElementById('nova-erro');
    btn.disabled = true;
    err.classList.add('hidden');

    try {
      await apiPost(BASE + '/api/auth/confirm-reset', {
        token,
        password: document.getElementById('nova-senha').value,
      });
      document.getElementById('nova-senha-form').classList.add('hidden');
      document.getElementById('success-msg').classList.remove('hidden');
    } catch (ex) {
      err.textContent = ex.message || 'Erro ao redefinir senha.';
      err.classList.remove('hidden');
      btn.disabled = false;
    }
  });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
