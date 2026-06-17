<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Models\TreinoModel;
use App\Models\AmizadeModel;
use App\Models\RankingModel;
use App\Models\UsuarioModel;

/**
 * Controller responsável pela página do Feed Social.
 */
class FeedController {
    /**
     * Renderiza o feed de posts dos amigos e do próprio usuário.
     */
    public function index() {
        // Garantir que o usuário está autenticado
        Session::check();

        $usuario_id = Session::get('usuario_id');
        
        // 1. Carregar posts de treinos (próprios + amigos)
        $treinoModel = new TreinoModel();
        $treinos = $treinoModel->listarFeed($usuario_id);

        // Pegar os exercícios (3 primeiros) para cada treino no feed (preview)
        foreach ($treinos as &$t) {
            $exercicios_completos = $treinoModel->buscarExercicios($t['id']);
            $t['exercicios_preview'] = array_slice($exercicios_completos, 0, 3);
            $t['total_exercicios'] = count($exercicios_completos);
        }

        // 2. Carregar ranking resumido dos amigos (Top 5)
        $rankingModel = new RankingModel();
        $rankingAmigosCompleto = $rankingModel->obterRankingAmigos($usuario_id);
        $rankingAmigos = array_slice($rankingAmigosCompleto, 0, 5); // Apenas top 5

        // 3. Carregar sugestões de amigos (não amigos locais/ativos)
        $amizadeModel = new AmizadeModel();
        $sugestoesAmigos = $amizadeModel->listarSugestoesAmigos($usuario_id, 5);

        // 4. Buscar informações completas do usuário logado para o cabeçalho/avatar
        $usuarioModel = new UsuarioModel();
        $usuarioLogado = $usuarioModel->buscarPorId($usuario_id);

        // Carregar a view do feed
        require_once __DIR__ . '/../views/feed/index.php';
    }
}
