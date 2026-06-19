<?php 
$pageTitle = "Rankings de Evolução";
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';

$logadoId = \App\Helpers\Session::get('usuario_id');
?>

<div class="dashboard-wrapper">
    <!-- Sidebar Esquerdo -->
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="main-content-gomos bg-dark">
        <div class="container-fluid">
            
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary pb-3">
                <div>
                    <h2 class="text-white m-0">RANKINGS DE <span class="text-orange">SUPERAÇÃO</span></h2>
                    <p class="text-secondary m-0">Confira a classificação dos atletas baseada em consistência e interação.</p>
                </div>
            </div>

            <!-- Como pontuar (Aviso) -->
            <div class="card-gomos p-3 mb-4 bg-dark border-secondary">
                <h5 class="text-white mb-2"><i class="fa-solid fa-circle-info text-orange"></i> Como acumular pontos e subir de nível:</h5>
                <div class="row g-2 text-secondary small text-center">
                    <div class="col-6 col-md-3"><strong>⚡ Realizar Treino:</strong> +10 pts</div>
                    <div class="col-6 col-md-3"><strong>📋 Ficha Copiada:</strong> +10 pts</div>
                    <div class="col-6 col-md-3"><strong>❤️ Curtida Recebida:</strong> +3 pts</div>
                    <div class="col-6 col-md-3"><strong>💬 Deixar Comentário:</strong> +1 pt</div>
                </div>
            </div>

            <!-- Sistema de Abas do Bootstrap -->
            <ul class="nav nav-tabs border-secondary mb-4" id="rankingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="admin-tab-btn active" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral-pane" type="button" role="tab" aria-controls="geral-pane" aria-selected="true"><i class="fa-solid fa-globe"></i> NACIONAIS (BRASIL)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="admin-tab-btn" id="amigos-tab" data-bs-toggle="tab" data-bs-target="#amigos-pane" type="button" role="tab" aria-controls="amigos-pane" aria-selected="false"><i class="fa-solid fa-user-group"></i> ENTRE AMIGOS</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="admin-tab-btn" id="regiao-tab" data-bs-toggle="tab" data-bs-target="#regiao-pane" type="button" role="tab" aria-controls="regiao-pane" aria-selected="false"><i class="fa-solid fa-location-dot"></i> POR REGIÃO</button>
                </li>
            </ul>

            <div class="tab-content" id="rankingTabsContent">
                
                <!-- ABA 1: RANKING GERAL BRASIL -->
                <div class="tab-pane fade show active" id="geral-pane" role="tabpanel" aria-labelledby="geral-tab">
                    <div class="card-gomos p-4">
                        <h4 class="text-white mb-3">TOP 50 ATLETAS — BRASIL</h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-striped align-middle m-0">
                                <thead>
                                    <tr class="text-secondary">
                                        <th scope="col" class="text-center" style="width: 80px;">Posição</th>
                                        <th scope="col">Atleta</th>
                                        <th scope="col">Cidade/Região</th>
                                        <th scope="col" class="text-center">Treinos Postados</th>
                                        <th scope="col" class="text-end">Pontuação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $pos = 1;
                                    foreach ($rankingGeral as $atleta): 
                                        $eh_logado = ($atleta['id'] == $logadoId);
                                    ?>
                                        <tr class="<?= $eh_logado ? 'table-active border-orange' : '' ?>" style="<?= $eh_logado ? 'border-left: 3px solid var(--accent-primary) !important;' : '' ?>">
                                            <td class="text-center fw-bold text-orange" style="font-family: 'Bebas Neue'; font-size: 1.35rem;">
                                                <?php if ($pos === 1): ?><i class="fa-solid fa-crown text-warning"></i> 1º
                                                <?php elseif ($pos === 2): ?>🥈 2º
                                                <?php elseif ($pos === 3): ?>🥉 3º
                                                <?php else: echo $pos . 'º'; endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= $root ?>/assets/img/<?= $atleta['foto_perfil'] ?>" alt="Avatar" class="rounded-circle me-3" style="width: 38px; height: 38px; object-fit: cover; border: 1.5px solid var(--accent-primary);">
                                                    <div>
                                                        <span class="fw-bold d-block"><?= $atleta['nome'] ?> <?= $eh_logado ? '<span class="badge bg-orange text-dark ms-1" style="font-size: 0.65rem;">VOCÊ</span>' : '' ?></span>
                                                        <span class="text-muted small">@<?= $atleta['username'] ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= $atleta['cidade'] ?> - <?= $atleta['estado'] ?></td>
                                            <td class="text-center"><?= $atleta['total_treinos'] ?></td>
                                            <td class="text-end fw-bold text-lime" style="font-family: 'Bebas Neue'; font-size: 1.25rem;"><?= $atleta['pontos_ranking'] ?> PTS</td>
                                        </tr>
                                    <?php 
                                        $pos++;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ABA 2: RANKING ENTRE AMIGOS -->
                <div class="tab-pane fade" id="amigos-pane" role="tabpanel" aria-labelledby="amigos-tab">
                    
                    <?php if ($motivarCampeao): ?>
                        <div class="alert bg-success text-dark border-0 p-4 mb-4 d-flex align-items-center gap-3">
                            <span style="font-size: 2.5rem;"><i class="fa-solid fa-crown"></i></span>
                            <div>
                                <h4 class="m-0 fw-bold">VOCÊ LIDERA O RANKING DOS AMIGOS!</h4>
                                <p class="m-0 small">Nenhum amigo superou seus pontos este mês. Continue esmagando as metas!</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="card-gomos p-4">
                        <h4 class="text-white mb-3">MINHA CLASSIFICAÇÃO ENTRE AMIGOS</h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-striped align-middle m-0">
                                <thead>
                                    <tr class="text-secondary">
                                        <th scope="col" class="text-center" style="width: 80px;">Posição</th>
                                        <th scope="col">Atleta</th>
                                        <th scope="col">Cidade/Região</th>
                                        <th scope="col" class="text-center">Treinos Postados</th>
                                        <th scope="col" class="text-end">Pontuação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $pos = 1;
                                    if (!empty($rankingAmigos)):
                                        foreach ($rankingAmigos as $atleta): 
                                            $eh_logado = ($atleta['id'] == $logadoId);
                                        ?>
                                            <tr class="<?= $eh_logado ? 'table-active' : '' ?>" style="<?= $eh_logado ? 'border-left: 3px solid var(--accent-primary) !important;' : '' ?>">
                                                <td class="text-center fw-bold text-orange" style="font-family: 'Bebas Neue'; font-size: 1.35rem;">
                                                    <?php if ($pos === 1): ?>🥇 1º
                                                    <?php elseif ($pos === 2): ?>🥈 2º
                                                    <?php elseif ($pos === 3): ?>🥉 3º
                                                    <?php else: echo $pos . 'º'; endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= $root ?>/assets/img/<?= $atleta['foto_perfil'] ?>" alt="Avatar" class="rounded-circle me-3" style="width: 38px; height: 38px; object-fit: cover; border: 1.5px solid var(--accent-primary);">
                                                        <div>
                                                            <span class="fw-bold d-block"><?= $atleta['nome'] ?> <?= $eh_logado ? '<span class="badge bg-orange text-dark ms-1" style="font-size: 0.65rem;">VOCÊ</span>' : '' ?></span>
                                                            <span class="text-muted small">@<?= $atleta['username'] ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= $atleta['cidade'] ?> - <?= $atleta['estado'] ?></td>
                                                <td class="text-center"><?= $atleta['total_treinos'] ?></td>
                                                <td class="text-end fw-bold text-lime" style="font-family: 'Bebas Neue'; font-size: 1.25rem;"><?= $atleta['pontos_ranking'] ?> PTS</td>
                                            </tr>
                                        <?php 
                                            $pos++;
                                        endforeach; 
                                    else:
                                    ?>
                                        <tr><td colspan="5" class="text-center text-muted">Você ainda não tem amigos conectados.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ABA 3: RANKING DA REGIÃO -->
                <div class="tab-pane fade" id="regiao-pane" role="tabpanel" aria-labelledby="regiao-tab">
                    
                    <!-- Filtro por Região -->
                    <div class="card-gomos p-4 mb-4">
                        <form action="<?= $rootUrl ?>/ranking" method="GET">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label for="cidade" class="form-label text-muted-gomos">Cidade</label>
                                    <input type="text" name="cidade" id="cidade" class="form-control form-control-gomos" value="<?= $cidade ?>" placeholder="Ex: São Paulo">
                                </div>
                                <div class="col-md-4">
                                    <label for="estado" class="form-label text-muted-gomos">Estado (UF)</label>
                                    <input type="text" name="estado" id="estado" class="form-control form-control-gomos" value="<?= $estado ?>" placeholder="Ex: SP" maxlength="2">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary-gomos w-100 py-3"><i class="fa-solid fa-search"></i> FILTRAR LOCAL</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-gomos p-4">
                        <h4 class="text-white mb-3">TOP ATLETAS — EM <?= strtoupper($cidade) ?> / <?= strtoupper($estado) ?></h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-striped align-middle m-0">
                                <thead>
                                    <tr class="text-secondary">
                                        <th scope="col" class="text-center" style="width: 80px;">Posição</th>
                                        <th scope="col">Atleta</th>
                                        <th scope="col">Cidade/Região</th>
                                        <th scope="col" class="text-center">Treinos Postados</th>
                                        <th scope="col" class="text-end">Pontuação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $pos = 1;
                                    if (!empty($rankingRegiao)):
                                        foreach ($rankingRegiao as $atleta): 
                                            $eh_logado = ($atleta['id'] == $logadoId);
                                        ?>
                                            <tr class="<?= $eh_logado ? 'table-active' : '' ?>" style="<?= $eh_logado ? 'border-left: 3px solid var(--accent-primary) !important;' : '' ?>">
                                                <td class="text-center fw-bold text-orange" style="font-family: 'Bebas Neue'; font-size: 1.35rem;">
                                                    <?php if ($pos === 1): ?>🥇 1º
                                                    <?php elseif ($pos === 2): ?>🥈 2º
                                                    <?php elseif ($pos === 3): ?>🥉 3º
                                                    <?php else: echo $pos . 'º'; endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= $root ?>/assets/img/<?= $atleta['foto_perfil'] ?>" alt="Avatar" class="rounded-circle me-3" style="width: 38px; height: 38px; object-fit: cover; border: 1.5px solid var(--accent-primary);">
                                                        <div>
                                                            <span class="fw-bold d-block"><?= $atleta['nome'] ?> <?= $eh_logado ? '<span class="badge bg-orange text-dark ms-1" style="font-size: 0.65rem;">VOCÊ</span>' : '' ?></span>
                                                            <span class="text-muted small">@<?= $atleta['username'] ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= $atleta['cidade'] ?> - <?= $atleta['estado'] ?></td>
                                                <td class="text-center"><?= $atleta['total_treinos'] ?></td>
                                                <td class="text-end fw-bold text-lime" style="font-family: 'Bebas Neue'; font-size: 1.25rem;"><?= $atleta['pontos_ranking'] ?> PTS</td>
                                            </tr>
                                        <?php 
                                            $pos++;
                                        endforeach; 
                                    else:
                                    ?>
                                        <tr><td colspan="5" class="text-center text-muted">Nenhum atleta cadastrado nessa região até agora.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>
</div>

<!-- Scripts de Bootstrap tabs -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obter aba ativa da URL
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'amigos') {
            const triggerEl = document.querySelector('#amigos-tab');
            if (triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        } else if (tab === 'regiao') {
            const triggerEl = document.querySelector('#regiao-tab');
            if (triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    });
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
