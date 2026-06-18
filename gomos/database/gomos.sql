-- Script de criação do banco de dados GOMOS
CREATE DATABASE IF NOT EXISTS gomos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gomos;

-- Tabela de Academias
CREATE TABLE IF NOT EXISTS academias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    cep VARCHAR(10) NOT NULL,
    telefone VARCHAR(20),
    site VARCHAR(255),
    foto VARCHAR(255) DEFAULT 'default_academia.jpg',
    total_membros INT DEFAULT 0,
    avaliacao_media DECIMAL(3,2) DEFAULT 0.00,
    verificada TINYINT(1) DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT 'default_avatar.png',
    bio TEXT,
    nivel_fitness ENUM('iniciante', 'intermediario', 'avancado') DEFAULT 'iniciante',
    academia_id INT,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    peso DECIMAL(5,2) DEFAULT 0.00,
    altura INT DEFAULT 0,
    total_treinos INT DEFAULT 0,
    total_curtidas INT DEFAULT 0,
    pontos_ranking INT DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acesso TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (academia_id) REFERENCES academias(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabela de Treinos
CREATE TABLE IF NOT EXISTS treinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    tipo_treino ENUM('A', 'B', 'C', 'ABC', 'PPL', 'Full') DEFAULT 'Full',
    grupo_muscular VARCHAR(100) NOT NULL,
    duracao_minutos INT DEFAULT 0,
    nivel_dificuldade ENUM('iniciante', 'intermediario', 'avancado') DEFAULT 'iniciante',
    total_curtidas INT DEFAULT 0,
    total_comentarios INT DEFAULT 0,
    total_visualizacoes INT DEFAULT 0,
    publico TINYINT(1) DEFAULT 1, -- 0=privado / 1=público
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Exercícios do Treino
CREATE TABLE IF NOT EXISTS exercicios_treino (
    id INT AUTO_INCREMENT PRIMARY KEY,
    treino_id INT NOT NULL,
    nome_exercicio VARCHAR(150) NOT NULL,
    series INT NOT NULL DEFAULT 3,
    repeticoes VARCHAR(50) NOT NULL DEFAULT '10',
    peso_kg DECIMAL(5,2) DEFAULT 0.00,
    descanso_segundos INT DEFAULT 60,
    observacoes TEXT,
    ordem INT DEFAULT 0,
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Catálogo de Exercícios
CREATE TABLE IF NOT EXISTS exercicios_catalogo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL UNIQUE,
    grupo_muscular VARCHAR(100) NOT NULL,
    equipamento VARCHAR(100),
    descricao TEXT,
    imagem VARCHAR(255) DEFAULT 'default_exercicio.jpg',
    aprovado TINYINT(1) DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de Curtidas nos Treinos
CREATE TABLE IF NOT EXISTS curtidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    treino_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY curtida_unica (usuario_id, treino_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Comentários
CREATE TABLE IF NOT EXISTS comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    treino_id INT NOT NULL,
    usuario_id INT NOT NULL,
    texto TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Amizades
CREATE TABLE IF NOT EXISTS amizades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitante_id INT NOT NULL,
    receptor_id INT NOT NULL,
    status ENUM('pendente', 'aceita', 'recusada') DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY amizade_unica (solicitante_id, receptor_id),
    FOREIGN KEY (solicitante_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (receptor_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Histórico de Ranking
CREATE TABLE IF NOT EXISTS ranking_historico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    pontos INT DEFAULT 0,
    posicao_geral INT DEFAULT 0,
    posicao_cidade INT DEFAULT 0,
    mes INT NOT NULL,
    ano INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Check-ins em Academias
CREATE TABLE IF NOT EXISTS check_ins_academia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    academia_id INT NOT NULL,
    data_checkin TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    treino_id INT,
    observacao TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (academia_id) REFERENCES academias(id) ON DELETE CASCADE,
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabela de Conquistas (Badges)
CREATE TABLE IF NOT EXISTS conquistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT NOT NULL,
    icone VARCHAR(255) DEFAULT 'default_badge.png',
    pontos_necessarios INT DEFAULT 0
) ENGINE=InnoDB;

-- Tabela de Conquistas dos Usuários
CREATE TABLE IF NOT EXISTS usuario_conquistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    conquista_id INT NOT NULL,
    data_conquista TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY conquista_usuario_unica (usuario_id, conquista_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (conquista_id) REFERENCES conquistas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Administradores
CREATE TABLE IF NOT EXISTS admin_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel_acesso INT DEFAULT 1, -- 1=Moderador, 2=SuperAdmin
    ativo TINYINT(1) DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ==========================================
-- SEED MOCK DATA (DADOS INICIAIS)
-- Todos os usuários e admin usam a senha '123456'
-- Cujo hash é: $2y$10$OvvNjg/OrA6KrZ9BWILAvOQWbijR4MDu5Qq464JELs1zpyt9V2Be.
-- ==========================================

-- 1. Inserir Academias
INSERT INTO academias (nome, endereco, cidade, estado, cep, telefone, site, foto, total_membros, avaliacao_media, verificada) VALUES
('Gomos CT Principal', 'Av. Dante Michelini, 1000', 'Vitória', 'ES', '29060-010', '(27) 3333-1111', 'https://gomos.com.br', 'default_academia.jpg', 3, 4.9, 1),
('Gomos Jardim Camburi', 'Av. Ranulpho Barbosa dos Santos, 500', 'Vitória', 'ES', '29090-120', '(27) 3333-2222', 'https://gomos.com.br', 'default_academia.jpg', 1, 4.7, 1),
('Gomos Praia do Canto', 'Rua Joaquim Lírio, 250', 'Vitória', 'ES', '29055-460', '(27) 3333-3333', 'https://gomos.com.br', 'default_academia.jpg', 1, 4.8, 1),
('Gomos Vila Velha', 'Av. Estudante José Júlio de Souza, 800', 'Vila Velha', 'ES', '29102-010', '(27) 3333-4444', 'https://gomos.com.br', 'default_academia.jpg', 1, 4.6, 1);

-- 2. Inserir Usuários
-- Senha de todos: '123456'
INSERT INTO usuarios (nome, username, email, senha, foto_perfil, bio, nivel_fitness, academia_id, cidade, estado, peso, altura, total_treinos, total_curtidas, pontos_ranking) VALUES
('Ramon Dino', 'ramondino', 'ramon@dino.com', '$2y$10$OvvNjg/OrA6KrZ9BWILAvOQWbijR4MDu5Qq464JELs1zpyt9V2Be.', 'ramon.jpg', 'Em busca do Mr. Olympia. Treino pesado todo dia.', 'avancado', 1, 'Vitória', 'ES', 105.50, 181, 2, 5, 120),
('Renato Cariani', 'rcariani', 'renato@cariani.com', '$2y$10$OvvNjg/OrA6KrZ9BWILAvOQWbijR4MDu5Qq464JELs1zpyt9V2Be.', 'cariani.jpg', 'Professor de química que ama musculação. Foco e disciplina.', 'avancado', 1, 'Vitória', 'ES', 96.00, 180, 1, 4, 95),
('Julio Balestrin', 'julinho', 'julio@balestrin.com', '$2y$10$OvvNjg/OrA6KrZ9BWILAvOQWbijR4MDu5Qq464JELs1zpyt9V2Be.', 'julio.jpg', 'Treinador dos monstros. Aqui o treino é até a falha.', 'avancado', 4, 'Vila Velha', 'ES', 115.00, 185, 1, 3, 80),
('Felipe Franco', 'fefranco', 'felipe@franco.com', '$2y$10$OvvNjg/OrA6KrZ9BWILAvOQWbijR4MDu5Qq464JELs1zpyt9V2Be.', 'felipe.jpg', 'Vem monstro! Quem não esmaga não cresce.', 'avancado', 3, 'Vitória', 'ES', 92.50, 180, 1, 2, 70),
('Carol Borba', 'carolborba', 'carol@borba.com', '$2y$10$OvvNjg/OrA6KrZ9BWILAvOQWbijR4MDu5Qq464JELs1zpyt9V2Be.', 'carol.jpg', 'Profissional de E.F., dicas de treino em casa e academia!', 'intermediario', 2, 'Vitória', 'ES', 62.00, 168, 1, 1, 45),
('Lucas Paton', 'lucaspaton', 'lcspaton@gmail.com', '$2y$10$OvvNjg/OrA6KrZ9BWILAvOQWbijR4MDu5Qq464JELs1zpyt9V2Be.', 'default_avatar.png', 'Focando na hipertrofia e na consistência.', 'iniciante', 1, 'Vitória', 'ES', 78.00, 175, 1, 0, 15);

-- 3. Atualizar total de membros nas academias correspondentes
UPDATE academias SET total_membros = 3 WHERE id = 1; -- Ramon, Cariani, Lucas
UPDATE academias SET total_membros = 1 WHERE id = 2; -- Carol
UPDATE academias SET total_membros = 1 WHERE id = 3; -- Felipe
UPDATE academias SET total_membros = 1 WHERE id = 4; -- Julio

-- 4. Inserir Catálogo de Exercícios
INSERT INTO exercicios_catalogo (nome, grupo_muscular, equipamento, descricao, imagem, aprovado) VALUES
('Supino Reto com Barra', 'Peito', 'Barra', 'Deitado no supino, descer a barra até o peitoral e empurrar.', 'supino_reto.jpg', 1),
('Agachamento Livre', 'Pernas', 'Barra', 'Agachamento com a barra apoiada nos ombros/trapézio.', 'agachamento.jpg', 1),
('Remada Curvada', 'Costas', 'Barra', 'Inclinado para frente, puxar a barra até a altura do abdômen.', 'remada_curvada.jpg', 1),
('Desenvolvimento com Halteres', 'Ombros', 'Halteres', 'Sentado, empurrar os halteres verticalmente acima da cabeça.', 'desenvolvimento.jpg', 1),
('Rosca Direta', 'Bíceps', 'Barra', 'Flexão de cotovelos em pé com a barra reta ou W.', 'rosca_direta.jpg', 1),
('Tríceps Testa com Barra', 'Tríceps', 'Barra', 'Deitado, flexionar os cotovelos trazendo a barra até a testa.', 'triceps_testa.jpg', 1),
('Leg Press 45°', 'Pernas', 'Máquina', 'Empurrar a plataforma inclinada a 45 graus.', 'leg_press.jpg', 1),
('Cadeira Extensora', 'Pernas', 'Máquina', 'Extensão de joelhos sentado na máquina.', 'extensora.jpg', 1),
('Puxada Alta', 'Costas', 'Máquina', 'Puxar a barra do pulley alto até a parte superior do peitoral.', 'puxada_alta.jpg', 1),
('Elevação Lateral', 'Ombros', 'Halteres', 'Elevar os braços lateralmente até a altura dos ombros.', 'elevacao_lateral.jpg', 1);

-- 5. Inserir Treinos
INSERT INTO treinos (usuario_id, titulo, descricao, tipo_treino, grupo_muscular, duracao_minutos, nivel_dificuldade, total_curtidas, total_comentarios, total_visualizacoes, publico) VALUES
(1, 'Peitoral de Ferro - Classic', 'Treino clássico para ganho de volume e densidade no peitoral.', 'A', 'Peito e Ombros', 60, 'avancado', 3, 2, 25, 1),
(1, 'Pernas Esmagadoras', 'Foco em quadríceps e força bruta no agachamento.', 'B', 'Pernas', 75, 'avancado', 2, 1, 15, 1),
(2, 'Costas e Bíceps - Foco Expansão', 'Remadas pesadas e contrações intensas.', 'ABC', 'Costas e Bíceps', 65, 'avancado', 4, 3, 30, 1),
(3, 'Ombros Blindados', 'Foco em deltoides laterais e posteriores para largura.', 'A', 'Ombros', 50, 'avancado', 3, 1, 12, 1),
(4, 'Treino de Peito Hardcore', 'Cargas máximas com execução perfeita.', 'A', 'Peito', 70, 'avancado', 2, 1, 18, 1),
(5, 'Condicionamento Geral Express', 'Circuito de pernas e core para queima rápida.', 'Full', 'Corpo Inteiro', 40, 'intermediario', 1, 0, 10, 1),
(6, 'Treino ABC - Adaptivo A', 'Início da jornada GOMOS. Foco na técnica.', 'A', 'Peito, Ombros e Tríceps', 60, 'iniciante', 0, 0, 3, 1);

-- 6. Exercícios dos Treinos (Tabela exercicios_treino)
-- Treino 1 (Peitoral de Ferro - Ramon Dino - ID 1)
INSERT INTO exercicios_treino (treino_id, nome_exercicio, series, repeticoes, peso_kg, descanso_segundos, observacoes, ordem) VALUES
(1, 'Supino Reto com Barra', 4, '8 a 12', 120.00, 90, 'Subida explosiva, descida controlada.', 1),
(1, 'Desenvolvimento com Halteres', 4, '10', 40.00, 90, 'Manter os cotovelos alinhados.', 2),
(1, 'Elevação Lateral', 4, '12 a 15', 20.00, 60, 'Fazer drop-set na última série.', 3);

-- Treino 2 (Pernas Esmagadoras - Ramon Dino - ID 2)
INSERT INTO exercicios_treino (treino_id, nome_exercicio, series, repeticoes, peso_kg, descanso_segundos, observacoes, ordem) VALUES
(2, 'Agachamento Livre', 5, '6 a 10', 160.00, 120, 'Foco em amplitude total.', 1),
(2, 'Leg Press 45°', 4, '12', 400.00, 90, 'Sem estender totalmente os joelhos.', 2),
(2, 'Cadeira Extensora', 4, '15', 80.00, 60, 'Segurar 2s na contração de pico.', 3);

-- Treino 3 (Costas e Bíceps - Cariani - ID 3)
INSERT INTO exercicios_treino (treino_id, nome_exercicio, series, repeticoes, peso_kg, descanso_segundos, observacoes, ordem) VALUES
(3, 'Remada Curvada', 4, '10', 90.00, 90, 'Manter a coluna bem estabilizada.', 1),
(3, 'Puxada Alta', 4, '12', 80.00, 60, 'Conduzir os cotovelos para baixo.', 2),
(3, 'Rosca Direta', 4, '10', 40.00, 60, 'Evitar roubar com o corpo.', 3);

-- Treino 7 (Treino ABC - Adaptivo A - Lucas Paton - ID 7)
INSERT INTO exercicios_treino (treino_id, nome_exercicio, series, repeticoes, peso_kg, descanso_segundos, observacoes, ordem) VALUES
(7, 'Supino Reto com Barra', 3, '12', 40.00, 60, 'Foco em aprender a técnica.', 1),
(7, 'Desenvolvimento com Halteres', 3, '12', 12.00, 60, 'Movimento controlado.', 2),
(7, 'Tríceps Testa com Barra', 3, '12', 15.00, 60, 'Cuidado com os cotovelos abertos.', 3);

-- 7. Curtidas
INSERT INTO curtidas (usuario_id, treino_id) VALUES
(2, 1), (3, 1), (4, 1), -- Curtiram treino 1 (Dino)
(1, 3), (3, 3), (4, 3), (5, 3), -- Curtiram treino 3 (Cariani)
(1, 4), (2, 4), (5, 4), -- Curtiram treino 4 (Julio)
(1, 5), (3, 5), -- Curtiram treino 5 (Felipe)
(2, 6); -- Curtiram treino 6 (Carol)

-- Atualizar contagem de curtidas nos treinos
UPDATE treinos t SET total_curtidas = (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id);

-- Atualizar total de curtidas recebidas pelos usuários
UPDATE usuarios u SET total_curtidas = (
    SELECT COALESCE(SUM(t.total_curtidas), 0) FROM treinos t WHERE t.usuario_id = u.id
);

-- Atualizar total de treinos dos usuários
UPDATE usuarios u SET total_treinos = (
    SELECT COUNT(*) FROM treinos WHERE usuario_id = u.id
);

-- 8. Comentários
INSERT INTO comentarios (treino_id, usuario_id, texto) VALUES
(1, 2, 'Treino absurdo! Vou copiar essa ficha.'),
(1, 3, 'Volume ideal para peito de Classic. Parabéns!'),
(3, 1, 'Execução da remada curvada desse treino tem que ser limpa, hein Cariani!'),
(3, 4, 'Show! Treino completo de dorsais.'),
(4, 1, 'Deltoides no limite!'),
(5, 2, 'Carga excelente no supino, Felipe.');

-- Atualizar contagem de comentários nos treinos
UPDATE treinos t SET total_comentarios = (SELECT COUNT(*) FROM comentarios WHERE treino_id = t.id);

-- 9. Amizades
INSERT INTO amizades (solicitante_id, receptor_id, status) VALUES
(1, 2, 'aceita'), -- Ramon e Cariani amigos
(1, 3, 'aceita'), -- Ramon e Julio amigos
(1, 4, 'aceita'), -- Ramon e Felipe amigos
(2, 3, 'aceita'), -- Cariani e Julio amigos
(2, 5, 'aceita'), -- Cariani e Carol amigos
(6, 1, 'pendente'), -- Lucas solicitou Ramon
(6, 2, 'aceita'); -- Lucas e Cariani amigos

-- 10. Check-ins em Academias
INSERT INTO check_ins_academia (usuario_id, academia_id, treino_id, observacao) VALUES
(1, 1, 1, 'Treino de peito concluído na melhor do Brasil.'),
(2, 1, 3, 'Sábado também é dia. Costas esmagadas!'),
(3, 4, 4, 'Treino de ombros finalizado na Gaviões.'),
(4, 3, 5, 'Check-in de peito concluído no RJ.'),
(5, 2, 6, 'Treino rápido feito.'),
(6, 1, 7, 'Primeiro treino registrado na Ironberg!');

-- 11. Conquistas
INSERT INTO conquistas (nome, descricao, icone, pontos_necessarios) VALUES
('Pioneiro GOMOS', 'Criou a conta na plataforma oficial.', 'pioneiro.png', 0),
('Primeiro Supino', 'Registrou o primeiro treino de Peito.', 'supino1.png', 10),
('Membro Ativo', 'Realizou o seu primeiro check-in em uma academia vinculada.', 'checkin1.png', 15),
('Feedback Construtivo', 'Deixou seu primeiro comentário em um treino de amigo.', 'feedback.png', 5),
('Atleta Curtido', 'Teve um treino que recebeu pelo menos 3 curtidas.', 'curtido.png', 30),
('Lenda dos Gomos', 'Alcançou mais de 100 pontos no ranking de evolução.', 'lenda.png', 100);

-- 12. Conquistas de Usuários
INSERT INTO usuario_conquistas (usuario_id, conquista_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 5), (1, 6), -- Ramon Dino tem quase todas
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), -- Cariani
(6, 1), (6, 3); -- Lucas tem Pioneiro e Membro Ativo

-- 13. Admin Usuários
INSERT INTO admin_usuarios (nome, email, senha, nivel_acesso) VALUES
('Admin Gomos', 'admin@gomos.com', '$2y$10$OvvNjg/OrA6KrZ9BWILAvOQWbijR4MDu5Qq464JELs1zpyt9V2Be.', 2),
('Moderador Vasco', 'moderador@gomos.com', '$2y$10$OvvNjg/OrA6KrZ9BWILAvOQWbijR4MDu5Qq464JELs1zpyt9V2Be.', 1);
