<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Validator;
use App\Models\UsuarioModel;
use App\Models\TreinoModel;
use App\Models\AmizadeModel;
use App\Models\RankingModel;
use App\Models\AcademiaModel;

/**
 * Controller responsável pelos perfis de usuário e interações de amigos.
 */
class UsuarioController {
    /**
     * Redireciona o usuário para o seu próprio perfil específico.
     */
    public function meuPerfil() {
        Session::check();
        $username = Session::get('username');
        header("Location: /perfil/" . $username);
        exit();
    }

    /**
     * Carrega e renderiza o perfil de um usuário específico.
     * 
     * @param string $username
     */
    public function perfil($username) {
        Session::check();
        
        $logado_id = Session::get('usuario_id');
        $usuarioModel = new UsuarioModel();
        
        // 1. Buscar dados do usuário pelo username
        $usuario = $usuarioModel->buscarPorUsername($username);
        if (!$usuario) {
            Session::setFlash('danger', 'Usuário não encontrado.');
            header("Location: /feed");
            exit();
        }

        $usuario_id = $usuario['id'];
        $eh_proprio = ($usuario_id == $logado_id);

        // 2. Buscar amizades e verificar status do relacionamento
        $amizadeModel = new AmizadeModel();
        $status_amizade = $eh_proprio ? 'auto' : $amizadeModel->verificarStatus($logado_id, $usuario_id);
        $amigos = $amizadeModel->listarAmigos($usuario_id);
        $total_amigos = count($amigos);

        // 3. Buscar posição no ranking geral
        $rankingModel = new RankingModel();
        $posicao_ranking = $rankingModel->obterPosicaoGeral($usuario_id);

        // 4. Buscar conquistas desbloqueadas
        $conquistas = $usuarioModel->buscarConquistas($usuario_id);

        // 5. Buscar treinos (se for o próprio perfil, traz privados também)
        $treinoModel = new TreinoModel();
        $treinos = $treinoModel->listarPorUsuario($usuario_id, $eh_proprio, $logado_id);

        // 6. Buscar solicitações de amizade pendentes (apenas se for o próprio perfil)
        $solicitacoes_pendentes = [];
        if ($eh_proprio) {
            $solicitacoes_pendentes = $amizadeModel->listarSolicitacoesPendentes($logado_id);
        }

        // Renderiza a página
        if ($eh_proprio) {
            require_once __DIR__ . '/../views/perfil/meu_perfil.php';
        } else {
            require_once __DIR__ . '/../views/perfil/visualizar.php';
        }
    }

    /**
     * Exibe o formulário de edição de perfil.
     */
    public function editarPerfilPagina() {
        Session::check();
        
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->buscarPorId(Session::get('usuario_id'));
        
        $academiaModel = new AcademiaModel();
        $academias = $academiaModel->listarTodas();

        require_once __DIR__ . '/../views/perfil/editar.php';
    }

    /**
     * Processa a edição de perfil.
     */
    public function editarPerfil() {
        Session::check();
        $usuario_id = Session::get('usuario_id');

        $nome = Validator::sanitize($_POST['nome'] ?? '');
        $bio = Validator::sanitize($_POST['bio'] ?? '');
        $nivel_fitness = Validator::sanitize($_POST['nivel_fitness'] ?? 'iniciante');
        $peso = Validator::sanitizeFloat($_POST['peso'] ?? 0);
        $altura = Validator::sanitizeInt($_POST['altura'] ?? 0);
        $cidade = Validator::sanitize($_POST['cidade'] ?? '');
        $estado = strtoupper(Validator::sanitize($_POST['estado'] ?? ''));
        $academia_id = Validator::sanitizeInt($_POST['academia_id'] ?? 0);

        if (empty($nome) || empty($cidade) || empty($estado)) {
            Session::setFlash('danger', 'Por favor, preencha os campos obrigatórios.');
            header("Location: /perfil/editar");
            exit();
        }

        $usuarioModel = new UsuarioModel();
        $dados = [
            'nome' => $nome,
            'bio' => $bio,
            'nivel_fitness' => $nivel_fitness,
            'peso' => $peso,
            'altura' => $altura,
            'cidade' => $cidade,
            'estado' => $estado,
            'academia_id' => $academia_id ?: null
        ];

        // Processar upload de nova foto se enviada
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
            $fileName = $_FILES['foto_perfil']['name'];
            $fileSize = $_FILES['foto_perfil']['size'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedfileExtensions)) {
                if ($fileSize <= 2 * 1024 * 1024) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../../public/assets/img/';
                    
                    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        $dados['foto_perfil'] = $newFileName;
                        Session::set('foto_perfil', $newFileName); // atualiza na sessão
                    }
                }
            }
        }

        $sucesso = $usuarioModel->atualizarPerfil($usuario_id, $dados);

        if ($sucesso) {
            // Atualiza dados na sessão
            Session::set('nome', $nome);
            Session::set('nivel_fitness', $nivel_fitness);
            Session::set('academia_id', $academia_id ?: null);
            Session::set('cidade', $cidade);
            Session::set('estado', $estado);

            Session::setFlash('success', 'Perfil atualizado com sucesso!');
        } else {
            Session::setFlash('danger', 'Houve um erro ao atualizar o perfil.');
        }

        header("Location: /perfil");
        exit();
    }

    /**
     * Envia solicitação de amizade.
     */
    public function adicionarAmigo($id) {
        Session::check();
        $logado_id = Session::get('usuario_id');
        $amigo_id = intval($id);

        if ($logado_id == $amigo_id) {
            Session::setFlash('danger', 'Você não pode adicionar a si mesmo.');
            header("Location: /feed");
            exit();
        }

        $amizadeModel = new AmizadeModel();
        $sucesso = $amizadeModel->enviarSolicitacao($logado_id, $amigo_id);

        if ($sucesso) {
            Session::setFlash('success', 'Solicitação de amizade enviada!');
        } else {
            Session::setFlash('danger', 'Não foi possível enviar a solicitação.');
        }

        // Redireciona de volta para o perfil do amigo
        $usuarioModel = new UsuarioModel();
        $amigo = $usuarioModel->buscarPorId($amigo_id);
        header("Location: /perfil/" . ($amigo ? $amigo['username'] : ''));
        exit();
    }

    /**
     * Aceita uma solicitação de amizade.
     */
    public function aceitarAmigo($id) {
        Session::check();
        $logado_id = Session::get('usuario_id');
        $solicitante_id = intval($id);

        $amizadeModel = new AmizadeModel();
        $sucesso = $amizadeModel->aceitarSolicitacao($solicitante_id, $logado_id);

        if ($sucesso) {
            Session::setFlash('success', 'Solicitação de amizade aceita!');
        } else {
            Session::setFlash('danger', 'Houve um erro ao aceitar a solicitação.');
        }

        header("Location: /perfil");
        exit();
    }

    /**
     * Recusa/desfaz uma solicitação de amizade.
     */
    public function recusarAmigo($id) {
        Session::check();
        $logado_id = Session::get('usuario_id');
        $outro_id = intval($id);

        $amizadeModel = new AmizadeModel();
        $sucesso = $amizadeModel->recusarOuDesfazerAmizade($logado_id, $outro_id);

        if ($sucesso) {
            Session::setFlash('success', 'Amizade desfeita/recusada.');
        } else {
            Session::setFlash('danger', 'Houve um erro ao desfazer amizade.');
        }

        header("Location: /perfil");
        exit();
    }
}
