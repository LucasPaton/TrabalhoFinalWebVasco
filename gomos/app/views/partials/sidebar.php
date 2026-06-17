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
            <li class="sidebar-menu-item <?= (strpos($uri, '/academias') !== false) ? 'active' : '' ?>">
                <a href="<?= $rootUrl ?>/academias">
                    <i class="fa-solid fa-location-dot"></i> Buscar Academias
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
