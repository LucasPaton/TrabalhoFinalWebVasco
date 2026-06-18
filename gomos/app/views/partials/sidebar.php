<?php
$usuarioId = \App\Helpers\Session::get('usuario_id');
$adminId = \App\Helpers\Session::get('admin_id');
$nome = \App\Helpers\Session::get('nome') ?? \App\Helpers\Session::get('admin_nome') ?? 'Atleta';
$username = \App\Helpers\Session::get('username') ?? 'admin';
$foto = \App\Helpers\Session::get('foto_perfil') ?? 'default_avatar.png';
$fitness = \App\Helpers\Session::get('nivel_fitness') ?? 'Admin';
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');

// Obter a rota atual para destacar o item ativo no menu
$uri = $_SERVER['REQUEST_URI'];
?>

<div class="sidebar-gomos">
    <!-- Brand Logo -->
    <div class="sidebar-brand text-center d-flex align-items-center justify-content-center">
        <a href="<?= $rootUrl ?>/feed" class="text-decoration-none">
            <h3 class="m-0 text-white brand-font"><span class="text-orange">G</span>OMOS <i class="fa-solid fa-dumbbell text-orange"></i></h3>
        </a>
    </div>

    <!-- User Profile Header in Sidebar -->
    <div class="sidebar-user-info">
        <img src="<?= $root ?>/assets/img/<?= $foto ?>" alt="Avatar" class="sidebar-avatar">
        <div>
            <h6 class="m-0 text-white fw-bold text-truncate" style="max-width: 160px;"><?= $nome ?></h6>
            <span class="text-muted-gomos small">@<?= $username ?></span>
            <div class="mt-1">
                <span class="badge badge-dificuldade badge-iniciante" style="font-size: 0.65rem; padding: 2px 6px;">
                    <?= strtoupper($fitness) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Barra de Busca de Usuários -->
    <?php if ($usuarioId): ?>
    <div class="px-3 py-3" style="border-bottom: 1px solid var(--border-color);">
        <div class="search-users-container">
            <i class="fa-solid fa-magnifying-glass search-users-icon"></i>
            <input type="text" 
                   id="busca-usuarios-input" 
                   class="search-users-input" 
                   placeholder="Buscar atletas..." 
                   autocomplete="off">
            <div id="busca-usuarios-resultados" class="search-results-dropdown"></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation Menu -->
    <ul class="sidebar-menu">
        <?php if ($usuarioId): ?>
            <li class="sidebar-menu-item <?= (strpos($uri, '/feed') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/feed">
                    <i class="fa-solid fa-house"></i> Feed Social
                </a>
            </li>
            <li class="sidebar-menu-item <?= (strpos($uri, '/perfil') !== false && strpos($uri, '/editar') === false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/perfil">
                    <i class="fa-solid fa-user"></i> Meu Perfil
                </a>
            </li>
            <li class="sidebar-menu-item <?= (strpos($uri, '/treino/criar') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/treino/criar">
                    <i class="fa-solid fa-plus"></i> Criar Treino
                </a>
            </li>
            <li class="sidebar-menu-item <?= (strpos($uri, '/treino/comparar') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/treino/comparar">
                    <i class="fa-solid fa-scale-balanced"></i> Comparar Treinos
                </a>
            </li>
            <li class="sidebar-menu-item <?= (strpos($uri, '/ranking') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/ranking">
                    <i class="fa-solid fa-trophy"></i> Rankings
                </a>
            </li>
            <li class="sidebar-menu-item <?= (strpos($uri, '/usuarios/pesquisar') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/usuarios/pesquisar">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar Atletas
                </a>
            </li>
            <li class="sidebar-menu-item <?= (strpos($uri, '/academias') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/academias">
                    <i class="fa-solid fa-location-dot"></i> Unidades GOMOS
                </a>
            </li>
        <?php endif; ?>

        <?php if ($adminId): ?>
            <li class="sidebar-menu-item <?= (strpos($uri, '/admin') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/admin">
                    <i class="fa-solid fa-lock"></i> Painel Admin
                </a>
            </li>
        <?php endif; ?>

        <li class="sidebar-menu-item mt-4">
            <a href="<?= $rootUrl ?>/logout" class="text-danger">
                <i class="fa-solid fa-right-from-bracket text-danger"></i> Sair
            </a>
        </li>
    </ul>
</div>

<!-- Script de Busca de Usuários -->
<?php if ($usuarioId): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBusca = document.getElementById('busca-usuarios-input');
    const containerResultados = document.getElementById('busca-usuarios-resultados');
    const rootPath = window.GOMOS_ROOT || '';
    let timerBusca = null;

    if (!inputBusca) return;

    // Redirecionar ao apertar Enter
    inputBusca.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query.length >= 2) {
                window.location.href = rootPath + '/usuarios/pesquisar?q=' + encodeURIComponent(query);
            }
        }
    });

    inputBusca.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(timerBusca);

        if (query.length < 2) {
            containerResultados.classList.remove('show');
            containerResultados.innerHTML = '';
            return;
        }

        // Mostrar loading
        containerResultados.innerHTML = '<div class="search-loading"><i class="fa-solid fa-spinner fa-spin"></i> Buscando...</div>';
        containerResultados.classList.add('show');

        // Debounce de 350ms
        timerBusca = setTimeout(function() {
            fetch(rootPath + '/usuarios/buscar?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.resultados && data.resultados.length > 0) {
                        let html = '';
                        data.resultados.forEach(function(user) {
                            html += '<a href="' + user.perfil_url + '" class="search-result-item">';
                            html += '  <img src="' + user.foto_url + '" alt="' + user.nome + '" class="search-result-avatar">';
                            html += '  <div class="search-result-info">';
                            html += '    <div class="search-result-name">' + user.nome + '</div>';
                            html += '    <div class="search-result-username">@' + user.username + '</div>';
                            if (user.cidade && user.estado) {
                                html += '    <div class="search-result-location"><i class="fa-solid fa-location-dot"></i> ' + user.cidade + '/' + user.estado + '</div>';
                            }
                            html += '  </div>';
                            html += '</a>';
                        });
                        
                        // Item de ver todos os resultados no final
                        html += '<a href="' + rootPath + '/usuarios/pesquisar?q=' + encodeURIComponent(query) + '" class="search-result-item text-center justify-content-center text-orange fw-bold border-top border-secondary">';
                        html += '  <i class="fa-solid fa-magnifying-glass me-2"></i> Ver todos os resultados';
                        html += '</a>';
                        
                        containerResultados.innerHTML = html;
                    } else {
                        containerResultados.innerHTML = '<div class="search-no-results"><i class="fa-solid fa-user-xmark"></i> Nenhum atleta encontrado.</div>';
                    }
                    containerResultados.classList.add('show');
                })
                .catch(function() {
                    containerResultados.innerHTML = '<div class="search-no-results">Erro ao buscar. Tente novamente.</div>';
                    containerResultados.classList.add('show');
                });
        }, 350);
    });

    // Fechar dropdown ao clicar fora
    document.addEventListener('click', function(e) {
        if (!inputBusca.contains(e.target) && !containerResultados.contains(e.target)) {
            containerResultados.classList.remove('show');
        }
    });

    // Reabrir ao focar no input se já tem texto
    inputBusca.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && containerResultados.innerHTML.trim() !== '') {
            containerResultados.classList.add('show');
        }
    });
});
</script>
<?php endif; ?>

