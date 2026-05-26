<?php $pageTitle = 'Entrar — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gray-50 px-4 py-12">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <div class="text-center mb-8">
        <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-12 mx-auto mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Entrar na sua conta</h1>
        <p class="text-gray-500 text-sm mt-1">Acesse sua área de aprendizado</p>
      </div>

      <form id="login-form" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
          <input type="email" id="login-email" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
            placeholder="seu@email.com">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
          <input type="password" id="login-password" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
            placeholder="••••••••">
        </div>

        <div id="login-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3"></div>

        <button type="submit" id="login-btn"
          class="w-full bg-primary text-white font-semibold py-3 rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-60">
          Entrar
        </button>
      </form>

      <div class="mt-6 text-center space-y-2">
        <a href="<?= BASE_PATH ?>/recuperar-senha" class="text-sm text-primary hover:underline block">Esqueci minha senha</a>
        <p class="text-sm text-gray-500">
          Não tem conta?
          <a href="<?= BASE_PATH ?>/registro" class="text-primary font-medium hover:underline">Cadastre-se grátis</a>
        </p>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('login-btn');
    const err = document.getElementById('login-error');
    btn.disabled = true;
    btn.textContent = 'Entrando...';
    err.classList.add('hidden');

    try {
      const res = await apiPost(BASE + '/api/auth/login', {
        email:    document.getElementById('login-email').value,
        password: document.getElementById('login-password').value,
      });
      authSetUser(res.user);
      const redirect = new URLSearchParams(location.search).get('redirect') || BASE + '/painel';
      window.location.href = redirect;
    } catch (ex) {
      err.textContent = ex.message || 'Erro ao fazer login.';
      err.classList.remove('hidden');
      btn.disabled = false;
      btn.textContent = 'Entrar';
    }
  });

  // Redireciona se já logado
  document.addEventListener('DOMContentLoaded', () => {
    if (authGetUser()) window.location.href = BASE + '/painel';
  });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
