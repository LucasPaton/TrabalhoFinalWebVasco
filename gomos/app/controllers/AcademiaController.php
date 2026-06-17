<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Validator;
use App\Models\AcademiaModel;
use App\Models\UsuarioModel;
use App\Models\TreinoModel;

/**
 * Controller responsável pela busca, check-in e vinculação de academias.
 */
class AcademiaController {
    /**
     * Exibe a busca de academias e os detalhes de uma academia específica.
     */
    public function buscar() {
        Session::check();
        $usuario_id = Session::get('usuario_id');

        $academiaModel = new AcademiaModel();
        $treinoModel = new TreinoModel();
        
        // 1. Verificar se o usuário quer visualizar uma academia específica
        $academia = null;
        $membros = [];
        $id_academia = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        if ($id_academia) {
            $academia = $academiaModel->buscarPorId($id_academia);
            if ($academia) {
                $membros = $academiaModel->listarMembros($id_academia);
            }
        }

        // 2. Filtros de busca avançada
        $nome = Validator::sanitize($_GET['nome'] ?? '');
        $cidade = Validator::sanitize($_GET['cidade'] ?? '');
        $estado = strtoupper(Validator::sanitize($_GET['estado'] ?? ''));

        if (!empty($nome) || !empty($cidade) || !empty($estado)) {
            $academias = $academiaModel->pesquisarAvancado($nome, $cidade, $estado);
        } else {
            $academias = $academiaModel->listarTodas();
        }

        // 3. Obter academias mais frequentadas (check-ins no mês)
        $academiasMaisFrequentadas = $academiaModel->academiasMaisFrequentadas(5);

        // 4. Carregar treinos do usuário logado para o modal de check-in
        $meus_treinos = $treinoModel->listarPorUsuario($usuario_id, true);

        require_once __DIR__ . '/../views/academia/buscar.php';
    }

    /**
     * Vincula a academia ao perfil do usuário logado.
     * 
     * @param int $id ID da academia
     */
    public function vincular($id) {
        Session::check();
        $usuario_id = Session::get('usuario_id');
        $academia_id = intval($id);

        $usuarioModel = new UsuarioModel();
        $sucesso = $usuarioModel->vincularAcademia($usuario_id, $academia_id);

        if ($sucesso) {
            // Atualizar academia_id na sessão do usuário
            Session::set('academia_id', $academia_id);
            Session::setFlash('success', 'Sua academia foi alterada com sucesso!');
        } else {
            Session::setFlash('danger', 'Erro ao vincular academia.');
        }

        header("Location: /academias?id=" . $academia_id);
        exit();
    }

    /**
     * Registra um check-in de treino na academia.
     */
    public function checkIn() {
        Session::check();
        $usuario_id = Session::get('usuario_id');

        $academia_id = Validator::sanitizeInt($_POST['academia_id'] ?? 0);
        $treino_id = Validator::sanitizeInt($_POST['treino_id'] ?? 0);
        $observacao = Validator::sanitize($_POST['observacao'] ?? '');

        if ($academia_id <= 0) {
            Session::setFlash('danger', 'Academia inválida para check-in.');
            header("Location: /academias");
            exit();
        }

        $academiaModel = new AcademiaModel();
        $sucesso = $academiaModel->registrarCheckin($usuario_id, $academia_id, $treino_id ?: null, $observacao);

        if ($sucesso) {
            Session::setFlash('success', '🏋️ Check-in registrado com sucesso! +5 pontos no Ranking.');
        } else {
            Session::setFlash('danger', 'Houve um erro ao registrar seu check-in.');
        }

        header("Location: /academias?id=" . $academia_id);
        exit();
    }
}
