<?php 
$pageTitle = "Entrar";
$isLanding = true;
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="py-5" style="background-color: var(--bg-dark); min-height: 100vh; display: flex; align-items: center;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card-gomos p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="text-white m-0"><span class="text-orange">G</span>OMOS <i class="fa-solid fa-dumbbell text-orange"></i></h2>
                        <p class="text-muted-gomos">Treina. Posta. Supera.</p>
                    </div>

                    <form action="<?= $rootUrl ?>/login" method="POST">
                        <!-- E-mail ou Username -->
                        <div class="mb-3">
                            <label for="email" class="form-label text-muted-gomos">E-mail ou Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="email" id="email" class="form-control form-control-gomos" placeholder="exemplo@email.com ou username" required>
                            </div>
                        </div>

                        <!-- Senha -->
                        <div class="mb-3">
                            <label for="senha" class="form-label text-muted-gomos">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="senha" id="senha" class="form-control form-control-gomos" placeholder="Sua senha de ferro" required>
                            </div>
                        </div>

                        <!-- Lembrar de mim e Esqueci minha senha -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input bg-dark border-secondary" type="checkbox" id="lembrar" name="lembrar">
                                <label class="form-check-label text-muted-gomos small" for="lembrar">
                                    Lembrar de mim
                                </label>
                            </div>
                            <a href="#" class="text-lime text-decoration-none small" onclick="alert('Funcionalidade de redefinição simulada! Entre em contato com o administrador.'); return false;">Esqueci minha senha</a>
                        </div>

                        <!-- Botão Entrar -->
                        <button type="submit" class="btn btn-primary-gomos w-100 py-3 mb-3">ENTRAR NA COMUNIDADE</button>

                        <div class="text-center mt-3">
                            <span class="text-muted-gomos small">Novo no GOMOS? </span>
                            <a href="<?= $rootUrl ?>/cadastro" class="text-lime text-decoration-none small fw-bold">Criar conta grátis</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
