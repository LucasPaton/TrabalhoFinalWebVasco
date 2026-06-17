<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Validator;
use App\Models\TreinoModel;
use App\Models\ExercicioModel;
use App\Models\ComentarioModel;
use App\Models\UsuarioModel;
use App\Models\AmizadeModel;

/**
 * Controller responsável pela criação, visualização e comparação de treinos.
 */
class TreinoController {
    /**
     * Exibe a tela de criação de treinos (Builder).
     * Se for uma requisição GET com parâmetro 'q', funciona como API autocomplete para os exercícios.
     */
    public function criarPagina() {
        Session::check();

        // Autocomplete de exercícios do catálogo
        if (isset($_GET['q'])) {
            $exModel = new ExercicioModel();
            $query = Validator::sanitize($_GET['q']);
            $resultados = $exModel->pesquisar($query);
            
            header('Content-Type: application/json');
            echo json_encode($resultados);
            exit();
        }

        require_once __DIR__ . '/../views/treino/criar.php';
    }

    /**
     * Salva o treino recém-criado com seus respectivos exercícios.
     */
    public function salvar() {
        Session::check();
        $usuario_id = Session::get('usuario_id');

        $titulo = Validator::sanitize($_POST['titulo'] ?? '');
        $descricao = Validator::sanitize($_POST['descricao'] ?? '');
        $tipo_treino = Validator::sanitize($_POST['tipo_treino'] ?? 'Full');
        $grupo_muscular = Validator::sanitize($_POST['grupo_muscular'] ?? '');
        $duracao_minutos = Validator::sanitizeInt($_POST['duracao_minutos'] ?? 0);
        $nivel_dificuldade = Validator::sanitize($_POST['nivel_dificuldade'] ?? 'iniciante');
        $publico = isset($_POST['publico']) ? intval($_POST['publico']) : 1;

        // Exercícios dinâmicos vindos do formulário
        $nomes_exercicios = $_POST['exercicio_nome'] ?? [];
        $series_arr = $_POST['exercicio_series'] ?? [];
        $repeticoes_arr = $_POST['exercicio_repeticoes'] ?? [];
        $pesos_arr = $_POST['exercicio_peso'] ?? [];
        $descansos_arr = $_POST['exercicio_descanso'] ?? [];
        $observacoes_arr = $_POST['exercicio_obs'] ?? [];

        if (empty($titulo) || empty($grupo_muscular)) {
            Session::setFlash('danger', 'Título do treino e Grupo Muscular são obrigatórios.');
            header("Location: " . BASE_PATH . "/treino/criar");
            exit();
        }

        // Montar array de exercícios formatados
        $exercicios = [];
        for ($i = 0; $i < count($nomes_exercicios); $i++) {
            if (!empty($nomes_exercicios[$i])) {
                $exercicios[] = [
                    'nome_exercicio' => Validator::sanitize($nomes_exercicios[$i]),
                    'series' => Validator::sanitizeInt($series_arr[$i] ?? 3),
                    'repeticoes' => Validator::sanitize($repeticoes_arr[$i] ?? '10'),
                    'peso_kg' => Validator::sanitizeFloat($pesos_arr[$i] ?? 0.00),
                    'descanso_segundos' => Validator::sanitizeInt($descansos_arr[$i] ?? 60),
                    'observacoes' => Validator::sanitize($observacoes_arr[$i] ?? '')
                ];
            }
        }

        $dados = [
            'usuario_id' => $usuario_id,
            'titulo' => $titulo,
            'descricao' => $descricao,
            'tipo_treino' => $tipo_treino,
            'grupo_muscular' => $grupo_muscular,
            'duracao_minutos' => $duracao_minutos,
            'nivel_dificuldade' => $nivel_dificuldade,
            'publico' => $publico,
            'exercicios' => $exercicios
        ];

        try {
            $treinoModel = new TreinoModel();
            $treino_id = $treinoModel->criar($dados);

            if ($treino_id) {
                Session::setFlash('success', 'Treino criado e publicado com sucesso!');
                header("Location: " . BASE_PATH . "/feed");
                exit();
            }
        } catch (\Exception $e) {
            Session::setFlash('danger', 'Erro ao salvar o treino: ' . $e->getMessage());
        }

        header("Location: " . BASE_PATH . "/treino/criar");
        exit();
    }

    /**
     * Exibe os detalhes de um treino.
     * 
     * @param int $id
     */
    public function visualizar($id) {
        Session::check();
        $id = intval($id);
        $usuario_id = Session::get('usuario_id');

        $treinoModel = new TreinoModel();
        
        // Incrementar contagem de visualizações
        $treinoModel->incrementarVisualizacoes($id);

        $treino = $treinoModel->buscarPorId($id, $usuario_id);

        if (!$treino) {
            Session::setFlash('danger', 'Treino não encontrado.');
            header("Location: " . BASE_PATH . "/feed");
            exit();
        }

        // Tabela de exercícios do treino
        $exercicios = $treinoModel->buscarExercicios($id);

        // Comentários do treino
        $comentarioModel = new ComentarioModel();
        $comentarios = $comentarioModel->buscarPorTreino($id);

        require_once __DIR__ . '/../views/treino/visualizar.php';
    }

