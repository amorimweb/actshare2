<?php
require_once __DIR__ . '/config.php';

$page = trim($_GET['_page'] ?? '', '/');

// Remove query string do _page se vier junto
$page = strtok($page, '?') ?: '';

// Extrai segmentos para roteamento dinâmico
$segments = explode('/', $page);

// Tabela de rotas: [padrão regex, arquivo view, extrator de params]
$routes = [
    ''                              => 'home',
    'login'                         => 'login',
    'registro'                      => 'registro',
    'recuperar-senha'               => 'recuperar-senha',
    'nova-senha'                    => 'nova-senha',
    'cursos'                        => 'cursos',
    'certificado'                   => 'certificado',
    'treinamentos/abertos'          => 'treinamentos/abertos',
    'treinamentos/gravados'         => 'treinamentos/gravados',
    'treinamentos/lgpd'             => 'treinamentos/lgpd',
    'painel'                        => 'painel',
    'meus-dados'                    => 'meus-dados',
    'alterar-senha'                 => 'alterar-senha',
    'gestor'                        => 'gestor/dashboard',
    'carrinho'                      => 'carrinho',
    'checkout'                      => 'checkout',
    'pedido/confirmacao'            => 'pedido-confirmacao',
    'admin'                         => 'admin/dashboard',
    'admin/cursos'                  => 'admin/cursos',
    'admin/categorias'              => 'admin/categorias',
    'admin/instrutores'             => 'admin/instrutores',
    'admin/usuarios'                => 'admin/usuarios',
    'admin/pedidos'                 => 'admin/pedidos',
    'admin/clientes'                => 'admin/clientes',
    'admin/alunos'                  => 'admin/alunos',
    'explicacao-exames'             => 'explicacao-exames',
    'admin/exames'                  => 'admin/exames',
    'admin/desconto-progressivo'    => 'admin/desconto-progressivo',
    'admin/email-templates'         => 'admin/email-templates',
    'admin/perguntas'               => 'admin/perguntas',
    'admin/cupons'                  => 'admin/cupons',
    'admin/combos'                  => 'admin/combos',
    'admin/certificados'            => 'admin/certificados',
    'admin/satisfacao'              => 'admin/satisfacao',
    'validar-certificado'           => 'validar-certificado',
];

// Rotas dinâmicas com parâmetro
$dynamicRoutes = [
    '#^cursos/(\d+)$#'       => ['view' => 'curso-detalhe',   'param' => 'id'],
    '#^combos/(\d+)$#'       => ['view' => 'combo-detalhe',   'param' => 'id'],
    '#^painel/curso/(\d+)$#' => ['view' => 'painel-curso',    'param' => 'id'],
    '#^painel/exame/(\d+)$#' => ['view' => 'painel-exame',    'param' => 'id'],
    '#^admin/cursos/(\d+)$#' => ['view' => 'admin/curso-detalhe', 'param' => 'id'],
    '#^gestor/curso/(\d+)$#' => ['view' => 'gestor/curso-detalhe', 'param' => 'id'],
];

$viewFile = null;

// Rota estática
if (isset($routes[$page])) {
    $viewFile = __DIR__ . '/views/' . $routes[$page] . '.php';
}

// Rota dinâmica
if (!$viewFile) {
    foreach ($dynamicRoutes as $pattern => $info) {
        if (preg_match($pattern, $page, $matches)) {
            $_GET[$info['param']] = $matches[1];
            $viewFile = __DIR__ . '/views/' . $info['view'] . '.php';
            break;
        }
    }
}

if ($viewFile && file_exists($viewFile)) {
    require $viewFile;
} else {
    http_response_code(404);
    $pageTitle = 'Página não encontrada — ActShare';
    require __DIR__ . '/views/layout/header.php';
    echo '<div class="max-w-xl mx-auto px-4 py-24 text-center">';
    echo '<h1 class="text-6xl font-bold text-gray-200 mb-4">404</h1>';
    echo '<p class="text-gray-500 text-lg mb-6">Página não encontrada.</p>';
    echo '<a href="' . BASE_PATH . '/" class="bg-primary text-white font-medium px-6 py-3 rounded-lg hover:bg-blue-900 transition-colors">Voltar ao início</a>';
    echo '</div>';
    require __DIR__ . '/views/layout/footer.php';
}
