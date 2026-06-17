<?php
namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Model para gerenciamento de solicitações de amizades e conexões.
 */
class AmizadeModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Envia uma solicitação de amizade.
     */
    public function enviarSolicitacao($solicitante_id, $receptor_id) {
        // Verifica se já existe algum registro
        $status = $this->verificarStatus($solicitante_id, $receptor_id);
        
        if ($status !== 'nenhum') {
            return false;
        }

        $sql = "INSERT INTO amizades (solicitante_id, receptor_id, status) VALUES (:solicitante_id, :receptor_id, 'pendente')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':solicitante_id' => $solicitante_id,
            ':receptor_id' => $receptor_id
        ]);
    }

    /**
     * Aceita uma solicitação de amizade pendente.
     */
    public function aceitarSolicitacao($solicitante_id, $receptor_id) {
        $sql = "UPDATE amizades 
                SET status = 'aceita' 
                WHERE (solicitante_id = :solicitante_id1 AND receptor_id = :receptor_id1) 
                   OR (solicitante_id = :receptor_id2 AND receptor_id = :solicitante_id2)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':solicitante_id1' => $solicitante_id,
            ':receptor_id1' => $receptor_id,
            ':solicitante_id2' => $solicitante_id,
            ':receptor_id2' => $receptor_id
        ]);
    }

    /**
     * Recusa ou remove uma solicitação de amizade.
     */
    public function recusarOuDesfazerAmizade($usuario1_id, $usuario2_id) {
        $sql = "DELETE FROM amizades 
                WHERE (solicitante_id = :user1_1 AND receptor_id = :user2_1) 
                   OR (solicitante_id = :user2_2 AND receptor_id = :user1_2)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user1_1' => $usuario1_id,
            ':user2_1' => $usuario2_id,
            ':user1_2' => $usuario1_id,
            ':user2_2' => $usuario2_id
        ]);
    }

    /**
     * Verifica o status de relacionamento entre dois usuários.
     * Retorna 'pendente_enviado', 'pendente_recebido', 'aceita' ou 'nenhum'
     */
    public function verificarStatus($usuario1_id, $usuario2_id) {
        $sql = "SELECT * FROM amizades 
                WHERE (solicitante_id = :user1_1 AND receptor_id = :user2_1) 
                   OR (solicitante_id = :user2_2 AND receptor_id = :user1_2)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user1_1' => $usuario1_id,
            ':user2_1' => $usuario2_id,
            ':user1_2' => $usuario1_id,
            ':user2_2' => $usuario2_id
        ]);
        $amizade = $stmt->fetch();

        if (!$amizade) {
            return 'nenhum';
        }

        if ($amizade['status'] === 'aceita') {
            return 'aceita';
        }

        // Se pendente, verifica quem enviou
        if ($amizade['solicitante_id'] == $usuario1_id) {
            return 'pendente_enviado';
        } else {
            return 'pendente_recebido';
        }
    }

    /**
     * Retorna a lista de amigos aceitos de um usuário.
     */
    public function listarAmigos($usuario_id) {
        $sql = "SELECT u.id, u.nome, u.username, u.foto_perfil, u.cidade, u.estado, u.nivel_fitness, u.pontos_ranking 
                FROM usuarios u 
                WHERE u.id IN (
                    SELECT receptor_id FROM amizades WHERE solicitante_id = :usuario_id_solicitante AND status = 'aceita'
                    UNION
                    SELECT solicitante_id FROM amizades WHERE receptor_id = :usuario_id_receptor AND status = 'aceita'
                ) AND u.ativo = 1 
                ORDER BY u.nome ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id_solicitante' => $usuario_id,
            ':usuario_id_receptor' => $usuario_id
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Retorna solicitações pendentes recebidas pelo usuário.
     */
    public function listarSolicitacoesPendentes($usuario_id) {
        $sql = "SELECT u.id, u.nome, u.username, u.foto_perfil, a.criado_em 
                FROM usuarios u 
                INNER JOIN amizades a ON a.solicitante_id = u.id 
                WHERE a.receptor_id = :usuario_id AND a.status = 'pendente' AND u.ativo = 1 
                ORDER BY a.criado_em DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }

    /**
     * Retorna sugestões de amigos (excluindo amigos atuais e solicitações pendentes).
     * Dá preferência a usuários da mesma cidade ou mesma academia.
     */
    public function listarSugestoesAmigos($usuario_id, $limite = 5) {
        // Buscar cidade e academia do usuário para refinar sugestões
        $sql_u = "SELECT cidade, academia_id FROM usuarios WHERE id = :id";
        $stmt_u = $this->db->prepare($sql_u);
        $stmt_u->execute([':id' => $usuario_id]);
        $user = $stmt_u->fetch();

        $cidade = $user ? $user['cidade'] : '';
        $academia_id = $user ? $user['academia_id'] : null;

        $sql = "SELECT id, nome, username, foto_perfil, cidade, estado 
                FROM usuarios 
                WHERE id != :usuario_id_excluir 
                  AND ativo = 1 
                  AND id NOT IN (
                      SELECT receptor_id FROM amizades WHERE solicitante_id = :usuario_id_solicitante
                      UNION
                      SELECT solicitante_id FROM amizades WHERE receptor_id = :usuario_id_receptor
                  )
                ORDER BY 
                  CASE WHEN academia_id = :academia_id THEN 1 ELSE 2 END,
                  CASE WHEN cidade = :cidade THEN 1 ELSE 2 END,
                  pontos_ranking DESC 
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario_id_excluir', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id_solicitante', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id_receptor', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':academia_id', $academia_id, PDO::PARAM_INT);
        $stmt->bindValue(':cidade', $cidade, PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
