<?php 
$pageTitle = "Minhas Fichas de Treino";
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Sidebar Esquerdo -->
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="main-content-gomos bg-dark">
        <div class="container-fluid">
            
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary pb-3">
                <div>
                    <h2 class="text-white m-0">MINHAS FICHAS DE <span class="text-orange">TREINO</span></h2>
                    <p class="text-secondary m-0">Escolha uma rotina e inicie o rastreamento em tempo real do seu treino.</p>
                </div>
                <a href="<?= $rootUrl ?>/treino/criar" class="btn btn-primary-gomos"><i class="fa-solid fa-plus text-dark"></i> NOVA FICHA</a>
            </div>

            <!-- Listagem de Fichas -->
            <div class="row">
                <?php if (!empty($treinos)): ?>
                    <?php foreach ($treinos as $t): ?>
                        <div class="col-xl-6 col-lg-12 mb-4">
                            <div class="card-gomos h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <!-- Cabeçalho da Ficha -->
                                    <div class="d-flex justify-content-between align-items-start border-bottom border-secondary pb-2 mb-3">
                                        <div>
                                            <h4 class="text-orange fw-bold mb-1"><?= htmlspecialchars($t['titulo']) ?></h4>
                                            <span class="text-secondary small">
                                                Criado em <?= date('d/m/Y', strtotime($t['criado_em'])) ?> 
                                                <?= $t['publico'] ? '• <span class="text-lime">Público</span>' : '• <span class="text-muted">Privado</span>' ?>
                                            </span>
                                        </div>
                                        <span class="badge badge-dificuldade badge-<?= $t['nivel_dificuldade'] ?>">
                                            <?= strtoupper($t['nivel_dificuldade']) ?>
                                        </span>
                                    </div>

                                    <!-- Detalhes Rápidos -->
                                    <div class="d-flex gap-2 mb-3">
                                        <span class="badge bg-dark border border-secondary text-secondary py-1 px-3"><i class="fa-solid fa-dumbbell text-orange me-1"></i> Divisão: <?= htmlspecialchars($t['tipo_treino']) ?></span>
                                        <span class="badge bg-dark border border-secondary text-secondary py-1 px-3"><i class="fa-solid fa-child text-lime me-1"></i> Grupo: <?= htmlspecialchars($t['grupo_muscular']) ?></span>
                                        <span class="badge bg-dark border border-secondary text-secondary py-1 px-3"><i class="fa-solid fa-clock me-1"></i> <?= htmlspecialchars($t['duracao_minutos']) ?> min</span>
                                    </div>

                                    <!-- Exercícios Cadastrados -->
                                    <div class="mb-3">
                                        <h6 class="text-white border-bottom border-secondary pb-1 mb-2 small fw-bold uppercase">
                                            <i class="fa-solid fa-list-check me-2 text-lime"></i> EXERCÍCIOS CADASTRADOS (<?= $t['total_exercicios'] ?>)
                                        </h6>
                                        <?php if (!empty($t['exercicios'])): ?>
                                            <ul class="list-group list-group-flush bg-transparent">
                                                <?php foreach ($t['exercicios'] as $ex): ?>
                                                    <li class="list-group-item bg-transparent text-white-50 border-0 py-1 px-0 d-flex justify-content-between align-items-center small">
                                                        <div>
                                                            <i class="fa-solid fa-circle text-orange me-2" style="font-size: 0.45rem;"></i>
                                                            <strong class="text-white"><?= htmlspecialchars($ex['nome_exercicio']) ?></strong>
                                                        </div>
                                                        <span><?= htmlspecialchars($ex['series']) ?>x<?= htmlspecialchars($ex['repeticoes']) ?> • <span class="text-lime fw-semibold"><?= htmlspecialchars($ex['peso_kg']) ?> kg</span></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-secondary small m-0">Nenhum exercício cadastrado nesta ficha.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Botões de Ação -->
                                <div class="border-top border-secondary pt-3 mt-3">
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <a href="<?= $rootUrl ?>/treino/iniciar/<?= $t['id'] ?>" class="btn btn-primary-gomos w-100 py-2 fw-bold"><i class="fa-solid fa-play text-dark me-2"></i> INICIAR TREINO</a>
                                        </div>
                                        <div class="col-sm-6">
                                            <a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>" class="btn btn-outline-gomos border-secondary text-secondary w-100 py-2"><i class="fa-solid fa-magnifying-glass me-2"></i> VER DETALHES</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Nenhuma Ficha Cadastrada -->
                    <div class="col-12">
                        <div class="card-gomos p-5 text-center">
                            <i class="fa-solid fa-sheet-plastic text-secondary mb-3 animate-bounce" style="font-size: 4rem;"></i>
                            <h4 class="text-white fw-bold">NENHUMA FICHA DE TREINO ENCONTRADA</h4>
                            <p class="text-secondary mx-auto mb-4" style="max-width: 500px;">
                                Você ainda não possui fichas criadas ou copiadas. Crie uma rotina de treino personalizada com os seus exercícios favoritos para começar a treinar e evoluir na rede GOMOS!
                            </p>
                            <a href="<?= $rootUrl ?>/treino/criar" class="btn btn-secondary-gomos text-dark fw-bold px-4 py-2"><i class="fa-solid fa-plus me-2"></i> CRIAR MINHA PRIMEIRA FICHA</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
