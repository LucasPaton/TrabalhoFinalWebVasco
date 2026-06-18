<?php 
$pageTitle = "Unidades GOMOS";
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';

$usuarioLogadoId = \App\Helpers\Session::get('usuario_id');
$usuarioAcademiaId = \App\Helpers\Session::get('academia_id');
?>

<div class="dashboard-wrapper">
    <!-- Sidebar Esquerdo -->
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="main-content-gomos bg-dark">
        <div class="container-fluid">
            
            <div class="row">
                <!-- Coluna de Busca e Lista (Esquerda) -->
                <div class="col-lg-7">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary pb-3">
                        <div>
                            <h2 class="text-white m-0">UNIDADES <span class="text-orange">GOMOS</span></h2>
                            <p class="text-secondary m-0">Encontre as unidades da rede GOMOS e veja quem treina nelas.</p>
                        </div>
                    </div>

                    <!-- Filtro de Busca -->
                    <div class="card-gomos p-3 mb-4">
                        <form action="<?= $rootUrl ?>/academias" method="GET">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" name="nome" class="form-control form-control-gomos" placeholder="Buscar unidade..." value="<?= isset($_GET['nome']) ? htmlspecialchars($_GET['nome']) : '' ?>">
                                </div>
                                <div class="col-md-4 col-7">
                                    <input type="text" name="cidade" class="form-control form-control-gomos" placeholder="Cidade..." value="<?= isset($_GET['cidade']) ? htmlspecialchars($_GET['cidade']) : '' ?>">
                                </div>
                                <div class="col-md-2 col-3">
                                    <input type="text" name="estado" class="form-control form-control-gomos" placeholder="UF" maxlength="2" value="<?= isset($_GET['estado']) ? htmlspecialchars($_GET['estado']) : '' ?>">
                                </div>
                                <div class="col-md-1 col-2">
                                    <button type="submit" class="btn btn-primary-gomos w-100 p-2 text-center" style="height: 100%; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de Academias -->
                    <div class="row g-3">
                        <?php if (!empty($academias)): ?>
                            <?php foreach ($academias as $ac): ?>
                                <div class="col-md-12">
                                    <div class="card-gomos p-3 m-0 d-flex flex-sm-row gap-3 align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= $root ?>/assets/img/<?= $ac['foto'] ?>" alt="Academia" class="rounded" style="width: 70px; height: 70px; object-fit: cover; border: 1px solid var(--border-color);">
                                            <div>
                                                <h5 class="text-white m-0 fw-bold">
                                                    <?= $ac['nome'] ?>
                                                    <?php if ($ac['verificada']): ?>
                                                        <span class="text-lime" style="font-size: 0.85rem;" title="Academia Verificada GOMOS"><i class="fa-solid fa-circle-check"></i></span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="text-secondary small m-0"><?= $ac['endereco'] ?>, <?= $ac['cidade'] ?> - <?= $ac['estado'] ?></p>
                                                
                                                <!-- Avaliações e Membros -->
                                                <div class="d-flex align-items-center gap-3 mt-1 small text-secondary">
                                                    <span class="text-warning"><i class="fa-solid fa-star"></i> <?= $ac['avaliacao_media'] ?></span>
                                                    <span><i class="fa-solid fa-user-group text-orange"></i> <?= $ac['total_membros'] ?> Membros</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="<?= $rootUrl ?>/academias?id=<?= $ac['id'] ?>" class="btn btn-outline-gomos btn-sm border-secondary text-secondary py-2">VER PERFIL</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="card-gomos p-4 text-center">
                                    <p class="text-secondary m-0">Nenhuma academia correspondente encontrada.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Detalhes e Check-in / Populares (Direita) -->
                <div class="col-lg-5">
                    <?php if ($academia): ?>
                        
                        <!-- Perfil da Academia Selecionada -->
                        <div class="card-gomos mb-4 p-4">
                            <div class="text-center mb-3">
                                <img src="<?= $root ?>/assets/img/<?= $academia['foto'] ?>" alt="Academia" class="rounded w-100 mb-3" style="max-height: 200px; object-fit: cover; border: 1.5px solid var(--border-color);">
                                <h3 class="text-white fw-bold m-0"><?= $academia['nome'] ?></h3>
                                <p class="text-secondary small m-0"><?= $academia['endereco'] ?>, <?= $academia['cidade'] ?> - <?= $academia['estado'] ?></p>
                            </div>

                            <!-- Contatos/Links -->
                            <div class="border-top border-bottom border-secondary py-3 my-3 small text-secondary" style="border-style: dashed !important;">
                                <div class="mb-2"><i class="fa-solid fa-phone me-2 text-orange"></i> <?= $academia['telefone'] ?: 'Não cadastrado' ?></div>
                                <div class="mb-2"><i class="fa-solid fa-globe me-2 text-lime"></i> <?= $academia['site'] ? '<a href="' . $academia['site'] . '" target="_blank" class="text-lime">' . $academia['site'] . '</a>' : 'Não cadastrado' ?></div>
                                <div><i class="fa-solid fa-location-crosshairs me-2 text-orange"></i> CEP: <?= $academia['cep'] ?></div>
                            </div>

                            <!-- Frequência / Ações de Vínculo -->
                            <div class="d-flex gap-2 mb-4">
                                <?php if ($usuarioAcademiaId == $academia['id']): ?>
                                    <button class="btn btn-outline-secondary w-100 py-3" disabled><i class="fa-solid fa-house-chimney text-lime"></i> ESTA É SUA UNIDADE</button>
                                <?php else: ?>
                                    <form action="<?= $rootUrl ?>/academias/vincular/<?= $academia['id'] ?>" method="POST" class="w-100 m-0">
                                        <button type="submit" class="btn btn-secondary-gomos text-dark w-100 py-3"><i class="fa-solid fa-house-laptop"></i> DEFINIR COMO MINHA UNIDADE</button>
                                    </form>
                                <?php endif; ?>
                            </div>

                            <!-- Botão de Registrar Check-in -->
                            <button type="button" class="btn btn-primary-gomos w-100 py-3 mb-4" data-bs-toggle="modal" data-bs-target="#modalCheckin">
                                <i class="fa-solid fa-location-arrow text-dark"></i> REGISTRAR CHECK-IN HOJE
                            </button>

                            <!-- Lista de Membros do Gomos na Academia -->
                             <h5 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-user-group text-lime me-2"></i> ATLETAS DA UNIDADE (<?= count($membros) ?>)</h5>
                            <div class="gym-members" style="max-height: 250px; overflow-y: auto;">
                                <?php if (!empty($membros)): ?>
                                    <ul class="list-unstyled m-0">
                                        <?php foreach ($membros as $m): ?>
                                            <li class="d-flex align-items-center justify-content-between py-2 border-bottom border-secondary" style="border-style: dotted !important;">
                                                <div class="d-flex align-items-center">
                                                    <a href="<?= $rootUrl ?>/perfil/<?= $m['username'] ?>">
                                                        <img src="<?= $root ?>/assets/img/<?= $m['foto_perfil'] ?>" alt="Avatar" class="rounded-circle me-3" style="width: 34px; height: 34px; object-fit: cover; border: 1.5px solid var(--accent-primary)">
                                                    </a>
                                                    <div>
                                                        <h6 class="m-0 text-white"><a href="<?= $rootUrl ?>/perfil/<?= $m['username'] ?>" class="text-decoration-none text-white fw-bold"><?= $m['nome'] ?></a></h6>
                                                        <small class="text-secondary">@<?= $m['username'] ?></small>
                                                    </div>
                                                </div>
                                                <span class="badge badge-dificuldade badge-iniciante" style="font-size: 0.65rem;"><?= $m['pontos_ranking'] ?> PTS</span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-secondary small m-0 py-2">Seja o primeiro membro desta unidade!</p>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Nenhuma Academia Selecionada - Mostrar Mais Frequentadas do Mês -->
                        <div class="card-gomos p-4">
                             <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-fire text-orange me-2"></i> UNIDADES POPULARES DO MÊS</h4>
                            <p class="text-secondary small mb-4">Classificação baseada no total de check-ins acumulados no mês corrente.</p>
                            
                            <div class="ranking-preview-list">
                                <?php 
                                $pos = 1;
                                if (!empty($academiasMaisFrequentadas)):
                                    foreach ($academiasMaisFrequentadas as $pop): 
                                ?>
                                    <div class="ranking-list-item d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="ranking-position text-secondary" style="font-family: 'Bebas Neue';">
                                                #<?= $pos ?>
                                            </span>
                                            <img src="<?= $root ?>/assets/img/<?= $pop['foto'] ?>" alt="Avatar" class="ranking-avatar ms-2 me-3 rounded">
                                            <div>
                                                <h6 class="m-0"><a href="<?= $rootUrl ?>/academias?id=<?= $pop['id'] ?>" class="text-decoration-none text-white fw-bold"><?= $pop['nome'] ?></a></h6>
                                                <small class="text-muted-gomos"><?= $pop['cidade'] ?> - <?= $pop['estado'] ?></small>
                                            </div>
                                        </div>
                                        <span class="badge badge-dificuldade badge-iniciante" style="font-size: 0.75rem;"><i class="fa-solid fa-location-arrow"></i> <?= $pop['total_checkins'] ?? 0 ?> CHECK-INS</span>
                                    </div>
                                <?php 
                                    $pos++;
                                    endforeach; 
                                else:
                                ?>
                                    <p class="text-secondary small m-0">Nenhum check-in registrado este mês.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Modal para Registrar Check-in -->
