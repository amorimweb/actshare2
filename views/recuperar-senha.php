<?php $pageTitle = 'Recuperar Senha — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gray-50 px-4 py-12">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Recuperar senha</h1>
        <p class="text-gray-500 text-sm mt-1">Enviaremos um link para redefinir sua senha</p>
      </div>

      <div id="success-msg" class="hidden bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4 space-y-2">
        <p>Se o e-mail estiver cadastrado, você receberá o link em breve.</p>
        <div id="debug-block" class="hidden bg-blue-50 border border-blue-150 rounded-xl p-3.5 mt-2 text-left space-y-1.5 text-blue-800">
          <p class="text-[10px] font-bold uppercase tracking-wide">Ambiente de Testes (Localhost)</p>
          <p class="text-xs text-blue-700">Clique no link abaixo para criar uma nova senha:</p>
          <a href="#" id="debug-link" class="text-xs font-bold text-primary hover:underline break-all block"></a>
        </div>
      </div>

      <form id="reset-form" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">E-mail cadastrado</label>
          <input type="email" id="reset-email" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="seu@email.com">
        </div>

        <div id="reset-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3"></div>

        <button type="submit" id="reset-btn"
          class="w-full bg-primary text-white font-semibold py-3 rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-60">
          Enviar link
        </button>
      </form>

      <p class="mt-6 text-center text-sm">
        <a href="<?= BASE_PATH ?>/login" class="text-primary hover:underline">Voltar para o login</a>
      </p>
    </div>
  </div>
</div>

<script>
  document.getElementById('reset-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('reset-btn');
    const err = document.getElementById('reset-error');
    btn.disabled = true;
    err.classList.add('hidden');

    try {
      const res = await apiPost(BASE + '/api/auth/reset-password', { email: document.getElementById('reset-email').value });
      document.getElementById('reset-form').classList.add('hidden');
      document.getElementById('success-msg').classList.remove('hidden');
      
      if (res.debug_link) {
        document.getElementById('debug-block').classList.remove('hidden');
        document.getElementById('debug-link').href = res.debug_link;
        document.getElementById('debug-link').textContent = res.debug_link;
      }
    } catch (ex) {
      err.textContent = ex.message || 'Erro ao enviar link.';
      err.classList.remove('hidden');
      btn.disabled = false;
    }
  });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
