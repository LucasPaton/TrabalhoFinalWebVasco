<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Validator;
use App\Models\TreinoModel;
use App\Models\ExercicioModel;
use App\Models\ComentarioModel;
use App\Models\UsuarioModel;
use App\Models\AmizadeModel;
use App\Models\AcademiaModel;

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
        $publico = 0; // Forçado 0 (Ficha/Template, não publicado no Feed)

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
        $exModel = new ExercicioModel();
        for ($i = 0; $i < count($nomes_exercicios); $i++) {
            if (!empty($nomes_exercicios[$i])) {
                $nome_ex = Validator::sanitize($nomes_exercicios[$i]);
                
                // Verificar se o exercício já existe no catálogo
                $db = \App\Config\Database::getConnection();
                $stmt_check = $db->prepare("SELECT COUNT(*) FROM exercicios_catalogo WHERE LOWER(nome) = LOWER(:nome)");
                $stmt_check->execute([':nome' => $nome_ex]);
                $existe = $stmt_check->fetchColumn() > 0;
                
                if (!$existe) {
                    // Inserir no catálogo dinamicamente
                    $exModel->criar([
                        'nome' => $nome_ex,
                        'grupo_muscular' => $grupo_muscular, // Usa o grupo muscular do treino como grupo do exercício
                        'equipamento' => 'Outros',
                        'descricao' => 'Cadastrado dinamicamente ao criar ficha de treino.',
                        'aprovado' => 1 // Aprovado automaticamente para constar no autocomplete do site
                    ]);
                }

                $exercicios[] = [
                    'nome_exercicio' => $nome_ex,
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

    /**
     * Busca de treinos por AJAX (retorna JSON para a sidebar).
     * Endpoint: GET /treinos/buscar?q=termo
     */
    public function buscar() {
        Session::check();
        $usuario_id = Session::get('usuario_id');
        $query = trim($_GET['q'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if (strlen($query) < 2) {
            echo json_encode(['resultados' => []]);
            exit();
        }

        $treinoModel = new TreinoModel();
        $resultados = $treinoModel->pesquisarTreinos($query, $usuario_id);

        $root = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $rootUrl = rtrim($root, '/public');

        $formatados = [];
        foreach ($resultados as $r) {
            $formatados[] = [
                'id' => $r['id'],
                'titulo' => $r['titulo'],
                'grupo_muscular' => $r['grupo_muscular'],
                'username' => $r['username'],
                'treino_url' => $rootUrl . '/treino/' . $r['id']
            ];
        }

        echo json_encode(['resultados' => $formatados]);
        exit();
    }

    /**
     * Tela dedicada de pesquisa de treinos.
     * Endpoint: GET /treinos/pesquisar?q=termo
     */
    public function pesquisar() {
        Session::check();
        $usuario_id = Session::get('usuario_id');
        $query = trim($_GET['q'] ?? '');

        $treinos = [];
        $treinoModel = new TreinoModel();

        if (strlen($query) >= 2) {
            $treinos = $treinoModel->pesquisarTreinos($query, $usuario_id);
            
            // Carregar preview dos exercícios para cada treino
            foreach ($treinos as &$t) {
                $t['exercicios_preview'] = $treinoModel->buscarExercicios($t['id']);
                $t['total_exercicios'] = count($t['exercicios_preview']);
                $t['exercicios_preview'] = array_slice($t['exercicios_preview'], 0, 3);
            }
        }

        require_once __DIR__ . '/../views/treino/pesquisar.php';
    }

    /**
     * Lista todas as fichas de treino do próprio usuário.
     * Endpoint: GET /treinos/fichas
     */
    public function fichasUsuario() {
        Session::check();
        $usuario_id = Session::get('usuario_id');
        $treinoModel = new TreinoModel();
        
        // Busca todos os treinos do usuário (incluindo os privados)
        $treinos = $treinoModel->listarPorUsuario($usuario_id, true, $usuario_id);
        
        // Carregar exercícios e estatísticas de cada ficha
        foreach ($treinos as &$t) {
            $t['exercicios'] = $treinoModel->buscarExercicios($t['id']);
            $t['total_exercicios'] = count($t['exercicios']);
        }
        
        require_once __DIR__ . '/../views/treino/fichas.php';
    }

    /**
     * Tela de rastreamento de treino ativo (Workout Tracker Hevy style).
     * Endpoint: GET /treino/iniciar/{id}
     */
    public function iniciarTreino($id) {
        Session::check();
        $usuario_id = Session::get('usuario_id');
        $treinoModel = new TreinoModel();
        
        $treino = $treinoModel->buscarPorId($id, $usuario_id);
        if (!$treino) {
            header('Location: ' . rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/public') . '/feed');
            exit();
        }
        
        $exercicios = $treinoModel->buscarExercicios($id);
        
        require_once __DIR__ . '/../views/treino/iniciar.php';
    }

    /**
     * Finaliza o treino ativo, registra o log de execução como check-in na academia
     * e concede pontos extras de evolução.
     * Endpoint: POST /treino/finalizar/{id}
     */
    public function finalizarTreino($id) {
        Session::check();
        $usuario_id = Session::get('usuario_id');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'erro', 'mensagem' => 'Método inválido.']);
            exit();
        }
        
        $duracao_minutos = intval($_POST['duracao_minutos'] ?? 0);
        $observacao_usuario = trim($_POST['observacao'] ?? '');
        
        $treinoModel = new TreinoModel();
        $treino = $treinoModel->buscarPorId($id);
        
        if (!$treino) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'erro', 'mensagem' => 'Treino não encontrado.']);
            exit();
        }
        
        // 1. Processar Upload da Foto do Treino (se houver)
        $foto_nome = null;
        if (isset($_FILES['foto_treino']) && $_FILES['foto_treino']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['foto_treino']['tmp_name'];
            $fileName = $_FILES['foto_treino']['name'];
            $fileSize = $_FILES['foto_treino']['size'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedfileExtensions)) {
                if ($fileSize <= 5 * 1024 * 1024) { // Limite de 5MB
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../../public/assets/img/';
                    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        $foto_nome = $newFileName;
                    }
                }
            }
        }

        // 2. Coletar e decodificar os exercícios realizados
        $exercicios_recebidos = json_decode($_POST['exercicios'] ?? '[]', true);
        if (empty($exercicios_recebidos)) {
            // Fallback: se nenhuma série foi marcada, copia do treino original
            $exercicios_originais = $treinoModel->buscarExercicios($id);
            foreach ($exercicios_originais as $ex) {
                $exercicios_recebidos[] = [
                    'nome_exercicio' => $ex['nome_exercicio'],
                    'series' => $ex['series'],
                    'repeticoes' => $ex['repeticoes'],
                    'peso_kg' => $ex['peso_kg'],
                    'descanso_segundos' => $ex['descanso_segundos'],
                    'observacoes' => $ex['observacoes']
                ];
            }
        }

        // 3. Criar o Treino Realizado (Publico = 1) no banco para aparecer no feed
        $dados_realizado = [
            'usuario_id' => $usuario_id,
            'titulo' => 'Realizou o treino: ' . $treino['titulo'],
            'descricao' => $observacao_usuario ?: 'Mais um treino finalizado com sucesso! 💪',
            'tipo_treino' => $treino['tipo_treino'],
            'grupo_muscular' => $treino['grupo_muscular'],
            'duracao_minutos' => $duracao_minutos,
            'nivel_dificuldade' => $treino['nivel_dificuldade'],
            'publico' => 1, // Torna público para ir para o feed
            'foto' => $foto_nome,
            'exercicios' => $exercicios_recebidos
        ];

        try {
            $novo_treino_id = $treinoModel->criar($dados_realizado);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao salvar o treino realizado: ' . $e->getMessage()]);
            exit();
        }

        // Carrega o usuário para obter a academia vinculada
        $uModel = new UsuarioModel();
        $usuario = $uModel->buscarPorId($usuario_id);
        
        // Determina a academia (usa a do usuário ou a de ID 1 como fallback)
        $academia_id = !empty($usuario['academia_id']) ? $usuario['academia_id'] : 1;
        
        // Formata observação de conclusão
        $obs = "Treino concluído com sucesso via GOMOS Tracker! Duração total: {$duracao_minutos} minutos.";
        if (!empty($observacao_usuario)) {
            $obs .= " Nota do atleta: " . $observacao_usuario;
        }
        
        // Registra o check-in na academia vinculado ao novo treino público
        $aModel = new AcademiaModel();
        $sucesso = $aModel->registrarCheckin($usuario_id, $academia_id, $novo_treino_id, $obs);
        
        if ($sucesso) {
            // Os pontos (+10) pelo treino concluído já são concedidos na criação do treino público no model.
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'sucesso',
                'mensagem' => 'Treino concluído e publicado! Parabéns pelos ganhos! +30 pontos de evolução adicionados.',
                'duracao' => $duracao_minutos
            ]);
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'erro', 'mensagem' => 'Não foi possível registrar o check-in do treino.']);
            exit();
        }
    }
}
