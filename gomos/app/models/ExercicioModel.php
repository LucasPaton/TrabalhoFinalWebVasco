<?php
namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Model para gerenciamento do catálogo de exercícios.
 */
class ExercicioModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os exercícios aprovados do catálogo.
     */
    public function listarAprovados() {
        $sql = "SELECT * FROM exercicios_catalogo WHERE aprovado = 1 ORDER BY nome ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Pesquisa exercícios no catálogo para o autocomplete (AJAX).
     */
    public function pesquisar($query) {
        $sql = "SELECT id, nome, grupo_muscular, equipamento 
                FROM exercicios_catalogo 
                WHERE aprovado = 1 AND nome LIKE :query 
                ORDER BY nome ASC 
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':query' => '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Busca um exercício por ID.
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM exercicios_catalogo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Cria um novo exercício no catálogo (pode ser enviado por usuário ou admin).
     */
    public function criar($dados) {
        $sql = "INSERT INTO exercicios_catalogo (nome, grupo_muscular, equipamento, descricao, imagem, aprovado) 
                VALUES (:nome, :grupo_muscular, :equipamento, :descricao, :imagem, :aprovado)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome' => $dados['nome'],
            ':grupo_muscular' => $dados['grupo_muscular'],
            ':equipamento' => $dados['equipamento'] ?? null,
            ':descricao' => $dados['descricao'] ?? null,
            ':imagem' => $dados['imagem'] ?? 'default_exercicio.jpg',
            ':aprovado' => $dados['aprovado'] ?? 0
        ]);
    }

    /**
     * Atualiza um exercício do catálogo.
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE exercicios_catalogo 
                SET nome = :nome, grupo_muscular = :grupo_muscular, equipamento = :equipamento, 
                    descricao = :descricao, imagem = :imagem, aprovado = :aprovado 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nome' => $dados['nome'],
            ':grupo_muscular' => $dados['grupo_muscular'],
            ':equipamento' => $dados['equipamento'] ?? null,
            ':descricao' => $dados['descricao'] ?? null,
            ':imagem' => $dados['imagem'] ?? 'default_exercicio.jpg',
            ':aprovado' => $dados['aprovado'] ?? 0
        ]);
    }

    /**
     * Exclui um exercício do catálogo.
     */
    public function excluir($id) {
        $sql = "DELETE FROM exercicios_catalogo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Aprova ou reprova um exercício do catálogo.
     */
    public function alterarStatusAprovacao($id, $aprovado) {
        $sql = "UPDATE exercicios_catalogo SET aprovado = :aprovado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':aprovado' => $aprovado]);
    }

    /**
     * Lista todos os exercícios cadastrados para visualização do Admin.
     */
    public function listarTodosAdmin() {
        $sql = "SELECT * FROM exercicios_catalogo ORDER BY aprovado ASC, nome ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
