<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Models\RankingModel;
use App\Models\UsuarioModel;
use App\Config\Database;

/**
 * Controller responsável pelas páginas públicas iniciais.
 */
class LandingController {
    /**
     * Renderiza a Landing Page pública.
     */
    public function index() {
        // Se já estiver logado, redireciona direto para o feed
        if (Session::has('usuario_id')) {
            header("Location: /feed");
            exit();
        }

        // Buscar dados reais para o teaser de ranking da Landing Page
        $rankingModel = new RankingModel();
        $topAtletas = $rankingModel->obterRankingGeral(5); // Top 5

        // Buscar contagem de usuários
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1");
        $res = $stmt->fetch();
        $totalAtletas = 12000 + ($res ? $res['total'] : 0); // Fictício base + real do BD

        // Renderiza a view da landing page
        require_once __DIR__ . '/../views/landing/index.php';
    }

    /**
     * Renderiza a página "Sobre o GOMOS".
     */
    public function sobre() {
        require_once __DIR__ . '/../views/landing/sobre.php';
    }
}
