<?php 
$pageTitle = $treino['titulo'];
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
            
            <!-- Voltar link -->
            <div class="mb-3">
                <a href="<?= $rootUrl ?>/feed" class="text-secondary text-decoration-none small"><i class="fa-solid fa-chevron-left me-1"></i> Voltar para o Feed</a>
            </div>

            <!-- Cabeçalho do Treino -->
            <div class="card-gomos p-4 mb-4">
                <div class="row align-items-center g-3">
                    <div class="col-md">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= $root ?>/assets/img/<?= $treino['foto_perfil'] ?>" alt="Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover; border: 1.5px solid var(--accent-primary);">
                            <div>
                                <h6 class="m-0 text-white fw-bold"><a href="<?= $rootUrl ?>/perfil/<?= $treino['username'] ?>" class="text-white text-decoration-none">@<?= $treino['username'] ?></a></h6>
                                <span class="text-secondary small">Postado em <?= date('d/m/Y H:i', strtotime($treino['criado_em'])) ?> • <i class="fa-solid fa-eye text-muted-gomos"></i> <?= $treino['total_visualizacoes'] ?> visualizações</span>
                            </div>
                        </div>
                        <h2 class="text-white fw-bold mb-2"><?= $treino['titulo'] ?></h2>
                        <p class="text-white-50 mb-3"><?= $treino['descricao'] ?></p>

                        <!-- Informações em Cards menores -->
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-dark border border-secondary text-secondary py-2 px-3"><i class="fa-solid fa-dumbbell me-1 text-orange"></i> Divisão: <?= $treino['tipo_treino'] ?></span>
                            <span class="badge bg-dark border border-secondary text-secondary py-2 px-3"><i class="fa-solid fa-child me-1 text-lime"></i> Grupo: <?= $treino['grupo_muscular'] ?></span>
                            <span class="badge bg-dark border border-secondary text-secondary py-2 px-3"><i class="fa-solid fa-clock me-1"></i> Duração: <?= $treino['duracao_minutos'] ?> min</span>
                            <span class="badge badge-dificuldade badge-<?= $treino['nivel_dificuldade'] ?>">Dificuldade: <?= strtoupper($treino['nivel_dificuldade']) ?></span>
                        </div>
                    </div>
                    
                    <!-- Ações de Clonagem / Comparação -->
                    <div class="col-md-auto text-md-end d-flex flex-column gap-2">
                        <?php if ($treino['usuario_id'] != \App\Helpers\Session::get('usuario_id')): ?>
                            <!-- Clonar Ficha -->
                            <form action="<?= $rootUrl ?>/treino/copiar/<?= $treino['id'] ?>" method="POST" class="m-0">
                                <button type="submit" class="btn btn-secondary-gomos text-dark w-100"><i class="fa-solid fa-copy"></i> COPIAR PARA MINHAS FICHAS</button>
                            </form>
                        <?php endif; ?>
                        
                        <!-- Comparar -->
                        <a href="<?= $rootUrl ?>/treino/comparar?t1=<?= $treino['id'] ?>" class="btn btn-outline-gomos border-secondary text-secondary"><i class="fa-solid fa-scale-balanced"></i> COMPARAR COM OUTRO TREINO</a>
                    </div>
                </div>
            </div>

            <!-- Tabela de Exercícios -->
            <div class="card-gomos mb-4">
                <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-list-check text-lime me-2"></i> LISTA COMPLETA DE EXERCÍCIOS</h4>
                <div class="table-responsive">
                    <table class="table table-dark table-hover table-striped align-middle m-0">
                        <thead>
                            <tr class="text-secondary">
                                <th scope="col" style="width: 50px;" class="text-center">Ordem</th>
                                <th scope="col">Exercício</th>
                                <th scope="col" class="text-center">Séries</th>
                                <th scope="col" class="text-center">Repetições</th>
                                <th scope="col" class="text-center">Carga (kg)</th>
                                <th scope="col" class="text-center">Descanso</th>
                                <th scope="col">Instruções / Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $idx = 1;
                            foreach ($exercicios as $ex): 
                            ?>
                                <tr>
                                    <td class="text-center fw-bold text-orange" style="font-family: 'Bebas Neue'; font-size: 1.25rem;"><?= $idx++ ?></td>
                                    <td class="fw-bold"><?= $ex['nome_exercicio'] ?></td>
                                    <td class="text-center"><?= $ex['series'] ?></td>
                                    <td class="text-center"><?= $ex['repeticoes'] ?></td>
                                    <td class="text-center text-lime fw-semibold"><?= $ex['peso_kg'] ?> kg</td>
                                    <td class="text-center"><?= $ex['descanso_segundos'] ?> segundos</td>
                                    <td class="text-white-50 small"><?= $ex['observacoes'] ?: '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Curtidas + Comentários -->
            <div class="row" id="comentarios">
                <!-- Seção de Comentários -->
                <div class="col-lg-8">
                    <div class="card-gomos mb-4">
                        <div class="d-flex align-items-center justify-content-between border-bottom border-secondary pb-2 mb-4">
                            <h4 class="text-white m-0"><i class="fa-regular fa-comments text-orange me-2"></i> DISCUSSÃO E FEEDBACK</h4>
                            <span class="badge bg-dark border border-secondary text-secondary py-1 px-3"><?= count($comentarios) ?> Comentários</span>
                        </div>

                        <!-- Formulário de Comentário -->
                        <form action="<?= $rootUrl ?>/treino/comentar/<?= $treino['id'] ?>" method="POST" class="mb-4">
                            <div class="row g-2">
                                <div class="col">
                                    <input type="text" name="texto" class="form-control form-control-gomos" placeholder="Deixe um comentário construtivo sobre o treino ou carga..." required>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary-gomos px-4"><i class="fa-solid fa-paper-plane text-dark"></i> ENVIAR</button>
                                </div>
                            </div>
                        </form>

                        <!-- Fluxo de Comentários -->
                        <div class="comments-flow">
                            <?php if (!empty($comentarios)): ?>
                                <?php foreach ($comentarios as $c): ?>
                                    <div class="d-flex py-3 border-bottom border-secondary" style="border-style: dashed !important;">
                                        <img src="<?= $root ?>/assets/img/<?= $c['foto_perfil'] ?>" alt="Avatar" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="m-0 text-white fw-bold"><?= $c['nome_usuario'] ?> <span class="text-secondary small fw-normal ms-1">@<?= $c['username'] ?></span></h6>
                                                <small class="text-secondary" style="font-size: 0.75rem;"><?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?></small>
                                            </div>
                                            <p class="text-white-50 small m-0"><?= $c['texto'] ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-secondary small m-0 text-center py-3">Seja o primeiro a deixar um feedback para este atleta!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Info curtidas sidebar -->
                <div class="col-lg-4">
                    <div class="card-gomos text-center p-4">
                        <h4 class="text-white mb-3">APROVAÇÃO DO TREINO</h4>
                        <button class="btn btn-primary-gomos w-100 py-3 btn-curtir-ajax <?= $treino['curtiu'] ? 'active text-orange' : '' ?>" data-id="<?= $treino['id'] ?>">
                            <i class="<?= $treino['curtiu'] ? 'fa-solid' : 'fa-regular' ?> fa-heart me-2"></i> ❤️ APOIAR TREINO (CURTIR)
                        </button>
                        <div class="mt-3 text-secondary">
                            <span>Total de curtidas recebidas: <strong class="text-lime curtidas-count"><?= $treino['total_curtidas'] ?></strong></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
