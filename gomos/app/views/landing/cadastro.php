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
                    <div class="position-relative mb-5 px-4">
                        <!-- Linha de progresso de fundo e de preenchimento ativo -->
                        <div class="progress position-absolute start-50 translate-middle-x" style="height: 4px; width: 88%; background-color: #262626; z-index: 0; pointer-events: none; border-radius: 2px; border: none; top: 22px;">
                            <div id="cadastro-progress-bar" class="progress-bar" role="progressbar" style="width: 0%; height: 100%; background-color: #FF6B00; transition: width 0.4s ease;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between position-relative" style="z-index: 1;">
                            <div class="text-center step-indicator step-active" id="indicator-1">
                                <span class="badge rounded-circle text-dark p-3 d-inline-flex align-items-center justify-content-center" style="font-size: 1.1rem; width: 45px; height: 45px; background-color: #FF6B00; border: 3px solid #1A1A1A; transition: all 0.3s ease;">1</span>
                                <div class="small text-white mt-2 d-none d-md-block fw-bold" style="transition: all 0.3s ease;">Conta</div>
                            </div>
                            <div class="text-center step-indicator" id="indicator-2">
                                <span class="badge rounded-circle text-white p-3 d-inline-flex align-items-center justify-content-center" style="font-size: 1.1rem; width: 45px; height: 45px; background-color: #262626; border: 3px solid #1A1A1A; transition: all 0.3s ease;">2</span>
                                <div class="small text-muted mt-2 d-none d-md-block" style="transition: all 0.3s ease;">Perfil Fitness</div>
                            </div>
                            <div class="text-center step-indicator" id="indicator-3">
                                <span class="badge rounded-circle text-white p-3 d-inline-flex align-items-center justify-content-center" style="font-size: 1.1rem; width: 45px; height: 45px; background-color: #262626; border: 3px solid #1A1A1A; transition: all 0.3s ease;">3</span>
                                <div class="small text-muted mt-2 d-none d-md-block" style="transition: all 0.3s ease;">Unidade GOMOS</div>
                            </div>
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
                                <input type="text" name="nome" id="nome" class="form-control form-control-gomos" placeholder="Ex: Lucas Paton" minlength="3" maxlength="60" pattern="^[a-zA-ZÀ-ÿ\s]+$" title="Apenas letras e espaços." required>
                                <div class="invalid-feedback text-danger">O nome deve conter apenas letras (3 a 60 caracteres).</div>
                            </div>

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label text-muted-gomos">Nome de Usuário (Username) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-secondary">@</span>
                                    <input type="text" name="username" id="username" class="form-control form-control-gomos" placeholder="lucaspaton" minlength="3" maxlength="20" pattern="^[a-zA-Z0-9_\-\.]+$" title="Apenas letras, números, pontos, traços ou sublinhados (sem espaços)." required>
                                </div>
                                <div id="username-feedback" class="form-text mt-1"></div>
                            </div>

                            <!-- E-mail -->
                            <div class="mb-3">
                                <label for="email" class="form-label text-muted-gomos">Endereço de E-mail *</label>
                                <input type="email" name="email" id="email" class="form-control form-control-gomos" placeholder="exemplo@email.com" maxlength="100" required>
                                <div class="invalid-feedback text-danger">Insira um e-mail válido (máximo 100 caracteres).</div>
                            </div>

                            <!-- Senha -->
                            <div class="mb-3">
                                <label for="senha" class="form-label text-muted-gomos">Senha (6 a 32 caracteres) *</label>
                                <input type="password" name="senha" id="senha" class="form-control form-control-gomos" placeholder="Sua senha secreta" minlength="6" maxlength="32" required>
                                <div class="invalid-feedback text-danger">A senha deve ter entre 6 e 32 caracteres.</div>
                            </div>

                            <!-- Confirmar Senha -->
                            <div class="mb-4">
                                <label for="confirmar_senha" class="form-label text-muted-gomos">Confirmar Senha *</label>
                                <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control form-control-gomos" placeholder="Repita a senha" minlength="6" maxlength="32" required>
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
                                    <input type="number" name="peso" id="peso" class="form-control form-control-gomos" placeholder="Ex: 75.5" step="0.1" min="30" max="300">
                                </div>
                                <div class="col-6">
                                    <label for="altura" class="form-label text-muted-gomos">Altura (cm)</label>
                                    <input type="number" name="altura" id="altura" class="form-control form-control-gomos" placeholder="Ex: 175" min="100" max="250">
                                </div>
                            </div>

                            <!-- Bio -->
                            <div class="mb-4">
                                <label for="bio" class="form-label text-muted-gomos">Bio (Escreva algo motivador sobre você)</label>
                                <textarea name="bio" id="bio" class="form-control form-control-gomos" rows="3" placeholder="Foco na hipertrofia e na disciplina diária." maxlength="250"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-gomos btn-anterior" data-prev="1"><i class="fa-solid fa-arrow-left"></i> VOLTAR</button>
                                <button type="button" class="btn btn-primary-gomos btn-proximo" data-next="3">PRÓXIMO PASSO <i class="fa-solid fa-arrow-right"></i></button>
                            </div>
                        </div>

                        <!-- ETAPA 3: ACADEMIA -->
                        <div class="step-pane d-none" id="step-3">
                            <h4 class="text-lime border-bottom border-secondary pb-2 mb-4">Etapa 3 — Localização e Unidade GOMOS</h4>

                            <!-- Cidade e Estado -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="cidade" class="form-label text-muted-gomos">Cidade *</label>
                                    <input type="text" name="cidade" id="cidade" class="form-control form-control-gomos" placeholder="Ex: São Paulo" minlength="2" maxlength="50" required>
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
                                <label for="academia_id" class="form-label text-muted-gomos">Qual unidade da GOMOS você frequenta?</label>
                                <select name="academia_id" id="academia_id" class="form-select form-select-gomos">
                                    <option value="">Busque ou selecione uma unidade...</option>
                                    <?php foreach ($academias as $academia): ?>
                                        <option value="<?= $academia['id'] ?>"><?= $academia['nome'] ?> - <?= $academia['cidade'] ?>/<?= $academia['estado'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-secondary mt-1">Você poderá alterar ou vincular uma unidade depois na busca.</div>
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
            const username = document.getElementById("username").value.trim();
            const email = document.getElementById("email").value.trim();
            const senha = document.getElementById("senha").value;
            const confirmar_senha = document.getElementById("confirmar_senha").value;
            const feedbackSenha = document.getElementById("senha-confirm-feedback");

            if (!nome || !username || !email || !senha || !confirmar_senha) {
                alert("Preencha todos os campos obrigatórios.");
                return false;
            }

            if (nome.length < 3 || nome.length > 60) {
                alert("O nome completo deve conter entre 3 e 60 caracteres.");
                return false;
            }
            if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(nome)) {
                alert("O nome deve conter apenas letras e espaços.");
                return false;
            }

            if (username.length < 3 || username.length > 20) {
                alert("O username deve conter entre 3 e 20 caracteres.");
                return false;
            }
            if (!/^[a-zA-Z0-9_\-\.]+$/.test(username)) {
                alert("O username deve conter apenas letras, números, pontos, traços ou sublinhados.");
                return false;
            }

            if (email.length > 100) {
                alert("O e-mail não pode exceder 100 caracteres.");
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert("Por favor, insira um e-mail válido.");
                return false;
            }

            if (senha.length < 6 || senha.length > 32) {
                alert("A senha deve conter entre 6 e 32 caracteres.");
                return false;
            }

            if (senha !== confirmar_senha) {
                feedbackSenha.textContent = "As senhas não coincidem.";
                return false;
            } else {
                feedbackSenha.textContent = "";
            }

            if (!usernameValido) {
                alert("Por favor, informe um username válido e disponível.");
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
            const pesoVal = parseFloat(document.getElementById("peso").value);
            if (!isNaN(pesoVal) && (pesoVal < 30 || pesoVal > 300)) {
                alert("O peso deve ser um valor entre 30kg e 300kg.");
                return false;
            }
            const alturaVal = parseInt(document.getElementById("altura").value);
            if (!isNaN(alturaVal) && (alturaVal < 100 || alturaVal > 250)) {
                alert("A altura deve ser um valor entre 100cm e 250cm.");
                return false;
            }
            const bioVal = document.getElementById("bio").value;
            if (bioVal.length > 250) {
                alert("A biografia não pode ultrapassar 250 caracteres.");
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

    const progressBar = document.getElementById("cadastro-progress-bar");

    function updateIndicators(activeStep) {
        // Atualiza a linha de progresso (0% na etapa 1, 50% na etapa 2, 100% na etapa 3)
        const progressPercentage = (activeStep - 1) * 50;
        progressBar.style.width = progressPercentage + "%";

        indicators.forEach((ind, index) => {
            const badge = ind.querySelector(".badge");
            const label = ind.querySelector("div");
            
            if (index + 1 === activeStep) {
                // Etapa Ativa
                badge.style.backgroundColor = "#FF6B00";
                badge.style.color = "#0D0D0D";
                badge.classList.remove("text-white");
                badge.classList.add("text-dark");
                
                label.className = "small text-white mt-2 d-none d-md-block fw-bold";
                label.style.color = "#FFF";
            } else if (index + 1 < activeStep) {
                // Etapa Concluída
                badge.style.backgroundColor = "#FF6B00";
                badge.style.color = "#0D0D0D";
                badge.classList.remove("text-white");
                badge.classList.add("text-dark");
                
                label.className = "small mt-2 d-none d-md-block fw-bold";
                label.style.color = "#FF6B00";
            } else {
                // Etapa Futura / Pendente
                badge.style.backgroundColor = "#262626";
                badge.style.color = "#FFF";
                badge.classList.remove("text-dark");
                badge.classList.add("text-white");
                
                label.className = "small text-muted mt-2 d-none d-md-block";
                label.style.color = "#6c757d";
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
