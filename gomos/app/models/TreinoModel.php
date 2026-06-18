<?php
namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Model para gerenciamento de treinos.
 */
class TreinoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        // Migração automática para adicionar a coluna 'foto' se ela não existir
        try {
            $this->db->query("SELECT foto FROM treinos LIMIT 1");
        } catch (\PDOException $e) {
            try {
                $this->db->exec("ALTER TABLE treinos ADD COLUMN foto VARCHAR(255) DEFAULT NULL");
            } catch (\Exception $ex) {
                // Silencia
            }
        }
    }

    /**
     * Cria um novo treino no banco de dados.
     */
    public function criar($dados) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO treinos (usuario_id, titulo, descricao, tipo_treino, grupo_muscular, duracao_minutos, nivel_dificuldade, publico, foto) 
                    VALUES (:usuario_id, :titulo, :descricao, :tipo_treino, :grupo_muscular, :duracao_minutos, :nivel_dificuldade, :publico, :foto)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $dados['usuario_id'],
                ':titulo' => $dados['titulo'],
                ':descricao' => $dados['descricao'],
                ':tipo_treino' => $dados['tipo_treino'],
                ':grupo_muscular' => $dados['grupo_muscular'],
                ':duracao_minutos' => $dados['duracao_minutos'],
                ':nivel_dificuldade' => $dados['nivel_dificuldade'],
                ':publico' => $dados['publico'],
                ':foto' => $dados['foto'] ?? null
            ]);

            $treino_id = $this->db->lastInsertId();

            // Inserir os exercícios se houver
            if (!empty($dados['exercicios'])) {
                $this->inserirExercicios($treino_id, $dados['exercicios']);
            }

            // Apenas incrementa estatísticas e prêmios se for treino realizado público (publico = 1)
            if (isset($dados['publico']) && $dados['publico'] == 1) {
                // Atualiza total_treinos no perfil do usuário e soma 10 pontos
                $sql_u = "UPDATE usuarios SET total_treinos = total_treinos + 1, pontos_ranking = pontos_ranking + 10 WHERE id = :usuario_id";
                $stmt_u = $this->db->prepare($sql_u);
                $stmt_u->execute([':usuario_id' => $dados['usuario_id']]);

                // Se for treino de Peito, ganha conquista de "Primeiro Supino" (ID 2 na seed)
                if (stripos($dados['grupo_muscular'], 'peito') !== false) {
                    $uModel = new UsuarioModel();
                    $uModel->desbloquearConquista($dados['usuario_id'], 2);
                }
            }

            $this->db->commit();
            return $treino_id;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Insere os exercícios vinculados a um treino.
     */
    public function inserirExercicios($treino_id, $exercicios) {
        $sql = "INSERT INTO exercicios_treino (treino_id, nome_exercicio, series, repeticoes, peso_kg, descanso_segundos, observacoes, ordem) 
                VALUES (:treino_id, :nome_exercicio, :series, :repeticoes, :peso_kg, :descanso_segundos, :observacoes, :ordem)";
        
        $stmt = $this->db->prepare($sql);
        $ordem = 1;
        foreach ($exercicios as $ex) {
            $stmt->execute([
                ':treino_id' => $treino_id,
                ':nome_exercicio' => $ex['nome_exercicio'],
                ':series' => $ex['series'] ?? 3,
                ':repeticoes' => $ex['repeticoes'] ?? '10',
                ':peso_kg' => $ex['peso_kg'] ?? 0.00,
                ':descanso_segundos' => $ex['descanso_segundos'] ?? 60,
                ':observacoes' => $ex['observacoes'] ?? null,
                ':ordem' => $ordem++
            ]);
        }
    }

    /**
     * Busca um treino por ID.
     */
    public function buscarPorId($id, $usuario_logado_id = null) {
        $sql = "SELECT t.*, u.nome as nome_usuario, u.username, u.foto_perfil, u.nivel_fitness,
                (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id) as total_curtidas,
                (SELECT COUNT(*) FROM comentarios WHERE treino_id = t.id) as total_comentarios";
        
        if ($usuario_logado_id) {
            $sql .= ", (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id AND usuario_id = :logado_id) as curtiu";
        }
        
        $sql .= " FROM treinos t 
                  INNER JOIN usuarios u ON t.usuario_id = u.id 
                  WHERE t.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $params = [':id' => $id];
        if ($usuario_logado_id) {
            $params[':logado_id'] = $usuario_logado_id;
        }
        
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Busca os exercícios pertencentes a um treino.
     */
    public function buscarExercicios($treino_id) {
        $sql = "SELECT * FROM exercicios_treino WHERE treino_id = :treino_id ORDER BY ordem ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':treino_id' => $treino_id]);
        return $stmt->fetchAll();
    }

    /**
     * Retorna a lista de posts de treinos para o Feed do usuário.
     * Inclui apenas treinos realizados públicos (publico = 1) do próprio usuário e de seus amigos.
     */
    public function listarFeed($usuario_id) {
        $sql = "SELECT t.*, u.nome as nome_usuario, u.username, u.foto_perfil, u.nivel_fitness,
                (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id) as total_curtidas,
                (SELECT COUNT(*) FROM comentarios WHERE treino_id = t.id) as total_comentarios,
                (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id AND usuario_id = :usuario_id_curtiu) as curtiu
                FROM treinos t
                INNER JOIN usuarios u ON t.usuario_id = u.id
                WHERE t.publico = 1 AND (
                    t.usuario_id = :usuario_id_dono
                    OR t.usuario_id IN (
                        SELECT solicitante_id FROM amizades WHERE receptor_id = :usuario_id_receptor AND status = 'aceita'
                        UNION
                        SELECT receptor_id FROM amizades WHERE solicitante_id = :usuario_id_solicitante AND status = 'aceita'
                    )
                )
                ORDER BY t.criado_em DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id_curtiu' => $usuario_id,
            ':usuario_id_dono' => $usuario_id,
            ':usuario_id_receptor' => $usuario_id,
            ':usuario_id_solicitante' => $usuario_id
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Lista as fichas de treino (templates/modelos, publico = 0) de um usuário específico.
     */
    public function listarFichasPorUsuario($usuario_id) {
        $sql = "SELECT t.*, u.nome as nome_usuario, u.username, u.foto_perfil,
                (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id) as total_curtidas,
                (SELECT COUNT(*) FROM comentarios WHERE treino_id = t.id) as total_comentarios
                FROM treinos t 
                INNER JOIN usuarios u ON t.usuario_id = u.id 
                WHERE t.usuario_id = :usuario_id AND t.publico = 0
                ORDER BY t.criado_em DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }

    /**
     * Lista os treinos realizados (publico = 1) de um usuário específico.
     */
    public function listarRealizadosPorUsuario($usuario_id, $usuario_logado_id = null) {
        $sql = "SELECT t.*, u.nome as nome_usuario, u.username, u.foto_perfil,
                (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id) as total_curtidas,
                (SELECT COUNT(*) FROM comentarios WHERE treino_id = t.id) as total_comentarios";
        
        if ($usuario_logado_id) {
            $sql .= ", (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id AND usuario_id = :logado_id) as curtiu";
        }
        
        $sql .= " FROM treinos t 
                  INNER JOIN usuarios u ON t.usuario_id = u.id 
                  WHERE t.usuario_id = :usuario_id AND t.publico = 1
                  ORDER BY t.criado_em DESC";
                  
        $stmt = $this->db->prepare($sql);
        $params = [':usuario_id' => $usuario_id];
        if ($usuario_logado_id) {
            $params[':logado_id'] = $usuario_logado_id;
        }
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lista os treinos de um usuário específico.
     * Mantido para compatibilidade retrógrada.
     */
    public function listarPorUsuario($usuario_id, $incluir_privados = false, $usuario_logado_id = null) {
        $sql = "SELECT t.*, u.nome as nome_usuario, u.username, u.foto_perfil,
                (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id) as total_curtidas,
                (SELECT COUNT(*) FROM comentarios WHERE treino_id = t.id) as total_comentarios";
        
        if ($usuario_logado_id) {
            $sql .= ", (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id AND usuario_id = :logado_id) as curtiu";
        }

        $sql .= " FROM treinos t 
                  INNER JOIN usuarios u ON t.usuario_id = u.id 
                  WHERE t.usuario_id = :usuario_id";

        if (!$incluir_privados) {
            $sql .= " AND t.publico = 1";
        }

        $sql .= " ORDER BY t.criado_em DESC";

        $stmt = $this->db->prepare($sql);
        $params = [':usuario_id' => $usuario_id];
        if ($usuario_logado_id) {
            $params[':logado_id'] = $usuario_logado_id;
        }
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Exclui um treino.
     */
    public function excluir($id, $usuario_id = null) {
        // Se informado o usuario_id, verifica se o treino pertence a ele
        if ($usuario_id) {
            $sql_check = "SELECT usuario_id FROM treinos WHERE id = :id";
            $stmt_check = $this->db->prepare($sql_check);
            $stmt_check->execute([':id' => $id]);
            $treino = $stmt_check->fetch();
            if (!$treino || $treino['usuario_id'] != $usuario_id) {
                return false;
            }
        }

        // Deleta o treino (cascade deletará exercicios, curtidas e comentarios se configurado no BD)
        $sql = "DELETE FROM treinos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        if ($usuario_id) {
            // Decrementa contagem de treinos do usuário
            $uModel = new UsuarioModel();
            $uModel->decrementarTreinos($usuario_id);
        }
        return true;
    }

    /**
     * Gerencia a ação de curtir/descurtir (toggle) via AJAX.
     * Retorna a nova contagem de curtidas.
     */
    public function toggleCurtida($usuario_id, $treino_id) {
        // Verifica se já curtiu
        $sql_check = "SELECT id FROM curtidas WHERE usuario_id = :usuario_id AND treino_id = :treino_id";
        $stmt_check = $this->db->prepare($sql_check);
        $stmt_check->execute([':usuario_id' => $usuario_id, ':treino_id' => $treino_id]);
        $curtida = $stmt_check->fetch();

        // Obter dono do treino para atualizar os pontos e conferir conquista
        $sql_owner = "SELECT usuario_id FROM treinos WHERE id = :treino_id";
        $stmt_owner = $this->db->prepare($sql_owner);
        $stmt_owner->execute([':treino_id' => $treino_id]);
        $owner = $stmt_owner->fetch();
        $owner_id = $owner ? $owner['usuario_id'] : null;

        if ($curtida) {
            // Descurtir
            $sql_del = "DELETE FROM curtidas WHERE usuario_id = :usuario_id AND treino_id = :treino_id";
            $stmt_del = $this->db->prepare($sql_del);
            $stmt_del->execute([':usuario_id' => $usuario_id, ':treino_id' => $treino_id]);

            // Remover pontos do dono do treino
            if ($owner_id && $owner_id != $usuario_id) {
                $uModel = new UsuarioModel();
                $uModel->adicionarPontos($owner_id, -2);
            }
            $acao = 'descurtiu';
        } else {
            // Curtir
            $sql_ins = "INSERT INTO curtidas (usuario_id, treino_id) VALUES (:usuario_id, :treino_id)";
            $stmt_ins = $this->db->prepare($sql_ins);
            $stmt_ins->execute([':usuario_id' => $usuario_id, ':treino_id' => $treino_id]);

            // Adicionar pontos (+2) ao dono do treino
            if ($owner_id && $owner_id != $usuario_id) {
                $uModel = new UsuarioModel();
                $uModel->adicionarPontos($owner_id, 2);
                
                // Dar conquista "Atleta Curtido" (ID 5 na seed) se alcançar >= 3 curtidas nesse treino
                $sql_count = "SELECT COUNT(*) as total FROM curtidas WHERE treino_id = :treino_id";
                $stmt_count = $this->db->prepare($sql_count);
                $stmt_count->execute([':treino_id' => $treino_id]);
                $count = $stmt_count->fetch();
                if ($count['total'] >= 3) {
                    $uModel->desbloquearConquista($owner_id, 5);
                }
            }
            $acao = 'curtiu';
        }

        // Atualizar total de curtidas na tabela de treinos
        $sql_up = "UPDATE treinos t SET total_curtidas = (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id) WHERE t.id = :treino_id";
        $stmt_up = $this->db->prepare($sql_up);
        $stmt_up->execute([':treino_id' => $treino_id]);

        // Atualizar total_curtidas geral do usuário
        if ($owner_id) {
            $sql_up_u = "UPDATE usuarios u SET total_curtidas = (SELECT COALESCE(SUM(t.total_curtidas), 0) FROM treinos t WHERE t.usuario_id = u.id) WHERE u.id = :owner_id";
            $stmt_up_u = $this->db->prepare($sql_up_u);
            $stmt_up_u->execute([':owner_id' => $owner_id]);
        }

        // Retornar dados da alteração
        $sql_count = "SELECT total_curtidas FROM treinos WHERE id = :treino_id";
        $stmt_count = $this->db->prepare($sql_count);
        $stmt_count->execute([':treino_id' => $treino_id]);
        $res = $stmt_count->fetch();

        return [
            'status' => 'sucesso',
            'acao' => $acao,
            'total_curtidas' => $res['total_curtidas']
        ];
    }

    /**
     * Adiciona um comentário a um treino.
     */
    public function comentar($usuario_id, $treino_id, $texto) {
        $sql = "INSERT INTO comentarios (treino_id, usuario_id, texto) VALUES (:treino_id, :usuario_id, :texto)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':treino_id' => $treino_id,
            ':usuario_id' => $usuario_id,
            ':texto' => $texto
        ]);

        // Concede +1 ponto de ranking ao comentarista
        $uModel = new UsuarioModel();
        $uModel->adicionarPontos($usuario_id, 1);
        
        // Verifica conquista "Feedback Construtivo" (ID 4 na seed)
        $uModel->desbloquearConquista($usuario_id, 4);

        // Atualiza a tabela de treinos
        $sql_up = "UPDATE treinos SET total_comentarios = total_comentarios + 1 WHERE id = :id";
        $stmt_up = $this->db->prepare($sql_up);
        $stmt_up->execute([':id' => $treino_id]);

        return true;
    }

    /**
     * Busca os comentários de um treino.
     */
    public function buscarComentarios($treino_id) {
        $sql = "SELECT c.*, u.nome as nome_usuario, u.username, u.foto_perfil 
                FROM comentarios c 
                INNER JOIN usuarios u ON c.usuario_id = u.id 
                WHERE c.treino_id = :treino_id 
                ORDER BY c.criado_em ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':treino_id' => $treino_id]);
        return $stmt->fetchAll();
    }

    /**
     * Clona/Copia um treino para a ficha do usuário logado.
     */
    public function copiarTreino($treino_id, $novo_usuario_id) {
        $treino = $this->buscarPorId($treino_id);
        $exercicios = $this->buscarExercicios($treino_id);

        if (!$treino) {
            return false;
        }

        $dados_novo_treino = [
            'usuario_id' => $novo_usuario_id,
            'titulo' => $treino['titulo'] . ' (Copiado)',
            'descricao' => 'Copiado de @' . $treino['username'] . '. ' . $treino['descricao'],
            'tipo_treino' => $treino['tipo_treino'],
            'grupo_muscular' => $treino['grupo_muscular'],
            'duracao_minutos' => $treino['duracao_minutos'],
            'nivel_dificuldade' => $treino['nivel_dificuldade'],
            'publico' => 0, // Por padrão, o copiado é privado até o usuário mudar
            'exercicios' => []
        ];

        foreach ($exercicios as $ex) {
            $dados_novo_treino['exercicios'][] = [
                'nome_exercicio' => $ex['nome_exercicio'],
                'series' => $ex['series'],
                'repeticoes' => $ex['repeticoes'],
                'peso_kg' => $ex['peso_kg'],
                'descanso_segundos' => $ex['descanso_segundos'],
                'observacoes' => $ex['observacoes']
            ];
        }

        return $this->criar($dados_novo_treino);
    }

    /**
     * Incrementa visualizações do treino.
     */
    public function incrementarVisualizacoes($id) {
        $sql = "UPDATE treinos SET total_visualizacoes = total_visualizacoes + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    /**
     * Pesquisa treinos pelo título, descrição ou grupo muscular.
     */
    public function pesquisarTreinos($query, $usuario_id) {
        $sql = "SELECT t.*, u.nome as nome_usuario, u.username, u.foto_perfil, u.nivel_fitness,
                (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id) as total_curtidas,
                (SELECT COUNT(*) FROM comentarios WHERE treino_id = t.id) as total_comentarios,
                (SELECT COUNT(*) FROM curtidas WHERE treino_id = t.id AND usuario_id = :usuario_id_curtiu) as curtiu
                FROM treinos t
                INNER JOIN usuarios u ON t.usuario_id = u.id
                WHERE (t.titulo LIKE :query1 OR t.descricao LIKE :query2 OR t.grupo_muscular LIKE :query3)
                  AND (t.publico = 1 OR t.usuario_id = :usuario_id_dono)
                  AND u.ativo = 1
                ORDER BY t.criado_em DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id_curtiu' => $usuario_id,
            ':usuario_id_dono' => $usuario_id,
            ':query1' => '%' . $query . '%',
            ':query2' => '%' . $query . '%',
            ':query3' => '%' . $query . '%'
        ]);
        return $stmt->fetchAll();
    }
}
