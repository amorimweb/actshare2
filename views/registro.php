<?php $pageTitle = 'Cadastro - ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="bg-gray-50 px-4 py-12">
  <div class="mx-auto w-full max-w-4xl">
    <div class="mb-6 text-center">
      <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-12 mx-auto mb-4">
      <h1 class="text-2xl font-bold text-gray-800">Criar sua conta</h1>
      <p class="text-gray-500 text-sm mt-1">Preencha os dados para acesso, certificado e nota fiscal.</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm md:p-8">
      <div id="success-msg" class="hidden bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">
        Cadastro realizado. Redirecionando...
      </div>

      <form id="registro-form" class="space-y-8">
        <section>
          <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 mb-4">Acesso</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
              <input type="text" id="reg-nome" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="Nome que aparecerá no certificado">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
              <input type="email" id="reg-email" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="seu@email.com">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
              <div class="relative">
                <input type="password" id="reg-password" required minlength="6"
                  class="w-full border border-gray-300 rounded-lg pl-4 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Mínimo 6 caracteres">
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
          </div>
        </section>

        <section>
          <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400">Dados fiscais e certificado</h2>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-500">Opcional por enquanto</span>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">CPF ou CNPJ</label>
              <input type="text" id="reg-documento"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="000.000.000-00">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp / Celular</label>
              <input type="text" id="reg-telefone"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="(11) 99999-9999">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de pessoa</label>
              <select id="reg-tipo-pessoa"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Selecionar depois</option>
                <option value="fisica">Pessoa física</option>
                <option value="juridica">Pessoa jurídica</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Data de nascimento</label>
              <input type="date" id="reg-data-nascimento"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Razão social</label>
              <input type="text" id="reg-razao-social"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="Obrigatório futuramente para CNPJ">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Inscrição estadual</label>
              <input type="text" id="reg-inscricao-estadual"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="Isento, se aplicável">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
              <input type="text" id="reg-cep"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="00000-000">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
              <input type="text" id="reg-endereco"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="Rua, avenida, travessa">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
              <input type="text" id="reg-numero"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
              <input type="text" id="reg-complemento"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
              <input type="text" id="reg-bairro"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
              <input type="text" id="reg-cidade"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
              <input type="text" id="reg-estado" maxlength="2"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="SP">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
              <input type="text" id="reg-pais"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                value="Brasil">
            </div>
          </div>
        </section>

        <div id="reg-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3"></div>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm text-gray-500">
            Já tem conta? <a href="<?= BASE_PATH ?>/login" class="text-primary font-medium hover:underline">Faça login</a>
          </p>
          <button type="submit" id="reg-btn"
            class="bg-primary text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-60">
            Criar conta
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const optionalFields = [
    'documento',
    'telefone',
    'tipo-pessoa',
    'data-nascimento',
    'razao-social',
    'inscricao-estadual',
    'cep',
    'endereco',
    'numero',
    'complemento',
    'bairro',
    'cidade',
    'estado',
    'pais',
  ];

  function getValue(id) {
    return document.getElementById(id)?.value.trim() || '';
  }

  document.getElementById('registro-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('reg-btn');
    const err = document.getElementById('reg-error');
    btn.disabled = true;
    btn.textContent = 'Criando conta...';
    err.classList.add('hidden');

    const payload = {
      nome: getValue('reg-nome'),
      email: getValue('reg-email'),
      password: getValue('reg-password'),
    };

    optionalFields.forEach((field) => {
      payload[field.replaceAll('-', '_')] = getValue('reg-' + field);
    });

    try {
      const res = await apiPost(BASE + '/api/auth/register', payload);
      if (res.user) authSetUser(res.user);

      document.getElementById('registro-form').classList.add('hidden');
      document.getElementById('success-msg').classList.remove('hidden');

      const redirect = new URLSearchParams(location.search).get('redirect') || BASE + '/painel';
      setTimeout(() => { window.location.href = redirect; }, 600);
    } catch (ex) {
      err.textContent = ex.message || 'Erro ao criar conta.';
      err.classList.remove('hidden');
      btn.disabled = false;
      btn.textContent = 'Criar conta';
    }
  });

  function togglePasswordVisibility() {
    const pwdInput = document.getElementById('reg-password');
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
