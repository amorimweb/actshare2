<?php $pageTitle = 'Cadastro — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gray-50 px-4 py-12">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <div class="text-center mb-8">
        <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-12 mx-auto mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Criar sua conta</h1>
        <p class="text-gray-500 text-sm mt-1">Comece a aprender hoje mesmo</p>
      </div>

      <div id="success-msg" class="hidden bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">
        Cadastro realizado! <a href="<?= BASE_PATH ?>/login" class="font-medium underline">Faça login</a>
      </div>

      <form id="registro-form" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
          <input type="text" id="reg-nome" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Seu nome">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
          <input type="email" id="reg-email" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="seu@email.com">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
          <input type="password" id="reg-password" required minlength="6"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Mínimo 6 caracteres">
        </div>

        <div id="reg-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3"></div>

        <button type="submit" id="reg-btn"
          class="w-full bg-primary text-white font-semibold py-3 rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-60">
          Criar conta
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-500">
        Já tem conta? <a href="<?= BASE_PATH ?>/login" class="text-primary font-medium hover:underline">Faça login</a>
      </p>
    </div>
  </div>
</div>

<script>
  document.getElementById('registro-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('reg-btn');
    const err = document.getElementById('reg-error');
    btn.disabled = true;
    btn.textContent = 'Criando conta...';
    err.classList.add('hidden');

    try {
      await apiPost(BASE + '/api/auth/register', {
        nome:     document.getElementById('reg-nome').value,
        email:    document.getElementById('reg-email').value,
        password: document.getElementById('reg-password').value,
      });
      document.getElementById('registro-form').classList.add('hidden');
      document.getElementById('success-msg').classList.remove('hidden');
    } catch (ex) {
      err.textContent = ex.message || 'Erro ao criar conta.';
      err.classList.remove('hidden');
      btn.disabled = false;
      btn.textContent = 'Criar conta';
    }
  });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
