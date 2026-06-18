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

                <!-- Coluna Direita (Abas de Treinos) -->
                <div class="col-lg-7">
                    <style>
                    .nav-pills .nav-link.active {
                        background-color: var(--accent-primary) !important;
                        color: #000 !important;
                    }
                    .nav-pills .nav-link {
                        border-radius: 4px;
                        transition: all 0.2s ease;
                    }
                    .nav-pills .nav-link:hover:not(.active) {
                        background-color: rgba(255, 107, 0, 0.1);
                        color: var(--accent-primary) !important;
                    }
                    </style>

                    <!-- Tabs de Navegação -->
                    <ul class="nav nav-pills nav-fill mb-4 border border-secondary p-1 rounded bg-black" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-white fw-bold py-2 px-3 border-0 bg-transparent" id="realizados-tab" data-bs-toggle="pill" data-bs-target="#realizados-content" type="button" role="tab" aria-controls="realizados-content" aria-selected="true">
                                <i class="fa-solid fa-clock-rotate-left me-2"></i> TREINOS REALIZADOS (<?= count($realizados) ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-white fw-bold py-2 px-3 border-0 bg-transparent" id="fichas-tab" data-bs-toggle="pill" data-bs-target="#fichas-content" type="button" role="tab" aria-controls="fichas-content" aria-selected="false">
                                <i class="fa-solid fa-file-lines me-2"></i> FICHAS DE TREINO (<?= count($fichas) ?>)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabsContent">
                        <!-- Aba de Treinos Realizados (Histórico) -->
                        <div class="tab-pane fade show active" id="realizados-content" role="tabpanel" aria-labelledby="realizados-tab">
                            <?php if (!empty($realizados)): ?>
                                <?php foreach ($realizados as $t): ?>
                                    <div class="card-gomos mb-3 p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 class="text-orange m-0"><a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>" class="text-orange text-decoration-none fw-bold"><?= $t['titulo'] ?></a></h5>
                                                <span class="text-muted-gomos small"><?= date('d/m/Y H:i', strtotime($t['criado_em'])) ?> • Divisão: <?= $t['tipo_treino'] ?> • <i class="fa-solid fa-clock"></i> <?= $t['duracao_minutos'] ?> min</span>
                                            </div>
                                        </div>
                                        <p class="text-white-50 small mb-2"><?= $t['descricao'] ?></p>
                                        
                                        <?php if (!empty($t['foto'])): ?>
                                            <div class="mb-3 text-center">
                                                <img src="<?= $root ?>/assets/img/<?= $t['foto'] ?>" alt="Foto do Treino" class="img-fluid rounded" style="max-height: 250px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
                                            </div>
                                        <?php endif; ?>

                                        <!-- Preview de exercícios realizados -->
                                        <div class="feed-post-exercises bg-dark p-2 rounded mb-2" style="font-size: 0.8rem;">
                                            <span class="text-lime fw-bold d-block mb-1 small uppercase"><i class="fa-solid fa-list-check me-1"></i> Séries Efetivas:</span>
                                            <table class="table table-dark table-sm m-0 table-borderless" style="font-size: 0.75rem;">
                                                <tbody>
                                                    <?php foreach ($t['exercicios_preview'] as $ex): ?>
                                                        <tr>
                                                            <td class="fw-bold"><?= $ex['nome_exercicio'] ?></td>
                                                            <td class="text-center"><?= $ex['series'] ?> séries</td>
                                                            <td class="text-end text-lime fw-semibold"><?= $ex['peso_kg'] ?> kg x <?= $ex['repeticoes'] ?> reps</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            <?php if ($t['total_exercicios'] > 3): ?>
                                                <div class="text-center mt-2 border-top border-secondary pt-1 small">
                                                    <a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>" class="text-lime text-decoration-none fw-semibold">Ver todos os <?= $t['total_exercicios'] ?> exercícios...</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-secondary" style="border-style: dashed !important;">
                                            <div class="small text-secondary">
                                                <span><i class="fa-solid fa-heart text-orange"></i> <?= $t['total_curtidas'] ?></span>
                                                <span class="ms-3"><i class="fa-solid fa-comment text-lime"></i> <?= $t['total_comentarios'] ?></span>
                                            </div>
                                            <a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>" class="btn btn-secondary-gomos btn-sm text-dark px-3 py-1" style="font-size: 0.75rem;">VER DETALHES</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="card-gomos text-center p-4">
                                    <p class="text-secondary m-0">Nenhum treino realizado por este atleta ainda.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Aba de Fichas de Treino -->
                        <div class="tab-pane fade" id="fichas-content" role="tabpanel" aria-labelledby="fichas-tab">
                            <?php if (!empty($fichas)): ?>
                                <?php foreach ($fichas as $t): ?>
                                    <div class="card-gomos mb-3 p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 class="text-orange m-0"><a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>" class="text-orange text-decoration-none fw-bold"><?= $t['titulo'] ?></a></h5>
                                                <span class="text-muted-gomos small">Ficha de Treino • Divisão: <?= $t['tipo_treino'] ?> • Grupo: <?= $t['grupo_muscular'] ?></span>
                                            </div>
                                        </div>
                                        <p class="text-white-50 small mb-2"><?= $t['descricao'] ?></p>

                                        <!-- Resumo Exercícios da Ficha -->
                                        <div class="bg-dark p-2 rounded mb-3 text-secondary" style="font-size: 0.8rem;">
                                            <span class="text-white fw-bold d-block mb-1 small uppercase"><i class="fa-solid fa-dumbbell text-orange me-1"></i> Estrutura do Treino (<?= $t['total_exercicios'] ?> exercícios):</span>
                                            <ul class="m-0 ps-3 small text-white-50">
                                                <?php foreach (array_slice($t['exercicios'], 0, 3) as $ex): ?>
                                                    <li><strong><?= $ex['nome_exercicio'] ?></strong>: <?= $ex['series'] ?> séries x <?= $ex['repeticoes'] ?> reps (<?= $ex['peso_kg'] ?> kg)</li>
                                                <?php endforeach; ?>
                                                <?php if ($t['total_exercicios'] > 3): ?>
                                                    <li class="list-unstyled text-lime mt-1 fw-semibold">E mais <?= $t['total_exercicios'] - 3 ?> exercícios...</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary" style="border-style: dashed !important;">
                                            <a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>" class="btn btn-outline-gomos btn-sm border-secondary text-secondary px-3 py-1" style="font-size: 0.75rem;">VER DETALHES</a>
                                            <form action="<?= $rootUrl ?>/treino/copiar/<?= $t['id'] ?>" method="POST" class="m-0">
                                                <button type="submit" class="btn btn-primary-gomos btn-sm px-4 py-1" style="font-size: 0.75rem;"><i class="fa-solid fa-copy me-1 text-dark"></i> COPIAR FICHA</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="card-gomos text-center p-4">
                                    <p class="text-secondary m-0">Nenhuma ficha pública de treino disponível.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
