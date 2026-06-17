<?php
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-glass fixed-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= $rootUrl ?>/">
            <h3 class="m-0 text-white brand-font"><span class="text-orange">G</span>OMOS <i class="fa-solid fa-dumbbell text-orange"></i></h3>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link nav-link-gomos" href="<?= $rootUrl ?>/">HOME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-gomos" href="<?= $rootUrl ?>/sobre">SOBRE</a>
                </li>
                <li class="nav-item mt-2 mt-lg-0">
                    <a class="btn btn-outline-gomos btn-sm border-secondary text-secondary" href="<?= $rootUrl ?>/login">ENTRAR</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary-gomos btn-sm text-dark px-3 py-2" href="<?= $rootUrl ?>/cadastro">CRIAR CONTA</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
