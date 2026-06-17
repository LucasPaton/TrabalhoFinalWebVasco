<?php 
$pageTitle = "Sobre Nós";
$isLanding = true;
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="py-5" style="background-color: var(--bg-dark); min-height: 100vh;">
    <div class="container py-5 mt-5">
        <div class="text-center mb-5">
            <h1 class="display-3 text-white">CONHEÇA O <span class="text-orange">GOMOS</span></h1>
            <p class="text-secondary fs-5">"Treina. Posta. Supera." — A nossa filosofia diária.</p>
        </div>

        <div class="row align-items-center g-5 mt-3">
            <div class="col-lg-6">
                <h2 class="text-white mb-3">A REDE SOCIAL DE QUEM <span class="text-lime">VIVE A ACADEMIA</span></h2>
                <p class="text-muted-gomos mb-4">
                    O GOMOS nasceu da paixão pela musculação e da necessidade de criar uma comunidade onde o foco é o treino, a consistência e a superação. 
                    Inspirado no compartilhamento de fichas de treino e na gamificação do esporte, o GOMOS oferece ferramentas completas para você documentar suas cargas, comparar seu rendimento com amigos e celebrar conquistas.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <h4 class="text-white"><i class="fa-solid fa-fire-flame-curved text-orange me-2"></i> Laranja Intenso</h4>
                        <p class="text-muted-gomos small">Representa a energia física, o calor da última repetição e o foco inabalável.</p>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="text-white"><i class="fa-solid fa-trophy text-lime me-2"></i> Verde-Limão</h4>
                        <p class="text-muted-gomos small">Simboliza a conquista da nova carga, a superação de metas e a evolução muscular.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card-gomos p-4 border-secondary text-center">
                    <i class="fa-solid fa-dumbbell text-orange mb-3" style="font-size: 3.5rem;"></i>
                    <h3 class="text-white">CONSTRUA SUA FICHA DE FORMA INTELIGENTE</h3>
                    <p class="text-muted-gomos mb-4">
                        Chega de fichas de papel rasgadas ou perdidas. Com o nosso builder dinâmico, você monta sua rotina A/B/C ou PPL arrastando os blocos, e pode duplicar instantaneamente as rotinas mais bem avaliadas da plataforma.
                    </p>
                    <a href="<?= $rootUrl ?>/cadastro" class="btn btn-secondary-gomos text-dark w-100">QUERO FAZER PARTE</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rodapé -->
<footer class="bg-black py-4 border-top border-secondary text-center">
    <div class="container">
        <p class="text-secondary m-0">© <?= date('Y') ?> GOMOS. Feito por amantes do ferro. Todos os direitos reservados.</p>
    </div>
</footer>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
