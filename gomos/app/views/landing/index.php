<?php 
$pageTitle = "Treina. Posta. Supera.";
$isLanding = true;
$root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootUrl = rtrim($root, '/public');
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<!-- Seção HERO (Tela Cheia) -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center animate-fade-in appear">
        <h1 class="hero-title text-white">TREINA. POSTA. <span class="text-orange">SUPERA.</span></h1>
        <p class="hero-subtitle text-muted-gomos mx-auto">
            A rede social feita para quem vive a academia. Monte seus treinos, siga amigos, compare volumes de carga e suba no ranking da sua região.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="<?= $rootUrl ?>/cadastro" class="btn btn-primary-gomos px-4 py-3"><i class="fa-solid fa-user-plus"></i> CRIAR CONTA GRÁTIS</a>
            <a href="#como-funciona" class="btn btn-outline-gomos px-4 py-3"><i class="fa-solid fa-arrow-down"></i> VER COMO FUNCIONA</a>
        </div>
    </div>
</section>

<!-- Seção COMO FUNCIONA -->
<section id="como-funciona" class="py-5 bg-dark">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in">
            <h2 class="display-4 text-white">COMO FUNCIONA O <span class="text-lime">GOMOS</span></h2>
            <p class="text-secondary mx-auto" style="max-width: 600px;">Três passos simples para você evoluir sua rotina de musculação e se conectar com outros atletas.</p>
        </div>
        
        <div class="row g-4 pt-4">
            <!-- Passo 1 -->
            <div class="col-md-4 animate-fade-in">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="feature-icon text-orange mt-2"><i class="fa-solid fa-calendar-plus"></i></div>
                    <h4 class="text-white mb-3">📋 Monte Seus Treinos</h4>
                    <p class="text-muted-gomos">Crie fichas de treinos dinâmicas organizando séries, repetições, pesos e intervalos de descanso no nosso builder intuitivo.</p>
                </div>
            </div>
            <!-- Passo 2 -->
            <div class="col-md-4 animate-fade-in">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="feature-icon text-lime mt-2"><i class="fa-solid fa-chart-line"></i></div>
                    <h4 class="text-white mb-3">🏆 Compare e Evolua</h4>
                    <p class="text-muted-gomos">Veja gráficos de comparação de volume total e compare suas cargas e desempenho diretamente com seus amigos e rivais.</p>
                </div>
            </div>
            <!-- Passo 3 -->
            <div class="col-md-4 animate-fade-in">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="feature-icon text-orange mt-2"><i class="fa-solid fa-location-crosshairs"></i></div>
                    <h4 class="text-white mb-3">📍 Descubra Academias</h4>
                    <p class="text-muted-gomos">Encontre novas academias na sua região, faça check-ins para marcar presença, ganhe pontos e descubra quem mais treina lá.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção RECURSOS DESTAQUES (Grid) -->
<section class="py-5" style="background-color: #080808;">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in">
            <h2 class="display-4 text-white">RECURSOS FEITOS PARA <span class="text-orange">EVOLUÇÃO</span></h2>
            <p class="text-secondary">Explore tudo o que a plataforma oferece para maximizar sua jornada.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4 animate-fade-in">
                <div class="feature-box">
                    <span class="feature-icon text-orange"><i class="fa-solid fa-share-nodes"></i></span>
                    <h4 class="text-white">Feed Social de Treinos</h4>
                    <p class="text-muted-gomos">Compartilhe suas fichas de exercícios, comente na rotina de seus amigos, curta e copie os treinos deles para a sua ficha com um clique.</p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-in">
                <div class="feature-box">
                    <span class="feature-icon text-lime"><i class="fa-solid fa-medal"></i></span>
                    <h4 class="text-white">Rankings Gamificados</h4>
                    <p class="text-muted-gomos">Suba de posição nos rankings Geral Nacional, de Amigos ou Regional. Seus treinos, check-ins e interações valem pontos valiosos.</p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-in">
                <div class="feature-box">
                    <span class="feature-icon text-orange"><i class="fa-solid fa-award"></i></span>
                    <h4 class="text-white">Conquistas e Badges</h4>
                    <p class="text-muted-gomos">Desbloqueie conquistas exclusivas à medida que atinge novos recordes de carga, executa mais treinos e domina novos exercícios.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção RANKING PREVIEW (Teaser do Ranking Real) -->
