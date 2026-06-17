<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Validator;
use App\Models\RankingModel;
use App\Models\UsuarioModel;

/**
 * Controller responsável pelas exibições de ranking de atletas.
 */
class RankingController {
    /**
     * Renderiza a página de Rankings com as abas: Geral, Amigos e Região.
     */
    public function index() {
        // Garantir autenticação
        Session::check();

        $usuario_id = Session::get('usuario_id');
        $rankingModel = new RankingModel();

        // 1. Obter ranking geral (Top 50 Brasil)
        $rankingGeral = $rankingModel->obterRankingGeral(50);

        // 2. Obter ranking entre amigos
        $rankingAmigos = $rankingModel->obterRankingAmigos($usuario_id);

        // 3. Obter ranking da região (Padrão: Cidade/Estado do usuário logado)
        // Permite filtrar por cidade/estado via query string (GET)
        $cidade = Validator::sanitize($_GET['cidade'] ?? Session::get('cidade'));
        $estado = strtoupper(Validator::sanitize($_GET['estado'] ?? Session::get('estado')));

        $rankingRegiao = $rankingModel->obterRankingRegiao($cidade, $estado, 50);

        // 4. Obter a posição específica do usuário logado para destaque visual
        $posicaoLogado = $rankingModel->obterPosicaoGeral($usuario_id);

        // Mensagem motivacional de 1º lugar se o usuário logado for o campeão do ranking de amigos
        $motivarCampeao = false;
        if (!empty($rankingAmigos) && $rankingAmigos[0]['id'] == $usuario_id) {
            $motivarCampeao = true;
        }

        require_once __DIR__ . '/../views/ranking/index.php';
    }
}
