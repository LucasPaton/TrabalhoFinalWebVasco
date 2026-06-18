<?php 
$pageTitle = "Criar Novo Treino";
$loadTreinoJs = true; // Força carregamento do treino.js no footer
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
                    <h2 class="text-white m-0">CONSTRUTOR DE <span class="text-orange">TREINO</span></h2>
                    <p class="text-secondary m-0">Monte sua ficha de exercícios e salve em seu perfil.</p>
                </div>
                <a href="<?= $rootUrl ?>/feed" class="btn btn-outline-gomos btn-sm border-secondary text-secondary"><i class="fa-solid fa-arrow-left"></i> CANCELAR</a>
            </div>

            <form action="<?= $rootUrl ?>/treino/criar" method="POST" id="form-criar-treino">
                <div class="row">
                    <!-- Configurações do Treino (Coluna da Esquerda) -->
                    <div class="col-lg-4">
                        <div class="card-gomos mb-4">
                            <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-gear text-orange me-2"></i> DETALHES DA FICHA</h4>
                            
                            <!-- Título do Treino -->
                            <div class="mb-3">
                                <label for="titulo" class="form-label text-muted-gomos">Título do Treino *</label>
                                <input type="text" name="titulo" id="titulo" class="form-control form-control-gomos" placeholder="Ex: Peito Extremo, Pernas Gigantes" required>
                            </div>

                            <!-- Grupo Muscular -->
                            <div class="mb-3">
                                <label for="grupo_muscular" class="form-label text-muted-gomos">Grupos Musculares *</label>
                                <input type="text" name="grupo_muscular" id="grupo_muscular" class="form-control form-control-gomos" placeholder="Ex: Peito, Ombros / Pernas Completo" required>
                            </div>

                            <!-- Divisão do Treino -->
                            <div class="mb-3">
                                <label for="tipo_treino" class="form-label text-muted-gomos">Tipo / Divisão de Treino</label>
                                <select name="tipo_treino" id="tipo_treino" class="form-select form-select-gomos">
                                    <option value="Full">Full Body (Corpo Inteiro)</option>
                                    <option value="A" selected>A (Ficha Única ou Divisão A)</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="ABC">ABC</option>
                                    <option value="PPL">Push/Pull/Legs (PPL)</option>
                                </select>
                            </div>

                            <!-- Duração em Minutos -->
                            <div class="mb-3">
                                <label for="duracao_minutos" class="form-label text-muted-gomos">Duração Média (minutos)</label>
                                <input type="number" name="duracao_minutos" id="duracao_minutos" class="form-control form-control-gomos" value="60" min="10" max="180">
                            </div>

                            <!-- Dificuldade -->
                            <div class="mb-3">
                                <label for="nivel_dificuldade" class="form-label text-muted-gomos">Nível de Dificuldade</label>
                                <select name="nivel_dificuldade" id="nivel_dificuldade" class="form-select form-select-gomos">
                                    <option value="iniciante">Iniciante</option>
                                    <option value="intermediario" selected>Intermediário</option>
                                    <option value="avancado">Avançado / Elite</option>
                                </select>
                            </div>

                            <!-- Descrição -->
                            <div class="mb-3">
                                <label for="descricao" class="form-label text-muted-gomos">Descrição / Objetivo do Treino</label>
                                <textarea name="descricao" id="descricao" class="form-control form-control-gomos" rows="3" placeholder="Foco em ganho de volume e ativação de pico no supino."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Exercícios Dinâmicos (Coluna da Direita) -->
                    <div class="col-lg-8">
                        <div class="card-gomos mb-4">
                            <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-2 mb-4">
                                <h4 class="text-white m-0"><i class="fa-solid fa-list-check text-lime me-2"></i> EXERCÍCIOS DA FICHA</h4>
                                <button type="button" class="btn btn-secondary-gomos btn-sm text-dark px-3 py-2" id="btn-add-exercicio">
                                    <i class="fa-solid fa-plus"></i> ADICIONAR EXERCÍCIO
                                </button>
                            </div>

                            <!-- Container de Listagem de Exercícios Ordenáveis -->
                            <div id="exercicios-container">
                                
                                <!-- Exercício Inicial Default (Obrigatório) -->
                                <div class="builder-exercise-card card-gomos mb-3" id="ex_card_1">
                                    <button type="button" class="builder-remove-btn btn-remove-exercicio" title="Remover Exercício">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <div class="row align-items-center">
                                        <div class="col-auto drag-handle text-secondary cursor-move" style="font-size: 1.5rem;">
                                            <i class="fa-solid fa-grip-vertical"></i>
                                        </div>
                                        <div class="col">
                                            <div class="row g-3">
                                                <!-- Nome do Exercício (Autocomplete) -->
                                                <div class="col-md-5">
                                                    <label class="form-label text-muted-gomos small">Nome do Exercício *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fa-solid fa-search"></i></span>
                                                        <input type="text" name="exercicio_nome[]" class="form-control form-control-gomos exercicio-autocomplete" placeholder="Ex: Supino Reto com Barra..." required>
                                                    </div>
                                                </div>
                                                
                                                <!-- Séries -->
                                                <div class="col-md-2 col-6">
                                                    <label class="form-label text-muted-gomos small">Séries</label>
                                                    <input type="number" name="exercicio_series[]" class="form-control form-control-gomos" value="4" min="1" required>
                                                </div>
                                                
                                                <!-- Repetições -->
                                                <div class="col-md-2 col-6">
                                                    <label class="form-label text-muted-gomos small">Repetições</label>
                                                    <input type="text" name="exercicio_repeticoes[]" class="form-control form-control-gomos" value="10" placeholder="Ex: 10, 8-12, Falha" required>
                                                </div>

                                                <!-- Carga (kg) -->
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted-gomos small">Carga (kg)</label>
                                                    <input type="number" name="exercicio_peso[]" class="form-control form-control-gomos" value="0" step="0.5">
                                                </div>
                                                
                                                <!-- Descanso -->
                                                <div class="col-md-4 col-12">
                                                    <label class="form-label text-muted-gomos small">Descanso (segundos)</label>
                                                    <select name="exercicio_descanso[]" class="form-select form-select-gomos">
                                                        <option value="30">30s</option>
                                                        <option value="45">45s</option>
                                                        <option value="60" selected>60s (1 min)</option>
                                                        <option value="90">90s (1.5 min)</option>
                                                        <option value="120">120s (2 min)</option>
                                                        <option value="180">180s (3 min)</option>
                                                    </select>
                                                </div>

                                                <!-- Observações -->
                                                <div class="col-md-8 col-12">
                                                    <label class="form-label text-muted-gomos small">Observações / Instruções</label>
                                                    <input type="text" name="exercicio_obs[]" class="form-control form-control-gomos" placeholder="Ex: Fazer movimento lento de descida...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> <!-- Fim exercicios-container -->

                            <!-- Botões Salvar -->
                            <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-secondary">
                                <a href="<?= $rootUrl ?>/feed" class="btn btn-outline-gomos border-secondary text-secondary">DESPERTAR/CANCELAR</a>
                                <button type="submit" class="btn btn-primary-gomos"><i class="fa-solid fa-floppy-disk"></i> SALVAR TREINO</button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
