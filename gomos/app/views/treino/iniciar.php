<?php 
$pageTitle = "Treino Ativo: " . htmlspecialchars($treino['titulo']);
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';
?>

<style>
/* Estilos Especiais do Tracker estilo Hevy */
.tracker-header-fixed {
    background: rgba(26, 26, 26, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 2px solid var(--accent-primary);
    position: sticky;
    top: 0;
    z-index: 1020;
    padding: 15px 0;
}
.timer-text {
    font-family: 'Bebas Neue', monospace;
    font-size: 2.25rem;
    color: var(--accent-primary);
    letter-spacing: 2px;
}
.exercise-tracker-card {
    background: rgba(30, 30, 30, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}
.exercise-tracker-card:hover {
    border-color: rgba(255, 95, 0, 0.3);
}
.serie-concluida {
    background-color: rgba(173, 255, 47, 0.08) !important;
    text-decoration: line-through;
    color: #a0a0a0 !important;
}
.serie-concluida input {
    text-decoration: line-through;
    color: #a0a0a0 !important;
    opacity: 0.5;
}
.btn-check-serie {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.2);
    background: transparent;
    color: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
    margin: 0 auto;
}
.btn-check-serie:hover {
    border-color: var(--accent-secondary);
    color: var(--accent-secondary);
}
.btn-check-serie.checked {
    background: var(--accent-secondary) !important;
    border-color: var(--accent-secondary) !important;
    color: #121212 !important;
}
.input-tracker-num {
    background: rgba(0, 0, 0, 0.3) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    color: #fff !important;
    text-align: center;
    font-weight: bold;
    border-radius: 6px;
    padding: 4px 8px;
    max-width: 80px;
    margin: 0 auto;
}
.input-tracker-num:focus {
    border-color: var(--accent-primary) !important;
    box-shadow: none !important;
}
/* Widget de Descanso */
.rest-timer-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: rgba(26, 26, 26, 0.95);
    border: 1px solid var(--accent-secondary);
    border-radius: 8px;
    padding: 12px 20px;
    color: #fff;
    z-index: 1050;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    gap: 15px;
}
.rest-timer-countdown {
    font-family: 'Bebas Neue', monospace;
    font-size: 1.75rem;
    color: var(--accent-secondary);
}
</style>

