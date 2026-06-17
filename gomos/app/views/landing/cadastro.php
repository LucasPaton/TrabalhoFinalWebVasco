<?php 
$pageTitle = "Cadastro de Atleta";
$isLanding = true;
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="py-5" style="background-color: var(--bg-dark); min-height: 100vh;">
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card-gomos p-4 p-md-5">
                    <h2 class="text-white text-center mb-4"><span class="text-orange">G</span>OMOS — CADASTRO EM ETAPAS</h2>
                    
                    <!-- Indicador de Etapas -->
                    <div class="d-flex justify-content-between mb-5 px-3">
                        <div class="text-center step-indicator step-active" id="indicator-1">
                            <span class="badge rounded-circle bg-orange text-dark p-3" style="font-size: 1rem; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                            <div class="small text-white mt-2 d-none d-md-block">Conta</div>
                        </div>
                        <div class="text-center step-indicator" id="indicator-2">
                            <span class="badge rounded-circle bg-secondary text-dark p-3" style="font-size: 1rem; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                            <div class="small text-muted mt-2 d-none d-md-block">Perfil Fitness</div>
                        </div>
                        <div class="text-center step-indicator" id="indicator-3">
                            <span class="badge rounded-circle bg-secondary text-dark p-3" style="font-size: 1rem; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                            <div class="small text-muted mt-2 d-none d-md-block">Academia</div>
                        </div>
                    </div>

                    <!-- Formulário Único -->
                    <form action="<?= $rootUrl ?>/cadastro" method="POST" enctype="multipart/form-data" id="form-cadastro">
                        
                        <!-- ETAPA 1: CONTA -->
                        <div class="step-pane" id="step-1">
                            <h4 class="text-lime border-bottom border-secondary pb-2 mb-4">Etapa 1 — Criar Sua Conta</h4>
                            
                            <!-- Nome Completo -->
                            <div class="mb-3">
                                <label for="nome" class="form-label text-muted-gomos">Nome Completo *</label>
                                <input type="text" name="nome" id="nome" class="form-control form-control-gomos" placeholder="Ex: Lucas Paton" required>
                                <div class="invalid-feedback text-danger">Por favor, insira seu nome.</div>
                            </div>

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label text-muted-gomos">Nome de Usuário (Username) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-secondary">@</span>
                                    <input type="text" name="username" id="username" class="form-control form-control-gomos" placeholder="lucaspaton" required>
                                </div>
                                <div id="username-feedback" class="form-text mt-1"></div>
                            </div>

                            <!-- E-mail -->
                            <div class="mb-3">
                                <label for="email" class="form-label text-muted-gomos">Endereço de E-mail *</label>
                                <input type="email" name="email" id="email" class="form-control form-control-gomos" placeholder="exemplo@email.com" required>
                                <div class="invalid-feedback text-danger">Insira um e-mail válido.</div>
                            </div>

                            <!-- Senha -->
                            <div class="mb-3">
                                <label for="senha" class="form-label text-muted-gomos">Senha (Mínimo 6 caracteres) *</label>
                                <input type="password" name="senha" id="senha" class="form-control form-control-gomos" placeholder="Sua senha secreta" required>
                                <div class="invalid-feedback text-danger">A senha deve ter pelo menos 6 caracteres.</div>
                            </div>

                            <!-- Confirmar Senha -->
                            <div class="mb-4">
                                <label for="confirmar_senha" class="form-label text-muted-gomos">Confirmar Senha *</label>
                                <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control form-control-gomos" placeholder="Repita a senha" required>
                                <div id="senha-confirm-feedback" class="form-text text-danger mt-1"></div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary-gomos btn-proximo" data-next="2">PRÓXIMO PASSO <i class="fa-solid fa-arrow-right"></i></button>
                            </div>
                        </div>

                        <!-- ETAPA 2: PERFIL FITNESS -->
                        <div class="step-pane d-none" id="step-2">
                            <h4 class="text-lime border-bottom border-secondary pb-2 mb-4">Etapa 2 — Perfil Fitness</h4>
                            
                            <!-- Foto de Perfil -->
                            <div class="mb-3">
                                <label for="foto_perfil" class="form-label text-muted-gomos">Foto de Perfil</label>
                                <input type="file" name="foto_perfil" id="foto_perfil" class="form-control form-control-gomos" accept="image/*">
                                <div class="form-text text-secondary">Tamanho máximo de 2MB. Formatos aceitos: JPG, JPEG, PNG.</div>
                            </div>

                            <!-- Nível Fitness -->
                            <div class="mb-3">
                                <label for="nivel_fitness" class="form-label text-muted-gomos">Qual o seu nível atual? *</label>
                                <select name="nivel_fitness" id="nivel_fitness" class="form-select form-select-gomos" required>
                                    <option value="iniciante">Iniciante (Começando agora/Pouca técnica)</option>
                                    <option value="intermediario" selected>Intermediário (Treina há alguns meses e conhece exercícios)</option>
                                    <option value="avancado">Avançado (Consistência alta, treina pesado)</option>
                                </select>
                            </div>

                            <!-- Peso e Altura -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="peso" class="form-label text-muted-gomos">Peso Atual (kg)</label>
                                    <input type="number" name="peso" id="peso" class="form-control form-control-gomos" placeholder="Ex: 75.5" step="0.1">
                                </div>
                                <div class="col-6">
                                    <label for="altura" class="form-label text-muted-gomos">Altura (cm)</label>
                                    <input type="number" name="altura" id="altura" class="form-control form-control-gomos" placeholder="Ex: 175" min="100" max="250">
                                </div>
                            </div>

                            <!-- Bio -->
                            <div class="mb-4">
                                <label for="bio" class="form-label text-muted-gomos">Bio (Escreva algo motivador sobre você)</label>
                                <textarea name="bio" id="bio" class="form-control form-control-gomos" rows="3" placeholder="Foco na hipertrofia e na disciplina diária."></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-gomos btn-anterior" data-prev="1"><i class="fa-solid fa-arrow-left"></i> VOLTAR</button>
                                <button type="button" class="btn btn-primary-gomos btn-proximo" data-next="3">PRÓXIMO PASSO <i class="fa-solid fa-arrow-right"></i></button>
                            </div>
                        </div>

                        <!-- ETAPA 3: ACADEMIA -->
                        <div class="step-pane d-none" id="step-3">
                            <h4 class="text-lime border-bottom border-secondary pb-2 mb-4">Etapa 3 — Localização e Academia</h4>

                            <!-- Cidade e Estado -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="cidade" class="form-label text-muted-gomos">Cidade *</label>
                                    <input type="text" name="cidade" id="cidade" class="form-control form-control-gomos" placeholder="Ex: São Paulo" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="estado" class="form-label text-muted-gomos">Estado (UF) *</label>
                                    <select name="estado" id="estado" class="form-select form-select-gomos" required>
                                        <option value="">Selecione...</option>
                                        <option value="AC">AC</option><option value="AL">AL</option><option value="AM">AM</option><option value="AP">AP</option>
                                        <option value="BA">BA</option><option value="CE">CE</option><option value="DF">DF</option><option value="ES">ES</option>
                                        <option value="GO">GO</option><option value="MA">MA</option><option value="MG">MG</option><option value="MS">MS</option>
                                        <option value="MT">MT</option><option value="PA">PA</option><option value="PB">PB</option><option value="PE">PE</option>
                                        <option value="PI">PI</option><option value="PR">PR</option><option value="RJ">RJ</option><option value="RN">RN</option>
                                        <option value="RO">RO</option><option value="RR">RR</option><option value="RS">RS</option><option value="SC">SC</option>
                                        <option value="SE">SE</option><option value="SP">SP</option><option value="TO">TO</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Seleção de Academia -->
                            <div class="mb-4">
                                <label for="academia_id" class="form-label text-muted-gomos">Qual academia você frequenta?</label>
                                <select name="academia_id" id="academia_id" class="form-select form-select-gomos">
                                    <option value="">Busque ou selecione uma academia...</option>
                                    <?php foreach ($academias as $academia): ?>
                                        <option value="<?= $academia['id'] ?>"><?= $academia['nome'] ?> - <?= $academia['cidade'] ?>/<?= $academia['estado'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-secondary mt-1">Você poderá alterar ou vincular uma academia depois na busca.</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-gomos btn-anterior" data-prev="2"><i class="fa-solid fa-arrow-left"></i> VOLTAR</button>
                                <button type="submit" class="btn btn-secondary-gomos text-dark" id="btn-finalizar-cadastro"><i class="fa-solid fa-dumbbell"></i> CONCLUIR E ENTRAR</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btnProximos = document.querySelectorAll(".btn-proximo");
    const btnAnteriores = document.querySelectorAll(".btn-anterior");
    const stepPanes = document.querySelectorAll(".step-pane");
    const indicators = document.querySelectorAll(".step-indicator");
    
    // Rota raiz para AJAX
    const rootPath = window.GOMOS_ROOT || '';

    // Validação de Username via AJAX
    const usernameInput = document.getElementById("username");
    const usernameFeedback = document.getElementById("username-feedback");
    let usernameValido = false;

    usernameInput.addEventListener("blur", function() {
        const username = usernameInput.value.trim();
        if (username.length < 3) {
            usernameFeedback.className = "form-text text-danger";
            usernameFeedback.textContent = "Username deve ter no mínimo 3 caracteres.";
            usernameValido = false;
            return;
        }

        $.ajax({
            url: rootPath + '/cadastro',
            type: 'GET',
            data: { check_username: username },
            success: function(response) {
                if (response.disponivel) {
                    usernameFeedback.className = "form-text text-lime";
                    usernameFeedback.textContent = "Username disponível!";
                    usernameValido = true;
                } else {
                    usernameFeedback.className = "form-text text-danger";
                    usernameFeedback.textContent = "Username já está em uso.";
                    usernameValido = false;
                }
            },
            error: function() {
                usernameFeedback.className = "form-text text-secondary";
                usernameFeedback.textContent = "Não foi possível checar a disponibilidade.";
            }
        });
    });

    // Validar etapa antes de avançar
    function validarEtapa(step) {
        if (step === 1) {
            const nome = document.getElementById("nome").value.trim();
            const email = document.getElementById("email").value.trim();
            const senha = document.getElementById("senha").value;
            const confirmar_senha = document.getElementById("confirmar_senha").value;
            const feedbackSenha = document.getElementById("senha-confirm-feedback");

            if (!nome || !email || !senha || !confirmar_senha) {
                alert("Preencha todos os campos obrigatórios.");
                return false;
            }

            if (senha.length < 6) {
                alert("A senha deve ter no mínimo 6 caracteres.");
                return false;
            }

            if (senha !== confirmar_senha) {
                feedbackSenha.textContent = "As senhas não coincidem.";
                return false;
            } else {
                feedbackSenha.textContent = "";
            }

            if (!usernameValido) {
                alert("Escolha um username disponível.");
                return false;
            }

            return true;
        }
        
        if (step === 2) {
            const nivel = document.getElementById("nivel_fitness").value;
            if (!nivel) {
                alert("Selecione o seu nível fitness.");
                return false;
            }
            return true;
        }

        return true;
    }

    // Lógica para avançar de etapa
    btnProximos.forEach(btn => {
        btn.addEventListener("click", function() {
            const nextStep = parseInt(this.getAttribute("data-next"));
            const currentStep = nextStep - 1;

            if (validarEtapa(currentStep)) {
                // Esconder todos os painéis e exibir o próximo
                stepPanes.forEach(pane => pane.classList.add("d-none"));
                document.getElementById("step-" + nextStep).classList.remove("d-none");

                // Atualizar indicadores
                updateIndicators(nextStep);
            }
        });
    });

    // Lógica para retroceder etapa
    btnAnteriores.forEach(btn => {
        btn.addEventListener("click", function() {
            const prevStep = parseInt(this.getAttribute("data-prev"));
            
            stepPanes.forEach(pane => pane.classList.add("d-none"));
            document.getElementById("step-" + prevStep).classList.remove("d-none");

            updateIndicators(prevStep);
        });
    });

    function updateIndicators(activeStep) {
        indicators.forEach((ind, index) => {
            const badge = ind.querySelector(".badge");
            const label = ind.querySelector("div");
            
            if (index + 1 === activeStep) {
                badge.className = "badge rounded-circle bg-orange text-dark p-3";
                label.className = "small text-white mt-2 d-none d-md-block";
            } else if (index + 1 < activeStep) {
                badge.className = "badge rounded-circle bg-lime text-dark p-3";
                label.className = "small text-lime mt-2 d-none d-md-block";
            } else {
                badge.className = "badge rounded-circle bg-secondary text-dark p-3";
                label.className = "small text-muted mt-2 d-none d-md-block";
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
