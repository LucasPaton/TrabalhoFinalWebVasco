<?php 
$pageTitle = "Feed de Treinos";
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
            
            <div class="row">
                <!-- Coluna do Feed (Central) -->
                <div class="col-lg-8">
                    <!-- Banner de Saudação -->
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary pb-3">
                        <div>
                            <h2 class="text-white m-0">FEED DE <span class="text-orange">TREINOS</span></h2>
                            <p class="text-secondary m-0">Confira a rotina e cargas de seus amigos.</p>
                        </div>
                        <a href="<?= $rootUrl ?>/treinos/fichas" class="btn btn-primary-gomos"><i class="fa-solid fa-circle-play me-1"></i> INICIAR TREINO</a>
                    </div>

                    <!-- Listagem de Posts -->
                    <?php if (!empty($treinos)): ?>
                        <?php foreach ($treinos as $t): ?>
                            <div class="feed-post-card">
                                <!-- Cabeçalho do Post -->
                                <div class="feed-post-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <a href="<?= $rootUrl ?>/perfil/<?= $t['username'] ?>" class="text-decoration-none">
                                            <img src="<?= $root ?>/assets/img/<?= $t['foto_perfil'] ?>" alt="Avatar" class="feed-post-avatar">
                                        </a>
                                        <div class="feed-post-meta">
                                            <h6 class="m-0"><a href="<?= $rootUrl ?>/perfil/<?= $t['username'] ?>" class="text-white text-decoration-none fw-bold"><?= $t['nome_usuario'] ?></a></h6>
                                            <span>@<?= $t['username'] ?> • <?= date('d/m/Y H:i', strtotime($t['criado_em'])) ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge badge-dificuldade badge-<?= $t['nivel_dificuldade'] ?>">
                                            <?= strtoupper($t['nivel_dificuldade']) ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Corpo do Post -->
                                <div class="feed-post-body">
                                    <h4 class="text-orange mb-2"><?= $t['titulo'] ?></h4>
                                    <p class="text-white-50 mb-3"><?= $t['descricao'] ?></p>

                                    <?php if (!empty($t['foto'])): ?>
                                        <div class="mb-3 text-center">
                                            <img src="<?= $root ?>/assets/img/<?= $t['foto'] ?>" alt="Foto do Treino" class="img-fluid rounded" style="max-height: 350px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
                                        </div>
                                    <?php endif; ?>

                                    <!-- Tags do Treino -->
                                    <div class="d-flex gap-2 mb-3">
                                        <span class="badge bg-dark border border-secondary text-secondary py-2 px-3"><i class="fa-solid fa-dumbbell me-1 text-orange"></i> Divisão: <?= $t['tipo_treino'] ?></span>
                                        <span class="badge bg-dark border border-secondary text-secondary py-2 px-3"><i class="fa-solid fa-child me-1 text-lime"></i> Grupo: <?= $t['grupo_muscular'] ?></span>
                                        <span class="badge bg-dark border border-secondary text-secondary py-2 px-3"><i class="fa-solid fa-clock me-1"></i> <?= $t['duracao_minutos'] ?> min</span>
                                    </div>

                                    <!-- Preview dos Exercícios (Top 3) -->
                                    <div class="feed-post-exercises">
                                        <h6 class="text-white border-bottom border-secondary pb-2 mb-3 small fw-bold uppercase"><i class="fa-solid fa-list-check me-2 text-lime"></i> EXERCÍCIOS (Preview)</h6>
                                        <div class="table-responsive">
                                            <table class="table table-dark table-sm m-0 table-borderless">
                                                <thead>
                                                    <tr class="text-muted-gomos" style="font-size: 0.75rem;">
                                                        <th>Exercício</th>
                                                        <th class="text-center">Séries</th>
                                                        <th class="text-center">Reps</th>
                                                        <th class="text-end">Peso</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($t['exercicios_preview'] as $ex): ?>
                                                        <tr style="font-size: 0.85rem;">
                                                            <td class="fw-bold"><?= $ex['nome_exercicio'] ?></td>
                                                            <td class="text-center"><?= $ex['series'] ?></td>
                                                            <td class="text-center"><?= $ex['repeticoes'] ?></td>
                                                            <td class="text-end text-lime fw-semibold"><?= $ex['peso_kg'] ?> kg</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if ($t['total_exercicios'] > 3): ?>
                                            <div class="text-center mt-3 border-top border-secondary pt-2">
                                                <a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>" class="text-lime text-decoration-none small fw-semibold">Ver mais <?= $t['total_exercicios'] - 3 ?> exercícios...</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Ações do Post -->
                                <div class="feed-post-actions">
                                    <button class="feed-post-action-btn btn-curtir-ajax <?= $t['curtiu'] ? 'active text-orange' : '' ?>" data-id="<?= $t['id'] ?>">
                                        <i class="<?= $t['curtiu'] ? 'fa-solid' : 'fa-regular' ?> fa-heart me-1"></i> <span class="curtidas-count"><?= $t['total_curtidas'] ?></span>
                                    </button>
                                    
                                    <a href="<?= $rootUrl ?>/treino/<?= $t['id'] ?>#comentarios" class="feed-post-action-btn text-decoration-none">
                                        <i class="fa-regular fa-comment me-1"></i> <span><?= $t['total_comentarios'] ?></span>
                                    </a>

                                    <?php if ($t['usuario_id'] != \App\Helpers\Session::get('usuario_id')): ?>
                                        <form action="<?= $rootUrl ?>/treino/copiar/<?= $t['id'] ?>" method="POST" class="m-0 d-inline">
                                            <button type="submit" class="feed-post-action-btn text-lime border-0 bg-transparent">
                                                <i class="fa-solid fa-copy me-1"></i> Copiar Ficha
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <button class="feed-post-action-btn" onclick="navigator.clipboard.writeText('<?= $_SERVER['HTTP_HOST'] . $rootUrl ?>/treino/<?= $t['id'] ?>'); alert('Link do treino copiado para a área de transferência!');">
                                        <i class="fa-regular fa-share-from-square"></i> Compartilhar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Feed Vazio -->
                        <div class="card-gomos p-5 text-center">
                            <i class="fa-solid fa-dumbbell text-secondary mb-3" style="font-size: 3rem;"></i>
                            <h4 class="text-white">NENHUM TREINO ENCONTRADO</h4>
                            <p class="text-secondary mx-auto mb-4" style="max-width: 450px;">Adicione alguns amigos ou comece a montar e postar seus próprios treinos para povoar o feed!</p>
                            <a href="<?= $rootUrl ?>/treino/criar" class="btn btn-secondary-gomos text-dark"><i class="fa-solid fa-plus"></i> MONTAR MEU PRIMEIRO TREINO</a>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Sidebar Direita (Desktop) -->
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="sidebar-right">
                        
                        <!-- Mini Ranking Amigos -->
                        <div class="card-gomos mb-4">
                            <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-trophy text-orange me-2"></i> TOP AMIGOS</h4>
                            <div class="ranking-preview-list">
                                <?php 
                                $pos = 1;
                                if (!empty($rankingAmigos)):
                                    foreach ($rankingAmigos as $amigo): 
                                ?>
                                    <div class="ranking-list-item d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="ranking-position text-secondary" style="font-family: 'Bebas Neue';">
                                                <?php if ($pos === 1): ?>🥇<?php elseif ($pos === 2): ?>🥈<?php elseif ($pos === 3): ?>🥉<?php else: echo $pos; endif; ?>
                                            </span>
                                            <a href="<?= $rootUrl ?>/perfil/<?= $amigo['username'] ?>">
                                                <img src="<?= $root ?>/assets/img/<?= $amigo['foto_perfil'] ?>" alt="Avatar" class="ranking-avatar ms-2 me-3 rounded-circle">
                                            </a>
                                            <div>
                                                <h6 class="m-0 text-white"><a href="<?= $rootUrl ?>/perfil/<?= $amigo['username'] ?>" class="text-decoration-none text-white fw-bold"><?= $amigo['nome'] ?></a></h6>
                                                <small class="text-muted-gomos">@<?= $amigo['username'] ?></small>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-lime" style="font-family: 'Bebas Neue';"><?= $amigo['pontos_ranking'] ?> PTS</span>
                                    </div>
                                <?php 
                                    $pos++;
                                    endforeach; 
                                else:
                                ?>
                                    <p class="text-muted small m-0 py-2">Sem dados de amigos.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Sugestões de Amigos -->
                        <div class="card-gomos">
                            <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-user-plus text-lime me-2"></i> DESCOBRIR ATLETAS</h4>
                            <ul class="list-unstyled m-0">
                                <?php if (!empty($sugestoesAmigos)): ?>
                                    <?php foreach ($sugestoesAmigos as $sugestao): ?>
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom border-secondary" style="border-style: dashed !important;">
                                            <div class="d-flex align-items-center">
                                                <a href="<?= $rootUrl ?>/perfil/<?= $sugestao['username'] ?>">
                                                    <img src="<?= $root ?>/assets/img/<?= $sugestao['foto_perfil'] ?>" alt="Avatar" class="rounded-circle me-3" style="width: 36px; height: 36px; object-fit: cover; border: 1.5px solid var(--accent-primary)">
                                                </a>
                                                <div>
                                                    <h6 class="m-0"><a href="<?= $rootUrl ?>/perfil/<?= $sugestao['username'] ?>" class="text-decoration-none text-white fw-bold"><?= $sugestao['nome'] ?></a></h6>
                                                    <small class="text-secondary" style="font-size: 0.75rem;"><?= $sugestao['cidade'] ?> - <?= $sugestao['estado'] ?></small>
                                                </div>
                                            </div>
                                            <!-- Form para adicionar -->
                                            <form action="<?= $rootUrl ?>/amigos/adicionar/<?= $sugestao['id'] ?>" method="POST" class="m-0">
                                                <button type="submit" class="btn btn-secondary-gomos btn-sm text-dark px-2 py-1" style="font-size: 0.75rem;"><i class="fa-solid fa-plus"></i> ADD</button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted small m-0">Você já está conectado com todos na sua região!</p>
                                <?php endif; ?>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
