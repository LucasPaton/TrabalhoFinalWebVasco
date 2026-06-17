// Script para montagem e construtor dinâmico de treinos - GOMOS

$(document).ready(function() {
    // 1. Configurar jQuery UI Sortable para ordenar exercícios arrastando
    if ($("#exercicios-container").length > 0) {
        $("#exercicios-container").sortable({
            handle: ".drag-handle",
            placeholder: "sortable-placeholder",
            update: function(event, ui) {
                renumerarExercicios();
            }
        });
    }

    // 2. Inicializar Autocomplete para campos existentes
    $(".exercicio-autocomplete").each(function() {
        vincularAutocomplete(this);
    });

    // 3. Botão de Adicionar Exercício Dinamicamente
    $("#btn-add-exercicio").click(function(e) {
        e.preventDefault();
        const exercicioCount = $(".builder-exercise-card").length + 1;

        const cardHTML = `
            <div class="builder-exercise-card card-gomos mb-3" id="ex_card_${exercicioCount}">
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
                                    <input type="text" name="exercicio_nome[]" class="form-control form-control-gomos exercicio-autocomplete" placeholder="Ex: Supino Reto, Agachamento..." required>
                                </div>
                            </div>
                            
                            <!-- Séries -->
                            <div class="col-md-2 col-6">
                                <label class="form-label text-muted-gomos small">Séries</label>
                                <input type="number" name="exercicio_series[]" class="form-control form-control-gomos" value="3" min="1" required>
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
                                <input type="text" name="exercicio_obs[]" class="form-control form-control-gomos" placeholder="Ex: Concentrar na descida, dropset na última série...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $("#exercicios-container").append(cardHTML);
        
        // Vincular autocomplete ao novo input inserido no DOM
        const novoInput = $("#ex_card_" + exercicioCount).find(".exercicio-autocomplete")[0];
        vincularAutocomplete(novoInput);
        
        renumerarExercicios();
    });

    // 4. Excluir Exercício da Lista
    $(document).on('click', '.btn-remove-exercicio', function(e) {
        e.preventDefault();
        
        // Impedir de excluir se for o único exercício na lista (mínimo de 1 necessário)
        if ($(".builder-exercise-card").length <= 1) {
            alert("O treino precisa de pelo menos 1 exercício!");
            return;
        }

        $(this).closest(".builder-exercise-card").fadeOut(300, function() {
            $(this).remove();
            renumerarExercicios();
        });
    });

    // Função de auxílio para vincular o autocomplete do jQuery UI
    function vincularAutocomplete(elemento) {
        const rootPath = window.GOMOS_ROOT || '';
        
        $(elemento).autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: rootPath + '/treino/criar',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: `${item.nome} (${item.grupo_muscular} - ${item.equipamento})`,
                                value: item.nome
                            };
                        }));
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $(this).val(ui.item.value);
                return false;
            }
        });
    }

    // Renumerar índices ou classes internas se for necessário
    function renumerarExercicios() {
        // Reservado para operações futuras de indexação
    }
});
