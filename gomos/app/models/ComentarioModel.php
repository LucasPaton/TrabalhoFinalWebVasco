<?php
namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Model para gerenciamento de Comentários em treinos.
 */
class ComentarioModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os comentários de um treino.
     */
    public function buscarPorTreino($treino_id) {
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
     * Adiciona um comentário.
     */
    public function criar($treino_id, $usuario_id, $texto) {
        $sql = "INSERT INTO comentarios (treino_id, usuario_id, texto) VALUES (:treino_id, :usuario_id, :texto)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':treino_id' => $treino_id,
            ':usuario_id' => $usuario_id,
            ':texto' => $texto
        ]);

        if ($result) {
            // Atualiza o contador na tabela treinos
            $sql_up = "UPDATE treinos SET total_comentarios = total_comentarios + 1 WHERE id = :treino_id";
            $stmt_up = $this->db->prepare($sql_up);
            $stmt_up->execute([':treino_id' => $treino_id]);

            // Concede +1 ponto ao comentarista
            $uModel = new UsuarioModel();
            $uModel->adicionarPontos($usuario_id, 1);
            $uModel->desbloquearConquista($usuario_id, 4); // Conquista feedback
        }

        return $result;
    }

    /**
     * Exclui um comentário.
     */
    public function excluir($id, $treino_id) {
        $sql = "DELETE FROM comentarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([':id' => $id]);

        if ($result) {
            // Atualiza o contador na tabela treinos
            $sql_up = "UPDATE treinos SET total_comentarios = GREATEST(0, total_comentarios - 1) WHERE id = :treino_id";
            $stmt_up = $this->db->prepare($sql_up);
            $stmt_up->execute([':treino_id' => $treino_id]);
        }

        return $result;
    }
}
