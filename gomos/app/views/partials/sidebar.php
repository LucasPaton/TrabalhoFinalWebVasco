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
    <div class="sidebar-brand d-flex align-items-center justify-content-between px-3">
        <a href="<?= $rootUrl ?>/feed" class="text-decoration-none">
            <h3 class="m-0 text-white brand-font"><span class="text-orange">G</span>OMOS <i class="fa-solid fa-dumbbell text-orange"></i></h3>
        </a>
        <button type="button" 
                id="btn-toggle-sidebar" 
                title="Recolher Menu" 
                style="background: transparent; border: none; color: rgba(255,255,255,0.6); font-size: 1.25rem; cursor: pointer; padding: 0; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; outline: none; box-shadow: none; border-radius: 50%; width: 36px; height: 36px;"
                onmouseover="this.style.color='#ff5f00'; this.style.background='rgba(255,255,255,0.05)';"
                onmouseout="this.style.color='rgba(255,255,255,0.6)'; this.style.background='transparent';">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
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

    <!-- Barra de Busca de Treinos -->
    <?php if ($usuarioId): ?>
    <div class="px-3 py-3" style="border-bottom: 1px solid var(--border-color);">
        <div class="search-users-container">
            <i class="fa-solid fa-magnifying-glass search-users-icon"></i>
            <input type="text" 
                   id="busca-treinos-input" 
                   class="search-users-input" 
                   placeholder="Buscar treinos..." 
                   autocomplete="off"
                   style="padding-left: 42px !important;">
            <div id="busca-treinos-resultados" class="search-results-dropdown"></div>
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
            <li class="sidebar-menu-item <?= (strpos($uri, '/treinos/fichas') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/treinos/fichas">
                    <i class="fa-solid fa-sheet-plastic"></i> Minhas Fichas
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
            <li class="sidebar-menu-item <?= (strpos($uri, '/treinos/pesquisar') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/treinos/pesquisar">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar Treinos
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

<!-- Script de Busca de Treinos -->
<?php if ($usuarioId): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBusca = document.getElementById('busca-treinos-input');
    const containerResultados = document.getElementById('busca-treinos-resultados');
    const rootPath = window.GOMOS_ROOT || '';
    let timerBusca = null;

    if (!inputBusca) return;

    // Redirecionar ao apertar Enter
    inputBusca.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query.length >= 2) {
                window.location.href = rootPath + '/treinos/pesquisar?q=' + encodeURIComponent(query);
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
            fetch(rootPath + '/treinos/buscar?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.resultados && data.resultados.length > 0) {
                        let html = '';
                        data.resultados.forEach(function(treino) {
                            html += '<a href="' + treino.treino_url + '" class="search-result-item">';
                            html += '  <div class="search-result-info w-100">';
                            html += '    <div class="search-result-name text-orange fw-bold">' + treino.titulo + '</div>';
                            html += '    <div class="search-result-username text-muted-gomos">Grupo: ' + treino.grupo_muscular + '</div>';
                            html += '    <div class="search-result-location small text-secondary">Postado por: @' + treino.username + '</div>';
                            html += '  </div>';
                            html += '</a>';
                        });
                        
                        // Item de ver todos os resultados no final
                        html += '<a href="' + rootPath + '/treinos/pesquisar?q=' + encodeURIComponent(query) + '" class="search-result-item text-center justify-content-center text-orange fw-bold border-top border-secondary">';
                        html += '  <i class="fa-solid fa-magnifying-glass me-2"></i> Ver todos os resultados';
                        html += '</a>';
                        
                        containerResultados.innerHTML = html;
                    } else {
                        containerResultados.innerHTML = '<div class="search-no-results"><i class="fa-solid fa-dumbbell"></i> Nenhum treino encontrado.</div>';
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

    // === Lógica de Recolhimento da Sidebar ===
    const btnToggle = document.getElementById('btn-toggle-sidebar');
    const btnReopen = document.getElementById('btn-reopen-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const wrapper = document.querySelector('.dashboard-wrapper');

    // Inicialização da persistência do estado da sidebar no Desktop
    if (localStorage.getItem('sidebarCollapsed') === '1' && window.innerWidth >= 992) {
        if (wrapper) wrapper.classList.add('sidebar-collapsed');
    }

    if (btnToggle) {
        btnToggle.addEventListener('click', function() {
            if (window.innerWidth >= 992) {
                if (wrapper) {
                    wrapper.classList.add('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', '1');
                }
            } else {
                if (wrapper) wrapper.classList.remove('sidebar-mobile-active');
            }
        });
    }

    if (btnReopen) {
        btnReopen.addEventListener('click', function() {
            if (window.innerWidth >= 992) {
                if (wrapper) {
                    wrapper.classList.remove('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', '0');
                }
            } else {
                if (wrapper) wrapper.classList.add('sidebar-mobile-active');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            if (wrapper) wrapper.classList.remove('sidebar-mobile-active');
        });
    }
});
</script>

<!-- Botão flutuante para reabrir a sidebar -->
<button type="button" 
        class="btn-reopen-sidebar" 
        id="btn-reopen-sidebar" 
        title="Expandir Menu"
        style="position: fixed; left: 15px; top: 15px; z-index: 1040; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); color: #ff5f00; width: 42px; height: 42px; border-radius: 8px; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.5); transition: all 0.2s ease; outline: none;"
        onmouseover="this.style.background='#ff5f00'; this.style.color='#121212'"
        onmouseout="this.style.background='#1a1a1a'; this.style.color='#ff5f00'">
    <i class="fa-solid fa-bars"></i>
</button>

<!-- Overlay para cliques em mobile -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>
<?php endif; ?>

