<?php
// Ativar exibição de erros durante o desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Helpers\Session;

// 1. Registro do Autoloader PSR-4 customizado
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    
    // Converte a primeira parte do namespace (ex: Controllers -> controllers)
    $parts = explode('\\', $relative_class);
    if (count($parts) > 1) {
        $parts[0] = strtolower($parts[0]);
    }
    $file = $base_dir . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Iniciar a sessão
Session::start();

// 3. Sistema de Roteamento
$request_uri = $_SERVER['REQUEST_URI'];
// Remover query string da URL (?param=valor)
if (($pos = strpos($request_uri, '?')) !== false) {
    $request_uri = substr($request_uri, 0, $pos);
}

// Obter a rota relativa caso esteja rodando em uma subpasta (ex: /Projeto_WebFinal/gomos/public/)
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);
$base_path = str_replace('\\', '/', $base_path); // Para compatibilidade com Windows

define('BASE_PATH', rtrim($base_path, '/'));

$alternative_base_path = rtrim($base_path, '/public');

if ($base_path === '/') {
    $path = $request_uri;
} else {
    if (strpos($request_uri, $base_path) === 0) {
        $path = substr($request_uri, strlen($base_path));
    } elseif (!empty($alternative_base_path) && strpos($request_uri, $alternative_base_path) === 0) {
        $path = substr($request_uri, strlen($alternative_base_path));
    } else {
        $path = $request_uri;
    }
}

// Garantir que a rota comece com '/'
if (empty($path)) {
    $path = '/';
}
if ($path[0] !== '/') {
    $path = '/' . $path;
}

$method = $_SERVER['REQUEST_METHOD'];

// Armazenamento das rotas
$routes = [
    'GET' => [],
    'POST' => []
];

// Helper para registrar rotas
function addRoute($method, $routePattern, $controllerAction) {
    global $routes;
    $routes[strtoupper($method)][$routePattern] = $controllerAction;
}

// === REGISTRO DE ROTAS ===

// Landing & Sobre
addRoute('GET', '/', 'App\Controllers\LandingController@index');
addRoute('GET', '/sobre', 'App\Controllers\LandingController@sobre');

// Autenticação
addRoute('GET', '/cadastro', 'App\Controllers\AuthController@cadastroPagina');
addRoute('POST', '/cadastro', 'App\Controllers\AuthController@cadastrar');
addRoute('GET', '/login', 'App\Controllers\AuthController@loginPagina');
addRoute('POST', '/login', 'App\Controllers\AuthController@login');
addRoute('GET', '/logout', 'App\Controllers\AuthController@logout');

// Feed Principal
addRoute('GET', '/feed', 'App\Controllers\FeedController@index');

// Perfis e Amigos
addRoute('GET', '/perfil', 'App\Controllers\UsuarioController@meuPerfil');
addRoute('GET', '/perfil/editar', 'App\Controllers\UsuarioController@editarPerfilPagina');
addRoute('POST', '/perfil/editar', 'App\Controllers\UsuarioController@editarPerfil');
addRoute('GET', '/perfil/{username}', 'App\Controllers\UsuarioController@perfil');
addRoute('POST', '/amigos/adicionar/{id}', 'App\Controllers\UsuarioController@adicionarAmigo');
addRoute('POST', '/amigos/aceitar/{id}', 'App\Controllers\UsuarioController@aceitarAmigo');
addRoute('POST', '/amigos/recusar/{id}', 'App\Controllers\UsuarioController@recusarAmigo');

// Busca de Treinos
addRoute('GET', '/treinos/buscar', 'App\Controllers\TreinoController@buscar');
addRoute('GET', '/treinos/pesquisar', 'App\Controllers\TreinoController@pesquisar');

// Treinos
addRoute('GET', '/treino/criar', 'App\Controllers\TreinoController@criarPagina');
addRoute('POST', '/treino/criar', 'App\Controllers\TreinoController@salvar');
addRoute('GET', '/treino/comparar', 'App\Controllers\TreinoController@comparar');
addRoute('GET', '/treino/{id}', 'App\Controllers\TreinoController@visualizar');
addRoute('POST', '/treino/excluir/{id}', 'App\Controllers\TreinoController@excluir');
addRoute('POST', '/treino/curtir/{id}', 'App\Controllers\TreinoController@curtir');
addRoute('POST', '/treino/comentar/{id}', 'App\Controllers\TreinoController@comentar');
addRoute('POST', '/treino/copiar/{id}', 'App\Controllers\TreinoController@copiar');
addRoute('GET', '/treinos/fichas', 'App\Controllers\TreinoController@fichasUsuario');
addRoute('GET', '/treino/iniciar/{id}', 'App\Controllers\TreinoController@iniciarTreino');
addRoute('POST', '/treino/finalizar/{id}', 'App\Controllers\TreinoController@finalizarTreino');

// Rankings
addRoute('GET', '/ranking', 'App\Controllers\RankingController@index');

// Academias
addRoute('GET', '/academias', 'App\Controllers\AcademiaController@buscar');
addRoute('POST', '/academias/vincular/{id}', 'App\Controllers\AcademiaController@vincular');
addRoute('POST', '/academias/checkin', 'App\Controllers\AcademiaController@checkIn');

// Painel Administrativo
addRoute('GET', '/admin', 'App\Controllers\AdminController@dashboard');
addRoute('POST', '/admin/usuarios/{action}/{id}', 'App\Controllers\AdminController@gerenciarUsuario');
addRoute('POST', '/admin/academias/{action}', 'App\Controllers\AdminController@gerenciarAcademia');
addRoute('POST', '/admin/exercicios/{action}', 'App\Controllers\AdminController@gerenciarExercicio');
addRoute('POST', '/admin/conquistas/{action}', 'App\Controllers\AdminController@gerenciarConquista');
addRoute('POST', '/admin/treino/excluir/{id}', 'App\Controllers\AdminController@excluirTreino');

// === RESOLUÇÃO DA ROTA ===
$matched = false;
$params = [];

if (isset($routes[$method])) {
    foreach ($routes[$method] as $routePattern => $controllerAction) {
        // Converte a rota dinâmica em expressão regular
        // Ex: /perfil/{username} -> ^/perfil/([^/]+)$
        $regex = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePattern);
        $regex = '@^' . $regex . '$@';

        if (preg_match($regex, $path, $matches)) {
            array_shift($matches); // Remove o primeiro elemento (full match)
            $params = $matches;
            $matched = $controllerAction;
            break;
        }
    }
}

if ($matched) {
    list($controllerName, $actionName) = explode('@', $matched);
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $actionName)) {
            // Invoca a ação passando os parâmetros extraídos da URL
            call_user_func_array([$controller, $actionName], $params);
            exit();
        }
    }
}

// Rota não encontrada - Renderiza erro 404 com tema dark
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Não Encontrada | GOMOS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0D0D0D;
            color: #F5F5F5;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 8rem;
            color: #FF6B00;
            line-height: 1;
        }
        .btn-gomos {
            background-color: #FF6B00;
            color: #0D0D0D;
            font-weight: bold;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.25rem;
            letter-spacing: 1px;
            border: none;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }
        .btn-gomos:hover {
            background-color: #A3E635;
            color: #0D0D0D;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="text-center">
        <h1>404</h1>
        <h2 class="mb-4">TREINOU DEMAIS E SE PERDEU?</h2>
        <p class="text-muted mb-4">A página que você está procurando não existe ou foi movida.</p>
        <a href="/" class="btn btn-gomos">VOLTAR PARA A HOME</a>
    </div>
</body>
</html>
