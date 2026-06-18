<?php 
$pageTitle = "Buscar Atletas";
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';

$usuarioLogadoId = \App\Helpers\Session::get('usuario_id');
?>

<div class="dashboard-wrapper">
    <!-- Sidebar Esquerdo -->
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="main-content-gomos bg-dark">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary pb-3">
                        <div>
                            <h2 class="text-white m-0">BUSCAR <span class="text-orange">ATLETAS</span></h2>
                            <p class="text-secondary m-0">Pesquise por nome ou username para se conectar com outros atletas da rede GOMOS.</p>
                        </div>
                    </div>

                    <!-- Caixa de Pesquisa Central -->
                    <div class="card-gomos p-4 mb-4">
                        <form action="<?= $rootUrl ?>/usuarios/pesquisar" method="GET" class="m-0">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-secondary" style="border-radius: 6px 0 0 6px;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" name="q" id="pesquisa-atleta-input" class="form-control form-control-gomos" placeholder="Digite o nome ou username..." value="<?= htmlspecialchars($query) ?>" style="border-radius: 0; border-left: none;" minlength="2" required>
                                <button type="submit" class="btn btn-primary-gomos px-4" style="border-radius: 0 6px 6px 0;">
                                    <i class="fa-solid fa-search text-dark me-1"></i> BUSCAR
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Seção de Resultados da Busca -->
                    <?php if (!empty($query)): ?>
                        <div class="card-gomos p-4 mb-4">
                            <h4 class="text-white border-bottom border-secondary pb-2 mb-4">
                                <i class="fa-solid fa-list-check text-orange me-2"></i> 
                                Resultados para "<?= htmlspecialchars($query) ?>" (<?= count($resultados) ?>)
                            </h4>

                            <?php if (!empty($resultados)): ?>
                                <div class="row g-3">
                                    <?php foreach ($resultados as $atleta): ?>
                                        <div class="col-md-6 col-xxl-4">
                                            <div class="card-gomos p-3 m-0 d-flex flex-column justify-content-between h-100 border border-secondary">
                                                <div class="d-flex align-items-start gap-3">
                                                    <a href="<?= $rootUrl ?>/perfil/<?= $atleta['username'] ?>">
                                                        <img src="<?= $root ?>/assets/img/<?= $atleta['foto_perfil'] ?: 'default_avatar.png' ?>" alt="Avatar" class="rounded-circle" style="width: 55px; height: 55px; object-fit: cover; border: 1.5px solid var(--accent-primary);">
                                                    </a>
                                                    <div class="flex-grow-1">
                                                        <h5 class="text-white m-0 fw-bold">
                                                            <a href="<?= $rootUrl ?>/perfil/<?= $atleta['username'] ?>" class="text-decoration-none text-white hover-orange">
                                                                <?= $atleta['nome'] ?>
                                                            </a>
                                                        </h5>
                                                        <span class="text-muted-gomos small d-block">@<?= $atleta['username'] ?></span>
                                                        
                                                        <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                                            <span class="badge badge-dificuldade badge-iniciante" style="font-size: 0.65rem;">
                                                                <?= strtoupper($atleta['nivel_fitness']) ?>
                                                            </span>
                                                            <span class="badge bg-secondary text-white-50" style="font-size: 0.65rem;">
                                                                <?= $atleta['pontos_ranking'] ?> PTS
                                                            </span>
                                                        </div>

                                                        <div class="text-secondary small mt-2">
                                                            <i class="fa-solid fa-location-dot me-1 text-orange"></i> <?= $atleta['cidade'] ?>/<?= $atleta['estado'] ?>
                                                        </div>
                                                        <?php if (!empty($atleta['academia_nome'])): ?>
                                                            <div class="text-secondary small mt-1">
                                                                <i class="fa-solid fa-dumbbell me-1 text-lime"></i> <?= $atleta['academia_nome'] ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="border-top border-secondary mt-3 pt-3 d-flex justify-content-end">
                                                    <?php if ($atleta['status_amizade'] === 'nenhum'): ?>
                                                        <form action="<?= $rootUrl ?>/amigos/adicionar/<?= $atleta['id'] ?>" method="POST" class="m-0 w-100">
                                                            <button type="submit" class="btn btn-secondary-gomos text-dark btn-sm w-100 py-2 fw-bold">
                                                                <i class="fa-solid fa-user-plus text-dark me-1"></i> ADICIONAR ATLETA
                                                            </button>
                                                        </form>
                                                    <?php elseif ($atleta['status_amizade'] === 'pendente_enviado'): ?>
                                                        <button class="btn btn-outline-secondary btn-sm w-100 py-2 border-secondary text-muted" disabled>
                                                            <i class="fa-solid fa-clock me-1"></i> SOLICITAÇÃO PENDENTE
                                                        </button>
                                                    <?php elseif ($atleta['status_amizade'] === 'pendente_recebido'): ?>
                                                        <div class="d-flex gap-2 w-100">
                                                            <form action="<?= $rootUrl ?>/amigos/aceitar/<?= $atleta['id'] ?>" method="POST" class="m-0 flex-grow-1">
                                                                <button type="submit" class="btn btn-secondary-gomos text-dark btn-sm w-100 py-2 fw-bold">
                                                                    <i class="fa-solid fa-check text-dark me-1"></i> ACEITAR
                                                                </button>
                                                            </form>
                                                            <form action="<?= $rootUrl ?>/amigos/recusar/<?= $atleta['id'] ?>" method="POST" class="m-0 flex-grow-1">
                                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-2 border-danger">
                                                                    <i class="fa-solid fa-xmark me-1"></i> RECUSAR
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php elseif ($atleta['status_amizade'] === 'aceita'): ?>
                                                        <form action="<?= $rootUrl ?>/amigos/recusar/<?= $atleta['id'] ?>" method="POST" onsubmit="return confirm('Deseja realmente remover este atleta dos seus amigos?');" class="m-0 w-100">
                                                            <button type="submit" class="btn btn-outline-secondary btn-sm w-100 py-2 border-secondary text-danger btn-desfazer-amizade">
                                                                <i class="fa-solid fa-user-minus me-1"></i> DESFAZER AMIZADE
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-secondary">
                                    <i class="fa-solid fa-user-xmark" style="font-size: 3rem;"></i>
                                    <p class="mt-3 m-0">Nenhum atleta encontrado com os termos fornecidos.</p>
                                    <p class="small text-muted">Tente buscar por partes do nome ou username.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Sugestões de Amizade / Recomendações -->
                    <div class="card-gomos p-4">
                        <h4 class="text-white border-bottom border-secondary pb-2 mb-4">
                            <i class="fa-solid fa-users text-lime me-2"></i> 
                            Sugestões de Atletas na Rede GOMOS
                        </h4>
                        
                        <?php if (!empty($sugestoes)): ?>
                            <div class="row g-3">
                                <?php foreach ($sugestoes as $sug): ?>
                                    <div class="col-md-6 col-xxl-4">
                                        <div class="card-gomos p-3 m-0 d-flex flex-column justify-content-between h-100 border border-secondary" style="background-color: rgba(255, 255, 255, 0.02);">
                                            <div class="d-flex align-items-start gap-3">
                                                <a href="<?= $rootUrl ?>/perfil/<?= $sug['username'] ?>">
                                                    <img src="<?= $root ?>/assets/img/<?= $sug['foto_perfil'] ?: 'default_avatar.png' ?>" alt="Avatar" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover; border: 1.5px solid var(--border-color);">
                                                </a>
                                                <div class="flex-grow-1">
                                                    <h6 class="text-white m-0 fw-bold">
                                                        <a href="<?= $rootUrl ?>/perfil/<?= $sug['username'] ?>" class="text-decoration-none text-white hover-orange">
                                                            <?= $sug['nome'] ?>
                                                        </a>
                                                    </h6>
                                                    <span class="text-muted-gomos small d-block">@<?= $sug['username'] ?></span>
                                                    
                                                    <div class="text-secondary small mt-2" style="font-size: 0.8rem;">
                                                        <i class="fa-solid fa-location-dot me-1 text-orange"></i> <?= $sug['cidade'] ?>/<?= $sug['estado'] ?>
                                                    </div>
                                                    <?php if (!empty($sug['academia_nome'])): ?>
                                                        <div class="text-secondary small mt-1" style="font-size: 0.8rem;">
                                                            <i class="fa-solid fa-dumbbell me-1 text-lime"></i> <?= $sug['academia_nome'] ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="border-top border-secondary mt-3 pt-2 d-flex justify-content-end">
                                                <?php if ($sug['status_amizade'] === 'nenhum'): ?>
                                                    <form action="<?= $rootUrl ?>/amigos/adicionar/<?= $sug['id'] ?>" method="POST" class="m-0 w-100">
                                                        <button type="submit" class="btn btn-outline-gomos btn-sm w-100 py-2 border-secondary text-secondary">
                                                            <i class="fa-solid fa-user-plus me-1"></i> ADICIONAR ATLETA
                                                        </button>
                                                    </form>
                                                <?php elseif ($sug['status_amizade'] === 'pendente_enviado'): ?>
                                                    <button class="btn btn-outline-secondary btn-sm w-100 py-2 border-secondary text-muted" disabled>
                                                        <i class="fa-solid fa-clock me-1"></i> ENVIADO
                                                    </button>
                                                <?php elseif ($sug['status_amizade'] === 'pendente_recebido'): ?>
                                                    <div class="d-flex gap-2 w-100">
                                                        <form action="<?= $rootUrl ?>/amigos/aceitar/<?= $sug['id'] ?>" method="POST" class="m-0 flex-grow-1">
                                                            <button type="submit" class="btn btn-secondary-gomos text-dark btn-sm w-100 py-2 fw-bold">
                                                                ACEITAR
                                                            </button>
                                                        </form>
                                                        <form action="<?= $rootUrl ?>/amigos/recusar/<?= $sug['id'] ?>" method="POST" class="m-0 flex-grow-1">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-2 border-danger">
                                                                RECUSAR
                                                            </button>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-secondary small m-0">Nenhuma sugestão de conexão disponível no momento.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
