<?php 
$pageTitle = "Buscar Treinos";
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
                <!-- Coluna Principal (Pesquisa e Fichas) -->
                <div class="col-lg-8">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary pb-3">
                        <div>
                            <h2 class="text-white m-0">BUSCAR <span class="text-orange">TREINOS</span></h2>
                            <p class="text-secondary m-0">Encontre rotinas de treino e fichas de exercícios de atletas da rede.</p>
                        </div>
                        <a href="<?= $rootUrl ?>/treino/criar" class="btn btn-primary-gomos"><i class="fa-solid fa-plus text-dark"></i> NOVO TREINO</a>
                    </div>

                    <!-- Formulário de Busca -->
                    <div class="card-gomos p-4 mb-4">
                        <form action="<?= $rootUrl ?>/treinos/pesquisar" method="GET" class="m-0">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-secondary" style="border-radius: 6px 0 0 6px;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" name="q" class="form-control form-control-gomos" placeholder="Pesquisar por título, grupo muscular ou divisão (ex: Peito, ABC, Pernas)..." value="<?= htmlspecialchars($query) ?>" style="border-radius: 0; border-left: none;" minlength="2" required>
                                <button type="submit" class="btn btn-primary-gomos px-4" style="border-radius: 0 6px 6px 0;">
                                    <i class="fa-solid fa-search text-dark me-1"></i> BUSCAR
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Resultados da Busca -->
                    <?php if (!empty($query)): ?>
                        <h4 class="text-white border-bottom border-secondary pb-2 mb-4">
                            <i class="fa-solid fa-dumbbell text-orange me-2"></i> 
                            Fichas de Treino Encontradas (<?= count($treinos) ?>)
                        </h4>

                        <?php if (!empty($treinos)): ?>
                            <?php foreach ($treinos as $t): ?>
                                <div class="feed-post-card">
                                    <!-- Cabeçalho do Post -->
                                    <div class="feed-post-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <a href="<?= $rootUrl ?>/perfil/<?= $t['username'] ?>" class="text-decoration-none">
                                                <img src="<?= $root ?>/assets/img/<?= $t['foto_perfil'] ?: 'default_avatar.png' ?>" alt="Avatar" class="feed-post-avatar">
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
                            <!-- Busca sem resultados -->
                            <div class="card-gomos p-5 text-center">
                                <i class="fa-solid fa-user-xmark text-secondary mb-3" style="font-size: 3rem;"></i>
                                <h4 class="text-white">NENHUM TREINO ENCONTRADO</h4>
                                <p class="text-secondary mx-auto mb-4" style="max-width: 450px;">Nenhuma rotina contendo o termo "<strong><?= htmlspecialchars($query) ?></strong>" foi encontrada na rede.</p>
                                <p class="small text-muted-gomos">Dica: Tente buscar por grupos musculares específicos (como Peito, Costas, Quadríceps) ou divisões de treino (ABC, A, B, Full Body).</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Tela de Busca Inicial (Limpa) -->
                        <div class="card-gomos p-5 text-center bg-dark border-secondary">
                            <i class="fa-solid fa-dumbbell text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h4 class="text-white">EXPLORE A REDE GOMOS</h4>
                            <p class="text-secondary mx-auto" style="max-width: 450px;">Use o campo de busca acima para encontrar fichas de treinos específicas por nome, grupos musculares ou divisões corporais.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Coluna Direita (Sugestões e Divisões Populares) -->
                <div class="col-lg-4 d-none d-lg-block">
                    <!-- Divisões Populares -->
                    <div class="card-gomos mb-4">
                        <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-tags text-orange me-2"></i> Sugestões de Busca</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= $rootUrl ?>/treinos/pesquisar?q=Peito" class="badge bg-dark border border-secondary text-secondary py-2 px-3 text-decoration-none hover-orange">Peito e Ombros</a>
                            <a href="<?= $rootUrl ?>/treinos/pesquisar?q=Pernas" class="badge bg-dark border border-secondary text-secondary py-2 px-3 text-decoration-none hover-orange">Pernas</a>
                            <a href="<?= $rootUrl ?>/treinos/pesquisar?q=Costas" class="badge bg-dark border border-secondary text-secondary py-2 px-3 text-decoration-none hover-orange">Costas e Bíceps</a>
                            <a href="<?= $rootUrl ?>/treinos/pesquisar?q=Ombros" class="badge bg-dark border border-secondary text-secondary py-2 px-3 text-decoration-none hover-orange">Ombros</a>
                            <a href="<?= $rootUrl ?>/treinos/pesquisar?q=ABC" class="badge bg-dark border border-secondary text-secondary py-2 px-3 text-decoration-none hover-orange">Divisão ABC</a>
                            <a href="<?= $rootUrl ?>/treinos/pesquisar?q=Full" class="badge bg-dark border border-secondary text-secondary py-2 px-3 text-decoration-none hover-orange">Full Body</a>
                        </div>
                    </div>

                    <!-- Dicas de evolução -->
                    <div class="card-gomos">
                        <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-circle-info text-lime me-2"></i> Dica GOMOS</h4>
                        <p class="text-secondary small m-0">Ao encontrar um treino interessante de outro atleta, clique nele para ver os detalhes. Você pode curtir, comentar para tirar dúvidas ou até mesmo **copiar a ficha** inteira para o seu perfil com apenas um clique!</p>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