<section class="py-5 bg-dark">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in">
            <h2 class="display-4 text-white">QUEM LIDERA OS <span class="text-lime">GOMOS</span>?</h2>
            <p class="text-secondary">Confira os atletas mais ativos da nossa comunidade neste mês.</p>
        </div>

        <div class="ranking-preview-card animate-fade-in">
            <div class="table-responsive">
                <table class="table table-dark table-hover table-striped ranking-table-preview m-0">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Posição</th>
                            <th scope="col">Atleta</th>
                            <th scope="col">Cidade/Região</th>
                            <th scope="col" class="text-end">Pontuação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $pos = 1;
                        if (!empty($topAtletas)):
                            foreach ($topAtletas as $atleta): 
                        ?>
                            <tr>
                                <td class="text-center fw-bold text-orange" style="font-family: 'Bebas Neue'; font-size: 1.35rem;">
                                    <?php if ($pos === 1): ?><i class="fa-solid fa-crown text-warning"></i> 1º
                                    <?php elseif ($pos === 2): ?>🥈 2º
                                    <?php elseif ($pos === 3): ?>🥉 3º
                                    <?php else: echo $pos . 'º'; endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $root ?>/assets/img/<?= $atleta['foto_perfil'] ?>" alt="Avatar" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover; border: 1.5px solid var(--accent-primary);">
                                        <div>
                                            <span class="fw-bold d-block"><?= $atleta['nome'] ?></span>
                                            <span class="text-muted small">@<?= $atleta['username'] ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $atleta['cidade'] ?> - <?= $atleta['estado'] ?></td>
                                <td class="text-end fw-bold text-lime" style="font-family: 'Bebas Neue'; font-size: 1.25rem;"><?= $atleta['pontos_ranking'] ?> PTS</td>
                            </tr>
                        <?php 
                            $pos++;
                            endforeach; 
                        else:
                        ?>
                            <tr><td colspan="4" class="text-center text-muted">Nenhum atleta listado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?= $rootUrl ?>/cadastro" class="btn btn-outline-gomos btn-sm mt-2">
                    VER MINHA POSIÇÃO NO RANKING →
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Seção DEPOIMENTOS (Carrossel Bootstrap) -->
<section class="py-5" style="background-color: #080808;">
    <div class="container py-5 text-center">
        <h2 class="display-4 text-white mb-5">DEPOIMENTOS DE <span class="text-orange">CAMPEÕES</span></h2>
        
        <div id="depoimentosCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-left text-orange mb-3" style="font-size: 2rem;"></i>
                        <p class="lead text-white-50">"O GOMOS revolucionou meu treino de pernas. Comparar o volume total com o Ramon me motivou a treinar até a falha toda semana!"</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <img src="<?= $root ?>/assets/img/default_avatar.png" alt="User" class="testimonial-avatar me-3">
                            <div class="text-start">
                                <h6 class="m-0 text-white">Rodrigo Silva</h6>
                                <small class="text-orange">São Paulo - SP</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-left text-orange mb-3" style="font-size: 2rem;"></i>
                        <p class="lead text-white-50">"Encontrei a Bluefit no GOMOS e vi que toda a galera do meu bairro treinava lá. Fiz a matrícula no mesmo dia e já vinculei no meu perfil!"</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <img src="<?= $root ?>/assets/img/default_avatar.png" alt="User" class="testimonial-avatar me-3">
                            <div class="text-start">
                                <h6 class="m-0 text-white">Aline Gomes</h6>
                                <small class="text-orange">Curitiba - PR</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-left text-orange mb-3" style="font-size: 2rem;"></i>
                        <p class="lead text-white-50">"Montar minhas fichas de treino A/B/C ficou muito mais dinâmico. Consigo arrastar os exercícios para mudar a ordem e atualizar minhas cargas na hora."</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <img src="<?= $root ?>/assets/img/default_avatar.png" alt="User" class="testimonial-avatar me-3">
                            <div class="text-start">
                                <h6 class="m-0 text-white">Carlos Eduardo</h6>
                                <small class="text-orange">Rio de Janeiro - RJ</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#depoimentosCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#depoimentosCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
        </div>
    </div>
</section>

<!-- Seção CTA FINAL -->
<section class="cta-final-section py-5 text-center">
    <div class="container py-4 position-relative">
        <h2 class="mb-3 text-dark fw-bold">JÁ SÃO <span class="text-white" id="contador-atletas">+<?= number_format($totalAtletas, 0, ',', '.') ?></span> ATLETAS NO GOMOS</h2>
        <p class="lead text-dark-50 mb-4 fw-semibold mx-auto" style="max-width: 600px;">Pare de treinar sozinho. Una-se à maior comunidade de academia do Brasil e comece a superar seus limites hoje.</p>
        <a href="<?= $rootUrl ?>/cadastro" class="btn btn-outline-gomos border-dark text-dark px-5 py-3 fs-5">QUERO ENTRAR AGORA</a>
    </div>
</section>

<!-- Rodapé Principal -->
<footer class="bg-black py-4 border-top border-secondary text-center">
    <div class="container">
        <p class="text-secondary m-0">© <?= date('Y') ?> GOMOS. Feito por amantes do ferro. Todos os direitos reservados.</p>
    </div>
</footer>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