<?php if ($academia): ?>
<div class="modal fade" id="modalCheckin" tabindex="-1" aria-labelledby="modalCheckinLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-gomos">
            <div class="modal-header modal-header-gomos">
                <h5 class="modal-title" id="modalCheckinLabel"><i class="fa-solid fa-location-arrow text-orange me-2"></i> REGISTRAR CHECK-IN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $rootUrl ?>/academias/checkin" method="POST">
                <input type="hidden" name="academia_id" value="<?= $academia['id'] ?>">
                
                <div class="modal-body">
                    <p class="small text-secondary mb-3">Qual treino você vai realizar hoje na <strong><?= $academia['nome'] ?></strong>?</p>
                    
                    <!-- Vincular Treino -->
                    <div class="mb-3">
                        <label for="treino_id" class="form-label text-muted-gomos">Treino a Realizar (Opcional)</label>
                        <select name="treino_id" id="treino_id" class="form-select form-select-gomos">
                            <option value="">Apenas marcar presença (Sem treino associado)</option>
                            <?php foreach ($meus_treinos as $mt): ?>
                                <option value="<?= $mt['id'] ?>"><?= $mt['titulo'] ?> (Grupo: <?= $mt['grupo_muscular'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Observação -->
                    <div class="mb-3">
                        <label for="observacao" class="form-label text-muted-gomos">Observação do dia (Ex: Foco no legpress / Cardio de 30 min)</label>
                        <textarea name="observacao" id="observacao" class="form-control form-control-gomos" rows="2" placeholder="Foco total na intensidade hoje!"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer modal-footer-gomos">
                    <button type="button" class="btn btn-outline-gomos border-secondary text-secondary" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary-gomos"><i class="fa-solid fa-check text-dark"></i> CONFIRMAR CHECK-IN</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
