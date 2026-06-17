<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Validator;
use App\Config\Database;
use App\Models\UsuarioModel;
use App\Models\AcademiaModel;
use App\Models\ExercicioModel;
use App\Models\TreinoModel;

/**
 * Controller responsável pelas operações do Painel Administrativo.
 */
class AdminController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Exibe o dashboard principal com estatísticas e listas para CRUD.
     */
    public function dashboard() {
        // Garantir acesso exclusivo a administradores
        Session::checkAdmin();

        // 1. Estatísticas Gerais (Totais)
        $totais = [];
        $totais['usuarios'] = $this->db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        $totais['treinos'] = $this->db->query("SELECT COUNT(*) FROM treinos")->fetchColumn();
        $totais['academias'] = $this->db->query("SELECT COUNT(*) FROM academias")->fetchColumn();
        $totais['checkins'] = $this->db->query("SELECT COUNT(*) FROM check_ins_academia")->fetchColumn();
        $totais['comentarios'] = $this->db->query("SELECT COUNT(*) FROM comentarios")->fetchColumn();

        // 2. Estatísticas de Cadastro por Mês (últimos 6 meses) para Gráfico
        $sql_grafico = "SELECT MONTHNAME(criado_em) as mes_nome, COUNT(id) as total 
                        FROM usuarios 
                        GROUP BY MONTH(criado_em) 
                        ORDER BY criado_em ASC 
                        LIMIT 6";
        $stmt_grafico = $this->db->query($sql_grafico);
        $dados_grafico = $stmt_grafico->fetchAll();

        // Se o banco for vazio ou tiver poucos dados, prover dados fictícios harmoniosos + reais
        $meses = [];
        $cadastros = [];
        if (count($dados_grafico) < 3) {
            $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'];
            $cadastros = [12, 19, 3, 5, 2, 3]; // mock format
        } else {
            foreach ($dados_grafico as $row) {
                $meses[] = $row['mes_nome'];
                $cadastros[] = $row['total'];
            }
        }

        // 3. Carregar listas para CRUDs
        // Listar usuários
        $usuarios = $this->db->query("SELECT * FROM usuarios ORDER BY criado_em DESC")->fetchAll();

        // Listar academias
        $academiaModel = new AcademiaModel();
        $academias = $academiaModel->listarTodas();

        // Listar catálogo de exercícios
        $exercicioModel = new ExercicioModel();
        $exercicios = $exercicioModel->listarTodosAdmin();

        // Listar conquistas
        $conquistas = $this->db->query("SELECT * FROM conquistas ORDER BY pontos_necessarios ASC")->fetchAll();

        // Listar treinos recentes para moderação
        $treinos = $this->db->query("SELECT t.*, u.username FROM treinos t INNER JOIN usuarios u ON t.usuario_id = u.id ORDER BY t.criado_em DESC")->fetchAll();

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Gerencia ações do CRUD de Usuários (bloquear, ativar, excluir).
     */
    public function gerenciarUsuario($action, $id) {
        Session::checkAdmin();
        $id = intval($id);

        if ($action === 'status') {
            // Inverter status ativo (0 -> 1, 1 -> 0)
            $stmt = $this->db->prepare("SELECT ativo FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $ativo = $stmt->fetchColumn();
            
            $novo_status = $ativo ? 0 : 1;
            $stmt_up = $this->db->prepare("UPDATE usuarios SET ativo = :ativo WHERE id = :id");
            $stmt_up->execute([':ativo' => $novo_status, ':id' => $id]);
            Session::setFlash('success', 'Status do usuário alterado com sucesso!');
        } elseif ($action === 'excluir') {
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            Session::setFlash('success', 'Usuário excluído permanentemente!');
        }

        header("Location: /admin?tab=usuarios");
        exit();
    }

    /**
     * Gerencia ações do CRUD de Academias.
     */
    public function gerenciarAcademia($action) {
        Session::checkAdmin();
        $academiaModel = new AcademiaModel();

        if ($action === 'criar') {
            $nome = Validator::sanitize($_POST['nome'] ?? '');
            $endereco = Validator::sanitize($_POST['endereco'] ?? '');
            $cidade = Validator::sanitize($_POST['cidade'] ?? '');
            $estado = strtoupper(Validator::sanitize($_POST['estado'] ?? ''));
            $cep = Validator::sanitize($_POST['cep'] ?? '');
            $telefone = Validator::sanitize($_POST['telefone'] ?? '');
            $site = Validator::sanitize($_POST['site'] ?? '');
            $verificada = isset($_POST['verificada']) ? 1 : 0;

            if (empty($nome) || empty($endereco) || empty($cidade) || empty($estado) || empty($cep)) {
                Session::setFlash('danger', 'Preencha os dados obrigatórios da academia.');
                header("Location: /admin?tab=academias");
                exit();
            }

            // Upload de foto se houver
            $foto = 'default_academia.jpg';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $newFileName = md5(time() . $_FILES['foto']['name']) . '.' . strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                if (move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../../public/assets/img/' . $newFileName)) {
                    $foto = $newFileName;
                }
            }

            $academiaModel->criar([
                'nome' => $nome,
                'endereco' => $endereco,
                'cidade' => $cidade,
                'estado' => $estado,
                'cep' => $cep,
                'telefone' => $telefone,
                'site' => $site,
                'foto' => $foto,
                'verificada' => $verificada
            ]);
            Session::setFlash('success', 'Academia cadastrada com sucesso!');
        } elseif ($action === 'editar') {
            $id = intval($_POST['id'] ?? 0);
            $nome = Validator::sanitize($_POST['nome'] ?? '');
            $endereco = Validator::sanitize($_POST['endereco'] ?? '');
            $cidade = Validator::sanitize($_POST['cidade'] ?? '');
            $estado = strtoupper(Validator::sanitize($_POST['estado'] ?? ''));
            $cep = Validator::sanitize($_POST['cep'] ?? '');
            $telefone = Validator::sanitize($_POST['telefone'] ?? '');
            $site = Validator::sanitize($_POST['site'] ?? '');
            $verificada = isset($_POST['verificada']) ? 1 : 0;

            $academia_existente = $academiaModel->buscarPorId($id);
            $foto = $academia_existente ? $academia_existente['foto'] : 'default_academia.jpg';

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $newFileName = md5(time() . $_FILES['foto']['name']) . '.' . strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                if (move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../../public/assets/img/' . $newFileName)) {
                    $foto = $newFileName;
                }
            }

            $academiaModel->atualizar($id, [
                'nome' => $nome,
                'endereco' => $endereco,
                'cidade' => $cidade,
                'estado' => $estado,
                'cep' => $cep,
                'telefone' => $telefone,
                'site' => $site,
                'foto' => $foto,
                'verificada' => $verificada
            ]);
            Session::setFlash('success', 'Academia atualizada com sucesso!');
        } elseif ($action === 'verificar') {
            $id = intval($_GET['id'] ?? 0);
            $stmt = $this->db->prepare("SELECT verificada FROM academias WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $verif = $stmt->fetchColumn();
            
            $academiaModel->alterarStatusVerificacao($id, $verif ? 0 : 1);
            Session::setFlash('success', 'Verificação da academia alterada!');
        } elseif ($action === 'excluir') {
            $id = intval($_GET['id'] ?? 0);
            $academiaModel->excluir($id);
            Session::setFlash('success', 'Academia removida!');
        }

        header("Location: /admin?tab=academias");
        exit();
    }

    /**
     * Gerencia ações do CRUD de Catálogo de Exercícios.
     */
    public function gerenciarExercicio($action) {
        Session::checkAdmin();
        $exercicioModel = new ExercicioModel();

        if ($action === 'criar') {
            $nome = Validator::sanitize($_POST['nome'] ?? '');
            $grupo_muscular = Validator::sanitize($_POST['grupo_muscular'] ?? '');
            $equipamento = Validator::sanitize($_POST['equipamento'] ?? '');
            $descricao = Validator::sanitize($_POST['descricao'] ?? '');
            $aprovado = isset($_POST['aprovado']) ? 1 : 0;

            if (empty($nome) || empty($grupo_muscular)) {
                Session::setFlash('danger', 'Nome e Grupo Muscular são obrigatórios.');
                header("Location: /admin?tab=exercicios");
                exit();
            }

            $imagem = 'default_exercicio.jpg';
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $newFileName = md5(time() . $_FILES['imagem']['name']) . '.' . strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], __DIR__ . '/../../public/assets/img/' . $newFileName)) {
                    $imagem = $newFileName;
                }
            }

            $exercicioModel->criar([
                'nome' => $nome,
                'grupo_muscular' => $grupo_muscular,
                'equipamento' => $equipamento,
                'descricao' => $descricao,
                'imagem' => $imagem,
                'aprovado' => $aprovado
            ]);
            Session::setFlash('success', 'Exercício adicionado ao catálogo!');
        } elseif ($action === 'aprovar') {
            $id = intval($_GET['id'] ?? 0);
            $stmt = $this->db->prepare("SELECT aprovado FROM exercicios_catalogo WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $aprov = $stmt->fetchColumn();

            $exercicioModel->alterarStatusAprovacao($id, $aprov ? 0 : 1);
            Session::setFlash('success', 'Status de aprovação do exercício alterado!');
        } elseif ($action === 'excluir') {
            $id = intval($_GET['id'] ?? 0);
            $exercicioModel->excluir($id);
            Session::setFlash('success', 'Exercício excluído do catálogo.');
        }

        header("Location: /admin?tab=exercicios");
        exit();
    }

    /**
     * Gerencia ações do CRUD de Conquistas.
     */
    public function gerenciarConquista($action) {
        Session::checkAdmin();

        if ($action === 'criar') {
            $nome = Validator::sanitize($_POST['nome'] ?? '');
            $descricao = Validator::sanitize($_POST['descricao'] ?? '');
            $pontos = Validator::sanitizeInt($_POST['pontos_necessarios'] ?? 0);

            if (empty($nome) || empty($descricao)) {
                Session::setFlash('danger', 'Nome e descrição são obrigatórios.');
                header("Location: /admin?tab=conquistas");
                exit();
            }

            $icone = 'default_badge.png';
            if (isset($_FILES['icone']) && $_FILES['icone']['error'] === UPLOAD_ERR_OK) {
                $newFileName = md5(time() . $_FILES['icone']['name']) . '.' . strtolower(pathinfo($_FILES['icone']['name'], PATHINFO_EXTENSION));
                if (move_uploaded_file($_FILES['icone']['tmp_name'], __DIR__ . '/../../public/assets/img/' . $newFileName)) {
                    $icone = $newFileName;
                }
            }

            $stmt = $this->db->prepare("INSERT INTO conquistas (nome, descricao, icone, pontos_necessarios) VALUES (:nome, :descricao, :icone, :pontos)");
            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':icone' => $icone,
                ':pontos' => $pontos
            ]);

            Session::setFlash('success', 'Nova conquista criada!');
        }

        header("Location: /admin?tab=conquistas");
        exit();
    }

    /**
     * Modera treinos excluindo treinos inadequados.
     */
    public function excluirTreino($id) {
        Session::checkAdmin();
        $id = intval($id);

        $treinoModel = new TreinoModel();
        // Carrega o treino antes para saber o autor
        $treino = $treinoModel->buscarPorId($id);
        
        if ($treino) {
            $treinoModel->excluir($id, $treino['usuario_id']);
            Session::setFlash('success', 'Treino moderado e excluído com sucesso!');
        } else {
            Session::setFlash('danger', 'Treino não encontrado.');
        }

        header("Location: /admin?tab=treinos");
        exit();
    }
}
