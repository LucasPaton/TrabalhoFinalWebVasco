<?php
namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Model para gerenciamento de Rankings de usuários.
 */
class RankingModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna o ranking geral nacional (Top 50).
     */
    public function obterRankingGeral($limite = 50) {
        $sql = "SELECT id, nome, username, foto_perfil, cidade, estado, pontos_ranking, total_treinos 
                FROM usuarios 
                WHERE ativo = 1 
                ORDER BY pontos_ranking DESC, total_treinos DESC 
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Retorna o ranking entre o usuário logado e seus amigos aceitos.
     */
    public function obterRankingAmigos($usuario_id) {
        $sql = "SELECT id, nome, username, foto_perfil, cidade, estado, pontos_ranking, total_treinos 
                FROM usuarios 
                WHERE (id = :usuario_id_self OR id IN (
                    SELECT receptor_id FROM amizades WHERE solicitante_id = :usuario_id_solicitante AND status = 'aceita'
                    UNION
                    SELECT solicitante_id FROM amizades WHERE receptor_id = :usuario_id_receptor AND status = 'aceita'
                )) AND ativo = 1
                ORDER BY pontos_ranking DESC, total_treinos DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id_self' => $usuario_id,
            ':usuario_id_solicitante' => $usuario_id,
            ':usuario_id_receptor' => $usuario_id
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Retorna o ranking regional (cidade e/ou estado).
     */
    public function obterRankingRegiao($cidade, $estado, $limite = 50) {
        $sql = "SELECT id, nome, username, foto_perfil, cidade, estado, pontos_ranking, total_treinos 
                FROM usuarios 
                WHERE 1=1 AND ativo = 1";
        
        $params = [];

        if (!empty($cidade)) {
            $sql .= " AND cidade = :cidade";
            $params[':cidade'] = $cidade;
        }

        if (!empty($estado)) {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY pontos_ranking DESC, total_treinos DESC LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Retorna a posição numérica de um usuário no ranking geral nacional.
     */
    public function obterPosicaoGeral($usuario_id) {
        // Obter os pontos do usuário
        $sql_pts = "SELECT pontos_ranking FROM usuarios WHERE id = :id";
        $stmt_pts = $this->db->prepare($sql_pts);
        $stmt_pts->execute([':id' => $usuario_id]);
        $res = $stmt_pts->fetch();
        
        if (!$res) {
            return 0;
        }
        
        $pontos = $res['pontos_ranking'];

        // Conta quantos usuários ativos possuem mais pontos (ou mesmos pontos e ID menor/total de treinos maior)
        $sql_pos = "SELECT COUNT(*) + 1 as posicao 
                    FROM usuarios 
                    WHERE (pontos_ranking > :pontos_comparar OR (pontos_ranking = :pontos_iguais AND total_treinos > (
                        SELECT total_treinos FROM usuarios WHERE id = :id
                    ))) AND ativo = 1";
        
        $stmt_pos = $this->db->prepare($sql_pos);
        $stmt_pos->execute([
            ':pontos_comparar' => $pontos,
            ':pontos_iguais' => $pontos,
            ':id' => $usuario_id
        ]);
        $pos = $stmt_pos->fetch();
        
        return $pos ? $pos['posicao'] : 0;
    }
}
