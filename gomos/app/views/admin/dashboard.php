<?php 
$pageTitle = "Painel Administrativo";
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';

// Pegar a aba ativa passada no redirecionamento do PHP (?tab=...)
$tabAtiva = $_GET['tab'] ?? 'dashboard';
?>

<div class="dashboard-wrapper">
    <!-- Sidebar Esquerdo -->
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="main-content-gomos bg-dark">
        <div class="container-fluid">
            
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary pb-3">
                <div>
                    <h2 class="text-white m-0">PAINEL <span class="text-orange">ADMINISTRATIVO</span></h2>
                    <p class="text-secondary m-0">Gerencie usuários, modere treinos, verifique academias e aprove exercícios.</p>
                </div>
            </div>

            <!-- Stats Block -->
            <div class="row g-3 mb-4">
                <div class="col-md-2 col-6">
                    <div class="card-gomos text-center p-3 m-0">
                        <h3 class="text-orange m-0" style="font-family: 'Bebas Neue';"><?= $totais['usuarios'] ?></h3>
                        <span class="text-secondary small fw-bold">USUÁRIOS</span>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="card-gomos text-center p-3 m-0">
                        <h3 class="text-lime m-0" style="font-family: 'Bebas Neue';"><?= $totais['treinos'] ?></h3>
                        <span class="text-secondary small fw-bold">TREINOS</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card-gomos text-center p-3 m-0">
                        <h3 class="text-orange m-0" style="font-family: 'Bebas Neue';"><?= $totais['academias'] ?></h3>
                        <span class="text-secondary small fw-bold">ACADEMIAS</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card-gomos text-center p-3 m-0">
                        <h3 class="text-lime m-0" style="font-family: 'Bebas Neue';"><?= $totais['checkins'] ?></h3>
                        <span class="text-secondary small fw-bold">CHECK-INS</span>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="card-gomos text-center p-3 m-0">
                        <h3 class="text-orange m-0" style="font-family: 'Bebas Neue';"><?= $totais['comentarios'] ?></h3>
                        <span class="text-secondary small fw-bold">COMENTÁRIOS</span>
                    </div>
                </div>
            </div>

            <!-- Sistema de Abas Admin -->
            <ul class="nav nav-tabs border-secondary mb-4" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="admin-tab-btn <?= $tabAtiva == 'dashboard' ? 'active' : '' ?>" id="dash-tab" data-bs-toggle="tab" data-bs-target="#dash-pane" type="button" role="tab" aria-selected="true"><i class="fa-solid fa-chart-pie"></i> Gráficos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="admin-tab-btn <?= $tabAtiva == 'usuarios' ? 'active' : '' ?>" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios-pane" type="button" role="tab" aria-selected="false"><i class="fa-solid fa-users"></i> Usuários (<?= count($usuarios) ?>)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="admin-tab-btn <?= $tabAtiva == 'academias' ? 'active' : '' ?>" id="academias-tab" data-bs-toggle="tab" data-bs-target="#academias-pane" type="button" role="tab" aria-selected="false"><i class="fa-solid fa-hotel"></i> Academias (<?= count($academias) ?>)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="admin-tab-btn <?= $tabAtiva == 'exercicios' ? 'active' : '' ?>" id="exercicios-tab" data-bs-toggle="tab" data-bs-target="#exercicios-pane" type="button" role="tab" aria-selected="false"><i class="fa-solid fa-dumbbell"></i> Catálogo (<?= count($exercicios) ?>)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="admin-tab-btn <?= $tabAtiva == 'conquistas' ? 'active' : '' ?>" id="conquistas-tab" data-bs-toggle="tab" data-bs-target="#conquistas-pane" type="button" role="tab" aria-selected="false"><i class="fa-solid fa-award"></i> Conquistas (<?= count($conquistas) ?>)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="admin-tab-btn <?= $tabAtiva == 'treinos' ? 'active' : '' ?>" id="treinos-tab" data-bs-toggle="tab" data-bs-target="#treinos-pane" type="button" role="tab" aria-selected="false"><i class="fa-solid fa-triangle-exclamation"></i> Moderação Treinos (<?= count($treinos) ?>)</button>
                </li>
            </ul>

            <div class="tab-content" id="adminTabsContent">
                
                <!-- ABA 1: GRÁFICOS DE CRESCIMENTO -->
                <div class="tab-pane fade <?= $tabAtiva == 'dashboard' ? 'show active' : '' ?>" id="dash-pane" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card-gomos p-4">
                                <h4 class="text-white border-bottom border-secondary pb-2 mb-3">NOVOS ATLETAS CADASTRADOS</h4>
                                <div style="height: 300px; position: relative;">
                                    <canvas id="graficoCadastros"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card-gomos p-4">
                                <h4 class="text-white border-bottom border-secondary pb-2 mb-3">DICAS DO ADMINISTRADOR</h4>
                                <p class="text-secondary small">
                                    • Verifique novas academias que bateram a marca de 5 membros para conceder o selo.<br><br>
                                    • Exercícios cadastrados por atletas comuns aparecem com status "Pendente". Revise o nome e a grafia antes de aprovar para o catálogo nacional.<br><br>
                                    • Treinos contendo palavreado inadequado ou ofensivo devem ser removidos na aba de Moderação.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 2: CRUD USUÁRIOS -->
                <div class="tab-pane fade <?= $tabAtiva == 'usuarios' ? 'show active' : '' ?>" id="usuarios-pane" role="tabpanel">
                    <div class="card-gomos p-4">
                        <h4 class="text-white mb-3">GESTÃO DE ATLETAS GOMOS</h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-striped align-middle">
                                <thead>
                                    <tr class="text-secondary">
                                        <th>Nome / Username</th>
                                        <th>E-mail</th>
                                        <th>Cidade</th>
                                        <th>Cadastro em</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $u): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= $root ?>/assets/img/<?= $u['foto_perfil'] ?>" alt="Avatar" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;">
                                                    <div>
                                                        <strong class="text-white d-block"><?= $u['nome'] ?></strong>
                                                        <span class="text-secondary small">@<?= $u['username'] ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= $u['email'] ?></td>
                                            <td><?= $u['cidade'] ?> - <?= $u['estado'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($u['criado_em'])) ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-<?= $u['ativo'] ? 'success' : 'danger' ?> text-dark">
                                                    <?= $u['ativo'] ? 'ATIVO' : 'BLOQUEADO' ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <!-- Toggle Status -->
                                                    <form action="<?= $rootUrl ?>/admin/usuarios/status/<?= $u['id'] ?>" method="POST" class="m-0">
                                                        <button type="submit" class="btn btn-outline-warning btn-sm" title="<?= $u['ativo'] ? 'Bloquear usuário' : 'Desbloquear usuário' ?>">
                                                            <i class="fa-solid <?= $u['ativo'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Excluir -->
                                                    <form action="<?= $rootUrl ?>/admin/usuarios/excluir/<?= $u['id'] ?>" method="POST" onsubmit="return confirm('Excluir permanentemente este usuário? Esta ação deletará todos os treinos vinculados.');" class="m-0">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ABA 3: CRUD ACADEMIAS -->
                <div class="tab-pane fade <?= $tabAtiva == 'academias' ? 'show active' : '' ?>" id="academias-pane" role="tabpanel">
                    <div class="row">
                        <!-- Formulário Adicionar Academia -->
                        <div class="col-lg-4">
                            <div class="card-gomos p-3 mb-4">
                                <h4 class="text-white border-bottom border-secondary pb-2 mb-3">CADASTRAR ACADEMIA</h4>
                                <form action="<?= $rootUrl ?>/admin/academias/criar" method="POST" enctype="multipart/form-data">
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Nome da Academia *</label>
                                        <input type="text" name="nome" class="form-control form-control-gomos form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Endereço *</label>
                                        <input type="text" name="endereco" class="form-control form-control-gomos form-control-sm" required>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-8">
                                            <label class="form-label text-secondary small m-0">Cidade *</label>
                                            <input type="text" name="cidade" class="form-control form-control-gomos form-control-sm" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label text-secondary small m-0">UF *</label>
                                            <input type="text" name="estado" class="form-control form-control-gomos form-control-sm" required maxlength="2">
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label text-secondary small m-0">CEP *</label>
                                            <input type="text" name="cep" class="form-control form-control-gomos form-control-sm" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-secondary small m-0">Telefone</label>
                                            <input type="text" name="telefone" class="form-control form-control-gomos form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Website</label>
                                        <input type="text" name="site" class="form-control form-control-gomos form-control-sm" placeholder="https://...">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Foto da Academia</label>
                                        <input type="file" name="foto" class="form-control form-control-gomos form-control-sm" accept="image/*">
                                    </div>
                                    <div class="form-check mb-3 mt-2">
                                        <input class="form-check-input bg-dark border-secondary" type="checkbox" name="verificada" value="1" id="verifCheck">
                                        <label class="form-check-label text-white small" for="verifCheck">Selo Verificada GOMOS</label>
                                    </div>
                                    <button type="submit" class="btn btn-secondary-gomos text-dark w-100"><i class="fa-solid fa-plus"></i> SALVAR CADASTRO</button>
                                </form>
                            </div>
                        </div>

                        <!-- Lista de Academias -->
                        <div class="col-lg-8">
                            <div class="card-gomos p-4">
                                <h4 class="text-white mb-3">LISTA DE ACADEMIAS</h4>
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover table-striped align-middle">
                                        <thead>
                                            <tr class="text-secondary">
                                                <th>Nome</th>
                                                <th>Cidade</th>
                                                <th>Membros</th>
                                                <th class="text-center">Selo</th>
                                                <th class="text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($academias as $ac): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?= $root ?>/assets/img/<?= $ac['foto'] ?>" alt="Academia" class="rounded me-3" style="width: 38px; height: 38px; object-fit: cover;">
                                                            <div>
                                                                <strong class="text-white d-block"><?= $ac['nome'] ?></strong>
                                                                <span class="text-secondary small"><?= $ac['endereco'] ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= $ac['cidade'] ?> - <?= $ac['estado'] ?></td>
                                                    <td><?= $ac['total_membros'] ?></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-<?= $ac['verificada'] ? 'success' : 'secondary' ?> text-dark">
                                                            <?= $ac['verificada'] ? 'VERIFICADA' : 'COMUM' ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex justify-content-end gap-2">
                                                            <!-- Toggle Verification -->
                                                            <a href="<?= $rootUrl ?>/admin/academias/verificar?id=<?= $ac['id'] ?>" class="btn btn-outline-warning btn-sm" title="Toggle Verificação"><i class="fa-solid fa-certificate"></i></a>
                                                            <!-- Delete -->
                                                            <a href="<?= $rootUrl ?>/admin/academias/excluir?id=<?= $ac['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Deseja realmente remover esta academia?');"><i class="fa-solid fa-trash"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 4: CRUD EXERCÍCIOS CATALOGO -->
                <div class="tab-pane fade <?= $tabAtiva == 'exercicios' ? 'show active' : '' ?>" id="exercicios-pane" role="tabpanel">
                    <div class="row">
                        <!-- Cadastrar Exercício -->
                        <div class="col-lg-4">
                            <div class="card-gomos p-3 mb-4">
                                <h4 class="text-white border-bottom border-secondary pb-2 mb-3">CADASTRAR EXERCÍCIO</h4>
                                <form action="<?= $rootUrl ?>/admin/exercicios/criar" method="POST" enctype="multipart/form-data">
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Nome do Exercício *</label>
                                        <input type="text" name="nome" class="form-control form-control-gomos form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Grupo Muscular *</label>
                                        <input type="text" name="grupo_muscular" class="form-control form-control-gomos form-control-sm" placeholder="Ex: Peito, Pernas..." required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Equipamento</label>
                                        <input type="text" name="equipamento" class="form-control form-control-gomos form-control-sm" placeholder="Ex: Halteres, Barra, Máquina">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Descrição Curta</label>
                                        <textarea name="descricao" class="form-control form-control-gomos form-control-sm" rows="2"></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Imagem de Exemplo</label>
                                        <input type="file" name="imagem" class="form-control form-control-gomos form-control-sm" accept="image/*">
                                    </div>
                                    <div class="form-check mb-3 mt-2">
                                        <input class="form-check-input bg-dark border-secondary" type="checkbox" name="aprovado" value="1" id="aprovCheck" checked>
                                        <label class="form-check-label text-white small" for="aprovCheck">Aprovado e Visível no Catálogo</label>
                                    </div>
                                    <button type="submit" class="btn btn-secondary-gomos text-dark w-100"><i class="fa-solid fa-plus"></i> SALVAR EXERCÍCIO</button>
                                </form>
                            </div>
                        </div>

                        <!-- Lista de Exercícios -->
                        <div class="col-lg-8">
                            <div class="card-gomos p-4">
                                <h4 class="text-white mb-3">CATÁLOGO NACIONAL DE EXERCÍCIOS</h4>
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover table-striped align-middle">
                                        <thead>
                                            <tr class="text-secondary">
                                                <th>Nome</th>
                                                <th>Grupo Muscular</th>
                                                <th>Equipamento</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($exercicios as $ex): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?= $root ?>/assets/img/<?= $ex['imagem'] ?>" alt="Exercício" class="rounded me-3" style="width: 36px; height: 36px; object-fit: cover;">
                                                            <strong><?= $ex['nome'] ?></strong>
                                                        </div>
                                                    </td>
                                                    <td><?= $ex['grupo_muscular'] ?></td>
                                                    <td><?= $ex['equipamento'] ?: '-' ?></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-<?= $ex['aprovado'] ? 'success' : 'warning' ?> text-dark">
                                                            <?= $ex['aprovado'] ? 'APROVADO' : 'PENDENTE' ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex justify-content-end gap-2">
                                                            <!-- Toggle status aprovação -->
                                                            <a href="<?= $rootUrl ?>/admin/exercicios/aprovar?id=<?= $ex['id'] ?>" class="btn btn-outline-warning btn-sm" title="Toggle Aprovação"><i class="fa-solid fa-circle-check"></i></a>
                                                            <!-- Delete -->
                                                            <a href="<?= $rootUrl ?>/admin/exercicios/excluir?id=<?= $ex['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Deseja realmente remover do catálogo?');"><i class="fa-solid fa-trash"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 5: CRUD CONQUISTAS (BADGES) -->
                <div class="tab-pane fade <?= $tabAtiva == 'conquistas' ? 'show active' : '' ?>" id="conquistas-pane" role="tabpanel">
                    <div class="row">
                        <!-- Criar Conquista -->
                        <div class="col-lg-4">
                            <div class="card-gomos p-3 mb-4">
                                <h4 class="text-white border-bottom border-secondary pb-2 mb-3">NOVA CONQUISTA</h4>
                                <form action="<?= $rootUrl ?>/admin/conquistas/criar" method="POST" enctype="multipart/form-data">
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Nome do Badge *</label>
                                        <input type="text" name="nome" class="form-control form-control-gomos form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Descrição da conquista *</label>
                                        <textarea name="descricao" class="form-control form-control-gomos form-control-sm" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-secondary small m-0">Pontos Concedidos *</label>
                                        <input type="number" name="pontos_necessarios" class="form-control form-control-gomos form-control-sm" value="20" min="0" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-secondary small m-0">Ícone do Badge (PNG)</label>
                                        <input type="file" name="icone" class="form-control form-control-gomos form-control-sm" accept="image/*">
                                    </div>
                                    <button type="submit" class="btn btn-secondary-gomos text-dark w-100"><i class="fa-solid fa-award"></i> CRIAR CONQUISTA</button>
                                </form>
                            </div>
                        </div>

                        <!-- Lista de Conquistas -->
                        <div class="col-lg-8">
                            <div class="card-gomos p-4">
                                <h4 class="text-white mb-3">CONQUISTAS E BADGES GOMOS</h4>
                                <div class="row g-3">
                                    <?php foreach ($conquistas as $conq): ?>
                                        <div class="col-md-6">
                                            <div class="card bg-dark border border-secondary p-3 d-flex flex-row align-items-center gap-3">
                                                <img src="<?= $root ?>/assets/img/<?= $conq['icone'] ?>" alt="Badge Icon" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <h6 class="text-orange m-0 fw-bold"><?= $conq['nome'] ?></h6>
                                                    <p class="text-secondary small m-0 mb-1"><?= $conq['descricao'] ?></p>
                                                    <span class="badge bg-lime text-dark fw-bold" style="font-size: 0.65rem;">Concede +<?= $conq['pontos_necessarios'] ?> PTS</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 6: MODERAÇÃO DE TREINOS -->
                <div class="tab-pane fade <?= $tabAtiva == 'treinos' ? 'show active' : '' ?>" id="treinos-pane" role="tabpanel">
                    <div class="card-gomos p-4">
                        <h4 class="text-white mb-3">MODERAÇÃO DE CONTEÚDO IMPRÓPRIO</h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-striped align-middle">
                                <thead>
                                    <tr class="text-secondary">
                                        <th>Título do Treino</th>
                                        <th>Autor</th>
                                        <th>Divisão / Grupo Muscular</th>
                                        <th>Criado em</th>
                                        <th class="text-center">Visibilidade</th>
                                        <th class="text-end">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($treinos as $tr): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-orange"><a href="<?= $rootUrl ?>/treino/<?= $tr['id'] ?>" class="text-orange text-decoration-none" target="_blank"><?= $tr['titulo'] ?></a></strong>
                                                <span class="text-secondary d-block small text-truncate" style="max-width: 300px;"><?= $tr['descricao'] ?></span>
                                            </td>
                                            <td><a href="<?= $rootUrl ?>/perfil/<?= $tr['username'] ?>" class="text-white text-decoration-none">@<?= $tr['username'] ?></a></td>
                                            <td><?= $tr['tipo_treino'] ?> • <?= $tr['grupo_muscular'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($tr['criado_em'])) ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-<?= $tr['publico'] ? 'lime' : 'secondary' ?> text-dark">
                                                    <?= $tr['publico'] ? 'PÚBLICO' : 'PRIVADO' ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <!-- Deletar treino inadequado -->
                                                <form action="<?= $rootUrl ?>/admin/treino/excluir/<?= $tr['id'] ?>" method="POST" onsubmit="return confirm('ATENÇÃO: Deseja excluir permanentemente este treino por violação de termos de uso?');" class="m-0">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Remover Conteúdo Inadequado"><i class="fa-solid fa-trash-can"></i> REMOVER</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>
</div>

<!-- Gráficos do Admin -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('graficoCadastros').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($meses) ?>,
                datasets: [{
                    label: 'Novos Cadastros',
                    data: <?= json_encode($cadastros) ?>,
                    backgroundColor: 'rgba(255, 107, 0, 0.15)',
                    borderColor: '#FF6B00',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#F5F5F5' }
                    }
                },
                scales: {
                    y: {
                        ticks: { color: '#888888', stepSize: 1 },
                        grid: { color: '#2D2D2D' }
                    },
                    x: {
                        ticks: { color: '#888888' },
                        grid: { color: '#2D2D2D' }
                    }
                }
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
