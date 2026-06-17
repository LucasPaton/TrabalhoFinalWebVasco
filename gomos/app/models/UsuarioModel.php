<?php
namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Model para gerenciamento de dados de Usuários.
 */
class UsuarioModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Cria um novo usuário no banco de dados.
     * 
     * @param array $dados
     * @return int ID do usuário criado
     */
    public function criar($dados) {
        $sql = "INSERT INTO usuarios (nome, username, email, senha, foto_perfil, bio, nivel_fitness, academia_id, cidade, estado, peso, altura, pontos_ranking) 
                VALUES (:nome, :username, :email, :senha, :foto_perfil, :bio, :nivel_fitness, :academia_id, :cidade, :estado, :peso, :altura, 10)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':username' => $dados['username'],
            ':email' => $dados['email'],
            ':senha' => $dados['senha'],
            ':foto_perfil' => $dados['foto_perfil'] ?? 'default_avatar.png',
            ':bio' => $dados['bio'] ?? null,
            ':nivel_fitness' => $dados['nivel_fitness'] ?? 'iniciante',
            ':academia_id' => $dados['academia_id'] ?? null,
            ':cidade' => $dados['cidade'],
            ':estado' => $dados['estado'],
            ':peso' => $dados['peso'] ?? 0.00,
            ':altura' => $dados['altura'] ?? 0
        ]);

        $usuario_id = $this->db->lastInsertId();

        // Incrementa o número de membros na academia vinculada, se houver
        if (!empty($dados['academia_id'])) {
            $this->incrementarMembrosAcademia($dados['academia_id']);
        }

        // Concede a conquista inicial de Pioneiro (ID 1 na seed)
        $this->desbloquearConquista($usuario_id, 1);

        return $usuario_id;
    }

    /**
     * Busca um usuário pelo ID.
     */
    public function buscarPorId($id) {
        $sql = "SELECT u.*, a.nome as nome_academia 
                FROM usuarios u 
                LEFT JOIN academias a ON u.academia_id = a.id 
                WHERE u.id = :id AND u.ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Busca um usuário pelo username.
     */
    public function buscarPorUsername($username) {
        $sql = "SELECT u.*, a.nome as nome_academia 
                FROM usuarios u 
                LEFT JOIN academias a ON u.academia_id = a.id 
                WHERE u.username = :username AND u.ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    /**
     * Busca um usuário pelo e-mail.
     */
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Verifica se o e-mail ou username já existe.
     */
    public function verificarUnicidade($username, $email, $except_id = null) {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE (username = :username OR email = :email)";
        $params = [':username' => $username, ':email' => $email];

        if ($except_id) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = $except_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] == 0;
    }

    /**
     * Atualiza o perfil do usuário logado.
     */
    public function atualizarPerfil($id, $dados) {
        // Obter academia antiga para decrementar membros se mudar
        $usuario_atual = $this->buscarPorId($id);
        $academia_antiga = $usuario_atual['academia_id'];

        $sql = "UPDATE usuarios 
                SET nome = :nome, bio = :bio, nivel_fitness = :nivel_fitness, 
                    peso = :peso, altura = :altura, cidade = :cidade, estado = :estado";
        
        $params = [
            ':id' => $id,
            ':nome' => $dados['nome'],
            ':bio' => $dados['bio'],
            ':nivel_fitness' => $dados['nivel_fitness'],
            ':peso' => $dados['peso'],
            ':altura' => $dados['altura'],
            ':cidade' => $dados['cidade'],
            ':estado' => $dados['estado']
        ];

        // Se uma nova foto foi enviada
        if (isset($dados['foto_perfil'])) {
            $sql .= ", foto_perfil = :foto_perfil";
            $params[':foto_perfil'] = $dados['foto_perfil'];
        }

        // Se a academia foi alterada
        if (isset($dados['academia_id'])) {
            $sql .= ", academia_id = :academia_id";
            $params[':academia_id'] = $dados['academia_id'] ?: null;
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        // Se mudou de academia
        if (isset($dados['academia_id']) && $dados['academia_id'] != $academia_antiga) {
            if (!empty($academia_antiga)) {
                $this->decrementarMembrosAcademia($academia_antiga);
            }
            if (!empty($dados['academia_id'])) {
                $this->incrementarMembrosAcademia($dados['academia_id']);
                // Se vinculou à academia pela primeira vez, ganha conquista de check-in/membro (ID 3 na seed)
                $this->desbloquearConquista($id, 3);
            }
        }

        return true;
    }

    /**
     * Vincula uma academia ao perfil do usuário logado.
     */
    public function vincularAcademia($usuario_id, $academia_id) {
        $usuario = $this->buscarPorId($usuario_id);
        $academia_antiga = $usuario['academia_id'];

        if ($academia_antiga == $academia_id) {
            return true;
        }

        $sql = "UPDATE usuarios SET academia_id = :academia_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':academia_id' => $academia_id,
            ':id' => $usuario_id
        ]);

        if (!empty($academia_antiga)) {
            $this->decrementarMembrosAcademia($academia_antiga);
        }
        $this->incrementarMembrosAcademia($academia_id);
        
        // Concede pontos de ranking
        $this->adicionarPontos($usuario_id, 10);
        // Concede conquista
        $this->desbloquearConquista($usuario_id, 3);

        return true;
    }

    /**
     * Adiciona pontos ao ranking do usuário.
     */
    public function adicionarPontos($usuario_id, $pontos) {
        $sql = "UPDATE usuarios SET pontos_ranking = pontos_ranking + :pontos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pontos' => $pontos, ':id' => $usuario_id]);

        // Verifica se o usuário bateu a marca de 100 pontos para dar a conquista "Lenda dos Gomos" (ID 6 na seed)
        $usuario = $this->buscarPorId($usuario_id);
        if ($usuario['pontos_ranking'] >= 100) {
            $this->desbloquearConquista($usuario_id, 6);
        }
    }

    /**
     * Incrementa o total de treinos criados.
     */
    public function incrementarTreinos($usuario_id) {
        $sql = "UPDATE usuarios SET total_treinos = total_treinos + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuario_id]);
    }

    /**
     * Decrementa o total de treinos criados.
     */
    public function decrementarTreinos($usuario_id) {
        $sql = "UPDATE usuarios SET total_treinos = GREATEST(0, total_treinos - 1) WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuario_id]);
    }

    /**
     * Incrementa o total de membros de uma academia.
     */
    private function incrementarMembrosAcademia($academia_id) {
        $sql = "UPDATE academias SET total_membros = total_membros + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $academia_id]);
    }

    /**
     * Decrementa o total de membros de uma academia.
     */
    private function decrementarMembrosAcademia($academia_id) {
        $sql = "UPDATE academias SET total_membros = GREATEST(0, total_membros - 1) WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $academia_id]);
    }

    /**
     * Desbloqueia uma conquista para o usuário.
     */
    public function desbloquearConquista($usuario_id, $conquista_id) {
        try {
            $sql = "INSERT INTO usuario_conquistas (usuario_id, conquista_id) VALUES (:usuario_id, :conquista_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id, ':conquista_id' => $conquista_id]);

            // Pegar pontos que a conquista dá e adicionar ao usuário
            $sql_c = "SELECT pontos_necessarios FROM conquistas WHERE id = :conquista_id";
            $stmt_c = $this->db->prepare($sql_c);
            $stmt_c->execute([':conquista_id' => $conquista_id]);
            $conquista = $stmt_c->fetch();

            if ($conquista && $conquista['pontos_necessarios'] > 0) {
                $this->adicionarPontos($usuario_id, $conquista['pontos_necessarios']);
            }
            return true;
        } catch (\PDOException $e) {
            // Se já tiver a conquista, ignora devido ao UNIQUE KEY
            return false;
        }
    }

    /**
     * Retorna todas as conquistas do usuário.
     */
    public function buscarConquistas($usuario_id) {
        $sql = "SELECT c.*, uc.data_conquista 
                FROM conquistas c 
                INNER JOIN usuario_conquistas uc ON uc.conquista_id = c.id 
                WHERE uc.usuario_id = :usuario_id 
                ORDER BY uc.data_conquista DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }

    /**
     * Pesquisa usuários por nome ou username.
     */
    public function pesquisarUsuarios($query, $usuario_logado_id) {
        $sql = "SELECT id, nome, username, foto_perfil, cidade, estado 
                FROM usuarios 
                WHERE (nome LIKE :query OR username LIKE :query) 
                  AND id != :usuario_id AND ativo = 1 
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':query' => '%' . $query . '%',
            ':usuario_id' => $usuario_logado_id
        ]);
        return $stmt->fetchAll();
    }
}
