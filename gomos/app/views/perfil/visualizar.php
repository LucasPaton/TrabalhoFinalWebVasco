<?php 
$pageTitle = "Perfil de " . $usuario['nome'];
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
            
            <!-- Perfil Header Card -->
            <div class="card-gomos p-4 mb-4">
                <div class="row align-items-center g-4">
                    <div class="col-md-auto text-center">
                        <img src="<?= $root ?>/assets/img/<?= $usuario['foto_perfil'] ?>" alt="Avatar" class="rounded-circle border border-3 border-orange" style="width: 130px; height: 130px; object-fit: cover;">
                    </div>
                    <div class="col-md">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                            <div>
                                <h2 class="text-white m-0 fw-bold"><?= $usuario['nome'] ?></h2>
                                <span class="text-orange">@<?= $usuario['username'] ?></span>
                            </div>
                            
                            <!-- Botões de Ação de Amizade -->
                            <div>
                                <?php if ($status_amizade === 'nenhum'): ?>
                                    <form action="<?= $rootUrl ?>/amigos/adicionar/<?= $usuario['id'] ?>" method="POST" class="m-0">
                                        <button type="submit" class="btn btn-primary-gomos btn-sm"><i class="fa-solid fa-user-plus"></i> ADICIONAR AMIGO</button>
                                    </form>
                                <?php elseif ($status_amizade === 'pendente_enviado'): ?>
                                    <button class="btn btn-outline-secondary btn-sm" disabled><i class="fa-solid fa-clock"></i> SOLICITAÇÃO PENDENTE</button>
                                <?php elseif ($status_amizade === 'pendente_recebido'): ?>
                                    <div class="d-flex gap-2">
                                        <form action="<?= $rootUrl ?>/amigos/aceitar/<?= $usuario['id'] ?>" method="POST" class="m-0">
                                            <button type="submit" class="btn btn-secondary-gomos btn-sm text-dark"><i class="fa-solid fa-check"></i> ACEITAR</button>
                                        </form>
                                        <form action="<?= $rootUrl ?>/amigos/recusar/<?= $usuario['id'] ?>" method="POST" class="m-0">
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-xmark"></i> RECUSAR</button>
                                        </form>
                                    </div>
                                <?php elseif ($status_amizade === 'aceita'): ?>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge bg-dark border border-secondary text-secondary py-2 px-3"><i class="fa-solid fa-user-check text-lime"></i> AMIGOS</span>
                                        <form action="<?= $rootUrl ?>/amigos/recusar/<?= $usuario['id'] ?>" method="POST" onsubmit="return confirm('Deseja realmente remover este amigo?');" class="m-0">
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-user-minus"></i> REMOVER</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="text-white-50 small mb-3"><?= $usuario['bio'] ?: 'Este atleta ainda não cadastrou uma bio.' ?></p>
                        
                        <div class="d-flex flex-wrap gap-2 align-items-center text-secondary small">
                            <span><i class="fa-solid fa-location-dot text-orange"></i> <?= $usuario['cidade'] ?> - <?= $usuario['estado'] ?></span>
                            <span class="mx-2">•</span>
                            <span><i class="fa-solid fa-hotel text-lime"></i> <?= $usuario['nome_academia'] ?: 'Sem Academia vinculada' ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Block -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card-gomos text-center p-3 m-0">
                        <h2 class="text-orange m-0" style="font-family: 'Bebas Neue';"><?= $usuario['total_treinos'] ?></h2>
                        <span class="text-secondary small fw-bold">TREINOS CRIADOS</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card-gomos text-center p-3 m-0">
                        <h2 class="text-lime m-0" style="font-family: 'Bebas Neue';"><?= $usuario['total_curtidas'] ?></h2>
                        <span class="text-secondary small fw-bold">CURTIDAS RECEBIDAS</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card-gomos text-center p-3 m-0">
                        <h2 class="text-orange m-0" style="font-family: 'Bebas Neue';"><?= $total_amigos ?></h2>
                        <span class="text-secondary small fw-bold">AMIGOS NA REDE</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card-gomos text-center p-3 m-0">
                        <h2 class="text-lime m-0" style="font-family: 'Bebas Neue';">#<?= $posicao_ranking ?></h2>
                        <span class="text-secondary small fw-bold">POSIÇÃO NACIONAL</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Coluna Esquerda (Nível + Conquistas) -->
                <div class="col-lg-5">
                    
                    <!-- Nível Fitness Card -->
                    <div class="card-gomos mb-4">
                        <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-chart-line text-orange me-2"></i> EVOLUÇÃO FITNESS</h4>
                        
                        <?php 
                        $nivel = $usuario['nivel_fitness'];
                        $progresso = 33;
                        if ($nivel === 'intermediario') $progresso = 66;
                        if ($nivel === 'avancado') $progresso = 100;
                        ?>
                        
                        <div class="d-flex justify-content-between text-secondary small mb-2 fw-semibold">
                            <span>Iniciante</span>
                            <span class="<?= $nivel === 'intermediario' ? 'text-lime' : '' ?>">Intermediário</span>
                            <span class="<?= $nivel === 'avancado' ? 'text-orange' : '' ?>">Avançado / Elite</span>
                        </div>
                        <div class="progress progress-gomos mb-3">
                            <div class="progress-bar progress-bar-gomos" role="progressbar" style="width: <?= $progresso ?>%;" aria-valuenow="<?= $progresso ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Conquistas (Badges) -->
                    <div class="card-gomos">
                        <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-award text-lime me-2"></i> CONQUISTAS (<?= count($conquistas) ?>)</h4>
                        <?php if (!empty($conquistas)): ?>
                            <div class="row g-3">
                                <?php foreach ($conquistas as $conq): ?>
                                    <div class="col-4">
                                        <div class="badge-grid-item" title="<?= $conq['descricao'] ?>">
                                            <img src="<?= $root ?>/assets/img/<?= $conq['icone'] ?>" alt="Badge Icon" class="badge-icon-img img-fluid">
                                            <h6 class="m-0 text-white text-truncate small"><?= $conq['nome'] ?></h6>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-secondary small m-0 text-center py-3">Este atleta ainda não desbloqueou conquistas.</p>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Coluna Direita (Histórico de Treinos Públicos) -->
                <div class="col-lg-7">
                    
                    <!-- Histórico de Treinos -->
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-secondary pb-2">
                        <h4 class="text-white m-0"><i class="fa-solid fa-clock-rotate-left text-orange me-2"></i> TREINOS PÚBLICOS</h4>
                        <span class="text-secondary small">Total: <?= count($treinos) ?> treinos</span>
                    </div>

                    <?php if (!empty($treinos)): ?>
                        <?php foreach ($treinos as $t): ?>
                            <div class="card-gomos mb-3 p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="text-orange m-0"><a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>" class="text-orange text-decoration-none fw-bold"><?= $t['titulo'] ?></a></h5>
                                        <span class="text-muted-gomos small"><?= date('d/m/Y', strtotime($t['criado_em'])) ?> • Divisão: <?= $t['tipo_treino'] ?></span>
                                    </div>
                                </div>
                                <p class="text-white-50 small mb-2 text-truncate"><?= $t['descricao'] ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary" style="border-style: dashed !important;">
                                    <div class="small text-secondary">
                                        <span><i class="fa-regular fa-heart"></i> <?= $t['total_curtidas'] ?></span>
                                        <span class="ms-3"><i class="fa-regular fa-comment"></i> <?= $t['total_comentarios'] ?></span>
                                    </div>
                                    <a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>" class="btn btn-secondary-gomos btn-sm text-dark px-3 py-1" style="font-size: 0.75rem;">ABRIR FICHA</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="card-gomos text-center p-4">
                            <p class="text-secondary m-0">Nenhum treino público disponível.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
