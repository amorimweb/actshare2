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
          <div class="relative">
            <input type="password" id="login-password" required
              class="w-full border border-gray-300 rounded-lg pl-4 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
              placeholder="••••••••">
            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-650" aria-label="Mostrar ou ocultar senha">
              <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg id="eye-off-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 013.914-4.636M12 5c.121 0 .241.002.361.006M15.525 5.525a10.05 10.05 0 014.561 4.636M9.9 9.9a3 3 0 114.2 4.2M3 3l18 18" />
              </svg>
            </button>
          </div>
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

    <div class="mt-4 rounded-xl border border-gray-200 bg-white/70 px-4 py-3 text-xs text-gray-500 shadow-sm">
      <p class="mb-2 font-semibold text-gray-600">Acessos para teste</p>
      <div class="space-y-1">
        <p><span class="font-medium text-gray-700">Admin:</span> admin.teste@actshare.com.br / Teste123</p>
        <p><span class="font-medium text-gray-700">Gestor:</span> gestor.teste@actshare.com.br / Teste123</p>
        <p><span class="font-medium text-gray-700">Aluno:</span> aluno.teste@actshare.com.br / Teste123</p>
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

  function togglePasswordVisibility() {
    const pwdInput = document.getElementById('login-password');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeOffIcon = document.getElementById('eye-off-icon');
    if (pwdInput.type === 'password') {
      pwdInput.type = 'text';
      eyeIcon.classList.add('hidden');
      eyeOffIcon.classList.remove('hidden');
    } else {
      pwdInput.type = 'password';
      eyeIcon.classList.remove('hidden');
      eyeOffIcon.classList.add('hidden');
    }
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
