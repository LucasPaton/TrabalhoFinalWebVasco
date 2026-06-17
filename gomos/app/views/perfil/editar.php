<?php 
$pageTitle = "Editar Perfil";
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
                    <h2 class="text-white m-0">EDITAR MEU <span class="text-orange">PERFIL</span></h2>
                    <p class="text-secondary m-0">Mantenha seus dados e medidas atualizados.</p>
                </div>
                <a href="<?= $rootUrl ?>/perfil" class="btn btn-outline-gomos btn-sm border-secondary text-secondary"><i class="fa-solid fa-arrow-left"></i> CANCELAR</a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-gomos p-4">
                        <form action="<?= $rootUrl ?>/perfil/editar" method="POST" enctype="multipart/form-data">
                            
                            <!-- Foto de Perfil Atual e Upload -->
                            <div class="row align-items-center mb-4 pb-3 border-bottom border-secondary" style="border-style: dashed !important;">
                                <div class="col-auto text-center">
                                    <img src="<?= $root ?>/assets/img/<?= $usuario['foto_perfil'] ?>" alt="Avatar Atual" class="rounded-circle border border-2 border-orange mb-2" style="width: 90px; height: 90px; object-fit: cover;">
                                </div>
                                <div class="col">
                                    <label for="foto_perfil" class="form-label text-white fw-bold">Alterar Foto de Perfil</label>
                                    <input type="file" name="foto_perfil" id="foto_perfil" class="form-control form-control-gomos" accept="image/*">
                                    <span class="form-text text-secondary">Apenas JPG, JPEG e PNG. Max 2MB.</span>
                                </div>
                            </div>

                            <!-- Nome Completo -->
                            <div class="mb-3">
                                <label for="nome" class="form-label text-muted-gomos">Nome Completo *</label>
                                <input type="text" name="nome" id="nome" class="form-control form-control-gomos" value="<?= $usuario['nome'] ?>" required>
                            </div>

                            <!-- Bio -->
                            <div class="mb-3">
                                <label for="bio" class="form-label text-muted-gomos">Bio (Aparece no seu perfil)</label>
                                <textarea name="bio" id="bio" class="form-control form-control-gomos" rows="3" placeholder="Foco na hipertrofia e na constância!"><?= $usuario['bio'] ?></textarea>
                            </div>

                            <!-- Nível Fitness -->
                            <div class="mb-3">
                                <label for="nivel_fitness" class="form-label text-muted-gomos">Nível Fitness *</label>
                                <select name="nivel_fitness" id="nivel_fitness" class="form-select form-select-gomos" required>
                                    <option value="iniciante" <?= $usuario['nivel_fitness'] == 'iniciante' ? 'selected' : '' ?>>Iniciante</option>
                                    <option value="intermediario" <?= $usuario['nivel_fitness'] == 'intermediario' ? 'selected' : '' ?>>Intermediário</option>
                                    <option value="avancado" <?= $usuario['nivel_fitness'] == 'avancado' ? 'selected' : '' ?>>Avançado</option>
                                </select>
                            </div>

                            <!-- Medidas (Peso e Altura) -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="peso" class="form-label text-muted-gomos">Peso Atual (kg)</label>
                                    <input type="number" name="peso" id="peso" class="form-control form-control-gomos" value="<?= $usuario['peso'] ?>" step="0.1">
                                </div>
                                <div class="col-6">
                                    <label for="altura" class="form-label text-muted-gomos">Altura (cm)</label>
                                    <input type="number" name="altura" id="altura" class="form-control form-control-gomos" value="<?= $usuario['altura'] ?>" min="100" max="250">
                                </div>
                            </div>

                            <!-- Localização (Cidade e Estado) -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="cidade" class="form-label text-muted-gomos">Cidade *</label>
                                    <input type="text" name="cidade" id="cidade" class="form-control form-control-gomos" value="<?= $usuario['cidade'] ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="estado" class="form-label text-muted-gomos">Estado *</label>
                                    <select name="estado" id="estado" class="form-select form-select-gomos" required>
                                        <?php 
                                        $estados = ['AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO'];
                                        foreach ($estados as $uf):
                                        ?>
                                            <option value="<?= $uf ?>" <?= $usuario['estado'] == $uf ? 'selected' : '' ?>><?= $uf ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Vínculo de Academia -->
                            <div class="mb-4">
                                <label for="academia_id" class="form-label text-muted-gomos">Qual academia você frequenta?</label>
                                <select name="academia_id" id="academia_id" class="form-select form-select-gomos">
                                    <option value="">Nenhuma academia (Treino por conta)</option>
                                    <?php foreach ($academias as $academia): ?>
                                        <option value="<?= $academia['id'] ?>" <?= $usuario['academia_id'] == $academia['id'] ? 'selected' : '' ?>><?= $academia['nome'] ?> - <?= $academia['cidade'] ?>/<?= $academia['estado'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-secondary">
                                <a href="<?= $rootUrl ?>/perfil" class="btn btn-outline-gomos border-secondary text-secondary">CANCELAR</a>
                                <button type="submit" class="btn btn-primary-gomos">SALVAR ALTERAÇÕES</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