<div class="dashboard-wrapper">
    <!-- Sidebar Esquerdo -->
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="main-content-gomos bg-dark p-0">
        
        <!-- Header Fixo do Rastreador -->
        <div class="tracker-header-fixed border-bottom border-secondary">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <span class="badge badge-iniciante mb-1 uppercase">TREINO ATIVO <i class="fa-solid fa-circle-play text-lime ms-1 animate-pulse"></i></span>
                        <h3 class="text-white fw-bold m-0 text-truncate" style="max-width: 350px;"><?= htmlspecialchars($treino['titulo']) ?></h3>
                    </div>
                    
                    <!-- Timer do Treino -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end">
                            <span class="text-secondary small d-block uppercase" style="letter-spacing: 1px;">Tempo Decorrido</span>
                            <span id="tracker-timer" class="timer-text">00:00:00</span>
                        </div>
                        <button type="button" class="btn btn-primary-gomos px-4 py-3 fw-bold" id="btn-finalizar-treino">
                            <i class="fa-solid fa-circle-check text-dark me-2"></i> FINALIZAR
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-4 py-4">
            <div class="row">
                <!-- Coluna de Exercícios -->
                <div class="col-lg-8 mb-4">
                    <form id="form-tracker-treino">
                        <?php if (!empty($exercicios)): ?>
                            <?php foreach ($exercicios as $eIdx => $ex): ?>
                                <div class="exercise-tracker-card" data-descanso="<?= htmlspecialchars($ex['descanso_segundos']) ?>">
                                    <div class="d-flex justify-content-between align-items-start border-bottom border-secondary pb-2 mb-3">
                                        <div>
                                            <h5 class="text-white fw-bold mb-0">
                                                <span class="text-orange me-2"><?= $eIdx + 1 ?>.</span> 
                                                <?= htmlspecialchars($ex['nome_exercicio']) ?>
                                            </h5>
                                            <span class="text-secondary small">
                                                <i class="fa-solid fa-clock text-muted-gomos me-1"></i> Descanso sugerido: <?= htmlspecialchars($ex['descanso_segundos']) ?>s
                                                <?php if (!empty($ex['observacoes'])): ?>
                                                    • <span class="text-white-50"><?= htmlspecialchars($ex['observacoes']) ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Tabela de Séries (Hevy Style) -->
                                    <div class="table-responsive">
                                        <table class="table table-dark table-borderless align-middle m-0 text-center">
                                            <thead>
                                                <tr class="text-secondary small uppercase" style="font-size: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.08);">
                                                    <th style="width: 60px;">Série</th>
                                                    <th>Anterior</th>
                                                    <th>Peso (kg)</th>
                                                    <th>Reps</th>
                                                    <th style="width: 80px;">Concluído</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $seriesCount = intval($ex['series']);
                                                for ($s = 1; $s <= $seriesCount; $s++): 
                                                ?>
                                                    <tr class="serie-row" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                                        <td class="fw-bold text-orange" style="font-family: 'Bebas Neue'; font-size: 1.1rem;"><?= $s ?></td>
                                                        <td class="text-secondary small">
                                                            <?= htmlspecialchars($ex['peso_kg']) ?> kg x <?= htmlspecialchars($ex['repeticoes']) ?>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                                <input type="number" class="form-control input-tracker-num weight-input" value="<?= htmlspecialchars($ex['peso_kg']) ?>" min="0" step="0.5">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                                <input type="number" class="form-control input-tracker-num reps-input" value="<?= htmlspecialchars($ex['repeticoes']) ?>" min="0">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn-check-serie" title="Marcar Série como Feita">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endfor; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="card-gomos p-5 text-center">
                                <p class="text-secondary m-0">Nenhum exercício cadastrado neste treino.</p>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Coluna Direita - Informações e Ajuda -->
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="card-gomos mb-4">
                        <h5 class="text-white border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-circle-question text-orange me-2"></i> Como Funciona?</h5>
                        <ul class="text-secondary small ps-3 mb-0" style="line-height: 1.6;">
                            <li class="mb-2">Ajuste o <strong>peso</strong> e as <strong>repetições</strong> para cada série realizada.</li>
                            <li class="mb-2">Ao terminar uma série, clique no botão circular <span class="text-lime"><i class="fa-solid fa-circle-check"></i></span>. A linha ficará riscada e tocará um bip de sinalização.</li>
                            <li class="mb-2">Um timer de descanso regressivo iniciará automaticamente no canto da tela.</li>
                            <li class="mb-2">Ao concluir todas as séries, clique em <strong>FINALIZAR</strong> no cabeçalho do tracker para computar seus pontos no ranking do GOMOS.</li>
                        </ul>
                    </div>

                    <div class="card-gomos text-center p-4">
                        <h5 class="text-white mb-2">QUER CANCELAR?</h5>
                        <p class="text-secondary small mb-3">Seus dados atuais de treino não serão gravados se você desistir.</p>
                        <a href="<?= $rootUrl ?>/treinos/fichas" class="btn btn-outline-gomos border-secondary text-secondary w-100"><i class="fa-solid fa-xmark"></i> ABANDONAR TREINO</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Widget do Timer de Descanso -->
<div class="rest-timer-widget" id="rest-timer-box">
    <div>
        <i class="fa-solid fa-clock-rotate-left text-lime fa-spin" style="font-size: 1.25rem;"></i>
    </div>
    <div>
        <span class="text-secondary small d-block uppercase" style="font-size: 0.65rem;">Tempo de Descanso</span>
        <span class="rest-timer-countdown" id="rest-timer-display">00s</span>
    </div>
    <button type="button" class="btn btn-close btn-close-white ms-2" id="btn-close-rest-timer" style="font-size: 0.75rem;"></button>
</div>