    /**
     * Exclui um treino próprio.
     * 
     * @param int $id
     */
    public function excluir($id) {
        Session::check();
        $id = intval($id);
        $usuario_id = Session::get('usuario_id');

        $treinoModel = new TreinoModel();
        $sucesso = $treinoModel->excluir($id, $usuario_id);

        if ($sucesso) {
            Session::setFlash('success', 'Treino excluído com sucesso.');
        } else {
            Session::setFlash('danger', 'Não foi possível excluir o treino.');
        }

        header("Location: " . BASE_PATH . "/perfil");
        exit();
    }

    /**
     * Ação AJAX para curtir/descurtir um treino.
     * 
     * @param int $id
     */
    public function curtir($id) {
        Session::check();
        $treino_id = intval($id);
        $usuario_id = Session::get('usuario_id');

        $treinoModel = new TreinoModel();
        $resultado = $treinoModel->toggleCurtida($usuario_id, $treino_id);

        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit();
    }

    /**
     * Comenta em um treino.
     * 
     * @param int $id
     */
    public function comentar($id) {
        Session::check();
        $treino_id = intval($id);
        $usuario_id = Session::get('usuario_id');
        $texto = Validator::sanitize($_POST['texto'] ?? '');

        if (empty($texto)) {
            Session::setFlash('danger', 'O comentário não pode estar vazio.');
            header("Location: " . BASE_PATH . "/treino/" . $treino_id);
            exit();
        }

        $comentarioModel = new ComentarioModel();
        $sucesso = $comentarioModel->criar($treino_id, $usuario_id, $texto);

        if ($sucesso) {
            Session::setFlash('success', 'Comentário enviado!');
        } else {
            Session::setFlash('danger', 'Erro ao enviar comentário.');
        }

        header("Location: " . BASE_PATH . "/treino/" . $treino_id);
        exit();
    }

    /**
     * Clona um treino para as fichas do usuário logado.
     * 
     * @param int $id
     */
    public function copiar($id) {
        Session::check();
        $treino_id = intval($id);
        $usuario_id = Session::get('usuario_id');

        $treinoModel = new TreinoModel();
        $novo_id = $treinoModel->copiarTreino($treino_id, $usuario_id);

        if ($novo_id) {
            Session::setFlash('success', 'Treino copiado com sucesso para a sua ficha!');
            header("Location: " . BASE_PATH . "/treino/" . $novo_id);
        } else {
            Session::setFlash('danger', 'Houve um erro ao copiar o treino.');
            header("Location: " . BASE_PATH . "/feed");
        }
        exit();
    }

    /**
     * Compara dois treinos lado a lado.
     */
    public function comparar() {
        Session::check();
        $usuario_logado_id = Session::get('usuario_id');

        $t1_id = isset($_GET['t1']) ? intval($_GET['t1']) : null;
        $t2_id = isset($_GET['t2']) ? intval($_GET['t2']) : null;

        $treinoModel = new TreinoModel();

        // Se ambos os treinos foram selecionados
        $treino1 = null;
        $treino2 = null;
        $exercicios1 = [];
        $exercicios2 = [];
        
        $volume1 = 0;
        $volume2 = 0;

        if ($t1_id && $t2_id) {
            $treino1 = $treinoModel->buscarPorId($t1_id);
            $treino2 = $treinoModel->buscarPorId($t2_id);

            if ($treino1 && $treino2) {
                $exercicios1 = $treinoModel->buscarExercicios($t1_id);
                $exercicios2 = $treinoModel->buscarExercicios($t2_id);

                // Calcular volume total de treino 1
                foreach ($exercicios1 as $ex) {
                    // Extrair número de repetições (pode ser "10" ou "8 a 12")
                    // Se for faixa, pegamos o maior número ou a média.
                    preg_match_all('!\d+!', $ex['repeticoes'], $matches);
                    $reps = 10;
                    if (!empty($matches[0])) {
                        $reps = intval(end($matches[0])); // Usa o maior número
                    }
                    $volume1 += $ex['series'] * $reps * $ex['peso_kg'];
                }

                // Calcular volume total de treino 2
                foreach ($exercicios2 as $ex) {
                    preg_match_all('!\d+!', $ex['repeticoes'], $matches);
                    $reps = 10;
                    if (!empty($matches[0])) {
                        $reps = intval(end($matches[0]));
                    }
                    $volume2 += $ex['series'] * $reps * $ex['peso_kg'];
                }
            }
        }

        // Carrega a lista de treinos que o usuário pode comparar
        // Ele pode comparar seus próprios treinos ou treinos de seus amigos
        $usuarioModel = new UsuarioModel();
        $meus_treinos = $treinoModel->listarPorUsuario($usuario_logado_id, true);
        
        // Pegar treinos dos amigos
        $amizadeModel = new AmizadeModel();
        $amigos = $amizadeModel->listarAmigos($usuario_logado_id);
        
        $treinos_amigos = [];
        foreach ($amigos as $amigo) {
            $t_amigo = $treinoModel->listarPorUsuario($amigo['id'], false);
            $treinos_amigos = array_merge($treinos_amigos, $t_amigo);
        }

        require_once __DIR__ . '/../views/treino/comparar.php';
    }
}
