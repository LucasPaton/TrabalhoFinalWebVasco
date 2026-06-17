<?php 
$pageTitle = "Comparar Treinos";
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
                    <h2 class="text-white m-0">COMPARAR <span class="text-orange">TREINOS</span></h2>
                    <p class="text-secondary m-0">Compare cargas, repetições e volume acumulado lado a lado.</p>
                </div>
            </div>

            <!-- Seleção de Treinos -->
            <div class="card-gomos p-4 mb-4">
                <form action="<?= $rootUrl ?>/treino/comparar" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="t1" class="form-label text-muted-gomos">Treino A (Primeiro Treino)</label>
                            <select name="t1" id="t1" class="form-select form-select-gomos" required>
                                <option value="">Selecione um treino...</option>
                                <optgroup label="Meus Treinos">
                                    <?php foreach ($meus_treinos as $mt): ?>
                                        <option value="<?= $mt['id'] ?>" <?= ($t1_id == $mt['id']) ? 'selected' : '' ?>><?= $mt['titulo'] ?> (Apoio: <?= $mt['total_curtidas'] ?>)</option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Treinos de Amigos">
                                    <?php foreach ($treinos_amigos as $ta): ?>
                                        <option value="<?= $ta['id'] ?>" <?= ($t1_id == $ta['id']) ? 'selected' : '' ?>><?= $ta['titulo'] ?> (@<?= $ta['username'] ?>)</option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="t2" class="form-label text-muted-gomos">Treino B (Segundo Treino)</label>
                            <select name="t2" id="t2" class="form-select form-select-gomos" required>
                                <option value="">Selecione um treino...</option>
                                <optgroup label="Meus Treinos">
                                    <?php foreach ($meus_treinos as $mt): ?>
                                        <option value="<?= $mt['id'] ?>" <?= ($t2_id == $mt['id']) ? 'selected' : '' ?>><?= $mt['titulo'] ?> (Apoio: <?= $mt['total_curtidas'] ?>)</option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Treinos de Amigos">
                                    <?php foreach ($treinos_amigos as $ta): ?>
                                        <option value="<?= $ta['id'] ?>" <?= ($t2_id == $ta['id']) ? 'selected' : '' ?>><?= $ta['titulo'] ?> (@<?= $ta['username'] ?>)</option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary-gomos w-100 py-3"><i class="fa-solid fa-scale-unbalanced-flip"></i> COMPARAR</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Exibição de Comparação -->
            <?php if ($treino1 && $treino2): ?>
                
                <?php 
                // Determinar o treino vencedor por Volume Acumulado
                $t1_win = ($volume1 >= $volume2);
                ?>

                <!-- Estatísticas de Volume Acumulado -->
                <div class="row g-4 mb-4">
                    <!-- Treino 1 Stats -->
                    <div class="col-md-6">
                        <div class="comparison-column <?= $t1_win ? 'winner-highlight' : '' ?>">
                            <?php if ($t1_win): ?>
                                <span class="winner-badge"><i class="fa-solid fa-crown text-warning"></i> MAIOR VOLUME</span>
                            <?php endif; ?>
                            <span class="text-secondary small d-block">Autor: @<?= $treino1['username'] ?></span>
                            <h3 class="text-white fw-bold mb-3"><?= $treino1['titulo'] ?></h3>
                            
                            <div class="row text-center mt-3 g-2">
                                <div class="col-4">
                                    <div class="bg-dark p-2 border border-secondary rounded">
                                        <h4 class="text-orange m-0"><?= count($exercicios1) ?></h4>
                                        <span class="text-muted small">Exercícios</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-dark p-2 border border-secondary rounded">
                                        <?php 
                                        $tot_series = 0; 
                                        foreach($exercicios1 as $ex) $tot_series += $ex['series'];
                                        ?>
                                        <h4 class="text-lime m-0"><?= $tot_series ?></h4>
                                        <span class="text-muted small">Séries Totais</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-dark p-2 border border-secondary rounded">
                                        <h4 class="text-orange m-0 text-truncate" title="<?= number_format($volume1, 1, ',', '.') ?> kg"><?= number_format($volume1, 0, '', '.') ?> kg</h4>
                                        <span class="text-muted small">Volume Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Treino 2 Stats -->
                    <div class="col-md-6">
                        <div class="comparison-column <?= !$t1_win ? 'winner-highlight' : '' ?>">
                            <?php if (!$t1_win): ?>
                                <span class="winner-badge"><i class="fa-solid fa-crown text-warning"></i> MAIOR VOLUME</span>
                            <?php endif; ?>
                            <span class="text-secondary small d-block">Autor: @<?= $treino2['username'] ?></span>
                            <h3 class="text-white fw-bold mb-3"><?= $treino2['titulo'] ?></h3>
                            
                            <div class="row text-center mt-3 g-2">
                                <div class="col-4">
                                    <div class="bg-dark p-2 border border-secondary rounded">
                                        <h4 class="text-orange m-0"><?= count($exercicios2) ?></h4>
                                        <span class="text-muted small">Exercícios</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-dark p-2 border border-secondary rounded">
                                        <?php 
                                        $tot_series = 0; 
                                        foreach($exercicios2 as $ex) $tot_series += $ex['series'];
                                        ?>
                                        <h4 class="text-lime m-0"><?= $tot_series ?></h4>
                                        <span class="text-muted small">Séries Totais</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-dark p-2 border border-secondary rounded">
                                        <h4 class="text-orange m-0 text-truncate" title="<?= number_format($volume2, 1, ',', '.') ?> kg"><?= number_format($volume2, 0, '', '.') ?> kg</h4>
                                        <span class="text-muted small">Volume Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Comparação Chart.js -->
                <div class="card-gomos mb-4">
                    <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-chart-column text-orange me-2"></i> COMPARATIVO DE TONELAGEM</h4>
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficoComparacao"></canvas>
                    </div>
                </div>

                <!-- Tabela Comparativa de Exercícios -->
                <div class="card-gomos">
                    <h4 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-arrow-right-arrow-left text-lime me-2"></i> COMPARATIVO DE EXERCÍCIOS</h4>
                    
                    <div class="row">
                        <!-- Coluna Exercícios 1 -->
                        <div class="col-md-6 border-end border-secondary">
                            <h5 class="text-white fw-bold mb-3"><?= $treino1['titulo'] ?></h5>
                            <ul class="list-group list-group-flush bg-transparent">
                                <?php foreach ($exercicios1 as $ex): ?>
                                    <li class="list-group-item bg-transparent text-white border-secondary small py-2 px-1">
                                        <div class="d-flex justify-content-between">
                                            <strong><?= $ex['nome_exercicio'] ?></strong>
                                            <span class="text-lime"><?= $ex['peso_kg'] ?> kg</span>
                                        </div>
                                        <span class="text-muted-gomos"><?= $ex['series'] ?>x<?= $ex['repeticoes'] ?> • Descanso: <?= $ex['descanso_segundos'] ?>s</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <!-- Coluna Exercícios 2 -->
                        <div class="col-md-6">
                            <h5 class="text-white fw-bold mb-3"><?= $treino2['titulo'] ?></h5>
                            <ul class="list-group list-group-flush bg-transparent">
                                <?php foreach ($exercicios2 as $ex): ?>
                                    <li class="list-group-item bg-transparent text-white border-secondary small py-2 px-1">
                                        <div class="d-flex justify-content-between">
                                            <strong><?= $ex['nome_exercicio'] ?></strong>
                                            <span class="text-lime"><?= $ex['peso_kg'] ?> kg</span>
                                        </div>
                                        <span class="text-muted-gomos"><?= $ex['series'] ?>x<?= $ex['repeticoes'] ?> • Descanso: <?= $ex['descanso_segundos'] ?>s</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Inicializar Gráficos Chart.js -->
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const ctx = document.getElementById('graficoComparacao').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['<?= addslashes($treino1['titulo']) ?>', '<?= addslashes($treino2['titulo']) ?>'],
                                datasets: [{
                                    label: 'Tonelagem Total Acumulada (Séries x Repetições x Carga)',
                                    data: [<?= $volume1 ?>, <?= $volume2 ?>],
                                    backgroundColor: ['#FF6B00', '#A3E635'],
                                    borderColor: ['#D65900', '#8DCC22'],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: '#F5F5F5'
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: { color: '#888888' },
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

            <?php else: ?>
                <!-- Estado Vazio (Sem seleção) -->
                <div class="card-gomos p-5 text-center">
                    <i class="fa-solid fa-scale-unbalanced text-secondary mb-3" style="font-size: 3.5rem;"></i>
                    <h4 class="text-white">SELECIONE DOIS TREINOS PARA COMPARAR</h4>
                    <p class="text-secondary mx-auto" style="max-width: 450px;">Use o formulário acima para escolher qualquer uma das suas fichas ou as fichas públicas de seus amigos.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