<!-- Modal de Confirmação de Finalização (Estilo Tweet Composer / Hevy) -->
<div class="modal fade" id="modalFinalizarTreino" tabindex="-1" aria-labelledby="modalFinalizarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary" style="border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div class="modal-header border-secondary py-3">
                <h5 class="modal-title text-white fw-bold d-flex align-items-center" id="modalFinalizarLabel">
                    <i class="fa-solid fa-paper-plane text-orange me-2"></i> PUBLICAR TREINO REALIZADO
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <!-- Informações rápidas do treino -->
                <div class="d-flex align-items-center gap-3 mb-3 bg-black p-3 rounded border border-secondary">
                    <div class="text-center flex-fill border-end border-secondary">
                        <span class="text-secondary small d-block uppercase font-monospace" style="font-size: 0.65rem; letter-spacing: 1px;">Duração</span>
                        <h4 id="modal-duracao-texto" class="text-lime fw-bold m-0" style="font-family: 'Bebas Neue';">0m</h4>
                    </div>
                    <div class="text-center flex-fill border-end border-secondary">
                        <span class="text-secondary small d-block uppercase font-monospace" style="font-size: 0.65rem; letter-spacing: 1px;">Séries Feitas</span>
                        <h4 id="modal-series-texto" class="text-orange fw-bold m-0" style="font-family: 'Bebas Neue';">0</h4>
                    </div>
                    <div class="text-center flex-fill">
                        <span class="text-secondary small d-block uppercase font-monospace" style="font-size: 0.65rem; letter-spacing: 1px;">Volume Total</span>
                        <h4 id="modal-volume-texto" class="text-lime fw-bold m-0" style="font-family: 'Bebas Neue';">0 kg</h4>
                    </div>
                </div>

                <!-- Tweet Composer layout -->
                <div class="d-flex gap-3">
                    <img src="<?= $root ?>/assets/img/<?= $_SESSION['foto_perfil'] ?? 'default_avatar.png' ?>" alt="Avatar" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover; border: 1.5px solid var(--accent-primary);">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-white small mb-1">
                            <?= htmlspecialchars($_SESSION['nome'] ?? 'Atleta') ?> 
                            <span class="text-secondary fw-normal">@<?= htmlspecialchars($_SESSION['username'] ?? 'atleta') ?></span>
                        </div>
                        <span class="badge bg-secondary text-dark fw-bold mb-2 font-monospace" style="font-size: 0.7rem; background-color: var(--accent-secondary) !important;">
                            REALIZOU: <?= htmlspecialchars($treino['titulo']) ?>
                        </span>
                        
                        <!-- Textarea do Tweet -->
                        <textarea class="form-control bg-transparent border-0 text-white p-0" id="obs-finalizacao" rows="3" placeholder="Como foi o treino de hoje? Adicione seus comentários, feedback de cargas ou evolução..." style="resize: none; font-size: 0.95rem; box-shadow: none; min-height: 80px;" maxlength="280"></textarea>
                    </div>
                </div>

                <!-- Preview da imagem selecionada -->
                <div class="mt-3 position-relative" id="image-preview-container" style="display: none;">
                    <img id="image-preview" src="#" alt="Preview" class="img-fluid rounded border border-secondary" style="max-height: 200px; width: 100%; object-fit: cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle" id="btn-remove-photo" style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Footer do Composer com botão de mídia -->
                <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top border-secondary">
                    <div>
                        <!-- Botão de mídia estilizado -->
                        <input type="file" id="foto-finalizacao" accept="image/*" style="display: none;">
                        <button type="button" class="btn btn-outline-secondary btn-sm text-secondary border-secondary d-flex align-items-center gap-1 hover-orange" onclick="document.getElementById('foto-finalizacao').click()" style="border-radius: 20px; font-size: 0.8rem; padding: 6px 14px; transition: all 0.2s ease;">
                            <i class="fa-solid fa-image text-orange"></i> Adicionar Foto
                        </button>
                    </div>
                    <div class="text-secondary small font-monospace" id="char-counter">280</div>
                </div>
            </div>
            <div class="modal-footer border-secondary py-2">
                <button type="button" class="btn btn-outline-gomos border-secondary text-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 20px;">CANCELAR</button>
                <button type="button" class="btn btn-primary-gomos px-4 py-2 fw-bold" id="btn-salvar-finalizacao" style="border-radius: 20px; font-size: 0.9rem;">
                    <i class="fa-solid fa-share-nodes text-dark me-1"></i> PUBLICAR NO FEED
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Controle do Cronômetro de Treino
    let tempoSegundos = 0;
    const timerDisplay = document.getElementById('tracker-timer');
    
    const cronometro = setInterval(function() {
        tempoSegundos++;
        const horas = Math.floor(tempoSegundos / 3600);
        const minutos = Math.floor((tempoSegundos % 3600) / 60);
        const segundos = tempoSegundos % 60;
        
        const hStr = horas.toString().padStart(2, '0');
        const mStr = minutos.toString().padStart(2, '0');
        const sStr = segundos.toString().padStart(2, '0');
        
        timerDisplay.textContent = `${hStr}:${mStr}:${sStr}`;
    }, 1000);

    // 2. Web Audio API para tocar bip eletrônico
    function playBeep() {
        try {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            const audioCtx = new AudioContextClass();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // Tom mais alto (Lá 5)
            gainNode.gain.setValueAtTime(0.08, audioCtx.currentTime); // Volume discreto
            
            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 0.08); // Dura 80ms
        } catch(e) {
            console.log("Web Audio API bloqueada ou não suportada pelo navegador.");
        }
    }

    // 3. Controle do Temporizador de Descanso
    const restBox = document.getElementById('rest-timer-box');
    const restDisplay = document.getElementById('rest-timer-display');
    const restCloseBtn = document.getElementById('btn-close-rest-timer');
    let restTimerInterval = null;

    function iniciarRestTimer(segundos) {
        clearInterval(restTimerInterval);
        if (segundos <= 0) return;

        restDisplay.textContent = `${segundos}s`;
        restBox.style.display = 'flex';

        restTimerInterval = setInterval(function() {
            segundos--;
            restDisplay.textContent = `${segundos}s`;
            
            if (segundos <= 0) {
                clearInterval(restTimerInterval);
                playBeep(); // Apita ao final do descanso também!
                restBox.style.display = 'none';
            }
        }, 1000);
    }

    restCloseBtn.addEventListener('click', function() {
        clearInterval(restTimerInterval);
        restBox.style.display = 'none';
    });

    // 4. Marcação de Séries Concluídas (Check)
    const checkButtons = document.querySelectorAll('.btn-check-serie');
    checkButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const row = this.closest('.serie-row');
            const weightInput = row.querySelector('.weight-input');
            const repsInput = row.querySelector('.reps-input');
            
            if (this.classList.contains('checked')) {
                // Desmarcar
                this.classList.remove('checked');
                row.classList.remove('serie-concluida');
                if (weightInput) weightInput.disabled = false;
                if (repsInput) repsInput.disabled = false;
            } else {
                // Marcar como feito
                this.classList.add('checked');
                row.classList.add('serie-concluida');
                if (weightInput) weightInput.disabled = true;
                if (repsInput) repsInput.disabled = true;
                
                playBeep(); // Som de confirmação

                // Buscar tempo de descanso sugerido do card do exercício
                const exerciseCard = this.closest('.exercise-tracker-card');
                const descanso = parseInt(exerciseCard.dataset.descanso) || 60;
                iniciarRestTimer(descanso);
            }
        });
    });

    // 5. Finalizar Treino (Abrir Modal)
    const btnFinalizar = document.getElementById('btn-finalizar-treino');
    const modalFinalizar = new bootstrap.Modal(document.getElementById('modalFinalizarTreino'));
    const modalDuracaoTexto = document.getElementById('modal-duracao-texto');
    const modalSeriesTexto = document.getElementById('modal-series-texto');
    const modalVolumeTexto = document.getElementById('modal-volume-texto');

    btnFinalizar.addEventListener('click', function() {
        const minutosTotal = Math.max(1, Math.round(tempoSegundos / 60));
        modalDuracaoTexto.textContent = `${minutosTotal}m`;

        // Calcular estatísticas das séries concluídas
        let totalSeries = 0;
        let volumeTotal = 0;
        document.querySelectorAll('.serie-row').forEach(row => {
            if (row.querySelector('.btn-check-serie').classList.contains('checked')) {
                totalSeries++;
                const weight = parseFloat(row.querySelector('.weight-input').value) || 0;
                const reps = parseInt(row.querySelector('.reps-input').value) || 0;
                volumeTotal += weight * reps;
            }
        });

        modalSeriesTexto.textContent = totalSeries;
        modalVolumeTexto.textContent = `${volumeTotal} kg`;

        modalFinalizar.show();
    });

    // 6. Preview da Imagem no Modal
    const fotoInput = document.getElementById('foto-finalizacao');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview');
    const removePhotoBtn = document.getElementById('btn-remove-photo');

    fotoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    removePhotoBtn.addEventListener('click', function() {
        fotoInput.value = '';
        previewImg.src = '#';
        previewContainer.style.display = 'none';
    });

    // 7. Contador de caracteres no Estilo Twitter
    const textComposer = document.getElementById('obs-finalizacao');
    const charCounter = document.getElementById('char-counter');

    textComposer.addEventListener('input', function() {
        const remaining = 280 - this.value.length;
        charCounter.textContent = remaining;
        if (remaining < 20) {
            charCounter.classList.add('text-danger');
            charCounter.classList.remove('text-secondary');
        } else {
            charCounter.classList.remove('text-danger');
            charCounter.classList.add('text-secondary');
        }
    });

    // 8. Confirmação do Envio e Gravação no Banco (Ajax)
    const btnSalvarFinalizacao = document.getElementById('btn-salvar-finalizacao');
    const rootPath = window.GOMOS_ROOT || '';
    const treinoId = <?= intval($treino['id']) ?>;

    btnSalvarFinalizacao.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> PUBLICANDO...';

        const minutosTotal = Math.max(1, Math.round(tempoSegundos / 60));
        const observacaoText = textComposer.value;

        // Coletar exercícios efetivamente concluídos
        const exerciciosConcluidos = [];
        document.querySelectorAll('.exercise-tracker-card').forEach((card) => {
            const nomeExercicio = card.querySelector('h5').innerText.replace(/^\d+\.\s*/, '').trim();
            const rows = card.querySelectorAll('.serie-row');
            const checkedRows = Array.from(rows).filter(row => row.querySelector('.btn-check-serie').classList.contains('checked'));
            
            if (checkedRows.length > 0) {
                const seriesCount = checkedRows.length;
                const repsList = checkedRows.map(row => row.querySelector('.reps-input').value).join(', ');
                const weights = checkedRows.map(row => parseFloat(row.querySelector('.weight-input').value) || 0);
                const maxWeight = Math.max(...weights, 0);
                const descanso = parseInt(card.dataset.descanso) || 60;
                
                exerciciosConcluidos.push({
                    nome_exercicio: nomeExercicio,
                    series: seriesCount,
                    repeticoes: repsList,
                    peso_kg: maxWeight,
                    descanso_segundos: descanso,
                    observacoes: ''
                });
            }
        });

        // Enviar os dados via POST
        const formData = new FormData();
        formData.append('duracao_minutos', minutosTotal);
        formData.append('observacao', observacaoText);
        formData.append('exercicios', JSON.stringify(exerciciosConcluidos));

        if (fotoInput && fotoInput.files.length > 0) {
            formData.append('foto_treino', fotoInput.files[0]);
        }

        fetch(rootPath + '/treino/finalizar/' + treinoId, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'sucesso') {
                clearInterval(cronometro);
                modalFinalizar.hide();
                window.location.href = rootPath + '/feed';
            } else {
                alert(data.mensagem || 'Ocorreu um erro ao finalizar o treino.');
                btnSalvarFinalizacao.disabled = false;
                btnSalvarFinalizacao.innerHTML = '<i class="fa-solid fa-share-nodes"></i> PUBLICAR NO FEED';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erro de conexão ao salvar treino.');
            btnSalvarFinalizacao.disabled = false;
            btnSalvarFinalizacao.innerHTML = '<i class="fa-solid fa-share-nodes"></i> PUBLICAR NO FEED';
        });
    });
});
</script>
